# Nether Cache

A cache interface.

This cache is designed to be lightweight. It only includes appcahe, memcache,
and diskcache, but also provides an abstract to base a new driver from if you
need something more specialised.

# Usage

## Low Level

Create a cache manager and throw cache engines into it. If no priority value is set then the priorities will be set such that the caches will always be checked FIFO. In this example case the LocalEngine will always be checked before the MemCache which will aways be checked before the DiskCache. The priorties can be used to stack caches from fastest to slowest.

```php
$Manager = new Nether\Cache\Manager;
$Manager->EngineAdd(new Nether\Cache\Engines\LocalEngine);
$Manager->EngineAdd(new Nether\Cache\Engines\MemCache);
$Manager->EngineAdd(new Nether\Cache\Engines\DiskCache);
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
