<?php
namespace App\Utility\Pricing;

class Unit
{
	public $value;
	public $name;

	public function __construct($calculations, $arguments = array()) {
		$this->calculations = $calculations;
		$this->__setName();
		$this->calculate($arguments);
	}

	public function calculation() {
		return null;
	}

	private function __setName() {
		foreach ($this->calculations as $calculation) {
			if(count($calculation->units) == 1) {
				foreach ($calculation->units as $key => $value) {
					$this->name = $key;
				}
			}
		}
	}

	private function __setUserDefinedUnits($arguments) {

		if(!empty($arguments)) {
			foreach ($this->calculations as &$calculation) {
				// echo'<pre>';print_r($calculation);
				foreach ($calculation->units as $unit => $value) {
					// echo'<pre>';print_r($arguments);exit;
					// foreach ($unit as $name => $value) {
						if(array_key_exists($unit, $arguments)) {
							// set this units
							// for calculation 
							$this->{$unit} = $arguments[$unit];

							// maybe just name
							// $name = $arguments[$name];
							// set units in calculation
							$calculation->units[$unit] = $arguments[$unit];
							// $unit[$value] = $arguments[$unit];
						}
					// }
				}
			}
		}
	}

	// @return bool
	// returns true if all calculation units are set;
	private function __isCalculable($calculation) {
		foreach ($calculation->units as $unit) {
			if(!isset($unit)) {
				return false;
			}
		}
		return true;
	}

	public function calculate($arguments) {
		$this->__setUserDefinedUnits($arguments);
		foreach ($this->calculations as &$calculation) {
			if($this->__isCalculable($calculation)) {
				$this->value = eval( 'return (' . $calculation->formula . ');' );
				break;
			}
		}
	}
}