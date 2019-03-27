<?php
namespace Transnational\APIUtil;

/*
*	Class to build up credit card object
*/
class AchObject implements PaymentObject{

	protected $ach = [];

	/**
	*	Card Options
	*/
	const ROUTING_NUMBER = 'routing_number';
	const ACCOUNT_NUMBER = 'account_number';
	const SEC_CODE = 'sec_code';
	const ACCOUNT_TYPE = 'account_type';
	const CHECK_NUMBER = 'check_number';
	const ACCOUNTHOLDER_AUTHENTICATION = 'accountholder_authentication';

	static function AccountholderAuth($dl_state,$dl_number){
		return array(
			'dl_state' =>  $dl_state,
			'dl_number' =>  $dl_number
		);
	}

	/**
	 * sets the card array
	 * @param string $entry_type - options ("keyed","swiped")
	 * @param string $number - card number
	 * @param string $expiration - payment_method
	 * @param string $cvc - payment_method
	 * @param string $cardholder_auth - payment_method
	 */
	public function __construct($routing_number,$account_number,$sec_code,$account_type,$check_number,$accountholder_authentication = null){
		$this->ach[self::ROUTING_NUMBER] = $routing_number;
		$this->ach[self::ACCOUNT_NUMBER] = $account_number;
		$this->ach[self::SEC_CODE] = $sec_code;
		$this->ach[self::ACCOUNT_TYPE] = $account_type;
		$this->ach[self::CHECK_NUMBER] = $check_number;
		if($accountholder_authentication != null){
			$this->ach[self::ACCOUNTHOLDER_AUTHENTICATION] = $accountholder_authentication;
		}else{
			$this->ach[self::ACCOUNTHOLDER_AUTHENTICATION] = new stdClass();
		}
		return $this;
	}

	public function get(){
		return $this->ach;
	}

	public function setRoutingNumber($routing_number){
		$this->ach[self::ROUTING_NUMBER] = $routing_number;
		return $this;
	}

	public function setAccountNumber($account_number){
		$this->ach[self::ACCOUNT_NUMBER] = $account_number;
		return $this;
	}

	public function setSecCode($sec_code){
		$this->ach[self::SEC_CODE] = $sec_code;
		return $this;
	}

	public function setAccountType($account_type){
		$this->ach[self::ACCOUNT_TYPE] = $account_type;
		return $this;
	}

	public function setCheckNumber($check_number){
		$this->ach[self::CHECK_NUMBER] = $check_number;
		return $this;
	}

	public function setAchAuth($accountholder_authentication){
		$this->ach[self::ACCOUNTHOLDER_AUTHENTICATION] = $accountholder_authentication;
		return $this;
	}
}
