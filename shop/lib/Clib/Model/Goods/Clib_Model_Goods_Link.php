<?php
/**
 * Clib_Model_Goods_Link
 * @author extacy @ godosoft development team.
 */
class Clib_Model_Goods_Link extends Clib_Model_Abstract
{

	/**
	 * {@inheritdoc}
	 */
	protected $objectRelationMapping = array(
		'category' => array(
			'modelName' => 'category',
			'foreignColumn' => 'category',
			'primaryColumn' => 'category',
		),
		'event' => array(
			'modelName' => 'event',
			'foreignColumn' => 'category',
			'primaryColumn' => 'category',
		),
	);

	/**
	 * {@inheritdoc}
	 */
	protected $idColumnName = 'sno';

	public function deleteExclude($goodsno, $categories)
	{
		$resource = $this->getResource();
		$resource->deleteExclude($this, $goodsno, $categories);
	}

}
