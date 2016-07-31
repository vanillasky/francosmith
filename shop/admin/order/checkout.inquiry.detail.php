<?php
/**
 * 네이버체크아웃 주문 > 문의답변보내기
 * @author sunny, oneorzero
 */
include '../_header.popup.php';

$inquiryNo = (int)$_GET['inquiryNo'];

$query = "select * from gd_navercheckout_inquiry where inquiryNo='{$inquiryNo}'";
$result = $db->_select($query);
$inquiryResult = $result[0];

$query = "select * from gd_navercheckout_inquiry_item where inquiryNo='{$inquiryNo}' order by seq asc";
$inquiryItemResult = $db->_select($query);
?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		window.frameElement.style.height = document.body.scrollHeight;
	});
</script>
<div style="background-color:#FDFDE1;color:#666666;padding:10px 5px 5px 5px">

	<table width="100%">
	<tr>
	<td width="80">주문번호</td>
	<td><?=$inquiryResult['OrderID']?></td>
	<td width="120"><?=$inquiryResult['CustomerID']?></td>
	<td width="120"><?=$inquiryResult['Email']?></td>
	</tr>
	<tr><td colspan="4" style="background-color:#cccccc;height:1px;"></td></tr>

	<? foreach($inquiryItemResult as $eachItem): ?>
	<tr><td colspan="4" style="line-height:20px">
	문의 내용 <br>
	<?=nl2br(htmlspecialchars($eachItem['InquiryContent']))?>
	</td></tr>
	<tr><td colspan="4" style="background-color:#cccccc;height:1px;"></td></tr>
	<tr><td colspan="4" style="line-height:20px">
	답변 (<?=$eachItem['AnswerDateTime']?>) <br>
	<?=nl2br(htmlspecialchars($eachItem['AnswerContentNaver']))?>
	</td></tr>
	<? if($eachItem['AnswerContentShop']): ?>
	<tr><td colspan="4" style="background-color:#cccccc;height:1px;"></td></tr>
	<tr><td colspan="4" style="line-height:20px">
	가맹점 답변<br>
	<?=nl2br(htmlspecialchars($eachItem['AnswerContentShop']))?>
	</td></tr>
	<? endif; ?>
	<tr><td colspan="4" style="background-color:#cccccc;height:1px;"></td></tr>
	<? endforeach; ?>

	<? if($inquiryResult['Answerable']=='y'): ?>
		<tr><td colspan="4" style="line-height:20px;padding:10px 10px 0px 10px">
		<form method="post" action="checkout.api.AnswerCustomerInquiry.php">
			<input type="hidden" name="mode" value="answer">
			<input type="hidden" name="inquiryNo" value="<?=$inquiryResult['inquiryNo']?>">
			<textarea style="width:100%;height:100px;margin-bottom:5px" name="AnswerContent"></textarea><br>
			<input type="submit" value=" 답변보내기 ">
		</form>
		</td></tr>
	<? endif; ?>

	</table>

</div>