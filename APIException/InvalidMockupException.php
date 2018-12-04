<?php
namespace App\Transnational\APIException;

use App\Transnational\TransnationalAPI;

class InvalidMockupException extends TransnationalException{

	// Redefine the exception so message isn't optional
	public function __construct($level, $dev, $code = 0, Exception $previous = null) {
        // some code
		$message = "You must use MOCKUP_LEVEL(" . TransnationalAPI::MOCKUP_LEVEL . ") with mockups: Current level: " . $level;
		if(!$dev){
			$message .= " You must be in dev mode to trigger a mockup.";
		}
        // make sure everything is assigned properly
		parent::__construct($message, $code, $previous);
	}
}
