<?php
/**
*	This class sets the getHeaders abstract method from Transnational API for use
*	with any API class that need to be both a POST and Authenticated Request
*/
require_once "TransnationalAPI.php";
abstract class TransnationalAuthPostRequest extends TransnationalAPI
{
	/**
	 * Overrides TransnationalAPI->getHeaders method
	 * gets the Headers to set for the API - Standard POST with AUTH headers
	 * @param string $AUTH_CODE - Authorization header API code
	 */
	protected function getHeaders($AUTH_CODE){
		$headers[] = "Content-type: application/json";
		$headers[] = "Authorization: " . $AUTH_CODE;
		return $headers;
	}

	/**
	 * Overrides TransnationalAPI->getHeaders method
	 * gets the Headers to set for the API - Standard POST with AUTH headers
	 * @param string $AUTH_CODE - Authorization header API code
	 */
	protected function getMethod(){
		return "POST";
	}

	protected function curlOptions($curl,$auth)
	{
		if(self::DEBUG == 3 || self::DEBUG == 1){
			//ouput headers
			curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		}
		// set json POST data
		curl_setopt($curl, CURLOPT_POSTFIELDS, $this->getPOST());
		if(self::DEBUG == 2 || self::DEBUG == 1){
			//ouput headers
			$this->var_dump_pre("POST REQUEST");
			$this->var_dump_pre($this->getPOST());
		}   
	}
}