<?php
namespace Transnational\APIValidation;

use \Transnational\APIException\InvalidAmountFormatException;
use \Transnational\APIException\InvalidRecurringBillingFormatException;

class BillingValidation extends TransactionValidation{

	const FREQUENCY_OPTIONS = [
		"MONTHLY" => "monthly",
		"TWICE_MONTHLY" => "twice_monthly"
	];

	public $interval;
	public $frequency;
	public $duration;
	public $days;


	public function __construct($interval,$frequency,$duration,$days){
		$this->interval = $interval;
		$this->frequency = $frequency;
		$this->duration = $duration;
		$this->days = $days;
	}

	/**
	 * Checks the amount to be proccessed
	 * @throws InvalidAmountFormatException - The format was inncorrect
	 */
	public function validate(){
		$this->validateInterval();
		$this->validateFrequency();
		$this->validateDays();
		return $this->exception;
	}

	private function validateInterval(){
		if(!is_int($this->interval)){
			$this->expiration = new InvalidRecurringBillingFormatException('interval');
		}
	}

	private function validateFrequency(){
		if(!in_array($this->frequency, self::FREQUENCY_OPTIONS)){
			$this->exception = new InvalidRecurringBillingFormatException('frequency');
		}
	}

	private function validateDuration(){
		if(!is_int($this->duration)){
			$this->expiration = new InvalidRecurringBillingFormatException('duration');
		}
	}

	private function validateDays(){
		$days = $this->days;
		if($this->frequency == "twice_monthly"){
			if(count($days) == 2){
				if(is_int($days[0]) && is_int($days[0])){
					if($days[0] < 1 || $days[0] > 31 || $days[1] < 1 || $days[1] > 31){
						$this->exception = new InvalidRecurringBillingFormatException('31');
					}else if($days[0] == $days[1]){
						$this->exception = new InvalidRecurringBillingFormatException('different');
					}
				}else{
					$this->exception = new InvalidRecurringBillingFormatException('days');
				}
			}else{
				$this->exception = new InvalidRecurringBillingFormatException('mismatch2');
			}
		}else{
			if(count($days) == 1){
				if(is_int($days[0])){
					if($days[0] < 1 || $days[0] > 31){
						$this->exception = new InvalidRecurringBillingFormatException('31');
					}
				}else{
					$this->exception = new InvalidRecurringBillingFormatException('days');
				}
			}else{
				$this->exception = new InvalidRecurringBillingFormatException('mismatch');
			}
		}
	}



}
