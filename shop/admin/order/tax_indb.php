<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

$mode = ($_POST[mode]) ? $_POST[mode] : $_GET[mode];
unset($_POST[mode]); unset($_POST[x]); unset($_POST[y]);

switch ($mode){

	case "tax":

		include "../../conf/config.pay.php";

		# 인감이미지
		if (isset($_FILES['seal_up'])){
			$_BGFILES = array( 'seal_up' => $_FILES['seal_up'] );
			$userori = array( 'seal' => 'seal' . strrChr( $_FILES['seal_up']['name'], "." ) );

			@include_once dirname(__FILE__) . "/../design/webftp/webftp.class_outcall.php";
			outcallUpload( $_BGFILES, '/img/common/', $userori );
			unset($_POST[seal_del]);
		}
		else $_POST[seal] = $set[tax][seal];

		$tmp['tax'] = &$_POST;
		$set = array_merge($set,$tmp);

		$qfile->open("../../conf/config.pay.php");
		$qfile->write("<? \n");
		foreach ($set as $k=>$v) foreach ($v as $k2=>$v2) $qfile->write("\$set['$k']['$k2'] = '$v2'; \n");
		$qfile->write("?>");
		$qfile->close();

		break;

	case "allmodify":

		foreach ($_POST[busino] as $k=>$v){
			$query = "
			update ".GD_TAX." set
				busino		= '{$_POST[busino][$k]}',
				company		= '{$_POST[company][$k]}',
				name		= '{$_POST[name][$k]}',
				service		= '{$_POST[service][$k]}',
				item		= '{$_POST[item][$k]}',
				address		= '{$_POST[address][$k]}',
				issuedate	= '{$_POST[issuedate][$k]}',
				goodsnm		= '{$_POST[goodsnm][$k]}',
				price		= '{$_POST[price][$k]}',
				supply		= '{$_POST[supply][$k]}',
				surtax		= '{$_POST[surtax][$k]}'
			where
				sno		= '$k'
			";
			$db->query($query);
		}

		break;

	case "delete":

		foreach ($_POST[chk] as $v){
			$query = "delete from ".GD_TAX." where sno = '$v'";
			$db->query($query);
		}

		break;

	case "agree":

		foreach ($_POST[chk] as $v){
			$query = "
			update ".GD_TAX." set
				step		= '1',
				agreedt		= now()
			where
				sno		= '$v'
			";
			$db->query($query);
		}

		break;

	case "print":

		$query = "
		update ".GD_TAX." set
			step		= '2',
			printdt		= now()
		where
			sno		= '$_GET[sno]'
		";
		$db->query($query);
		exit;

		break;

	case "request":

		include dirname(__FILE__)."/../../lib/json.class.php";
		$json = new Services_JSON();
		$getParam = $json->encode($_GET);
		echo "<script>parent.WRS.receive('{$getParam}');</script>";
		exit;

		break;

	case "isExists": case "putMerchant": case "putTaxbill": case "putSugiTaxbill": case "getTaxbill": case "getTaxsugiList": case "ccrTaxbill":

		header("Content-type: text/html; charset=euc-kr");
		include_once dirname(__FILE__)."/../../lib/tax.class.php";
		$etax = new eTax();
		$out = $etax->$mode(array_merge($_GET, $_POST));
		$rCode = ($out[0] ? $out[0] : 400);
		if (!preg_match("/^[true|false|join]/i",$out[1])) unset($out[1]);
		if (preg_match("/^false/i",$out[1])) $out[1] = trim(preg_replace("/^false[ |]*-[ |]*/i", "", $out[1]));

		if ($mode == 'isExists'){
			if ($out[1] == 'true' || $out[1] == 'join') echo $out[1];
			else header("Status: {$out[1]}", true, $rCode);
		}
		else if ($mode == 'putMerchant'){
			if ($out[1] == 'true') echo $out[1];
			else header("Status: {$out[1]}{RESAVE}", true, $rCode);
		}
		else if ($mode == 'putTaxbill'){
			if ($out[1] == 'true') echo "<b>{subject} 발행요청 <font color=0077B5>(결과: 처리성공!)</font></b>";
			else header("Status: <b>{subject} 발행요청 <font color=0077B5>(결과: 처리실패!)</font></b>" . ($out[1] ? "^{$out[1]}" : ""), true, $rCode);
		}
		else if ($mode == 'putSugiTaxbill'){
			if ($out[1] == 'true') echo "<b>발행요청 <font color=0077B5>(결과: 처리성공!)</font></b>";
			else header("Status: <b>발행요청 <font color=0077B5>(결과: 처리실패!)</font></b>" . ($out[1] ? " : {$out[1]}" : ""), true, $rCode);
		}
		else if ($mode == 'getTaxbill'){
			if (preg_match("/^true/i",$out[1])) echo trim(preg_replace("/^true[ |]*-[ |]*/i", "", $out[1]));
			else header("Status: {$out[1]}", true, $rCode);
		}
		else if ($mode == 'getTaxsugiList'){
			if (preg_match("/^true/i",$out[1])) echo trim(preg_replace("/^true[ |]*-[ |]*/i", "", $out[1]));
			else header("Status: [로딩 실패] {$out[1]}", true, $rCode);
		}
		else if ($mode == 'ccrTaxbill'){
			if (preg_match("/^true/i",$out[1])) echo trim(preg_replace("/^true[ |]*-[ |]*/i", "", $out[1]));
			else header("Status: {$out[1]}", true, $rCode);
		}
		echo ""; # 삭제마요
		exit;

		break;

}

go($_SERVER[HTTP_REFERER]);

?>