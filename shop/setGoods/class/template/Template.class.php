<?php
/**
 * Created on 2012-09-03
 *
 * Filename	: Template.class.php
 * Comment 	: CLASS
 * Function	: 
 * History	: sf2000 by v1.0 최소작성
 * 
 **/
?>
<?
require_once dirname(__FILE__) . "/../../../setGoods/class/framework/BaseObject.class.php";

class Template extends BaseObject {
	function Template() {
		parent::initProperties(
			array(	'TP_idx',    
					'TP_name',
					'TP_group',
					'TP_id',
					'TP_state',
					'TP_regdate'
			)
		);
	}
}
?>







