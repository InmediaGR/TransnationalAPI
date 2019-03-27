<?php
namespace Transnational\APIValidation;

use \Transnational\APIException\InvalidParameterException;

class TransactionAchValidation extends TransactionValidation{

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


	public function __construct($ach){
		$this->routing_number = $ach['routing_number'];
		$this->account_number = $ach['account_number'];
		$this->sec_code = $ach['sec_code'];
		$this->account_type = $ach['account_type'];
		$this->check_number = $ach['check_number'];
	}

	/**
	 * Checks the paramters of the passed in check
	 * @throws InvalidParameterException - More details about why the card failed
	 */
	public function validate(){
		$this->validateRoutingNumber();
		$this->validateAccountNumber();
		$this->validateSecCode();
		$this->validateAccountType();
		$this->validateCheckNumber();
		return $this->exception;
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
			$this->exception = new InvalidParameterException('SEC code',implode(self::SEC_CODE_OPTIONS,'","'));
		}
	}
	private function validateAccountType()
	{

	}
	private function validateCheckNumber()
	{

	}
}
