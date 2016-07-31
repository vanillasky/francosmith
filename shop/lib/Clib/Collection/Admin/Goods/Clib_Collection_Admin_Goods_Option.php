<?php
/**
 * Clib_Collection_Admin_Goods_Option
 * @author extacy @ godosoft development team.
 */
class Clib_Collection_Admin_Goods_Option extends Clib_Collection_Admin_Goods
{

	/**
	 * {@inheritdoc}
	 */
	protected $valueModel = 'goods_option';

	protected function construct()
	{
		$this->addFilter('goods_option.go_is_deleted', '1', '<>');
	}

}
