<?php
namespace Transnational;

use \Transnational\APIException\InvalidMockupException;
use \Transnational\APIException\WrongCallException;
use \Transnational\GetAllPlans;
use \Transnational\QueryTransaction;
use \Transnational\Testing\MockupResponse;
use \Transnational\TransnationalAPI;
use \Transnational\TransnationalResult;

class MockCallTransnationalAPI{

	protected $api_key;
	protected $mockupResponse;
	protected $level;
	protected $error;
	const MOCKUP_LEVEL = TransnationalAPI::MOCKUP_LEVEL;

	public function __construct(MockupResponse $mockupResponse,$api_key = null,  $error = false){
		$this->api_key = $api_key;
		$this->mockupResponse = $mockupResponse;
		$this->error = $error;
	}

	// public function callAPI($call_object){
	// 	return $this->call($call_object,TransnationalAPI::class);
	// }

	public function QueryTransaction($call_object = null, $asObject = true){
		$mockup = !$this->error ? $this->mockupResponse->QueryTransaction : $this->mockupResponse->ErrorQueryTransaction;
		if($asObject){
			return new QueryTransactionResult($mockup, TransnationalResult::HTTP_OK);
		}
		return $mockedup;
	}

	public function ProcessTransaction($call_object = null, $asObject = true){
		$mockup = !$this->error ? $this->mockupResponse->ProcessTransaction : $this->mockupResponse->ErrorProcessTransaction;
		if($asObject){
			return new ProcessTransactionResult($mockup, TransnationalResult::HTTP_OK);
		}
		return $mockedup;
	}

	public function CreatePlan($call_object = null, $asObject = true){
		$mockup = !$this->error ? $this->mockupResponse->CreatePlan : $this->mockupResponse->ErrorCreatePlan;
		if($asObject){
			return new CreatePlanResult($mockup, TransnationalResult::HTTP_OK);
		}
		return $mockedup;
	}

	public function CreateSubscription($call_object = null, $asObject = true){
		$mockup = !$this->error ? $this->mockupResponse->CreateSubscription : $this->mockupResponse->ErrorCreateSubscription;
		if($asObject){
			return new CreateSubscriptionResult($mockup, TransnationalResult::HTTP_OK);
		}
		return $mockedup;
	}

	public function CreateCustomer($call_object = null, $asObject = true){
		$mockup = !$this->error ? $this->mockupResponse->CreateCustomer : $this->mockupResponse->ErrorCreateCustomer;
		if($asObject){
			return new CreateCustomerResult($mockup, TransnationalResult::HTTP_OK);
		}
		return $mockedup;
	}

	public function GetAllPlans($call_object = null, $asObject = true){
		$mockup = !$this->error ? $this->mockupResponse->GetAllPlans : $this->mockupResponse->ErrorGetAllPlans;
		if($asObject){
			return new GetAllPlansResult($mockup, TransnationalResult::HTTP_OK);
		}
		return $mockedup;
	}

	public function callAPI($call_object, $clazz, $asClazz = null){
		if($call_object == null || $call_object instanceof $clazz){
			$reflect = new \ReflectionClass($clazz);
			$mockedup = $this->mockupResponse->get($reflect->getShortName());
			if($asClazz != null && is_subclass_of($asClazz,TransnationalResult::class)){
				return new $asClazz($mockedup, TransnationalResult::HTTP_OK);
			}
			return $mockedup;
		}
		throw new WrongCallException($call_object,$clazz);
	}
}
