<?
// SMS 동기화 2012.02.23 wheeya

$sms_result = "";

ob_start();
include '../_godoConn/sms.php';
$sms_result = ob_get_contents();
ob_end_clean();

if($sms_result == 'OK'){
	echo "<script>".
		"alert('SMS 동기화가 정상적으로 처리되었습니다.');".
		"parent.location.reload();</script>".
		"</script>";
}else{
	echo "<script>".
		"alert('SMS 동기화가 실패되었습니다.\\n다시 한번 시도하시거나 고도고객센터로 문의주시기 바랍니다.');".
		"parent.location.reload();</script>".
		"</script>";
}

?>