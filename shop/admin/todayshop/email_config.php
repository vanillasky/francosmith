<?
/*
	mode ���� ������ sms_config.php �� arTitle �迭 ������ Ű���� �����
*/

$mode = $_GET[mode];

$location = "�����̼� > $loc[$mode]";
include "../_header.php";

$todayShop = &load_class('todayshop', 'todayshop');

if (!$todayShop->auth()) {
	msg(' ���� ��û�ȳ��� ���� �����ͷ� �������ֽñ� �ٶ��ϴ�.', -1);
}

$tsCfg = $todayShop->cfg;

$arTitle['orderc'] = array('title'=>'������ǰ �ֹ��Ϸ�� �ڵ��߼�', 'desc'=>'(���� �Ϸ�� �߼۵Ǵ� �޽����Դϴ�.)');
$arTitle['salec'] = array('title'=>'������ǰ �Ǹż����� �ڵ��߼�', 'desc'=>'(�ǸŰ� �����Ǹ� �߼۵˴ϴ�.)');
//$arTitle['giftc'] = array('title'=>'�����Ǹ� ������ �ڵ��߼�(�����ϱ�)', 'desc'=>'(������ �޴� ������� �߼۵˴ϴ�.)');
$arTitle['orderg'] = array('title'=>'�ǹ���ǰ �ֹ��Ϸ�� �ڵ��߼�', 'desc'=>'(���� �Ϸ�� �߼۵Ǵ� �޽����Դϴ�.)');
$arTitle['deliveryg'] = array('title'=>'�ǹ���ǰ ��۽� �ڵ��߼�', 'desc'=>'(�ǸŰ� �����ǰ� ���°� ��������� �ٲ� �� �߼۵Ǵ� �޼����Դϴ�.)');
$arTitle['cancel'] = array('title'=>'�ǸŽ��н� �ڵ��߼�', 'desc'=>'(��ǥ���ŷ��� �������� ���� ��� ���� ��� �޽��� �Դϴ�.)');

// �й� ��������
$mail_body = '';

if (preg_match('/\.php$/',$tsCfg['mailMsg_'.$mode]) && is_file('../../conf/email/'.$tsCfg['mailMsg_'.$mode])) {
	ob_start();
		include( '../../conf/email/'.$tsCfg['mailMsg_'.$mode] );
		$mail_body = ob_get_contents();
	ob_end_clean();
}
?>

<form method=post action="./indb.email_config.php" onsubmit="return chkForm(this)">

<div class="title title_top"><?=$arTitle[$mode]['title']?><span><?=$arTitle[$mode]['desc']?> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=13')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table width=100% class=tb>
<col class=cellC><col class=cellL>
<tr height=25>
	<td>�ڵ��߼ۿ���</td>
	<td class=noline>
	<label><input type=radio name=mailUse_<?=$mode?> value="y" <?=($tsCfg['mailUse_'.$mode] == 'y') ? 'checked' : ''?>>�ڵ����� ����</label>
	<label><input type=radio name=mailUse_<?=$mode?> value="n" <?=($tsCfg['mailUse_'.$mode] != 'y') ? 'checked' : ''?>>����������</label>
	</td>
</tr>

<tr height=25>
	<td>��������</td>
	<td><input type=text name="mailSbj_<?=$mode?>" value="<?=$tsCfg['mailSbj_'.$mode]?>" style="width:100%" required class="line"></td>
</tr>
<tr>
	<td>����</td>
	<td style="padding:5px">
	<textarea name=mailMsg_<?=$mode?> type=editor style="width:100%;height:500px"><?=htmlspecialchars($mail_body)?></textarea>
	<script src="../../lib/meditor/mini_editor.js"></script>
	<script>mini_editor("../../lib/meditor/")</script>
	</td>
</tr>
</table>

<div class=button>
<input type=image src="../img/btn_modify.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� �ϴܿ� �ִ� �ΰ�� <a href="../todayshop/codi.banner.php" target=_blank><font color=white><b>[�ΰ�/��ʰ���]</b></font></a> ���� ���Ϸΰ� ����Ͻø� �˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� ���뿡 ���̴� �̹������� <a href="../design/design_webftp.php" target=_blank><font color=white><b>[webFTP�̹������� > data > editor]</b></font></a> ���� �����ϼ���.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>