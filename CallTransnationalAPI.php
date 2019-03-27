<?php
namespace Transnational;

use \Transnational\APIException\InvalidMockupException;
use \Transnational\APIException\WrongCallException;
use \Transnational\GetAllPlans;
use \Transnational\QueryTransaction;
use \Transnational\Testing\MockupResponse;
use \Transnational\TransnationalAPI;

class CallTransnationalAPI{

	public $api_key;
	public $dev;
	public $level;

	public function __construct($api_key,$dev = false, $level = 0){
		$this->api_key = $api_key;
		$this->dev = $dev;
		$this->level = $level;
	}

	public function callAPI($call_object){
		return $this->call($call_object,TransnationalAPI::class);
	}

	public function QueryTransaction($query_transaction_object){
		return $this->call($query_transaction_object,QueryTransaction::class);
	}

	public function ProcessTransaction($process_transation_object){
		return $this->call($process_transation_object,ProcessTransaction::class);
	}

	public function CreatePlan($create_plan_object){
		return $this->call($create_plan_object,CreatePlan::class);
	}

	public function CreateSubscription($create_subscription_object){
		return $this->call($create_subscription_object,CreateSubscription::class);
	}

	public function CreateCustomer($create_customer_object){
		return $this->call($create_customer_object,CreateCustomer::class);
	}

	public function GetAllPlans($get_all_plans_object = false){
		if(!$get_all_plans_object){
			$get_all_plans_object = new GetAllPlans();
		}
		return $this->call($get_all_plans_object,GetAllPlans::class);
	}

	private function call($call_object, $clazz){
		if($call_object instanceof $clazz){
			$call_object->setEnv($this->dev,$this->level);
			return $call_object->run($this->api_key);
		}
		throw new WrongCallException($call_object,$clazz);
	}


	/**
	 *	 Used for forcing back a predefinned repsonse
	 *	 Good for test
	 *	@param MockupResponse $mockupResponse - a subclass of mockup response that contains the mockedup repsonses.
	 *	@return MockCallTransnationalAPI object - Faker version of this class that returns Mockup data
	 */
	public function useMockupResponse(MockupResponse $mockupResponse,$error = false){
		if($this->level == MockCallTransnationalAPI::MOCKUP_LEVEL && $this->dev){
			return new MockCallTransnationalAPI($mockupResponse,$this->api_key,$error);
		}
		throw new InvalidMockupException($this->level,$this->dev);
	}
}
