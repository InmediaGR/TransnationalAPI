<?php

class TransnationalEndpointResponseCodes{


	public static function getResponse($code){
		if(array_key_exists($code, self::CODES)){
			return self::CODES[$code]['response'];
		}
		return false;
	}

	public static function getMeaning($code){
		if(array_key_exists($code, self::CODES)){
			return self::CODES[$code]['meaning'];
		}
		return false;
	}
	public static function getAction($code){
		$code = self::CODES[$code];
		if($code){
			return $code['action'];
		}
		return false;
	}

	const CODES = array(
		100 => array(
			'code' => 100,
			'response' => "Approved",
			'meaning' => "Approved",
			"action" => "N/A"
		),
		110 => array(
			'code' => 110,
			'response' => "PartialApproval",
			'meaning' => "Partial Approval",
			"action" => "N/A"

		),
		200 => array(
			'code' => 200,
			'response' => "Decline",
			'meaning' => "Decline",
			"action" => "Cust"

		),
		201 => array(
			'code' => 201,
			'response' => "Decline",
			'meaning' => "Do not honor",
			"action" => "Cust"

		),
		202 => array(
			'code' => 202,
			'response' => "Decline",
			'meaning' => "Insufficient Funds",
			"action" => "Cust"

		),
		203 => array(
			'code' => 203,
			'response' => "Decline",
			'meaning' => "Exceeds withdrawl limit",
			"action" => "Cust"

		),
		204 => array(
			'code' => 204,
			'response' => "Decline",
			'meaning' => "Invalid Transaction",
			"action" => "Cust"

		),
		220 => array(
			'code' => 220,
			'response' => "Decline",
			'meaning' => "Invalid Amount",
			"action" => "Fix"

		),
		221 => array(
			'code' => 221,
			'response' => "Decline",
			'meaning' => "No Such Issuer",
			"action" => "N/A"

		),
		222 => array(
			'code' => 222,
			'response' => "Decline",
			'meaning' => "No Credit Account",
			"action" => "N/A"

		),
		223 => array(
			'code' => 223,
			'response' => "Decline",
			'meaning' => "Expired Card",
			"action" => "Cust"

		),
		225 => array(
			'code' => 225,
			'response' => "Decline",
			'meaning' => "Invalid CVC",
			"action" => "Cust"

		),
		226 => array(
			'code' => 226,
			'response' => "Decline",
			'meaning' => "Cannot Verify Pin",
			"action" => "Cust"

		),
		240 => array(
			'code' => 240,
			'response' => "Decline",
			'meaning' => "Refer to issuer",
			"action" => "N/A"

		),
		250 => array(
			'code' => 250,
			'response' => "Decline",
			'meaning' => "Pick up card (no fraud)",
			"action" => "Cust"

		),
		251 => array(
			'code' => 251,
			'response' => "Decline",
			'meaning' => "Lost card, pick up (fraud account)",
			"action" => "Cust"

		),
		252 => array(
			'code' => 252,
			'response' => "Decline",
			'meaning' => "Stolen card, pick up (fraud account)",
			"action" => "Cust"

		),
		253 => array(
			'code' => 253,
			'response' => "Decline",
			'meaning' => "Pick up card, special condition",
			"action" => "Cust"

		),
		261 => array(
			'code' => 261,
			'response' => "Decline",
			'meaning' => "Stop recurring",
			"action" => "N/A",

		),
		262 => array(
			'code' => 262,
			'response' => "Decline",
			'meaning' => "Stop recurring",
			"action" => "N/A"

		),
		300 => array(
			'code' => 300,
			'response' => "Error",
			'meaning' => "Gateway error",
			"action" => "N/A"

		),
		301 => array(
			'code' => 301,
			'response' => "Error",
			'meaning' => "Duplicate transaction",
			"action" => "Cust"

		),
		400 => array(
			'code' => 400,
			'response' => "Processor Error",
			'meaning' => "Transaction error returned by processor",
			"action" => "N/A"

		),
		410 => array(
			'code' => 410,
			'response' => "Processor Error",
			'meaning' => "Invalid merchant configuration",
			"action" => "N/A"

		),
		421 => array(
			'code' => 421,
			'response' => "Processor Error",
			'meaning' => "Communication error with processor",
			"action" => "N/A"

		),
		430 => array(
			'code' => 430,
			'response' => "Processor Error",
			'meaning' => "Duplicate transaction at processor",
			"action" => "N/A"

		),
		440 => array(
			'code' => 440,
			'response' => "Processor Error",
			'meaning' => "Processor format error",
			"action" => "N/A"

		),
		0 => array(
			'code' => 0,
			'response' => "Unkown",
			'meaning' => "Unknown",
			"action" => "N/A"

		)
	);
}

