<?

include "../../../lib/library.php";
include "../../../conf/config.php";
//include "../../../conf/pg.$cfg[settlePg].php";

// 투데이샵 사용중인 경우 PG 설정 교체
resetPaymentGateway();

if($pg['serviceType'] == "test"){
	$pg['id']	= "t".$pg['id'];
	echo "<SCRIPT language=JavaScript src='http://pgweb.dacom.net:7085/js/DACOMEscrow.js'></SCRIPT>".chr(10);
}else{
	echo "<SCRIPT language=JavaScript src='http://pgweb.dacom.net/js/DACOMEscrow.js'></SCRIPT>".chr(10);
}
echo "<SCRIPT language=JavaScript>var ResultCode = checkDacomESC ('".$pg['id']."', '".$_GET['ordno']."', '');</SCRIPT>".chr(10);
echo "<SCRIPT language=JavaScript>if( ResultCode != '10002' ) document.location.replace('{$_GET[ret_path]}')</SCRIPT>".chr(10);
?>