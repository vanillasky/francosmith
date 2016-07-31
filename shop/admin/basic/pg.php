<?
$location = "결제모듈연동 > 전자결제서비스 설정";
include "../_header.php";

// 데이콤 체크 (현재 데이콤 사용중인 경우를 제외하고 전부 XPay 방식으로 처리 하기)
if ($cfg['settlePg'] == "dacom") {
	$tmpDacom	= "old";
} else {
	$tmpDacom	= "new";
}

// 이니시스 체크 (현재 INIpay TX4 사용중인 경우를 제외하고 전부 INIpay TX5 방식으로 처리 하기)
if ($cfg['settlePg'] == "inicis") {
	$tmpInicis	= "old";
} else {
	$tmpInicis	= "new";
}

// 올앳페이 (현재 PLUS 사용중인 경우를 제외하고 전부 BASIC 방식으로 처리 하기)
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
통합 전자결제 설정<span>계약된 전자결제(PG) 서비스사의 정보를 설정하여 구매자에게 신용카드 등의 결제수단을 제공할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=20')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>

<?if($cfg['settlePg']){?>
<div style="margin:10px 0">
현재 고객님이 이용중인 전자결제 서비스사는 ‘<b name="pgbtext" id="pgbtext"></b>’ 입니다.
</div>
<?}?>

<table border=5 bordercolor=#627dce style="border: 5px solid #627dce;border-collapse:collapse">
<tr><td colspan=10 align=center style="padding: 10px 0px 10px 12px"><font color=627dce>계약하신 전자결제(PG) 서비스사 한 곳을 클릭한 후 전자결제 설정 정보를 입력하세요.
</font></td></tr>
<tr align=center height=40>
	<? if ($tmpDacom == "old") {?><td width="190" name="pgtd" id="pgtd"><a href="javascript:chgifrm('dacom.php',0)"><b name="pgb" id="pgb">LG U+</b></a></td><?}?>
	<? if ($tmpDacom == "new") {?><td width="190" name="pgtd" id="pgtd"><a href="javascript:chgifrm('lgdacom.php',0)"><b name="pgb" id="pgb">LG U+</b></a></td><?}?>
	<? if ($tmpAllat == "old") {?><td width="190" name="pgtd" id="pgtd"><a href="javascript:chgifrm('allat.php',1)"><b name="pgb" id="pgb">삼성올앳</b></a></td><?}?>
	<? if ($tmpAllat == "new") {?><td width="190" name="pgtd" id="pgtd"><a href="javascript:chgifrm('allatbasic.php',1)"><b name="pgb" id="pgb">삼성올앳</b></a></td><?}?>
	<td width="190" name="pgtd" id="pgtd"><a href="javascript:chgifrm('kcp.php',2)"><b name="pgb" id="pgb">KCP</b></a></td>
	<? if ($tmpInicis == "old") {?><td width="190" name="pgtd" id="pgtd"><a href="javascript:chgifrm('inicis.php',3)"><b name="pgb" id="pgb">이니시스</b></a></td><?}?>
	<? if ($tmpInicis == "new") {?><td width="190" name="pgtd" id="pgtd"><a href="javascript:chgifrm('inipay.php',3)"><b name="pgb" id="pgb">이니시스(TX5)</b></a></td><?}?>
	<td width="190" name="pgtd" id="pgtd"><a href="javascript:chgifrm('agspay.php',4)"><b name="pgb" id="pgb">올더게이트</b></a></td>
	<td width="190" name="pgtd" id="pgtd"><a href="javascript:chgifrm('easypay.php',5)"><b name="pgb" id="pgb">이지페이</b></a></td>
	<td width="190" name="pgtd" id="pgtd"><a href="javascript:chgifrm('settlebank.php',6)"><b name="pgb" id="pgb">세틀뱅크</b></a></td>
</tr>
</table>

<div style="padding-top: 20px"></div>
<?php
if($godo['blogData'] == 2){
	$cfg['settlePg'] = 'inicis';
?>
<div style="color:red;padding-left:190">고객님은 블로그샵이용고객으로 이니시스 전자결제서비스 등록비 무료입니다.</div>
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