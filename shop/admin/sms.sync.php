<?
// SMS ����ȭ 2012.02.23 wheeya

$sms_result = "";

ob_start();
include '../_godoConn/sms.php';
$sms_result = ob_get_contents();
ob_end_clean();

if($sms_result == 'OK'){
	echo "<script>".
		"alert('SMS ����ȭ�� ���������� ó���Ǿ����ϴ�.');".
		"parent.location.reload();</script>".
		"</script>";
}else{
	echo "<script>".
		"alert('SMS ����ȭ�� ���еǾ����ϴ�.\\n�ٽ� �ѹ� �õ��Ͻðų� �������ͷ� �����ֽñ� �ٶ��ϴ�.');".
		"parent.location.reload();</script>".
		"</script>";
}

?>