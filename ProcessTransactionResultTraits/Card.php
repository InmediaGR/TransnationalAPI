<?php
trait Card{


	/**
	 *	Methods to get specific data from the response body
	 *	Card specific methods
	 */

	public function get_rb_card_type(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->card_type;
		}
	}
	public function get_rb_first_six(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->first_six;
		}
	}
	public function get_rb_last_four(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->last_four;
		}
	}
	public function get_rb_masked_card(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->masked_card;
		}
	}
	public function get_rb_expiration_date(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->expiration_date;
		}
	}

	public function get_rb_avs_response_code(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->avs_response_code;
		}
	}
	public function get_rb_cvv_response_code(){
		$rb = $this->get_rb_data();
		if($rb){
			return $rb->cvv_response_code;
		}
	}

}
