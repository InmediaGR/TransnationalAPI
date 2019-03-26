<?php
require_once("transnational/TransnationalException.php");
class WrongCallException extends TransnationalException{
	
	// Redefine the exception so message isn't optional
	public function __construct($called, $expected, $code = 0, Exception $previous = null) {
        // some code
		$message = "Expecting type: " . $expected;
		$message .= " (Type called: " . get_class($called) . ")";
        // make sure everything is assigned properly
		parent::__construct($message, $code, $previous);
	}
}