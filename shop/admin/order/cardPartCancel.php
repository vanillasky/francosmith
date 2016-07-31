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

	$partCancel->price = $price; // ��ұݾ�
	$partCancel->repay = $repay; // ȯ������ �� �ݾ� ( ��ü�ֹ��ǿ��� ȯ�������� ��ǰ�ݾ��� �� )

	// �ߺ� ��� ���� �÷��� ����
	$db->query("update ".GD_ORDER_CANCEL." set cancel_try = 'y' where sno='".$sno."' and ordno = '".$ordno."' and cancel_try = 'n' and pgcancel = 'n'");
	$result = mysql_affected_rows();
	
	// ���� �÷��װ� �������� �ʾ��� ���
	if ($result < 1) {
		echo '------ ó���� �Դϴ�. ------';
		// ���� ������ ���μ����� �Ϸ� �Ǵ� ���� ��ٸ��� ���
		flush();
		sleep(3);
		$query = "select pgcancel from ".GD_ORDER_CANCEL." where ordno='".$ordno."' and sno='".$sno."'";
		$data = $db->fetch($query);
		
		// ��� �� ó���� �Ǿ����� �ѹ� �� Ȯ��
		if($data['pgcancel'] !== 'n'){
			msg('���ó���� �Ϸ�� �ֹ��Դϴ�.');
			echo("<script>parent.location.reload();</script>");
		}
		else{}
	}
	else {}
	
	$res = $partCancel->partCancel_pg($ordno,$sno);

	if ($res) {
		msg('����������ҿϷ�');
		echo("<script>parent.location.reload();</script>");
	}
	else {}
}

if($_GET['ordno']){
	$sno = $_GET['sno'];

	$query = "select a.cardtno, a.settleprice, b.pgcancel from ".GD_ORDER." a left join ".GD_ORDER_CANCEL." b on a.ordno=b.ordno where b.sno='".$sno."' limit 1 ";
	$res = $db->fetch($query);
	
	// �̹� ó�� �Ǿ����� üũ
	if ($res['pgcancel'] !== 'n') {
		echo '<div class="title title_top">���ó���� �Ϸ�� �ֹ��Դϴ�.<span></span></div>';
	}
	else if ($res['pgcancel'] === 'n'){
	 
?>
<link rel="styleSheet" href="../style.css">
<script src="../common.js"></script>
<form name="frmIni" method="post" action="cardPartCancel.php">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="ordno" value="<?=$_GET['ordno']?>" /> <!-- �ֹ���ȣ -->
<input type="hidden" name="sno" value="<?=$_GET['sno']?>" /> <!-- ȯ��������ȣ -->
<input type="hidden" name="repay" value="<?=$_GET['repay']?>" /> <!-- ȯ�������� �ݾ� -->
	<div class="subtitle">
		<div class="title title_top">ī�� ���� �κ� ���<span></span></div>
	</div>
	<div class="input_wrap">
		<table class=tb>
		<col class="cellC">
		<col class="cellL">
		<tr>
			<th class="input_title r_space">����� �ݾ�</th>
			<td class="input_area"><input type="text" name="price" value="<?=$_GET['lastRepay']?>" onblur="price_calculate();" class="input_text width_small" /> ��</td>
		</tr>
		</table>
		<div style="padding-top:10px;" align="center">
			<input type="button" onClick="javascript:formChk();" value="ī�� �κ� ���" id="subBtn" /></a>
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
		// ����� �ݾ�
		var cancelPrice = document.getElementsByName('price')[0].value;

		// ����� �ݾ� üũ
		if (cancelPrice < 0 || cancelPrice == '') {
			alert('����� �ݾ��� 0�� �̻��̿��� �մϴ�.');
			document.getElementsByName('price')[0].value = "";
			return false;
		}

		// ���ο�û �ݾ�
		var check_price	= parseInt(<?php echo $_GET[repay];?>) - parseInt(cancelPrice);

		// ���� ȯ�ұݾ� üũ
		if (check_price < 0) {
			alert('���ο�û �ݾ׿���! (�ִ� <?php echo number_format($_GET[repay]);?>��)');
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
