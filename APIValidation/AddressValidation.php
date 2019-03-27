<?php
namespace Transnational\APIValidation;

use \Transnational\APIException\InvalidParameterException;
use \Transnational\APIUtil\AddressObject;
use \Transnational\APIUtil\CardObject;

class AddressValidation extends TransactionValidation{

	const LEVEL_CREATE_CUSTOMER = 4;

	public $address;
	public $level;


	public function __construct($address,$level){
		$this->address = $address;
		$this->level = $level;
	}

	/**
	 * Checks the paramters of the passed in card
	 * @throws InvalidParameterException - More details about why the card failed
	 */
	public function validate(){
		if($this->address instanceof AddressObject){
			$this->validate1();
		}else{
			$this->exception = new InvalidParameterException('Address',"Address Object");
		}
		return $this->exception;
	}
	private function validate1()
	{
		if($level = 4){
			return true;
		}
	}
}
