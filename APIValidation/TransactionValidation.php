<?php
namespace Transnational\APIValidation;

abstract class TransactionValidation{

	protected $exception = null;
	/**
	 * Checks the paramters of the passed in check
	 * @param $exceptions - array to add exception to
	 */
	public function getValidate(&$exceptions){
		$exception = $this->validate();
		if($exception){
			$exceptions[] = $exception;
		}
	}
}
