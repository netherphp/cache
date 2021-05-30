<?php

use PHPUnit\Framework\TestCase;
use Nether\Cache\Engines;

class AppCacheTest
extends TestCase {

	/** @test */
	public function
	TestHasSetGetDrop():
	void {
	/*//
	@date 2021-05-30
	//*/

		$Engine = new Engines\AppCache;
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

		$Engine = new Engines\AppCache;
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
	TestCacheDataObject():
	void {
	/*//
	@date 2021-05-30
	//*/

		$Engine = new Engines\AppCache;
		$Key = 'test';
		$Value = 'mccoy';
		$CacheData = NULL;
		$Now = time();

		$Engine->Set($Key,$Value);
		$CacheData = $Engine->GetCacheData($Key);

		$this->AssertInstanceOf('Nether\\Cache\\CacheData',$CacheData);
		$this->AssertEquals($Value,$CacheData->Data);
		$this->AssertGreaterThanOrEqual($Now,$CacheData->Time);

		return;
	}

}
