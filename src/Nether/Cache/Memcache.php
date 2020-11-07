<?php

namespace Nether\Cache;
use \Nether;

Nether\Option::Define([
	'cache-memcache-pool' => []
]);

class Memcache extends DriverInterface {

	protected $Driver;
	/*//
	@type object
	this is an instance of the Memcache driver from the PHP Memcache Extension
	for us to interact with the network pool.
	see: http://us1.php.net/manual/en/class.memcache.php
	//*/

	////////////////
	////////////////

	public function __construct() {
		$this->Driver = new \Memcached;

		$pool = Nether\Option::Get('cache-memcache-pool');

		/*//todo/
		log instead of throw exception.
		//*/

		if(!is_array($pool))
		throw new \Exception('cache-memcache-pool is not an array');

		foreach($pool as $server)
		$this->AddServer($server);

		return;
	}

	public function AddServer($server) {
		if(strpos($server,':')) {
			list($host,$port) = explode(':',$server);
		} else {
			$host = $server;
			$port = 11211;
		}

		/*//todo/
		log instead of throw exception.
		//*/

		if(!$this->Driver->addServer($host,$port))
		throw new \Exception("Error adding {$host} to Memcache pool.");

		return;
	}

	public function GetStats() {
		return $this->Driver->getStats();
	}

	////////////////
	////////////////

	public function Drop($key) {
		if(!is_object($this->Driver)) return;

		$this->Driver->delete($key);

		return;
	}

	public function Flush() {
		if(!is_object($this->Driver)) return;

		$this->Driver->flush();

		return;
	}

	public function Get($key) {
		if(!is_object($this->Driver)) return;

		$value = $this->Driver->get($key);
		if($value === false) {
			++$this->Miss;
			return null;
		} else {
			++$this->Hit;
			return unserialize($value);
		}
	}

	public function Set($key,$value,$ttl=null) {
		if(!is_object($this->Driver)) return;

		$this->Driver->set($key,serialize($value),($ttl??0));

		return;
	}

}
