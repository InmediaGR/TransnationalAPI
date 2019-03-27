<?php

namespace Transnational;

class GetAllPlansResult extends TransnationalResult
{
	/**
	// *	Helper Functions
	// */
	// public function isSuccess(){
	// 	return ($this->response_code == self::HTTP_OK && $this->get_request_status() == self::SUCCESS);
	// }

	public function isPlans(){
		if($this->data){
			return count($this->data) >= 1;
		}
	}

	public function get_plans(){
		if($this->data){
			return $this->data;
		}
	}

	public function get_ids(){
		if($this->data){
			return array_map(function($plan){
				return $plan->id;
			},$this->data);
		}
	}

}
