<?php

namespace Transnational;

use \Transnational\TransnationalAuthPostRequest;
use \Transnational\ProcessTransactionResult;
use \Transnational\APIException\InvalidPaymentException;
use \Transnational\APIException\InvalidParameterException;
use \Transnational\APIException\InvalidAmountFormatException;
use \Transnational\APIValidation\TransactionCardValidation;
use \Transnational\APIValidation\TransactionAchValidation;
use \Transnational\APIValidation\TransactionAmountValidation;

/*
*	Class to handle the API call - Get All Plans
*	The call must be an authorized GET request
*/
class GetAllPlans extends TransnationalAuthGetRequest
{

	/**
	*	Transaction path
	*/
	const PATH = "recurring/plans";

	/**
	* Overrides TransnationalAPI->getURL method
	* passes up the URL this API request will class
	*/
	protected function getPath(){
		return "recurring/plans";
	}

	/**
	* Wrapper method to the TransnationalAPI->callAPI method
	*/
	public function run($auth){
		return new GetAllPlansResult($this->callAPI($auth),$this->getHTTPCode());
	}

	protected function newInstace($json_data){
		return $this;
	}

}
