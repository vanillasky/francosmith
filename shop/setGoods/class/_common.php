<?php
/**
 * Created on 2012-07-23
 *
 * Filename	: /class/_common.php
 * Comment 	: 기본설정 define
 * Function	: 
 * History	: sf2000 by v1.0 최소작성
 * 
 **/
?>
<?
include dirname(__FILE__) . "/../../../shop/conf/db.conf.php";
error_reporting( version_compare(PHP_VERSION, '5.3.0' ,'>=') ? E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED : E_ALL ^ E_NOTICE ^ E_WARNING );

define('Language', 'Korean');
define('DB_TYPE', 'MySQL');
define('DB_HOST', $db_host);
define('DB_DATABASENAME', $db_name);
define('DB_USER', $db_user);
define('DB_PASSWD', $db_pass);

### 스킨 패스
define('IMG_DIR', '/shop/skin/');
define('SKIN_DIR', $_SERVER[DOCUMENT_ROOT] . '/shop/skin/');
define('TEMPLATE_DIR', $_SERVER[DOCUMENT_ROOT] . "/shop/Template_/_compiles/");

define("FatalErrorMsg", "I'm sorry.It is a serious error. Please contact your system administrator.");
?>