<?php

namespace App\Transnational;

use App\Transnational\APIValidation\BillingValidation;
use App\Transnational\APIValidation\TransactionAmountValidation;
use App\Transnational\RequestTrait;
use App\Transnational\TransnationalAuthPostRequest;

/*
*	Class to handle the API call - Create A Plan
*	The call must be an authorized POST request
*/
class CreateSubscription extends TransnationalAuthPostRequest
{
	use RequestTrait\Billing;
	const PATH = "recurring/subscription";

	/**
	*	Plans name
	*	@var string
	*/
	protected $plan_id;

	/**
	*	Plans name
	*	@var string
	*/
	protected $customer;

	/**
	*	Description of plan
	*	@var string
	*/
	protected $description;

	/**
	*	Amount to pay every interval
	*	@var int
	*/
	protected $amount;

	/**
	*	add-on object
	*	@var object
	*/
	protected $add_ons = null;

	/**
	*	discount object
	*	@var object
	*/
	protected $discounts = null;

	/**
	 * Constructor
	 * @param string $payment_method - payment_method
	 */
	function __construct($plan_id, $description = "recurring plan"){
		$this->plan_id = $plan_id;
		$this->description = $description;
	}

	public function setPlan($plan_id){
		$this->plan_id = $plan_id;
		return $this;
	}

	public function setCustomer($customer_id){
		$customer_object = new \stdClass();
		$customer_object->id = $customer_id;
		$this->customer = $customer_object;
		return $this;
	}

	public function setCustomerObject($customer_object){
		$this->customer = $customer_object;
		return $this;

	}

	public function setDescription($description){
		$this->description = $description;
		return $this;
	}

	public function setAmount($amount){
		$this->amount = $amount;
		return $this;
	}

	public function setDollarAmount($amount){
		$this->amount = $amount * 100;
		return $this;
	}

	protected function getPath(){
		return  self::PATH;
	}

	/**
	* Overrides TransnationalAPI->getPOST method
	* Pulls the variables of this class into a json string for the POST request
	*/
	protected function getPOST(){
		$data = array();
		$data['description'] = $this->description;
		$data['plan_id'] = $this->plan_id;

		$data['customer'] = $this->customer;

		$v = new TransactionAmountValidation($this->amount);
		$v->getValidate($this->exceptions);
		$data['amount'] = $this->amount;

		$this->addBillingPOST($data);


		if($this->add_ons != null){
			$data['add_ons'] = $this->add_ons;
		}
		if($this->discounts != null){
			$data['discounts'] = $this->discounts;
		}
		return json_encode($data);
	}

	/**
	* Wrapper method to the TransnationalAPI->callAPI method
	*/
	public function run($auth){
		return new CreateSubscriptionResult($this->callAPI($auth),$this->getHTTPCode());
	}

	protected function newInstace($json_data){
		return $this;
	}

}
