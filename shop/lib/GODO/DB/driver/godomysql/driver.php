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
 * GODO_DB_driver_godomysql_driver
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
final class GODO_DB_driver_godomysql_driver
								extends		GODO_DB_driver
								implements	GODO_DB_interface_driver {

	protected $name = 'godomysql';

	public function getName() {
		return $this->name;
	}

	function begin() {

		if ($this->transaction === true) {
			return;
		}

		if ($this->query('START TRANSACTION')) {
			$this->transaction = true;
		}

		return true;
	}

	function rollback() {
		if ($this->query('ROLLBACK')) {
			$this->transaction = false;
		}
		return true;
	}

	function commit() {
		if ($this->query('COMMIT')) {
			$this->transaction = false;
		}
		return true;
	}

	function connect($server) {

		if ($dbconn = mysql_connect($server['hostname'], $server['username'], $server['password'], true)) {
			mysql_select_db($server['database']);

			// ex) utf-8 to utf8, euc-kr to euckr
			$charset = preg_replace('/[^a-zA-Z0-9]/', '', $server['charset']);
			mysql_query('set names '.$charset);
		}
		else {

			Core::raiseError('DB connection fail.');

		}

		return $dbconn;

	}

	function disconnect($dbconn) {
		return @mysql_close($dbconn);
	}

	function escape($var) {

		if (is_int($var)) {
			return $var;
		}
		else {
			$var = str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $var);
			return $var;
		}

	}

	function desc($table_name) {

		$sql = "SHOW COLUMNS FROM ".$table_name;

		$stmt = $this->prepare($sql);
		$stmt->execute();

		$result = array();
		foreach ($stmt as $row) {
			$result[] = $row['Field'];
		}

		return $result;

	}

	function lastID() {
		return mysql_insert_id($this->dbconn);
	}

	function errorCode() {

		if (!is_null($this->dbconn))
			return mysql_errno($this->dbconn);
		else
			return false;

	}

	function errorInfo() {

		if (!is_null($this->dbconn))
			return mysql_error($this->dbconn);
		else
			return false;

	}

	/* ----------------------
	 * old db class compatible.
	 * --------------------*/

	var $count;
	var $page_number = 10;
	var $replaceNum;
	var $replaceArgs;

	function fetch($res,$mode=0)
	{
		if (!is_resource($res)) $res = $this->query($res);
		return (!$mode) ? @mysql_fetch_array($res) : @mysql_fetch_assoc($res);
	}

	function count_($result)
	{
		if(is_resource($result))$rows = mysql_num_rows($result);
		if ($rows !== null) return $rows;
	}

	function tableCheck($tablename)
	{
		$tableQuery	= "show tables like '".$tablename."'";
		if( $this->count_($this->query($tableQuery)) >= 1 ){
			return true;
		}else{
			return false;
		}
	}

	function _escape($var) {
		return $this->escape($var);
	}

	function _query_print($query) {
		$argList = func_get_args();
		array_shift($argList);
		$this->replaceNum=0;
		$this->replaceArgs=$argList;
		$query = preg_replace_callback('/\[(i|d|s|c|cv|vs|v)\]/',array(&$this,'_queryReplace'), $query);
		return $query;
	}

	function _query($query) {
		return $this->query($query);
	}

	function _last_insert_id() {
		return $this->lastID();
	}

	function _select($query) {
		$this->getDBconn();
		$result = $this->query($query);
		if(!$result) {
			return false;
		}
		$arResult=array();
		while ($row = mysql_fetch_assoc($result)) {
			$arResult[]=$row;
		}
		return $arResult;
	}

	function _select_page($number,$page,$query) {
		$start = ($page-1)*$number;
		$query= trim($query)." limit $start , $number";

		if(!preg_match('/SQL_CALC_FOUND_ROWS/',$query)) {
			$query = preg_replace("/^select/i","select SQL_CALC_FOUND_ROWS",$query);
		}

		if(!($result = $this->query($query))) {
			return false;
		}

		if(!($c_result = $this->query("SELECT FOUND_ROWS()")))
		{
			return false;
		}
		list($totalcount) = mysql_fetch_row($c_result);

		return $this->__paging($result,$totalcount,$number,$page);
	}

	function _select_manual_page($number,$page,$totalcount,$query) {
		$start = ($page-1)*$number;
		$query= trim($query)." limit $start , $number";
		if(!preg_match("/^select/i",$query)) {
			return false;
		}

		if(!($result = $this->query($query))) {
			return false;
		}

		return $this->__paging($result,$totalcount,$number,$page);
	}

	function __paging($result,$totalcount,$number,$page) {
		$start = ($page-1)*$number;
		$ar_return['record'] = array();
		$count=1;
		while($row = mysql_fetch_assoc($result))
		{
			$row['_no'] =$start+$count;
			$row['_rno'] =$totalcount-($start+$count)+1;
			$ar_return['record'][] = $row;
			$count++;
		}

		if($totalcount%$number)
			$totalpage = (int)($totalcount/$number)+1;
		else
			$totalpage = $totalcount/$number;

		$step = ceil($page/$this->page_number);

		$ar_return['page']=array(
			'totalpage'=>$totalpage,
			'totalcount'=>$totalcount,
			'nowpage'=>$page,
			'page'=>array(),
			'next'=>false,
			'prev'=>false,
			'last'=>false,
			'first'=>false
		);

		if($step*$this->page_number<$totalpage) $ar_return['page']['next']=$step*$this->page_number+1;
		if($step!=1) $ar_return['page']['prev']=($step-1)*$this->page_number;

		if($ar_return['page']['prev']) $ar_return['page']['first']=1;
		if($ar_return['page']['next']) $ar_return['page']['last']=$totalpage;

		if($ar_return['page']['next']) $count=$this->page_number;
		else {
			if($totalpage) $count=$totalpage%$this->page_number ? $totalpage%$this->page_number : $this->page_number;
			else $count=0;
		}

		$loop_start = ($step-1)*$this->page_number+1;
		for($i=0;$i<$count;$i++)
		{
			$ar_return['page']['page'][$i]=$loop_start+$i;
		}

		return $ar_return;
	}

	function _queryReplace($matches) {
		if($matches[1]=='i') {
			$result = (int)$this->replaceArgs[$this->replaceNum];
		}
		elseif($matches[1]=='d') {
			$result = (float)$this->replaceArgs[$this->replaceNum];
		}
		elseif($matches[1]=='s') {
			if(!is_scalar($this->replaceArgs[$this->replaceNum])) {
				die('query_error');
			}
			$result = '"'.$this->escape($this->replaceArgs[$this->replaceNum]).'"';
		}
		elseif($matches[1]=='c') {
			$cols = &$this->replaceArgs[$this->replaceNum];
			if(!(is_array($cols) && count($cols))) {
				die('query_error');
			}
			foreach($cols as $eachCol) {
				if(!preg_match("/[_a-zA-Z0-9-]+/",$eachCol)) {
					die('query_error');
				}
			}
			$result = '('.implode(",",$cols).')';
		}
		elseif($matches[1]=='v') {
			$values = &$this->replaceArgs[$this->replaceNum];
			if(!(is_array($values) && count($values))) {
				die('fff');
			}
			foreach($values as $k=>$eachValue) {
				if(is_null($eachValue)) {
					$values[$k]='null';
				}
				else {
					$values[$k]='"'.$this->escape($eachValue).'"';
				}

			}
			$result = '('.implode(",",$values).')';
		}
		elseif($matches[1]=='vs') {
			$values = &$this->replaceArgs[$this->replaceNum];
			if(!(is_array($values) && count($values))) {
				die('query_error');
			}
			$arRecord=array();
			foreach($values as $eachValue) {
				foreach($eachValue as $k=>$eachElement) {
					if(is_null($eachElement)) {
						$eachValue[$k]='null';
					}
					else {
						$eachValue[$k]='"'.$this->escape($eachElement).'"';
					}

				}
				$arRecord[]='('.implode(",",$eachValue).')';
			}
			$result = implode(',',$arRecord);
		}
		elseif($matches[1]=='cv') {
			$colValues = &$this->replaceArgs[$this->replaceNum];
			if(!(is_array($colValues) && count($colValues))) {
				die('query_error');
			}
			$arImplode=array();
			foreach($colValues as $eachCol=>$eachValue) {
				if(is_null($eachValue)) {
					$arImplode[]= $eachCol.'=null';
				}
				else {
					$arImplode[]= $eachCol.'="'.$this->escape($eachValue).'"';
				}

			}
			$result = implode(",",$arImplode);
		}
		$this->replaceNum++;
		return $result;
	}

	function close()
	{
		$ret = @mysql_close($this->dbconn);
		$this->dbconn = null;
		return $ret;
	}

	function error($err)
	{
		if($this->err_report){
			//msg("정상적인 요청이 아니거나 DB에 문제가 있습니다",-1);
			echo "
			<div style='background-color:#f7f7f7;padding:2'>
			<table width=100% border=1 bordercolor='#cccccc' style='border-collapse:collapse;font:9pt tahoma'>
			<col width=100 style='padding-right:10;text-align:right;font-weight:bold'><col style='padding:3 0 3 10'>
			<tr><td>error</td><td>".mysql_error()."</td></tr>
			";
			foreach ($err as $k=>$v) echo "<tr><td>$k</td><td>$v</td></tr>";
			echo "</table></div>";
			//exit();
		}
	}

	function viewLog()
	{
		echo "
		<table width=800 border=1 bordercolor='#cccccc' style='border-collapse:collapse;font:8pt tahoma'>
		<tr bgcolor='#f7f7f7'>
			<th width=40 nowrap>no</th>
			<th width=100%>query</th>
			<th width=80 nowrap>time</th>
		</tr>
		<col align=center><col style='padding-left:5'><col align=center>
		";
		foreach ($this->log as $k=>$v){
			echo "
			<tr>
				<td>".++$idx."</td>
				<td>$v</td>
				<td>{$this->time[$k]}</td>
			</tr>
			";
		}
		echo "
		<tr bgcolor='#f7f7f7'>
			<td>total</td>
			<td></td>
			<td>".array_sum($this->time)."</td>
		</tr>
		</table>
		";
	}

	function free($rs) {
		if (is_resource($rs)) mysql_free_result($rs);
	}

	function affected() {
		return mysql_affected_rows($this->dbconn);
	}

	// select 쿼리의 explain 정보 출력
	function explain($query) {
		$query = trim($query);
		if (!preg_match('/^select/i',$query)) return;

		$res = $this->query('EXPLAIN '.$query);

		echo '<pre class="prettyprint">'.GODO_DB_formatter::format($query, false).'</pre>';

		echo '
		<table class="table table-bordered">
		<thead>
		<tr>
			<th>ID</th>
			<th>SELECT_TYPE</th>
			<th>TABLE</th>
			<th>TYPE</th>
			<th>POSSIBLE_KEYS</th>
			<th>KEY</th>
			<th>KEY_LEN</th>
			<th>REF</th>
			<th>ROWS</th>
			<th>EXTRA</th>
		</tr>
		</thead>
		<tbody>
		';

		while ($row = $this->fetch($res,1)) {
			if ($row['type'] == 'ALL') $color = 'error';
			else $color = '';
			echo "
			<tr class='$color'>
				<td>$row[id]</td>
				<td>$row[select_type]</td>
				<td>$row[table]</td>
				<td>$row[type]</td>
				<td>$row[possible_keys]</td>
				<td>$row[key]</td>
				<td>$row[key_len]</td>
				<td>$row[ref]</td>
				<td>".number_format($row[rows])."</td>
				<td>$row[Extra]</td>
			</tr>
			";
		}
		echo "
		</tbody>
		</table>
		";
	}
}
?>
