<?php

namespace Transnational;

use \Transnational\ProcessTransactionResultTrait\Ach;
use \Transnational\ProcessTransactionResultTrait\Card;
use \Transnational\ProcessTransactionResultTrait\Universal;
use \Transnational\ResponseTrait;

class ProcessTransactionResult extends TransnationalResult
{

	use Universal;
	use Card;
	use Ach;
	use ResponseTrait\Address;
	use ResponseTrait\Amount;
	/**
	*	Helper Functions
	*/
	public function isSuccess(){
		if(parent::isSuccess()){
			$response_code = $this->get_response_code();
			if($response_code != null && $response_code == self::SUCCESS_CODE){
				return true;
			}
		}
		return false;
		// if($this->response_code == self::HTTP_OK && $this->get_request_status() == self::SUCCESS){
		// 	$response_code = $this->get_response_code();
		// 	if($response_code != null && $response_code == self::SUCCESS_CODE){
		// 		return true;
		// 	}
		// }
		// return false;

	}

	/*
	*	Semi Human readable processor responses. More for developers and company
	*/
	public function getProcessorResponse(){
		$response = TransnationalResponseCodes::getResponse($this->get_rb_processor_response_code());
		return $response ? $response : $this->get_message();
	}

	/*
	*	Human Readable processor Error text. For end users
	*/
	public function getProcessorDefinition(){
		$response = TransnationalResponseCodes::getDefinition($this->get_rb_processor_response_code());
		return $response ? $response : $this->get_message();
	}

	/*
	*	Semi Human readable endpoint responses. More for developers and company
	*/
	public function getEndpointResponse(){
		$response = TransnationalEndpointResponseCodes::getResponse($this->get_response_code());
		return $response ? $response : $this->get_message();
	}

	/*
	*	Human Readable endpoint Error text. For end users
	*/
	public function getEndpointMeaning(){
		$response = TransnationalEndpointResponseCodes::getMeaning($this->get_response_code());
		return $response ? $response : $this->get_message();
	}
	/*
	*	Human Readable endpoint Error action. For end users
	*/
	public function getEndpointAction(){
		$response = TransnationalEndpointResponseCodes::getAction($this->get_response_code());
		return $response ? $response : "N/A";
	}


	/*
	*	Series of functions to get specific data return values
	*/
	public function get_id(){
		if($this->data){
			return $this->data->id;
		}
	}
	public function get_user_id(){
		if($this->data){
			return $this->data->user_id;
		}
	}
	public function get_user_name(){
		if($this->data){
			return $this->data->user_name;
		}
	}
	public function get_idempotency_key(){
		if($this->data){
			return $this->data->idempotency_key;
		}
	}
	public function get_idempotency_time(){
		if($this->data){
			return $this->data->idempotency_time;
		}
	}
	public function get_type(){
		if($this->data){
			return $this->data->type;
		}
	}
	public function get_amount(){
		if($this->data){
			return $this->data->amount;
		}
	}

	public function get_amount_authorized(){
		if($this->data){
			return $this->data->amount_authorized;
		}
	}
	public function get_amount_captured(){
		if($this->data){
			return $this->data->amount_captured;
		}
	}
	public function get_amount_settled(){
		if($this->data){
			return $this->data->amount_settled;
		}
	}
	public function get_tip_amount(){
		if($this->data){
			return $this->data->tip_amount;
		}
	}
	public function get_processor_id(){
		if($this->data){
			return $this->data->processor_id;
		}
	}
	public function get_processor_type(){
		if($this->data){
			return $this->data->processor_type;
		}
	}
	public function get_processor_name(){
		if($this->data){
			return $this->data->processor_name;
		}
	}
	public function get_payment_method(){
		if($this->data){
			return $this->data->payment_method;
		}
	}
	public function get_payment_type(){
		if($this->data){
			return $this->data->payment_type;
		}
	}
	public function get_tax_amount(){
		if($this->data){
			return $this->data->tax_amount;
		}
	}
	public function get_tax_exempt(){
		if($this->data){
			return $this->data->tax_exempt;
		}
	}
	public function get_shipping_amount(){
		if($this->data){
			return $this->data->shipping_amount;
		}
	}
	public function get_currency(){
		if($this->data){
			return $this->data->currency;
		}
	}
	public function get_description(){
		if($this->data){
			return $this->data->description;
		}
	}
	public function get_order_id(){
		if($this->data){
			return $this->data->order_id;
		}
	}
	public function get_po_number(){
		if($this->data){
			return $this->data->po_number;
		}
	}
	public function get_ip_address(){
		if($this->data){
			return $this->data->ip_address;
		}
	}
	public function get_transaction_source(){
		if($this->data){
			return $this->data->transaction_source;
		}
	}
	public function get_email_receipt(){
		if($this->data){
			return $this->data->email_receipt;
		}
	}
	public function get_email_address(){
		if($this->data){
			return $this->data->email_address;
		}
	}
	public function get_customer_id(){
		if($this->data){
			return $this->data->customer_id;
		}
	}
	public function get_referenced_transaction_id(){
		if($this->data){
			return $this->data->referenced_transaction_id;
		}
	}
	public function get_status(){
		if($this->data){
			return $this->data->status;
		}
	}
	/*
	 *	Not to be confused with getResponse() in parent method;
	 */
	public function get_response(){
		if($this->data){
			return $this->data->response;
		}
	}
	public function get_response_code(){
		if($this->data){
			return $this->data->response_code;
		}
	}
	public function get_created_at(){
		if($this->data){
			return $this->data->created_at;
		}
	}
	public function get_captured_at(){
		if($this->data){
			return $this->data->captured_at;
		}
	}
	public function get_settled_at(){
		if($this->data){
			return $this->data->settled_at;
		}
	}

	/**
	*	Return Entire response body
	*/
	public function get_response_body(){
		if($this->data){
			return $this->data->response_body;
		}
	}

	/**
	*	Return Entire response body's payment method
	*/
	public function get_rb_method(){
		if($this->data){
			foreach($this->data->response_body as $key => $value){
				return $key;
			}
		}
	}

	/**
	 *	Return all response body data
	 *	Essentially everyhing except payment method
	 */
	public function get_rb_data(){
		if($this->data){
			foreach($this->data->response_body as $key => $value){
				return $value;
			}
		}
	}




}
