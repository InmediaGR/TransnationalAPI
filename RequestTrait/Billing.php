<?php
namespace Transnational\RequestTrait;

use \Transnational\APIValidation\BillingValidation;
use \Carbon;

/**
* Trait to reuse billing address for POST requests
*/
trait Billing{


	static $monthly =  BillingValidation::FREQUENCY_OPTIONS['MONTHLY'];
	static $twice_monthly = BillingValidation::FREQUENCY_OPTIONS['TWICE_MONTHLY'];

	/**
	*	Every x $billing_cycle_interval
	*	@var int
	*/
	protected $billing_cycle_interval = 1;

	/**
	*	Every x $billing_frequency
	*	@var int/String
	*/
	protected $billing_frequency = "";

	/**
	*	What day to run
	*	@var int
	*	@var string
	*/
	protected $billing_days = [1];

	/**
	*	How many billing cycles to run
	*	@var int
	*	@var string
	*/
	protected $duration = 0;


	public function setBillingCycleInterval($interval){
		$this->billing_cycle_interval = $interval;
	}

	public function setBillingFrequency($frequency){
		$this->billing_frequency = $frequency;
		if($this->billing_days == 1){
			$this->billing_days = "1,15";
		}
	}

	public function setBillingDuration($duration){
		$this->duration = $duration;
	}

	public function setBillingDays($day,$day2 = null){
		if($day2 == null){
			$this->billing_days = [$day];
		}else{
			$this->billing_days = [$day,$day2];
		}
	}

	public function setBilling($interval,$frequency,$duration,$day,$day2 = null){
		$this->setBillingCycleInterval($interval);
		$this->setBillingFrequency($frequency);
		$this->setBillingDays($day,$day2);
		$this->setBillingDuration($duration);
	}

	protected function addBillingPOST(&$data){
		$v = new BillingValidation($this->billing_cycle_interval,$this->billing_frequency,$this->duration,$this->billing_days);
		$v->getValidate($this->exceptions);
		$data['billing_cycle_interval'] = $this->billing_cycle_interval;
		$data['billing_frequency'] = $this->billing_frequency;
		$data['billing_days'] = implode(',',$this->billing_days);
		$data['duration'] = $this->duration;
		$data['Next_bill_date'] = getNextBillingDate($data['billing_days'][0]);
	}

	protected function setEndlessDuration(){
		$this->duration = 0;
	}

	protected function getNextBillingDate($billing_date){
		$current = Carbon::now();
		if($billing_date < $current->day){
			$current->addMonth();
		}
		$carbon = Carbon::createFromDate($current->year, $current->month, $billing_date);
		return $carbon->toDateString();
	}


}
