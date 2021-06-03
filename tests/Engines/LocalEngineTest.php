<?php

use PHPUnit\Framework\TestCase;
use Nether\Cache\Engines;

class LocalEngineTest
extends TestCase {

	/** @test */
	public function
	TestHasSetGetDrop():
	void {
	/*//
	@date 2021-05-30
	//*/

		$Engine = new Engines\LocalEngine;
		$Key = 'test';
		$Value = 'picard';

		$this->AssertFalse($Engine->Has($Key));
		$this->AssertNull($Engine->Get($Key));

		$Engine->Set($Key,$Value);
		$this->AssertTrue($Engine->Has($Key));
		$this->AssertEquals($Value,$Engine->Get($Key));

		$Engine->Drop($Key);
		$this->AssertFalse($Engine->Has($Key));
		$this->AssertNull($Engine->Get($Key));

		return;
	}

	/** @test */
	public function
	TestCountFlush():
	void {
	/*//
	@date 2021-05-30
	//*/

		$Engine = new Engines\LocalEngine;
		$Iter = 0;

		////////

		$this->AssertEquals(0,$Engine->Count());

		////////

		for($Iter = 1; $Iter <= 10; $Iter++)
		$Engine->Set("TestKey{$Iter}","TestVal{$Iter}");

		$this->AssertEquals(10,$Engine->Count());

		////////

		$Engine->Flush();
		$this->AssertEquals(0,$Engine->Count());

		return;
	}

	/** @test */
	public function
	TestCacheObjectObject():
	void {
	/*//
	@date 2021-05-30
	//*/

		$Engine = new Engines\LocalEngine;
		$Key = 'test';
		$Value = 'mccoy';
		$CacheObject = NULL;
		$Now = time();

		$Engine->Set($Key,$Value);
		$CacheObject = $Engine->GetCacheObject($Key);

		$this->AssertInstanceOf('Nether\\Cache\\Struct\CacheObject',$CacheObject);
		$this->AssertEquals($Value,$CacheObject->Data);
		$this->AssertGreaterThanOrEqual($Now,$CacheObject->Time);

		return;
	}

	/** @test */
	public function
	TestDataGlobalStorage():
	void {
	/*//
	@date 2021-05-30
	//*/

		$Engine1 = new Engines\LocalEngine;
		$Engine2 = new Engines\LocalEngine;
		$Key = 'test';
		$Value = 'geordi';

		$Engine1->UseGlobalStorage(TRUE);
		$Engine2->UseGlobalStorage(TRUE);

		////////

		$Engine1->Set($Key,$Value);

		$this->AssertEquals(
			$Value,
			$Engine1->Get($Key)
		);

		$this->AssertEquals(
			$Engine1->Get($Key),
			$Engine2->Get($Key)
		);

		return;
	}

	/** @test */
	public function
	TestDataGlobalStorageConstructor():
	void {
	/*//
	@date 2021-05-30
	//*/

		$Engine1 = new Engines\LocalEngine(UseGlobal: TRUE);
		$Engine2 = new Engines\LocalEngine(UseGlobal: TRUE);
		$Key = 'test';
		$Value = 'uhura';

		////////

		$Engine1->Set($Key,$Value);

		$this->AssertEquals(
			$Value,
			$Engine1->Get($Key)
		);

		$this->AssertEquals(
			$Engine1->Get($Key),
			$Engine2->Get($Key)
		);

		return;
	}

	/** @test */
	public function
	TestCacheHitStats():
	void {
	/*//
	@date 2021-05-30
	//*/

		$Engine = new Engines\LocalEngine(UseGlobal: TRUE);
		$Key = 'test';
		$Value = 'data';

		////////

		$this->AssertEquals(0,$Engine->GetHitCount());
		$this->AssertEquals(0,$Engine->GetMissCount());
		$Engine->Set($Key,$Value);

		$this->AssertEquals($Value,$Engine->Get($Key));
		$this->AssertEquals(1,$Engine->GetHitCount());
		$this->AssertEquals(1.0,$Engine->GetHitRatio());
		$this->AssertEquals(0,$Engine->GetMissCount());
		$this->AssertEquals(0.0,$Engine->GetMissRatio());

		$this->AssertNull($Engine->Get('nope'));
		$this->AssertEquals(1,$Engine->GetHitCount());
		$this->AssertEquals(0.5,$Engine->GetHitRatio());
		$this->AssertEquals(1,$Engine->GetMissCount());
		$this->AssertEquals(0.5,$Engine->GetMissRatio());

		return;
	}

}
