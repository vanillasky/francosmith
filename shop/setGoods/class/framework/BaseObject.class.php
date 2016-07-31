<?php
/**
 * Created on 2008. 01. 26
 *
 * Filename	: BaseObject.class.php
 * Comment 	: get메소드
 * Function	: 
 * History	: sf2000 by v1.0 최소작성
 * 
 **/
?>
<?

class BaseObject {
	var $jar;
	var $fields;
	
	function EntityObject($names = null) {
		if(!is_null($names)){
			$this->initProperties($names);
		}
	}
	
	function initProperties($names){
		if(is_array($names)){
			$this->fields = $names;
			foreach($names as $val) {
				$this->jar[$val] = null; 
			}
		}
	}
	
	function init($array) {
		if(is_array($array)){
			foreach ( $array as $key => $val) {
				$this->set($key, $val);
			}
		}
	}
	
	//커스텀 초기화 
	function cinit($array) {
		if(is_array($array)){
			foreach ( $array as $key => $val) {
				$this->cset($key, $val);
			}
		}
	}

	//값을 가지고 온다
	function get($name) {
		if ( array_key_exists($name, $this->jar) ) {
			return $this->jar[$name];	
		} else {
			PrintError(101, 'get property [' . $name . '] is not exist.');
		}
	}
		
	//date값을 포멧형식으로 가지고온다.	
	function getDate($name, $format) {
		$date = strtotime($this->get($name));
		return date($format, $date);
	}
	//숫자형식으로 ,를 찍어준다.
	function getNumberFormat($name) {
		return number_format($this->get($name));
	}
	//" ,' 것의 html값을 변환해준다.
	function getHtmlCharDecode($name) {
		//return $this->get($name);
		return html_entity_decode($this->get($name), ENT_QUOTES);
	}
	//오프젝트에 셋한다.
	function set($name, $val) {
		if ( array_key_exists($name, $this->jar) ) {
			$this->jar[$name] = $val;	
		} else {
			PrintError(101, 'set property [' . $name . '] is not exist.');
		}
	}
	
	function cset($name, $val) {
			$this->jar[$name] = $val;			
	}

	//오프젝트를 비운다.
	function clear() {
		$this->initProperties($this->fields);
	}
}
?>