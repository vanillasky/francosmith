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
 * GODO_DB_driver_godomysql_builder
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
final class GODO_DB_driver_godomysql_builder extends GODO_DB_builder {

	/**
	 * 컬럼을 감싸는 따옴표 `
	 * @var string
	 */
	protected $backtic = '`';

	/**
	 * SQL문을 생성하여 리턴
	 * @todo insert 시 on duplicate key update 처리
	 * @return string SQL
	 */
	protected function generate() {

		$query = array();

		switch ($this->operate) {

			case 'SELECT' :
				$query[] = $this->operate;
				$query[] = $this->getSyntax_option($this->operate);
				$query[] = $this->getSyntax_column();
				$query[] = sprintf('FROM %s', $this->getSyntax_table());
				$query[] = $this->getSyntax_where();
				$query[] = $this->getSyntax_group();
				$query[] = $this->getSyntax_having();
				$query[] = $this->getSyntax_order();
				$query[] = $this->getSyntax_limit();
				break;

			case 'UNION' :

				if (sizeof($this->scheme['option']) > 0) {
					foreach($this->scheme['option'] as $_option) {
						$this->scheme['query'][0]->option($_option);
					}
				}

				$query[] = implode(' UNION ', $this->scheme['query']);
				$query[] = $this->getSyntax_order();
				$query[] = $this->getSyntax_limit();
				break;

			case 'INSERT' :
				$query[] = $this->operate;
				$query[] = $this->getSyntax_option($this->operate);
				$query[] = sprintf('INTO %s', $this->getSyntax_table(true));
				if (!($_body = $this->getSyntax_set())) return false;
				$query[] = $_body;
				$query[] = $this->getSyntax_onDuplicateKeyUpdate();
				break;

			case 'UPDATE' :
				$query[] = $this->operate;
				$query[] = $this->getSyntax_option($this->operate);
				$query[] = $this->getSyntax_table();
				if (!($_body = $this->getSyntax_set())) return false;
				$query[] = $_body;
				$query[] = $this->getSyntax_where();

				if ( sizeof($this->scheme['table']) == 1 ) {
					$query[] = $this->getSyntax_order();
					$query[] = $this->getSyntax_limit(true);
				}
				break;

			case 'DELETE' :
				$query[] = $this->operate;
				$query[] = $this->getSyntax_option($this->operate);
				if ( sizeof($this->scheme['table']) > 1 ) {
					$query[] = $this->getSyntax_tableList();
				}

				$query[] = sprintf('FROM %s', $this->getSyntax_table());
				$query[] = $this->getSyntax_where();

				if ( sizeof($this->scheme['table']) == 1 ) {
					$query[] = $this->getSyntax_order();
					$query[] = $this->getSyntax_limit(true);
				}

				break;

			case 'REPLACE' :
				$query[] = $this->operate;
				$query[] = $this->getSyntax_option($this->operate);
				$query[] = sprintf('INTO %s', $this->getSyntax_table(true));
				$query[] = $this->getSyntax_set();
				break;

		}

		return implode( PHP_EOL, $query );

	}

	/**
	 * return limit clause syntax
	 * @return string limit(in mysql) clause syntax
	 */
	protected function getSyntax_limit($row_count_only = false) {

		$limit = array();

		if ( $this->scheme['limit']['offset'] )
			$limit[] = $this->scheme['limit']['offset'];

		if ( $this->scheme['limit']['row_count'] )
			$limit[] = $this->scheme['limit']['row_count'];

		if ($row_count_only)
			unset($limit[1]);

		return
			! empty( $limit )
			? sprintf( 'LIMIT %s', implode( ',', $limit ) )
			: null;

	}

	/**
	 * return having clause syntax
	 * @return string having clause syntax
	 */
	protected function getSyntax_having() {

		$_data = array();

		foreach ((array)$this->scheme['having'] as $where) {

			//if ($where['value'])
				$where['condition'] = $this->db->fillReplaceHolder($where['condition'], $where['value']);

			$_data[] = sprintf( '%s (%s)', ( sizeof( $_data ) > 0 ) ? ( $where['chain'] ? $where['chain'] : 'AND' ) : '', $where['condition'] );

		}

		return
			0 < sizeof( $_data )
			? sprintf( 'HAVING %s', implode( PHP_EOL.' ', $_data ) )
			: null;

	}

	/**
	 * return order clause syntax
	 * @return string order clause syntax
	 */
	protected function getSyntax_order() {

		return
			0 < sizeof( $this->scheme['order'] )
			? sprintf( 'ORDER BY %s', implode( ', ', $this->scheme['order'] ) )
			: null;

	}

	/**
	 * return group clause syntax
	 * @return string group clause syntax
	 */
	protected function getSyntax_group() {

		return
			0 < sizeof( $this->scheme['group'] )
			? sprintf( 'GROUP BY %s', implode( ', ', $this->scheme['group'] ) )
			: null;

	}

	/**
	 * return columns clause syntax
	 * @return string columns clause syntax
	 */
	protected function getSyntax_column() {

		$_data = array();

		foreach ((array)$this->scheme['column'] as $alias => $column) {

			if ( is_numeric( $alias ) ) {
				$_data[] = $column;
			}
			else {

				if (is_null($column)) $column = 'NULL';
				$_data[] = sprintf( '%s AS %s', $column, $alias);
			}

		}

		return implode( ', ', $_data );

	}

	/**
	 * @return string
	 */
	protected function getValueString() {

		if (is_object($this->scheme['value'])) return $this->scheme['value']->toString();

		$_data = array();

		foreach ((array)$this->scheme['value'] as $column => $value) {

			$_data[] = $this->db->quote($value);

		}

		return sprintf('VALUES (%s)', implode( ', ', $_data ) );

	}

	/**
	 * return table clause syntax
	 * @return string table clause syntax
	 */
	protected function getSyntax_tableList() {

		$tables = array();

		foreach ((array)$this->scheme['table'] as $alias => $table) {

			if ( is_numeric( $alias ) ) {
				$tables[] = $table;
			}
			else {
				$tables[] = $alias;
			}

		}

		return implode( ', ', $tables );

	}

	/**
	 * return table clause syntax
	 * @return string table clause syntax
	 */
	protected function getSyntax_table($single_table = false) {

		$tables = array();

		if ($single_table && sizeof($this->scheme['table']) > 1) Core::raiseError('this operate support only single table.');

		foreach ((array)$this->scheme['table'] as $alias => $table) {

			if ( is_numeric( $alias ) ) {
				$tables[] = $table;
			}
			else {

				if (is_object($table)) {
					$table = sprintf('(%s)', $table->toString());
				}

				$table = ! empty( $alias ) ? sprintf( '%s AS %s', $table, $alias ) : $table;

				if ( isset( $this->scheme['join'][$alias] ) ) {
					$_join = $this->scheme['join'][$alias];
					$tables[] = sprintf( $_join['format'], $table, $_join['condition'] );
				}
				else {
					$tables[] = $table;
				}
			}

		}

		return implode( PHP_EOL.' ', $tables );

	}

	/**
	 * return where clause syntax
	 * @return string where clause
	 */
	protected function getSyntax_where() {

		$_data = array();

		foreach ((array)$this->scheme['where'] as $where) {

			//if ($where['value'])
			$where['condition'] = $this->db->fillReplaceHolder($where['condition'], $where['value']);

			$_data[] = sprintf( '%s (%s)', ( sizeof( $_data ) > 0 ) ? ( $where['chain'] ? $where['chain'] : 'AND' ) : '', $where['condition'] );

		}

		return
			0 < sizeof( $_data )
			? sprintf( 'WHERE %s', implode( PHP_EOL.' ', $_data ) )
			: null;

	}

	/**
	 * return set clause
	 * @return string set clause
	 */
	protected function getSyntax_onDuplicateKeyUpdate() {

		if ($this->scheme['onduplicatekeyupdate'] !== true) return;

		$_data = array();

		foreach ((array)$this->scheme['column'] as $column) {
			$_data[] = sprintf('%s = VALUES(%s)', $column, $column);
		}

		return
			0 < sizeof( $_data )
			? sprintf( ' ON DUPLICATE KEY UPDATE %s', implode( ','.PHP_EOL, $_data ) )
			: null;
	}

	/**
	 * return set clause
	 * @return string set clause
	 */
	protected function getSyntax_set() {

		$_data = array();

		foreach ((array)$this->scheme['column'] as $seq => $column) {

			$value = $this->scheme['value'][$seq];

			if ( $value instanceof GODO_DB_expression) {
				$value = $value->getExpression();
			}
			else {
				$value = $this->db->quote($value);
			}

			$_data[] = sprintf('%s = %s', $column, $value);
		}

		return
			0 < sizeof( $_data )
			? sprintf( 'SET %s', implode( ','.PHP_EOL, $_data ) )
			: null;
	}

	protected function getSyntax_option($operate) {

		$_data = array();

		switch ($operate) {
			case 'SELECT':
				$_available = array('ALL','DISTINCT','DISTINCTROW','HIGH_PRIORITY','STRAIGHT_JOIN','SQL_SMALL_RESULT','SQL_BIG_RESULT','SQL_BUFFER_RESULT','SQL_CACHE','SQL_NO_CACHE','SQL_CALC_FOUND_ROWS');
				break;

			case 'UPDATE':
				$_available = array('LOW_PRIORITY','IGNORE');
				break;

			case 'INSERT':
				$_available = array('LOW_PRIORITY','DELAYED','HIGH_PRIORITY','IGNORE');
				break;

			case 'DELETE':
				$_available = array('LOW_PRIORITY','QUICK','IGNORE');
				break;

			case 'REPLACE':
				$_available = array('LOW_PRIORITY','DELAYED');
				break;

			default:
				return '';
		}

		foreach ((array)$this->scheme['option'] as $option) {
			if (in_array($option, $_available) && !in_array($option, $_data)) $_data[] = $option;
		}

		return implode(' ', $_data);
	}

} //

?>
