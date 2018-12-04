<?php
namespace App\Transnational\APIException;

use Exception;
class GenericTransnationalException extends TransnationalException{
	public function __construct($tnp, $code = 0, Exception $previous = null) {
		$this->tnp = $tnp;
		parent::__construct($tnp, $code, $previous);
	}


}

