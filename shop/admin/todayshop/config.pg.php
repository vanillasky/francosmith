<?
$location = "투데이샵 > 투데이샵 전자결제 설정";
include "../_header.php";

// 투데이샵 pg 설정값 불러오기
$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' 서비스 신청안내는 고도몰 고객센터로 문의해주시기 바랍니다.', -1);
}
$tsCfg = $todayShop->cfg;
$tsPG = ($tsCfg['pg'] != '') ? unserialize($tsCfg['pg']) : array();

// 데이콤 체크 (현재 데이콤 사용중인 경우를 제외하고 전부 XPay 방식으로 처리 하기)
if ($tsPG['cfg']['settlePg'] == "dacom") {
	$tmpDacom	= "old";
} else {
	$tmpDacom	= "new";
}

// 이니시스 체크 (현재 INIpay TX4 사용중인 경우를 제외하고 전부 INIpay TX5 방식으로 처리 하기)
if ($tsPG['cfg']['settlePg'] == "inicis") {
	$tmpInicis	= "old";
} else {
	$tmpInicis	= "new";
}

// 올앳 체크 (현재 올앳 사용중인 경우를 제외하고 전부 올앳BASIC 방식으로 처리 하기)
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
투데이샵 전자결제 설정<span>계약된 전자결제(PG) 서비스사의 정보를 설정하여 구매자에게 신용카드 등의 결제수단을 제공할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=14')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>


<table border=5 bordercolor=#627dce style="border-collapse:collapse">
<tr><td colspan=10 align=center style="padding: 10px 0px 10px 12px"><font color=627dce>계약하신 전자결제(PG) 서비스사 한 곳을 클릭한 후 전자결제 설정 정보를 입력하세요.
</font></td></tr>
<tr align=center height=40>
	<? if ($tmpDacom == "old") {?><td width="190" id="pgtd"><a href="javascript:chgifrm('config.pg.inc.dacom.php',0)"><b id="pgb">LG U+</b></a></td><?}?>
	<? if ($tmpDacom == "new") {?><td width="190" id="pgtd"><a href="javascript:chgifrm('config.pg.inc.lgdacom.php',0)"><b id="pgb">LG U+</b></a></td><?}?>
	<? if ($tmpAllat == "old") {?><td width="190" id="pgtd"><a href="javascript:chgifrm('config.pg.inc.allat.php',1)"><b id="pgb">삼성올앳</b></a></td><?}?>
	<? if ($tmpAllat == "new") {?><td width="190" id="pgtd"><a href="javascript:chgifrm('config.pg.inc.allatbasic.php',1)"><b id="pgb">삼성올앳BASIC</b></a></td><?}?>
	<td width="190" id="pgtd"><a href="javascript:chgifrm('config.pg.inc.kcp.php',2)"><b id="pgb">KCP</b></a></td>
	<? if ($tmpInicis == "old") {?><td width="190" id="pgtd"><a href="javascript:chgifrm('config.pg.inc.inicis.php',3)"><b id="pgb">이니시스</b></a></td><?}?>
	<? if ($tmpInicis == "new") {?><td width="190" id="pgtd"><a href="javascript:chgifrm('config.pg.inc.inipay.php',3)"><b id="pgb">이니시스(TX5)</b></a></td><?}?>
	<td width="190" id="pgtd"><a href="javascript:chgifrm('config.pg.inc.agspay.php',4)"><b id="pgb">올더게이트</b></a></td>
</tr>
</table>

<div style="padding-top: 20px"></div>
<?php
if($godo['blogData'] == 2){
	$tsPG['cfg']['settlePg'] = 'inicis';
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