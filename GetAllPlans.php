<?php

namespace App\Transnational;

use \App\Transnational\TransnationalAuthPostRequest;
use \App\Transnational\ProcessTransactionResult;
use \App\Transnational\APIException\InvalidPaymentException;
use \App\Transnational\APIException\InvalidParameterException;
use \App\Transnational\APIException\InvalidAmountFormatException;
use \App\Transnational\APIValidation\TransactionCardValidation;
use \App\Transnational\APIValidation\TransactionAchValidation;
use \App\Transnational\APIValidation\TransactionAmountValidation;

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
