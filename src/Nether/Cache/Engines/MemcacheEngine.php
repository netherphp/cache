<?php

namespace Nether\Cache\Engines;
use Nether\Cache\Traits;
use Nether\Cache\Errors;

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

	protected bool
	$Compress = TRUE;
	/*//
	@date 2021-05-30
	allow memcache to compress the data as it stores it.
	//*/

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(
		bool $Global=FALSE,
		Memcache $Memcache=NULL,
		array $Servers=[]
	) {
	/*//
	@date 2021-05-30
	//*/

		// initialize the memcache pool with either the injected dependency
		// or constructing its own.

		if($Memcache instanceof Memcache)
		$this->Pool = $Memcache;
		else
		$this->UseGlobalPool($Global);

		// if a list of servers was provided, add them to the pool.

		if(count($Servers))
		$this->ServerAddList($Servers);

		return;
	}

	////////////////////////////////////////////////////////////////
	// implement EngineInterface ///////////////////////////////////

	use
	Traits\CacheHitStats;

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

		if($Found instanceof CacheObject) {
			$this->BumpHitCount();
			return $Found->Data;
		}

		$this->BumpMissCount();
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
			$Found->Engine = $this;
			$this->BumpHitCount();
		}

		$this->BumpMissCount();
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
	Set(string $Key, mixed $Val, ?string $Origin=NULL):
	void {
	/*//
	@date 2021-05-29
	//*/

		$this->Pool->Add(
			$Key,
			serialize(new CacheObject($Val, Origin:$Origin)),
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

	public function
	ServerAddList(array $Input):
	static {
	/*//
	@date 2021-06-02
	//*/

		$Item = NULL;
		$Host = NULL;
		$Port = NULL;

		foreach($Input as $Item) {
			if(!str_contains($Item,':'))
			$Item .= ':11211';

			list($Host,$Port) = explode(':',$Item);
			$this->ServerAdd($Host,$Port);
		}

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	UseGlobalPool(bool $Use):
	static {
	/*//
	@date 2021-05-30
	//*/

		if($Use) {
			if(!isset(static::$PoolGlobal))
			static::$PoolGlobal = new Memcache;

			unset($this->Pool);
			$this->Pool = &static::$Pool;
		}

		else {
			unset($this->Pool);
			$this->Pool = new Memcache;
		}

		return $this;
	}

}
