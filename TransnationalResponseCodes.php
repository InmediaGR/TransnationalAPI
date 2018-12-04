<?php

namespace App\Transnational;

class TransnationalResponseCodes{


	public static function getResponse($code){
		if(array_key_exists($code, self::CODES)){
			return self::CODES[$code]['response'];
		}
		return false;
	}

	public static function getDefinition($code){
		if(array_key_exists($code, self::CODES)){
			return self::CODES[$code]['definition'];
		}
		return false;
	}

	const CODES = array(
		'00' => array(
			'code' => '0',
			'response' => "Approval",
			'definition' => "Approved and completed",
		),
		'01' => array(
			'code' => '1',
			'response' => "Call",
			'definition' => "Refer to issuer",
		),
		'02' => array(
			'code' => '2',
			'response' => "Call",
			'definition' => "Refer to issuer - special condition",
		),
		'03' => array(
			'code' => '3',
			'response' => "Term ID Error",
			'definition' => "Invalid Merchant ID",
		),
		'04' => array(
			'code' => '4',
			'response' => "Hold-call or Pick Up Card",
			'definition' => "Pick up card (no fraud)",
		),
		'05' => array(
			'code' => '5',
			'response' => "Decline",
			'definition' => "Do not honor",
		),
		'06' => array(
			'code' => '6',
			'response' => "Error XXXX",
			'definition' => "General error Invalid State Code or Province Code sent (Authorization and Capture) ",
		),
		'06*' => array(
			'code' => '06*',
			'response' => "Check Service Custom Text)",
			'definition' => "Error response text from check service",
		),
		'07' => array(
			'code' => '7',
			'response' => "Hold-call or Pick Up Card",
			'definition' => "Pick up card, special condition (fraud account)",
		),
		'08' => array(
			'code' => '8',
			'response' => "Approval",
			'definition' => "Honor MasterCard with ID",
		),
		'10' => array(
			'code' => '10',
			'response' => "Partial Approval",
			'definition' => "Partial approval for the authorized amount returned in Group III version 022",
		),
		'11' => array(
			'code' => '11',
			'response' => "Approval",
			'definition' => "VIP approval",
		),
		'12' => array(
			'code' => '12',
			'response' => "Invalid Trans",
			'definition' => "Invalid transaction",
		),
		'13' => array(
			'code' => '13',
			'response' => "Amount Error",
			'definition' => "Invalid amount",
		),
		'14' => array(
			'code' => '14',
			'response' => "Card No. Error",
			'definition' => "Invalid card number",
		),
		'15' => array(
			'code' => '15',
			'response' => "No Such Issuer",
			'definition' => "No such issuer",
		),
		'19' => array(
			'code' => '19',
			'response' => "RE Enter",
			'definition' => "Re-enter transaction",
		),
		'21' => array(
			'code' => '21',
			'response' => "No Action Taken",
			'definition' => "Unable to back out transaction",
		),
		'28' => array(
			'code' => '28',
			'response' => "No Reply",
			'definition' => "File is temporarily unavailable",
		),
		'30' => array(
			'code' => '30',
			'response' => "Format Error",
			'definition' => "Discover format error",
		),
		'34' => array(
			'code' => '34',
			'response' => "Transaction Canceled",
			'definition' => "MasterCard use only, Transaction Canceled; Fraud Concern (Used in reversal requests only)",
		),
		'39' => array(
			'code' => '39',
			'response' => "No Credit Acct",
			'definition' => "No credit account",
		),
		'41' => array(
			'code' => '41',
			'response' => "Hold-call or Pick Up Card",
			'definition' => "Lost card, pick up (fraud account)",
		),
		'43' => array(
			'code' => '43',
			'response' => "Hold-call or Pick Up Card",
			'definition' => "Stolen card, pick up (fraud account)",
		),
		'51' => array(
			'code' => '51',
			'response' => "Decline",
			'definition' => "Insufficient funds",
		),
		'52' => array(
			'code' => '52',
			'response' => "No Check Account",
			'definition' => "No checking account",
		),
		'53' => array(
			'code' => '53',
			'response' => "No Save Account",
			'definition' => "No savings account",
		),
		'54' => array(
			'code' => '54',
			'response' => "Expired Card",
			'definition' => "Expired card",
		),
		'55' => array(
			'code' => '55',
			'response' => "Wrong PIN",
			'definition' => "Incorrect PIN",
		),
		'57' => array(
			'code' => '57',
			'response' => "Serv not allowed",
			'definition' => "Transaction not permitted-Card",
		),
		'57' => array(
			'code' => '57',
			'response' => "Decline",
			'definition' => "Exceeds daily limit",
		),
		'58' => array(
			'code' => '58',
			'response' => "Serv not allowed",
			'definition' => "Transaction not permitted-Terminal",
		),
		'59' => array(
			'code' => '59',
			'response' => "Serv not allowed",
			'definition' => "Transaction not permitted-Merchant",
		),
		'61' => array(
			'code' => '61',
			'response' => "Declined",
			'definition' => "Exceeds withdrawal limit",
		),
		'62' => array(
			'code' => '62',
			'response' => "Declined",
			'definition' => "Invalid service code, restricted",
		),
		'63' => array(
			'code' => '63',
			'response' => "Sec Violation",
			'definition' => "Security violation",
		),
		'65' => array(
			'code' => '65',
			'response' => "Declined",
			'definition' => "Activity limit exceeded",
		),
		'75' => array(
			'code' => '75',
			'response' => "PIN Exceeded",
			'definition' => "PIN tried exceeded",
		),
		'76' => array(
			'code' => '76',
			'response' => "Unsolicated Reversal",
			'definition' => "Unable to locate, no match",
		),
		'77' => array(
			'code' => '77',
			'response' => "No Action Taken",
			'definition' => "Inconsistent data, reversed, or repeat",
		),
		'78' => array(
			'code' => '78',
			'response' => "No Account",
			'definition' => "No account",
		),
		'79' => array(
			'code' => '79',
			'response' => "Already Reversed",
			'definition' => "Already reversed at switch",
		),
		'80' => array(
			'code' => '80',
			'response' => "Date Error",
			'definition' => "Invalid date",
		),
		'80' => array(
			'code' => '80',
			'response' => "No Impact",
			'definition' => "No Financial impact (used in reversal responses to declined originals)",
		),
		'81' => array(
			'code' => '81',
			'response' => "Encryption Error",
			'definition' => "Cryptographic error",
		),
		'82' => array(
			'code' => '82',
			'response' => "Incorrect CVV",
			'definition' => "CVV data is not correct",
		),
		'83' => array(
			'code' => '83',
			'response' => "Cannot Verify PIN",
			'definition' => "Cannot verify PIN",
		),
		'85' => array(
			'code' => '85',
			'response' => "Card OK",
			'definition' => "No reason to decline",
		),
		'86' => array(
			'code' => '86',
			'response' => "Cannot Verify PIN",
			'definition' => "Cannot verify PIN",
		),
		'91' => array(
			'code' => '91',
			'response' => "No Reply",
			'definition' => "Issuer or switch is unavailable",
		),
		'92' => array(
			'code' => '92',
			'response' => "Invalid Routing",
			'definition' => "Destination not found",
		),
		'93' => array(
			'code' => '93',
			'response' => "Decline",
			'definition' => "Violation, cannot complete",
		),
		'94' => array(
			'code' => '94',
			'response' => "Duplicate Trans",
			'definition' => "Unable to locate, no match",
		),
		'96' => array(
			'code' => '96',
			'response' => "System Error",
			'definition' => "System malfunction",
		),
		'A1' => array(
			'code' => 'A1',
			'response' => "Activated",
			'definition' => "POS device authentication successful",
		),
		'A2' => array(
			'code' => 'A2',
			'response' => "Not Activated",
			'definition' => "POS device authentication not successful",
		),
		'A3' => array(
			'code' => 'A3',
			'response' => "Deactivated",
			'definition' => "POS device deactivation successful",
		),
		'B1' => array(
			'code' => 'B1',
			'response' => "SRCHG Not Allowed",
			'definition' => "Surcharge amount not permitted on debit cards or EBT food stamps",
		),
		'B2' => array(
			'code' => 'B2',
			'response' => "SRCHG Not Allowed",
			'definition' => "Surcharge amount not supported by debit network issuer",
		),
		'CV' => array(
			'code' => 'CV',
			'response' => "Failure CV",
			'definition' => "Card Type Verification Error",
		),
		'D3' => array(
			'code' => 'D3',
			'response' => "Domain Restriction Controls Failure",
			'definition' => "Mobile transactions must include a data indicator for Discover tokenized MITs or the transaction will be declined.",
		),
		'E1' => array(
			'code' => 'E1',
			'response' => "ENCR NOT CONFIGD",
			'definition' => "Encryption is not configured",
		),
		'E2' => array(
			'code' => 'E2',
			'response' => "TERM NOT AUTHENT",
			'definition' => "Terminal is not authenticated",
		),
		'E3' => array(
			'code' => 'E3',
			'response' => "DECRYPT FAILURE",
			'definition' => "Data could not be decrypted",
		),
		'EA' => array(
			'code' => 'EA',
			'response' => "Acct Length Err",
			'definition' => "Verification error",
		),
		'EB' => array(
			'code' => 'EB',
			'response' => "Check Digit Err",
			'definition' => "Verification error",
		),
		'EC' => array(
			'code' => 'EC',
			'response' => "CID Format Error",
			'definition' => "Verification error",
		),
		'H1' => array(
			'code' => 'H1',
			'response' => "Invalid BIN/KSI combination",
			'definition' => "Invalid BIN/KSI combination",
		),
		'H2' => array(
			'code' => 'H2',
			'response' => "Missing PIN block",
			'definition' => "PIN information is missing",
		),
		'H3' => array(
			'code' => 'H3',
			'response' => "Invalid KSN or unable to determine KSI",
			'definition' => "Invalid KSN or unable to determine KSI",
		),
		'H4' => array(
			'code' => 'H4',
			'response' => "HSM unavailable",
			'definition' => "Timeout",
		),
		'H5' => array(
			'code' => 'H5',
			'response' => "HSM unavailable",
			'definition' => "TCP\IP error",
		),
		'H6' => array(
			'code' => 'H6',
			'response' => "Encryption Error",
			'definition' => "PIN pad encryption information for debit transaction received an error.",
		),
		'H7' => array(
			'code' => 'H7',
			'response' => "BIN/ZKI not found",
			'definition' => "BIN/ZKI not found",
		),
		'H8' => array(
			'code' => 'H8',
			'response' => "HSM Sanity Check",
			'definition' => "HSM Sanity Check",
		),
		'H9' => array(
			'code' => 'H9',
			'response' => "Unable to decrypt/re-encrypt PIN",
			'definition' => "Unable to decrypt/re-encrypt PIN",
		),
		'HV' => array(
			'code' => 'HV',
			'response' => "Failure HV",
			'definition' => "Hierarchy Verification Error",
		),
		'K0' => array(
			'code' => 'K0',
			'response' => "TOKEN RESPONSE",
			'definition' => "Token request was processed",
		),
		'K1' => array(
			'code' => 'K1',
			'response' => "TOKEN NOT CONFIGD",
			'definition' => "Tokenization is not configured",
		),
		'K2' => array(
			'code' => 'K2',
			'response' => "TERM NOT AUTHENT",
			'definition' => "Terminal is not authenticated",
		),
		'K3' => array(
			'code' => 'K3',
			'response' => "TOKEN FAILURE",
			'definition' => "Data could not be de-tokenized",
		),
		'N3' => array(
			'code' => 'N3',
			'response' => "Cashback Not Avl",
			'definition' => "Cash back service not available",
		),
		'N4' => array(
			'code' => 'N4',
			'response' => "Decline",
			'definition' => "Exceeds issuer withdrawal limit",
		),
		'N7' => array(
			'code' => 'N7',
			'response' => "CCV2 Mismatch",
			'definition' => "CVV2 Value supplied is invalid",
		),
		'R0' => array(
			'code' => 'R0',
			'response' => "Stop recurring",
			'definition' => "Customer requested stop of specific recurring payment",
		),
		'R1' => array(
			'code' => 'R1',
			'response' => "Stop recurring",
			'definition' => "Customer requested stop of all recurring payments from specific merchant",
		),
		'RT' => array(
			'code' => 'RT',
			'response' => "SEND TXN AGAIN",
			'definition' => "Send the original transaction again. First transaction was not received and could not be reversed.",
		),
		'T0' => array(
			'code' => 'T0',
			'response' => "Approval",
			'definition' => "First check is OK and has been converted",
		),
		'T1' => array(
			'code' => 'T1',
			'response' => "Cannot Convert",
			'definition' => "Check is OK but cannot be converted Note: This is a declined transaction",
		),
		'T2' => array(
			'code' => 'T2',
			'response' => "Invalid ABA",
			'definition' => "Invalid ABA number, not an ACH participant",
		),
		'T3' => array(
			'code' => 'T3',
			'response' => "Amount Error",
			'definition' => "Amount greater than the limit",
		),
		'T4' => array(
			'code' => 'T4',
			'response' => "Unpaid Items",
			'definition' => "Unpaid items, failed negative file check",
		),
		'T5' => array(
			'code' => 'T5',
			'response' => "Duplicate Number",
			'definition' => "Duplicate check number",
		),
		'T6' => array(
			'code' => 'T6',
			'response' => "MICR Error",
			'definition' => "MICR error",
		),
		'T7' => array(
			'code' => 'T7',
			'response' => "Too Many Checks",
			'definition' => "Too many checks (over merchant or bank limit)",
		),
		'V1' => array(
			'code' => 'V1',
			'response' => "Failure VM",
			'definition' => "Daily threshold"
		)
	);


}
