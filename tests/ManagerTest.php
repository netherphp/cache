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

		$Manager->EngineAdd(new Cache\Engines\AppCache);
		$this->AssertEquals(1,$Manager->EngineCount());

		$Manager->EngineAdd(new Cache\Engines\AppCache);
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

		$Manager->EngineAdd(new Cache\Engines\AppCache);
		$Manager->EngineAdd(new Cache\Engines\AppCache);
		$this->AssertEquals($Count,$Manager->EngineCount());

		$Manager->EngineRemoveByType('Nether\\Cache\\Engines\\AppCache');
		$this->AssertEquals(
			($Count - 2),
			$Manager->EngineCount()
		);

		return;
	}

}
