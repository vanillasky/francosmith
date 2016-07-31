<?
include_once dirname(__FILE__)."/../../lib/library.php";
include_once dirname(__FILE__)."/../../lib/lib.enc.php";

require_once("../../lib/qfile.class.php");
require_once("../../lib/upload.lib.php");

## 글로벌변수 $acecounter를 읽어온다. 
@include "../../conf/config.acecounter.php"; 
@include "../../conf/config.acecounter.constant.php"; 

$uid = $_GET['uid']; 	// decoding 해야 하나 ? ->> 그냥 보낼께요ㅎ
## 쇼핑몰의 에이스카운터와 동일해야 동작한다.

if (strlen($uid) > 0 
	&& $acecounter['id'] == $uid 
	&& strlen($acecounter['shopno'])>0 ) 
{
	$target_url = $GET_APPLY_URL; 
	$target_url .= "?";
	//$target_url .= "shopno=".godoConnEncode($acecounter['shopno']);
	//$target_url .= "&uid=".godoConnEncode($uid);
	$target_url .= "shopno=".$acecounter['shopno'];
	$target_url .= "&uid=".$uid;
	$out = readurl($target_url);
	//debug($acecounter);
	//debug($out);
	## 여기에서 config.acecounter.php 에 저장한다. 
	$arr_out = explode("|", $out); 

	//if ($arr_out[0] == $acecounter['id']) {
		$acecounter['gcode'] = $arr_out[1]; 
		$acecounter['status_use'] = $arr_out[2]; 
		$acecounter['use'] = 'Y'; 
		$acecounter['ver_use'] = $arr_out[3]; 
		$acecounter['start'] = $arr_out[5]; 
		$acecounter['end'] = $arr_out[6]; 		
		$acecounter['status_apply'] = 'N'; 
		$acecounter['ver_apply'] = ''; 

		$qfile = new qfile();
		$qfile->open("../../conf/config.acecounter.php");
		$qfile->write("<? \n");
		$qfile->write("\$acecounter = array( \n");
		foreach ($acecounter as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();	
		@chmod("../../conf/config.acecounter.php",0707);
		echo "SUCC";	
	//} else {
	//	echo "FAIL"; 
	//	exit; 
	//}
}
?>