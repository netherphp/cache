<?php

namespace Nether\Cache\Errors;

use Exception;

class DirectoryNotCraftable
extends Exception {

	public function
	__Construct(mixed $Path) {
		parent::__Construct("{$Path} could not be created. perhaps its parents do not exist or are not writable themselves.");
		return;
	}

}
