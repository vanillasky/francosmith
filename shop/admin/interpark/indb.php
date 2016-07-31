<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

function reReferer($except, $request){
	$vars = '&' . getVars($except, $request);
	return str_replace($vars, "", $_SERVER[HTTP_REFERER]) . $vars;
}

switch ($_GET[mode]){

	case "getClaimReqList":
	case "getClaimList":

		header("Content-type: text/html; charset=euc-kr");

		ob_start();
		if ($_GET[mode] == "getClaimReqList") $whr = "and step='r'";
		else $whr = "and step='c'";

		$res = $db->query("select * from ".INPK_CLAIM." where ordno='{$_GET['ordno']}' {$whr} order by clmsno desc");
		$json_var = array();

		### 목록
		$json_var = array();
		$list_tmp = &$json_var;
		while ($data=$db->fetch($res, 'assoc'))
		{
			$item = &$data['item'];
			$sRes = $db->query("select * from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}'");
			while ($sData=$db->fetch($sRes, 'assoc'))
			{
				$gItem = $db->fetch("select goodsnm, opt1, opt2, addopt from ".GD_ORDER_ITEM." where sno='{$sData['item_sno']}'");
				$sData['goodsnm'] = $gItem['goodsnm'];
				if ($gItem['opt1']) $sData['goodsnm'] .= "[{$gItem['opt1']}" . ($gItem['opt2'] ? "/{$gItem['opt2']}" : "") . "]";
				if ($gItem['addopt']) $sData['goodsnm'] .= "<div>[" . str_replace("^","] [",$gItem[addopt]) . "]</div>";
				$item[] = $sData;
			}
			$list_tmp[] = $data;
		}
		ob_end_clean();

		include dirname(__FILE__)."/../../lib/json.class.php";
		$json = new Services_JSON();
		$output = $json->encode($json_var);

		echo $output;

		exit;
		break;

	case "getOrderItem":

		header("Content-type: text/html; charset=euc-kr");

		ob_start();
		$res = $db->query("select * from ".GD_ORDER_ITEM." where ordno='{$_GET['ordno']}'");
		$json_var = array();

		### 목록
		$json_var = array();
		$list_tmp = &$json_var;
		while ($data=$db->fetch($res, 'assoc'))
		{
			list($data['outOfStock']) = $GLOBALS['db']->fetch("select count(itmsno) from ".INPK_CLAIM_ITEM." where item_sno='{$data['sno']}' and clm_rsn_tpnm='판매자품절'");
			$list_tmp[ $data['sno'] ] = $data;
		}
		ob_end_clean();

		include dirname(__FILE__)."/../../lib/json.class.php";
		$json = new Services_JSON();
		$output = $json->encode($json_var);

		echo $output;

		exit;
		break;

	case "isClaim": # 클레임여부 출력

		header("Content-type: text/html; charset=euc-kr");
		$cnt = 0;
		$res = $db->query("select c.step, i.clm_statnm, i.inpk_ordseq, i.clm_qty from ".INPK_CLAIM." c left join ".INPK_CLAIM_ITEM." i on c.clmsno=i.clmsno where ordno='{$_GET['ordno']}'");
		while ($data=$db->fetch($res, 'assoc'))
		{
			if ($data['step'] == 'r' && in_array($data['clm_statnm'], array('거부', '요청철회')));
			else if ($data['step'] == 'r' && $data['clm_statnm'] == '승인')
			{
				list($itmsno) = $db->fetch("select itmsno from ".INPK_CLAIM." c left join ".INPK_CLAIM_ITEM." i on c.clmsno=i.clmsno where ordno='{$_GET['ordno']}' and c.step='c' and i.clm_statnm='클레임취소' and i.inpk_ordseq='{$data['inpk_ordseq']}' and i.clm_qty='{$data['clm_qty']}'");
				if ($itmsno);
				else $cnt++;
			}
			else if ($data['step'] == 'c' && $data['clm_statnm'] == '클레임취소');
			else $cnt++;
		}
		if ($cnt > 0) $out = '<font style="color:red; background-color:#FFE792; padding:1px 2px; margin-left:5px;">클레임</font>';
		else $out = '';
		echo $out;

		break;

}

switch ($_POST[mode]){

	case "set":

		ob_start();
		$confFile = "../../conf/interparkOpenStyle.php";
		if (file_exists($confFile))
		{
			include $confFile;
			if (is_array($inpkOSCfg)){
				$inpkOSCfg = array_map("stripslashes",$inpkOSCfg);
				$inpkOSCfg = array_map("addslashes",$inpkOSCfg);
			}
		}
		$inpkOSCfg = array_merge($inpkOSCfg,$_POST['inpkOSCfg']);
		unset($inpkOSCfg['use']);
		if (isset($_POST['inpkOSCfg']['ippSubmitYn']) === false) unset($inpkOSCfg['ippSubmitYn']);

		$qfile->open($confFile);
		$qfile->write("<? \n");
		$qfile->write("\$inpkOSCfg = array( \n");
		$qfile->write("'use' => '" . ($inpkOSCfg['entrNo'] != '' && $inpkOSCfg['ctrtSeq'] != '' ? 'Y' : 'N') . "', \n");
		foreach ($inpkOSCfg as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();
		@chmod($confFile,0707);
		

		## 특이사항 저장
		$qfile->open("../../conf/interpark_spcaseEd.php");
		$qfile->write(stripslashes($_POST['spcaseEd']));
		$qfile->close();
		ob_end_clean();
		break;

	case "link":

		if (!$_POST[returnUrl]) $_POST[returnUrl] = reReferer('chk,query', $_POST);
		if ($_POST['isall'] == 'Y' && $_POST['query'])
		{
			$_POST['query'] = stripslashes($_POST['query']);
			$res = $db->query($_POST['query']);
			while ($data=$db->fetch($res)){
				if ($data[inpk_prdno] == '') $db->query("update ".GD_GOODS." set inpk_dispno='{$_POST[sinpk_dispno]}' where goodsno='{$data['goodsno']}'");
			}
		}
		else {
			foreach ($_POST['chk'] as $goodsno) $db->query("update ".GD_GOODS." set inpk_dispno='{$_POST[sinpk_dispno]}' where goodsno='{$goodsno}'");
		}
		break;

	case "unlink":

		if (!$_POST[returnUrl]) $_POST[returnUrl] = reReferer('chk,query', $_POST);
		if ($_POST['isall'] == 'Y' && $_POST['query'])
		{
			$_POST['query'] = stripslashes($_POST['query']);
			$res = $db->query($_POST['query']);
			while ($data=$db->fetch($res)){
				if ($data[inpk_prdno] == '') $db->query("update ".GD_GOODS." set inpk_dispno='' where goodsno='{$data['goodsno']}'");
			}
		}
		else {
			foreach ($_POST['chk'] as $goodsno) $db->query("update ".GD_GOODS." set inpk_dispno='' where goodsno='{$goodsno}'");
		}
		break;

}

if (!$_POST[returnUrl]) $_POST[returnUrl] = $_SERVER[HTTP_REFERER];
go($_POST[returnUrl]);

?>