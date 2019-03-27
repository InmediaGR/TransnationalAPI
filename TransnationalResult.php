<?php

namespace Transnational;

use \Transnational\APIException\ErrorResultException;

class TransnationalResult{

	const SUCCESS = "success";
	const SUCCESS_CODE = 100;
	const HTTP_OK = 200;
	const HTTP_BAD_REQUEST = 400;
	const HTTP_NOT_FOUND = 404;
	const HTTP_UNAUTHORIZED = 401;

	const DEFAULT_FAIL_MESSAGE = "There was a problem please call us";

	/**
	* Highest level return values
	*/
	protected $response_code = 0;

	protected $request_status = null;

	protected $message = null;

	protected $data = null;

	private static $http_code_whitelist = array(self::HTTP_OK, self::HTTP_BAD_REQUEST, self::HTTP_NOT_FOUND);

	public static function isWhiteList($code){
		return in_array($code,self::$http_code_whitelist);
	}

	public function __construct($json_result,$http_code,$throwErrorOnFail = true)
	{
		$this->response_code = $http_code;
		if($json_result && self::isWhiteList($http_code)){
			$result = json_decode($json_result);
			$this->request_status = $result->status;
			$this->message = $result->msg;
			if(isset($result->data)){
				$this->data = $result->data;
			}
		}else if($throwErrorOnFail){
			throw new ErrorResultException($json_result,$http_code);
		}
	}

	public function isSuccess(){
		if($this->response_code == self::HTTP_OK && $this->get_request_status() == self::SUCCESS){
			return true;
		}
		return false;
	}

	public function get_http_response_code(){
		return $this->response_code;
	}

	public function get_request_status(){
		return $this->request_status;
	}

	public function get_message(){
		return $this->message;
	}

	public function get_data(){
		return $this->data;
	}

	public static function FAIL_RESULT($failMessage = false){
		$th = new static(null,0,false);
		$th->request_status = "failure";
		$th->message = $failMessage ? $failMessage : "There was a problem";
		return $th;
	}

}
