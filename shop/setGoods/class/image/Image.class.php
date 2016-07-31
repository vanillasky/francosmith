<?php
/**
 * Created on 2012-08-28
 *
 * Filename	: Image.class.php
 * Comment 	: CLASS
 * Function	: 
 * History	: sf2000 by v1.0 최소작성
 * 
 **/
?>
<?
require_once dirname(__FILE__) . "/../../../setGoods/class/framework/BaseObject.class.php";

class Image extends BaseObject {
	function Image() {
		parent::initProperties(
			array(	'idx', 
					'type',
					'tem_index',
					'cody_idx',
					'goods_idx',
					'name',
					'file_name',
					'file_alt',
					'source',
					'price',
					'regdate'					
			)
		);
	}
}
?>







