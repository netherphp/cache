<?php

namespace Nether\Cache\Errors;

use Exception;

class HashNotSupported
extends Exception {

	public function
	__Construct(mixed $Algo) {
		parent::__Construct("{$Algo} is not supported by this system");
		return;
	}

}
