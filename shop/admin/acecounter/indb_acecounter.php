<?
include "../lib.php";
include_once dirname(__FILE__)."/../../lib/lib.enc.php";
require_once("../../lib/qfile.class.php");
require_once("../../lib/upload.lib.php");
## 글로벌변수 $acecounter를 읽어온다. 
include "../../conf/config.php";
@include "../../conf/config.acecounter.php"; 
@include "../../conf/config.acecounter.constant.php";
$qfile = new qfile();

$acecounter['shopno'] = $godo['sno']; 
if (!$acecounter['status_apply']) $acecounter['status_apply'] = 'N'; 
if (!$acecounter['status_use']) $acecounter['status_use'] = 'N'; 
if (!$acecounter['use']) $acecounter['use'] = 'N'; 

$bWrite = false; 
if (!$acecounter['id'] || !$acecounter['pass']) $bWrite = true; 
if (!$acecounter['id']) $acecounter['id'] = "ac".$godo['sno']; 

## if (!$acecounter['pass']) $acecounter['pass'] = "gd".rand()%10000;
## 혹시모를 경우를 대비해서
if (!$acecounter['pass']) $acecounter['pass'] = "gd".substr($godo['sno'],0,4);
if ($bWrite) {
	$qfile->open("../../conf/config.acecounter.php");
	$qfile->write("<? \n");
	$qfile->write("\$acecounter = array( \n");
	foreach ($acecounter as $k=>$v) $qfile->write("'$k' => '$v', \n");
	$qfile->write(") \n;");
	$qfile->write("?>");
	$qfile->close();
	@chmod("../../conf/config.acecounter.php",0707);
}

$mode = ($_POST[mode]) ? $_POST[mode] : $_GET[mode];
$version = ($_POST[version]) ? $_POST[version] : $_GET[version];
unset($_POST[mode]); unset($_POST[x]); unset($_POST[y]);

switch ($mode){
	case "acecounter":
		$acecounter_val = Array(); 
		if ($acecounter) {
			$acecounter = array_map("stripslashes",$acecounter);
			$acecounter = array_map("addslashes",$acecounter);
		}
		$acecounter_val = $_POST['acecounter'];

		if ($acecounter_val['use'] && $acecounter_val['use'] != $acecounter['use']) {
			$acecounter['use'] = $acecounter_val['use']; 
			if ($acecounter) {
				$qfile->open("../../conf/config.acecounter.php");
				$qfile->write("<? \n");
				$qfile->write("\$acecounter = array( \n");
				foreach ($acecounter as $k=>$v) {					
					$qfile->write("'$k' => '$v', \n");
				}
				$qfile->write(") \n;");
				$qfile->write("?>");
				$qfile->close();
				@chmod("../../conf/config.acecounter.php",0707);
			}
		}
		break;
	case "apply":
		# 에이스 카운터 를 신청한다. 
		if ($acecounter['ver_use'] == $version) {
			msg("동일한 버전 신청은 안됩니다. ".$arr_out[1], -1); 
		}
		
		$target_url = $APPLY_URL; 
		$target_url .= "?";
		/*
		$target_url .= "shopno=".godoConnEncode($godo['sno']);
		$target_url .= "&uid=".godoConnEncode($acecounter['id']); 
		$target_url .= "&pwd=".godoConnEncode($acecounter['pass']); 
		$target_url .= "&domain=".godoConnEncode($_SERVER['HTTP_HOST']);
		$target_url .= "&ver=".godoConnEncode($version); 
		*/
		
		$acecounter['status_apply'] = 'Y'; 
		$acecounter['ver_apply'] = $version;
		
		$qfile->open("../../conf/config.acecounter.php");
		$qfile->write("<? \n");
		$qfile->write("\$acecounter = array( \n");
		foreach ($acecounter as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();
		@chmod("../../conf/config.acecounter.php",0707);
		
		$target_url .= "shopno=".$godo['sno'];
		$target_url .= "&uid=".$acecounter['id']; 
		$target_url .= "&pwd=".$acecounter['pass']; 
		$target_url .= "&domain=".$_SERVER['HTTP_HOST'];
		$target_url .= "&ver=".$version; 
		$target_url .= "&notify_url=".urlencode("http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/admin/acecounter/apply_result.php");
		$target_url .= "&notify_pay_url=".urlencode("http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/admin/acecounter/pay_result.php");		
		//$target_url .= "&notify_tran_url=".urlencode("http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/admin/acecounter/tran_result.php");
		$out = readurl($target_url);
		
		$arr_out = explode("|", $out); 
		if ($arr_out[0] == 'SUCC') {			
			msg("에이스카운터 서비스 신청 완료 ",-1);
		} else {
			$acecounter['status_apply'] = 'N'; 
			$acecounter['ver_apply'] = "";
		
			$qfile->open("../../conf/config.acecounter.php");
			$qfile->write("<? \n");
			$qfile->write("\$acecounter = array( \n");
			foreach ($acecounter as $k=>$v) $qfile->write("'$k' => '$v', \n");
			$qfile->write(") \n;");
			$qfile->write("?>");
			$qfile->close();
			@chmod("../../conf/config.acecounter.php",0707);			
			msg("에이스카운터 서비스 신청 실패 : ".$arr_out[1], -1); 
		}
		exit; 
		break; 
	case "get_result":
		# 신청 결과를 조회한다. 
		$target_url = $GET_APPLY_URL; 
		$target_url .= "?";
		//$target_url .= "shopno=".godoConnEncode($acecounter['shopno']);
		//$target_url .= "&uid=".godoConnEncode($uid);
		$target_url .= "shopno=".$acecounter['shopno'];
		$target_url .= "&uid=".$acecounter['id'];
		$out = readurl($target_url);
		## 여기에서 config.acecounter.php 에 저장한다. 
		$arr_out = explode("|", $out); 

		if ($arr_out[0] == $acecounter['id']) {
			$acecounter['gcode'] = $arr_out[1]; 
			$acecounter['status_use'] = 'Y'; 
			$acecounter['use'] = 'Y'; 
			$acecounter['ver_use'] = $arr_out[2]; 
			$acecounter['start'] = $arr_out[4]; 
			$acecounter['end'] = $arr_out[5]; 
			
			$acecounter['status_apply'] = 'N'; 
			$acecounter['ver_apply'] = ''; 
			
			//debug($acecounter);
			
			$qfile = new qfile();
			$qfile->open("../../conf/config.acecounter.php");
			$qfile->write("<? \n");
			$qfile->write("\$acecounter = array( \n");
			foreach ($acecounter as $k=>$v) $qfile->write("'$k' => '$v', \n");
			$qfile->write(") \n;");
			$qfile->write("?>");
			$qfile->close();	
			@chmod("../../conf/config.acecounter.php",0707);
		}

		break; 
	case "pay_result":
		$target_url = $GET_PAY_URL; 
		$target_url .= "?";
		// FOR TEST
		//$target_url .= "uid=".godoConnEncode($uid);
		//$target_url .= "&sno=".godoConnEncode($sno);
		$target_url .= "uid=".$acecounter['id'];
		$target_url .= "&sno=".$sno;
		$out = readurl($target_url);
		//debug($target_url);
		//debug($out);
		## 여기에서 config.acecounter.php 에 저장한다. 
		$arr_out = explode("|", $out); 
		//debug($arr_out);
		//exit; 
		if ($arr_out[0] == $acecounter['id']) {
			$acecounter['use'] = 'Y'; 
			$acecounter['recent_pay'] = $arr_out[2]; 
			$acecounter['start'] = $arr_out[3]; 
			$acecounter['end'] = $arr_out[4]; 
			
			//debug($acecounter);
			$qfile = new qfile();
			$qfile->open("../../conf/config.acecounter.php");
			$qfile->write("<? \n");
			$qfile->write("\$acecounter = array( \n");
			foreach ($acecounter as $k=>$v) $qfile->write("'$k' => '$v', \n");
			$qfile->write(") \n;");
			$qfile->write("?>");
			$qfile->close();	
			@chmod("../../conf/config.acecounter.php",0707);	
		}
		break; 
}

go($_SERVER[HTTP_REFERER]);

?>