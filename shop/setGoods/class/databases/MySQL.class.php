<?php
/**
 * Created on 2012-07-23
 *
 * Filename	: ResultSet.class.php
 * Comment 	: DB 쿼리 값처리 class
 * Function	: 
 * History	: sf2000 by v1.0 최소작성
 * 
 **/
?>
<?php
require_once dirname(__FILE__) . "/../../../setGoods/class/_common.php";
require_once dirname(__FILE__) . "/../../../setGoods/class/_error.php";
require_once dirname(__FILE__) . "/../../../setGoods/class/databases/ResultSet.class.php";

class MySQL {
	var $connection;
	var $lastInsertId;
	var $isDebug;
	
	function MySQL($host, $id, $pw, $db) {
		$this->connection = null;
		$this->isDebug = false;
		$this->lastInsertedId = null;
		return $this->connect($host, $id, $pw, $db);
	}
	
	function connect($host, $id, $pw, $db) {
		$this->connection = @mysql_connect($host, $id, $pw) or Die(FatalErrorMsg);
		//mysql_query("set names utf8");
		if ( !is_null($this->connection) ) {
			@mysql_select_db($db, $this->connection) or $this->PrintError(801); 
		}
	}
	
	function close() {
		@mysql_close($this->connection) or $this->PrintError(802);
	}
	
	function execute($sql) {
		$this->debug($sql);
		$sql = $this->parseQuery($sql);
		if ( $res = @mysql_query($sql, $this->connection) or $this->PrintError(803) ) {
			$this->lastInsertId = mysql_insert_id($this->connection);
			@mysql_free_result($res);
			return true;
		}
		return false;		
	}
	
	###기존  사용 
	function getResultSet($sql) {
		$this->debug($sql);
		$sql = $this->parseQuery($sql);
		$rs = new ResultSet();
		$res = @mysql_query($sql, $this->connection) or $this->PrintError(803);
		while ( $row = @mysql_fetch_assoc($res) ) {
			$rs->add($row);
		}
		@mysql_free_result($res);
		return $rs;
	}
	
	function getArray($sql) {
		$this->debug($sql);
		$sql = $this->parseQuery($sql);
		$rs = array();
		$res = @mysql_query($sql, $this->connection) or $this->PrintError(803);
		while ( $row = @mysql_fetch_assoc($res) ) {
			array_push($rs, $row);
		}
		@mysql_free_result($res);
		return $rs;
	}
	
	function getResult($sql, $row, $field = 0) {
		$this->debug($sql);
		$sql = $this->parseQuery($sql);
		$res = @mysql_query($sql, $this->connection) or $this->PrintError(803);
		$ret = @mysql_result($res, $row, $field);
		@mysql_free_result($res);
		return $ret;
	}
	
	function debug($str) {
		if ( $this->isDebug ) {
			echo "Debug/MySQL: " . $str ."\n";
		}
	}
	
	function parseQuery($q) {
		// [d[field]] -> date_format
		$q = ereg_replace("\[d\[([a-zA-Z0-9_]*)\]\]", "date_format(\\1, '%Y-%m-%d %H:%i:%s') \\1", $q);
		// ....
		return $q;
	}
	
	function PrintError($errorNum) {
		if ( $this->isDebug ) {
			echo '[system]' . mysql_error($this->connection);
		}
		PrintError($errorNum);
	}
	
}
?>