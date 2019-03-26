<?php 
require_once("transnational/TransnationalException.php");
class ErrorResultException extends TransnationalException{

	public $http_code;

	public $json_data;
	// Redefine the exception so message isn't optional
	public function __construct($json_data, $http_code, $code = 0, Exception $previous = null) {
		$this->json_data = $json_data;
		$this->http_code = $http_code;
		$message = "API call returned with unexpected results. ";
		$message .= "[Http code]: (" . $http_code  . ") ";
		$message .= "[json data]: (" . json_encode($json_data)  . ") ";

        // make sure everything is assigned properly
		parent::__construct($message, $code, $previous);
	}

	/**
     * @return mixed
     */
	public function getHttpCode()
	{
		return $this->http_code;
	}

	/**
     * @return mixed
     */
	public function getJsonData()
	{
		return $this->json_data;
	}
}
