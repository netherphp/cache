# Nether Cache

Lightweight management of cache storage and retrieval.

* `LocalEngine` - caches data in local memory while the app is running.
* `MemcacheEngine` - accesses memcached network cache.
* `FilesystemEngine` - cache data as files.

Additional cache engines can be implemented with `EngineInterface`.

Licenced under BSD+Patent. See LICENSE for details.

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
	->EngineAdd(new Nether\Cache\Engines\FilesystemEngine)
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
