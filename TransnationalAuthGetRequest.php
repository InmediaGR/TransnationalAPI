<?php

namespace Transnational;

/**
*	This class sets the getHeaders abstract method from Transnational API for use
*	with any API class that need to be both a POST and Authenticated Request
*/
abstract class TransnationalAuthGetRequest extends TransnationalAPI
{
	/**
	 * Overrides TransnationalAPI->getHeaders method
	 * gets the Headers to set for the API - Standard POST with AUTH headers
	 * @param string $AUTH_CODE - Authorization header API code
	 */
	protected function getHeaders($AUTH_CODE){
		$headers[] = "Authorization: " . $AUTH_CODE;
		return $headers;
	}

	/**
	 * Overrides TransnationalAPI->getHeaders method
	 * gets the Headers to set for the API - Standard POST with AUTH headers
	 * @param string $AUTH_CODE - Authorization header API code
	 */
	protected function getMethod(){
		return "GET";
	}

	protected function curlOptions($curl,$auth)
	{
	}
}
