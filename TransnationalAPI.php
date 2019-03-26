<?php
abstract class TransnationalAPI
{
	/**
	*	Transaction URL
	*/
	const DEV_URL = "https://sandbox.gotnpgateway.com/api/";
	const URL = "https://app.gotnpgateway.com/api/";

	/**
	*	DEBUG CONSTANT
	*/
	const DEBUG = 0;

	/* Variable that indicated the dev/prod status */
	protected $dev = false;

	/* sets the dev/prod status */
	public function setEnv($dev){
		$this->dev = $dev;
	}
	/**
	 * Returned HTTP HEADER CODE
	 */
	protected $HTTP_CODE = 500;

	/**
	 * The Http Headers
	 */
	protected $HTTP_HEADERS = [];

	/**
	 * Call the API using cURL based on the class calling the callAPI function
	 * @param string $auth - the auth key
	 */
	public function callAPI($auth){
		// create a new cURL resource
		$curl = curl_init();
		// set URL and other appropriate options
		curl_setopt($curl, CURLOPT_URL, $this->getURL());

		//Set Headers
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getHeaders($auth));
		if($this->method() != "GET"){
			if($this->method() == "POST"){
				curl_setopt($curl, CURLOPT_POST, 1);
			}else{
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->method());
			}
		}

		$headers = &$this->HTTP_HEADERS;
		$code = &$this->HTTP_CODE;
		curl_setopt($curl,CURLOPT_HEADERFUNCTION,
			/*
			 * https://stackoverflow.com/a/41135574/9301369
			 * Here is a very clean method of performing this using PHP closures.
			 * It also converts all headers to lowercase for consistent handling across servers and HTTP versions.
			 */
			function($curl, $header) use (&$headers)
			{
				$len = strlen($header);
				$header = explode(':', $header, 2);

				// ignore invalid headers
				if (count($header) < 2)
					return $len;

				$name = strtolower(trim($header[0]));
				if (!array_key_exists($name, $headers))
					$headers[$name] = [trim($header[1])];
				else
					$headers[$name][] = trim($header[1]);

				return $len;
			});


		//Set Other Options based on request class
		$this->curlOptions($curl,$auth);

		//Have curl return result to variable
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		// grab URL and pass it to the browser
		$result = curl_exec($curl);
		$this->HTTP_CODE = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if(self::DEBUG == 5 || self::DEBUG == 1){
			$this->var_dump_pre(curl_errno($curl));
			$this->var_dump_pre(curl_error($curl));
		}
		//get info from request
		if(self::DEBUG == 3 || self::DEBUG == 1 ){
			$information = curl_getinfo($curl);
			$this->var_dump_pre($information);
		}
		if(self::DEBUG == 5 || self::DEBUG == 1 ){
			$this->var_dump_pre($result);
		}
		// close cURL resource, and free up system resources
		curl_close($curl);
		return $result;
	}

	/**
	 * Set other method options for cURL
	 */
	protected abstract function curlOptions($curl,$auth);

	/**
	 * get Additional information from cURL
	 * Defaults returns no additional info
	 */
	protected function curlGetInfo($curl,$auth){
		return;
	}


	/**
	 * gets the method for calling the API
	 * Default is GET
	 */
	protected function method(){
		return "GET";
	}

	/**
	 * gets the URL for calling the API
	 */
	protected abstract function getURL();

	/**
	 * gets the Headers to set for the API
	 */
	protected abstract function getHeaders($auth);

	/**
	 * gets the POST data to send to the API
	 */
	protected function getPOST(){
		return null;
	}

	/**
	 * returns the headers from the cURL request
	 */
	public final function getHTTPHeaders(){
		return $this->HTTP_HEADERS;
	}

	/**
	 * returns the http code from the cURL request
	 */
	public final function getHTTPCode(){
		return $this->HTTP_CODE;
	}


	static function var_dump_pre($mixed = null) {
		echo '<pre>';
		var_dump($mixed);
		echo '</pre>';
		return null;
	}

	static function var_dump_ret($mixed = null) {
		ob_start();
		var_dump($mixed);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

}
