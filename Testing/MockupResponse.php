<?php
namespace Transnational\Testing;

class MockupResponse{

	public $QueryTransaction = "";
	public $ProcessTransaction = "";
	public $CreatePlan = "";
	public $CreateSubscription = "";
	public $CreateCustomer = "";
	public $GetAllPlans = "";

	public $ErrorQueryTransaction = "";
	public $ErrorProcessTransaction = "";
	public $ErrorCreatePlan = "";
	public $ErrorCreateSubscription = "";
	public $ErrorCreateCustomer = "";
	public $ErrorGetAllPlans = "";

	public function get(String $object_name){
		return $this->$object_name;
	}
	public function getError(String $object_name)
	{
		$object_name = "Error" .$object_name;
		return $this->$object_name;
	}
}
