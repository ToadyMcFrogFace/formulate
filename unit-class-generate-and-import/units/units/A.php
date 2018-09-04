<?php

namespace App\Units;

use App\Units\UnitInterface;

class A implements UnitInterface
{
	public function __construct() {
		$this->name = __CLASS__ ;
		$this->fullname = 'alaska';
		$this->calculations = [
			(object) [
				'units' => [
					'Co' => null,
					'T' => null,
				], 
				'formula' => '$this->Co + $this->T'
			]
		];
	}
}