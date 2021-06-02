# Nether Cache

Lightweight management of cache storage and retrieval.

* `LocalEngine` - caches data in local memory while the app is running.
* `MemcacheEngine` - accesses memcached network cache.
* `FilesystemEngine` - cache data as files.

Additional cache engines can be implemented with `EngineInterface`.

Licenced under BSD-2-Clause-Patent. See LICENSE for details.

# Usage

## Low Level

Create a cache manager and throw cache engines into it. If no priority value is set then the priorities will be set such that the caches will always be checked FIFO. In this example case the LocalEngine will always be checked before the MemcacheEngine which will aways be checked before the FilesystemEngine. The priorties can be used to stack caches from fastest to slowest.

```php
$Manager = (
	(new Nether\Cache\Manager)
	->EngineAdd(new Nether\Cache\Engines\LocalEngine)
	->EngineAdd(
		(new Nether\Cache\Engines\MemcacheEngine)
		->ServerAdd('localhost',11211)
	)
	->EngineAdd(
		(new Nether\Cache\Engines\FilesystemEngine)
		->SetPath('/where/ever')
	)
);
```

Throw data into the caches, ask about it, and get it back out.

```php
$Manager->Set('unique-id','value');

var_dump(
	$Manager->Has('unique-id'),
	$Manager->Get('unique-id')
);

// bool(true)
// string(5) "value"
```

Delete data from the caches.

```php
$Manager->Drop('unique-id');

var_dump(
	$Manager->Has('unique-id'),
	$Manager->Get('unique-id')
);

// bool(false)
// NULL
```

## Debugging

The contents of the cache are wrapped with a small descriptor object that describes the data, and can be inspected by getting a cache object rather than the cache data directly.

* `CacheObject->Time` is the unix timestamp the data was added to the cache.
* `CacheObject->Engine` will be the the cache engine instance that the data was found in.
* `CacheObject->Origin` will be NULL unless it is defined when data is set. It is meant to be meta to trace what part of a project pushed the data into the cache.

```php
$Manager->Set('test', 'geordi', Origin:'engineering');
print_r($Manager->GetCacheObject('unique-id'));
```

```
Nether\Cache\Struct\CacheObject Object
(
	[Data]   => geordi
	[Time]   => 1622487745
	[Origin] => engineering
	[Engine] => Nether\Cache\Engines\LocalEngine Object
		(
			[...]
		)
)
```

# LocalEngine

```php
new Nether\Cache\Engines\LocalEngine(
	UseGlobal: bool
);
```

Setting UseGlobal to TRUE will allow multiple instances to access the same
dataset. This would allow creation of instances when needed rather than early
in an app and stored as a singleton somewhere.

The way this engine works is literally it is just an array that is local
to this currently running application. Why ask Memcached for the same thing
twice if we could remember it the first time?

# MemcacheEngine

```php
new Nether\Cache\Engines\MemcacheEngine(
	UseGlobal: bool,
	Memcache: Memcache|null
);
```

Setting UseGlobal to TRUE will allow multiple instances to access the same
defined server pool. This would allow creation of instances when needed rather
than early in an app and stored as a singleton somewhere.

Providing a Memcache instance will use whatever pool that instance was built
with. Additionally, this allows dependency injection of a mock for testing.

`Engine->ServerAdd(string Host, int Port=11211)`

Add servers to the Memcache pool.

# FilesystemEngine

```php
new Nether\Cache\Engines\FilesystemEngine(
	Path: string,
	UseHashType: string|NULL,
	UseHashStruct: bool
	UseFullDrop: bool
);
```

Path is the only required argument, that being the path to the directory where
cache data should be stored.

By default the filesystem engine works just like the others. Store data under
the key "test" and get a file called "test" in the directory the engine is
pointing at. There are additional features though to help make the filesystem
engine more robust at larger scales.

`Engine->UseHashType(?string HashAlgoName)`

Given any hash name supported by your system instead of storing the cache file
as a literal file called "test" it will be called whatever it hashes out to be.
Setting it to NULL will disable the hashing.

`Engine->UseHashStruct(bool Should)`

If TRUE the engine will mess around with the final filename a bit to help avoid
hitting filesystem limits for maximum files in a directory. Given a filename
hash worked out to `abcdef` it will be transformed to be `ab/cdef` to help
distribute the cache files across many directories. As of the time of this
writing, this is the same as how Git stores its object files in the .git
folders.

