<?php
require_once "TransnationalAuthPostRequest.php";
class QueryTransaction extends TransnationalAuthPostRequest
{

	/**
	*	Transaction search path
	*/
	const PATH = "transaction/search";

	const TRANSACTION_ID = "transaction_id";
	const USER_ID = "user_id";
	const TYPE = "type";
	const IP_ADDRESS = "ip_address";
	const AMOUNT = "amount";
	const AMOUNT_AUTHORIZED = "amount_authorized";
	const AMOUNT_CAPTURED = "amount_captured";
	const AMOUNT_SETTLED = "amount_settled";
	const TAX_AMOUNT = "tax_amount";
	const PO_NUMBER = "po_number";
	const ORDER_ID = "order_id";
	const PAYMENT_METHOD = "payment_method";
	const PAYMENT_TYPE = "payment_type";
	const STATUS = "status";
	const PROCESSOR_ID = "processor_id";
	const CUSTOMER_ID = "customer_id";
	const CREATED_AT = "created_at";
	const CAPTURED_AT = "captured_at";
	const SETTLED_AT = "settled_at";
	const ADDRESS = array(
		"BILLING_ADDRESS" => 'billing_address',
		"SHIPPING_ADDRESS" => 'shipping_address'
	);
	const FEILD = array(
		'ADDRESS_ID' => 'address_id',
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

	protected $offset = null;
	protected $limit = null;
	protected $query = array();


	public function setOffset($offset){
		$this->offset = $offset;
	}

	public function setLimit($limit){
		$this->limit = $limit;
	}

	public function addSearch($condition,$operator,$value){
		$this->query[$condition] =  
		array(
			'operator' => $operator,
			'value' => $value
		);
	}

	public function addAddressSearch($address,$condition,$operator,$value){
		if(!isset($this->query[$address])){
			$this->query[$address] = array($condition => array());
		}
		$this->query[$address][$condition] =
		array(
			'operator' =>  $operator,
			'value' => $value 
		);
		return $this->query;
	}

	protected function getPOST(){
		$data = $this->query;
		if($this->offset != null){
			$data['offset'] = $this->offset;
		}
		if($this->limit != null){
			$data['limit'] = $this->limit;
		}
		if(!$data){
			return "{}";
		}
		return json_encode($data);
	}

	protected function getURL(){
		return self::URL . self::PATH;
	}

}
