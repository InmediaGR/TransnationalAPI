<?php
namespace Transnational\ResponseTrait;

/**
* Trait to reuse address returns from repsonses
*/
trait Timestamps{

	/**
	*	@param bool $returnNullIfEmpty - Determines output if no Address
	*	If True - return output as null
	*	If false - return output as array of empty strings
	*/
	public function get_created_at(){
		if($this->data){
			return $this->data->created_at();
		}
	}
	public function get_updated_at(){
		if($this->data){
			return $this->data->updated_at();
		}
	}
}
