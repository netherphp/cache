<?php

namespace Nether\Cache\Engines;

use Nether\Cache\EngineInterface;
use Nether\Cache\CacheData;

class AppCache
implements EngineInterface {
/*//
@date 2021-05-29
//*/

	protected array
	$Data = [];

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

		if($this->Has($Key))
		return $this->Data[$Key]->Data;

		return NULL;
	}

	public function
	GetCacheData(string $Key):
	?CacheData {
	/*//
	@date 2021-05-29
	//*/

		if($this->Has($Key))
		return $this->Data[$Key];

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
			&& ($this->Data[$Key] instanceof CacheData)
		);
	}

	public function
	Set(string $Key, mixed $Val):
	void {
	/*//
	@date 2021-05-29
	//*/

		$this->Data[$Key] = new CacheData($Val);
		return;
	}

}
