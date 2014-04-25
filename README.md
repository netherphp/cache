# Nether Cache

A cache interface.

This cache is designed to be lightweight. It only includes appcahe, memcache,
and diskcache, but also provides an abstract to base a new driver from if you
need something more specialised.

## Basic Usage

In your file where you configure your app (e.g. include all your files), you
will create the cache. You probably should be using composer, so this should
be done after including vendor/autoload.php.

	new Nether\Cache;

The cache by default will store itself in the Nether Stash - a global instance
manager - so that you can access it from any scope of the application. That is
why I didn't bother to assign it.

To use the cache you can get the instance out of the stash in any scope you
like. By default it will automatically cache in Appcache (local to the app) and
in Memcache if any servers have been configured. Storing to disk is not
something you will want to do for every tiny object.

	function get_user($id) {
		$cache = Nether\Stash::Get('cache');

		if($row = $cache->Get("user-id-{$id}"))
		return $row;

		/*
		 * fetch the user from the database.
		 * $row = $db->Query('select ...')->Next();
		 */

		if($row) {
			$cache->Set("user-id-{$id}",$row);
			return $row;
		} else {
			return false;
		}
	}

## Adding Memcache Servers

	Nether\Option::Set('cache-memcache-pool',[
		'c1.appsrv.local:11211',
		'c2.appsrv.local:11211'
	]);

## Adding Cache Drivers

This is the default setting for the cache drivers that are loaded. You will only
need to tweak it if you invent new ones.

	Nether\Option::Set('cache-driver-load',[
		'App' => 'Nether\Cache\Drivers\Appcache',
		'Mem' => 'Nether\Cache\Drivers\Memcache',
		'Disk' => 'Nether\Cache\Drivers\Diskcache'
	]);

This is what defines the default drivers used for Get/Set etc if no drivers
are specified.

	Nether\Option::Set('cache-driver-use',['App','Mem']);

## Storing things to Disk

You will need to set a directory for cache to go to.

	Nether\Option::Set('cache-diskcache-path','/path/to/dir');

Then unless you have modified the cache-driver-use option (which is not
recommended for general use) you can store things in specific caches by using
the alias defined in the cache-driver-load option. You do this by adding another
argument to the calls, which is an array of the caches you want to use.


	$cache->Set('test','test data',['Disk']);
	
# Notes

* All options should be set BEFORE you create the instance of Nether\Cache.
