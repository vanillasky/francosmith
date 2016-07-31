<?php
class Clib_Form_Admin_Goods_Search extends Clib_Form_Abstract
{
	// dont't create construct & destruct.

	protected function initialize()
	{
		/*
		 * $this->input(columnName, attributes, $validationOption);
		 *
		 * $validationOption like this :
		 *
		 * array(
		 *       'required' => Clib_Validation::REQUIRED_YES,
		 *       'type' => Clib_Validation::TYPE_STRING,
		 *       'length' => 9,
		 *       'length_type' => Clib_Validation::TYPE_LENGTH_EQUAL
		 * );
		 */

		$this->input('sword');
		$this->select('skey', array('value' => array(
				'상품명' => 'goods.goodsnm',
				'상품번호' => 'goods.goodsno',
				'별도관리코드' => 'goods.goodscd',
				'제조사' => 'goods.maker',
				'유사검색어' => 'goods.keyword',
				'이벤트문구' => 'goods.naver_event',
			)));

		$brandnoValue = Clib_Application::getCollectionClass('goods_brand')->setOrder('sort')->load();
		$brandnoValue->unshiftItem(Clib_Application::getModelClass('goods_brand')->setData(array('brandnm' => '- 브랜드 선택 -', )));
		$this->select('brandno', array('value' => $brandnoValue));

		$this->radio('open', array(
			'default_value' => '',
			'value' => array(
				'전체' => '',
				'진열상품' => '1',
				'미진열상품' => '0'
			)
		));

		$this->radio('soldout', array(
			'default_value' => '',
			'value' => array(
				'전체' => '',
				'품절' => '1',
				'품절상품제외' => '0'
			)
		));

		$this->radio('discount', array(
			'default_value' => '',
			'value' => array(
				'전체' => '',
				'할인상품' => '1',
				'할인상품제외' => '2'
			)
		));

		$this->input('origin');

		$this->radio('stock_type', array(
			'default_value' => 'product',
			'value' => array(
				'상품재고(품목재고 합)' => 'product',
				'품목재고' => 'item',
			)
		));

		$this->input('stock', array('style'=>'width:50px'));
	}

}
