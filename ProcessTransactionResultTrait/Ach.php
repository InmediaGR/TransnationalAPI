<?php
namespace App\Transnational\ProcessTransactionResultTrait;
trait Ach{

	/**
	 *	Methods to get specific data from the response body
	 *	Ach specific methods
	 */

	public function get_rb_sec_code(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->sec_code;
		}
	}
	public function get_rb_account_type(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->account_type;
		}
	}
	public function get_rb_masked_account_number(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->masked_account_number;
		}
	}
	public function get_rb_routing_number(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->routing_number;
		}
	}

}
