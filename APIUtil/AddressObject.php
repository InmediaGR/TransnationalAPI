<?php
namespace App\Transnational\APIUtil;

/*
*	Class to build up address objects for use with POST requests
*/
class AddressObject{

	protected $address_object = [];

	/**
	*	Address Options
	*/
	// 'ADDRESS_ID' => 'address_id',
	const FIRST_NAME = 'first_name';
	const LAST_NAME = 'last_name';
	const COMPANY = 'company';
	const ADDRESS_LINE_1 = 'address_line_1';
	const ADDRESS_LINE_2 = 'address_line_2';
	const CITY = 'city';
	const STATE = 'state';
	const POSTAL_CODE = 'postal_code';
	const COUNTRY = 'country';
	const EMAIL = 'email';
	const PHONE = 'phone';
	const FAX = 'fax';

	static function getAddressFields() {
		$oClass = new \ReflectionClass(__CLASS__);
		return $oClass->getConstants();
	}


	public function __construct($first_name = null ,$last_name = null ,$company = null ,$address_line_1 = null ,$address_line_2 = null ,$city = null ,$state = null ,$postal_code = null ,$country = null ,$phone = null ,$fax = null ,$email = null){
		$this->address_object = [
			self::FIRST_NAME => $first_name,
			self::LAST_NAME => $last_name,
			self::COMPANY => $company,
			self::ADDRESS_LINE_1 => $address_line_1,
			self::ADDRESS_LINE_2 => $address_line_2,
			self::CITY => $city,
			self::STATE => $state,
			self::POSTAL_CODE => $postal_code,
			self::COUNTRY => $country,
			self::PHONE => $phone,
			self::FAX => $fax,
			self::EMAIL => $email
		];
	}

	public function get(){
		return $this->address_object;
	}

	/**
	 * set the address object fields
	 * @param string $key - the address_field
	 * @param string $value - the data
	 */
	public function set($key, $value){
		if(in_array($key,self::getAddressFields())){
			$this->address_object[$key] = $value;
			return true;
		}
		return false;
	}

	public function setFirstname($firstname){
		$this->address_object[self::FIRST_NAME] = $firstname;
		return $this;
	}
	public function setLastname($lastname){
		$this->address_object[self::LAST_NAME] = $lastname;
		return $this;
	}
	public function setCompany($company){
		$this->address_object[self::COMPANY] = $company;
		return $this;
	}
	public function setAddress($ADDRESS_LINE_1,$ADDRESS_LINE_2 = null){
		$this->address_object[self::ADDRESS_LINE_1] = $ADDRESS_LINE_1;
		$this->address_object[self::ADDRESS_LINE_2] = $ADDRESS_LINE_2;
		return $this;
	}
	public function setCity($city){
		$this->address_object[self::CITY] = $city;
		return $this;
	}
	public function setState($state){
		$this->address_object[self::STATE] = $state;
		return $this;
	}
	public function setPostalCode($postal_code){
		$this->address_object[self::POSTAL_CODE] = $postal_code;
		return $this;
	}

	public function setCountry($country){
		$this->address_object[self::COUNTRY] = $country;
		return $this;
	}
	public function setPhone($phone){
		$this->address_object[self::PHONE] = $phone;
		return $this;
	}
	public function setFax($fax){
		$this->address_object[self::FAX] = $fax;
		return $this;
	}
	public function setEmail($email){
		$this->address_object[self::EMAIL] = $email;
		return $this;
	}

	/**
	 * set the city state and zipcode at once
	 * @param string $city
	 * @param string $state
	 * @param string $zip - postal_code
	 */
	public function setCSZ($city, $state, $zip){
		$this->address_object[self::CITY] = $city;
		$this->address_object[self::STATE] = $state;
		$this->address_object[self::POSTAL_CODE] = $zip;
		return $this;
	}
}
