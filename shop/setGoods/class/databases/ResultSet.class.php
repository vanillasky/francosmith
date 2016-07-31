<?php
/**
 * Created on 2012-08-21
 *
 * Filename	: ResultSet.class.php
 * Comment 	: DB 孽府 蔼贸府 class
 * Function	: 
 * History	: sf2000 by v1.0 弥家累己
 * 
 **/
?>
<?php
class ResultSet {
	var $jar;
	var $rownum;
	
	function ResultSet() {
		return $this->init(null);
	}
	
	function init($array) {
		$this->rownum = -1;
		if ( is_null($array) ) {
			$array = array();
		}
		$this->jar = $array;
	}
	
	function add($value) {
		array_push($this->jar, $value);
	}
	
	function remove($value) {}
	
	function next() {
		$this->rownum++;
		if ( is_array($this->jar[$this->rownum])) {
			return true;
		}
		return false;
	}
	
	function size() {
		return count($this->jar);
	}
	
	function count() {
		return $this->size();
	}
	
	function get($name) {
		if ( @array_key_exists($name, $this->jar[$this->rownum]) ) {
			return $this->jar[$this->rownum][$name];	
		} else {
			PrintError(101, 'property [' . $name . '] is not exist.');
		}
	}
}
?>