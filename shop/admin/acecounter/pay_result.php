<?
include_once dirname(__FILE__)."/../../lib/library.php";
include_once dirname(__FILE__)."/../../lib/lib.enc.php";

require_once("../../lib/qfile.class.php");
require_once("../../lib/upload.lib.php");
## �۷ι����� $acecounter�� �о�´�. 
@include "../../conf/config.acecounter.php"; 
@include "../../conf/config.acecounter.constant.php"; 

$uid = $_GET['uid']; 	// decoding �ؾ� �ϳ� ? 
$sno = $_GET['sno']; 	// decoding �ؾ� �ϳ� ? 

## ���θ��� ���̽�ī���Ϳ� �����ؾ� �����Ѵ�.
if (strlen($uid) > 0 
	&& strlen($sno) > 0 
	&& $acecounter['id'] == $uid 
	) 
{
	$target_url = $GET_PAY_URL; 
	$target_url .= "?";
	// FOR TEST
	//$target_url .= "uid=".godoConnEncode($uid);
	//$target_url .= "&sno=".godoConnEncode($sno);
	$target_url .= "uid=".$uid;
	$target_url .= "&sno=".$sno;
	$out = readurl($target_url);
	//debug($target_url);
	//debug($out);
	## ���⿡�� config.acecounter.php �� �����Ѵ�. 
	$arr_out = unserialize(base64_decode($out)); 
	//debug($arr_out);
	
	if ($arr_out['s_userid'] == $acecounter['id']) {
		$acecounter['use']			= 'Y'; 
		$acecounter['recent_pay']	= $arr_out['total_pay']; 
		$acecounter['start']		= $arr_out['service_start']; 
		$acecounter['end']			= $arr_out['service_end']; 
		$acecounter['status_use']	= $arr_out['status']; 
		
		// ���� �����
		if($arr_out['ver'] && $arr_out['ver'] != $acecounter['ver_use']){
			$acecounter['ver_use'] = $arr_out['ver'];
		}

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
		echo "SUCC";
	} else {
		echo "FAIL"; 
		exit; 
	}
}
?>