<?php
namespace App\Transnational\ResponseTrait;

/**
* Trait to reuse addon and discount responses
*/
trait AddonDiscount{

	public function get_total_add_ons(){
		if($this->data){
			return $this->data->total_add_ons;
		}
	}
	public function get_total_discounts(){
		if($this->data){
			return $this->data->total_discounts;
		}
	}
	public function get_add_ons(){
		if($this->data){
			return $this->data->total_add_ons;
		}
	}
	public function get_discounts(){
		if($this->data){
			return $this->data->total_discounts;
		}
	}
}
