<?php
/**
 * Clib_Model_Goods_Relate
 */
class Clib_Model_Goods_Related extends Clib_Model_Abstract
{

	/**
	 * {@inheritdoc}
	 */
	protected $idColumnName = 'goodsno';

	public function deleteAll($id)
	{
		return $this->getResource()->deleteAll($this, $id);

		// $db->query(" DELETE FROM ".GD_GOODS_RELATED." WHERE goodsno = $id ");
	}

	public function getSort($id)
	{
		return $this->getResource()->getSort($this, $id);
	}

	public function save()
	{
		parent::save();

		// 서로등록
		if ($this['r_type'] == 'couple') {

			// 상대 상품의 관련상품 데이터 보정.
			fixRelationGoods($this['r_goodsno']);

			$db = $this->getResource()->getSqlBuilder('insert');
			$db->from($this->getTableName());
			$db->set(array(
				'goodsno' => $this['r_goodsno'],
				'sort' => $this->getSort($this['r_goodsno']),
				'r_type' => $this['r_type'],
				'r_goodsno' => $this['goodsno'],
				'r_start' => (!empty($this['r_start']) ? $this['r_start'] : null),
				'r_end' => (!empty($this['r_end']) ? $this['r_end'] : null),
				'regdt' => $this['regdt'],
			));
			$db->duplicateupdate();
			$db->query();

		}
		else {

			// 상대 상품이 서로 등록인 경우에만 삭제.
			$db = $this->getResource()->getSqlBuilder('delete');
			$db->from($this->getTableName());
			$db->where('goodsno = ?', $this['r_goodsno']);
			$db->where('r_goodsno = ?', $this['goodsno']);
			$db->where('r_type = ?', 'couple');
			$db->query();
		}

		return $this;
	}

}
