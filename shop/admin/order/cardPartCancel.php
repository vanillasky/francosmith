<?php
include "../lib.php";
include "../../conf/config.php";
include_once '../../conf/pg.'.$cfg['settlePg'].'.php';
include "../../lib/cardCancel.class.php";

if($_POST[mode] == 'repay' ){
	$partCancel = new cardCancel();

	$price = $_POST['price'];
	$repay = $_POST['repay'];
	$ordno = $_POST['ordno'];
	$sno = $_POST['sno'];

	$partCancel->price = $price; // 취소금액
	$partCancel->repay = $repay; // 환불접수 된 금액 ( 전체주문건에서 환불접수된 상품금액의 합 )

	// 중복 취소 방지 플래그 수정
	$db->query("update ".GD_ORDER_CANCEL." set cancel_try = 'y' where sno='".$sno."' and ordno = '".$ordno."' and cancel_try = 'n' and pgcancel = 'n'");
	$result = mysql_affected_rows();
	
	// 방지 플래그가 수정되지 않았을 경우
	if ($result < 1) {
		echo '------ 처리중 입니다. ------';
		// 먼저 진입한 프로세스가 완료 되는 것을 기다리며 대기
		flush();
		sleep(3);
		$query = "select pgcancel from ".GD_ORDER_CANCEL." where ordno='".$ordno."' and sno='".$sno."'";
		$data = $db->fetch($query);
		
		// 대기 후 처리가 되었는지 한번 더 확인
		if($data['pgcancel'] !== 'n'){
			msg('취소처리가 완료된 주문입니다.');
			echo("<script>parent.location.reload();</script>");
		}
		else{}
	}
	else {}
	
	$res = $partCancel->partCancel_pg($ordno,$sno);

	if ($res) {
		msg('결제승인취소완료');
		echo("<script>parent.location.reload();</script>");
	}
	else {}
}

if($_GET['ordno']){
	$sno = $_GET['sno'];

	$query = "select a.cardtno, a.settleprice, b.pgcancel from ".GD_ORDER." a left join ".GD_ORDER_CANCEL." b on a.ordno=b.ordno where b.sno='".$sno."' limit 1 ";
	$res = $db->fetch($query);
	
	// 이미 처리 되었는지 체크
	if ($res['pgcancel'] !== 'n') {
		echo '<div class="title title_top">취소처리가 완료된 주문입니다.<span></span></div>';
	}
	else if ($res['pgcancel'] === 'n'){
	 
?>
<link rel="styleSheet" href="../style.css">
<script src="../common.js"></script>
<form name="frmIni" method="post" action="cardPartCancel.php">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="ordno" value="<?=$_GET['ordno']?>" /> <!-- 주문번호 -->
<input type="hidden" name="sno" value="<?=$_GET['sno']?>" /> <!-- 환불접수번호 -->
<input type="hidden" name="repay" value="<?=$_GET['repay']?>" /> <!-- 환불접수된 금액 -->
	<div class="subtitle">
		<div class="title title_top">카드 결제 부분 취소<span></span></div>
	</div>
	<div class="input_wrap">
		<table class=tb>
		<col class="cellC">
		<col class="cellL">
		<tr>
			<th class="input_title r_space">취소할 금액</th>
			<td class="input_area"><input type="text" name="price" value="<?=$_GET['lastRepay']?>" onblur="price_calculate();" class="input_text width_small" /> 원</td>
		</tr>
		</table>
		<div style="padding-top:10px;" align="center">
			<input type="button" onClick="javascript:formChk();" value="카드 부분 취소" id="subBtn" /></a>
		</div>
	</div>
</form>

<script>
	function formChk(){
		if(price_calculate()){
			document.getElementById("subBtn").disabled=true;
			document.getElementsByName('mode')[0].value = "repay";
			frmIni.submit();
		}
	}

	function price_calculate(){
		// 취소할 금액
		var cancelPrice = document.getElementsByName('price')[0].value;

		// 취소할 금액 체크
		if (cancelPrice < 0 || cancelPrice == '') {
			alert('취소할 금액은 0원 이상이여야 합니다.');
			document.getElementsByName('price')[0].value = "";
			return false;
		}

		// 승인요청 금액
		var check_price	= parseInt(<?php echo $_GET[repay];?>) - parseInt(cancelPrice);

		// 최종 환불금액 체크
		if (check_price < 0) {
			alert('승인요청 금액오류! (최대 <?php echo number_format($_GET[repay]);?>원)');
			document.getElementsByName('price')[0].value = "";
			return false;
		}

		return true;
	}
	table_design_load();
</script>

<?php
	}
}
?>
