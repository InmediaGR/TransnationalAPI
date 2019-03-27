<?php
namespace Transnational\ResponseTrait;

use \Transnational\APIUtil\AddressObject;

/**
* Trait to reuse address returns from repsonses
*/
trait Address{

	/**
	*	@param bool $returnNullIfEmpty - Determines output if no Address
	*	If True - return output as null
	*	If false - return output as array of empty strings
	*/
	public function get_billing_address($returnNullIfEmpty = true){
		if($this->data){
			if(!$returnNullIfEmpty){
				return $this->data->billing_address;
			}
			foreach ($this->data->billing_address as $key => $value) {
				if($value != ""){
					return $this->data->billing_address;
				}
			}
		}
		return null;
	}
	public function get_shipping_address($returnNullIfEmpty = true){
		if(!$returnNullIfEmpty){
			return $this->data->shipping_address;
		}
		foreach ($this->data->shipping_address as $key => $value) {
			if($value != ""){
				return $this->data->shipping_address;
			}
		}
		return null;
	}
}
