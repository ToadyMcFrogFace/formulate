<?php

namespace App\Units;

use App\Units\UnitInterface;

class T implements UnitInterface
{
	public function __construct() {
		$this->name = __CLASS__;
		$this->fullname = 'texas';
		$this->calculations = [
			(object) [
				'units' => [
					'A' => null, 
					'Co' => null
				], 
				'formula' => '$this->A - $this->Co'
			]
		];
	}
}