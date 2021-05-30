# Nether Cache

A cache interface.

This cache is designed to be lightweight. It only includes appcahe, memcache,
and diskcache, but also provides an abstract to base a new driver from if you
need something more specialised.

# Usage

## Low Level

Create a cache manager and throw cache engines into it.

```php
$Manager = new Nether\Cache\Manager;
$Manager->EngineAdd(new Nether\Cache\Engines\AppCache);
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
