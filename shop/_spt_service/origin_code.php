<?php
/**
	쇼플 중계서버에서 전송한 원산지 지역 코드 정보를 갱신
 */
set_time_limit(0);
ini_set('memory_limit',-1);

define(_SHOPLE_SOAP_NAME_SPACE_,	'godo.shople');

require_once('./common.inc.php');
$shople = Core::loader('shople');

$soap_server = new nusoap_server();
$soap_server->configureWSDL(_SHOPLE_SOAP_NAME_SPACE_, 'urn:'. _SHOPLE_SOAP_NAME_SPACE_);

$soap_server->register(
	'setOriginCode',
	array('data' => 'xsd:String'),
	array('return' => 'xsd:String'),
	'uri:'. _SHOPLE_SOAP_NAME_SPACE_ ,
	'uri:'. _SHOPLE_SOAP_NAME_SPACE_ .'#setOriginCode'
);

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$soap_server->service($HTTP_RAW_POST_DATA);


function _setVar($str) {
	return base64_encode(serialize($str));
}

function _getVar($str) {
	return unserialize(base64_decode($str));
}


function setOriginCode($data='') {

	global $db;

	$arRow = _getVar($data);
	$arRow_keys = array_keys($arRow);

	$db->query("TRUNCATE TABLE ".GD_SHOPLE_ORIGIN_CODE);

	for ($i=0,$max=sizeof($arRow_keys);$i<$max;$i++) {
		$row = $arRow[ $arRow_keys[$i] ];

		$query = '
			INSERT INTO '.GD_SHOPLE_ORIGIN_CODE.' SET
				country = \''.$row['country'].'\',
				area = \''.$row['area'].'\',
				name = \''.$row['name'].'\',
				value  = \''.$row['value'].'\'
			';
		$db->query($query);

	}

	return '';
}
?>
