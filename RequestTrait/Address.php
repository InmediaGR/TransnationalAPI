<?php
namespace Transnational\RequestTrait;

use \Transnational\APIUtil\AddressObject;
use \Transnational\APIValidation\AddressValidation;

/**
* Trait to reuse billing address for POST requests
*/
trait Address{

	/**
	*	Array of billing address fields
	*	@var array
	*/
	protected $billing_address = [];

	/**
	*	Array of shipping address fields
	*	@var array
	*/
	protected $shipping_address = [];

	/**
	 * set the billing address
	 * @param BillingObject $billingAddress - the billing Address
	 */
	public function setBillingAddress(AddressObject $billingObject){
		$this->billing_address = $billingObject;
		return $this;
	}

	/**
	 * set the shipping address
	 * @param ShippingObject $shippingAddress - the shipping Address
	 */
	public function setShippingAddress(AddressObject $shippingObject){
		$this->shipping_address = $shippingObject;
		return $this;
	}

	/**
	 * @param array $data - post array to add to
	 * @return int 0-3
	 * 0 - None added;
	 * 1 - Billing added;
	 * 2 - Shipping Added;
	 * 3 - Billing and Shipping added;
	 */
	public function addAddressPOST(array &$data){
		$x = 0;
		if($this->billing_address){
			$v = new AddressValidation($this->billing_address,AddressValidation::LEVEL_CREATE_CUSTOMER);
			$v->getValidate($this->exceptions);
			$data['billing_address'] = $this->billing_address->get();
			$x++;
		}
		if($this->shipping_address){
			$v = new AddressValidation($this->shipping_address,AddressValidation::LEVEL_CREATE_CUSTOMER);
			$v->getValidate($this->exceptions);
			$data['shipping_address'] = $this->shipping_address->get();
			$x+=2;
		}
		return $x;
	}
}
