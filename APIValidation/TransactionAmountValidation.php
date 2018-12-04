<?php
namespace App\Transnational\APIValidation;

use App\Transnational\APIException\InvalidAmountFormatException;

class TransactionAmountValidation extends TransactionValidation{

	protected $invalid_field = false;
	public $amount;
	public $tax_amount;
	public $shipping_amount;


	public function __construct($amount,$tax_amount = null, $shipping_amount = null){
		$this->amount = $amount;
		$this->tax_amount = $tax_amount;
		$this->shipping_amount = $shipping_amount;
	}

	/**
	 * Checks the amount to be proccessed
	 * @throws InvalidAmountFormatException - The format was inncorrect
	 */
	public function validate(){
		$this->validateAmount();
		if($this->tax_amount != null){
			$this->validateTaxAmount();
		}
		if($this->shipping_amount != null){
			$this->validateShippingAmount();
		}
		if($this->invalid_field != null){
			$this->exception = new InvalidAmountFormatException($this->invalid_field);
		}
		return $this->exception;
	}

	private function validateAmount(){
		$this->invalid_field = self::isValid($this->amount) ? $this->invalid_field : "amount";
	}

	private function validateShippingAmount(){
		$this->invalid_field = self::isValid($this->shipping_amount) ? $this->invalid_field : "shipping_amount";
	}

	private function validateTaxAmount(){
		$this->invalid_field = self::isValid($this->tax_amount) ? $this->invalid_field : "tax_amount";
	}

	public static function isValid($amount){
		return is_int($amount);
	}
}
