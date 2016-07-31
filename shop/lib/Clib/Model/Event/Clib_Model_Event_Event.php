<?php /**
 * Clib_Model_Event_Event
 */
class Clib_Model_Event_Event extends Clib_Model_Abstract
{
	/**
	 * {@inheritdoc}
	 */
	protected $idColumnName = 'sno';

	public function isFinished()
	{
		if ($this['edate'] && Core::helper('date')->max($this['edate'], false) < G_CONST_NOW) {
			return true;
		}
		else {
			return false;
		}
	}

	public function isStarted()
	{
		if (Core::helper('date')->min($this['sdate'], false) > G_CONST_NOW) {
			return false;
		}
		else {
			return true;
		}
	}

	public function getCategoryCollection()
	{
		$tmp = explode('|', $this['r_category']);
		$collection = Clib_Application::getCollectionClass('category');

		if ( ! empty($tmp)) {
			$collection->addFilter('category', $tmp, 'in')->load();
		}

		return $collection;
	}

	public function getBrandCollection()
	{
		$tmp = explode('|', $this['r_brand']);
		$collection = Clib_Application::getCollectionClass('goods_brand');

		if ( ! empty($tmp)) {
			$collection->addFilter('sno', $tmp, 'in')->load();

			foreach ($collection as $item) {
				$item['brandno'] = $item['sno'];
			}
		}

		return $collection;
	}

	public function getDisplayId()
	{
		return 'e'.$this['sno'];
	}

}
