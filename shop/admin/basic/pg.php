<?
$location = "������⿬�� > ���ڰ������� ����";
include "../_header.php";

// ������ üũ (���� ������ ������� ��츦 �����ϰ� ���� XPay ������� ó�� �ϱ�)
if ($cfg['settlePg'] == "dacom") {
	$tmpDacom	= "old";
} else {
	$tmpDacom	= "new";
}

// �̴Ͻý� üũ (���� INIpay TX4 ������� ��츦 �����ϰ� ���� INIpay TX5 ������� ó�� �ϱ�)
if ($cfg['settlePg'] == "inicis") {
	$tmpInicis	= "old";
} else {
	$tmpInicis	= "new";
}

// �þ����� (���� PLUS ������� ��츦 �����ϰ� ���� BASIC ������� ó�� �ϱ�)
if ($cfg['settlePg'] == "allat") {
	$tmpAllat	= "old";
} else {
	$tmpAllat	= "new";
}
?>
<script>
function PG_tilech(pgcode){
	if (document.getElementById('pgbtext') != null) {
		document.getElementById('pgbtext').innerText = document.getElementsByName('pgb')[pgcode].innerText;
	}
}

function chgifrm(src,k){
	document.getElementById('pgifrm').src = src;
	var pgCnt = document.getElementsByName('pgtd').length;
	for(var i=0;i<pgCnt;i++){
		if(i == k){
			document.getElementsByName('pgtd')[i].style.background="#627dce";
			document.getElementsByName('pgb')[i].style.color="#ffffff";
		}else{
			document.getElementsByName('pgtd')[i].style.background="#ffffff";
			document.getElementsByName('pgb')[i].style.color="#627dce";
		}
		<?php
		if($godo['blogData'] == 2){
			echo "if(i>0){document.getElementsByName('pgtd')[i].style.display='none';}else{document.getElementsByName('pgtd')[i].width='760';}";
		}
		?>
	}
}
</script>
<div class="title title_top">
���� ���ڰ��� ����<span>���� ���ڰ���(PG) ���񽺻��� ������ �����Ͽ� �����ڿ��� �ſ�ī�� ���� ���������� ������ �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=20')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>

<?if($cfg['settlePg']){?>
<div style="margin:10px 0">
���� ������ �̿����� ���ڰ��� ���񽺻�� ��<b name="pgbtext" id="pgbtext"></b>�� �Դϴ�.
</div>
<?}?>

<table border=5 bordercolor=#627dce style="border: 5px solid #627dce;border-collapse:collapse">
<tr><td colspan=10 align=center style="padding: 10px 0px 10px 12px"><font color=627dce>����Ͻ� ���ڰ���(PG) ���񽺻� �� ���� Ŭ���� �� ���ڰ��� ���� ������ �Է��ϼ���.
</font></td></tr>
<tr align=center height=40>
	<? if ($tmpDacom == "old") {?><td width="190" name="pgtd" id="pgtd"><a href="javascript:chgifrm('dacom.php',0)"><b name="pgb" id="pgb">LG U+</b></a></td><?}?>
	<? if ($tmpDacom == "new") {?><td width="190" name="pgtd" id="pgtd"><a href="javascript:chgifrm('lgdacom.php',0)"><b name="pgb" id="pgb">LG U+</b></a></td><?}?>
	<? if ($tmpAllat == "old") {?><td width="190" name="pgtd" id="pgtd"><a href="javascript:chgifrm('allat.php',1)"><b name="pgb" id="pgb">�Ｚ�þ�</b></a></td><?}?>
	<? if ($tmpAllat == "new") {?><td width="190" name="pgtd" id="pgtd"><a href="javascript:chgifrm('allatbasic.php',1)"><b name="pgb" id="pgb">�Ｚ�þ�</b></a></td><?}?>
	<td width="190" name="pgtd" id="pgtd"><a href="javascript:chgifrm('kcp.php',2)"><b name="pgb" id="pgb">KCP</b></a></td>
	<? if ($tmpInicis == "old") {?><td width="190" name="pgtd" id="pgtd"><a href="javascript:chgifrm('inicis.php',3)"><b name="pgb" id="pgb">�̴Ͻý�</b></a></td><?}?>
	<? if ($tmpInicis == "new") {?><td width="190" name="pgtd" id="pgtd"><a href="javascript:chgifrm('inipay.php',3)"><b name="pgb" id="pgb">�̴Ͻý�(TX5)</b></a></td><?}?>
	<td width="190" name="pgtd" id="pgtd"><a href="javascript:chgifrm('agspay.php',4)"><b name="pgb" id="pgb">�ô�����Ʈ</b></a></td>
	<td width="190" name="pgtd" id="pgtd"><a href="javascript:chgifrm('easypay.php',5)"><b name="pgb" id="pgb">��������</b></a></td>
	<td width="190" name="pgtd" id="pgtd"><a href="javascript:chgifrm('settlebank.php',6)"><b name="pgb" id="pgb">��Ʋ��ũ</b></a></td>
</tr>
</table>

<div style="padding-top: 20px"></div>
<?php
if($godo['blogData'] == 2){
	$cfg['settlePg'] = 'inicis';
?>
<div style="color:red;padding-left:190">������ ��α׼��̿������ �̴Ͻý� ���ڰ������� ��Ϻ� �����Դϴ�.</div>
<?php
}
?>
<table width="100%" cellpadding=0 cellspacing=0 border=0>
<tr>
	<td>
	<iframe id="pgifrm" src="inicis.php" width="100%" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="10" scrolling="no"></iframe>
	</td>
</tr>
</table>
<?
if($cfg['settlePg']){
	switch($cfg['settlePg']){

		case "inicis" :
			echo("<script>PG_tilech('3');chgifrm('inicis.php',3);</script>");
		break;

		case "inipay" :
			echo("<script>PG_tilech('3');chgifrm('inipay.php',3);</script>");
		break;

		case "kcp" :
			echo("<script>PG_tilech('2');chgifrm('kcp.php',2);</script>");
		break;

		case "dacom" :
			echo("<script>PG_tilech('0');chgifrm('dacom.php',0);</script>");
		break;

		case "lgdacom" :
			echo("<script>PG_tilech('0');chgifrm('lgdacom.php',0);</script>");
		break;

		case "allat" :
			echo("<script>PG_tilech('1');chgifrm('allat.php',1);</script>");
		break;

		case "allatbasic" :
			echo("<script>PG_tilech('1');chgifrm('allatbasic.php',1);</script>");
		break;

		case "agspay" :
			echo("<script>PG_tilech('4');chgifrm('agspay.php',4);</script>");
		break;

		case "easypay" :
			echo("<script>PG_tilech('5');chgifrm('easypay.php',5);</script>");
		break;

		case "settlebank" :
			echo("<script>PG_tilech('6');chgifrm('settlebank.php',6);</script>");
		break;

	}
}

if(!$cfg['settlePg']) echo("<script>PG_tilech('0');chgifrm('lgdacom.php',0);</script>");
?>
<? include "../_footer.php"; ?>