<?php
class TransactionCardValidation{

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


	public function __construct($entry_type,$number,$expiration,$cvc){
		$this->entry_type = $entry_type;
		$this->number = $number;
		$this->expiration = $expiration;
		$this->cvc = $cvc;

	}

	/**
	 * Checks the paramters of the passed in card
	 * @throws InvalidParameterException - More details about why the card failed
	 */
	public function validateCard(){
		$this->validateEntryType();
		$this->validateCardNumber();
		$this->validateExperation();
		$this->validateCVC();
	}

	public function isCardValid(){
		$isValid = false;
		try{
			$this->validateCard();
			$isValid =  true;
		}catch(InvalidParameterException $ignore){
			$isValid = false;
		}
		return $isValid;
	}

	private function validateEntryType(){
		if(!in_array($this->entry_type,self::CARD_ENTRY_TYPE_OPTIONS)){
			throw new InvalidParameterException('Entry Type must be either ("' . implode(self::CARD_ENTRY_TYPE_OPTIONS,'","') . '")');
		}
	}

	private function validateCardNumber()
	{
		$cc_number = strval($this->number);
		if(!$this->is_valid_luhn($cc_number)){
			throw new InvalidParameterException('Card Must be valid and pass luhn check');
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
