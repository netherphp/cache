<?php

namespace Nether\Cache\Engines;
use Nether\Cache\Errors;
use Nether\Cache\Engines;

use Memcache;
use Nether\Cache\EngineInterface;
use Nether\Cache\Struct\CacheObject;

class MemcacheEngine
implements EngineInterface {
/*//
@date 2021-05-29
//*/

	protected Memcache
	$Pool;
	/*//
	@date 2021-05-30
	default instance storage.
	//*/

	static protected Memcache
	$PoolGlobal;
	/*//
	@date 2021-05-30
	global instance storage. only filled if used.
	//*/

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected string
	$Host = 'localhost';

	protected int|string
	$Port = 11211;

	protected bool
	$Compress = TRUE;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(bool $Global=FALSE, ?Memcache $Memcache=NULL) {
	/*//
	@date 2021-05-30
	//*/

		if($Memcache instanceof Memcache)
		$this->Pool = $Memcache;
		else
		$this->UseGlobalPool($Global);

		return;
	}

	////////////////////////////////////////////////////////////////
	// implement EngineInterface ///////////////////////////////////

	public function
	Drop(string $Key):
	void {
	/*//
	@date 2021-05-29
	//*/

		$this->Pool->Delete($Key);
		return;
	}

	public function
	Flush():
	void {
	/*//
	@date 2021-05-30
	//*/

		$this->Pool->Flush();
		return;
	}

	public function
	Get(string $Key):
	mixed {
	/*//
	@date 2021-05-29
	//*/

		$Found = unserialize($this->Pool->Get($Key));

		if($Found instanceof CacheObject)
		return $Found->Data;

		return NULL;
	}

	public function
	GetCacheObject(string $Key):
	?CacheObject {
	/*//
	@date 2021-05-29
	//*/

		$Found = unserialize($this->Pool->Get(
			$Key
		));

		if($Found instanceof CacheObject) {
			$Found = clone $Found;
			$Found->Source = $this;
		}

		return NULL;
	}

	public function
	Has(string $Key):
	bool {
	/*//
	@date 2021-05-29
	//*/

		$Found = unserialize($this->Pool->Get(
			$Key
		));

		return ($Found instanceof CacheObject);
	}

	public function
	Set(string $Key, mixed $Val):
	void {
	/*//
	@date 2021-05-29
	//*/

		$this->Pool->Add(
			$Key,
			serialize(new CacheObject($Val)),
			($this->Compress)?(MEMCACHE_COMPRESSED):(0)
		);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	ServerAdd(string $Host, int|string $Port=11211):
	static {
	/*//
	@date 2021-05-30
	//*/

		$this->Pool->AddServer(
			$Host,
			$Port,
			TRUE
		);

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	UseGlobalPool(bool $Use):
	static {

		if($Use) {
			if(!isset(static::$PoolGlobal))
			static::$PoolGlobal = static::NewMemcache();

			unset($this->Pool);
			$this->Pool = &static::$Pool;
		}

		else {
			unset($this->Pool);
			$this->Pool = static::NewMemcache();
		}

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	NewMemcache(?Memcache $Instance=NULL):
	Memcache {

		return new Memcache;
	}

}
