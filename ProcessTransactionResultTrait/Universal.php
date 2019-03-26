<?php
namespace App\Transnational\ProcessTransactionResultTrait;
trait Universal{
	/*
	 *	Depending on method the proccessor specific can be different
	 */
	public function get_rb_processor_specific(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->processor_specific;
		}
	}

	/**
	 *	Methods to get specific data from the response body
	 * These are returned universally
	 */
	public function get_rb_id(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->id;
		}
	}

	public function get_rb_response(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->response;
		}
	}
	public function get_rb_response_code(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->response_code;
		}
	}
	public function get_rb_auth_code(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->auth_code;
		}
	}
	public function get_rb_processor_response_code(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->processor_response_code;
		}
	}
	public function get_rb_processor_response_text(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->processor_response_text;
		}
	}
	public function get_rb_processor_type(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->processor_type;
		}
	}
	public function get_rb_processor_id(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->processor_id;
		}
	}

	public function get_rb_created_at(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->created_at;
		}
	}

	public function get_rb_updated_at(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->updated_at;
		}
	}

}
