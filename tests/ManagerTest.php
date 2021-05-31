<?php

use PHPUnit\Framework\TestCase;
use Nether\Cache;

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

}
