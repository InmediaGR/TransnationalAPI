<?php
namespace Transnational\APIException;

class InvalidParameterException extends TransnationalException{
	public function __construct($invalid,$acceptable, $code = 0, Exception $previous = null) {
		$message = $invalid . ' must be ("' . $acceptable . '")';
        // make sure everything is assigned properly
		parent::__construct($message, $code, $previous);
	}




}
