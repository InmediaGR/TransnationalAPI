<?php
class TransactionAchValidation{

	/**
	*	SEC code options
	*/
	const SEC_CODE_OPTIONS = array(
		'WEB',
		'CCD',
		'PPD',
		'TEL'
	);

	public $routing_number;
	public $account_number;
	public $sec_code;
	public $account_type;
	public $check_number;


	public function __construct($routing_number,$account_number,$sec_code,$account_type,$check_number){
		$this->routing_number = $routing_number;
		$this->account_number = $account_number;
		$this->sec_code = $sec_code;
		$this->account_type = $account_type;
		$this->check_number = $check_number;
	}

	/**
	 * Checks the paramters of the passed in check
	 * @throws InvalidParameterException - More details about why the card failed
	 */
	public function validateAch(){
		$this->validateRoutingNumber();
		$this->validateAccountNumber();
		$this->validateSecCode();
		$this->validateAccountType();
		$this->validateCheckNumber();
	}

	public function isCardValid(){
		$isValid = false;
		try{
			$this->validateAch();
			$isValid =  true;
		}catch(InvalidParameterException $ignore){
			$isValid = false;
		}
		return $isValid;
	}



	private function validateRoutingNumber()
	{
		/*TODO add other validations */
	}
	private function validateAccountNumber()
	{

	}
	private function validateSecCode()
	{
		$sec_code = strtoupper($this->sec_code);
		if(!in_array($sec_code, self::SEC_CODE_OPTIONS)){
			throw new InvalidParameterException('SEC code must be either ("' . implode(self::SEC_CODE_OPTIONS,'","') . '")');
		}
	}
	private function validateAccountType()
	{

	}
	private function validateCheckNumber()
	{

	}
}
