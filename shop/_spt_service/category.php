<?php
/**
	쇼플 중계서버에서 전송한 카테고리 정보를 갱신
 */
set_time_limit(0);
ini_set('memory_limit',-1);

define(_SHOPLE_SOAP_NAME_SPACE_,	'godo.shople');

require_once('./common.inc.php');
$shople = Core::loader('shople');

$soap_server = new nusoap_server();
$soap_server->configureWSDL(_SHOPLE_SOAP_NAME_SPACE_, 'urn:'. _SHOPLE_SOAP_NAME_SPACE_);

$soap_server->register(
	'setCategory',
	array('data' => 'xsd:String'),
	array('return' => 'xsd:String'),
	'uri:'. _SHOPLE_SOAP_NAME_SPACE_ ,
	'uri:'. _SHOPLE_SOAP_NAME_SPACE_ .'#setCategory'
);

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$soap_server->service($HTTP_RAW_POST_DATA);


function _setVar($str) {
	return base64_encode(serialize($str));
}

function _getVar($str) {
	return unserialize(base64_decode($str));
}


function setCategory($data='') {

	global $db;

	$arRow = _getVar($data);
	$arRow_keys = array_keys($arRow);

	for ($i=0,$max=sizeof($arRow_keys);$i<$max;$i++) {
		$row = $arRow[ $arRow_keys[$i] ];

		$query = '';

		switch ($row['status']) {
			case 'D' :
				$query = 'DELETE FROM '.GD_SHOPLE_CATEGORY.' WHERE openmarket = \'11st\' AND dispno = \''.$row['dispNo'].'\'';
				break;
			case 'U' :
				$query = 'UPDATE '.GD_SHOPLE_CATEGORY.' SET name = \''.$row['dispNm'].'\' WHERE openmarket = \'11st\' AND dispno = \''.$row['dispNo'].'\'';
				break;
			case 'S' :
			case 'I' :
				$query = 'INSERT INTO '.GD_SHOPLE_CATEGORY.' SET openmarket = \'11st\', depth = \''.$row['depth'].'\', name = \''.$row['dispNm'].'\', dispno  = \''.$row['dispNo'].'\', p_dispno  = \''.$row['parentDispNo'].'\'';
				break;
		}

		if ($query) {
			$db->query($query);
		}
	}

	return $data;
}
?>
