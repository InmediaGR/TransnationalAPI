<?php
namespace Transnational\APIException;

class InvalidRecurringBillingFormatException extends TransnationalException{

	public function __construct($case, $code = 0, Exception $previous = null) {
		$message = "Invalid Recurring Billing Format";
		switch($case){
			case "interval":
			$message .= ": Interval needs to be a number";
			break;
			case "frequency":
			$message .= ": Invalid frequency";
			break;
			case 'duration':
			$message .= ": Billing duration must be a number";
			break;
			case "days":
			$message .= ": Invalid billing_days. Billing day(s) must be int";
			break;
			case 'mismatch':
			$message .= ": Frequency/Days Mismatch. Only one day can be set unless using a Twice Monthly frequency";
			break;
			case 'mismatch2':
			$message .= ": Frequency/Days Mismatch. Two days must be set for a Twice Monthly frequency";
			break;
			case '31':
			$message .= ": Billing Days must be between 1 and 31";
			break;
			case 'different':
			$message .=	": Billing Days must be different";
			break;

		}
        // make sure everything is assigned properly
		parent::__construct($message, $code, $previous);
	}
}
