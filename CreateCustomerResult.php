<?php

namespace Transnational;

use \Transnational\CreateCustomerResultTrait\Card;
use \Transnational\ResponseTrait;

class CreateCustomerResult extends TransnationalResult
{

	use ResponseTrait\Address;
	use ResponseTrait\Timestamps;
	use Card;

	// /**
	// *	Helper Functions
	// */
	// public function isSuccess(){
	// 	if($this->response_code == self::HTTP_OK && $this->get_request_status() == self::SUCCESS){
	// 		return true;
	// 	}
	// 	return false;
	// }

	public function get_id(){
		if($this->data){
			return $this->data->id;
		}
	}

	public function description(){
		if($this->data){
			return $this->data->description;
		}
	}

	public function get_payment_method(){
		if($this->data){
			return $this->data->payment_method;
		}
	}

	public function get_payment_method_type(){
		if($this->data){
			return $this->data->payment_method_type;
		}
	}

	/**
	*	Return Entire response body's payment method
	*/
	public function get_pm_method(){
		if($this->data){
			foreach($this->data->payment_method as $key => $value){
				return $key;
			}
		}
	}
	/**
	 *	Return all payment_data data
	 *	Essentially everyhing except payment method
	 */
	public function get_payment_data(){
		if($this->data){
			foreach($this->data->payment_method as $key => $value){
				return $value;
			}
		}
	}



}
