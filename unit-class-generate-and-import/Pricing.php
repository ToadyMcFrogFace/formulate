<?php
namespace App;

use App\Utility\Pricing\Unit;

class Formulate
{
	// user defined units
	public $undefinedUnits = [];

	public $units = [];

	public $unitFullNames = [];

	public $userDefinedUnits;

	private $classes = [];

	// Import all classes that were generated
	// something like 

	public function __construct($arguments = []) 
	{
		$this->__import();
		$this->userDefinedUnits = $this->units;
		$this->__setUserDefinedUnits($arguments);
	}

	private function __import()
	{
		foreach (glob("classes/*.php") as $filename)
		{
			// will have to checkout method name is not blacklisted ie. a name that is already in use
			include $filename;
			$this->classes[] = $filename;
			$this->units[$filename->name] = NULL;
			$this->unitFullNames[$filename->unitFullName] = $filename->name;
			$this->{'__' . $filename->name} = NULL;
		}
	}

	__call($method, $args) {
	//  Commission Amount
		if(in_array($method, $this->units)) {
			$this->__setUserDefinedUnits($arguments);
			$calculations = $this->units[$method]->calculations;
			return $this->__unit($method, $calculations);
		}
	}

	private function __setUserDefinedUnits($arguments = []) 
	{
		if(!empty($arguments)) {
			if($this->__isRedefiningUnit($arguments)) {
				$this->__emptyUnits();
			}
			foreach ($arguments as $key => $value) {
				if(array_key_exists($key, $this->userDefinedUnits)) {
					$this->userDefinedUnits[$key] = $value;
				} else {
					$this->undefinedUnits[$key] = $value;
				}
			}
			foreach ($this->userDefinedUnits as $key => $value) {
				$this->units[$key] = $value;
			}
		}
	}

	private function __isRedefiningUnit($arguments) 
	{
		foreach ($arguments as $key => $value) {
			if(isset($this->userDefinedUnits[$key])) {
				return true;
			}
		}
		return false;
	}

	private function __emptyUnits() 
	{
		foreach ($this->units as $key => $value) {
			$this->{'__' . $key} = NULL;
			$this->units[$key] = NULL;
		}
	}

	private function __unit($unit, $calculations = array()) 
	{
		if(isset($this->{'__' . $unit}->value)) {
			return $this->{'__' . $unit};
		}
		
		$defaultCalculation =  [
			(object) [
				'units' => [
					$unit => null
				], 
				'formula' => '$this->' . $unit . '' //todo: check if there is a reason for the   . ''   at the end
			]
		];

		$calculations = array_merge($defaultCalculation, $calculations);

		if(!isset($this->{'__' . $unit})) {
			$this->{'__' . $unit} = new Unit($calculations, $this->units);
		}

		if(!isset($this->{'__' . $unit}->value)) {
			$unitCalculated = false;
			foreach ($this->{'__' . $unit}->calculations as $calculation) {
				foreach ($calculation->units as $requiredUnitName => $value) {
					if($requiredUnitName != $unit && !$value) {
						if(!isset($this->{'__' . $requiredUnitName})) {
							$this->{$requiredUnitName}();
						}
						if(!isset($this->{'__' . $requiredUnitName})) {
							throw new Exception('Failed to set');
						}
						if(isset($this->{'__' . $requiredUnitName}->value)) {
							$unitCalculated = true;
							$this->units[$requiredUnitName] = $this->{'__' . $requiredUnitName}->value;
						}
					}
				}
			}

			if($unitCalculated) {
				$this->{'__' . $unit}->calculate($this->units);
			}
		}
		
		if(isset($this->{'__' . $unit}->value)) {
			$this->units[$this->{'__' . $unit}->name] = $this->{'__' . $unit}->value;
		}

		return $this->{'__' . $unit};
	}

	public function recalculate($arguments = []) 
	{
		$calculable = true;
		$this->__setUserDefinedUnits($arguments);
		foreach ($this->userDefinedUnits as $key => $value) {
			if(!isset($this->{'__' . $key})) {
				$this->{$key}();
			}
			if(!isset($this->{'__' . $key})) {
				exit('failed to set');
			}
			if(!isset($this->{'__' . $key}->value)) {
				throw new Exception('Price could not be calculated');
			}
		}

		return $calculable;
	}

	public function pricing() 
	{
		$return = [];
		foreach ($this->unitFullNames as $key => $value) {
			$return[$key] = $this->units[$value];
		}
		return $return;
	}
}