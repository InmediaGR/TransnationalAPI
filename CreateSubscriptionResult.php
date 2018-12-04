<?php

namespace App\Transnational;

use App\Transnational\ResponseTrait;

class CreateSubscriptionResult extends TransnationalResult
{

	use ResponseTrait\Timestamps;
	use ResponseTrait\AddonDiscount;
	use ResponseTrait\Amount;
	// /**
	// *	Helper Functions
	// */
	// public function isSuccess(){
	// 	return ($this->response_code == self::HTTP_OK && $this->get_request_status() == self::SUCCESS);
	// }

	/*
	*	Series of functions to get specific data return values
	*/
	public function get_id(){
		if($this->data){
			return $this->data->id;
		}
	}
	public function get_plan_id(){
		if($this->data){
			return $this->data->plan_id;
		}
	}

	public function get_customer_id(){
		if($this->data){
			return $this->data->customer->id;
		}
	}

	public function get_description(){
		if($this->data){
			return $this->data->description;
		}
	}
	public function get_amount(){
		if($this->data){
			return $this->data->amount;
		}
	}

	public function get_billing_cycle_interval(){
		if($this->data){
			return $this->data->billing_cycle_interval;
		}
	}
	public function get_billing_frequency(){
		if($this->data){
			return $this->data->billing_frequency;
		}
	}
	public function get_billing_days(){
		if($this->data){
			return $this->data->billing_days;
		}
	}
	public function get_duration(){
		if($this->data){
			return $this->data->duration;
		}
	}

	public function get_next_bill_date(){
		if($this->data){
			return $this->data->next_bill_date;
		}
	}

}
