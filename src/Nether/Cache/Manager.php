<?php

namespace Nether\Cache;
use Nether\Cache\Struct;

use Nether\Object\Datastore;
use Nether\Cache\EngineInterface;
use Nether\Cache\Struct\EngineObject;


class Manager {
/*//
@date 2021-05-30
//*/

	protected ?Datastore
	$Engines = NULL;

	protected bool
	$Backfill = TRUE;

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
	// basics from EngineInterface /d///////////////////////////////

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
		$Iter = NULL;
		$Output = NULL;

		foreach($this->Engines as $Iter => $Eng) {
			if($Eng->Engine->Has($Key)) {
				$Output = $Eng->Engine->Get($Key);

				if($this->Backfill)
				$this->Backfill($Iter,$Key,$Output);

				return $Output;
			}

			$Eng->Engine->BumpMissCount();
		}

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
	Set(string $Key, mixed $Value, ?string $Origin=NULL):
	static {
	/*//
	@date 2021-05-30
	make some data be stored there.
	//*/

		$this->Engines->Each(
			fn(Struct\EngineObject $Eng)
			=> $Eng->Engine->Set($Key,$Value,Origin:$Origin)
		);

		return $this;
	}

	public function
	GetHitCount():
	int {
	/*//
	@date 2021-06-03
	//*/

		return $this->Engines->Accumulate(
			0,
			fn(int $Cur, EngineObject $Val)
			=> ($Cur + $Val->Engine->GetHitCount())
		);
	}

	public function
	GetHitRatio():
	float {
	/*//
	@date 2021-06-03
	//*/

		$Hits = $this->GetHitCount();
		$Total = $Hits + $this->GetMissCount();

		if($Total === 0)
		return 0;

		return $Hits / $Total;
	}

	public function
	GetMissCount():
	int {
	/*//
	@date 2021-06-03
	//*/

		return $this->Engines->Accumulate(
			0,
			fn(int $Cur, EngineObject $Val)
			=> ($Cur + $Val->Engine->GetMissCount())
		);
	}

	public function
	GetMissRatio():
	float {
	/*//
	@date 2021-06-03
	//*/

		$Miss = $this->GetMissCount();
		$Total = $Miss + $this->GetHitCount();

		if($Total === 0)
		return 0;

		return $Miss / $Total;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

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

	public function
	GetStats():
	Struct\CacheStats {
	/*//
	@date 2021-06-03
	//*/

		return new Struct\CacheStats($this);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	Backfill(int $Iter, string $Key, mixed $Data):
	static {
	/*//
	@date 2021-06-08
	given the engine iter to start at, backfill the engines that
	were skipped over so that they are not skipped again.
	//*/

		$Eng = NULL;
		$Cur = 0;

		foreach($this->Engines as $Cur => $Eng) {
			if($Cur >= $Iter)
			break;

			$Eng->Engine->Set(
				$Key,
				$Data,
				Origin: 'CacheManagerBackfill'
			);
		}

		return $this;
	}

	public function
	BackfillEnable(bool $Backfill):
	static {
	/*//
	@date 2021-06-08
	set if this cache manager should automatically backfill lower
	priority cache engines when data is found within a higher one.
	//*/

		$this->Backfill = $Backfill;
		return $this;
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
