<?php
namespace App\Transnational\APIUtil;

/*
*	Class to build up credit card object
*/
class CardObject implements PaymentObject{

	protected $card = [];

	/**
	*	Card Options
	*/
	const ENTRY = 'entry_type';
	const NUMBER = 'number';
	const CARD_NUMBER = 'card_number';
	const EXPIRATIONDATE = 'expiration_date';
	const CVC = 'cvc';
	const CARDHOLDER_AUTHENTICATION = 'cardholder_authentication';

	static function CardholderAuth($condition,$eci,$cavv,$xid){
		return array(
			'condition' =>  $condition,
			'eci' =>  $eci,
			'cavv' =>  $cavv,
			'xid' =>  $xid
		);
	}

	public static function fromCustomerCardObject(CustomerCardObject $customer_card_object){
		$card = $customer_card_object->get();
		return new static(null,
			$card[CardObject::NUMBER],
			$card[CardObject::EXPIRATIONDATE]
		);
	}

	public function toCustomerCardObject(){
		return new CustomerCardObject(
			$this->card[self::NUMBER],
			$this->card[self::EXPIRATIONDATE]
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
	public function __construct($entry_type,$number,$expiration,$cvc,$cardholder_auth = null){
		$this->card[self::ENTRY] = $entry_type;
		$this->card[self::NUMBER] = $number;
		$this->card[self::EXPIRATIONDATE] = $expiration;
		$this->card[self::CVC] = $cvc;
		if($cardholder_auth != null){
			$this->card[self::CARDHOLDERAUTHENTICATION] = $cardholder_auth;
		}
		return $this;
	}

	public function get(){
		return $this->card;
	}

	public function setEntryType($entry){
		$this->address_object[self::ENTRY] = $entry;
		return $this;
	}

	public function setCardNumber($number){
		$this->address_object[self::NUMBER] = $number;
		return $this;
	}

	public function setExpirationDate($expirationdate){
		$this->address_object[self::EXPIRATIONDATE] = $expirationdate;
		return $this;
	}

	public function setCVC($cvc){
		$this->address_object[self::CVC] = $cvc;
		return $this;
	}

	public function setCardAuth($cardholder_auth){
		$this->address_object[self::CARDHOLDERAUTHENTICATION] = $cardholder_auth;
		return $this;
	}
}
