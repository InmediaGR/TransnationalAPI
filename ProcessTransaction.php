<?php

namespace Transnational;

use \Transnational\APIException\InvalidAmountFormatException;
use \Transnational\APIException\InvalidParameterException;
use \Transnational\APIException\InvalidPaymentException;
use \Transnational\APIUtil\AchObject;
use \Transnational\APIUtil\AddressObject;
use \Transnational\APIUtil\CardObject;
use \Transnational\APIValidation\TransactionAchValidation;
use \Transnational\APIValidation\TransactionAmountValidation;
use \Transnational\APIValidation\TransactionCardValidation;
use \Transnational\ProcessTransactionResult;
use \Transnational\RequestTrait;
use \Transnational\TransnationalAuthPostRequest;

/*
*	Class to handle the API call - Processing a Transaction
*	The call must be an authorized POST request
*/
class ProcessTransaction extends TransnationalAuthPostRequest
{
	use RequestTrait\Address;
	use RequestTrait\PaymentMethod;

	/**
	*	Transaction path
	*/
	const PATH = "transaction";


	/**
	*	Transaction type options
	*/
	const TYPE_OPTIONS = array(
		"TYPE_SALE" => "sale",
		"TYPE_AUTH" => "authorized",
	);


	/**
	*	Type passed to API
	*	@var string
	*/
	protected $type;

	/**
	*	Transactions Amount
	*	@var int
	*/
	protected $amount;

	/**
	*	Is transaction tax exempt
	*	@var boolean
	*/
	protected $tax_exempt = false;

	/**
	*	Tax Amount in cents
	*	@var int
	*/
	protected $tax_amount = 0;

	/**
	*	Shipping Amount in cents
	*	@var int
	*/
	protected $shipping_amount = 0;

	/**
	*	ISO 4217 currency
	*	@var string
	*/
	protected $currency = "USD";

	/**
	*	Text Field, 0 - 255 characters
	*	Transaction Description
	*	@var string
	*/
	protected $description = null;

	/**
	*	Transaction IPv4 Or IPv6 address
	*	@var string
	*/
	protected $ip_address = null;

	/**
	*	Weather to send email receipt
	*	@var boolean
	*/
	protected $email_receipt = false;

	/**
	*	Email Address to send receipt
	*	@var string
	*/
	protected $email_address = null;

	/**
	 * Constructor
	 * @param string $payment_method - payment_method
	 */
	function __construct($payment_method){
		$this->setPaymentMethod($payment_method);
	}

	/**
	 * set transaction type
	 * @param string $type - the transaction type
	 */
	public function setType($type){
		$this->type = $type;
		return $this;
	}

	/**
	 * set transaction type to sale
	 */
	public function setSale(){
		$this->setType(self::TYPE_OPTIONS['TYPE_SALE']);
		return $this;
	}

	/**
	 * set transaction type to auth
	 */
	public function setAuth(){
		$this->setType(self::TYPE_OPTIONS['TYPE_AUTH']);
		return $this;
	}

	/**
	 * set tax exemption boolean
	 * @param boolean $isExempt
	 */
	public function setTaxExemption($isExempt = false){
		$this->tax_exempt = $isExempt;
		return $this;
	}

	/**
	 * set transaction description
	 * @param string $description
	 */
	public function setDescription($description){
		$this->description = $description;
		return $this;
	}

	/**
	 * set transaction IP Address
	 * @param string $ip
	 */
	public function setIP($ip){
		/* TODO:  IF ip in correct IPV4/IPV6 else null */
		$this->ip = $ip_address;
		return $this;
	}

	/**
	 * set Email address for transaction recipt and tell if it should send
	 * @param string $email
	 * @param boolean $doSend
	 */
	public function setEmail($email, $doSend = true){
		$this->email_address = $email;
		$this->email_receipt = $doSend;
		return $this;
	}

	/**
	 * set Tranaction Amounts
	 * @param int $amount - the amount in cents
	 * @param int $tax_amount - the tax_amount in cents
	 * @param int $shipping_amount - the shipping_amount in cents
	 * @param string $currency - amount currency
	 */
	public function setAmount($amount,$tax_amount = null, $shipping_amount = null, $currency = "USD"){
		$this->amount = $amount;
		$this->tax_amount = $tax_amount;
		$this->shipping_amount = $shipping_amount;
		$this->currency = $currency;
		return $this;
	}


	public function setDollarAmount($amount,$tax_amount = null, $shipping_amount = null, $currency = "USD"){
		$this->amount = $amount * 100;
		$this->tax_amount = $tax_amount != null ? $tax_amount * 100 : null;
		$this->shipping_amount = $shipping_amount != null ? $shipping_amount * 100 : null;
		$this->currency = $currency;
		return $this;

	}

	/**
	* Overrides TransnationalAPI->getPOST method
	* Pulls the variables of this class into a json string for the POST request
	*/
	protected function getPOST(){
		$data = array();

		$payment_method = $this->addPaymentMethodPOST($data);
		if($payment_method == false){
			$this->exceptions[] = new InvalidPaymentException();
		}

		if(in_array($this->type,self::TYPE_OPTIONS)){
			$data['type'] = $this->type;
		}else{
			$this->exceptions[] = new InvalidParameterException('Transaction Type',implode(self::TYPE_OPTIONS,'","'));
		}

		$data['tax_exempt'] = $this->tax_exempt;

		$this->checkAndAddAmounts($data);
		$data['currency'] = $this->currency;

		$data['ip_address'] = $this->ip_address;
		if($this->email_receipt){
			$data['email_receipt'] = $this->email_receipt;
			$data['email_address'] = $this->email_address;
		}
		$this->addAddressPOST($data);
		return json_encode($data,JSON_UNESCAPED_SLASHES);

	}

	private function checkAndAddAmounts(&$data){
		$v = new TransactionAmountValidation($this->amount,$this->tax_amount,$this->shipping_amount);
		$v->getValidate($this->exceptions);

		$data['amount'] = $this->amount;
		if($this->tax_amount != null){
			$data['tax_amount'] = $this->tax_amount;
		}
		if($this->shipping_amount != null){
			$data['shipping_amount'] = $this->shipping_amount;
		}
	}

	/**
	* Overrides TransnationalAPI->getURL method
	* passes up the URL this API request will class
	*/
	protected function getPath(){
		return self::PATH;
	}

	/**
	* Wrapper method to the TransnationalAPI->callAPI method
	*/
	public function run($auth){
		return new ProcessTransactionResult($this->callAPI($auth),$this->getHTTPCode());
	}

	protected function newInstace($json_data){
		return $this;
	}

}
