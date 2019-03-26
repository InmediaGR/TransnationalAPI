<?php
namespace App\Transnational\APIValidation;

use App\Transnational\APIException\InvalidParameterException;
use App\Transnational\APIUtil\CustomerCardObject;

class TransactionCustomerCardValidation extends TransactionValidation{


	public $number;
	public $expiration;


	public function __construct($card){
		$this->number = $card[CustomerCardObject::NUMBER];
		$this->expiration = $card[CustomerCardObject::EXPIRATIONDATE];
	}

	/**
	 * Checks the paramters of the passed in card
	 * @throws InvalidParameterException - More details about why the card failed
	 */
	public function validate(){
		$this->validateCardNumber();
		$this->validateExperation();
		return $this->exception;
	}

	private function validateCardNumber()
	{
		$cc_number = strval($this->number);
		if(!$this->is_valid_luhn($cc_number)){
			$this->exception = new InvalidParameterException('Card','valid and pass luhn check');
		}
	}
	private function validateExperation()
	{

	}
	private function validateCVC()
	{

	}

	private function is_valid_luhn($number) {
		settype($number, 'string');
		$sumTable = array(
			array(0,1,2,3,4,5,6,7,8,9),
			array(0,2,4,6,8,1,3,5,7,9));
		$sum = 0;
		$flip = 0;
		for ($i = strlen($number) - 1; $i >= 0; $i--) {
			$sum += $sumTable[$flip++ & 0x1][$number[$i]];
		}
		return $sum % 10 === 0;
	}
}
