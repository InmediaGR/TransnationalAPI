<?php
namespace App\Transnational\APIException;

use Exception;
class TransnationalException extends Exception{
	public $tnp = 'Unexpected error! Please contact Us';

	public function get_tnp_error_message(){
		return $this->tnp;
	}
}

