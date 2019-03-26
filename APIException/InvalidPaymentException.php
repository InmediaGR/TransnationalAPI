<?php
namespace App\Transnational\APIException;

class InvalidPaymentException extends TransnationalException{

	public function __construct($code = 0, Exception $previous = null) {
		$message = "No valid payment method provided";
        // make sure everything is assigned properly
		parent::__construct($message, $code, $previous);
	}
}
