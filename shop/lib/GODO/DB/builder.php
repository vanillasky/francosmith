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
 * GODO_DB_builder
 *
 * SQL문을 생성
 *
 * 사용 예제:
 *
 * <code>
 *  // SQL문 생성
 *  $builder = $db->builder();
 *  echo $builder->select()->from(GD_MEMBER)->where("m_id = ?", 'blue')->toString();
 *
 *  // 레코드 조회
 *  $builder = $db->builder()->select();
 *  $builder->from(GD_MEMBER);
 *  $builder->order('regdt');
 *  $builder->limit(5);
 *  $res = $builder->query();
 * </code>
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
abstract class GODO_DB_builder {

	/**
	 * 컬럼을 감싸는 따옴표 `
	 * @var string
	 */
	protected $backtic = '`';

	/**
	 * 생성할 SQL문을 위한 데이터
	 * @var array
	 */
	protected $scheme = array();

	/**
	 * 생성할 SQL문의 동작
	 * @var string
	 */
	protected $operate = null;

	/**
	 * GODO_DB
	 * @var object
	 */
	protected $db;

	/**
	 * Construct
	 * @param GODO_DB
 	 * @return void
	 */
	public function __construct(&$db) {
		$this->db = $db;
	}

	/**
	 * 인스턴스 복제
	 * @return GODO_DB_builder
	 */
	public function __clone() {

		return $this;

	}

	/**
	 * string 캐스팅시 온전한 쿼리문을 생성하여 리턴
	 * @return string sql query string
	 */
	public function __toString() {
		return (string)$this->generate();
	}

	/**
	 * join, *join, select, update, insert, delete, replace, GODO_DB 메서드 콜백
	 * @param string $name
	 * @param mixed $arguments
	 * @return mixed
	 */
	public function __call( $name, $arguments ) {

		// join
		if ( preg_match( '/^([a-zA-Z]{0,})(join)$/', $name, $matches ) ) {

			array_unshift(
				$arguments,
				! empty( $matches[1] ) ? $matches[1] : 'inner'
			);

			return
				call_user_func_array( array($this, '_join'), $arguments );
		}
		// operate
		else if (in_array(strtolower($name), array('select','update','insert','delete','replace'))) {

			$builder = $this->_getThis(strtolower($name));

			if (sizeof($arguments) > 0) {
				foreach($arguments as $option) {
					$builder->option($option);
				}
			}

			return $builder;

		}
		// db method
		else {

			if (!in_array(strtolower($name), array('wildcard','func','expression','quote'))) {
				array_unshift(
					$arguments,
					$this->toString()
				);
			}

			return call_user_func_array( array( $this->db, $name), $arguments );

		}

	}

	/**
	 * 생성할 SQL의 동작을 설정
	 * @param string $operate
	 * @return void
	 */
	private function _setOperate( $operate ) {
		$this->operate = strtoupper($operate);
	}

	/**
	 * Builder를 생성 or 복제하여 리턴
	 * @param string $operate
	 * @return GODO_DB_builder
	 */
	private function _getThis($operate) {

		static $_this = null;

		if ($_this === null) {
			$builder = $this;
		}
		else {
			$builder = clone $this;
		}

		$builder->_setOperate( $operate );

		return $builder;

	}

	/**
	 * JOIN 절에서 사용할 데이터 설정
	 * @param string $join_type
	 * @param string $table
	 * @param string $condition
	 * @param mixed $columns [optional]
	 * @return GODO_DB_builder
	 */
	protected function _join( $join_type, $table, $condition, $columns = '*' ) {

		$table_alias = $this->_setTable( $table, $join_type, $condition );
		$this->_setColumn( $columns, $table_alias );

		return $this;

	}

	/**
	 * SQL 에서 사용할 테이블 설정
	 * @param string $table
	 * @param string $join_type [optional]
	 * @param string $join_condition [optional]
	 * @return string 테이블 별칭 (선언된 경우에만)
	 */
	protected function _setTable( $table, $join_type = false, $join_condition = false ) {

		if ( !is_array( $table ))
			$table = array(	$table 	);

		if ( sizeof( $table ) > 1)
			Core::raiseError( '한개만 댐' );

		$_tmp = array_keys( $table );
		$alias = array_shift( $_tmp );

		$_tmp = array_values( $table );
		$table = array_shift( $_tmp );

		unset($_tmp);

		$alias = !is_numeric( $alias ) ? $alias : '';

		if ( $join_type ) {

			switch ( strtoupper( $join_type ) ) {
				case 'INNER' :
					$format = 'INNER JOIN %s ON %s';
					break;
				case 'LEFT' :
					$format = 'LEFT JOIN %s ON %s';
					break;
				case 'STRAIGHT' :
					$format = 'STRAIGHT_JOIN %s ON %s';	// straight_join 키워드를 이용한 join 과 같음. 문맥상 보기 좋게 하기 위해 넣음.
					break;
				case 'NATURAL' :
					$format = 'NATURAL JOIN %s ON %s';
					break;
				case 'RIGHT' :
					$format = 'RIGHT JOIN %s ON %s';
					break;
				case 'CROSS' :
					$format = 'CROSS JOIN %s ON %s';
					break;
				default :
					Core::raiseError( $join_type . ' join type does not support.' );
					break;
			}

			if ($alias)
				$this->scheme['join'][$alias] = array(
					'type'=>$join_type,
					'condition'=>$join_condition,
					'format'=>$format
				);
			else
				$this->scheme['join'][] = array(
					'type'=>$join_type,
					'condition'=>$join_condition,
					'format'=>$format
				);

		}

		if ($alias)
			$this->scheme['table'][$alias] = $table;
		else
			$this->scheme['table'][] = $table;

		return $alias;

	}

	/**
	 * ORDER 절에서 사용할 데이터 설정
	 * @param mixed $orders
	 * @return void
	 */
	protected function _setOrder( $orders ) {

		if ( !is_array( $orders ))
			$orders = array($orders);

		foreach($orders as $order) {
			$this->scheme['order'][] = is_array( $order ) ? implode(',',$order) : $order;
		}

	}

	/**
	 * GROUP 절에서 사용할 데이터 설정
	 * @param string $groups
	 * @return void
	 */
	protected function _setGroup( $groups ) {

		if ( !is_array( $groups ))
			$groups = array($groups);

		foreach($groups as $group) {
			$_group = is_array( $group ) ? implode(',', $group) : $group;

			if (!in_array($_group, $this->scheme['group'])) {
				$this->scheme['group'][] = $_group;
			}
		}

	}

	/**
	 * LIMIT (limit, top 등 driver 별로 다름) 절에서 사용할 데이터 설정
	 * @param string $offset [optional]
	 * @param string $row_count
	 * @return void
	 */
	protected function _setLimit( $offset = 0, $row_count ) {

		$this->scheme['limit']['offset'] = $offset;
		$this->scheme['limit']['row_count'] = $row_count;

	}

	/**
	 * HAVING 절에서 사용할 데이터 설정
	 * @param string $condition
	 * @param mixed $value
	 * @param string $chain [optional]
	 * @return void
	 */
	protected function _setHaving( $condition, $value, $chain = 'and' ) {

		$this->scheme['having'][] = array(
			'condition'=>$condition,
			'value'=>$value,
			'chain'=>$chain
		);

	}

	/**
	 * WHERE 절에서 사용할 데이터 설정
	 * @param string $condition
	 * @param mixed $value
	 * @param string $chain [optional]
	 * @return void
	 */
	protected function _setWhere( $condition, $value, $chain = 'and' ) {

		$this->scheme['where'][] = array(
			'condition'=>$condition,
			'value'=>$value,
			'chain'=>$chain
		);

	}

	/**
	 * columns 절에 매핑할 데이터를 설정
	 * @param string $values
	 * @return void
	 */
	protected function _setValues($values) {

		if ( !is_array( $values ))
			$values = array( $values );

		foreach($values as $column => $value) {

			if ( !is_int($column) ) {
				$column = $this->backticQuote($column);
				$this->scheme['value'][$column] = $value;
			}
			else {
				$this->scheme['value'][] = $value;
			}
		}

	}

	/**
	 * COLUMNS 절에서 사용할 데이터 설정
	 * @param string $columns
	 * @param string [optional] $table_alias
	 * @return void
	 */
	protected function _setColumn( $columns, $table_alias = '') {

		if (empty($columns)) return;

		if ( !is_array( $columns ))
			$columns = array( $columns );

		foreach ( $columns as $alias => $column ) {

			if ( $column instanceof GODO_DB_expression ) {
				$column = $column->getExpression();
			}
			else if ( strpos( $column, '.' ) !== false || empty( $table_alias ) ) {
				// nothing to do
			}
			else {
				$column = sprintf( '%s.%s', $table_alias, $column );
			}

			$column = $this->backticQuote($column);

			if ( is_integer( $alias ) ) {
				$this->scheme['column'][] = $column;
			}
			else {
				$this->scheme['column'][$alias] = $column;
			}

		}

	}

	/**
	 * 컬럼을 quote 하여 리턴
	 * 컬럼 에만 사용해야 함
	 * @param string $var
	 * @return string
	 */
	public function backticQuote($var) {

		if (
			   empty($var)
			|| $this->backtic == ''
			|| strpos($var, '(') !== false
			|| strpos($var, ' ') !== false
			|| strpos($var, '*') !== false
		) return $var;

		if (strpos($var, '.') !== false) {
			$var = str_replace('.', '.'.$this->backtic, $var) . $this->backtic;
		}
		else {
			$var = $this->backtic . $var . $this->backtic;
		}

		return $var;

	}

	/**
	 * __toString 메서드 결과를 리턴
 	 * @return string
	 */
	public function toString() {
		return (string)$this->generate();
	}

	/**
	 * from 절 설정
 	 * @param mixed $table table name
	 * @param mixed $columns [optional] column name
	 * @return GODO_DB_builder
	 */
	public function from( $table, $columns = '*' ) {

		$table_alias = $this->_setTable( $table );
		$this->_setColumn( $columns, $table_alias );

		return $this;

	}

	/**
	 * into 절 설정
	 * @param string $table
	 * @param string $columns [optional]
	 * @return GODO_DB_builder
	 */
	public function into($table, $columns = null) {

		$table_alias = $this->_setTable( $table );
		$this->_setColumn( $columns, $table_alias );

		return $this;
	}

	/**
	 * set column clause
 	 * @param string $columns
	 * @param string $table_alias [optional]
	 * @return GODO_DB_builder
	 */
	public function columns( $columns, $table_alias = '' ) {

		$this->_setColumn( $columns, $table_alias );

		return $this;
	}

	/**
	 * set where clause
 	 * @param string $condition	 condition
	 * @param string $value [optional]
	 * @param string $chain [optional] and 또는 or
	 * @return GODO_DB_builder
	 */
	public function where( $condition, $value = false, $chain = 'AND' ) {
		$this->_setWhere( $condition, $value, $chain );
		return $this;
	}

	/**
	 * $condition 문자열 내의 placeholder 를 $value 로 채워 리턴
	 * @param string $condition
	 * @param mixed $value
	 * @return string
	 */
	public function parse($condition, $value) {
		return $this->db->fillReplaceHolder($condition, $value);
	}

	/**
	 * group 절 설정
 	 * @param string $columns group 구문
	 * @return GODO_DB_builder
	 */
	public function group( $columns ) {
		$this->_setGroup( $columns );
		return $this;
	}

	/**
	 * order 절 설정
 	 * @param mixed $columns order 구문
	 * @return GODO_DB_builder
	 */
	public function order( $columns ) {
		$this->_setOrder( $columns );
		return $this;
	}

	/**
	 * limit 절 설정
 	 * @param integer $offset 가져올 행의 시작 위치
	 * @param integer [optional] $row_count 가져올 행의 수
	 * @return GODO_DB_builder
	 */
	public function limit( $offset, $row_count = null) {
		$this->_setLimit( $offset, $row_count );
		return $this;
	}

	/**
	 * having 절 설정
 	 * @param string $condition 조건
	 * @param string $value [optional]
	 * @param string $chain [optional]
	 * @return GODO_DB_builder
	 */
	public function having( $condition, $value = false, $chain = 'AND' ) {

		$this->_setHaving( $condition, $value, $chain );

		return $this;

	}


	/**
	 * set 절 설정
 	 * @param array $values
	 * @return GODO_DB_builder
	 */
	public function set($values) {

		$this->reset('column');
		$this->reset('values');

		$columns = array_keys($values);
		$values = array_values($values);

		$this->_setColumn($columns);
		$this->_setValues($values);

		return $this;

	}

	/**
	 * 각 컬럼에 매칭할 값을 설정
	 * @param mixed $values
	 * @return GODO_DB_builder
	 */
	public function values($values) {

		$this->_setValues($values);

		return $this;
	}

	/**
	 * 특정 데이터 또는 전체 데이터를 리셋
	 * @param string $specify [optional]
	 * @return GODO_DB_builder
	 */
	public function reset($specify='') {

		if ($specify) {
			if (!empty($this->scheme[$specify])) {

				if ($specify == 'join') {
					$_keys = array_keys($this->scheme['table']);
					for ($i=0,$m=sizeof($_keys);$i<$m;$i++) {
						$_key = $_keys[$i];

						if (isset($this->scheme[$specify][$_key])) {
							unset( $this->scheme[$specify][$_key], $this->scheme['table'][$_key] );
						}
					}
				}
				else {
					$this->scheme[$specify] = array();
				}

			}
		}
		else {
			$this->scheme = array();
		}

		return $this;
	}

	/**
	 * option 절 설정
	 * @param string $option
	 * @return GODO_DB_builder
	 */
	public function option($option) {
		$this->scheme['option'][] = strtoupper($option);

		return $this;
	}

	/**
	 * union 절 설정
	 * @return GODO_DB_builder
	 */
	public function union() {

		if (func_num_args() < 2) Core::raiseError('union must be 2 sql queries.');

		$this->_setOperate( 'union' );

		$this->reset();
		$this->scheme['query'] = func_get_args();

		return $this;

	}

	/**
	 * SQL 빌더의 동작을 리턴
	 * @return string
	 */
	public function getOperate () {
		return strtoupper($this->operate);
	}

	/**
	 * $key 에 해당 절이 설정되어 있는지 체크
     * @param String $key
	 * @return boolean
	 */
	public function has($key) {

		return
			  sizeof($this->scheme[$key]) > 0
			? true
			: false;
	}

	/**
	 * 검색 에 사용할 join 문을 생성
	 * @param string $table
	 * @param string $pk
	 * @param array $columns
	 * @param string $keyword
	 * @return void
	 */
	public function search($table, $pk, $columns, $keyword) {

		static $scope = 0;

		$sc = $this->db->indexer();

		if ($q = $sc->search($table, $columns, $keyword)) {

			$scope++;

			$this->join(
				array('__search_'.$scope => sprintf('( %s )', $q))
				,sprintf('__search_%d.pk = %s', $scope, $pk)
			);

		}

	}

	/**
	 * on duplicate key update 문의 사용 여부 설정
	 * @return GODO_DB_builder
	 */
	public function duplicateupdate() {

		$this->scheme['onduplicatekeyupdate'] = true;

		return $this;
	}

	/**
	 * SQL문을 생성하여 리턴
	 * @return string SQL
	 */
	abstract protected function generate();

	/**
	 * limit (skip, top 등)절 생성 하여 리턴
     * @param boolean $row_count_only
	 * @return string
	 */
	abstract protected function getSyntax_limit($row_count_only = false);

	/**
	 * having 절 생성 하여 리턴
	 * @return string
	 */
	abstract protected function getSyntax_having();

	/**
	 * order 절 생성 하여 리턴
	 * @return string
	 */
	abstract protected function getSyntax_order();

	/**
	 * return절 생성 하여 리턴
	 * @return string
	 */
	abstract protected function getSyntax_group();

	/**
	 * column 절 생성 하여 리턴
	 * @return string
	 */
	abstract protected function getSyntax_column();

	/**
     * values 절 생성하여 리턴
	 * @return string
	 */
	abstract protected function getValueString();

	/**
	 * table 절 (, 로 연결) 생성 하여 리턴
	 * @return string
	 */
	abstract protected function getSyntax_tableList();

	/**
	 * table 절 생성 하여 리턴
     * @param boolean $single_table
	 * @return string
	 */
	abstract protected function getSyntax_table($single_table = false);

	/**
	 * SET 절 생성 하여 리턴
	 * @return string
	 */
	abstract protected function getSyntax_where();

	/**
	 * on duplicate key update 절 생성 하여 리턴
	 * @return string
	 */
	abstract protected function getSyntax_onDuplicateKeyUpdate();

	/**
	 * SET 절 생성 하여 리턴
	 * @return string
	 */
	abstract protected function getSyntax_set();

	/**
	 * 옵션 절 생성 하여 리턴
	 * @param string $operate
	 * @return string
	 */
	abstract protected function getSyntax_option($operate);

}
?>
