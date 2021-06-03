<?php

namespace Nether\Cache\Traits;

trait CacheHitStats {

	protected int
	$CacheHit = 0;

	protected int
	$CacheMiss = 0;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	BumpHitCount(int $Inc=1):
	static {
	/*//
	@date 2021-06-02
	//*/

		$this->CacheHit += $Inc;
		return $this;
	}

	public function
	BumpMissCount(int $Inc=1):
	static {
	/*//
	@date 2021-06-02
	//*/

		$this->CacheMiss += $Inc;
		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	GetHitCount():
	int {
	/*//
	@date 2021-06-02
	//*/

		return $this->CacheHit;
	}

	public function
	GetHitRatio():
	float {
	/*//
	@date 2021-06-02
	//*/

		return ($this->CacheHit / ($this->CacheHit + $this->CacheMiss));
	}

	public function
	GetMissCount():
	int {
	/*//
	@date 2021-06-02
	//*/

		return $this->CacheMiss;
	}

	public function
	GetMissRatio():
	float {
	/*//
	@date 2021-06-02
	//*/

		return ($this->CacheMiss / ($this->CacheHit + $this->CacheMiss));
	}

}
