<?php
namespace App\Utility\Pricing;

use App\Utility\Pricing\Unit;
use Cake\Network\Exception\NotFoundException;

class Pricing
{
	// user defined units
	public $undefinedUnits = [];
	public $units = [
		'M' => null,
		'V' => null,
		'Cms' => null,
		'ApiP' => null,
		'SD' => null,
		'SDA' => null,
		'CmsA' => null,
		// 'T' => null,
		'CPInV' => null,
		'VOnCP' => null,
		'MOnCP' => null,
		'CP' => null,
		'CPInM' => null,
		'SP' => null
	];

	public $unitFullNames = [
		'margin' => 'M',
		'vat' => 'V',
		'commission' => 'Cms',
		'api_price' => 'ApiP',
		'commission_amount' => 'CmsA',
		// 'tax' => 'T',
		'supplier_discount' => 'SD',
		'supplier_discount_amount' => 'SDA',
		'cost_price_including_vat' => 'CPInV',
		'vat_on_cost_price' => 'VOnCP',
		'cost_price' => 'CP',
		'cost_price_including_margin' => 'CPInM',
		'margin_on_cost_price' => 'MOnCP',
		'sell_price' => 'SP'
	];

	public $userDefinedUnits;

	private $__M;
	private $__V;
	private $__Cms;
	private $__ApiP;
	private $__SD;
	private $__SDA;
	private $__CmsA;
	// private $__T;
	private $__CPInV;
	private $__MOnCP;
	private $__VOnCP;
	private $__CP;
	private $__CPInM;
	private $__SP;


	public function __construct($arguments = []) 
	{
		$units = $this->units;
		$this->userDefinedUnits = $units;

		$this->__setUserDefinedUnits($arguments);
	}

	//  Margin %
	public function M($arguments = []) 
	{
		$this->__setUserDefinedUnits($arguments);
		return $this->__unit(__function__);
	}

	// //  Tax %
	// public function T($arguments = []) {
	// 	$this->__setUserDefinedUnits($arguments);
	// 	return $this->__unit(__function__);
	// }

	//  Vat %
	public function V($arguments = []) 
	{
		$this->__setUserDefinedUnits($arguments);
		$calculations = [  
			(object) [
				'units' => [
					'CP' => null, 
					'VOnCP' => null
				], 
				'formula' => '$this->CP / $this->VOnCP'
			]
		];
		return $this->__unit(__function__, $calculations);
	}

	//  Commission %
	public function Cms($arguments = []) 
	{
		$this->__setUserDefinedUnits($arguments);
		return $this->__unit(__function__);
	}

	//  API Price
	public function ApiP($arguments = []) 
	{
		$this->__setUserDefinedUnits($arguments);
		$calculations = [  
			(object) [
				'units' => [
					'CmsA' => null, 
					'VOnCP' => null, 
					'CP' => null
				], 
				'formula' => '$this->CmsA + $this->VOnCP + $this->CP'
			]
		];
		return $this->__unit(__function__, $calculations);
	}

	//  Commission Amount
	public function CmsA($arguments = []) 
	{
		$this->__setUserDefinedUnits($arguments);
		$calculations = [
			(object) [
				'units' => [
					'ApiP' => null, 
					'Cms' => null,
				], 
				'formula' => '$this->ApiP * ($this->Cms / (100 + $this->Cms))'
			]
		];
		return $this->__unit(__function__, $calculations);
	}

	//  Supplier Discount %
	public function SD($arguments = []) 
	{
		$this->__setUserDefinedUnits($arguments);
		return $this->__unit(__function__);
	}

	//   Supplier Discount Amount
	public function SDA($arguments = []) 
	{
		$this->__setUserDefinedUnits($arguments);
		$calculations = [
			(object) [
				'units' => [
					'CPInV' => null, 
					'SD' => null
				], 
				'formula' => '$this->CPInV * ($this->SD / (100 + $this->SD))'
			]
		];
		return $this->__unit(__function__, $calculations);
	}

	//  Cost Price Including VAT
	public function CPInV($arguments = []) 
	{
		$this->__setUserDefinedUnits($arguments);
		$calculations = [
			(object) [
				'units' => [
					'ApiP' => null, 
					'CmsA' => null
				], 
				'formula' => '$this->ApiP - $this->CmsA'
			]
		];
		return $this->__unit(__function__, $calculations);
	}

	//  Vat On Cost Price
	public function VOnCP($arguments = []) 
	{
		$this->__setUserDefinedUnits($arguments);
		$calculations = [
			(object) [
				'units' => [
					'CPInV' => null, 
					'V' => null
				],
				'formula' => '$this->CPInV * ($this->V / (100 + $this->V))'
			],
			(object) [
				'units' => [
					'CP' => null, 
					'V' => null
				],
				'formula' => '($this->CP / 100) * $this->V'
			]
		];
		return $this->__unit(__function__, $calculations);
	}

	//  Cost Price Excluding Vat
	public function CP($arguments = []) 
	{
		$this->__setUserDefinedUnits($arguments);
		$calculations = [
			(object) [
				'units' => [
					'CPInV' => null, 
					'VOnCP' => null,
					'SDA'	=> null
				], 
				'formula' => '$this->CPInV - $this->VOnCP - $this->SDA'
			]
		];
		return $this->__unit(__function__, $calculations);
	}

	//  Cost Price Including Margin
	public function CPInM($arguments = []) 
	{
		$this->__setUserDefinedUnits($arguments);
		$calculations = [
			(object)[
				'units' => [
					'CP' => null, 
					'M' => null
				],
				// Margin of 100% will break calculation
				'formula' => '$this->CP * (100 / (100 - $this->M))'
			]
		];
		return $this->__unit(__function__, $calculations);
	}

	 // Marin On Cost Price
	public function MOnCP($arguments = []) 
	{
		$this->__setUserDefinedUnits($arguments);
		$calculations = [
			(object)[
				'units' => [
					'CPInM' => null, 
					'CP' => null
				],
				'formula' => '$this->CPInM - $this->CP'
			]
		];
		return $this->__unit(__function__, $calculations);
	}

	//  Sell Price
	public function SP($arguments = []) 
	{
		$this->__setUserDefinedUnits($arguments);
		$calculations = [
			(object) [
				'units' => [
					'CPInM' => null, 
					'VOnCP' => null,
					// 'T' => null,
				],
				// 'formula' => '$this->CPInM + $this->VOnCP + $this->T'
				'formula' => '$this->CPInM + $this->VOnCP'
			]
		];
		return $this->__unit(__function__, $calculations);
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
				'formula' => '$this->' . $unit . ''
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
							exit('failed to set');
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
				throw new NotFoundException(__('Price could not be calculated'));
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