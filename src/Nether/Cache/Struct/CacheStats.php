<?php

namespace Nether\Cache\Struct;

use Nether\Cache\EngineInterface;
use Nether\Cache\Manager;

class CacheStats {

	public int
	$Hit = 0;

	public int
	$Miss = 0;

	public float
	$HitRatio = 0.0;

	public float
	$MissRatio = 0.0;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(EngineInterface|Manager $Input) {
	/*//
	@date 2021-06-03
	//*/

		$this->Hit = $Input->GetHitCount();
		$this->HitRatio = $Input->GetHitRatio();

		$this->Miss = $Input->GetMissCount();
		$this->MissRatio = $Input->GetMissRatio();

		return;
	}

}
