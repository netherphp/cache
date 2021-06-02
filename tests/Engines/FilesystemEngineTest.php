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
			'file://%s/%s',
			$Path, $Key
		);

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
			'file://%s/%s',
			$Path, $Key
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

	/** @test */
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

	/** @test */
	public function
	TestHashMode():
	void {
	/*//
	@date 2021-06-01
	test that the Has method is properly handling both just the cache item
	key as well as an absolute file uri.
	//*/

		$Hash = 'sha3-224';
		$Path = $this->GetTestPath('data');
		$Engine = new Engines\FilesystemEngine(Path:$Path);
		$Key = 'test';
		$Value = 'chapel';
		$ExpectedFilename = sprintf(
			'file://%s/%s',
			$Path, hash($Hash,$Key)
		);

		$Engine->UseHashType($Hash);
		$Engine->Set($Key,$Value);
		$this->AssertFileExists($ExpectedFilename);

		$Engine->Drop($Key);
		$this->AssertFileDoesNotExist($ExpectedFilename);

		return;
	}

	/** @test */
	public function
	TestHashStructMode():
	void {
	/*//
	@date 2021-06-01
	test that the Has method is properly handling both just the cache item
	key as well as an absolute file uri.
	//*/

		$Hash = 'sha3-224';
		$Path = $this->GetTestPath('data');
		$Engine = new Engines\FilesystemEngine(Path:$Path);
		$Key = 'test';
		$Value = 'chapel';
		$ExpectedHash = hash($Hash,$Key);

		// turns abcdef into ab/cdef
		$ExpectedHashStruct = sprintf(
			'%s/%s',
			substr($ExpectedHash, 0, 2),
			substr($ExpectedHash, 2)
		);

		$ExpectedFilename = sprintf(
			'file://%s/%s',
			$Path, $ExpectedHashStruct
		);

		$Engine->UseHashType($Hash);
		$Engine->UseHashStruct(TRUE);
		$Engine->Set($Key,$Value);
		$this->AssertFileExists($ExpectedFilename);

		$Engine->Drop($Key);
		$this->AssertFileDoesNotExist($ExpectedFilename);

		return;
	}

}
