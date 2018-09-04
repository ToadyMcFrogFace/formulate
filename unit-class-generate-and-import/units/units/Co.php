<?php

namespace App\Units;

use App\Units\UnitInterface;

class Co implements UnitInterface
{
	public function __construct() {
		$this->name = __CLASS__;
		$this->fullname = 'cold';
		$this->calculations = [
			(object) [
				'units' => [
					'A' => null,
					'T' => null,
				], 
				'formula' => '$this->A - $this->T'
			]
		];
	}
}