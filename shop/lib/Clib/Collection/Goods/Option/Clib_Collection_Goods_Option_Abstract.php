<?php
class Clib_Collection_Goods_Option_Abstract extends Clib_Collection_Abstract
{

	/**
	 * {@inheritdoc}
	 */
	protected $valueModel = 'goods_option';

	protected function construct()
	{
		$this->addFilter('goods_option.go_is_deleted', '1', '<>');
		// go_sort 값으로 정렬을 하게 되면,
		// 관리자 페이지 옵션 이미지 설정시 정상 동작 하지 않으므로,
		// front 에서는 별도로 옵션 순서를 정렬해야 한다.
		//$this->setOrder('go_sort asc');
		$this->setOrder('sno asc');
	}

	/**
	 * 내부 모델의 nth 째 옵션명의 배열을 리턴한다.
	 * @param object $nth
	 * @return
	 */
	public function getNthNames($nth)// 1~
	{
		$names = array();

		foreach ($this as $option) {
			if (($name = $option->getNthName($nth)) !== '') {
				if ( ! in_array($name, $names)) {
					$names[] = $name;
				}
			}
		}

		return $names;
	}

	/**
	 * 내부 모델의 nth 째 옵션명의 이미지(or 컬러코드)를 리턴한다.
	 * @param object $nth
	 * @return
	 */
	public function getNthIcons($nth)// 1~
	{
		$names = array();

		foreach ($this as $option) {
			if (($name = $option->getNthIcon($nth)) !== '') {
				if ( ! in_array($name, $names)) {
					$names[$option->getNthName($nth)] = $name;
				}
			}
		}

		return $names;
	}

	/**
	 * 1차 옵션의 이미지를 가져온다.
	 * 이미지가 없는 경우 공백으로 채워진다.
	 * @return array
	 */
	public function getImages()
	{
		$names = array();

		foreach ($this as $option) {
			if (($name = $option->getImage()) !== '') {
				if ( ! in_array($name, $names)) {
					$names[$option->getNthName(1)] = $name;
				}
			}
		}

		return $names;

	}

	public function getEmptyStockCount()
	{
		$cnt = 0;
		foreach ($this as $option) {
			if ((int)$option->getStock() === 0) {
				$cnt++;
			}
		}
		return $cnt;
	}

	public function sort()
	{
		$sortedItems = $this->getIterator();

		$sortkey = 0;
		foreach($sortedItems as $item) {
			$sortkey++;
			$item[go_sort] = $item[go_sort] ? $item[go_sort] : $sortkey;
		}
		$sortedItems->uasort(array($this, 'compareSort'));

		$this->resetItems();
		foreach($sortedItems as $item) {
			$this->addItem($item);
		}
		return $this;
	}

	public function compareSort($a, $b)
	{
		if ( $a['go_sort'] == $b['go_sort'] )
			return 0;
		else if ( $a['go_sort'] < $b['go_sort'] )
			return -1;
        else
			return 1;
	}

	public function removeInvisible()
	{

		foreach($this as $k => $item) {

			if (!$item['go_is_display']) {
				unset($sortedItems[$k]);
			}
		}

		return $this;
	}

}
