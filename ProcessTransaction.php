<?php
require_once "TransnationalAuthPostRequest.php";
require_once "ProcessTransactionResult.php";
require_once "APIExceptions/InvalidPaymentException.php";
require_once "APIExceptions/InvalidParameterException.php";
require_once "APIExceptions/InvalidAmountFormatException.php";
require_once "APIValidation/TransactionCardValidation.php";
require_once "APIValidation/TransactionAchValidation.php";
require_once "APIValidation/TransactionAmountValidation.php";

/*
*	Class to handle the API call - Processing a Transaction
*	The call must be an authorized POST request
*/
class ProcessTransaction extends TransnationalAuthPostRequest
{

	/**
	*	Transaction path
	*/
	const PATH = "transaction";

	/**
	*	Payment method options
	*/
	const METHOD_CARD = "card";
	const METHOD_ACH = "ach";
	const METHOD_CUSTOMER = "customer";
	const METHOD_TERMINAL = "terminal";


	/**
	*	Transaction type options
	*/
	const TYPE_OPTIONS = array(
		"TYPE_SALE" => "sale",
		"TYPE_AUTH" => "authorized",
	);
	const CARD_ENTRY_TYPE_OPTIONS = TransactionCardValidation::CARD_ENTRY_TYPE_OPTIONS;

	/**
	*	Address Options
	*/
	const ADDRESS = array(
		// 'ADDRESS_ID' => 'address_id',
		'FIRST_NAME' => 'first_name',
		'LAST_NAME' => 'last_name',
		'COMPANY' => 'company',
		'ADDRESS_LINE_1' => 'address_line_1',
		'ADDRESS_LINE_2' => 'address_line_2',
		'CITY' => 'city',
		'STATE' => 'state',
		'POSTAL_CODE' => 'postal_code',
		'COUNTRY' => 'country',
		'EMAIL' => 'email',
		'PHONE' => 'phone',
		'FAX' => 'fax'
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
	*	The method of payment used for determining other required fields
	*	Set on creation
	*	@var string
	*/
	protected $payment_method;

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
	*	holds credit card fields if payment_type = "card"
	*	Ex:
	*	array(
	*		'entry' => entry
	*		'number' => number
	*		'expiration' => expiration
	*		'cvc' => cvc
	*		'auth' => AuthArray
	*	)
	*
	*	@var array
	*/
	protected $card;
	protected $ach;
	protected $customer;
	protected $terminal;

	/**
	 * Constructor
	 * @param string $payment_method - payment_method
	 */
	function __construct($payment_method){
		$this->payment_method = $payment_method;
		switch($payment_method){
			case self::METHOD_CARD:
			$this->card = array();
			break;
			case self::METHOD_ACH:
			$this->ach = array();
			break;
			case self::METHOD_CUSTOMER:
			$this->customer = array();
			break;
			case self::METHOD_TERMINAL:
			$this->terminal = array();
			break;
			default:
			throw new InvalidPaymentException("Invalid Payment Method");
		}
	}

	/**
	 * Creates an Instance with payment method METHOD_CARD
	 * @param string $entry_type - options ("keyed","swiped")
	 * @param string $number - card number
	 * @param string $expiration - payment_method
	 * @param string $cvc - payment_method
	 * @param string $cardholder_auth - payment_method
	 */
	static function Card($entry_type,$number,$expiration,$cvc,$cardholder_auth = null){
		$process = new ProcessTransaction(self::METHOD_CARD);
		$process->setCard($entry_type,$number,$expiration,$cvc,$cardholder_auth);
		return $process;

	}

	/**
	 * sets the card array
	 * @param string $entry_type - options ("keyed","swiped")
	 * @param string $number - card number
	 * @param string $expiration - payment_method
	 * @param string $cvc - payment_method
	 * @param string $cardholder_auth - payment_method
	 */
	public function setCard($entry_type,$number,$expiration,$cvc,$cardholder_auth = null){
		$v = new TransactionCardValidation($entry_type,$number,$expiration,$cvc);
		$v->validateCard();
		$this->card['entry'] = $entry_type;
		$this->card['number'] = $number;
		$this->card['expiration_date'] = $expiration;
		$this->card['cvc'] = $cvc;
		if($cardholder_auth != null){
			$this->card['cardholder_authentication'] = $cardholder_auth;
		}
		return $this;
	}

	/**
	 * Creates an Instance with payment method METHOD_ACH
	 *  TODO
	 */
	static function Ach($routing_number,$account_number,$sec_code,$account_type,$check_number,$accountholder_authentication = null){
		$process = new ProcessTransaction(self::METHOD_ACH);
		$process->setAch($routing_number,$account_number,$sec_code,$account_type,$check_number,$accountholder_authentication);
		return $process;
	}

	/**
	 * sets the ach array
	 *  TODO
	 */
	public function setAch($routing_number,$account_number,$sec_code,$account_type,$check_number,$accountholder_authentication = null){
		$v = new TransactionAchValidation($routing_number,$account_number,$sec_code,$account_type,$check_number);
		$v->validateAch();
		$this->ach['routing_number'] = $routing_number;
		$this->ach['account_number'] = $account_number;
		$this->ach['sec_code'] = $sec_code;
		$this->ach['account_type'] = $account_type;
		$this->ach['check_number'] = $check_number;
		if($accountholder_authentication != null){
			$this->ach['accountholder_authentication'] = $accountholder_authentication;
		}else{
			$this->ach['accountholder_authentication'] = new stdClass();
		}
		return $this;
	}

	/**
	 * Constructor
	 * @param string $payment_method - payment_method
	 */
	static function CardholderAuth($condition,$eci,$cavv,$xid){
		return array(
			'condition' =>  $condition,
			'eci' =>  $eci,
			'cavv' =>  $cavv,
			'xid' =>  $xid
		);
	}

	/**
	 * Constructor
	 * @param string $payment_method - payment_method
	 */
	static function AccountholderAuth($dl_state,$dl_number){
		return array(
			'dl_state' =>  $dl_state,
			'dl_number' =>  $dl_number
		);
	}
	/**
	 *	Static method to build an address object from just pieces of data
	 *	@param string... fields to pass to address object
	 */
	public static function buildAddressObject($first_name = null ,$last_name = null ,$company = null ,$address_line_1 = null ,$address_line_2 = null ,$city = null ,$state = null ,$postal_code = null ,$country = null ,$phone = null ,$fax = null ,$email = null){
		return [
			self::ADDRESS['FIRST_NAME'] => $first_name,
			self::ADDRESS['LAST_NAME'] => $last_name,
			self::ADDRESS['COMPANY'] => $company,
			self::ADDRESS['ADDRESS_LINE_1'] => $address_line_1,
			self::ADDRESS['ADDRESS_LINE_2'] => $address_line_2,
			self::ADDRESS['CITY'] => $city,
			self::ADDRESS['STATE'] => $state,
			self::ADDRESS['POSTAL_CODE'] => $postal_code,
			self::ADDRESS['COUNTRY'] => $country,
			self::ADDRESS['PHONE'] => $phone,
			self::ADDRESS['FAX'] => $fax,
			self::ADDRESS['EMAIL'] => $email
		];
	}

	/**
	 * set transaction type
	 * @param string $type - the transaction type
	 */
	public function setType($type){
		if(in_array($type,self::TYPE_OPTIONS)){
			$this->type = $type;
		}else{
			throw new InvalidParameterException('Transaction Type must be either ("' . implode(self::TYPE_OPTIONS,'","') . '")');
		}
	}

	/**
	 * set tax exemption boolean
	 * @param boolean $isExempt
	 */
	public function setTaxExemption($isExempt = false){
		$this->tax_exempt = $isExempt;
	}

	/**
	 * set transaction description
	 * @param string $description
	 */
	public function setDescription($description){
		$this->description = $description;
	}

	/**
	 * set transaction IP Address
	 * @param string $ip
	 */
	public function setIP($ip){
		/* TODO:  IF ip in correct IPV4/IPV6 else null */
		$this->ip = $ip_address;
	}

	/**
	 * set Email address for transaction recipt and tell if it should send
	 * @param string $email
	 * @param boolean $doSend
	 */
	public function setEmail($email, $doSend = true){
		$this->email_address = $email;
		$this->email_receipt = $doSend;
	}

	/**
	 * set the billing address
	 * @param BillingObject $billingAddress - the billing Address
	 */
	public function setBillingAddress($billingObject){
		$this->billing_address = $billingObject;
	}

	/**
	 * set the shipping address
	 * @param ShippingObject $shippingAddress - the shipping Address
	 */
	public function setShippingAddress($shippingObject){
		$this->shipping_address = $shippingObject;
	}

	/**
	 * set the shipping address
	 * @param string $field - the address_field
	 * @param string $data - the data
	 */
	public function setBillingAddressField($field,$data){
		if(in_array($field,self::ADDRESS)){
			$this->billing_address[$field] = $data;
			return true;
		}
		return false;
	}

	/**
	 * set the shipping address
	 * @param string $field - the address_field
	 * @param string $data - the data
	 */
	public function setShippingAddressField($field,$data){
		if(in_array($field,self::ADDRESS)){
			$this->shipping_address[$field] = $data;
			return true;
		}
		return false;

	}

	/**
	 * set Tranaction Amounts
	 * @param int $amount - the amount in cents
	 * @param int $tax_amount - the tax_amount in cents
	 * @param int $shipping_amount - the shipping_amount in cents
	 * @param string $currency - amount currency
	 */
	public function setAmount($amount,$tax_amount = 0, $shipping_amount = 0, $currency = "USD"){
		$v = new TransactionAmountValidation($amount,$tax_amount,$shipping_amount);
		$v->validateAmounts();
		$this->amount = $amount;
		$this->tax_amount = $tax_amount;
		$this->shipping_amount = $shipping_amount;
		$this->currency = $currency;
	}

	/**
	* Overrides TransnationalAPI->getPOST method
	* Pulls the variables of this class into a json string for the POST request
	*/
	protected function getPOST(){
		$data = array();
		$method_data;
		switch($this->payment_method){
			case self::METHOD_CARD:
			$method_data = $this->card;
			break;
			case self::METHOD_ACH:
			$method_data = $this->ach;
			break;
			case self::METHOD_CUSTOMER:
			$method_data = $this->customer;
			break;
			case self::METHOD_TERMINAL:
			$method_data = $this->terminal;
			break;
			default:
			throw new InvalidPaymentException("No valid payment method provided");
		}

		$data['payment_method'] = array($this->payment_method => $method_data );
		$data['type'] = $this->type;
		$data['amount'] = $this->amount;
		$data['tax_exempt'] = $this->tax_exempt;
		if($this->tax_amount != null){
			$data['tax_amount'] = $this->tax_amount;
		}
		if($this->shipping_amount != null){
			$data['shipping_amount'] = $this->shipping_amount;
		}
		$data['currency'] = $this->currency;
		$data['ip_address'] = $this->ip_address;
		if($this->email_receipt){
			$data['email_receipt'] = $this->email_receipt;
			$data['email_address'] = $this->email_address;
		}
		if($this->billing_address){
			$data['billing_address'] = $this->billing_address;
		}
		if($this->shipping_address){
			$data['shipping_address'] = $this->shipping_address;
		}
		return json_encode($data);
	}

	/**
	* Overrides TransnationalAPI->getURL method
	* passes up the URL this API request will class
	*/
	protected function getURL(){
		if($this->dev){
			return self::DEV_URL . self::PATH;
		}else{
			return self::URL . self::PATH;
		}
	}
	/**
	* Wrapper method to the TransnationalAPI->callAPI method
	*/
	public function ProcessTransaction($auth){
		return new ProcessTransactionResult($this->callAPI($auth),$this->getHTTPCode());
	}

	protected function newInstace($json_data){
		return $this;
	}

}
