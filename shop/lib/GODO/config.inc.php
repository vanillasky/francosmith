<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 */

/**
 * magic quote gpc 설정 여부
 */
define('G_CONST_MAGIC_QUOTES',	get_magic_quotes_gpc() ? true : false);

/**
 * 현재 시간 (타임스탬프)
 */
define('G_CONST_NOW',			time());

/**
 * 스크립트 시작 시간 (마이크로 타임)
 */
define('G_CONST_SCRIPT_START',	microtime());

/**
 * 문자열 구분자
 */
define('G_STR_DIVISION',		'|!|');

/**
 * document root
 */
if (defined('SHOPROOT'))
	define('G_CONST_DOCROOT',	SHOPROOT);
else
	define('G_CONST_DOCROOT',	realpath(dirname(__FILE__).'/../../'));


/**
 * 개발자 모드
 */
define('G_CONST_DEVELOPER_MODE',	false);

// include path.
$_inc_path = explode(PATH_SEPARATOR, ini_get('include_path'));
$_inc_path[] = SHOPROOT.'/lib';
$_inc_path[] = SHOPROOT.'/lib/pear';
$_inc_path[] = SHOPROOT.'/lib/GODO';

ini_set("register_globals",0);
ini_set('include_path', implode(PATH_SEPARATOR, $_inc_path));

// config.
$_CFG = array();

$_CFG['global']['charset'] = 'euc-kr';	// 타 캐릭터셋 호환 되지 않음
$_CFG['global']['use_security_filter'] = true;
$_CFG['global']['use_custom_error_handler'] = false;
$_CFG['global']['strip_requests_slashes'] = false;	// 수정 금지
$_CFG['global']['report_error_level'] = version_compare(PHP_VERSION, '5.3.0' ,'>=') ? E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED : E_ALL ^ E_NOTICE ^ E_WARNING;

$_CFG['global']['use_output_compression'] = false;
$_CFG['global']['output_compression_level'] = 3;  // 0 (no compression) to 9 (most compression)

$_CFG['session']['use_db'] = false;
$_CFG['session']['savepath'] = 'session_tmp';
$_CFG['session']['lifetime'] = 18000;	// 5시간(초)

$_CFG['db']['persistent_connect'] = false;
$_CFG['db']['use_cache'] = false;

//
unset($_inc_path);

/**
 * 상품분류 연결방식 전환 여부
 */
define('_CATEGORY_NEW_METHOD_',	is_file(SHOPROOT.'/conf/category_new_method') ? true : false);
?>