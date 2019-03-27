<?php

namespace Transnational;

use \Transnational\APIException\InvalidPaymentException;
use \Transnational\APIUtil\CardObject;
use \Transnational\APIUtil\CustomerCardObject;
use \Transnational\APIValidation\BillingValidation;
use \Transnational\APIValidation\TransactionAmountValidation;
use \Transnational\APIValidation\TransactionCustomerCardValidation;
use \Transnational\CreateCustomerResult;
use \Transnational\RequestTrait;
use \Transnational\TransnationalAuthPostRequest;

/*
*	Class to handle the API call - Create A Plan
*	The call must be an authorized POST request
*/
class CreateCustomer extends TransnationalAuthPostRequest
{
	use RequestTrait\Address;
	const PATH = "customer";




	/**
	*	Customer Payment method options
	*/
	protected static $method_card = "card";
	protected static $method_ach = "ach";


	/**
	*	Text Field, 0 - 255 characters
	*	Customer Description
	*	@var string
	*/
	protected $description = null;

	function __construct($payment_method){
		$this->setPaymentMethod($payment_method);
	}

	/**
	 * set customer description
	 * @param string $description
	 */
	public function setDescription($description){
		$this->description = $description;
		return $this;
	}



	/**
	*	The method of payment used for determining other required fields
	*	Set on creation
	*	@var string
	*/
	protected $payment_method;

	protected $payment_object = null;

	public function setPaymentMethod($payment_method){
		$this->payment_method = $payment_method;
	}

	public function getPaymentMethod(){
		$method_data = array();
		if($this->payment_object == null){
			return null;
		}
		switch($this->payment_method){
			case self::$method_card:
			$v = new TransactionCustomerCardValidation($this->payment_object->get());
			$v->getValidate($this->exceptions);
			$method_data = $this->payment_object->get();
			break;
			case self::$method_ach:
			$v = new TransactionAchValidation($this->payment_object->get());
			$v->getValidate($this->exceptions);
			$method_data = $this->payment_object->get();
			break;
			default:
			return null;
		}
		return array($this->payment_method => $method_data );
	}

	/**
	 * Creates an Instance with payment method method_card
	 * @param CustomerCardObject $customer_card_object - object holding card data
	 */
	static function Card(CustomerCardObject $customer_card_object){
		$payment = new static(self::$method_card);
		return $payment->setCard($customer_card_object);
	}

	/**
	 * sets the card object
	 * @param CustomerCardObject $customer_card_object - object holding card data
	 */
	public function setCard(CustomerCardObject $customer_card_object){
		$this->payment_object = $customer_card_object;
		return $this;
	}

	/**
	 * sets the card object
	 * @param CardObject $card_object - object holding card data
	 */
	public function setCard2(CardObject $card_object){
		$this->payment_object = $card_object->toCardObject();
		return $this;
	}

	/* Comming Soon */
	// /**
	//  * Creates an Instance with payment method method_ach
	//  * @param AchObject $ach_object - object holding ach data
	//  */
	// static function Ach(CustomerAchObject $ach_object){
	// 	$payment = new static(self::$method_ach);
	// 	return $payment->setAch($ach_object);
	// }

	// /**
	//  * sets the ach array
	//  * @param AchObject $ach_object - object holding ach data
	//  */
	// public function setAch(CustomerAchObject $ach_object){
	// 	$this->payment_object = $ach_object;
	// 	return $this;
	// }


	protected function getPath(){
		return  self::PATH;
	}

	/**
	* Overrides TransnationalAPI->getPOST method
	* Pulls the variables of this class into a json string for the POST request
	*/
	protected function getPOST(){
		$data = array();
		if($this->description != null){
			$data['description'] = $this->description;
		}

		$payment_method = $this->getPaymentMethod();
		if($payment_method != null){
			$data['payment_method'] = $payment_method;
		}else{
			$this->exceptions[] = new InvalidPaymentException();
		}
		$this->addAddressPOST($data);
		return json_encode($data);
	}

	/**
	* Wrapper method to the TransnationalAPI->callAPI method
	*/
	public function run($auth){
		return new CreateCustomerResult($this->callAPI($auth),$this->getHTTPCode());
	}

	protected function newInstace($json_data){
		return $this;
	}

}
