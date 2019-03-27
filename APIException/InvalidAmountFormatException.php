<?php
namespace Transnational\APIException;

class InvalidAmountFormatException extends TransnationalException{

	public function __construct($invalid_amount, $code = 0, Exception $previous = null) {
		$message = "Invalid Amount: \"$invalid_amount\" should be an int and recorded in cents. (Ex. $1.00 should be 100)";
        // make sure everything is assigned properly
		parent::__construct($message, $code, $previous);
	}
}
