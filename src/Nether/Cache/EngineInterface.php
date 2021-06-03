<?php

namespace Nether\Cache;

use Nether\Cache\Struct\CacheObject;

interface EngineInterface {

	/////////////////////////////////////////////////////////////////////////
	// direct implementation required ///////////////////////////////////////

	public function
	Drop(string $Key):
	void;
	/*//
	@date 2021-05-30
	forget the data stored here.
	//*/

	public function
	Flush():
	void;
	/*//
	@date 2021-05-30
	forget all of the data ever.
	//*/

	public function
	Get(string $Key):
	mixed;
	/*//
	@date 2021-05-30
	get the data stored here.
	//*/

	public function
	GetCacheObject(string $Key):
	?CacheObject;
	/*//
	@date 2021-05-30
	get the full cache data wrapper stored here.
	//*/

	public function
	Has(string $Key):
	bool;
	/*//
	@date 2021-05-30
	check if there is data stored here.
	//*/

	public function
	Set(string $Key, mixed $Val, ?string $Origin=NULL):
	void;
	/*//
	@date 2021-05-30
	make there be data stored here.
	//*/

	/////////////////////////////////////////////////////////////////////////
	// implemented by extending EngineBase //////////////////////////////////

	public function
	BumpHitCount(int $Inc=1):
	static;

	public function
	BumpMissCount(int $Inc=1):
	static;

}
