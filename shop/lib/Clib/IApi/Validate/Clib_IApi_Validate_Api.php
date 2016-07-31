<?php
/**
 * Enamoo OpenAPI용 변수 유효성 체크 Class
 *
 * @author Clib_ApiValidation.php, dn, leeys@godo.co.kr
 * @version 1.0
 * @date 2013-01-23
 *
 */
class Clib_IApi_Validate_Api extends Clib_IApi_Validate_Validate
{
	/*
	 const REQUIRED_YES = 1;
	 const REQUIRED_NO = 2;

	 const TYPE_INTEGER = 'integer';
	 const TYPE_FLOAT = 'float';
	 const TYPE_NUMBER = 'number';
	 const TYPE_STRING = 'string';
	 const TYPE_BOOL = 'bool';
	 const TYPE_ARRAY = 'array';
	 const TYPE_OBJECT = 'object';

	 const TYPE_LENGTH_EQUAL = 'equal';
	 const TYPE_LENGTH_EQUAL_LARGE = 'equal_large';
	 const TYPE_LENGTH_EQUAL_SMALL = 'equal_small';
	 const TYPE_LENGTH_LARGE = 'large';
	 const TYPE_LENGTH_SMALL = 'small';
	 */
	private $_conf_param = Array();

	/**
	 * Construct
	 * @return void
	 */
	public function __construct()
	{
		$this->_conf_param['key_authentication']['ap_code'] = Array(
			'required' => true,
			'type' => parent::TYPE_STRING,
			'length' => '5'
		);
		$this->_conf_param['key_authentication']['key'] = Array(
			'required' => true,
			'type' => parent::TYPE_STRING
		);

		$this->_conf_param['key_otp']['ap_code'] = Array(
			'required' => true,
			'type' => parent::TYPE_STRING,
			'length' => '5'
		);
		$this->_conf_param['key_otp']['key'] = Array(
			'required' => true,
			'type' => parent::TYPE_STRING
		);

		$this->_conf_param['goods_search']['data_type'] = Array(
			'required' => true,
			'type' => parent::TYPE_ENGLISH
		);
		$this->_conf_param['goods_search']['goods_no'] = Array(
			'type' => parent::TYPE_STRING,
			'length' => '14'
		);
		$this->_conf_param['goods_search']['st_date'] = Array(
			'required' => true,
			'type' => parent::TYPE_NUMBER,
			'length' => '8'
		);
		$this->_conf_param['goods_search']['ed_date'] = Array(
			'required' => true,
			'type' => parent::TYPE_NUMBER,
			'length' => '8'
		);
		$this->_conf_param['goods_search']['page'] = Array(
			'required' => true,
			'type' => parent::TYPE_NUMBER
		);

		$this->_conf_param['goods_insert']['data_type'] = Array(
			'required' => true,
			'type' => parent::TYPE_ENGLISH
		);
		$this->_conf_param['goods_insert']['data_url'] = Array(
			'required' => true,
			'type' => parent::TYPE_URL
		);

		$this->_conf_param['goods_insert_data']['goodsnm'] = Array(
			'required' => true,
			'type' => parent::TYPE_STRING,
			'length' => '0, 255'
		);
		$this->_conf_param['goods_insert_data']['consumer'] = Array(
			'type' => parent::TYPE_INTEGER,
			'length' => '0, 10'
		);
		$this->_conf_param['goods_insert_data']['price'] = Array(
			'type' => parent::TYPE_INTEGER,
			'length' => '0, 10'
		);
		$this->_conf_param['goods_insert_data']['reserve'] = Array(
			'type' => parent::TYPE_INTEGER,
			'length' => '0, 10'
		);
		$this->_conf_param['goods_insert_data']['maker'] = Array(
			'type' => parent::TYPE_STRING,
			'length' => '0, 50'
		);
		$this->_conf_param['goods_insert_data']['brandno'] = Array(
			'type' => parent::TYPE_STRING,
			'length' => '0, 10'
		);
		$this->_conf_param['goods_insert_data']['launch_dt'] = Array(
			'type' => parent::TYPE_STRING,
			'length' => '0, 10'
		);
		$this->_conf_param['goods_insert_data']['origin'] = Array(
			'type' => parent::TYPE_STRING,
			'length' => '0, 50'
		);
		$this->_conf_param['goods_insert_data']['img_i'] = Array(
			'type' => parent::TYPE_URL,
			'length' => '0, 255'
		);
		$this->_conf_param['goods_insert_data']['img_s'] = Array(
			'type' => parent::TYPE_URL,
			'length' => '0, 255'
		);
		$this->_conf_param['goods_insert_data']['img_m'] = Array(
			'type' => parent::TYPE_URL,
			'length' => '0, 255'
		);
		$this->_conf_param['goods_insert_data']['img_l'] = Array(
			'type' => parent::TYPE_URL,
			'length' => '0, 255'
		);
		$this->_conf_param['goods_insert_data']['open'] = Array(
			'type' => parent::TYPE_INTEGER,
			'length' => '1'
		);
		$this->_conf_param['goods_insert_data']['runout'] = Array(
			'type' => parent::TYPE_INTEGER,
			'length' => '1'
		);
		$this->_conf_param['goods_insert_data']['longdesc'] = Array('type' => parent::TYPE_STRING);
		$this->_conf_param['goods_insert_data']['regdt'] = Array(
			'type' => parent::TYPE_DATETIME,
			'length' => '0, 19'
		);
		$this->_conf_param['goods_insert_data']['updatedt'] = Array(
			'type' => parent::TYPE_DATETIME,
			'length' => '0, 19'
		);

	}

	public function __destruct()
	{
		$this->_conf_param = array();
	}

	/**
	 *
	 * @param array $param
	 * @return array $result
	 */
	public function paramCheck($param, $chk_page)
	{
		$chk_result = Clib_Application::iapi('validate')->paramCheck($this->_conf_param[$chk_page], $param);

		return $chk_result;
	}

}
?>
