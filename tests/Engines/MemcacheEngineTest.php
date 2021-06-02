<?php

use PHPUnit\Framework\TestCase;

use Nether\Cache\Engines;
use Nether\Cache\Struct\CacheObject;

class MemcacheEngineTest
extends TestCase {

	/** @test */
	public function
	TestHasSetGetDropFlush():
	void {
	/*//
	@date 2021-05-30
	//*/

		$Mock = $this->CreateStub(Memcache::class);
		$Engine = new Engines\MemcacheEngine(Memcache:$Mock);
		$Key = static::class;
		$Value = 'kirk';
		$CacheObject = serialize(new CacheObject($Value));

		// set up lies.

		$Mock
		->Method('get')
		->Will($this->OnConsecutiveCalls(
			FALSE, FALSE,
			$CacheObject, $CacheObject,
			FALSE, FALSE,
			$CacheObject, $CacheObject,
			FALSE, FALSE
		));

		////////

		$this->AssertFalse($Engine->Has($Key));
		$this->AssertNull($Engine->Get($Key));

		////////

		$Engine->Set($Key,$Value);
		$Mock->Method('get')->WillReturn($CacheObject);
		$this->AssertTrue($Engine->Has($Key));
		$this->AssertEquals($Value,$Engine->Get($Key));

		////////

		$Engine->Drop($Key);
		$this->AssertFalse($Engine->Has($Key));
		$this->AssertNull($Engine->Get($Key));

		////////

		$Engine->Set($Key,$Value);
		$this->AssertTrue($Engine->Has($Key));
		$this->AssertEquals($Value,$Engine->Get($Key));

		////////

		$Engine->Flush($Key);
		$this->AssertFalse($Engine->Has($Key));
		$this->AssertNull($Engine->Get($Key));

		return;
	}

	/** @test */
	public function
	TestServersViaConstructor():
	void {
	/*//
	@date 2021-06-02
	//*/

		$Engine = new Engines\MemcacheEngine(
			Servers: [ 'localhost:11211' ]
		);

		// lmafo i do not think the basic bitch memcache ext without the d
		// actually has a way to query what servers are in the pool. wtf.

		$this->AssertTrue($Engine instanceof Engines\MemcacheEngine);
		return;
	}

}
