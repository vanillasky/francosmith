<?php
$mainpage = 1;

# div 위치이동 2007-02-21 kwons
$scriptLoad='<script language="javascript" src="./divmove_table.js"></script>';
# 다이어리 호출
$scriptLoad.='<script language="javascript" src="./malldiary.js"></script>';
# 현재 위치 표시
$location = "관리자메인";

include "../_header.php";
@include "../goods/stockalarm.php";

# 계정용량체크
if (function_exists('disk')){
	list( $disk_errno, $disk_msg ) = disk();
	if ( !empty( $disk_errno ) ){
		echo "
		<script language='javascript'>
		if ( !getCookie( 'blnCookie_disk' ) ) {
			var win=popup_return( '../proc/warning_disk_pop.php', 'disk_err', 320, 260, 100, 100, 'no' );
			win.focus();
		}
		</script>
		";
	}
}

### 회원 생일자 SMS
include "./birth_sms.php";

### 회원 등급 조정
$member_grp = Core::loader('member_grp');
$member_grp->execUpdate();
?>
<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td valign="top">
		<div class="main-contents">
			<div class="left">
				<!-- 관리자 메인 레프트 -->
				<? include './adm_basic_index_left.inc.php'; ?>
			</div>
			<div class="center">
				<!-- 관리자 메인 센터 상단 -->
				<? include './adm_basic_index_center_top.inc.php'; ?>

				<!-- 관리자 메인 센터 위젯메뉴 -->
				<? include './adm_basic_index_center_menu.inc.php'; ?>

				<!-- 관리자 메인 센터 하단 -->
				<? include './adm_basic_index_center_bottom.inc.php'; ?>
			</div>
			<div class="right">
				<!-- 관리자 메인 라이트 -->
				<? include './adm_basic_index_right.inc.php'; ?>
			</div>
		</div>
	</td>
</tr>
</table>
<div id="maxlicense" style="display:none;"></div>
<?php if(isset($_COOKIE['maxpopup']) === false){ ?>
<div id="panel_POPUP"></div>
<?php } ?>
<script>
linecss();
table_design_load();
adm_panelAPI('panelAPI', 'panelAPI', 0);
</script>
<? if ($hiddenLeft){ ?><script>hiddenLeft()</script><? } ?>

<?
$mainAutoSort = Core::loader('mainAutoSort');

if ($mainAutoSort->save_yn === false) {
?>
<script>
new Ajax.Request("../goods/makeMainAutoSort.php",
{
	method:'post'
});
</script>
<? } ?>

<?
$dormant = Core::loader('dormant');
//휴면회원 전환 30일전 대상자 메일 발송
if($dormant->checkAutoMailExecuteAble() === true){
	echo $dormant->loadIframe('dormantMemberAutoMail');
}

//휴면회원 자동 전환
if($dormant->checkAutoExecuteAble() === true){
	echo $dormant->loadIframe('dormantMemberAuto');
}

//휴면회원 전환 안내 SMS 발송
if($dormant->checkAutoSmsExecuteAble() === true){
	echo $dormant->loadIframe('dormantMemberAutoSms');
}


if($cfg[autoCancel]){
	list($cnt) = $db->fetch("SELECT COUNT(*) FROM (SELECT a.ordno, b.memo FROM ".GD_ORDER." a LEFT JOIN ".GD_ORDER_CANCEL." b ON a.ordno = b.ordno AND b.memo = '자동주문취소' WHERE a.orddt <= DATE_SUB(NOW(), INTERVAL ".$cfg['autoCancel']." ".($cfg['autoCancelUnit'] == 'h' ? 'HOUR' : 'DAY').") AND a.step='0' AND a.step2='0' AND a.settlekind='a')c WHERE c.memo IS NULL");
	if($cnt) echo "<script>window.onload = function (){ popupLayer('../proc/popup.autoCancel.php',500,300); };</script>";
}
?>