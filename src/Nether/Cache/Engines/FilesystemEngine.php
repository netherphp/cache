<?php

namespace Nether\Cache\Engines;
use Nether\Cache\Errors;

use ValueError;
use Nether\Cache\EngineInterface;
use Nether\Cache\Struct\CacheObject;

class FilesystemEngine
implements EngineInterface {
/*//
@date 2021-05-29
//*/

	protected ?string
	$Path = NULL;
	/*//
	@date 2021-05-30
	path everything gets saved to.
	//*/

	protected ?string
	$UseHashType = NULL;
	/*//
	@date 2021-06-01
	anything hash() accepts or NULL to not.
	//*/

	protected bool
	$UseHashStruct = FALSE;
	/*//
	@date 2021-06-01
	will mess around a bit to create subfolders that distribute the cache
	files across many directories to avoid filesystem limitations.
	//*/

	protected bool
	$UseKeyPath = TRUE;
	/*//
	@date 2021-06-01
	if true, keys that contain slashes will try to create subdirectories
	within the root of the cache path.
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

		$File = $this->GenerateFilename($Key);
		$Path = $this->GetFilePath($File);

		if($this->Has($Key))
		unlink($Path);

		return;
	}

	public function
	Flush():
	void {
	/*//
	@date 2021-05-30
	//*/

		// @todo
		// so this needs to iterate over the cache dir and delete all of
		// the files.

		return;
	}

	public function
	Get(string $Key):
	mixed {
	/*//
	@date 2021-05-29
	//*/

		$File = $this->GenerateFilename($Key);
		$Path = $this->GetFilePath($File);
		$Data = NULL;

		if($this->Has($Key)) {
			$Data = unserialize(file_get_contents($Path));

			if($Data instanceof CacheObject)
			return $Data->Data;
		}

		return NULL;
	}

	public function
	GetCacheObject(string $Key):
	?CacheObject {
	/*//
	@date 2021-05-29
	//*/

		$File = $this->GenerateFilename($Key);
		$Path = $this->GetFilePath($File);
		$Data = NULL;

		if($this->Has($Key)) {
			$Data = unserialize(file_get_contents($Path));

			if($Data instanceof CacheObject)
			return $Data;
		}

		return NULL;
	}

	public function
	Has(string $Key):
	bool {
	/*//
	@date 2021-05-29
	//*/

		$File = NULL;
		$Path = $Key;

		if(!str_starts_with($Key,'file://')) {
			$File = $this->GenerateFilename($Key);
			$Path = $this->GetFilePath($File);
		}

		return (
			file_exists($Path)
			&& is_readable($Path)
		);
	}

	public function
	Set(string $Key, mixed $Val, ?string $Origin=NULL):
	void {
	/*//
	@date 2021-05-29
	//*/

		$DS = DIRECTORY_SEPARATOR;
		$File = $this->GenerateFilename($Key);
		$Path = $this->GetFilePath($File);
		$Base = dirname($Path);

		// if enabled when a slash is detected in a key name then we will
		// try to create the subdirectory structure requested. a decent way
		// for the app to work around any jank filesystem restrictions.

		if(str_contains($File,$DS) && $this->UseKeyPath)
		static::MkDir($Base);

		// make sure the request to store the file is servicable.

		if(!file_exists($Base) || !is_writable($Base))
		throw new Errors\DirectoryNotCraftable($Base);

		// write the file.

		file_put_contents(
			$Path,
			serialize(new CacheObject($Val, Origin:$Origin))
		);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	GenerateFilename(string $Key):
	string {
	/*//
	@date 2021-06-01
	//*/

		if($this->UseHashType)
		return hash($this->UseHashType,$Key);

		return $Key;
	}

	public function
	GetPath():
	?string {
	/*//
	@date 2021-06-01
	//*/

		return $this->Path;
	}

	public function
	GetFilePath(string $Filename):
	string {
	/*//
	@date 2021-06-01
	//*/

		if(!$this->Path)
		throw new ValueError('path is not set');

		$Output = sprintf(
			'file://%s%s%s',
			$this->Path,
			DIRECTORY_SEPARATOR,
			$Filename
		);

		if(DIRECTORY_SEPARATOR === '\\')
		$Output = str_replace('\\','/',$Output);

		return $Output;
	}

	public function
	SetPath(string $Path):
	static {
	/*//
	@date 2021-06-01
	//*/

		// @todo 2021-05-31
		// value error is the wrong error. it looks like php has no good
		// ones so just extend your own from exception.

		if(!file_exists($Path) && !static::MkDir($Path))
		throw new Errors\DirectoryNotWritable($Path);

		if(!is_writable($Path))
		throw new Errors\DirectoryNotWritable($Path);

		$this->Path = $Path;
		return $this;
	}

	public function
	UseHashType(?string $Which):
	static {
	/*//
	@date 2021-06-01
	//*/

		$Valid = hash_algos();

		if(!in_array($Which,$Valid))
		throw new Errors\HashNotSupported($Which);

		$this->UseHashType = $Which;
		return $this;
	}

	public function
	UseHashStruct(bool $Should):
	static {
	/*//
	@date 2021-06-01
	//*/

		$this->UseHashStruct = $Should;
		return $this;
	}

	public function
	UseKeyPath(bool $Should):
	static {
	/*//
	@date 2021-06-01
	//*/

		$this->UseKeyPath = $Should;
		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	MkDir(string $Path, int $Mode=0666):
	bool {
	/*//
	@date 2021-06-01
	//*/

		$OldMask = umask(0);

		if(!@mkdir($Path, $Mode, TRUE))
		throw new Errors\DirectoryNotCraftable($Path);

		chmod($Path, $Mode);
		umask($OldMask);
		return TRUE;
	}

}
