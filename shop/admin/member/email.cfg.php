<?

$mode = $_GET[mode];

$loc	= array(
		0	=> "�ֹ�Ȯ�θ���",
		1	=> "�Ա�Ȯ�θ���",
		3	=> "���/�߼۸���",
		4	=> "��ۿϷ����",
		10	=> "ȸ�����Ը���",
		11	=> "���̵�/��й�ȣã�����",
		12	=> "ȸ��Ż�����",
		13	=> "��й�ȣã�� ��������",
		14	=> "��й�ȣ���� �ȳ�����",
		20	=> "1:1���Ǵ亯����",
		30	=> "���ŰźοϷ� �ȳ�����",
		31	=> "���ŵ��Ǽ��� �ȳ�����",
		40	=> "�޸� ��ȯ ���� �ȳ� ����",
		);
$default_info="������ �ڵ��߼۵Ǵ� ������ �����ϰ� �����մϴ�";
$info	= array(
		0	=> $default_info,
		1	=> $default_info,
		3	=> "������ �ڵ��߼۵Ǵ� ������ �����ϰ� �����մϴ�",
		4	=> $default_info,
		10	=> $default_info,
		11	=> $default_info,
		12	=> $default_info,
		13	=> $default_info,
		14	=> $default_info,
		20	=> $default_info,
		30	=> $default_info,
		31	=> $default_info,
		40	=> $default_info,
		);

$location = "�ڵ����ϼ��� > $loc[$mode]";
include "../_header.php";
include "../../conf/config.php";

$useyn = $cfg["mailyn_$mode"];
if (!$useyn) $useyn = "n";
$checked[useyn][$useyn] = "checked";

$body = file_get_contents("../../conf/email/tpl_{$mode}.php");	//����
@require_once "../../conf/email/subject_{$mode}.php";	//����

$skin_type_checked[$cfg[skin_type]]="checked";
$skin_type_checked[$cfg[skin_type]]="checked";
?>

<form method=post action="../proc/indb.email.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$mode?>">

<div class="title title_top"><?=$loc[$mode]?><span><?=$info[$mode]?> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=7')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table width=100% class=tb>
<col class=cellC><col class=cellL>
<?
if(! in_array($_GET[mode],array('11','20','13'))){
?>
<tr height=25>
	<td>�ڵ��߼ۿ���</td>
	<td class=noline>
	<input type=radio name=cfg[mailyn_<?=$mode?>] value="y" <?=$checked[useyn][y]?>>�ڵ����� ����
	<input type=radio name=cfg[mailyn_<?=$mode?>] value="n" <?=$checked[useyn][n]?>>����������
	</td>
</tr>
<?}?>
<?php
if(in_array($_GET['mode'],$r_sendDateCode['mail'])) {
	// �⺻�� ó��
	if (empty($cfg['mailSendDate_'.$mode])) {
		$cfg['mailSendDate_'.$mode]	= $r_sendDateDefault['mail'];
	}
?>
<tr height=25>
	<td>�߼۴��</td>
	<td class=noline>
		�ֱ�
		<select name="cfg[mailSendDate_<?php echo $mode;?>]">
			<?php foreach ($r_sendDatePeriod['mail'] as $dayVal) {?>
			<option value="<?php echo $dayVal;?>" <?php if ($cfg['mailSendDate_'.$mode] == $dayVal) echo 'selected="selected"';?>><?php echo $dayVal;?>��</option>
			<?php }?>
		</select>
		�ֹ��Ǹ�
	</td>
</tr>
<?php
}
?>
<tr height=25>
	<td>�߼���Email</td>
	<td><?if($cfg[adminEmail])echo $cfg[adminEmail]; else echo "����";?>&nbsp;<a href="javascript:emailModify()"><img src="../img/i_edit.gif" align="absmiddle" /></a>
	<div style="margin-top:5px"><span class=small><font class=extext>��[�⺻��������>������ Email] �� ��ϵ� Email�� �߼۵Ǹ�, �Է� ������ ������ �߼۵��� �ʽ��ϴ�.</font></span></div>
	</td>
</tr>
<tr height=25>
	<td>��������</td>
	<td><input type=text name="subject" value="<?=$headers[Subject]?>" style="width:100%" required class="line"></td>
</tr>
<?if($mode==3) {?>
<tr height=25>
	<td>���� �⺻���</td>
	<td>
	<a href="javascript:skin_sel('1')"><img src="../img/btn_type_a.gif" alt="Ÿ��1 �ʱ�ȭ" /></a>&nbsp;&nbsp;<a href="javascript:skin_sel('2')"><img src="../img/btn_type_b.gif" alt="Ÿ��2 �ʱ�ȭ" /></a>
	</td>
</tr>
<?}?>
<tr>
	<td>����</td>
	<td style="padding:5px">
	<textarea name=body type=editor style="width:100%;height:830px"><?=htmlspecialchars($body)?></textarea>
	<script src="../../lib/meditor/mini_editor.js"></script>
	<script>mini_editor("../../lib/meditor/")</script>
	</td>
</tr>
<?if($mode==3) {?>
<tr>
	<td>ġȯ�ڵ�</td>
	<td style="padding:5px;line-height:20px">
		���Ż�ǰ���� ���� : {orderInfo} <a href="javascript:clipboard('{orderInfo}')">[����]</a><br/>
		�������� ���� : {settleInfo} <a href="javascript:clipboard('{settleInfo}')">[����]</a><br/>
		������� ���� : {deliveryInfo} <a href="javascript:clipboard('{deliveryInfo}')">[����]</a><br/>
		<br/>
		<span style="font:11px ����;color:#627dce">���� ���� ������ ġȯ�ڵ带 �����Ͽ� ����ϼ���.</span>
	</td>
</tr>
<?}?>
</table>

	<div class='button' style="width:100%;position:relative" >

			<input type='image' src="../img/btn_modify.gif" />&nbsp;<a href="javascript:history.back()"><img src="../img/btn_cancel.gif" /></a>
			<?if($mode==3){?>
				&nbsp;&nbsp;
				<a href="javascript:rollbackList()" onMouseOver="javascript:rollbackList()"><img src="../img/btn_source_restore.gif" alt="�ҽ�����" /></a>
				<div id="rollbackList" style="position:absolute;left:59%;top:0px;"></div>
			<?}?>

	</div>
</form>

<?php if($mode == 10 || $mode == 30 || $mode == 31){ ?>
<table cellpadding="0" cellspacing="2" width="100%" border="0" style="margin: 0px 0px 30px 0px; border: 3px #dce1e1 solid; padding: 5px;">
<tr>
	<td style="color: #627dce; font-weight: bold;">�� ���� ���� ���ŵ��ǿ� ���� ������Ÿ��� �ؼ� ���� �ȳ�</td>
</tr>
<tr>
	<td style="padding-top: 15px; font-weight: bold;">������Ÿ����� ���ؿ� ���� �Ʒ� �ڵ����� �߼� �� ���� ���� ���ŵ��� ������ �Բ� �߼۵˴ϴ�.</td>
</tr>
<tr>
	<td><a href="http://www.law.go.kr/lsInfoP.do?lsiSeq=164340&ancYd=20141128&ancNo=25789&efYd=20150818&nwJoYnInfo=N&efGubun=Y&chrClsCd=010202#0000" target="_blank"><span style="text-decoration: underline; color: blue; font-weight: bold;">[������Ÿ� �̿����� �� ������ȣ � ���� ���� ����� �ٷΰ���]</span></a></td>
</tr>
<tr>
	<td style="padding-top: 15px;">
		<div style="margin-left: 5px;">1. ȸ�����Ը���</div>
		<div style="margin-left: 10px;">* ���� ���� ���ŵ��� ���� ���뵵 �Բ� �߼�</div>
		<div style="margin-left: 13px;">�� �̸��� �� �޴��� ������ �������� ���� ��쿡�� ���Űź� ���·� ���� �� �ȳ��˴ϴ�.</div>
	</td>
</tr>
<tr>
	<td style="padding-top: 15px;">
		<div style="margin-left: 5px;">2. ���ŵ��Ǽ��� �ȳ�����</div>
		<div style="margin-left: 10px;">* �������ŵ��� ���� ���� ������ �߼�</div>
		<div style="margin-left: 10px;">* ȸ���������� ���� ����, �̺�Ʈ SMS/���� ���ŵ��� ���� ������ �߼�</div>
	</td>
</tr>
<tr>
	<td style="color: #627dce; padding-top: 15px;">
		�� 2015�� 7�� 30�� ���ĺ��� ����� ���θ��� ��� ��ȸ�����Ը��ϡ��� �����ŰźοϷ� �ȳ����ϡ��� ���� ������ �߰��Ǿ�� �մϴ�.<br />
		<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=7')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>�� �����Ͻþ� ���ó����� ���Ͽ� �����Ͻñ� �ٶ��ϴ�.
	</td>
</tr>
</table>
<?php } ?>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� �ϴܿ� �ִ� �ΰ�� <a href="../design/design_banner.php" target=_blank><font color=white><b>[�ΰ�/��ʰ���]</b></font></a> ���� ���Ϸΰ� ����Ͻø� �˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� ���뿡 ���̴� �̹������� <a href="design/design_webftp.php" target=_blank><font color=white><b>[webFTP�̹������� > data > editor]</b></font></a> ���� �����ϼ���.</td></tr>
<?if($mode==3) {?>
<tr><td ><img src="../img/icon_list.gif" align="absmiddle">���� ������ ������ ���� �� ���� �� ������ html�ҽ�(�̹�������)���� ����մϴ�.</td></tr>
<tr><td style="padding-left:10px">-��������� �ֱټ����� 3�������� ����Ǹ� ��Ÿ ������ ����� ���� ���� ��츦 ����Ͽ�
������ �������� html���⸦ �ϼż� ���� �ҽ��� ��� �޾Ƶνð� �۾��Ͻñ� �ٶ��ϴ�.
</td></tr>
<?}?>
</table>
</div>
<script>cssRound('MSG01')</script>
<script type="text/javascript">
<!--
	function clipboard(str){
		window.clipboardData.setData('Text',str);
		alert("Ŭ�����忡 ����Ǿ����ϴ�.");
	}

	function rollbackList(){
		ifrmHidden.location.href="email.cfg.proc.php?mail_mode=<?=$mode?>&mode=rollbackView";
	}

	function sel_file(filename){
		ifrmHidden.location.href="email.cfg.proc.php?mail_mode=<?=$mode?>&mode=rollbackForm&filename="+filename;
	}

	function rollbackList_remove(){
		document.getElementById("rollbackList").innerHTML='';
	}
	function skin_sel(skinType){
		if (confirm('�����Ͻ� �⺻������� �ʱ�ȭ�Ͻðڽ��ϱ�?\n\n���� �������� �����ǰ� �ʱ�ȭ �ǹǷ� \n�ʿ� �� HTML�� ������ �����Ͻ� �� �ʱ�ȭ �Ͻñ� �ٶ��ϴ�.'))
		{
			ifrmHidden.location.href="email.cfg.proc.php?skinType="+skinType+"&mail_mode=<?=$mode?>&mode=skin_select";
		}
	}
	function emailModify(){
		if(confirm('�⺻�������� �������� �̵��մϴ�. \n�̵� �� �ۼ����� ������ ������� �ʽ��ϴ�.\n\n�̵��Ͻðڽ��ϱ�?'))
		{
			location.href='../basic/default.php';
		}

	}
//-->
</script>
<? include "../_footer.php"; ?>