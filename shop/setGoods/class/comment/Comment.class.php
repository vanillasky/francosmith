<?php
/**
 * Created on 2012-08-28
 *
 * Filename	: Comment.class.php
 * Comment 	: CLASS
 * Function	: 
 * History	: sf2000 by v1.0 최소작성
 * 
 **/
?>
<?
require_once dirname(__FILE__) . "/../../../setGoods/class/framework/BaseObject.class.php";

class Comment extends BaseObject {
	function Comment() {
		parent::initProperties(
			array(	'idx',
					'cody_idx',
					'm_no',
					'nickname',
					'password',
					'memo',
					'regdate'
			)
		);
	}
}
?>







