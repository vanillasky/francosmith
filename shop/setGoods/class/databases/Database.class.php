<?php
/**
 * Created on 2012-07-23
 *
 * Filename	: Database.class.php
 * Comment 	: DB ���� class
 * Function	: 
 * History	: sf2000 by v1.0 �ּ��ۼ�
 * 
 **/
?>
<?php
require_once dirname(__FILE__) . "/../../../setGoods/class/_common.php";
require_once dirname(__FILE__) . "/../../../setGoods/class/databases/MySQL.class.php";
			
class Database extends MySQL {
	function Database() {
		if (DB_TYPE == 'MySQL'){
			parent::MySQL(DB_HOST, DB_USER, DB_PASSWD, DB_DATABASENAME);	
		}else if(DB_TYPE == 'ORACLE'){
			//���� �������� �׽��ϴ�.
		}
	}
}
?>