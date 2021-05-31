<?php

namespace Nether\Cache\Struct;

use Nether\Cache\EngineInterface;

class CacheObject {

	public mixed
	$Data;

	public int
	$Time;

	public ?EngineInterface
	$Source;

	public function
	__Construct(mixed $Data, ?EngineInterface $Source=NULL) {
		$this->Data = $Data;
		$this->Time = time();
		$this->Source = $Source;
		return;
	}

}
