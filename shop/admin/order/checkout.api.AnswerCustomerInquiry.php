<?php
/**
 * �߰輭���� ���, �亯���
 * @author sunny, oneorzero
 */
include "../_header.popup.php";
$naverCheckoutAPI = Core::loader('naverCheckoutAPI');
$inquiryNo = $_POST['inquiryNo'];
$AnswerContent = $_POST['AnswerContent'];

?>

<br>
�߰輭���� ��� �� ...<br>
<?
flush();

$query = $db->_query_print('select InquiryID from gd_navercheckout_inquiry where inquiryNo=[s]',$inquiryNo);
$result = $db->_select($query);
echo '���ǹ�ȣ '.$result[0]['InquiryID'].'�� ���� �亯ó�� ���Դϴ�<br>';
flush();
if($naverCheckoutAPI->AnswerCustomerInquiry($inquiryNo,$AnswerContent)) {
	if($naverCheckoutAPI->SyncInquiry($inquiryNo)) {
		echo '�亯����� ���������� ó���Ͽ����ϴ�<br><br>';
	}
	else {
		echo '�亯��� �۾� �� ������ �߻��߽��ϴ�<br>'.$naverCheckoutAPI->error.'<br><br>';
	}
}
else {
	echo '�亯��� �۾� �� ������ �߻��߽��ϴ�<br>'.$naverCheckoutAPI->error.'<br><br>';
}
flush();

echo '�Ϸ�Ǿ����ϴ�';
?>
<br><br>
<input type="button" value="Ȯ��" onclick="location.href='checkout.inquiry.detail.php?inquiryNo=<?=$inquiryNo?>';">

