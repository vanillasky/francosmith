<?
$location = "�����̼� > �����̼� ���ڰ��� ����";
include "../_header.php";

// �����̼� pg ������ �ҷ�����
$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' ���� ��û�ȳ��� ���� �����ͷ� �������ֽñ� �ٶ��ϴ�.', -1);
}
$tsCfg = $todayShop->cfg;
$tsPG = ($tsCfg['pg'] != '') ? unserialize($tsCfg['pg']) : array();

// ������ üũ (���� ������ ������� ��츦 �����ϰ� ���� XPay ������� ó�� �ϱ�)
if ($tsPG['cfg']['settlePg'] == "dacom") {
	$tmpDacom	= "old";
} else {
	$tmpDacom	= "new";
}

// �̴Ͻý� üũ (���� INIpay TX4 ������� ��츦 �����ϰ� ���� INIpay TX5 ������� ó�� �ϱ�)
if ($tsPG['cfg']['settlePg'] == "inicis") {
	$tmpInicis	= "old";
} else {
	$tmpInicis	= "new";
}

// �þ� üũ (���� �þ� ������� ��츦 �����ϰ� ���� �þ�BASIC ������� ó�� �ϱ�)
if ($tsPG['cfg']['settlePg'] == "allat") {
	$tmpAllat	= "old";
} else {
	$tmpAllat	= "new";
}
?>
<script>
function chgifrm(src,k){
	document.getElementById('pgifrm').src = src;
	for(var i=0;i<5;i++){
		if(i == k){
			document.getElementsByName('pgtd')[i].style.background='#627dce';
			document.getElementsByName('pgb')[i].style.color='#ffffff';
		}else{
			document.getElementsByName('pgtd')[i].style.background='#ffffff';
			document.getElementsByName('pgb')[i].style.color='#627dce';
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
�����̼� ���ڰ��� ����<span>���� ���ڰ���(PG) ���񽺻��� ������ �����Ͽ� �����ڿ��� �ſ�ī�� ���� ���������� ������ �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=14')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>


<table border=5 bordercolor=#627dce style="border-collapse:collapse">
<tr><td colspan=10 align=center style="padding: 10px 0px 10px 12px"><font color=627dce>����Ͻ� ���ڰ���(PG) ���񽺻� �� ���� Ŭ���� �� ���ڰ��� ���� ������ �Է��ϼ���.
</font></td></tr>
<tr align=center height=40>
	<? if ($tmpDacom == "old") {?><td width="190" id="pgtd"><a href="javascript:chgifrm('config.pg.inc.dacom.php',0)"><b id="pgb">LG U+</b></a></td><?}?>
	<? if ($tmpDacom == "new") {?><td width="190" id="pgtd"><a href="javascript:chgifrm('config.pg.inc.lgdacom.php',0)"><b id="pgb">LG U+</b></a></td><?}?>
	<? if ($tmpAllat == "old") {?><td width="190" id="pgtd"><a href="javascript:chgifrm('config.pg.inc.allat.php',1)"><b id="pgb">�Ｚ�þ�</b></a></td><?}?>
	<? if ($tmpAllat == "new") {?><td width="190" id="pgtd"><a href="javascript:chgifrm('config.pg.inc.allatbasic.php',1)"><b id="pgb">�Ｚ�þ�BASIC</b></a></td><?}?>
	<td width="190" id="pgtd"><a href="javascript:chgifrm('config.pg.inc.kcp.php',2)"><b id="pgb">KCP</b></a></td>
	<? if ($tmpInicis == "old") {?><td width="190" id="pgtd"><a href="javascript:chgifrm('config.pg.inc.inicis.php',3)"><b id="pgb">�̴Ͻý�</b></a></td><?}?>
	<? if ($tmpInicis == "new") {?><td width="190" id="pgtd"><a href="javascript:chgifrm('config.pg.inc.inipay.php',3)"><b id="pgb">�̴Ͻý�(TX5)</b></a></td><?}?>
	<td width="190" id="pgtd"><a href="javascript:chgifrm('config.pg.inc.agspay.php',4)"><b id="pgb">�ô�����Ʈ</b></a></td>
</tr>
</table>

<div style="padding-top: 20px"></div>
<?php
if($godo['blogData'] == 2){
	$tsPG['cfg']['settlePg'] = 'inicis';
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
if($tsPG['cfg']['settlePg']){
	switch($tsPG['cfg']['settlePg']){

		case "inicis" :
			echo("<script>chgifrm('config.pg.inc.inicis.php',3);</script>");
		break;

		case "inipay" :
			echo("<script>chgifrm('config.pg.inc.inipay.php',3);</script>");
		break;

		case "kcp" :
			echo("<script>chgifrm('config.pg.inc.kcp.php',2);</script>");
		break;

		case "dacom" :
			echo("<script>chgifrm('config.pg.inc.dacom.php',0);</script>");
		break;

		case "lgdacom" :
			echo("<script>chgifrm('config.pg.inc.lgdacom.php',0);</script>");
		break;

		case "allat" :
			echo("<script>chgifrm('config.pg.inc.allat.php',1);</script>");
		break;

		case "allatbasic" :
			echo("<script>chgifrm('config.pg.inc.allatbasic.php',1);</script>");
		break;

		case "agspay" :
			echo("<script>chgifrm('config.pg.inc.agspay.php',4);</script>");
		break;

	}
}

if(!$tsPG['cfg']['settlePg']) echo("<script>chgifrm('config.pg.inc.lgdacom.php',0);</script>");
?>
<? include "../_footer.php"; ?>