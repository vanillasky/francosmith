<?php
/**
 * Clib_Collection_Category_Category
 * @author extacy @ godosoft development team.
 */
class Clib_Collection_Category_Category extends Clib_Collection_Abstract
{

	/**
	 * {@inheritdoc}
	 */
	protected $valueModel = 'category';

	/**
	 *
	 * @param object $categoryId
	 * @return
	 */
	public function setCategoryFilter($categoryId)
	{
		$this->addFilter('category', $categoryId);
	}

	/**
	 * 해당 컬렉션내, 상위 카테고리가 있는지 체크하여 그 경로를 생성하여 리턴
	 * @param Clib_Model_Category_Category $category
	 * @return string
	 */
	public function getCategoryRoute($category, $glue = ' > ')
	{

		$depth = $category->getDepth();

		if ($depth > 1) {

			$parents = array();
			$route = array();

			for ($i = 1; $i <= $depth - 1; $i++) {
				$id = substr($category->getId(), 0, $i * 3);

				if ( ! $parents[$_id]) {
					foreach ($this as $parent) {
						if ($parent->getId() == $id) {
							$parents[$id] = $parent->getData('catnm');
							break;
						}
					}
				}
				$route[] = $parents[$id];
			}

			$route[] = $category->getData('catnm');

			return implode($glue, $route);
		}
		else {
			return $category->getData('catnm');
		}
	}

}
