<?php
function newAreaTotalCount()
{
	global $db;

	$totalRes = $db->query(" SELECT areaNo FROM " . GD_AREA_DELIVERY . " ");
	$totalCount = $db->count_($totalRes);

	return $totalCount;
}

function newAreaLimitCheck()
{
	$msg = "";
	$totalCount = newAreaTotalCount();
	if($totalCount > 999){ 
		$msg = '주소지 등록은 1,000개 까지 가능합니다.';
	}

	return $msg;
}

function newAreaPayLimitCheck($pay)
{
	if((int)$pay > 10000000) $msg = '추가배송비는 10,000,000원까지 입력할 수 있습니다.';

	return $msg;
}

function newAreaProcess($areaName)
{
	$_newAreaDelivery	= array();
	$_newAreaDelivery	= @explode(' ', $areaName);
	$_newAreaDelivery	= @array_values(@array_filter($_newAreaDelivery));
	$areaSido			= @array_shift($_newAreaDelivery);
	$areaGugun			= @array_shift($_newAreaDelivery);
	$areaEtc			= @implode(' ', $_newAreaDelivery);
	unset($_newAreaDelivery);

	return array($areaSido, $areaGugun, $areaEtc);
}

function newAreaOnlyNumCheck($newArea)
{
	if(preg_match('/[^0-9]/', trim($newArea))){
		msg('가격은 숫자만 입력가능합니다.\n콤마(,) 원(￦) 등 기호는 제외하고 등록해주세요.', -1);
		exit;
	}
}

function funcExplode($r_area)
{
	global $checkSido;

	$_r_areaValue = @array_filter(@explode(",", $r_area));
	foreach($_r_areaValue as $key => $value){
		$r_areaValue = explode(" ", trim($value));
		list($areaSido) = @array_keys(@preg_grep('/' . $r_areaValue[0] . '/', $checkSido));

		$resultArea[$key]['sido'] = $areaSido;
		$resultArea[$key]['gugun'] = $r_areaValue[1];

		array_shift($r_areaValue);
		array_shift($r_areaValue);

		if($r_areaValue[0]){
			$resultArea[$key]['etc'] = implode(" ", $r_areaValue);
		}
	}

	return $resultArea;
}

function funcHyphen($_zipcode)
{
	$zipcode = substr($_zipcode,0,3) . '-' . substr($_zipcode,3,3);
	
	return $zipcode;
}

function newAreaExistCheck($areaSido, $areaGugun, $areaEtc)
{
	global $db;

	$query = " 
		SELECT areaNo FROM 
			" . GD_AREA_DELIVERY . "
		WHERE 
			areaSido='".trim($areaSido)."' and 
			areaGugun='".trim($areaGugun)."' and 
			areaEtc='".trim($areaEtc)."' 
		LIMIT 1";
		
	list($data) = $db->_select($query);

	return $data['areaNo'];
}

function newAreaIconv($euckrString)
{
	return iconv('UTF-8', 'EUC-KR', $euckrString);
}

function newAreaSkinCheck()
{
	global $cfg;

	$skinFileName = '/order/order.htm';
	$skinFilePath = dirname(__FILE__) . "/../../data/skin/" . $cfg['tplSkin'] . $skinFileName;

	if(@is_file($skinFilePath)){
		$fp = fopen($skinFilePath, "r");
		$skinString = @fread($fp, @filesize($skinFilePath));
		fclose($fp);
	}

	if(preg_match("/var road_address = form.road_address.value;/", $skinString)){
		return true;
	}

	return false;
}
?>