<?php

namespace App\Transnational;

use App\Transnational\ResponseTrait;

class QueryTransactionResult extends TransnationalResult
{

	use ResponseTrait\Timestamps;
	use ResponseTrait\AddonDiscount;
	// /**
	// *	Helper Functions
	// */
	// public function isSuccess(){
	// 	return ($this->response_code == self::HTTP_OK && $this->get_request_status() == self::SUCCESS);
	// }
	public function isResult(){
		return ($this->isSuccess() && $this->data != null);

	}

}
