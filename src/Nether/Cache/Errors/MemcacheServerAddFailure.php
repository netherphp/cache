<?php

namespace Nether\Cache\Errors;

use Exception;

class MemcacheServerAddFailure
extends Exception {

	public function
	__Construct(string $Host, int|string $Port) {
		parent::__Construct("Memcache: Error Adding Server {$Host}:{$Port}");
		return;
	}

}
