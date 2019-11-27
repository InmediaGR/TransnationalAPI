<?php

namespace Transnational;

use \Transnational\APIValidation\BillingValidation;
use \Transnational\APIValidation\TransactionAmountValidation;
use \Transnational\RequestTrait;
use \Transnational\TransnationalAuthPostRequest;

/*
*	Class to handle the API call - Create A Plan
*	The call must be an authorized POST request
*/
class CreatePlan extends TransnationalAuthPostRequest
{

	use RequestTrait\Billing;
	const PATH = "recurring/plan";

	/**
	*	Plans name
	*	@var string
	*/
	protected $name;

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
	function __construct($name, $description = "recurring plan"){
		$this->name = $name;
		$this->description = $description;
	}

	public function setName($name){
		$this->name = $name;
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
		$data['name'] = $this->name;
		$data['description'] = $this->description;

		$v = new TransactionAmountValidation($this->amount);
		$v->getValidate($this->exceptions);
		$data['amount'] = $this->amount;

		$billing = $this->addBillingPOST($data);

		if($this->add_ons != null){
			$data['add_ons'] = $this->add_ons;
		}
		if($this->discounts != null){
			$data['discounts'] = $this->discounts;
		}

		return json_encode($data,JSON_UNESCAPED_SLASHES);

	}

	/**
	* Wrapper method to the TransnationalAPI->callAPI method
	*/
	public function run($auth){
		return new CreatePlanResult($this->callAPI($auth),$this->getHTTPCode());
	}

	protected function newInstace($json_data){
		return $this;
	}

}
