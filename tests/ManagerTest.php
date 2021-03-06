<?php

use PHPUnit\Framework\TestCase;
use Nether\Cache;
use Nether\Cache\Engines;

class ManagerTest
extends TestCase {

	/** @test */
	public function
	TestEngineAddRemoveKey():
	void {
	/*//
	@date 2021-05-30
	//*/

		$Manager = new Cache\Manager;
		$EngineKeys = NULL;
		$Key = NULL;
		$Count = 0;

		////////

		$this->AssertEquals(0,$Manager->EngineCount());

		$Manager->EngineAdd(new Cache\Engines\LocalEngine);
		$this->AssertEquals(1,$Manager->EngineCount());

		$Manager->EngineAdd(new Cache\Engines\LocalEngine);
		$this->AssertEquals(2,$Manager->EngineCount());

		////////

		$EngineKeys = array_keys($Manager->Engines()->GetData());
		$Count = count($EngineKeys);
		$this->AssertEquals($Count,$Manager->EngineCount());

		////////

		foreach($EngineKeys as $Key) {
			$Manager->EngineRemoveByKey($Key);

			$this->AssertEquals(
				(--$Count),
				$Manager->EngineCount()
			);
		}

		return;
	}

	/** @test */
	public function
	TestEngineAddRemoveType():
	void {
	/*//
	@date 2021-05-30
	//*/

		$Manager = new Cache\Manager;
		$Count = 2;

		////////

		$Manager->EngineAdd(new Cache\Engines\LocalEngine);
		$Manager->EngineAdd(new Cache\Engines\LocalEngine);
		$this->AssertEquals($Count,$Manager->EngineCount());

		$Manager->EngineRemoveByType('Nether\\Cache\\Engines\\LocalEngine');
		$this->AssertEquals(
			($Count - 2),
			$Manager->EngineCount()
		);

		return;
	}

	/** @test */
	public function
	TestHasSetGetDrop():
	void {
	/*//
	@date 2021-05-30
	//*/

		$Manager = new Cache\Manager;
		$Manager->EngineAdd(new Cache\Engines\LocalEngine);
		$Key = 'test';
		$Value = 'scotty';

		$this->AssertFalse($Manager->Has($Key));
		$this->AssertNull($Manager->Get($Key));

		$Manager->Set($Key,$Value);
		$this->AssertTrue($Manager->Has($Key));
		$this->AssertEquals($Value,$Manager->Get($Key));

		$Manager->Drop($Key);
		$this->AssertFalse($Manager->Has($Key));
		$this->AssertNull($Manager->Get($Key));

		return;
	}

	/** @test */
	public function
	TestWhere():
	void {
	/*//
	@date 2021-05-30
	//*/

		$Engine1 = new Cache\Engines\LocalEngine;
		$Engine2 = new Cache\Engines\LocalEngine;
		$Manager = (
			(new Cache\Manager)
			->EngineAdd($Engine1)
			->EngineAdd($Engine2)
		);

		$Key = 'test';
		$Value = 'scotty';

		$Manager->Set($Key,$Value);
		$this->AssertTrue($Manager->Where($Key) === $Engine1);

		$Engine1->Drop($Key);
		$this->AssertTrue($Manager->Where($Key) === $Engine2);

		$Engine2->Drop($Key);
		$this->AssertNull($Manager->Where($Key));

		return;
	}

	/** @test */
	public function
	TestEngineAlias():
	void {
	/*//
	@date 2021-05-30
	//*/

		$Engine1 = new Cache\Engines\LocalEngine;
		$Engine2 = new Cache\Engines\LocalEngine;
		$Manager = (
			(new Cache\Manager)
			->EngineAdd($Engine1)
			->EngineAdd($Engine2)
		);

		$Engines = $Manager->Engines();
		$this->AssertCount(2,$Engines);
		$this->AssertTrue($Engines->HasKey(0));
		$this->AssertTrue($Engines->HasKey(1));
		$this->AssertFalse($Engines->HasKey(2));

		$Manager
		->EngineRemoveByKey(0)
		->EngineRemoveByKey(1)
		->EngineAdd($Engine1, Alias:'Engine1')
		->EngineAdd($Engine2, Alias:'Engine2');

		$Engines = $Manager->Engines();
		$this->AssertCount(2,$Engines);
		$this->AssertTrue($Engines->HasKey('Engine1'));
		$this->AssertTrue($Engines->HasKey('Engine2'));
		$this->AssertFalse($Engines->HasKey(0));

		return;
	}

	/** @test */
	public function
	TestCacheObjectEngine():
	void {
	/*//
	@date 2021-05-30
	//*/

		$Engine1 = new Cache\Engines\LocalEngine;
		$Engine2 = new Cache\Engines\LocalEngine;
		$Manager = (
			(new Cache\Manager)
			->EngineAdd($Engine1)
			->EngineAdd($Engine2)
		);

		$Key = static::class;
		$Value = 'sulu';
		$Result = NULL;

		$Manager->Set($Key,$Value);
		$Result = $Manager->GetCacheObject($Key);
		$this->AssertTrue($Result->Engine === $Engine1);

		$Engine1->Drop($Key);
		$Result = $Manager->GetCacheObject($Key);
		$this->AssertTrue($Result->Engine === $Engine2);

		return;
	}

	/** @test */
	public function
	TestCacheObjectOrigin():
	void {
	/*//
	@date 2021-05-30
	//*/

		$Manager = (
			(new Cache\Manager)
			->EngineAdd(new Cache\Engines\LocalEngine)
		);

		$Manager->Set('test1', 'chekov');
		$Manager->Set('test2', 'chekov', Origin:'unit2');

		$this->AssertNull($Manager->GetCacheObject('test1')->Origin);
		$this->AssertEquals('unit2',$Manager->GetCacheObject('test2')->Origin);

		return;
	}

	/** @test */
	public function
	TestCacheHitStats():
	void {
	/*//
	@date 2021-05-30
	//*/

		$Manager = new Nether\Cache\Manager;
		$Engine = new Engines\LocalEngine;
		$Manager->EngineAdd($Engine);

		$Key = 'test';
		$Value = 'worf';
		$Stats = NULL;

		////////

		$this->AssertEquals(0,$Manager->GetHitCount());
		$this->AssertEquals(0,$Manager->GetMissCount());
		$Manager->Set($Key,$Value);

		$this->AssertEquals($Value,$Manager->Get($Key));
		$this->AssertEquals(1,$Manager->GetHitCount());
		$this->AssertEquals(1.0,$Manager->GetHitRatio());
		$this->AssertEquals(0,$Manager->GetMissCount());
		$this->AssertEquals(0.0,$Manager->GetMissRatio());

		$this->AssertNull($Manager->Get('nope'));
		$this->AssertEquals(1,$Manager->GetHitCount());
		$this->AssertEquals(0.5,$Manager->GetHitRatio());
		$this->AssertEquals(1,$Manager->GetMissCount());
		$this->AssertEquals(0.5,$Manager->GetMissRatio());

		$Stats = $Manager->GetStats();
		$this->AssertEquals(1,$Stats->Hit);
		$this->AssertEquals(0.5,$Stats->HitRatio);
		$this->AssertEquals(1,$Stats->Miss);
		$this->AssertEquals(0.5,$Stats->MissRatio);

		$Stats = $Engine->GetStats();
		$this->AssertEquals(1,$Stats->Hit);
		$this->AssertEquals(0.5,$Stats->HitRatio);
		$this->AssertEquals(1,$Stats->Miss);
		$this->AssertEquals(0.5,$Stats->MissRatio);

		return;
	}

	/** @test */
	public function
	TestManagerBackfillEnabled():
	void {
	/*//
	@date 2021-06-08
	//*/

		$Key = 'test';
		$Value = 'mudd';

		$Manager = new Nether\Cache\Manager;
		$Engine1 = new Engines\LocalEngine;
		$Engine2 = new Engines\LocalEngine;
		$Manager->EngineAdd($Engine1);
		$Manager->EngineAdd($Engine2);
		$Manager->BackfillEnable(TRUE);

		$Engine2->Set($Key,$Value);

		// it should be in engine 2.
		$this->AssertEquals($Value,$Engine2->Get($Key));
		$this->AssertEquals($Engine2,$Manager->Where($Key));

		// ask for it normally.
		$this->AssertEquals($Value,$Manager->Get($Key));

		// now its in engine 1 as well.
		$this->AssertEquals($Value,$Engine1->Get($Key));
		$this->AssertEquals($Engine1,$Manager->Where($Key));

		return;
	}

	/** @test */
	public function
	TestManagerBackfillDisabled():
	void {
	/*//
	@date 2021-06-08
	//*/

		$Key = 'test';
		$Value = 'mudd';

		$Manager = new Nether\Cache\Manager;
		$Engine1 = new Engines\LocalEngine;
		$Engine2 = new Engines\LocalEngine;
		$Manager->EngineAdd($Engine1);
		$Manager->EngineAdd($Engine2);
		$Manager->BackfillEnable(FALSE);

		$Engine2->Set($Key,$Value);

		// it should be in engine 2.
		$this->AssertEquals($Value,$Engine2->Get($Key));
		$this->AssertEquals($Engine2,$Manager->Where($Key));

		// ask for it normally.
		$this->AssertEquals($Value,$Manager->Get($Key));

		// i should still only be in engine 2.
		$this->AssertNull($Engine1->Get($Key));
		$this->AssertEquals($Engine2,$Manager->Where($Key));

		return;
	}

}
