<?php

use PHPUnit\Framework\TestCase;

use Nether\Cache\Engines;
use Nether\Cache\Struct\CacheObject;

class FilesystemEngineTest
extends TestCase {
/*//
@date 2021-06-01
note if any of these tests fail, its possible you have files laying around in
the temp dir still which will then make even more of these tests fail when you
rerun it as many tests first check that it initialized empty.
//*/

	protected function
	GetTestPath(string $Dir):
	string {
	/*//
	@date 2021-06-01
	//*/

		return sprintf(
			'%s%s%s',
			dirname(__FILE__,3),
			DIRECTORY_SEPARATOR,
			$Dir
		);
	}

	/** @test */
	public function
	TestHasSetGetDrop():
	void {
	/*//
	@date 2021-06-01
	test the basics of the cache engine.
	//*/

		$Path = $this->GetTestPath('data');
		$Engine = new Engines\FilesystemEngine(Path:$Path);
		$Key = 'test';
		$Value = 'spock';
		$Result = NULL;
		$ExpectedFilename = sprintf(
			'file://%s%s%s',
			$Path, DIRECTORY_SEPARATOR, $Key
		);

		if(DIRECTORY_SEPARATOR === '\\')
		$ExpectedFilename = str_replace('\\','/',$ExpectedFilename);

		$this->AssertTrue($Engine instanceof Engines\FilesystemEngine);
		$this->AssertDirectoryExists($Path);
		$this->AssertDirectoryIsWritable($Path);
		$this->AssertFalse($Engine->Has($Key));
		$this->AssertNull($Engine->Get($Key));

		$Engine->Set($Key,$Value);
		$this->AssertFileExists($ExpectedFilename);
		$this->assertFileIsReadable($ExpectedFilename);

		$Result = $Engine->Get($Key);
		$this->AssertEquals($Value,$Result);

		$Engine->Drop($Key);
		$this->AssertFileDoesNotExist($ExpectedFilename);

		return;
	}

	/** @test */
	public function
	TestHasInputType():
	void {
	/*//
	@date 2021-06-01
	test that the Has method is properly handling both just the cache item
	key as well as an absolute file uri.
	//*/

		$Path = $this->GetTestPath('data');
		$Engine = new Engines\FilesystemEngine(Path:$Path);

		$Key = 'test';
		$Filename = sprintf(
			'file://%s%s%s',
			$Path, DIRECTORY_SEPARATOR, $Key
		);

		$this->AssertFalse($Engine->Has($Key));
		$this->AssertFalse($Engine->Has($Filename));

		$Engine->Set($Key,$Key);
		$this->AssertTrue($Engine->Has($Key));
		$this->AssertTrue($Engine->Has($Filename));

		$Engine->Drop($Key);
		$this->AssertFalse($Engine->Has($Key));
		$this->AssertFalse($Engine->Has($Filename));

		return;
	}

	/**
	@test
	@covers Flush
	*/
	public function
	TestFlush():
	void {
	/*//
	@date 2021-06-01
	test that the Has method is properly handling both just the cache item
	key as well as an absolute file uri.
	//*/

		$Path = $this->GetTestPath('data');
		$Engine = new Engines\FilesystemEngine(Path:$Path);
		$Iter = 0;

		for($Iter = 1; $Iter <= 10; $Iter++) {
			$Engine->Set("test-{$Iter}", "value-{$Iter}");
			$this->AssertTrue($Engine->Has("test-{$Iter}"));
		}

		////////

		$Engine->Flush();

		for($Iter = 1; $Iter <= 10; $Iter++)
		$this->AssertFalse($Engine->Has("test-{$Iter}"));

		return;
	}

}
