<?php
/**
 * Created on 2012-07-23
 *
 * Filename	: BaseDAO.class.php
 * Comment 	: 기본class 파일
 * Function	: 
 * History	: sf2000 by v1.0 최소작성
 * 
 **/
?>
<?
require_once dirname(__FILE__) . "/../../../setGoods/class/lib/Basic.lib.php";
require_once dirname(__FILE__) . "/../../../setGoods/class/databases/Database.class.php";

class BaseDAO {
	var $db;
	var $isDebug;
	var $tablename;
	var $fields;
	var $object;
	var $total;
	var $lastInsertId;
	//기본 객체를 생성한다.
	function BaseDAO() {
		$this->db = new Database();
		$this->isDebug = false;
		$this->total = 0;
		$this->lastInsertId = 0;
		$this->fields = '*';
	}
	//쿼리 디버그를 보여준다.
	function debug($str) {
		if ( $this->isDebug ) {
			echo "Debug/BaseDAO: " . $str ."\n";
		}
	}
	//인풋한다
	function add($fields) {
		$sql = 'insert into ' . $this->tablename;
		foreach ( $fields as $val  ) {
			$sqlFields .= $val . ',';
			if($this->object->get($val) == 'now()'){
				$sqlValues .= "now(),";
			} else {
				$sqlValues .= "'" . @htmlspecialchars($this->object->get($val), ENT_QUOTES) . "',";
			}			
		}
		$sqlFields = substr($sqlFields, 0, -1);
		$sqlValues = substr($sqlValues, 0, -1);
		$sql .= '(' . $sqlFields . ')';
		$sql .= ' values (' . $sqlValues . ')';
		
		$this->debug($sql);
		$ret = $this->db->execute($sql);
		$this->lastInsertId = $this->db->lastInsertId;
		//$this->object->set('idx', $this->lastInsertId);
		return $ret;
	}
	//수정한다. $where 가 없으면 idx값으로 수정한다.
	function modify($fields, $where = null) {
		$sql = 'update ' . $this->tablename .' set ';
		$sqlSets = '';
		foreach ( $fields as $val  ) {
			$sqlSets .= $val . ' = ';
			if($this->object->get($val) == 'now()'){
				$sqlSets .= "now(),";
			} else {
				$sqlSets .= "'" . htmlspecialchars($this->object->get($val), ENT_QUOTES) . "',";
			}			
		}
		$sqlSets = substr($sqlSets, 0, -1);
		$sql .= $sqlSets;
		if(!is_null($where)){
			$sql .= ' where ' . $where;
		} else {
			$sql .= ' where idx =' . $this->object->get('idx');
		}
		
		$this->debug($sql);
		$ret = $this->db->execute($sql);
		return $ret;
	}
	//삭제 한다. $where 가 없으면 idx값으로 지운다.
	function delete($where = null) {
		$sql = 'delete from ' . $this->tablename;
		if(!is_null($where)){
			$sql .= ' where ' . $where;
		} else {
			$sql .= ' where idx =' . $this->object->get('idx');
		}
		
		$this->debug($sql);
		$ret = $this->db->execute($sql);
		return $ret;
	}

	// 로우를 조회한다.
	function find($where, $orderby='') {
		$sql = 'select ' . $this->fields . ' from ' . $this->tablename;
		$sql .= ' where ' . $where;
		if($orderby != ''){
			$sql .= ' order by ' . $orderby;
		}
		$sql .= ' limit 1';
		$this->debug($sql);
		
		$rows = $this->db->getArray($sql);
		
		if(!is_null($this->object)){
			$this->object->clear();
			$this->object->init($rows[0]);
			return $this->object;	
		}
	}
	//리스트를 가지고 온다. 	
	function getList($page = 0, $listNum = 0, $orderby = null, $where = null){
		if ( $page > 0 && $listNum > 0 ) {
			$sql = "select {$this->fields} from " . $this->tablename;
			$sql .= is_null($where) || ($where == '') ? '' : ' where ' . $where;
			$sql .= is_null($orderby) || ($orderby == '') ? '' : ' order by ' . $orderby;
			$sql .= " limit ".(($page-1) * $listNum).",".$listNum;
		} else {
			$sql = "select {$this->fields} from " . $this->tablename;
			$sql .= is_null($where) || ($where == '') ? '' : ' where ' . $where;
			$sql .= is_null($orderby) || ($orderby == '') ? '' : ' order by ' . $orderby;
		}
		$this->debug($sql);
		
		$rows = $this->db->getArray($sql);
		
		$retrunArray = array();
		$className = get_class($this->object);
		for ($i=0; $i<count($rows); $i++ ) {
			eval("\$tempObejct = new {$className}();");
			$tempObejct->clear();
			$tempObejct->init($rows[$i]);
			array_push($retrunArray, $tempObejct);
		}

		
		return $retrunArray;
	}
	
	//정규화되지 않은 쿼리에 대한 커넥션 join 등
	function getCustemList($sql){
		
		$this->debug($sql);
		
		$rows = $this->db->getArray($sql);
		
		$retrunArray = array();
		$className = get_class($this->object);
		for ($i=0; $i<count($rows); $i++ ) {
			eval("\$tempObejct = new {$className}();");
			$tempObejct->clear();
			$tempObejct->cinit($rows[$i]);
			array_push($retrunArray, $tempObejct);
		}
		return $retrunArray;
	}

	//전체 수를 조회한다.
	function getTotal($where = null){
		if ( $this->total == 0 ) {
			
			$sql = 'select count(*)as cnt from ' . $this->tablename;
			$sql .= is_null($where) || ($where == '') ? '' : ' where ' . $where;
			$this->debug($sql);
			
			return $this->db->getResult($sql,0,0);
		} else {
			return $this->total;
		}
	}
	

	### template_ 사용을 위한 컨버터
	function jarArrayConverter($objs,$type='',$rand = ''){	
		if($type == 'L'){
			foreach ( $objs as $obj) {
				while (list($key,$value) = each($obj)) { 
					if($key == 'jar'){
						$cvtObjs[] = $value;					
					}
				} 
			}
			
			### 넘어온요소를 랜덤하게 배열한다.
			if($rand == '1'){
				if(count($cvtObjs) > 2){
					shuffle($cvtObjs);
				}
			}			
			
		}else{
			foreach ( $objs as $key=>$value) {
				if($key == 'jar'){
					$cvtObjs[] = $value;					
				}
			} 
		}
		return $cvtObjs;
	}


	//페이징을 만든다.
	function getPaging($pg, $jp, $total, $ls, $options) {
		return getPaging($pg, $jp, $total, $ls, $options);
	}
	//사이트 페이징을 만든다.
	function getMainPaging($pg, $jp, $total, $ls, $options) {
		return getMainPaging($pg, $jp, $total, $ls, $options);
	}
	//받은 인자값을 오브잭트화한다
	function setObject($object) {
		$this->object = $object;
	}
    //오브젝트를 가지고온다
	function getObject() {
		return $this->object;
	}
	//디비를 닫는다.
	function close() {
		$this->db->close();
	}
	//정렬 방식을 변경한다.
	function getOrderByReverse($s) {
		if ( stripos($s, 'desc') > 0 ) {
			$s = str_ireplace('desc', 'asc', $s);
		} else if ( !stripos($s, 'asc') ) {
			$s = str_ireplace('asc', 'desc', $s);
		} else {
			$s .= ' desc';
		}
		
		return $s;
	}
	//오브젝트로 넘어온 필드값을 정렬한다.
	function initFields() {
		if ( !is_null($this->object) ) {
			foreach($this->object->fields as $val) {
				$fields .= $val . ','; 
			}
			$this->fields = substr($fields, 0, -1);
		}
	}

	function menuName($dir){
		$nemu_dir = explode('/',$dir);
		$menu_name[$nemu_dir[count($nemu_dir)-1]] = 'id="current"';

		return $menu_name;
	}
}
?>