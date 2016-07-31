<?php
/**
 * Created on 2012-08-28
 *
 * Filename	: Cody.class.php
 * Comment 	: CLASS
 * Function	: 
 * History	: sf2000 by v1.0 최소작성
 * 
 **/
?>
<?
require_once dirname(__FILE__) . "/../../../setGoods/class/framework/BaseObject.class.php";

class Cody extends BaseObject {
	function Cody() {
		parent::initProperties(
			array(	'idx',    
					'member_id',
					'cate_idx',
					'board_idx',			
					'tem_idx',	
					'setCost',
					'cody_name',
					'memo',		
					'CD_content',
					'member_name',			
					'thumnail_name',
					'state',
					'like_cnt',			
					'recody_cnt',			
					'regdate',
					'goodsState',
					'goodsStateRegdate'

			)
		);
	}
}
?>







