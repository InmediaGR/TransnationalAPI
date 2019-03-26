<?php

namespace App\Transnational;

use App\Transnational\ResponseTrait;

class CreatePlanResult extends TransnationalResult
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
		TODO: I noticed in the return that these fields appeared. I don't want to damage the API wrapper so i will add these later
		"total_add_ons":0,
       "total_discounts":0,
	*/
	/*
	*	Series of functions to get specific data return values
	*/
	public function get_id(){
		if($this->data){
			return $this->data->id;
		}
	}
	public function get_name(){
		if($this->data){
			return $this->data->name;
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

}
