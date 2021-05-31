<?php

namespace Nether\Cache\Struct;

use Nether\Cache\EngineInterface;

class EngineObject {

	public EngineInterface
	$Engine;

	public int
	$Priority;

	public function
	__Construct(EngineInterface $Engine, int $Priority=50) {
		$this->Engine = $Engine;
		$this->Priority = $Priority;
		return;
	}

}
