<?php
namespace Transnational\APIUtil;

/*
*	Class to build up credit card object
*/
class CustomerCardObject implements PaymentObject{

	protected $card = [];

	/**
	*	Customer-Card Options
	*/
	const NUMBER = 'card_number';
	const EXPIRATIONDATE = 'expiration_date';

	public static function fromCardObject(CardObject $card_object){
		$card = $card_object->get();
		return new static($card[CardObject::NUMBER],$card[CardObject::EXPIRATIONDATE]
		);
	}

	public function toCardObject(){
		return new CardObject(
			null,
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
	public function __construct($number,$expiration){
		$this->card[self::NUMBER] = $number;
		$this->card[self::EXPIRATIONDATE] = $expiration;
		return $this;
	}

	public function get(){
		return $this->card;
	}

	public function setCardNumber($number){
		$this->address_object[self::NUMBER] = $number;
		return $this;
	}

	public function setExpirationDate($expirationDate){
		$this->address_object[self::EXPIRATIONDATE] = $expirationDate;
		return $this;
	}
}
