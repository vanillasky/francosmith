<?php
include dirname(__FILE__) . '/../lib.php';

$totalCount = $receiveRefuseCount = 0;

switch($_POST['mode']){
	case 'smsBatch':
		$totalQuery = $receiveRefuseQuery = '';

		if($_POST['type'] == 'select'){
			if($_POST['parameter']){
				$arrayMno = array();
				$arrayMno = array_filter(explode("|", $_POST['parameter']));
				$totalCount = count($arrayMno);
				$receiveRefuseQuery	= "SELECT mobile FROM " . GD_MEMBER . " WHERE m_no in ('".implode("','", $arrayMno)."') and sms = 'n' ";
			}
		}
		else if ($_POST['type'] == 'query'){
			if($_POST['parameter']){
				$_POST['parameter'] = iconv('utf-8', 'euc-kr', $_POST['parameter']);
				$_POST['parameter'] =  (get_magic_quotes_gpc()) ? stripslashes($_POST['parameter']) : $_POST['parameter'];
				$totalQuery = preg_replace('/\*/', 'mobile', $_POST['parameter']);
				$res = $db->query($totalQuery);
				$totalCount = $db->count_($res);

				$receiveRefuseQuery = preg_replace('/(order)(\s|\w|\D|\d)+/i', '', $totalQuery);
				$whereType = (preg_match('/where/i', $receiveRefuseQuery)) ? 'AND' : 'WHERE';
				$receiveRefuseQuery .= $whereType . " sms = 'n' ";
			}
		}
		else {

		}

		if($receiveRefuseQuery){
			$res = $db->query($receiveRefuseQuery);
			$receiveRefuseCount = $db->count_($res);
		}
	break;

	case 'powermail' : case 'individualEmail' :
		$checkBox = array();
		if($_POST['type'] == 'select'){
			$checkBoxMno = array_values(array_filter(explode("|", $_POST['parameter'])));
			$totalCount = count($checkBoxMno);
			$res = $db->query("SELECT m_no FROM ".GD_MEMBER." WHERE m_no IN ('".implode("','", $checkBoxMno)."') AND mailling='n' ");
			$receiveRefuseCount = $db->count_($res);
		}
		else if($_POST['type'] == 'query'){
			$query = '';
			$_POST['parameter'] = iconv('utf-8', 'euc-kr', $_POST['parameter']);
			$query = preg_replace('/(order)(\s|\w|\D|\d)+/i', '', $_POST['parameter']);
			$query =  (get_magic_quotes_gpc()) ? stripslashes($query) : $query;
			$res = $db->query($query);
			$totalCount = $db->count_($res);
			$whereType = (preg_match('/where/i', $query)) ? 'AND' : 'WHERE';
			$query = $query . $whereType ." mailling = 'n' ";
			$res = $db->query($query);
			$receiveRefuseCount = $db->count_($res);
		}
		else {

		}
	break;
}

echo $totalCount . ',' . $receiveRefuseCount;
?>