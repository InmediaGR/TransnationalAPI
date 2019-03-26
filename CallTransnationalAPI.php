<?php
require_once("transnational/APIExceptions/WrongCallException.php");

class CallTransnationalAPI{

	public $api_key;
	public $dev;

	public function __construct($api_key,$dev = false){
		$this->api_key = $api_key;
		$this->dev = $dev;
		// if($test){
		// 	$this->url = TransnationalAPI::URL;
		// }else{
		// 	if($this->url == null){
		// 		$this->url = TransnationalAPI::DEV_URL;
		// 	}else{
		// 		$this->url = $url;
		// 	}
		// }
	}

	public function ProcessTransaction($process_transation_object){
		if($process_transation_object instanceof ProcessTransaction){
			$process_transation_object->setEnv($this->dev);
			return $process_transation_object->ProcessTransaction($this->api_key);
		}
		throw new WrongCallException($process_transation_object,ProcessTransaction::class);

	}
}
