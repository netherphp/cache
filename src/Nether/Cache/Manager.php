<?php

namespace Nether\Cache;

use Nether\Object\Datastore;
use Nether\Cache\EngineInterface;

class Manager {
/*//
@date 2021-05-30
//*/

	protected ?Datastore
	$Engines = NULL;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct() {
	/*//
	@date 2021-05-30
	//*/

		$this->Engines = new Datastore;

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Drop(string $Key):
	static {
	/*//
	@date 2021-05-30
	//*/

		$this->Engines->Each(
			fn(EngineData $Eng)
			=> $Eng->Engine->Drop($Key)
		);

		return $this;
	}

	public function
	Flush(string $Key):
	static {
	/*//
	@date 2021-05-30
	//*/

		$this->Engines->Each(
			fn(EngineData $Eng)
			=> $Eng->Engine->Flush($Key)
		);

		return $this;
	}

	public function
	Has(string $Key):
	bool {
	/*//
	@date 2021-05-30
	//*/

		return FALSE;
	}

	public function
	Get(string $Key):
	mixed {
	/*//
	@date 2021-05-30
	//*/

		return NULL;
	}

	public function
	GetCacheData(string $Key):
	mixed {
	/*//
	@date 2021-05-30
	//*/

		return NULL;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Engines():
	Datastore {
	/*//
	@date 2021-05-30
	//*/

		return $this->Engines->Map(
			fn(EngineData $Eng)
			=> $Eng->Engine
		);
	}

	public function
	EngineAdd(EngineInterface $Engine, int $Priority=50):
	static {
	/*//
	@date 2021-05-30
	//*/

		($this->Engines)
		->Push(new EngineData($Engine,$Priority))
		->Sort(
			fn(EngineData $A, EngineData $B)
			=> $A->Priority <=> $B->Priority
		);

		return $this;
	}

	public function
	EngineCount():
	int {
	/*//
	@date 2021-05-30
	//*/

		return $this->Engines->Count();
	}

	public function
	EngineRemoveByKey(int $Key):
	static {
	/*//
	@date 2021-05-30
	//*/

		$this->Engines->Remove($Key);

		return $this;
	}

	public function
	EngineRemoveByType(string $Class):
	static {
	/*//
	@date 2021-05-30
	//*/

		$this->Engines->Filter(
			fn(EngineData $Val)
			=> !($Val->Engine instanceof $Class)
		);

		return $this;
	}

}
