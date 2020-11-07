<?php

namespace Nether\Cache;
use \Nether;

class Appcache extends DriverInterface {

	protected $Storage = [];
	/*//
	@type array
	where the appcache will store its data.
	//*/

	////////////////
	////////////////

	public function Drop($key) {
		if(array_key_exists($key,$this->Storage)) {
			unset($this->Storage[$key]);
		}

		return;
	}

	public function Flush() {
		unset($this->Storage);
		$this->Storage = [];

		return;
	}

	public function Get($key) {
		if(array_key_exists($key,$this->Storage)) {
			++$this->Hit;
			return $this->Storage[$key];
		} else {
			++$this->Miss;
			return;
		}
	}

	public function Set($key,$value,$ttl=null) {
		$this->Storage[$key] = $value;

		return;
	}

}
