<?php
/**
 * 중계서버와 통신, 답변등록
 * @author sunny, oneorzero
 */
include "../_header.popup.php";
$naverCheckoutAPI = Core::loader('naverCheckoutAPI');
$inquiryNo = $_POST['inquiryNo'];
$AnswerContent = $_POST['AnswerContent'];

?>

<br>
중계서버와 통신 중 ...<br>
<?
flush();

$query = $db->_query_print('select InquiryID from gd_navercheckout_inquiry where inquiryNo=[s]',$inquiryNo);
$result = $db->_select($query);
echo '문의번호 '.$result[0]['InquiryID'].'에 대한 답변처리 중입니다<br>';
flush();
if($naverCheckoutAPI->AnswerCustomerInquiry($inquiryNo,$AnswerContent)) {
	if($naverCheckoutAPI->SyncInquiry($inquiryNo)) {
		echo '답변등록을 정상적으로 처리하였습니다<br><br>';
	}
	else {
		echo '답변등록 작업 중 오류가 발생했습니다<br>'.$naverCheckoutAPI->error.'<br><br>';
	}
}
else {
	echo '답변등록 작업 중 오류가 발생했습니다<br>'.$naverCheckoutAPI->error.'<br><br>';
}
flush();

echo '완료되었습니다';
?>
<br><br>
<input type="button" value="확인" onclick="location.href='checkout.inquiry.detail.php?inquiryNo=<?=$inquiryNo?>';">

