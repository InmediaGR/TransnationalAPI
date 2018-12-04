<?php
namespace App\Transnational\RequestTrait;

use App\Transnational\APIException\InvalidPaymentException;
use App\Transnational\APIUtil\AchObject;
use App\Transnational\APIUtil\CardObject;
use App\Transnational\APIValidation\TransactionAchValidation;
use App\Transnational\APIValidation\TransactionCardValidation;
trait PaymentMethod{

	/**
	*	Payment method options
	*/

	protected static $method_card = "card";
	protected static $method_ach = "ach";
	protected static $method_customer = "customer";
	protected static $method_terminal = "terminal";

	protected static $card_entry_type_options = TransactionCardValidation::CARD_ENTRY_TYPE_OPTIONS;


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
			$v = new TransactionCardValidation($this->payment_object->get());
			$v->getValidate($this->exceptions);
			$method_data = $this->payment_object->get();
			break;
			case self::$method_ach:
			$v = new TransactionAchValidation($this->payment_object->get());
			$v->getValidate($this->exceptions);
			$method_data = $this->payment_object->get();
			break;
			case self::$method_customer:
			$method_data = $this->customer;
			break;
			case self::$method_terminal:
			$method_data = $this->terminal;
			break;
			default:
			return null;
		}
		return array($this->payment_method => $method_data );
	}
	/**
	 * @param array $data - post array to add to
	 * @return boolean
	 */
	public function addPaymentMethodPOST(&$data){
		if($this->getPaymentMethod() == null){
			$data['payment_method'] = $payment_method;
			return true;
		}
		return false;
	}
	/**
	 * Creates an Instance with payment method method_card
	 * @param CardObject $card_object - object holding card data
	 */
	static function Card(CardObject $card_object){
		$payment = new static(self::$method_card);
		return $payment->setCard($card_object);
	}

	/**
	 * sets the card object
	 * @param CardObject $card_object - object holding card data
	 */
	public function setCard(CardObject $card_object){
		$this->payment_object = $card_object;
		return $this;
	}

	/**
	 * Creates an Instance with payment method method_ach
	 * @param AchObject $ach_object - object holding ach data
	 */
	static function Ach(AchObject $ach_object){
		$payment = new static(self::$method_ach);
		return $payment->setAch($ach_object);
	}

	/**
	 * sets the ach array
	 * @param AchObject $ach_object - object holding ach data
	 */
	public function setAch(AchObject $ach_object){
		$this->payment_object = $ach_object;
		return $this;
	}


}
