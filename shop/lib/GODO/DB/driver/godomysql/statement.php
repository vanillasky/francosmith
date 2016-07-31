<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */

/**
 * GODO_DB_driver_godomysql_statement
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
final class GODO_DB_driver_godomysql_statement
											extends		GODO_DB_statement
											implements	GODO_DB_interface_statement, Iterator
											{

	// statement
	protected $name = 'godomysql';

	public function setDbconn($dbconn) {

		if (is_resource($dbconn)) $this->dbconn = $dbconn;
	}

	public function getResultResource() {
		return $this->rs;
	}

	function escape($var) {

		if (is_numeric($var)) {
			return $var;
		}
		else {
			$var = str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $var);

			return $var;
		}

	}

	function fetchAll($fetchstyle = 0) {

		$this->dataSeek(0);

		$result = array();

		while ($row = $this->fetch($fetchstyle)) {
			$result[] = $row;
		}

		return $result;
	}

	function rowCount() {

		return is_resource($this->rs) ? mysql_num_rows($this->rs) : false;
	}

	function dataSeek($field_offset) {
		if (is_array($this->rs)) {
			return isset($this->rs[$field_offset]);
		}

		return @mysql_data_seek($this->rs, $field_offset);

	}

	function fetchArray($rs = null) {

		if (is_array($this->rs)) {
			return $this->rs[$field_offset];
		}

		return
			  $rs !== null
			? mysql_fetch_array($rs)
			: mysql_fetch_array($this->rs);

	}

	function fetchAssoc($rs = null) {

		if (is_array($this->rs)) {
			return $this->rs[$field_offset];
		}

		return
			  $rs !== null
			? mysql_fetch_assoc($rs)
			: mysql_fetch_assoc($this->rs);
	}

	function fetchObject($rs = null) {
		return
			  $rs !== null
			? mysql_fetch_object($rs)
			: mysql_fetch_object($this->rs);
	}

	public function fetch( $fetchstyle = 0 ) {

		switch ( (int)$fetchstyle ) {
			case 1:
				return $this->fetchAssoc();
			default:
				return $this->fetchArray();
		}

	}

	function query($query) {
		return mysql_query($query, $this->dbconn);
	}

	function lastID() {
		return mysql_insert_id($this->dbconn);
	}

	// iterater
    private $position = 0;

    function rewind() {
        $this->position = 0;
    }

    function current() {
		$this->dataSeek($this->position);
        return $this->fetchAssoc();
    }

    function key() {
        return $this->position;
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return $this->dataSeek($this->position);
    }

}
?>
