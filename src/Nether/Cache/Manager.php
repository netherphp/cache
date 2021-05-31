<?php

namespace Nether\Cache;
use Nether\Cache\Struct;

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
	forget the data stored there.
	//*/

		$this->Engines->Each(
			fn(Struct\EngineObject $Eng)
			=> $Eng->Engine->Drop($Key)
		);

		return $this;
	}

	public function
	Flush():
	static {
	/*//
	@date 2021-05-30
	forget all the data stored there.
	//*/

		$this->Engines->Each(
			fn(Struct\EngineObject $Eng)
			=> $Eng->Engine->Flush()
		);

		return $this;
	}

	public function
	Get(string $Key):
	mixed {
	/*//
	@date 2021-05-30
	get the data stored there.
	//*/

		$Eng = NULL;

		foreach($this->Engines as $Eng)
		if($Eng->Engine->Has($Key))
		return $Eng->Engine->Get($Key);

		return NULL;
	}

	public function
	GetCacheObject(string $Key):
	mixed {
	/*//
	@date 2021-05-30
	get the full cache data wrapper stored there.
	//*/

		$Eng = NULL;

		foreach($this->Engines as $Eng)
		if($Eng->Engine->Has($Key))
		return $Eng->Engine->GetCacheObject($Key);

		return NULL;
	}

	public function
	Has(string $Key):
	bool {
	/*//
	@date 2021-05-30
	check if data is stored there.
	//*/

		$Eng = NULL;

		foreach($this->Engines as $Eng)
		if($Eng->Engine->Has($Key))
		return TRUE;

		return FALSE;
	}

	public function
	Set(string $Key, mixed $Value):
	static {
	/*//
	@date 2021-05-30
	make some data be stored there.
	//*/

		$this->Engines->Each(
			fn(Struct\EngineObject $Eng)
			=> $Eng->Engine->Set($Key,$Value)
		);

		return $this;
	}

	public function
	Where(string $Key):
	?EngineInterface {
	/*//
	@date 2021-05-30
	get the engine some data is stored in.
	//*/

		$Eng = NULL;

		foreach($this->Engines as $Eng)
		if($Eng->Engine->Has($Key))
		return $Eng->Engine;

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
			fn(Struct\EngineObject $Eng)
			=> $Eng->Engine
		);
	}

	public function
	EngineAdd(EngineInterface $Engine, ?int $Priority=NULL, ?string $Alias=NULL):
	static {
	/*//
	@date 2021-05-30
	//*/

		// if no priority specified set it to come after what might
		// already be in there.

		if($Priority === NULL)
		$Priority = $this->Engines->Accumulate(
			0,
			fn(int $Max, Struct\EngineObject $Eng)
			=> ($Eng->Priority >= $Max) ? ($Eng->Priority + 10) : ($Max)
		);

		($this->Engines)
		->Push(new Struct\EngineObject($Engine,$Priority),$Alias)
		->Sort(
			fn(Struct\EngineObject $A, Struct\EngineObject $B)
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
			fn(Struct\EngineObject $Val)
			=> !($Val->Engine instanceof $Class)
		);

		return $this;
	}

}
