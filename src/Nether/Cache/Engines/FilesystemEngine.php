<?php

namespace Nether\Cache\Engines;

use ValueError;
use Nether\Cache\EngineInterface;
use Nether\Cache\Struct\CacheObject;

class FilesystemEngine
implements EngineInterface {
/*//
@date 2021-05-29
//*/

	protected string
	$Path;
	/*//
	@date 2021-05-30
	path everything gets saved to.
	//*/

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(?string $Path=NULL) {
	/*//
	@date 2021-05-30
	//*/

		$this->SetPath($Path);

		return;
	}

	////////////////////////////////////////////////////////////////
	// implement EngineInterface ///////////////////////////////////

	public function
	Drop(string $Key):
	void {
	/*//
	@date 2021-05-29
	//*/

		if($this->Has($Key)) {
			$this->Data[$Key] = NULL;
			unset($this->Data[$Key]);
		}

		return;
	}

	public function
	Flush():
	void {
	/*//
	@date 2021-05-30
	//*/

		unset($this->Data);
		$this->Data = [];

		return;
	}

	public function
	Get(string $Key):
	mixed {
	/*//
	@date 2021-05-29
	//*/

		if($this->Has($Key))
		return $this->Data[$Key]->Data;

		return NULL;
	}

	public function
	GetCacheObject(string $Key):
	?CacheObject {
	/*//
	@date 2021-05-29
	//*/

		$Found = NULL;

		if($this->Has($Key)) {
			$Found = clone $this->Data[$Key];
			$Found->Engine = $this;
			return $Found;
		}

		return NULL;
	}

	public function
	Has(string $Key):
	bool {
	/*//
	@date 2021-05-29
	//*/

		return (
			array_key_exists($Key,$this->Data)
			&& ($this->Data[$Key] instanceof CacheObject)
		);
	}

	public function
	Set(string $Key, mixed $Val, ?string $Origin=NULL):
	void {
	/*//
	@date 2021-05-29
	//*/

		$this->Data[$Key] = new CacheObject($Val, Origin:$Origin);
		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	MkDir($Path):
	bool {

		return;
	}

	public function
	SetPath(string $Path):
	static {
	/*//
	@date 2021-05-31
	//*/

		// @todo 2021-05-31
		// value error is the wrong error. it looks like php has no good
		// ones so just extend your own from exception.

		if(!file_exists($Path))
		if(!$this->MkDir($Path))
		throw new ValueError('specified path does not exist and could not be created.');

		if(!is_writable($Path))
		throw new ValueError('the specified path is not writable.');

		return $this;
	}

}
