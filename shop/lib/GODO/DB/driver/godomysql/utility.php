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
 * GODO_DB_driver_godomysql_utility
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
final class GODO_DB_driver_godomysql_utility
								extends		GODO_DB_utility
								implements	GODO_DB_interface_utility
{

	/**
	 *
	 * @param GODO_DB_builder $builder
	 * @return
	 */
	function getTotalCount(GODO_DB_builder $builder) {

		if ($builder->getOperate() == 'SELECT') {

			$_builder = clone $builder;
			$_builder->reset('column')->reset('order')->reset('limit');

			if ($_builder->has('group')) {
				$_builder->columns(array($this->db->expression('1 AS __CNT__')));
				$_cnt_sql = "SELECT COUNT(__COUNT_SQL__.__CNT__) FROM (" . $_builder->toString() . ") AS __COUNT_SQL__";
			}
			else {
				$_cnt_sql = $_builder->columns(array($this->db->expression('COUNT(*) AS __CNT__')))->toString();
			}

			list($total_count) = $this->db->fetch( $_cnt_sql );

		}
		else if ($builder->getOperate() == 'UNION') {

			list($total_count) = $this->db->fetch("SELECT FOUND_ROWS()");

		}

		return $total_count;

	}

	/**
	 * parse Builder object and return result
 	 * @param GODO_DB_builder $builder
	 * @param integer $page_size [optional]
	 * @param integer $page [optional]
	 * @return GODO_DB_statement
	 */
	public function getPaging(GODO_DB_builder $builder, $page_size=20, $page=1) {

		$offset = ($page - 1) * $page_size;

		if ($page > 1) {
			$builder->limit($offset, $page_size);
		}
		else {
			$builder->limit($page_size);
		}

		if ($builder->getOperate() == 'UNION') {
			$builder->option('SQL_CALC_FOUND_ROWS');
		}

		$sql = $builder->toString();

		$stmt = $this->db->prepare($sql);
		$stmt->execute();

		$total_count = $this->getTotalCount($builder);

		if ($total_count % $page_size)
			$totalpage = (int) ($total_count / $page_size) + 1;
		else
			$totalpage = $total_count / $page_size;

		// ÆäÀÌÂ¡
		$pg = Core::loader('Page',$page,$page_size);
		$pg->recode['total'] = $total_count;
		$pg->page['total'] = $totalpage;
		$pg->idx = $pg->recode[total] - $pg->recode[start];
		$pg->setNavi($tpl='');
		$pg->query = $sql;

		$stmt->page = clone $pg;

		return $stmt;

		/*
		$result = array();
		$result['page'] = $pg;

		// °á°ú
		$count = 0;	$result['record'] = array();
		while ( $row = $this->db->fetch( $rs , 1)) {

			$count++;

			$row['_no'] = $offset + $count;
			$row['_rno'] = $total_count - ($offset + $count) + 1;

			$result['record'][] = $row;
		}

		return $result;
		*/

	}

	public function getAll(GODO_DB_builder $builder) {

		$stmt = $this->db->prepare($builder->toString());
		$stmt->execute();

		return $stmt;

	}

	public function getOne(GODO_DB_builder $builder) {

		$builder->limit(1);

		$stmt = $this->db->prepare($builder->toString());
		$stmt->execute();

		return $stmt;

	}
}
?>
