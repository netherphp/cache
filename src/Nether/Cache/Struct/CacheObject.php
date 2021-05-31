<?php

namespace Nether\Cache\Struct;

use Nether\Cache\EngineInterface;

class CacheObject {

	public mixed
	$Data;

	public int
	$Time;

	public ?string
	$Origin;

	public ?EngineInterface
	$Engine;

	public function
	__Construct(mixed $Data, ?EngineInterface $Engine=NULL, ?string $Origin=NULL) {
		$this->Data = $Data;
		$this->Time = time();
		$this->Engine = $Engine;
		$this->Origin = $Origin;
		return;
	}

}
