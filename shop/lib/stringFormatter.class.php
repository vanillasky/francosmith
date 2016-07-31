<?
/**
	2011-01-25 by x-ta-c
	- 문자열을 특정 포맷의 문자열로 체크,변환하여 리턴.

	usage :
	$formatter = new stringFormatter();

	$formatter->get('0104-55-511-16','dial','-');
	-> 010-4555-1116 의 값을 리턴함

	$formatter->get('01000004-55-511-16','dial','-');
	-> false 를 리턴함

 */
class stringFormatter {

	var $string;
	var $method;
	var $glue;

	// construct
	function stringFormatter() {

		$this->string = '';
		$this->method = '';
		$this->glue = '';

		return $this;
	}


	/* public */ function get($string='', $method='',$glue='') {
		$this->_set($string, $method, $glue);

		return $this->_get();
	}




	/* private */ function _set($string='', $method='',$glue='') {
		$this->method = strtolower($method);
		$this->string = $string;
		$this->glue = $glue;

		$this->_strip();

		return $this;
	}


	/* private */ function _get() {

		if ($this->_validate()) {
			return $this->_format();
		}
		else {
			return false;
		}
	}

	/* private */ function _strip() {

		switch($this->method) {
			case "dial":
				$regExp = "/[^0-9]/";
				break;
			default :
				return $this->string;
				break;
		}

		$this->string = preg_replace($regExp,'',$this->string);

		return $this;

	}	// _strip


	/* private */ function _validate() {

		switch($this->method) {
			case "dial":
				$regExp = "/^([0]{1}[0-9]{1,2})-?([1-9]{1}[0-9]{2,3})-?([0-9]{4})$/";
				break;
			case "email":
				$regExp = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/";
				break;
			case "domain":
				$regExp = "/^.+(\.[a-zA-Z]{2,3})$/";
				break;
			default :
				return false;
				break;
		}

		return (preg_match($regExp, $this->string)) ? true : false;

	} // _validate


	/* private */ function _format() {

		switch($this->method) {
			case "dial":
				$_result = array();

				switch (strlen($this->string)) {
					case 11:
						$_result['area'] = substr($this->string,0,3);
						$_result['prefix'] = substr($this->string,3,4);
						$_result['suffix'] = substr($this->string,-4);
						break;
					case 10:
						$_result['area'] = (substr($this->string,0,2) == '02') ? substr($this->string,0,2) : substr($this->string,0,3);
						$_result['prefix'] = (substr($this->string,0,2) == '02') ? substr($this->string,2,4) : substr($this->string,3,3);
						$_result['suffix'] = substr($this->string,-4);
						break;
					case 9:
						$_result['area'] = substr($this->string,0,2);
						$_result['prefix'] = substr($this->string,2,3);
						$_result['suffix'] = substr($this->string,-4);
						break;
				}
				return implode($this->glue,$_result);
			// case 'asdasda':
			default :
				return $this->string;

		}

		return;
	} // _format

}	// eof class;

?>