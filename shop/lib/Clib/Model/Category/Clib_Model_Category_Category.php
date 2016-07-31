<?php
/**
 * Clib_Model_Category_Category
 * @author extacy @ godosoft development team.
 */
class Clib_Model_Category_Category extends Clib_Model_Abstract
{
	/**
	 * {@inheritdoc}
	 */
	protected $idColumnName = 'category';

	private function _checkPermission($category, $level)
	{

		if ($category['level']) {

			if ($category['level'] <= $level) {
				return true;
			}
			else {

				switch($category['level_auth']) {//권한체크
					case '1' :	//모두숨김
						return false;
						break;
					case '2' :	//카테고리만
						if (( ! $level ? '0' : $level) < $category['level']) {
							return false;
						}
						break;
				}
			}
		}

		return true;

	}

	/**
	 *
	 * @return
	 */
	public function checkPermission($level)
	{
		for ($i = $this->getDepth() - 1, $m = 1; $i >= $m; $i--) {

			$_categoryId = substr($this->getId(), 0, $i * 3);
			$category = clone $this;
			$category->resetData()->load($_categoryId);

			if ( ! $this->_checkPermission($category, $level)) {
				return false;
			}

		}

		return $this->_checkPermission($this, $level);

	}

	/**
	 *
	 * @return
	 */
	private function _getParentCategory()
	{
		$categoryId = $this->getCategory();

		return false;
	}

	/**
	 *
	 * @return
	 */
	public function getDepth()
	{
		return strlen($this->getId()) / 3;
	}

	public function getAuthStep()
	{
		$cauth_step = explode(':', $this['auth_step']);

		$_auth_step = array();
		$_auth_step[0] = (in_array('1', $cauth_step) ? 'Y' : 'N');
		$_auth_step[1] = (in_array('2', $cauth_step) ? 'Y' : 'N');
		$_auth_step[2] = (in_array('3', $cauth_step) ? 'Y' : 'N');

		return $_auth_step;
	}

	public function getExcludeCategoryCollection()
	{

		if ($this->getDepth() == 4) {
			return;
		}

		$collection = Clib_Application::getCollectionClass('category');
		$collection->addFilter('category', Clib_Application::database()->wildcard($this->getId(), 1), 'like');
		$collection->addFilter('level_auth', array(
			1,
			2
		), 'in');
		$collection->load();

		return $collection;

	}

	public function getConfig()
	{

		global $cfg;

		@include  sprintf('%s/conf/category/%s.php', SHOPROOT, $this->getId());

		if ( ! $lstcfg['cols'])
			$lstcfg['cols'] = 4;
		if ( ! $lstcfg['size'])
			$lstcfg['size'] = $cfg['img_s'];
		if ( ! $lstcfg['tpl'])
			$lstcfg['tpl'] = "tpl_01";
		if ( ! count($lstcfg['page_num']))
			$lstcfg['page_num'] = array(
				12,
				20,
				32,
				48,
			);

		if ( ! $lstcfg['rcols'])
			$lstcfg['rcols'] = 4;
		if ( ! $lstcfg['rsize'])
			$lstcfg['rsize'] = $cfg['img_s'];
		if ( ! $lstcfg['rtpl'])
			$lstcfg['rtpl'] = "tpl_01";
		if ( ! $lstcfg['rpage_num'] || $lstcfg['rpage_num'] == 0)
			$lstcfg['rpage_num'] = 4;

		// 디스플레이 유형 설정값
		// 템플릿에서 글로벌 변수로 불러오지 않는 경우 scope 가 지정되지 않아 tpl, rtpl키를 갖는 값이 비워집니다
		$_dpCfg_keys = array(
			'alphaRate',
			'dOpt1',
			'dOpt2',
			'dOpt3',
			'dOpt4',
			'dOpt5',
			'dOpt6',
			'dOpt7',
			'dOpt8',
			'dOpt9',
			'dOpt10',
			'dOpt11',
		);
		foreach (array('rtpl','tpl') as $k => $v) {
			foreach ($_dpCfg_keys as $_k => $_v) {
				$GLOBALS['dpCfg'][$v][$_v] = $lstcfg[$_v][$v];
			}
		}

		return $lstcfg;
	}

	public function getSortField($category, $tableAlias = null)
	{
		$categoryInfo = $this->_dbo->fetch('SELECT sort_type FROM '.GD_CATEGORY.' WHERE category="'.$category.'"', true);
		if ($categoryInfo['sort_type'] === 'MANUAL') {
			$categoryDepth = (strlen($category) / 3);
			if ($categoryDepth === 2) $sortField = 'sort2';
			else if ($categoryDepth === 3) $sortField = 'sort3';
			else if ($categoryDepth === 4) $sortField = 'sort4';
			else $sortField = 'sort1';
		}
		else {
			$sortField = 'sort';
		}
		return ($tableAlias === null) ? $sortField : $tableAlias.'.'.$sortField;
	}

	/**
	 * @see GoodsSort::getSortField
	 */
	public function getSortColumnName($target = 'goods_link')
	{
		$name = 'sort';

		if ($this->getSortType() == 'MANUAL') {
			if (($depth = $this->getDepth()) >= 1 && $depth <= 4) {
				$name = 'sort' . $depth;
			}
			else {
				$name = 'sort1';
			}
		}

		return $target ? sprintf('%s.%s', $target, $name) : $name;

	}

	/**
	 * 서브 카테고리 갯수
	 *
	 * @return integer
	 */
	public function getSubCategoryCount()
	{
		$count = 0;

		if ($this->hasLoaded()) {
			$sql = sprintf(
				"select count(*) from %s where category like '%s%%' and length(category)='%d'", GD_CATEGORY, $this->getId(), ($this->getDepth() + 1) * 3
			);
			list($count) = Clib_Application::database()->fetch($sql);
		}

		return $count;
	}

}
