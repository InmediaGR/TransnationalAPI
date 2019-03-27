<?php
namespace Transnational\APIValidation;

use \Transnational\APIException\InvalidParameterException;
use \Transnational\APIUtil\CardObject;

class TransactionCardValidation extends TransactionValidation{

	/**
	*	Transaction type options
	*/
	const CARD_ENTRY_TYPE_OPTIONS = array(
		"TYPE_KEYED" => "keyed",
		"TYPE_SWIPED" => "swiped"
	);

	public $entry_type;
	public $number;
	public $expiration;
	public $cvc;


	public function __construct($card){
		$this->entry_type = $card[CardObject::ENTRY];
		$this->number = $card[CardObject::NUMBER];
		$this->expiration = $card[CardObject::EXPIRATIONDATE];
		$this->cvc = $card[CardObject::CVC];
	}

	/**
	 * Checks the paramters of the passed in card
	 * @throws InvalidParameterException - More details about why the card failed
	 */
	public function validate(){
		$this->validateEntryType();
		$this->validateCardNumber();
		$this->validateExperation();
		$this->validateCVC();
		return $this->exception;
	}

	private function validateEntryType(){
		if(!in_array($this->entry_type,self::CARD_ENTRY_TYPE_OPTIONS)){
			$this->exception = new InvalidParameterException('Entry Type',implode(self::CARD_ENTRY_TYPE_OPTIONS,'","'));
		}
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
