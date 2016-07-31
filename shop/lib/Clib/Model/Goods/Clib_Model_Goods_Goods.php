<?php
/**
 * Clib_Model_Goods_Goods
 * @author extacy @ godosoft development team.
 */
class Clib_Model_Goods_Goods extends Clib_Model_Goods_Abstract
{
	/**
	 * {@inheritdoc}
	 */
	protected $objectRelationMapping = array(
		'discount' => array(
			'modelName' => 'goods_discount',
			'deleteCascade' => true,
			'joinType' => 'left',
			'foreignColumn' => 'gd_goodsno',
			'primaryColumn' => 'goodsno',
		),
		'options' => array(
			'modelName' => 'goods_option',
			'isCollection' => true,
			'foreignColumn' => 'goodsno'
		),
		'categories' => array(
			'modelName' => 'goods_link',
			'isCollection' => true,
			'foreignColumn' => 'goodsno',
			'deleteCascade' => true,
			'withoutGroup' => _CATEGORY_NEW_METHOD_,
		),
		'display' => array(
			'modelName' => 'goods_display',
			'isCollection' => true,
			'foreignColumn' => 'goodsno',
			'deleteCascade' => true,
		),
		'brand' => array(
			'modelName' => 'goods_brand',
			'deleteCascade' => false,
			'joinType' => 'left',
			'foreignColumn' => 'sno',
			'primaryColumn' => 'brandno',
		),
	);

	/**
	 * {@inheritdoc}
	 */
	protected $idColumnName = 'goodsno';

	public function getName()
	{
		return 'goods';
	}

	public function setDefaultData()
	{
		$this->setData('tax', '1');
		$this->setData('open', '0');
		$this->setData('relationis', '0');
		$this->setData('open_mobile', '0');
		$this->setData('goodsno', '');
	}

	public function createNew()
	{
		return parent::createNew(array('regdt' => Core::helper('date')->now()));
	}

	public function getOptionFromOwnData()
	{
		$option = Clib_Application::getModelClass('goods_option');
		$data = $option->getFilteredData($this->getData());
		$option->setData($data);

		return $option;
	}

	/**
	 *
	 * @return
	 */
	public function getReviewPoint()
	{
		$goodsno = $this->getId();
		$query = "select round(avg(point)) from " . GD_GOODS_REVIEW . " where goodsno='$goodsno' and sno=parent";
		list($point) = Clib_Application::database()->fetch($query);
		return $point;
	}

	/**
	 *
	 * @return
	 */
	public function getBrandName()
	{
		$sno = $this->getBrandno();

		$query = "select brandnm from " . GD_GOODS_BRAND . " where sno='$sno'";

		list($brandnm) = Clib_Application::database()->fetch($query);
		return $brandnm;
	}

	/**
	 *
	 * @return
	 */
	public function getQR()
	{

	}

	/**
	 *
	 * @return
	 */
	public function getGoodsSelectableAddOptions()
	{
		$goodsno = $this->getId();

		$query = "select * from " . GD_GOODS_ADD . " where goodsno='$goodsno' order by step,sno";
		$res = Clib_Application::database()->query($query);

		$addoptions = array();

		$r_addoptnm = explode("|", $this->getAddoptnm());
		for ($i = 0; $i < count($r_addoptnm); $i++)
			list($addoptnm[], $addoptreq[]) = explode("^", $r_addoptnm[$i]);

		while ($row = Clib_Application::database()->fetch($res, 1)) {
			$addoptions[$addoptnm[$row['step']]][] = $row;
		}

		return $addoptions;
	}

	/**
	 *
	 * @return
	 */
	public function getGoodsInputableAddOptions()
	{
		$goodsno = $this->getId();

		$query = "select * from " . GD_GOODS_ADD . " where goodsno='$goodsno' order by step,sno";
		$res = Clib_Application::database()->query($query);

		$addoptions = array();

		$r_addoptnm = explode("|", $this->getAddoptnm());
		for ($i = 0; $i < count($r_addoptnm); $i++)
			list($addoptnm[], $addoptreq[]) = explode("^", $r_addoptnm[$i]);

		while ($row = Clib_Application::database()->fetch($res, 1)) {
			$addoptions[$addoptnm[$row['step']]][] = $row;
		}

		return $addoptions;
	}

	/**
	 *
	 * @return
	 */
	public function getOptions()
	{
		if (is_null($this->options) || $this->hasChanged() == true) {

			$options = Clib_Application::getModelClass('goods_option')->getCollection();
			$options->addFilter('goods_option.goodsno', $this->getId());
			$options->load();

			$this->options = $options;
		}

		return $this->options;
	}

	public function delOptions()
	{
		$query = "update gd_goods_option set go_is_deleted = '1' where goodsno = " . $this->getId();
		Clib_Application::database()->query($query);

	}

	public function hasOptions()
	{
		switch ($this['use_option']) {
			case null:
				// 옵션 사용여부 미설정된 경우이므로, 사용여부를 체크 하여 저장해준다.
				// 단일 상품은 옵션이 1개인 상품이므로 1개 초과로 체크한다.
				$options = $this->getOptions();
				$use_option = $options->count() > 1 ? 1 : 0;
				$this->setData('use_option', (int)$use_option);
				$this->save();
				return $options->count() > 1 ? true : false;

				break;
			case '1':
				return true;
				break;
			case '0':
				return false;
				break;
		}

		if ( ! $this['use_option']) {
			return false;
		}
		else {

		}
	}

	/**
	 *
	 * @return
	 */
	public function addOptions()
	{
	}

	public function getOptionName()
	{
		$names = array();

		if ( ! empty($this['option_name']))
			$names = explode('|', $this['option_name']);
		elseif ( ! empty($this['optnm']))
			$names = explode('|', $this['optnm']);

		$names = array_notnull($names);

		$optValLength = sizeof($this->getOptionValue());
		if ($optValLength > count($names)) {
			$names = array_pad($names, $optValLength, '');
		}

		return $names;
	}

	public function getOptionValue()
	{
		$optionValue = array();

		if ( ! empty($this['option_value'])) {
			$optionValue = explode('|', $this['option_value']);
		}
		else {

			foreach ($this->getOptions() as $option) {
				$n = 0;
				while ($name = $option->getNthName($n + 1)) {
					if ( ! in_array($name, $optionValue[$n])) {
						$optionValue[$n][] = $name;
					}

					if ($n > 100)
						break;
					// 무한루프 방지
					$n++;
				}
			}

			foreach ($optionValue as $n => $value) {
				$optionValue[$n] = implode(',', $value);
			}
		}

		$optionValue = array_notnull($optionValue);

		return $optionValue;

	}

	public function getPairedOptionNameArray($option, $glue = ':')
	{
		$paired = array();

		foreach ($this->getOptionName() as $key => $name) {
			$paired[] = sprintf('%s %s %s', $name, $glue, $option->getNthName($key + 1));
		}

		return $paired;
	}

	function getReadableId()
	{
		return sprintf('A%08s', $this->getId());
	}

	function getReserve()
	{

		global $set;
		if (!$set) $set = Core::config('configpay');

		$reserve = 0;

		if ( ! $this->getData('use_emoney')) {
			if ( ! $set['emoney']['chk_goods_emoney']) {
				if ($set['emoney']['goods_emoney']) {
					$reserve = getDcprice($this->getPrice(), $set['emoney']['goods_emoney'] . '%');
				}
			}
			else {
				$reserve = $set['emoney']['goods_emoney'];
			}
		}
		else {
			$reserve = $this->getData('goods_reserve');
		}

		return (int)$reserve;
	}

	public function getStock()
	{
		return $this['totstock'];
	}

	/**
	 *
	 * @return
	 */
	function getListImage()
	{
		$image = $this->getImgS();
	}

	/**
	 *
	 * @return
	 */
	function getPrice()
	{
		return $this['goods_price'];
	}

	/**
	 *
	 * @return
	 */
	function getStatus()
	{
	}

	/**
	 *
	 */
	function getIcons()
	{
		$icon = $this->getIcon();
	}

	/**
	 *
	 * @return
	 */
	function getGoodsName()
	{
		$name = $this->getData('goodsnm');

		return $name;
	}

	/**
	 *
	 * @param object $depth [optional]
	 * @return
	 */
	function getCategory($depth = 0)
	{
		// $depth 미지정일때 전체 연결 카테고리를 가져온다.
		if ((int)$depth > 0) {

		}
		else {
			$categories = Clib_Application::getCollectionClass('goods_link');
			$categories->addFilter('goodsno', $this->getId());
			$categories->addOrder('category', 'ASC');
			$categories->load();

			return $categories;
		}
	}

	/**
	 *
	 * @return
	 */
	public function getStockCode()
	{
	}

	/**
	 *
	 * @param object $stock
	 * @return
	 */
	public function addStock($stock)
	{
	}

	/**
	 *
	 * @param object $stock
	 * @return
	 */
	public function cutStock($stock)
	{
	}

	public function getDiscount()
	{
		return $this->discount;
	}

	public function getSpecialDiscountAmount()
	{
		if ($this->getData('use_goods_discount')) {
			return $this->getDiscount()->getDiscountAmount($this);
		}
		else {
			return 0;
		}
	}

	public function getSalesRangeStart()
	{
		return (int)$this->getData('sales_range_start');
	}

	public function getSalesRangeEnd()
	{
		return (int)$this->getData('sales_range_end');
	}

	public function getSalesRange()
	{
		return array(
			$this->getSalesRangeStart(),
			$this->getSalesRangeEnd(),
		);
	}

	public function getSalesStatus()
	{
		// before|end|ing|range (판매전, 판매종료, 판매중, 판매기간중)

		$status = 'ing';

		$range = $this->getSalesRange();
		if ( ! $range[0] && ! $range[1]) {
			return $status;
		}

		if ($range[0] && $range[0] > G_CONST_NOW) {
			$status = 'before';
		}

		if ($range[1] && $range[1] < G_CONST_NOW) {
			$status = 'end';
		}

		if ($range[0] && $range[1] && $range[0] <= G_CONST_NOW && $range[1] >= G_CONST_NOW) {
			$status = 'range';
		}

		return $status;
	}

	public function canSales()
	{
		return in_array($this->getSalesStatus(), array(
			'ing',
			'range'
		));
	}

	public function getRunout()
	{
		return $this->getSoldout();
	}

	public function getSoldout()
	{
		if ( ! $this['runout'] && $this['usestock'] && $this['totstock'] < 1) {
			$this['runout'] = 1;
		}

		return $this['runout'];

	}

	public function getIconHtml($path = '')
	{
		// $path : /shop/goods/ 기준 이므로, 경로를 잡아 줘야 함.
		return setIcon($this['icon'], $this['regdt'], $path);
	}

}
