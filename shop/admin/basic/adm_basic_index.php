<?php
$mainpage = 1;

# div ��ġ�̵� 2007-02-21 kwons
$scriptLoad='<script language="javascript" src="./divmove_table.js"></script>';
# ���̾ ȣ��
$scriptLoad.='<script language="javascript" src="./malldiary.js"></script>';
# ���� ��ġ ǥ��
$location = "�����ڸ���";

include "../_header.php";
@include "../goods/stockalarm.php";

# �����뷮üũ
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

### ȸ�� ������ SMS
include "./birth_sms.php";

### ȸ�� ��� ����
$member_grp = Core::loader('member_grp');
$member_grp->execUpdate();
?>
<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td valign="top">
		<div class="main-contents">
			<div class="left">
				<!-- ������ ���� ����Ʈ -->
				<? include './adm_basic_index_left.inc.php'; ?>
			</div>
			<div class="center">
				<!-- ������ ���� ���� ��� -->
				<? include './adm_basic_index_center_top.inc.php'; ?>

				<!-- ������ ���� ���� �����޴� -->
				<? include './adm_basic_index_center_menu.inc.php'; ?>

				<!-- ������ ���� ���� �ϴ� -->
				<? include './adm_basic_index_center_bottom.inc.php'; ?>
			</div>
			<div class="right">
				<!-- ������ ���� ����Ʈ -->
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
//�޸�ȸ�� ��ȯ 30���� ����� ���� �߼�
if($dormant->checkAutoMailExecuteAble() === true){
	echo $dormant->loadIframe('dormantMemberAutoMail');
}

//�޸�ȸ�� �ڵ� ��ȯ
if($dormant->checkAutoExecuteAble() === true){
	echo $dormant->loadIframe('dormantMemberAuto');
}

//�޸�ȸ�� ��ȯ �ȳ� SMS �߼�
if($dormant->checkAutoSmsExecuteAble() === true){
	echo $dormant->loadIframe('dormantMemberAutoSms');
}


if($cfg[autoCancel]){
	list($cnt) = $db->fetch("SELECT COUNT(*) FROM (SELECT a.ordno, b.memo FROM ".GD_ORDER." a LEFT JOIN ".GD_ORDER_CANCEL." b ON a.ordno = b.ordno AND b.memo = '�ڵ��ֹ����' WHERE a.orddt <= DATE_SUB(NOW(), INTERVAL ".$cfg['autoCancel']." ".($cfg['autoCancelUnit'] == 'h' ? 'HOUR' : 'DAY').") AND a.step='0' AND a.step2='0' AND a.settlekind='a')c WHERE c.memo IS NULL");
	if($cnt) echo "<script>window.onload = function (){ popupLayer('../proc/popup.autoCancel.php',500,300); };</script>";
}
?>