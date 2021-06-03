<?php

namespace Nether\Cache\Engines;
use Nether\Cache\Errors;

use FilesystemIterator;
use ValueError;
use Nether\Cache\EngineBase;
use Nether\Cache\EngineInterface;
use Nether\Cache\Struct\CacheObject;

class FilesystemEngine
extends EngineBase
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

	protected bool
	$UseFullDrop = TRUE;
	/*//
	@date 2021-06-01
	if true, will perform extra effort to remove subdirectories if they
	are empty after dropping the file.
	//*/

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(
		string $Path=NULL,
		string $UseHashType=NULL,
		bool $UseHashStruct=FALSE,
		bool $UseFullDrop=TRUE
	) {
	/*//
	@date 2021-05-30
	//*/

		if($Path !== NULL)
		$this->SetPath($Path);

		if($UseHashType !== NULL)
		$this->UseHashType($UseHashType);

		$this->UseHashStruct($UseHashStruct);
		$this->UseFullDrop($UseFullDrop);

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

		if($this->Has($Path))
		unlink($Path);

		if($this->UseFullDrop)
		$this->Drop_CleanupEmptyTree($File);

		return;
	}

	protected function
	Drop_CleanupEmptyTree($File):
	void {
	/*//
	@date 2021-06-01
	walks through this path and cleans up any empty subdirectories it might
	leave laying around otherwise.
	//*/

		$Iter = 0;
		$Cur = NULL;
		$Depth = substr_count($File,'/') + 1;

		if(str_contains($File,'/')) {
			for($Iter = 1; $Iter < $Depth; $Iter++) {
				$Cur = dirname($File,$Iter);

				if(count(scandir($this->GetFilePath($Cur))) === 2)
				rmdir($this->GetFilePath($Cur));
			}
		}

		return;
	}

	public function
	Flush():
	void {
	/*//
	@date 2021-05-30
	//*/

		if(!$this->Path)
		throw new ValueError('path is not set');

		////////

		$Plunger = new class {
			public function
			Plunge(string $Path):
			void {
				$Files = new FilesystemIterator(
					$Path,
					FilesystemIterator::KEY_AS_PATHNAME
					| FilesystemIterator::CURRENT_AS_FILEINFO
					| FilesystemIterator::SKIP_DOTS
				);

				foreach($Files as $File) {
					if($File->IsDir()) {
						$this->Plunge($File->GetPathname());
						rmdir($File->GetPathname());
					}

					else
					unlink($File->GetPathname());
				}

				return;
			}
		};

		$Plunger->Plunge($this->Path);
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

		if($this->Has($Path)) {
			$Data = unserialize(file_get_contents($Path));

			if($Data instanceof CacheObject) {
				$this->BumpHitCount();
				return $Data->Data;
			}
		}

		$this->BumpMissCount();
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

		if($this->Has($Path)) {
			$Data = unserialize(file_get_contents($Path));

			if($Data instanceof CacheObject) {
				$this->BumpHitCount();
				return $Data;
			}
		}

		$this->BumpMissCount();
		return NULL;
	}

	public function
	Has(string $Key):
	bool {
	/*//
	@date 2021-05-29
	able to handle both the cache key but also a validly structured file uri.
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

		$File = $this->GenerateFilename($Key);
		$Path = $this->GetFilePath($File);
		$Base = dirname($Path);

		$UseKeyStruct = (
			$this->UseKeyPath
			|| ($this->UseHashType && $this->UseHashStruct)
		);

		// if enabled when a slash is detected in a key name then we will
		// try to create the subdirectory structure requested. a decent way
		// for the app to work around any jank filesystem restrictions.

		if($UseKeyStruct && str_contains($File,'/'))
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

		$Output = $Key;

		if($this->UseHashType) {
			$Output = hash($this->UseHashType,$Key);

			if($this->UseHashStruct)
			$Output = sprintf(
				'%s/%s',
				substr($Output,0,2),
				substr($Output,2)
			);
		}

		return $Output;
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
	GetFilePath(string $Filename):
	string {
	/*//
	@date 2021-06-01
	//*/

		if(!$this->Path)
		throw new ValueError('path is not set');

		$Output = sprintf(
			'file://%s/%s',
			$this->Path,
			$Filename
		);

		if(DIRECTORY_SEPARATOR === '\\')
		$Output = str_replace('\\','/',$Output);

		return $Output;
	}

	public function
	UseHashType(?string $Which):
	static {
	/*//
	@date 2021-06-01
	//*/

		if($Which !== NULL)
		if(!static::IsHashSupported($Which))
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

	public function
	UseFullDrop(bool $Should):
	static {
	/*//
	@date 2021-06-01
	//*/

		$this->UseFullDrop = $Should;
		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	IsHashSupported(string $HashName):
	bool {
	/*//
	@date 2021-06-02
	//*/

		return in_array($HashName,hash_algos());
	}

	static public function
	MkDir(string $Path, int $Mode=0755):
	bool {
	/*//
	@date 2021-06-01
	//*/

		if(is_dir($Path))
		return TRUE;

		$OldMask = umask(0);

		if(!@mkdir($Path, $Mode, TRUE))
		throw new Errors\DirectoryNotCraftable($Path);

		chmod($Path, $Mode);
		umask($OldMask);
		return TRUE;
	}

}
