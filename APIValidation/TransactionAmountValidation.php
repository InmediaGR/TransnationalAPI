<?php 
class TransactionAmountValidation{

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
	public function validateAmounts(){
		self::isValid($this->amount);
		if($this->tax_amount){
			self::isValid($this->tax_amount);
		}
		if($this->shipping_amount){
			self::isValid($this->shipping_amount);
		}
	}
	public static function isValid($amount){
		if(is_int($amount)){
			return true;
		}
		throw new InvalidAmountFormatException("Invalid Amount: Amount should be recorded in cents. (Ex. $1.00 should be 100)");
	}
}