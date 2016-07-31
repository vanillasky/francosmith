<?php
/**
 * Created on 2008. 01. 26
 *
 * Filename	: BaseObject.class.php
 * Comment 	: Request메소드
 * Function	: 
 * History	: sf2000 by v1.0 최소작성
 * 
 **/
?>
<?
class Request {
	var $jar;
	function Request($m = 'ALL') {
		switch ( strtoupper($m) ) {
			case 'GET' : $this->jar = $_GET; break;
			case 'POST' : $this->jar = $_POST; break;
			case 'COOKIE' : $this->jar = $_COOKIE; break;
			case 'FILES' : $this->jar = $_FILES; break;
			default : $this->jar = $_REQUEST;
		}
	}

	function set($name, $value) {
		$this->jar[$name] = $value;
	}

	function get($name, $def = '') {
		return (String) ($this->jar[$name] == '') ? $def : $this->jar[$name];
	}
	
	function getFile($name,$def = '') {
		return $def == '' ? $this->jar[$name]['name'] : $this->jar[$name]['tmp_name'];
	}

	function getFileSize($name) {
		return $this->jar[$name]['size'];
	}

	function getString($name, $def = '') {
		return $this->get($name, $def);
	}

	function getSingleQuoteString($name, $def = '') {
		return "'" . $this->get($name, $def) . "'";
	}

	function getDoubleQuoteString($name, $def = '') {
		return "\"" . $this->get($name, $def) . "\"";
	}

	function getUrlEncodeString($name, $def = '') {
		return (String) urlencode(((String) $this->jar[$name] == '') ? $def : $this->jar[$name]);
	}

	function getInt($name, $def = 0) {
		return (int) ($this->jar[$name] == 0) ? $def : $this->jar[$name];
	}

	function getFloat($name) {
		return (float) ($this->jar[$name] == 0) ? $def : $this->jar[$name];
	}

	function getBoolean($name) {
		$t = strtoupper((String) ($this->jar[$name]));
		return ($t == '1' || $t == 'Y' || $t == 'YES' || $t == 'TRUE') ? 1 : 0;
	}

	function getStringArray($name) {
		return $this->jar[$name];
	}

	function getIntArray($name) {
		$t =& $this->jar[$name];
		if ( !is_array($t) ) return null;
		
		$r = array();
		for ( $i = 0; $i < count($t); $i++ ) {
			array_push($r, (int) $t[$i]);
		}

		return $r;
	}

	function getBooleanArray($name) {
		$t =& $this->jar[$name];
		if ( !is_array($t) ) return null;
		
		$r = array();
		for ( $i = 0; $i < count($t); $i++ ) {
			$t2 = strtoupper($t[$i]);
			array_push($r, ($t2 == '1' || $t2 == 'Y' || $t2 == 'YES') ? 1 : 0);
		}

		return $r;
	}

	function getParameter($names) {
		if ( !is_array($names) || count($names) < 1 ) return '';

		while ( list($k, $v) = each($names) ) { 
			if ( $this->get($v) != '' ) $r .= "&" . $v . "=" . $this->get($v);
		}

		return $r;
	}
	
	
}
?>