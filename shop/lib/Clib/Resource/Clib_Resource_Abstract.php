<?php
abstract class Clib_Resource_Abstract
{

	/**
	 * @var
	 */
	protected $filters = array();

	/**
	 * @var
	 */
	protected $db = null;

	public function __construct()
	{
		$this->db = Clib_Application::database();
	}

	public function resetFilter()
	{
		$this->filters = array();
	}

	/**
	 *
	 * @param object $column
	 * @param object $value
	 * @param object $chain [optional]
	 * @return
	 */
	public function addFilter($column, $value, $chain = 'and')
	{
		$filter = array(
			'column' => $column,
			'value' => $value,
			'chain' => $type
		);

		$this->filters[] = $filter;

		return $this;
	}

	/**
	 *
	 * @param object $column
	 * @return
	 */
	public function getFilter($column = null)
	{
		if (empty($column)) {
			return $this->filters;
		}

		if (is_array($column)) {
			$result = array();
			foreach ($this->filters as $filter) {
				if (in_array($filter['column'], $column)) {
					$result[] = $filter;
				}
			}
			return $result;
		}

		foreach ($this->filters as $filter) {
			if ($filter['column'] === $column) {
				return $filter;
			}
		}
	}

	public function getSqlBuilder($operate = 'select')
	{
		$builder = $this->db->builder()->reset();
		if ($operate) {
			$builder = call_user_func(array(
				$builder,
				$operate
			));
		}
		return $builder;
	}

	public function getColumns($tableName, $tableNameAlias = null)
	{
		if (is_null($tableNameAlias)) {
			$tableNameAlias = Clib_Application::getAlias($tableName);
		}

		$columns = array();

		foreach ($this->_getColumnsFromDb($tableName) as $column) {
			//$columns[] = $column;
			$columns[] = "`{$tableNameAlias}`.`{$column}` AS `{$tableNameAlias}.{$column}`";
			// `(backtic);
		}

		return $columns;

	}

	private function _getColumnsFromDb($tableName)
	{
		$rs = $this->db->query("SHOW COLUMNS FROM `$tableName`");

		$columns = array();

		while ($row = $this->db->fetch($rs, 1)) {
			$columns[] = $row['Field'];
		}

		return $columns;

	}

	public function loadById(&$object, $id, $columns = null)
	{
		$table = $object->getTableName();
		$alias = Clib_Application::getAlias($object->getClassName());
		//$columns= $this->getColumns($table, $alias);
		$format = sprintf('`%s`.`%s` = ?', $alias, $object->getIdColumnName());

		$select = $this->getSqlBuilder();
		$select->from(array($alias => $table));
		$select->where($format, $id);

		// 선택 컬럼
		// @todo : 재 구현
		/*if (!is_null($columns)) {
		 if (!is_array($columns)) {
		 $columns = array($columns);
		 }

		 if (!in_array($object->getIdColumnName(), $columns)) {
		 array_push($columns, $object->getIdColumnName());
		 }

		 $select->reset('column')->columns($columns);
		 }/**/

		// filter;
		foreach ($this->getFilter() as $filter) {
			$select->where($filter['column'], $filter['value'], $filter['chain']);
		}

		// retrive data;
		if ($data = $select->fetch(1)) {

			$object->setData($data);

			$object->setOriginalData();
			$object->setLoaded(true);
			$object->setChanged(false);

		}
		else {
			$object->resetData();
		}
		return $object;

	}

	public function delete(&$object)
	{

		$format = sprintf('%s = ?', $object->getIdColumnName());

		$builder = $this->getSqlBuilder('delete');
		$builder->from($object->getTableName());
		$builder->where($format, $object->getId());

		if ($builder->query()) {
			$object->resetData();
		}
		else {
			// 삭제 실패시, 데이터 리셋 하지 않음
		}

		return $object;

	}

	public function load(&$object, $id, $columns = null)
	{
		return $this->loadById($object, $id, $columns = null);

	}

	public function save(&$object)
	{
		$builder = $this->getSqlBuilder();
		$builder->from($object->getTableName());

		if ($object->hasLoaded()) {
			$format = sprintf('%s = ?', $object->getIdColumnName());
			$builder->update();
			$builder->where($format, $object->getId());
		}
		else {
			$builder->insert();
		}

		$builder->set($object->getChangedData());
		$builder->query();

		$object->setLoaded(true);
		$object->setChanged(false);

		return $object;

	}

	public function create(&$object)
	{
		$builder = $this->getSqlBuilder('insert');
		$builder->from($object->getTableName());

		$data = $object->getData();

		if (empty($data)) {
			$data = array($object->getIdColumnName() => '');
		}

		$builder->set($data);
		$builder->query();

		if ( ! $object->getId()) {
			$object->setId($builder->lastID());
		}
		$object->setOriginalData();

		$object->setLoaded(true);
		$object->setChanged(false);

		return $object;
	}

}
