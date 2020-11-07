<?php

namespace Nether\Cache;

abstract class DriverInterface {

	protected $Hit = 0;
	/*//
	@type int
	the number of cache hits.
	//*/


	protected $Miss = 0;
	/*//
	@type int
	the number of cache misses.
	//*/

	////////////////
	////////////////

	final public function GetHitCount() {
	/*//
	@return int
	fetch the number of hits this cache has had.
	//*/

		return $this->Hit;
	}

	final public function GetMissCount() {
	/*//
	@return int
	fetch the number of misses this cache has had.
	//*/

		return $this->Miss;
	}

	////////////////
	////////////////

	abstract public function Drop($key);
	/*//
	@argv string Key
	@return null
	method requirements:
	* delete the specified key from the cache.
	* return null.
	//*/

	abstract public function Flush();
	/*//
	@return null
	method requirements:
	* delete all data from cache.
	* return null
	//*/

	abstract public function Get($key);
	/*//
	@argv string Key
	@return mixed or null
	method requirements:
	* cache hits should increment $Hit and return the value contained.
	* cache misses should incremement $Miss and return a hard null. yes. this
	means storing a null in cache will slightly unoptimise the application.
	avoid storing nulls. if you want to blank data out, use Drop. in the end
	do whatever. store a null. it won't actually hurt anything.
	//*/

	abstract public function Set($key,$value,$ttl=null);
	/*//
	@argv string Key, mixed Value
	@return null
	method requirements:
	* store the specified value under the specified key.
	* return null.
	//*/

}
