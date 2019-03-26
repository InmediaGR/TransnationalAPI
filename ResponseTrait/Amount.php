<?php
namespace App\Transnational\ResponseTrait;

/**
* Trait to reuse address returns from repsonses
*/
trait Amount{

	public function get_dollar_amount(){
		if($this->get_amount() !== null){
			return $this->get_amount() / (float) 100;
		}
	}

	public function get_string_dollar_amount(){
		if($this->get_amount() !== null){
			return number_format($this->get_amount() / (float) 100, 2);
		}
	}

}
