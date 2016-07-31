<?php
/**
 * Clib_Model_Member_Group
 * @author extacy @ godosoft development team.
 */
class Clib_Model_Member_Group extends Clib_Model_Abstract
{
	/**
	 * {@inheritdoc}
	 */
	protected $idColumnName = 'sno';

	/**
	 * 그룹내 회원 수 리턴
	 * @return integer
	 */
	public function getMemberCount()
	{
		static $counts = array();

		$level = $this->getLevel();

		if ( ! isset($counts[$level])) {
			$counts[$level] = $this->getResource()->getMemberCountByLevel($level);
		}

		return $counts[$level];
	}

	/**
	 *
	 * @return
	 */
	public function getTableName()
	{
		return 'gd_member_grp';
	}

	/**
	 *
	 * @param object $level
	 * @param object $columns [optional]
	 * @return
	 */
	public function loadByLevel($level, $columns = null)
	{
		if ($this->hasLoaded()) {
			return $this;
		}

		$this->setChanged(false);
		$this->setLoaded(true);

		$resource = $this->getResource();
		return $resource->loadByLevel($this, $level, $columns);
	}

}
