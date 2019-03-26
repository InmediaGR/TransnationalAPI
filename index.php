<?php
include_once("path.inc.php");
include_once("session.php");
	// database/email config
include_once("config.inc.php");
	// FirstData config
include_once("tnpconfig.inc.php");
// include_once("fdconfig.inc.php");
	// mysqli class
include_once("class.mysqli.php");
	// functions
include_once("functions.inc.php");
	// firstdata api wrapper class
	// require_once("fd/v13/FirstDataApi/FirstData.php");
require_once("transnational/CallTransnationalAPI.php");

require_once("transnational/ProcessTransaction.php");
require_once("transnational/TransnationalException.php");
	// mandrill	send api
require_once("mandrill-api-php/src/Mandrill.php");

$response_messages = [];

$start_time = new DateTime("now");

	// email log report on exception
$exception = false;
$exceptions = array();

	// logfile timestamp and name
$start_time = new DateTime("now");
$log_fn = '/log' . date("Ymd_His") . '.txt';
$logfilename = DIR_LOGS . $log_fn;

$jsonarray = array();

	// connect to database
$db = dbconn::instance($db_settings);

	// Error?
if (!$db) {
	$exceptions[] = 'ERROR: ' . $db->error($db);
	$exceptions[] = 'File:   ' . __FILE__;
	$exceptions[] = 'Line #: ' . __LINE__;
	$exception = true;
} else {
		// set collation for result set
	$collation = $db->query("SET NAMES 'utf8'");

		// Error?
	if (!$collation) {
		$exceptions[] = 'ERROR: ' . $db->error($db);
		$exceptions[] = 'File:   ' . __FILE__;
		$exceptions[] = 'Line #: ' . __LINE__;
		$exception = true;
	} else {
			// set default variable values
		$is_guest = $_SESSION['guest'];
		$is_admin = $_SESSION['admin'];
		$approved = true;
		$bill_approved = false;
		$fee_approved = false;
		$testMode = true;
		$amounts_types = array();
		$json_array = array();
		$receipt_text = '';
		$receipt_text_accounts = '';
		$customer_ip = $_SERVER['REMOTE_ADDR'];
		$send_customer_receipt = true;
		$send_admin_receipt = false;

			// source of transaction
			// P = Phone
			// R = Rapid-Pay
			// O = Office (admin)
			// U = Registered User
		if ($is_guest == 1) {
			$source = 'R';
			$send_customer_receipt = false;
		} elseif ($is_admin == 1) {
			$source = 'O';
			$send_customer_receipt = false;
		} else {
			$source = 'U';
		}

			// form values
		$company = $db->sanitize(trim($_POST['company']));
		$company_id = $db->sanitize(trim($_POST['company_id']));
			// customer_id contains customer ids separated by comma
		$customer_id = $db->sanitize(trim($_POST['customer_id']));
			// create array of customer ids
		$customer_ids = explode(",", $customer_id);
			// customer_numbers contains customer numbers separated by comma
		$customer_number = $db->sanitize(trim($_POST['customer_number']));
			// create array of customer numbers
		$customer_numbers = explode(",", $customer_number);
			// customer_amount contains customer amounts separated by comma
		$customer_amount = $db->sanitize(($_POST['customer_amount']));
			// create array of customer numbers
		$customer_amounts = explode(",", $customer_amount);
			// customer_accounts contains customer numbers/service address separated by <br>
			// use remove_slashes to remove all slashes since stripslashes isn't recursive
		$customer_account = remove_slashes($db->sanitize(trim($_POST['customer_accounts'])));
			// create array of customer accounts
		$customer_accounts = explode("<br>", $customer_account);
		$cardholder_name_first = addslashes(remove_slashes($db->sanitize(ucwords(trim($_POST['cardholder_name_first'])))));
		$cardholder_name_last = addslashes(remove_slashes($db->sanitize(ucwords(trim($_POST['cardholder_name_last'])))));

		// $cardholder_name = addslashes(remove_slashes($db->sanitize(ucwords(trim($_POST['cardholder_name'])))));
			// set to camelcase

			// card transactions
		$creditcard_number = $db->sanitize(trim($_POST['creditcard_number']));
		$creditcard_expmonth = $db->sanitize(trim($_POST['creditcard_expmonth']));
		$creditcard_expyear = substr($db->sanitize(trim($_POST['creditcard_expyear'])), -2);
		$creditcard_ccv = $db->sanitize(trim($_POST['creditcard_ccv']));

			// check transactions
		$cardholder_address = remove_slashes($db->sanitize(trim($_POST['cardholder_address'])));
		$cardholder_city = remove_slashes($db->sanitize(trim($_POST['cardholder_city'])));
		$cardholder_state = $db->sanitize(trim($_POST['cardholder_state']));
		$check_state = $db->sanitize(trim($_POST['check_state']));
		$cardholder_zip = $db->sanitize(trim($_POST['cardholder_zip']));

		$receipt_address = "{$cardholder_address}, {$cardholder_city}, {$check_state}  {$cardholder_zip}";
		$address = "{$cardholder_address}|{$cardholder_zip}|{$cardholder_city}|{$check_state}|US";

		$payment_method = $db->sanitize(trim($_POST['payment_method']));

			// check transactions
		$check_type = strtoupper($db->sanitize($_POST['check_type']));
		$bank_id = $db->sanitize($_POST['bank_id']);
		$account_number = $db->sanitize($_POST['account_number']);
		$check_number = $db->sanitize($_POST['check_number']);
		$customer_id_type = $db->sanitize($_POST['customer_id_type']);

		/* temp fix for dl license with state */
		if ($customer_id_type == '0') {
			$customer_id_number = $check_state . $db->sanitize($_POST['customer_id_number']);
		} else {
			$customer_id_number = $db->sanitize($_POST['customer_id_number']);
		}

		$client_email = $db->sanitize($_POST['client_email']);

		$billamt = $db->sanitize(trim($_POST['camount']));
		$feeamt = $db->sanitize(trim($_POST['cfee']));

			// customer_amounts holds the total balance due
			// when paying multiple accounts.
			// if only 1 element, use billamt in case
			// payment amount is not total amount due
		if (count($customer_amounts) == 1) {
			$customer_amounts[0] = $billamt;
		}

			// get borough admin email address
			$admin_email = null; // default value
			$receivePaymentEmails = 0;
			$query = "
			SELECT DISTINCT
			cp.`email` AS EmailAddress
			,	IFNULL(cs.`receivePaymentEmails`, 0) AS receivePaymentEmails
			FROM
			`customer_profile` cp
			LEFT JOIN `company_settings` cs ON (cs.`CompanyId` = cp.`company_id`)
			WHERE
			cp.`company_id` = $company_id
			AND
			cp.`is_admin` = 1
			LIMIT 1
			";

			// Results of SQL statement
			$resultSQL = $db->query($query);

			if ($resultSQL) {
				$num_rows = $db->affected($db);

				if ($num_rows > 0) {
					$objects = $db->objects($resultSQL);
					$receivePaymentEmails = $objects->receivePaymentEmails;
					$admin_email = $objects->EmailAddress;

					if ($receivePaymentEmails == 1) {
						$send_admin_receipt = true;
					} else {
						$send_admin_receipt = false;
					}
				}
			}

			// check company for DemoMode and get borough information
			$query = "
			SELECT DISTINCT
			`DemoMode`
			,	`DisplayLine1` AS CompanyName
			,	`DisplayLine2`
			,	`DisplayLine3`
			,	`DisplayLine4`
			,	IFNULL(`LogoName`, 'nologo.png') AS LogoName
			,	`CompanyPhoneno`
			FROM
			`company`
			WHERE
			`Id` = $company_id
			";

			// Results of SQL statement
			$resultSQL = $db->query($query);

			// Error?
			if (!$resultSQL) {
				$exceptions[] = 'ERROR: ' . $db->error($db) . ' [' . $query . ']';
				$exceptions[] = 'File:   ' . __FILE__;
				$exceptions[] = 'Line #: ' . __LINE__;
				$exception = true;
			} else {
				$objects = $db->objects($resultSQL);

				$DemoMode = $objects->DemoMode;
				$CompanyName = $objects->CompanyName;
				$DisplayLine2 = $objects->DisplayLine2;
				$DisplayLine3 = $objects->DisplayLine3;
				$DisplayLine4 = $objects->DisplayLine4;
				$LogoName = $objects->LogoName;
				$CompanyPhoneno = $objects->CompanyPhoneno;

				$CompanyInfo = (!is_null($DisplayLine2) || !empty($DisplayLine2) || strlen($DisplayLine2) > 0) ? $DisplayLine2 : '';
				$CompanyInfo .= (!is_null($DisplayLine3) || !empty($DisplayLine3) || strlen($DisplayLine3) > 0) ? '<br>' . $DisplayLine3 : '';
				$CompanyInfo .= (!is_null($DisplayLine4) || !empty($DisplayLine4) || strlen($DisplayLine4) > 0) ? '<br>' . $DisplayLine4 : '';
				$CompanyInfo .= (!is_null($CompanyPhoneno) || !empty($CompanyPhoneno) || strlen($CompanyPhoneno) > 0) ? '<br>' . $CompanyPhoneno : '';

				// set gateway/password for demo or live api
				if ($DemoMode == 'Y') {
					$testMode = true;

					/*** TRANSNATIONAL CODE ********/
					$dtc_api_key = $company_settings['diversified']['demo_api_key'];
					// $dtc_url_path = $company_settings['diversified']['demo_url'];
					$cust_api_key = $company_settings['diversified']['demo_api_key'];
					// $cust_url_path = $company_settings['diversified']['demo_url'];
				} else {
					$testMode = false;

					$dtc_api_key = $company_settings['diversified']['prod_api_key'];
					// $dtc_url_path = $company_settings['diversified']['prod_url'];
					$cust_api_key = $company_settings['company'][$company_id]['api_key'];
					// $cust_url_path = $company_settings['company'][$company_id]['demo_url'];
				}


				$cust_dba_name = $company_settings['company'][$company_id]['dba_name'];
				$dtc_dba_name = $company_settings['diversified']['dba_name'];

				// process payment amount first, then fee
				// if bill is declined do not process fee
				// $amounts_types[] = array($billamt, 'bill');
				// $amounts_types[] = array($feeamt, 'fee');
				// $amounts = $billamt + $feeamt;
				// set customer trans_key
				$guid = set_guid();

				if ($approved) {
						// $amount = (!@number_format($amounts[0], 2, '.', '')) ? 'NULL' : number_format($amounts[0], 2, '.', '');

					$amount_type = 'bill';

						// only send transactions with amount > 0 to First Data
						// amount may = 0 where no fee is collected

					/*** TRANSNATIONAL ADDITIONAL CODE   *********/
					$billamt = $_POST['camount'];
					$feeamt = $_POST['cfee'];
					$db_amount = ($billamt + $feeamt);
					$amount = $db_amount * 100;
					$amount = (int) round($amount);
					/*** </TRANSNATIONAL ADDITIONAL CODE   *********/

					if ($amount > 0) {

							/********* FIRSTDATA COMMENT OUT CODE  **********

							// set gateway/password based on fee or customer payment
							// $gateway_id = ($amount_type == 'fee' ? $dtc_gateway_id : $cust_gateway_id);
							// $password = ($amount_type == 'fee' ? $dtc_password : $cust_password);
							// $hmac_key = ($amount_type == 'fee' ? $dtc_hmac_key : $cust_hmac_key);
							// $key_id = ($amount_type == 'fee' ? $dtc_key_id : $cust_key_id);
							// $dba_name = ($amount_type == 'fee' ? $dtc_dba_name : $cust_dba_name);
							// $fee_transaction = ($amount_type == 'fee' ? "'Y'" : 'NULL');

							*******************   FIRSTDATA COMMENTED OUT CODE   **********/
							$dba_name = $cust_dba_name;
							$fee_transaction = "NULL";


							// set FeeTransaction base on transaction type

							// if ($payment_method == 'card') {
							// 	$nvp = array(
							// 		"gateway_id"      => $gateway_id,
							// 		"password"        => $password,
							// 		"hmac_key"        => $hmac_key,
							// 		"key_id"          => $key_id,
							// 		"cardholder_name" => $cardholder_name,
							// 		"amount"          => $amount,
							// 		"cc_number"       => $creditcard_number,
							// 		"cc_expiry"       => $creditcard_expmonth . substr($creditcard_expyear, -2),
							// 		"cvv"             => $creditcard_ccv
							// 	);
							// } elseif ($payment_method == 'check') {
							// 	$nvp = array(
							// 		"gateway_id"         => $gateway_id,
							// 		"password"           => $password,
							// 		"hmac_key"           => $hmac_key,
							// 		"key_id"             => $key_id,
							// 		"cardholder_name"    => $cardholder_name,
							// 		"amount"             => $amount,
							// 		"check_type"         => $check_type,
							// 		"bank_id"            => $bank_id,
							// 		"account_number"     => $account_number,
							// 		"customer_id_type"   => $customer_id_type,
							// 		"customer_id_number" => $customer_id_number,
							// 		"check_number"       => $check_number,
							// 		"client_email"       => $client_email,
							// 		"ecommerce_flag"     => 7
							// 		// eci indicator for telecheck
							// 	);
							// }

							/*** TRANSNATIONAL ADDITIONAL CODE   *********/

							// $Transnational->ProcessTransaction();
							/*** </TRANSNATIONAL ADDITIONAL CODE   *********/


							/********* FIRSTDATA COMMENT OUT CODE  **********
							// $firstData = new FirstData($nvp['gateway_id'], $nvp['password'], $nvp['hmac_key'], $nvp['key_id'], $testMode);


							$firstData->setTransactionType(FirstData::TRAN_PURCHASE);

							// card purchase
							if ($payment_method == 'card') {
								$firstData->setCustomerName($nvp['cardholder_name'])
										  ->setCreditCardNumber($nvp['cc_number'])
										  ->setAmount($nvp['amount'])
										  ->setCreditCardExpiration($nvp['cc_expiry'])
										  ->setCreditCardVerification($nvp['cvv'])
								;
							// check purchase
							} elseif ($payment_method == 'check') {
								$firstData->setAmount($nvp['amount'])
										  ->setCheckType($nvp['check_type'])
										  ->setBankRoutingNumber($nvp['bank_id'])
										  ->setBankAccountNumber($nvp['account_number'])
										  ->setCheckNumber($nvp['check_number'])
										  ->setCustomerName($nvp['cardholder_name'])
										  ->setEcommerceFlag($nvp['ecommerce_flag'])
										  ->setCustomerIdType($nvp['customer_id_type'])
										  ->setCustomerId($nvp['customer_id_number'])
										  ->setCustomerEmail($nvp['client_email'])
										  ->setCustomerAddress($address)
								;
							}
							*******************   FIRSTDATA COMMENTED OUT CODE   **********/

							// set soft descriptors for fee
							/*if ($amount_type == 'fee') {
								$dba_phone = $company_settings['diversified']['dba_phone'];

								$firstData->setSoftDescriptors($dba_name=$dba_name, $merchant_contact_info=$dba_phone);
							}*/

							/*** TRANSNATIONAL ADDITIONAL CODE   *********/
							$result = null;
							// $cust_url_path;
							$caller = new CallTransnationalAPI($cust_api_key,$testMode);
							try{
								$address_123 = ProcessTransaction::buildAddressObject($cardholder_name_first,$cardholder_name_last);
								$Transnational = ProcessTransaction::Card("keyed",$creditcard_number,$creditcard_expmonth . "/" . $creditcard_expyear,$creditcard_ccv);
								$Transnational->setAmount($amount);
								$Transnational->setBillingAddress($address_123);
								$Transnational->setType(ProcessTransaction::TYPE_OPTIONS["TYPE_SALE"]);
								$result = $caller->ProcessTransaction($Transnational);
								$suc = $result->isSuccess();
							}catch(TransnationalException $e){
								$suc = 0;
								$result = ProcessTransactionResult::FAIL_RESULT(
									'Unexpected error!<br>Please contact <strong>' . $support_company_name . '</strong> at <strong>' . $support_company_phone . '</strong>.  Customer Service is available from 8am to 5pm, Monday-Friday.'
								);
							}


							/*** </TRANSNATIONAL ADDITIONAL CODE   *********/
							if(!$suc){
								$errorCode = 999;
								$errorMsg = 'Unexpected error!<br>Please contact <strong>' . $support_company_name . '</strong> at <strong>' . $support_company_phone . '</strong>.  Customer Service is available from 8am to 5pm, Monday-Friday.';
								$failure_msg = "";
								$error_comment = "";
								$approved = false;
								/* if api error */
								if($result->get_data() == null){
									$errorCode = 999;
									$errorMsg = $result->get_message();
									$failure_msg = $errorMsg;
									$error_comment = $result->get_message();

								}else{
									$errorCode = $result->get_rb_response_code();
									$errorMsg = $result->getProcessorResponse();
									$failure_msg = $errorMsg;
									$error_comment = $result->getProcessorDefinition();


									$errorCodeEnd = $result->get_response_code();
									$errorMsgEnd = $result->getEndpointResponse();
									$error_commentEnd = $result->getEndpointMeaning();
								}

								/***/
								//	 * Get transaction bank response type
								//	 *  S = Successful Response Codes
								//	 *	R = Reject Response Codes
								//	 *	D = Decline Response Codes
								$bank_response_type = "N/A";
								$bank_response_code = $errorCode;
								$bank_response_name = $errorMsg;
								$bank_response_action = $result->getEndpointAction();
								$bank_response_comment = $error_comment;



								// $bank_response_type = $firstData->getBankResponseType();
								// $bank_response_code = $firstData->getBankResponseCode();
								// $bank_response_name = $firstData->getBankResponseName();
								// $bank_response_action = $firstData->getBankResponseAction();
								// $bank_response_comment = $firstData->getBankResponseComments();

								$bank_message = $bank_response_name;
								$exact_resp_code = $errorCodeEnd;
								$exact_message = $error_commentEnd;

								$trans_approved = 0;
								$trans_response = $exact_message;

								// add response data
								$json_array['trans_approved'] = $errorCode;
								$json_array['trans_response'] = $errorMsg;

								$json_array['bank_response_type'] = $bank_response_type;
								$json_array['bank_response_code'] = $bank_response_code;
								$json_array['bank_response_name'] = $bank_response_name;
								$json_array['bank_response_action'] = $bank_response_action;
								$json_array['bank_response_comment'] = $bank_response_comment;

								$json_array['bank_message'] = $bank_message;
								$json_array['exact_resp_code'] = $exact_resp_code;
								$json_array['exact_message'] = $exact_message;
								$json_array['utrans_key'] = $guid;


								$json_array[$amount_type]['trans_approved'] = $errorCode;
								$json_array[$amount_type]['trans_response'] = $errorMsg;

								$json_array[$amount_type]['bank_response_type'] = $bank_response_type;
								$json_array[$amount_type]['bank_response_code'] = $bank_response_code;
								$json_array[$amount_type]['bank_response_name'] = $bank_response_name;
								$json_array[$amount_type]['bank_response_action'] = $bank_response_action;
								$json_array[$amount_type]['bank_response_comment'] = $bank_response_comment;

								$json_array[$amount_type]['bank_message'] = $bank_message;
								$json_array[$amount_type]['exact_resp_code'] = $exact_resp_code;
								$json_array[$amount_type]['exact_message'] = $exact_message;
								$json_array[$amount_type]['trans_date'] = date("Y-m-d H:i:s");


								$json_array[$amount_type]['authorization_num'] = $result->get_rb_auth_code();
								$json_array[$amount_type]['retrieval_ref_no'] = $result->get_rb_id();
								$json_array[$amount_type]['sequence_no'] = "N/A";//$firstData->getSequenceNumber();

								// if ($amount_type == 'fee') {
								$json_array[$amount_type]['dba_name'] = $dba_name;
								// } else {
								// 	$json_array[$amount_type]['dba_name'] = $response_array['merchant_name'];
								// }

								$json_array[$amount_type]['trans_amount'] = $db_amount;

								$token = 'NULL';

								// temp fix for unexpected error
								if ($errorCode == 999) {
									$token = 'NULL';
								}

								$token = (empty($token))
								? 'NULL'
								: $token;

								// set message for bill type only
								// if ($amount_type == 'bill') {
								/********** START FAILURE MESSAGE *********/
								$trans_approved = $json_array['bill']['trans_approved'];
								$trans_response = $json_array['bill']['trans_response'];

								$exact_resp_code =	$json_array['bill']['exact_resp_code'];
								$exact_message = $json_array['bill']['exact_message'];

								$bank_response_code = $json_array['bill']['bank_response_code'];
								$bank_response_name = $json_array['bill']['bank_response_name'];
								$bank_response_action =	$json_array['bill']['bank_response_action'];
								$bank_response_comment = $json_array['bill']['bank_response_comment'];

								$failure_msg_type = ($payment_method == 'card') ? 'credit card' : $payment_method;
								$failure_msg = "<p>Your $failure_msg_type was declined for the following reason:";

								$failure_msg .= "<ul class='list-group'>";

								if ($trans_approved == 999) {
									$failure_msg .= "<li class='list-group-item'>Code: {$exact_resp_code}</li><li class='list-group-item'>{$exact_message}</li><li class='list-group-item'>{$trans_response}</li>";
								} elseif ($trans_approved > 100) {
									$failure_msg .= "<li class='list-group-item'>Code: {$bank_response_code} - {$bank_response_name}</li><li class='list-group-item'>{$bank_response_comment}</li>";
								} elseif ($trans_approved == 100) {
									$failure_msg .= "<li class='list-group-item'>Code: {$exact_resp_code} - {$exact_message}</li>";
								} elseif ($trans_approved > 1 && $trans_approved < 100) {
									$failure_msg .= "<li class='list-group-item'>Code: {$trans_approved} - {$trans_response}</li>";
								}

								if (!empty($bank_response_action) && ($bank_response_action == 'Fix' || $bank_response_action == 'Call' || $bank_response_action == 'Voice')) {
									$failure_msg .= "<li class='list-group-item'>Please call <strong>{$support_company_name}</strong> at <strong>{$support_company_phone}</strong> for assistance.<br>Customer Service is available from 8am to 5pm, Monday-Friday.</li>";
								} elseif ($bank_response_action == 'Resend') {
									$failure_msg .= "<li class='list-group-item'>Please retry your payment in 15 minutes.</li>";
								}

								$failure_msg .= "</ul></p>";

								$response_messages[] = $failure_msg;
									// set message to session for redirect
								set_message($response_messages, 'alert-warning', true);

								$jsonarray['redirect-failure'] = "/connect/payment.php?company={$company}";

								foreach ($customer_ids as $customer_id) {
									$query = "
									INSERT INTO `customer_transaction_exceptions`
									(
									`CompanyId`
									,	`CustomerId`
									,	`trans_key`
									,	`trans_type`
									,	`trans_amount`
									,	`trans_code`
									,	`trans_message`
									,	`bank_response_code`
									,	`bank_response_comment`
									,	`bank_response_action`
									,	`bank_message`
									,	`exact_resp_code`
									,	`exact_message`
									,	`ivr_key`
									,	`third4`
									,	`transarmor_token`
									,	`reprocessed`
									,	`card_holder`
									,	`payment_method`
									,	`source`
									)
									VALUES
									(
									$company_id
									,	$customer_id
									,	'" . $guid . "'
									,	'" . $amount_type . "'
									,	$db_amount
									,	'" . $errorCode . "'
									,	'" . addslashes($errorMsg) . "'
									,	'" . $bank_response_code . "'
									,	'" . $bank_response_comment . "'
									,	'" . $bank_response_action . "'
									,	'" . addslashes($bank_message) . "'
									,	'" . $exact_resp_code . "'
									,	'" . addslashes($exact_message) . "'
									,	NULL
									";

									if ($payment_method == 'card') {
										$query .= "
										,	'" . substr($creditcard_number, -8, 4) . "'
										";
									} elseif ($payment_method == 'check') {
										$query .= "
										,	NULL
										";
									}

									$query .= "
									,	$token
									,	0
									,	'" . $cardholder_name_first . " " . $cardholder_name_last . "'
									,	'" . $payment_method . "'
									,	'" . $source . "'
									)
									";

									// Results of SQL statement
									$resultSQL = $db->query($query);
									// Error?
									if (!$resultSQL) {
										$exceptions[] = 'ERROR: ' . $db->error($db) . ' [' . $query . ']';
										$exceptions[] = 'File:   ' . __FILE__;
										$exceptions[] = 'Line #: ' . __LINE__;
										$exception = true;
									}
								}


							}

							// set approval status
							// $approved = $firstData->isApproved() == 1
							// ? true
							// : false;

							// set response data
							// $response = $firstData->getResponse();
							// decode json data
							// $response_array = json_decode($response, true);

							// add response data

							$json_array['trans_approved'] = $suc;
							$json_array['trans_response'] = $error_comment;

							// $json_array['trans_approved'] = $firstData->isApproved();
							// $json_array['trans_response'] = $firstData->getBankResponseMessage();

							$json_array['bank_response_type'] = "N/A";
							$json_array['bank_response_code'] = $errorCode;
							$json_array['bank_response_name'] = $errorMsg;
							$json_array['bank_response_action'] = "N/A";
							$json_array['bank_response_comment'] = "N/A";

							// $json_array['bank_response_type'] = $firstData->getBankResponseType();
							// $json_array['bank_response_code'] = $firstData->getBankResponseCode();
							// $json_array['bank_response_name'] = $firstData->getBankResponseName();
							// $json_array['bank_response_action'] = $firstData->getBankResponseAction();
							// $json_array['bank_response_comment'] = $firstData->getBankResponseComments();

							$json_array['bank_message'] = "N/A";
							$json_array['exact_resp_code'] = $errorCodeEnd;
							$json_array['exact_message'] = $errorMsgEnd;
							$json_array['utrans_key'] = $guid;


							// $json_array['bank_message'] = $firstData->getBankResponseMessage();
							// $json_array['exact_resp_code'] = $firstData->getExactResponseCode();
							// $json_array['exact_message'] = $firstData->getExactResponseMessage();
							// $json_array['utrans_key'] = $guid;



							$json_array[$amount_type]['trans_approved'] = $suc;
							$json_array[$amount_type]['trans_response'] = $error_comment;


							// add trans response data to response array
							// $json_array[$amount_type]['trans_approved'] = $firstData->isApproved();
							// $json_array[$amount_type]['trans_response'] = $firstData->getBankResponseMessage();

							$json_array[$amount_type]['bank_response_type'] = "N/A";
							$json_array[$amount_type]['bank_response_code'] = $errorCode;
							$json_array[$amount_type]['bank_response_name'] = $errorMsg;
							$json_array[$amount_type]['bank_response_action'] = "N/A";
							$json_array[$amount_type]['bank_response_comment'] = "N/A";


							// $json_array[$amount_type]['bank_response_type'] = $firstData->getBankResponseType();
							// $json_array[$amount_type]['bank_response_code'] = $firstData->getBankResponseCode();
							// $json_array[$amount_type]['bank_response_name'] = $firstData->getBankResponseName();
							// $json_array[$amount_type]['bank_response_action'] = $firstData->getBankResponseAction();
							// $json_array[$amount_type]['bank_response_comment'] = $firstData->getBankResponseComments();

							$json_array[$amount_type]['bank_message'] = "N/A";
							$json_array[$amount_type]['exact_resp_code'] = $errorCodeEnd;
							$json_array[$amount_type]['exact_message'] = $errorMsgEnd;
							$json_array[$amount_type]['trans_date'] = date("Y-m-d H:i:s");
							$json_array[$amount_type]['authorization_num'] = $result->get_rb_auth_code();
							$json_array[$amount_type]['retrieval_ref_no'] = "N/A";
							$json_array[$amount_type]['sequence_no'] = "N/A";

							$json_array[$amount_type]['dba_name'] = $dba_name;

							$json_array[$amount_type]['trans_amount'] = $db_amount;

							// if approved insert customer_transactions

								/**TODO NOTE  TO SELF THIS CHUNK OF CODE SEEMS LIKE IT COULD BE REPLACED BY
								A LARGE IF $suc ELSE STATMENT. I DIDN'T FUCK WITH IT THOUGH BECAUSE I JUST WANT THE SITE TO WORK*/
								if ($suc) {
									$bill_approved = true;

								// insert transactions for each customer_id
									$i = 0;
									foreach ($customer_ids as $customer_id) {
										if ($i == 0) {
											$parent_id = $customer_id;
										}

										$_amount = ($amount_type == 'fee' ? $amount : $customer_amounts[$i]);
										number_format($_amount, 2, '.', '');

										$query = "
										SELECT DISTINCT
										`CustomerName`
										FROM
										`customer`
										WHERE
										`Id` = $customer_id
										AND
										`CompanyId` = $company_id
										LIMIT 1
										";

									// Results of SQL statement
										$resultSQL = $db->query($query);
										$objects = $db->objects($resultSQL);

										$CustomerName = $objects->CustomerName;

										$cclastfour = ($payment_method == 'card')
										? substr($creditcard_number, -4, 4)
										: substr($account_number, -4, 4);

										$ccexpdate = ($payment_method == 'card')
										? "'" . $creditcard_expmonth . substr($creditcard_expyear, -2) . "'"
										: 'NULL';

										$query = "
										INSERT INTO `customer_transactions`
										(
										`CompanyId`
										,	`CustomerId`
										,	`ParentId`
										,	`TransDate`
										,	`Amount`
										,	`Posted`
										,	`payment_method`
										,	`card_holder`
										,	`AuthCode`
										,	`CCType`
										,	`CCLastFour`
										,	`CCExpDate`
										,	`CustomerIPNo`
										,	`FeeTransaction`
										,	`TransactionKey`
										,	`isIVR`
										,	`ivr_key`
										,	`source`
										)
										VALUES
										(
										$company_id
										,	$customer_id
										,	$parent_id
										,	'" . date("Y-m-d H:i:s") . "'
										,	$_amount
										,	'N'
										,	'" . $payment_method . "'
										,	'" . $cardholder_name_first . " " . $cardholder_name_last . "'
										,	'" . $result->get_rb_auth_code() . "'
										,	'" . $result->get_rb_card_type() . "'
										,	'" . $cclastfour . "'
										,	{$ccexpdate}
										,	'" . $customer_ip . "'
										,	NULL
										,	'" . $guid . "'
										,	0
										,	NULL
										,	'" . $source . "'
										)
										";

									// Results of SQL statement
										$resultSQL = $db->query($query);

									// Error?
										if (!$resultSQL) {
											$exceptions[] = 'ERROR: ' . $db->error($db) . ' [' . $query . ']';
											$exceptions[] = 'File:   ' . __FILE__;
											$exceptions[] = 'Line #: ' . __LINE__;
											$exception = true;

											/************ SUCCESSFUL TRANSACTION NOT ADDED TO WEBSITE DATABASE ************/
											/********** Send error back to Diversified **********/
											$to_email = 'billpay@diversifiedtechnology.net';
											$bcc_email = 'support@utbill.com';
											$subject = $CompanyName . ' - Successful Payment - Not Added to Database';
											$body = $query;
											$tag = 'billpay-payment-exception';
											send_email($company_id, $to_email, $bcc_email, $subject, $body, $tag, false);
											/********** END SUCCESSFUL TRANSACTION NOT ADDED TO WEBSITE DATABASE **********/
										}
										$queryfee = "
										INSERT INTO `customer_transactions`
										(
										`CompanyId`
										,	`CustomerId`
										,	`ParentId`
										,	`TransDate`
										,	`Amount`
										,	`Posted`
										,	`payment_method`
										,	`card_holder`
										,	`AuthCode`
										,	`CCType`
										,	`CCLastFour`
										,	`CCExpDate`
										,	`CustomerIPNo`
										,	`FeeTransaction`
										,	`TransactionKey`
										,	`isIVR`
										,	`ivr_key`
										,	`source`
										)
										VALUES
										(
										$company_id
										,	$customer_id
										,	$parent_id
										,	'" . date("Y-m-d H:i:s") . "'
										,	$feeamt
										,	'N'
										,	'" . $payment_method . "'
										,	'" . $cardholder_name_first . " " . $cardholder_name_last . "'
										,	'" . $result->get_rb_auth_code() . "'
										,	'" . $result->get_rb_card_type() . "'
										,	'" . $cclastfour . "'
										,	{$ccexpdate}
										,	'" . $customer_ip . "'
										,	" . "'Y'" . "
										,	'" . $guid . "'
										,	0
										,	NULL
										,	'" . $source . "'
										)
										";

									// Results of SQL statement
										$resultSQL = $db->query($queryfee);

									// Error?
										if (!$resultSQL) {
											$exceptions[] = 'ERROR: ' . $db->error($db) . ' [' . $queryfee . ']';
											$exceptions[] = 'File:   ' . __FILE__;
											$exceptions[] = 'Line #: ' . __LINE__;
											$exception = true;

											/************ SUCCESSFUL TRANSACTION NOT ADDED TO WEBSITE DATABASE ************/
											/********** Send error back to Diversified **********/
											$to_email = 'billpay@diversifiedtechnology.net';
											$bcc_email = 'support@utbill.com';
											$subject = $CompanyName . ' - Successful Payment Fee - Not Added to Database';
											$body = $queryfee;
											$tag = 'billpay-payment-exception';
											send_email($company_id, $to_email, $bcc_email, $subject, $body, $tag, false);
											/********** END SUCCESSFUL TRANSACTION NOT ADDED TO WEBSITE DATABASE **********/
										}


										$i++;
									}

									/********** START RECEIPT **********/
									$receipt_text .= '<div>' . $dba_name . '</div>';

									// use merchant address from web database for bill payments
									$receipt_text .= '<div style="margin-bottom: 10px;">' . $CompanyInfo . '</div>';


									$receipt_text .= '<table cellpadding="0" cellspacing="5" border="0">';
									$receipt_text .= '<tbody>';
									$receipt_text .= '<tr>';
									$receipt_text .= '<td>TYPE:</td>';
									$receipt_text .= '<td><strong>' . ucwords($amount_type) . ' Payment</strong></td>';
									$receipt_text .= '</tr>';
									$receipt_text .= '<tr>';
									$receipt_text .= '<td>CUSTOMER NAME:</td>';
									$receipt_text .= '<td><strong>' . $CustomerName . '</strong></td>';
									$receipt_text .= '</tr>';
									$receipt_text .= '<tr>';
									$receipt_text .= '<td>CUSTOMER ACCOUNT' . ((count($customer_ids) > 0) ? 'S' : '') . ':</td>';
									$receipt_text .= '<td><strong>' . $customer_account . '</strong></td>';
									$receipt_text .= '</tr>';
									$receipt_text .= '<tr>';
									$receipt_text .= '<td>AMOUNT:</td>';
									$receipt_text .= '<td><strong>$' . number_format((float)$db_amount, 2, '.', '') . ' ' . $result->get_currency() . '</strong></td>';
									$receipt_text .= '</tr>';
									$receipt_text .= '<tr>';

									if ($payment_method == 'card') {
										$receipt_text .= '<td valign="top">CARD HOLDER:</td>';
										$receipt_text .= '<td valign="top"><strong>' . stripslashes($cardholder_name_first . " " . $cardholder_name_last) . '</strong></td>';
										$receipt_text .= '</tr>';
										$receipt_text .= '<tr>';
										$receipt_text .= '<td>CARD NUMBER:</td>';
										$receipt_text .= '<td><strong>' . $result->get_rb_card_type() . ' - ' . substr($creditcard_number, -4, 4) . '</strong></td>';
										$receipt_text .= '</tr>';
									} elseif ($payment_method == 'check') {
										$receipt_text .= '<td valign="top">BANK ACCOUNT HOLDER:</td>';
										$receipt_text .= '<td valign="top"><strong>' . stripslashes($cardholder_name_first . " " . $cardholder_name_last) . '</strong><br>' . $receipt_address . '</td>';
										$receipt_text .= '</tr>';
										$receipt_text .= '<tr>';
										$receipt_text .= '<td>BANK ACCOUNT NUMBER:</td>';
										$receipt_text .= '<td><strong>********' . substr($account_number, -4, 4) . '</strong></td>';
										$receipt_text .= '</tr>';
									}

									$receipt_text .= '<tr>';
									$receipt_text .= '<td>DATE/TIME:</td>';
									$receipt_text .= '<td><strong>' . $json_array[$amount_type]['trans_date'] . '</strong></td>';
									$receipt_text .= '</tr>';
									$receipt_text .= '<tr>';
									$receipt_text .= '<td>REFERENCE #:</td>';
									$receipt_text .= '<td><strong>' . $result->get_rb_id() . '</strong></td>';
									$receipt_text .= '</tr>';
									$receipt_text .= '<tr>';
									$receipt_text .= '<td>AUTHORIZATION #:</td>';
									$receipt_text .= '<td><strong>' . $result->get_rb_auth_code() . '</strong></td>';
									$receipt_text .= '</tr>';
									$receipt_text .= '<tr>';
									$receipt_text .= '<td>APPEAR ON STMT AS:</td>';
									$receipt_text .= '<td><strong>' . $dba_name . '</strong></td>';
									$receipt_text .= '</tr>';
									$receipt_text .= '</tbody>';
									$receipt_text .= '</table>';
									$receipt_text .= '<div style="margin-top: 10px;margin-bottom: 20px;"><strong>' . $result->get_response() . '</strong> - Thank You</div>';
									$receipt_text .= '<hr>';
									/********** END RECEIPT **********/

								//echo "<p>" . $receipt_text . "</p>";
								}
							}
						}


				// if bill amount is approved set success message and insert customer_receipt
						if ($bill_approved) {
							/********** START SUCCESS MESSAGE **********/
							$bank_message = $json_array['bill']['bank_message'];
							$bill_dba_name = $json_array['bill']['dba_name'];
							$bill_trans_amount = number_format($json_array['bill']['trans_amount'], 2, '.', ',');
							$bill_authorization_num = $json_array['bill']['authorization_num'];

							// $fee_dba_name = $json_array['fee']['dba_name'];
							// $fee_trans_amount = number_format($json_array['fee']['trans_amount'], 2, '.', ',');
							// $fee_authorization_num = $json_array['fee']['authorization_num'];


							$utrans_key = $json_array['utrans_key'];

							$success_msg = "<h4>Thank you for your payment of <strong>$" . $bill_trans_amount . "</strong>!</h4>";

							$success_msg .= "<p>The confirmation number for your credit card payment of <strong>$" . $bill_trans_amount . "</strong> is <strong>{$bill_authorization_num}</strong> and will appear on your credit card statement as <strong>" . $bill_dba_name . "</strong>.</p>";

						// if ($fee_approved === true) {
						// 	$success_msg .= "<p>The confirmation number for your credit card convenience fee of <strong>$" . $fee_trans_amount . "</strong> is <strong>" . $fee_authorization_num . "</strong> and will appear on your credit card statement as <strong>" . $fee_dba_name . "</strong>.</p>";
						// }

							if ($is_admin == 0) {
								$success_msg .= "<p>It may take up to 24 hours for this payment to be reflected in your <a href='/connect/accounts.php?company=" . $company . "'>Account Summary</a>.  Until then, it will show as <strong>Scheduled</strong> in your <a href='/connect/payment-history.php?company=" . $company . "'>Payment History</a>.</p>";
							}

							$success_msg .= '<hr><div><button id="authorization" class="btn btn-default" type="button" onclick="viewReceipt(\'' . html_entity_decode($utrans_key, ENT_QUOTES|ENT_HTML5) . '\')"><i class="fa fa-print fa-fw"></i>Print this receipt</button> and retain for your records.</div>';

							$response_messages[] = $success_msg;
					// set success message to session for redirect
							set_payment_success_message($response_messages, true);

							$jsonarray['redirect-success'] = "/connect/payment-success.php?company={$company}";
							/********** END SUCCESS MESSAGE **********/

							$json_array['trans_approved'] = $json_array['bill']['trans_approved'];
							$json_array['trans_response'] = $json_array['bill']['trans_response'];
							$json_array['bank_response_comment'] = $json_array['bill']['bank_response_comment'];

							$receipt = '<div><img src="' . $host_settings['domain'] . 'assets/logos/' . $LogoName . '" style="max-width: 200px;max-height: 100px;"></div>';
							$receipt .= '<hr>';
							$receipt .= nl2br(trim($receipt_text));
							$receipt .= '<div>Please retain this copy for your records.</div>';
							$receipt .= '<div style="margin-bottom: 10px;">Cardholder will pay above amount to card issuer pursuant to cardholder agreement.</div>';

					// insert receipts for each customer_id
							foreach ($customer_ids as $customer_id) {
								$receipt = str_replace("'", "&#39;", $receipt);

								$query = "
								INSERT INTO `customer_receipts`
								(
								`CompanyId`
								,	`CustomerId`
								,	`TransactionKey`
								,	`Receipt`
								)
								VALUES
								(
								$company_id
								,	$customer_id
								,	'" . $guid . "'
								,	'" . nl2br(trim($receipt)) . "'
								)
								";

						// Results of SQL statement
								$resultSQL = $db->query($query);

						// Error?
								if (!$resultSQL) {
									$exceptions[] = 'ERROR: ' . $db->error($db) . ' [' . $query . ']';
									$exceptions[] = 'File:   ' . __FILE__;
									$exceptions[] = 'Line #: ' . __LINE__;
									$exception = true;
								}
							}

							if ($send_customer_receipt === true || $send_admin_receipt === true) {
								$message_text = $receipt . '<br><hr>Please do not reply to this message. Replies to this message are routed to an unmonitored mailbox.<br>If you did not make this request and feel you are receiving this message in error, please contact <strong>' . $support_company_name . '</strong> at <strong>' . $support_company_phone . '</strong>.  Customer Service is available from 8am to 5pm, Monday-Friday.<br><br>To ensure delivery to your inbox, please add <a href="mailto:' .  $email_settings['supportmailto'] . '" target="_blank">' . $email_settings['supportmailto'] . '</a> to your email addresses/contacts!';

						// send email with mandrill
						// R = Rapid Pay  - only send to admin
						// O = Office -  only send to admin
								if ($send_admin_receipt === true) {
									if (($source == 'R' || $source == 'O')) {
										$to_email = $admin_email;
										$bcc_email = null;
									} else {
										$to_email = $_SESSION['custemail'];
										$bcc_email = $admin_email;
									}
								} else {
									if ($source == 'U') {
										$to_email = $_SESSION['custemail'];
										$bcc_email = null;
									}
								}

								$subject = $CompanyName . ' - Payment Receipt';
								$body = $message_text;
								$tag = 'billpay-receipt';
						// set message to session for redirect
								send_email($company_id, $to_email, $bcc_email, $subject, $body, $tag, true);
							}
						}
					}
				}
			}

			echo json_encode($jsonarray);

			if ($exception === true) {
				write_logfile($logfilename, str_repeat('*',72));
				write_logfile($logfilename, 'Start Exceptions for ' . $_SESSION['cname'] . '['. $_SESSION['cid'] . ']');

				$end_time = new DateTime("now");
				$interval = $start_time->diff($end_time);

				foreach ($exceptions as $error) {
					write_logfile($logfilename, $error);
				}

				write_logfile($logfilename, $interval->format('Transaction Duration: %H:%I:%S'));
				write_logfile($logfilename, 'End Exceptions for ' . $_SESSION['cname'] . '['. $_SESSION['cid'] . ']');
				write_logfile($logfilename, str_repeat('*',72));
			}
