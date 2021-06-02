<?php

namespace Nether\Cache\Errors;

use Exception;

class DirectoryNotWritable
extends Exception {

	public function
	__Construct(mixed $Path) {
		parent::__Construct("{$Path} is not writable");
		return;
	}

}
