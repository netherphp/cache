<?php

namespace Nether\Cache;

class CacheData {

	public mixed
	$Data = NULL;

	public int
	$Time = 0;

	public function
	__Construct(mixed $Data) {
		$this->Data = $Data;
		$this->Time = time();
		return;
	}

}
