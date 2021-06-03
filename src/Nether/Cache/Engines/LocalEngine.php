<?php

namespace Nether\Cache\Engines;
use Nether\Cache\Errors;

use Nether\Cache\EngineBase;
use Nether\Cache\EngineInterface;
use Nether\Cache\Struct\CacheObject;

class LocalEngine
extends EngineBase
implements EngineInterface {
/*//
@date 2021-05-29
//*/

	protected array
	$Data = [];
	/*//
	@date 2021-05-30
	default instance storage.
	//*/

	static protected array
	$DataGlobal;
	/*//
	@date 2021-05-30
	global instance storage. only filled if used.
	//*/

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(bool $UseGlobal=FALSE) {
	/*//
	@date 2021-05-30
	//*/

		if($UseGlobal)
		$this->UseGlobalStorage(TRUE);

		return;
	}

	////////////////////////////////////////////////////////////////
	// implement EngineInterface ///////////////////////////////////

	public function
	Count():
	int {
	/*//
	@date 2021-05-29
	//*/

		return count($this->Data);
	}

	public function
	Drop(string $Key):
	void {
	/*//
	@date 2021-05-29
	//*/

		if($this->Has($Key)) {
			$this->Data[$Key] = NULL;
			unset($this->Data[$Key]);
		}

		return;
	}

	public function
	Flush():
	void {
	/*//
	@date 2021-05-30
	//*/

		unset($this->Data);
		$this->Data = [];

		return;
	}

	public function
	Get(string $Key):
	mixed {
	/*//
	@date 2021-05-29
	//*/

		if($this->Has($Key)) {
			$this->BumpHitCount();
			return $this->Data[$Key]->Data;
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

		$Found = NULL;

		if($this->Has($Key)) {
			$Found = clone $this->Data[$Key];
			$Found->Engine = $this;
			$this->BumpHitCount();
			return $Found;
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

		return (
			array_key_exists($Key,$this->Data)
			&& ($this->Data[$Key] instanceof CacheObject)
		);
	}

	public function
	Set(string $Key, mixed $Val, ?string $Origin=NULL):
	void {
	/*//
	@date 2021-05-29
	//*/

		$this->Data[$Key] = new CacheObject($Val, Origin:$Origin);
		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	UseGlobalStorage(bool $Use):
	static {
	/*//
	@date 2021-05-30
	//*/

		if($Use) {
			if(!isset(static::$DataGlobal))
			static::$DataGlobal = [];

			unset($this->Data);
			$this->Data = &static::$DataGlobal;
		}

		else {
			unset($this->Data);
			$this->Data = [];
		}

		return $this;
	}

}
