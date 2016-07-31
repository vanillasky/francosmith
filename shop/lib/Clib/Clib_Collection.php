<?php
/**
 * Clib_Collection
 * @author extacy @ godosoft development team.
 */
class Clib_Collection implements IteratorAggregate, Countable
{
	/**
	 * @var
	 */
	protected $items = array();

	/**
	 * @var
	 */
	protected $orders = array();

	/**
	 * @var
	 */
	protected $filters = array();

	/**
	 * DB 등의 Resource 와 동기화(load or save) 여부
	 * @var boolean
	 */
	private $_hasLoaded = false;

	/**
	 * DB 등의 Resource 와 동기화 이후, 데이터의 변형이 있었는지 여부
	 * @var boolean
	 */
	private $_hasChanged = false;

	/**
	 * 데이터 로드 여부를 리턴
	 * @return boolean
	 */
	public function hasLoaded()
	{
		return $this->_hasLoaded;
	}

	/**
	 * 데이터 변경 여부를 리턴
	 * @return boolean
	 */
	public function hasChanged()
	{
		return $this->_hasChanged;
	}

	/**
	 * 데이터 변경 여부를 설정
	 * @param boolean $bool
	 * @return Clib_Object
	 */
	public function setChanged($bool)
	{
		$this->_hasChanged = (bool)$bool;
		return $this;
	}

	/**
	 * 데이터 로드 여부를 설정
	 * @param boolean $bool
	 * @return Clib_Object
	 */
	public function setLoaded($bool)
	{
		$this->_hasLoaded = (bool)$bool;
		return $this;
	}

	/**
	 *
	 * @param object $column
	 * @param object $direction [optional]
	 * @return
	 */
	public function setOrder($column, $direction = null)
	{

		if (empty($column)) {
			return $this;
		}

		if (strpos($column, ' ') !== false) {
			list($column, $direction) = explode(' ', $column);
		}

		if (empty($direction)) {
			$direction = 'ASC';
		}

		$dotNotation = $this->parseDotNotation($column);

		$order = array(
			'column' => $column,
			'direction' => $direction,
		);

		if ($dotNotation['targetModel']) {
			$this->orders[$dotNotation['targetModel']][] = $order;
		}
		else {
			$this->orders['_'][] = $order;
		}

		$this->setChanged(true);

		return $this;
	}

	public function addExpressionOrder($expression, $direction = 'DESC')
	{
		$split = preg_split('/\s/', $expression, - 1, PREG_SPLIT_NO_EMPTY);

		$expression = array();

		foreach ($split as $str) {
			$tmp = $this->parseDotNotation($str);
			if ( ! is_null($tmp['targetModel'])) {

				$tmp['targetModel'] = Clib_Application::getAlias($tmp['targetModel']);
				$expression[] = implode('.', $tmp);
			}
			else {
				$expression[] = $str;
			}
		}

		$this->orders['_'][] = array(
			'expression' => implode(' ', $expression),
			'direction' => $direction
		);

		$this->setChanged(true);

		return $this;
	}

	public function addOrder($column, $direction = null)
	{
		return $this->setOrder($column, $direction);
	}

	public function hasModelOrder($modelName = null)
	{
		if (is_null($modelName)) {
			return (bool)sizeof($this->orders['_']);
		}
		else {
			return (bool)sizeof($this->orders[$modelName]);
		}
	}

	public function hasOrder($column)
	{

	}

	public function getOrder($column = null)
	{
		if (empty($column)) {
			return $this->orders;
		}

		if (is_array($column)) {
			$result = array();
			foreach ($this->orders as $filter) {
				if (in_array($filter['column'], $column)) {
					$result[] = $filter;
				}
			}
			return $result;
		}

		foreach ($this->orders as $filter) {
			if ($filter['column'] === $column) {
				return $filter;
			}
		}
	}

	protected function parseDotNotation($str)
	{
		if (strpos($str, '.') > 0 && !preg_match("/'.*\..*'/",$str)) {
			list($modelName, $columnName) = explode('.', $str);
			$modelName = Clib_Application::getClassName('model', $modelName);
			return array(
				'targetModel' => $modelName,
				'column' => $columnName
			);
		}
		else {
			return array(
				'targetModel' => null,
				'column' => $str
			);
		}

	}

	public function addExpressionFilter($expression, $chain = 'and')
	{
		$split = preg_split('/\s/', $expression, - 1, PREG_SPLIT_NO_EMPTY);

		$expression = array();

		foreach ($split as $str) {
			$tmp = $this->parseDotNotation($str);
			if ( ! is_null($tmp['targetModel'])) {

				$tmp['targetModel'] = Clib_Application::getAlias($tmp['targetModel']);
				$expression[] = implode('.', $tmp);
			}
			else {
				$expression[] = $str;
			}
		}

		$this->filters['_'][] = array(
			'expression' => implode(' ', $expression),
			'chain' => $chain
		);

		return $this;
	}

	public function addExpressionJoinFilter($expression, $chain = 'and')
	{
		$split = preg_split('/\s/', $expression, - 1, PREG_SPLIT_NO_EMPTY);

		$expression = array();
		$targetModel= '_';

		foreach ($split as $str) {
			$tmp = $this->parseDotNotation($str);
			if ( ! is_null($tmp['targetModel'])) {
				if($targetModel == '_') $targetModel = $tmp['targetModel'];
				$tmp['targetModel'] = Clib_Application::getAlias($tmp['targetModel']);
				$expression[] = implode('.', $tmp);
			}
			else {
				$expression[] = $str;
			}
		}

		$this->filters[$targetModel][] = array(
			'expression' => implode(' ', $expression),
			'chain' => $chain
		);

		return $this;
	}

	/**
	 *
	 * @param string $column
	 * @param mixed $value
	 * @param string $operator [optional]
	 * @param string $chain [optional]
	 * @return
	 */
	public function addFilter($column, $value, $operator = 'equal', $chain = 'and')
	{

		$dotNotation = $this->parseDotNotation($column);

		$filter = array(
			'column' => $dotNotation['column'],
			'value' => $value,
			'operator' => $operator,
			'chain' => $chain,
		);

		if ($dotNotation['targetModel']) {
			$this->filters[$dotNotation['targetModel']][] = $filter;
		}
		else {
			$this->filters['_'][] = $filter;
		}

		$this->setChanged(true);

		return $this;
	}

	public function hasModelFilter($modelName = null)
	{
		if (is_null($modelName)) {
			return (bool)sizeof($this->filters['_']);
		}
		else {
			return (bool)sizeof($this->filters[$modelName]);
		}
	}

	public function hasFilter($column, $targetModel = null)
	{
		$filter = $this->getFilter($column);
		return ! empty($filter);
	}

	/**
	 *
	 * @param string $column
	 * @param array $values
	 * @return
	 */
	public function addRangeFilter($column, $values)
	{
		$this->addFilter($column, $values, 'range');
	}

	/**
	 *
	 * @param string|null $column
	 * @return
	 */
	public function getFilter($column = null)
	{
		if (empty($column)) {
			return $this->filters;
		}

		/*
		 if (is_array($column)) {
		 $result = array();
		 foreach ($this->filters as $filter) {
		 if (in_array($filter['column'], $column)) {
		 $result[] = $filter;
		 }
		 }
		 return $result;
		 }
		 */

		$dotNotation = $this->parseDotNotation($column);

		if ($dotNotation['targetModel']) {
			$_filters = &$this->filters[$dotNotation['targetModel']];
		}
		else {
			$_filters = &$this->filters['_'];
		}

		foreach ($_filters as $filter) {
			if ($filter['column'] === $dotNotation['column']) {
				return $filter;
			}
		}
	}

	/**
	 *
	 * @param string|null $column
	 * @return
	 */
	public function delFilter($column)
	{
		if ($this->hasFilter($column)) {

			$dotNotation = $this->parseDotNotation($column);

			foreach ($this->filters[$dotNotation['targetModel']] as $key => $filter) {

				if ($filter['column'] === $dotNotation['column']) {
					unset($this->filters[$dotNotation['targetModel']][$key]);
					return $this;
				}
			}
		}
	}

	public function resetItems()
	{
		$this->items = array();
	}

	/**
	 *
	 * @param object $item
	 * @return
	 */
	public function addItem($item, $key = null)
	{
		if (is_null($key)) {
			$this->items[] = $item;
		}
		else {
			$this->items[$key] = $item;
		}
	}

	/**
	 *
	 * @param object $item
	 * @return
	 */
	public function unshiftItem($item)
	{
		array_unshift($this->items, $item);
		return $this;
	}

	/**
	 * Retrieve an external iterator
	 * @return
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->items);
	}

	/**
	 * Count elements of an object
	 * @return
	 */
	public function count()
	{
		return count($this->items);
	}

	/**
	 * items Property convert to json
	 * @return string
	 */
	public function toJson()
	{
		$blocks = array();

		foreach ($this as $k => $item) {

			if ($item instanceof Clib_Object) {
				$json = $item->toJson();
			}
			else {
				$json = json_encode($item);
			}

			$blocks[] = sprintf('"%d":%s', $k, $json);
		}

		return '{' . implode(',', $blocks) . '}';

	}

	/**
	 * items Property convert to json
	 * @return string
	 */
	public function toArray($callback = null)
	{
		$blocks = array();

		foreach ($this as $k => $item) {
			if ($item instanceof Clib_Object) {
				$tmp_arr = $item->getData();
			}
			else {
				$tmp_arr = $item;

			}

			$blocks[] = $tmp_arr;
		}

		if (is_callable($callback)) {
			$blocks = call_user_func($callback, $blocks);
		}

		return $blocks;

	}

}
