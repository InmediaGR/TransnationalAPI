<?php
namespace Transnational\CreateCustomerResultTrait;
trait Card{


	/**
	 *	Methods to get specific data from the payment method
	 *	Card specific methods
	 */

	public function get_payment_id(){
		$pm = $this->get_payment_data();
		if($pm){
			return $pm->id;
		}
	}
	public function get_payment_card_type(){
		$pm = $this->get_payment_data();
		if($pm){
			return $pm->card_type;
		}
	}
	public function get_payment_first_six(){
		$pm = $this->get_payment_data();
		if($pm){
			return $pm->first_six;
		}
	}
	public function get_payment_last_four(){
		$pm = $this->get_payment_data();
		if($pm){
			return $pm->last_four;
		}
	}
	public function get_payment_masked_card(){
		$pm = $this->get_payment_data();
		if($pm){
			return $pm->masked_card;
		}
	}

	public function get_payment_expiration_date(){
		$pm = $this->get_payment_data();
		if($pm){
			return $pm->expiration_date;
		}
	}
	public function get_payment_created_at(){
		$pm = $this->get_payment_data();
		if($pm){
			return $pm->created_at;
		}
	}
	public function get_payment_updated_at(){
		$pm = $this->get_payment_data();
		if($pm){
			return $pm->updated_at;
		}
	}

}
