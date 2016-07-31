<?php
class Clib_Model_Goods_Option extends Clib_Model_Abstract
{
	protected $propertyMap = array(
		'sno' => 'sno',
		'goodsno' => 'goodsno',
		'sku' => 'sku',
		'opt1' => 'opt1',
		'opt2' => 'opt2',
		'optn' => 'optn',
		'price' => 'price',
		'consumer' => 'consumer',
		'supply' => 'supply',
		'reserve' => 'reserve',
		'stock' => 'stock',
		'opt1img' => 'opt1img',
		'opt1icon' => 'opt1icon',
		'opt2icon' => 'opt2icon',
		'link' => 'link',
		'optno' => 'optno',
		'pchsno' => 'pchsno',
		'go_is_deleted' => 'go_is_deleted',
		'go_is_display' => 'go_is_display',
		'go_sort' => 'go_sort',
	);

	/**
	 * {@inheritdoc}
	 */
	protected $objectRelationMapping = array(
		'goods' => array(
			'modelName' => 'goods',
			'foreignColumn' => 'goodsno',
			'primaryColumn' => 'goodsno',
		),
		'categories' => array(
			'modelName' => 'goods_link',
			'isCollection' => true,
			'foreignColumn' => 'goodsno',
			'primaryColumn' => 'goodsno',
			'withoutGroup' => _CATEGORY_NEW_METHOD_,
		),
		'display' => array(
			'modelName' => 'goods_display',
			'isCollection' => true,
			'foreignColumn' => 'goodsno',
			'primaryColumn' => 'goodsno',
		),
	);

	private $_statistics = null;

	public function getFilteredData($data)
	{
		$_data = array();

		foreach ($data as $k => $v) {
			if (array_key_exists($k, $this->propertyMap)) {
				$_data[$k] = $v;
			}
		}

		return $_data;

	}

	public function createNew($goodsno)
	{
		parent::createNew(array('goodsno' => $goodsno));
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	protected $idColumnName = 'sno';

	public function getImage()
	{
		return (string)$this->getData('opt1img');
	}

	public function getNthIcon($nth)// 1~
	{

		if ($nth > 2) {
			$tmp = explode('|', $this->getData('optnicon'));
			$nth = $nth - 3;
			return (string)$tmp[$nth];
		}
		else {
			return (string)$this->getData('opt' . $nth . 'icon');
		}

	}

	public function getNthName($nth)// 1~
	{

		if ($nth > 2) {
			$tmp = explode('|', $this->getData('optn'));
			$nth = $nth - 3;
			return (string)$tmp[$nth];
		}
		else {
			return (string)$this->getData('opt' . $nth);
		}

	}

	public function getName($glue = ',')
	{
		$names = array();

		$nth = 1;

		while (($name = $this->getNthName($nth)) != '') {

			$names[] = $name;
			$nth++;
		}

		return implode($glue, $names);
	}

	public function getReserve()
	{
		return $this->getData('reserve');
	}

	public function getStock()
	{
		return $this->getData('stock');
	}

	public function addStock($value)
	{
		$value = abs($value);

		// 재고량
		$stock = $this->getStock() + $value;

		// 재고 갱신
		$this->setData('stock', $stock)->save();

		return $this;
	}

	public function subStock($value)
	{
		$value = abs($value);

		// 재고량
		$stock = $this->getStock() - $value;

		// 재고 갱신
		$this->setData('stock', $stock)->save();

		return $this;
	}

	public function setStock($value)
	{
		// 입력 재고량과 현 재고량의 차에 따른 가감
		$stock = abs($value) - $this->getStock();

		if ($stock < 0) {
			// sub;
			$this->subStock($stock);
		}
		else if ($stock > 0) {
			// add;
			$this->addStock($stock);

		}
		else {
			// nothing to do;
			return $this;
		}
	}

	public function save()
	{
		// 삭제된 상품은 출력, 링크값 해제
		if ($this['go_is_deleted'])
		{
			$this['link'] = 0;
			$this['go_is_display'] = 0;
		}

		return parent::save();

	}

	public function delete()
	{
		$this['go_is_deleted'] = 1;
		return $this->save();
	}


}
