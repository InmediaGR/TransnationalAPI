<?php
namespace App\Transnational;

use App\Transnational\TransnationalAuthGetRequest;

class TransactionStatus extends TransnationalAuthGetRequest
{

	/**
	*	Transaction Status path
	*/
	const PATH = "transaction/<transaction_id>";

	protected $transaction_id;

	protected function set_transation_id($transaction_id){
		$this->transaction_id = $transaction_id;
	}

	protected function getURL(){
		$made_path = str_replace("<transaction_id>",$this->transaction_id,self::PATH);
		return self::URL . $made_path;
	}

	public function getStatus($api)
	{
		return $this->callAPI($api);
	}

	public static function get($transaction_id,$api)
	{
		$transaction_status = new self();
		$transaction_status->set_transation_id($transaction_id);
		return $transaction_status->getStatus($api);
	}
}
