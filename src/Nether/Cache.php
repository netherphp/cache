<?php

namespace Nether;
use \Nether;

use \Exception;

Option::Define([
	'cache-autostash'    => true,
	/*//option/
	@type boolean
	define if cache should automatically attempt to stash itself when created.
	//*/

	'cache-stash-name'   => 'cache',
	/*//option/
	@type string
	the key name the cache will auto stash under.
	//*/

	'cache-verbose-get'  => true,
	/*//option/
	@type boolean
	if true successful cache hits will return an object with properites which
	will say which cache the data was fetched from, the key, the full key, and
	the data. if false, only the data is returned as it was.
	//*/

	'cache-key-prefix'   => 'nether-cache',
	/*//option/
	@type string
	define a prefix to add to cache keys so that multiple applications could
	share cache servers if they had to.
	//*/

	'cache-drivers-load' => [
		'App'  => 'Nether\\Cache\\Appcache',
		'Mem'  => 'Nether\\Cache\\Memcache',
		'Disk' => 'Nether\\Cache\\Diskcache'
	],
	/*//option/
	@type array
	define a list of cache drivers to automatically load. specfied as an array
	with the key being the alias you use to reference the cache, and the value
	being the class which extends Nether\Cache\DriverInterface.
	//*/

	'cache-drivers-use'  => ['App','Mem']
	/*//option/
	@type array
	a list of cache drivers to use automatically when performing operations when
	specific drivers are not listed. this will allow you to cache most things
	like user objects in all the quick volitile caches automatically. when you
	want to cache something special to disk only (like a huge cron'd query) you
	can specify the disk driver in the use parametre of the methods on this
	class.
	//*/

]);

class Cache {

	protected $Drivers = [];
	/*//
	@type array
	list of currently active driver objects.
	//*/

	////////////////
	////////////////

	public function __construct() {

		if(Option::Get('cache-autostash'))
		Stash::Set(Option::Get('cache-stash-name'),$this);

		$this->LoadDrivers();
		return;
	}

	////////////////
	////////////////

	public function GetDriver($key) {
	/*//
	@argv string Key
	fetch a driver. mainly so we can query memcache about shit.
	//*/
		if(array_key_exists($key,$this->Drivers)) return $this->Drivers[$key];
		else return false;
	}

	protected function LoadDrivers() {
	/*//
	spool up the specified drivers.
	//*/

		$drivers = Option::Get('cache-drivers-load');

		/*//todo/
		log instead of throwing exception
		//*/

		if(!is_array($drivers))
		throw new Exception('cache-drivers-load not an array');

		foreach($drivers as $key => $class) {
			if(!class_exists($class))
			throw new Exception("unable to load cache driver {$class}");

			$this->Drivers[$key] = new $class;
		}

		return;
	}

	protected function GetFullKey($key) {
	/*//
	@argv string Key
	prepend the defined cache-key-prefix to the requested key so that the data
	stores can prevent collisions if a single memcache is running multiple
	applications, or something like that.
	//*/
		return sprintf(
			'%s-%s',
			Nether\Option::Get('cache-key-prefix'),
			$key
		);
	}

	////////////////
	////////////////

	public function Drop($key,$use=null) {
	/*//
	@argv string Key
	@argv string Key, array Drivers
	drop a specific key from the cache.
	//*/

		if(!$use)
		$use = Option::Get('cache-drivers-use');

		foreach($this->Drivers as $dkey => $driver) {
			if(!in_array($dkey,$use)) continue;

			$driver->Drop($this->GetFullKey($key));
		}

		return;
	}

	public function Flush($use=null) {
	/*//
	@argv array Drivers
	drop all things from the cache.
	//*/

		if(!$use)
		$use = Option::Get('cache-drivers-use');

		foreach($this->Drivers as $dkey => $driver) {
			if(!in_array($dkey,$use)) continue;

			$driver->Flush();
		}

		return;
	}

	public function Get($key,$use=null) {
	/*//
	@argv string Key
	@argv string Key, array Drivers
	get the value stored under the specified key from a cache.
	//*/

		if(!$use)
		$use = Option::Get('cache-drivers-use');

		foreach($this->Drivers as $dkey => $driver) {
			if(!in_array($dkey,$use)) continue;

			$value = $driver->Get($this->GetFullKey($key));

			if($value !== null) {
				if(Option::Get('cache-verbose-get')) {
					return (object)[
						'Cache'   => $dkey,
						'Key'     => $key,
						'FullKey' => $this->GetFullKey($key),
						'Value'   => $value
					];
				} else {
					return $value;
				}
			}
		}

		return null;
	}

	public function Set($key,$value,$use=null) {
	/*//
	@argv string Key, mixed Value
	@argv string Key, mixed Value, array Drivers
	store a specified value under the key name in the cache.
	//*/

		if(!$use)
		$use = Option::Get('cache-drivers-use');

		foreach($this->Drivers as $dkey => $driver) {
			if(!in_array($dkey,$use)) continue;

			$driver->Set($this->GetFullKey($key),$value);
		}

		return;
	}

	////////////////
	////////////////

	public function GetStats($use=null) {
	/*//
	@return array
	returns an object with the hit/miss statistics of the cache.
	//*/

		if(!$use)
		$use = Option::Get('cache-drivers-use');

		$hit = 0;
		$miss = 0;

		foreach($use as $dkey) {
			if(!array_key_exists($dkey,$this->Drivers)) continue;

			$hit += $this->Drivers[$dkey]->GetHitCount();
			$miss += $this->Drivers[$dkey]->GetMissCount();
		}

		return (object)[
			'HitCount'   => $hit,
			'MissCount'  => $miss,
			'QueryCount' => ($hit+$miss),
			'ConnectTime' => 0,
			'QueryTime' => 0
		];
	}

}
