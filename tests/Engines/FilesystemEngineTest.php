<?php

use PHPUnit\Framework\TestCase;

use Nether\Cache\Engines;
use Nether\Cache\Struct\CacheObject;

class FilesystemEngineTest
extends TestCase {

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

		$Engine->Set($Key,$Value);
		$this->AssertFileExists($ExpectedFilename);

		$Result = $Engine->Get($Key);
		$this->AssertEquals($Value,$Result);

		return;
	}

}
