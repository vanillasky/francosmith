<?php /**
 * Clib_Collection_Abstract
 * @author extacy @ godosoft development team.
 */
abstract class Clib_Collection_Abstract extends Clib_Collection
{
	/**
	 * 저장할 모델 이름
	 * @var string
	 */
	protected $valueModel = '';

	/**
	 * @var
	 */
	private $_loadResource = null;

	/**
	 * @var
	 */
	protected $currentPage = 1;

	/**
	 * @var
	 */
	protected $pageSize = null;

	/**
	 * @var
	 */
	protected $totalCount = null;

	/**
	 * @var
	 */
	protected $singularize = false;

	/**
	 * Construct
	 * @return void
	 */
	final public function __construct()
	{
		if (method_exists($this, 'construct')) {
			$this->construct();
		}
	}

	public function setChanged($bool)
	{
		$this->_hasRenderResource = array();
		return parent::setChanged($bool);
	}

	/**
	 *
	 * @param integer $page
	 * @return
	 */
	public function setCurrentPage($page)
	{
		$page = intval($page);
		if ($page === 0) {
			$page = 1;	// 페이징 클래스 기본 값
		}

		$this->currentPage = $page;
		return $this;
	}

	/**
	 *
	 * @param integer $size
	 * @return
	 */
	public function setPageSize($size)
	{
		$size = intval($size);
		if ($size === 0) {
			$size = 20;	// 페이징 클래스 기본 값
		}

		$this->pageSize = $size;
		return $this;
	}

	/**
	 *
	 * @return
	 */
	final public function getPaging()
	{
		$offset[0] = $this->currentPage;
		$offset[1] = $this->pageSize;

		$select = $this->getResource();

		$total_count = $this->_getTotalCount($select);

		if ($total_count % $offset[1]) {
			$totalpage = (int)($total_count / $offset[1]) + 1;
		}
		else {
			$totalpage = $total_count / $offset[1];
		}

		// 페이징
		$pg = new Page($offset[0], $offset[1]);
		$pg->recode['total'] = $total_count;
		$pg->page['total'] = $totalpage;
		$pg->idx = $pg->recode['total'] - $pg->recode['start'];
		$pg->setNavi($tpl = '');
		$pg->query = $select->toString();

		return $pg;
	}

	public function getTotalCount()
	{
		$this->renderResource();
		return $this->_getTotalCount();
	}

	/**
	 *
	 * @return integer
	 */
	private function _getTotalCount()
	{
		$select = clone $this->getResource();
		$select->reset('column')->reset('order')->reset('limit');

		if ($select->has('group')) {
			$select->columns(array(Clib_Application::database()->expression('1 AS __CNT__')));
			$_cnt_sql = "SELECT COUNT(__COUNT_SQL__.__CNT__) FROM (" . $select->toString() . ") AS __COUNT_SQL__";
		}
		else {
			$_cnt_sql = $select->columns(array(Clib_Application::database()->expression('COUNT(*) AS __CNT__')))->toString();
		}

		list($total_count) = Clib_Application::database()->fetch($_cnt_sql);

		return (int) $total_count;
	}

	/**
	 * Value Model 의 이름 또는 object 설정
	 * @param string|object $name
	 * @return Clib_Collection_Abstract
	 */
	public function setValueModel($name)
	{
		$this->valueModel = $name;
		return $this;
	}

	/**
	 * 리소스를 저장할 모델을 리턴
	 * @return Clib_Model_Abstract
	 */
	public function getValueModel()
	{
		switch (true) {
			case is_object($this->valueModel) :
				return $this->valueModel;
				break;
			case empty($this->valueModel) :
				$this->setValueModel(Clib_Application::getClassLastName($this));
			default :
				$this->setValueModel(Clib_Application::getModelClass($this->valueModel));
				return $this->getValueModel();
				break;
		}

	}

	/**
	 *
	 * @return GODO_DB_Builder
	 */
	protected function getResource()
	{
		if ($this->_loadResource === null) {
			$this->_loadResource = Clib_Application::database()->builder();
			$this->_loadResource->select();
		}

		return $this->_loadResource;
	}

	/**
	 *
	 * @return boolean
	 */
	protected function renderResource()
	{
		try {

			$this->_renderResourceTable();
			$this->_renderResourceWhere();
			$this->_renderResourceOrder();
			$this->_renderResourceLimit();

		}
		catch (Clib_Exception $e) {
			// @todo : exception 처리
			return false;
		}

		return true;
	}

	private $_hasRenderResource = array();
	// 각 키별로 true/false

	/**
	 *
	 * @param string $column
	 * @param string $modelName [optional]
	 * @return string
	 */
	private function _getColumnName($column, $modelName = null)
	{
		$dotNotation = $this->parseDotNotation($column);

		if ($dotNotation['targetModel']) {
			$modelName = $dotNotation['targetModel'];
			$columnName = $dotNotation['column'];
		}
		else {
			if (is_null($modelName) || $modelName == '_') {
				$modelName = $this->getValueModel()->getClassName();
			}
			else {
				$tmpModelName = Clib_Application::getClassName('model', $modelName);

				if ($tmpModelName != $modelName) {
					$modelName = $tmpModelName;
				}
			}

			$columnName = $column;
		}

		$columnName = sprintf('%s.%s', Clib_Application::getAlias($modelName), $columnName);
		//$columnName = $this->getResource()->backticQuote($columnName);

		return $columnName;
	}

	/**
	 *
	 * @param object $resource
	 * @param object $object
	 * @return
	 */
	private function _renderResourceTableJoin($resource, $object)
	{
		//debug($object->getRelationShip());
		if ($object->hasRelationShip()) {
			foreach ($object->getRelationShip() as $property => $config) {

				// for basic usage;
				if (is_int($property) && ! is_array($config)) {
					$property = $config;
					$config = array();
				}
				// create model;
				$modelName = $config['modelName'] ? $config['modelName'] : $property;

				$relatedObject = Clib_Application::getModelClass($modelName);

				if (false === $this->hasModelFilter($relatedObject->getClassName()) && false === $this->hasModelOrder($relatedObject->getClassName())) {//
					continue;
				}

				$config = $object->getRelationShipConfig($relatedObject, $config);
				$this->_renderResourceJoin($object, $relatedObject, $resource, $config);
			}
		}
	}

	private function _renderResourceJoin($object, $relatedObject, $resource, $config)
	{
		$joinObject = $object;
		//조인모델

		if ($config['require']) {
			$requireModel = Clib_Application::getModelClass($config['require']);

			$relativeConfig = $object->getRelationShip();

			$objectConfig = $relativeConfig[strtolower(Clib_Application::getClassLastName($object))];
			$requireModelConfig = $relativeConfig[strtolower(Clib_Application::getClassLastName($requireModel))];
			$relatedObjectConfig = $config;

			$resource->join(array(Clib_Application::getAlias($requireModel->getClassName()) => $requireModel->getTableName()), sprintf('%s = %s', $this->_getColumnName($requireModelConfig['primaryColumn'], $object->getClassName()), $this->_getColumnName($requireModelConfig['foreignColumn'], $requireModel->getClassName())));
			$joinObject = $requireModel;
		}

		$joinTables = array(Clib_Application::getAlias($relatedObject->getClassName()) => $relatedObject->getTableName());
		$relationColumn = sprintf('%s = %s', $this->_getColumnName($config['primaryColumn'], $joinObject->getClassName()), $this->_getColumnName($config['foreignColumn'], $relatedObject->getClassName()));

		if ($config['joinType'] == 'left') {
			$resource->leftjoin($joinTables, $relationColumn);
		}
		else {
			$resource->join($joinTables, $relationColumn);
		}
		/*
		 if (!$config['isCollection']) {
		 //$resource->group($this->_getColumnName($config['primaryColumn'], $object->getClassName()));
		 $resource->group($this->_getColumnName($object->getIdColumnName(), $object->getClassName()));
		 }
		 */
		// GROUP BY 처리 여부
		if ($config['withoutGroup'] !== true) {
			$resource->group($this->_getColumnName($object->getIdColumnName(), $object->getClassName()));
		}
	}

	private function _renderResourceTable($force = false)
	{
		$renderingType = 'table';

		if ( ! $force && $this->_hasRenderResource[$renderingType]) {
			return true;
		}

		$model = $this->getValueModel();

		$resource = $this->getResource()->reset('table')->reset('join');
		if ($resource->has('column')) {
			$resource->from(array(Clib_Application::getAlias($model->getClassName()) => $model->getTableName()), null);
		}
		else {
			$resource->from(array(Clib_Application::getAlias($model->getClassName()) => $model->getTableName()));
		}

		if ($model->hasRelationShip() && ! $this->singularize) {
			$this->_renderResourceTableJoin($resource, $this->getValueModel());
		}
		else {
			//
		}

		$this->_hasRenderResource[$renderingType] = true;

		return true;
	}

	/**
	 * WHERE 절을 설정한다
	 * @return
	 */
	private function _renderResourceWhere($force = false)
	{
		$renderingType = 'where';

		if ( ! $force && $this->_hasRenderResource[$renderingType]) {
			return true;
		}

		$resource = $this->getResource()->reset($renderingType);

		foreach ($this->getFilter() as $targetModel => $filters) {

			foreach ($filters as $filter) {

				if (isset($filter['expression'])) {
					$resource->where($filter['expression'], null, $filter['chain']);
					continue;
				}

				$column = $this->_getColumnName($filter['column'], $targetModel);
				$value = $filter['value'];
				$chain = $filter['chain'];
				$operator = (string)$filter['operator'];

				switch ($operator) {
					case 'range' :
						$format = sprintf('%s between ? AND ?', $column);
						break;

					case 'like' :
						$format = sprintf('%s like ?', $column);
						break;

					case 'in' :
						$value = array($value);
						$format = sprintf('%s in (?)', $column);
						break;

					case 'equal' :
					case '' :
						$format = sprintf('%s = ?', $column);
						break;

					default :
						$format = sprintf('%s %s ?', $column, $operator);
						break;
				}

				$resource->where($format, $value, $chain);

			}

		}

		$this->_hasRenderResource[$renderingType] = true;

		return true;
	}

	/**
	 * ORDER 절을 설정한다
	 * @return Clib_Collection_Abstract
	 */
	private function _renderResourceOrder($force = false)
	{
		$renderingType = 'order';

		if ( ! $force && $this->_hasRenderResource[$renderingType]) {
			return true;
		}

		$resource = $this->getResource()->reset($renderingType);

		foreach ($this->getOrder() as $targetModel => $orders) {

			foreach ($orders as $order) {

				if (isset($order['expression'])) {
					$resource->order(sprintf('%s %s', $order['expression'], $order['direction']));
				}
				else {
					$resource->order(sprintf('%s %s', $this->_getColumnName($order['column'], $targetModel), $order['direction']));
				}
			}

		}

		$this->_hasRenderResource[$renderingType] = true;

		return true;
	}

	/**
	 * LIMIT 절을 설정한다
	 * @return true
	 */
	private function _renderResourceLimit($force = false)
	{
		$renderingType = 'limit';

		if ( ! $force && $this->_hasRenderResource[$renderingType]) {
			return true;
		}

		$resource = $this->getResource()->reset($renderingType);

		$from = ($this->currentPage - 1) * $this->pageSize;
		$to = $this->pageSize;

		$resource->limit($from, $to);

		$this->_hasRenderResource[$renderingType] = true;

		return $this;

	}

	protected function getAllIds($resource)
	{
		$resource->reset('column');
		$vo = $this->getValueModel();
		$resource->columns($this->_getColumnName($vo->getIdColumnName(), $vo->getClassName()));

		$stmt = $resource->prepare();
		//$stmt->setCache(300);	// 5 minute;
		$stmt->execute();

		$ids = array();

		foreach ($stmt as $row) {
			$ids[] = array_shift($row);
		}

		return $ids;
	}

	/**
	 *
	 * @return
	 */
	public function load()
	{
		if ($this->hasLoaded() && ! $this->hasChanged()) {
			return $this;
		}

		// rendering resource;
		$this->renderResource();
		$resource = $this->getResource();

		// value object has relation ship?
		//*/
		if ($this->getValueModel()->hasRelationShip() && ! $this->singularize) {
			$ids = $this->getAllIds($resource);
			return $this->loadByIds($ids);
		}
		/**/

		return $this->loadBySql($resource);
	}

	/**
	 *
	 * @return
	 */
	public function save()
	{
		if ($this->hasLoaded()) {
			foreach ($this as $item) {
				if ($item->hasChanged())
					$item->save();
			}
		}

		return $this;
	}

	/**
	 *
	 * @return
	 */
	public function delete()
	{
		if ($this->hasLoaded()) {
			foreach ($this as $item) {
				if ($item->hasLoaded())
					$item->delete();
			}
		}

		return $this;
	}

	public function loadByIds($ids)
	{
		if ( ! is_array($ids)) {
			$ids = array($ids);
		}

		$_item = $this->getValueModel();

		// load by sql;
		if (sizeof($ids) > 1) {
			$sql = sprintf('SELECT * FROM %s WHERE %s IN (%s) ORDER BY FIND_IN_SET(%s, \'%s\')', $_item->getTableName(), $_item->getIdColumnName(), $this->_getIdString($ids), $_item->getIdColumnName(), implode(',',$ids));
			return $this->loadBySql($sql);
		}

		foreach ($ids as $id) {
			$item = clone $_item;

			$item->load($id);
			$this->addItem($item);
		}

		$this->setLoaded(true);
		$this->setChanged(false);

		return $this;

	}

	private function _getIdString($ids)
	{
		foreach($ids as &$id) {
			if (preg_match('/[^0-9]/', $id)) {
				$id = Clib_Application::database()->quote($id);
			}
		}

		return implode(',', $ids);

	}

	/**
	 *
	 * @param GODO_DB_Builder|string $sql
	 * @return
	 */
	public function loadBySql($sql)
	{
		if ($sql instanceof GODO_DB_builder) {
			$sql = $sql->toString();
		}

		$stmt = Clib_Application::database()->prepare($sql);
		//$stmt->setCache(300);	// 5 minute;
		$stmt->execute();

		$_item = $this->getValueModel();

		foreach ($stmt as $row) {
			$item = clone $_item;
			$item->setData($row);

			$item->setOriginalData();
			$item->setLoaded(true);
			$item->setChanged(false);
			$this->addItem($item);
		}

		$this->setLoaded(true);
		$this->setChanged(false);

		return $this;

	}

	/**
	 *
	 * @return
	 */
	public function getSql()
	{
		if ($this->renderResource()) {
			$resource = $this->getResource();
			return $resource->toString();
		}
		else {
			return false;
		}
	}

	/**
	 *
	 * @return
	 */
	public function setSingularize($bool = true)
	{
		$this->singularize = $bool;
	}

}
