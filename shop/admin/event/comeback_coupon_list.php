<?
$location = "�Ĺ� ����/SMS > �Ĺ� ����/SMS ����";
include "../_header.php";

$db_table = GD_COMEBACK_COUPON;
$_GET[page_num] = $_GET[page_num] ? $_GET[page_num] : 20;

$pg = new Page($_GET['page'],$_GET['page_num']);
$pg -> field = "*";

$pg->cntQuery = "SELECT COUNT(*) FROM ".$db_table;

$pg->setQuery($db_table,'',"sno DESC");
$pg->exec();

$res = $db->query($pg->query);
?>
<style>
#alt_msg {border: solid 4px #dce1e1; border-collapse: collapse; margin-bottom: 20px; padding: 10px 0 10px 10px;}
#alt_msg img {float: left; padding: 15px 15px;}
</style>
<script type="text/javascript">
function comeback_coupon_copy(sno) {
	if (confirm("�Ĺ鱸��/SMS�� ������ �������� ���� ��ϵ˴ϴ�.\n�̹� �߱޵� ������ ���� ȸ������ ��߱��� �� ����, ����������� ���� ������ ����� �� �����Ƿ� ���ο� ��� ������ ����/���� �� �߱��Ͻñ� �ٶ��ϴ�.")) {
		location.href = "./comeback_coupon_indb.php?mode=copy&sno="+sno;
	}
}

function comeback_coupon_delete(sendyn, sno) {
	var confirm_text = "�����Ͻðڽ��ϱ�?";
	if (sendyn == 'y') confirm_text = "������ ���� �߱� ����, SMS �߼۰���� Ȯ���� �� �����ϴ�. "+confirm_text;

	if (confirm(confirm_text)) {
		location.href = "./comeback_coupon_indb.php?mode=delete&sno="+sno;
	}
}
</script>

<div class="title title_top">�Ĺ� ����/SMS &nbsp; &nbsp; 
    <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=25')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
</div>
<div id="alt_msg">
	<img src="../img/btn__icon.gif"/>
	<ul>
		<li>�ֹ� �̷��� �ִ� �� �� �ѵ��� �ֹ��� ���� �ʾҴ� ������ �Ĺ� ������ �����ϰ�</li>
		<li>SMS�� �߼��� �� �湮�� �����ϼ���!</li>
		<li>������ �߱��ϰų� SMS�� �߼� �� ���� �ֽ��ϴ�.</li>
	</ul>
</div>

<div class="title title_top">���� SMS ���� �Է�<span>���� SMS ������ �Է��ϼ���. </span>
    <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=25')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
</div>
<table class="tb">
    <col class="cellC">
    <col class="cellL">
    <tr>
        <td>ȸ����ȭ��ȣ</td>
        <td><?=$cfg['smsRecall']?>
            <p class="extext">*<strong>SMS �ڵ��߼�/���� �޴�</strong>���� �߽Ź�ȣ�� ������ �ּ���.
                <a href="../member/sms.auto.php">[�ٷΰ���]</a></p>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="padding:7px 0px 10px 10px">
            <table style="width: 700px;">
                <tr>
                    <td>
                        <? $sms = Core::loader('Sms'); ?>
                        �ܿ� SMS ����Ʈ :
                        <span style="font-weight:bold;color:#627DCE;"><?=number_format($sms->smsPt)?></span> ��
                    </td>
                    <td>
                        <div style="padding-top:7px; color:#666666" class="g9">SMS ����Ʈ�� ���� ��� SMS�� �߼۵��� �ʽ��ϴ�.</div>
                        <div style="padding-top:5px; color:#666666" class="g9">SMS����Ʈ�� �����Ͽ� �߼��Ͻñ� �ٶ��ϴ�.</div>
                    </td>
                    <td>
                        <a href="../member/sms.pay.php"><img src="../img/btn_point_pay.gif"/></a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<div style="padding-top:15px"></div>

<div class="title title_top">�Ĺ� ���� / SMS ����Ʈ<span>���� �߱�/SMS �߼� ������ ����� �����մϴ�.</span>
    <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=25')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
</div>

<div class="right" style="margin-bottom:5px;">
	<img src="../img/sname_output.gif" align="absmiddle" />
	<select name=page_num onchange="location.href=location.pathname+'?page_num='+this.value;">
	<?
	$r_pagenum = array(10,20,40,60,100);
	foreach ($r_pagenum as $v){
	?>
	<option value="<?=$v?>" <?=($v == Clib_Application::request()->get('page_num')) ? 'selected' : ''?>><?=$v?>�� ���
	<? } ?>
	</select>
	<a href="./comeback_coupon_form.php"><img src="../img/btn_comeback_new.gif" align="absmiddle" /></a>
</div>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<col><col width="200"><col width="100"><col width="100"><col width="250"><col width="100"><col width="150">
<tr class=rndbg>
	<th><font class=small1>�̸�</th>
	<th><font class=small1>����</th>
	<th><font class=small1>����</th>
	<th><font class=small1>�߼۴��</th>
	<th><font class=small1>�߼۳��� ��ȸ</th>
	<th><font class=small1>�߱�/�߼�</th>
	<th><font class=small1>����/����/����</th>
</tr>
<tr><td class=rnd colspan=11></td></tr>
<? while ($data = $db->fetch($res)) {?>
<tr height=35>
	<td style="padding-left:10px;"><a href="./comeback_coupon_form.php?sno=<?=$data['sno']?>"><?=$data['title']?></a></td>
	<td align="center">
		<?if ($data['couponyn'] == 'y') { echo '<img src="../img/img_01.gif" align="absmiddle" /> ����';}?>
		<?if ($data['smsyn'] == 'y') { echo '<img src="../img/img_02.gif" align="absmiddle" /> SMS';}?>
	</td>
	<td align="center"><?if ($data['sendyn'] == 'y') { echo "�Ϸ�";} else { echo "���";}?></td>
	<td align="center"><?if ($data['sendyn'] == 'n') { echo '<a onclick="popup(\'popup.comeback_coupon_user.php?sno='.$data['sno'].'\',550,850)" class="hand"><img src="../img/btn_comeback_see.gif" align="absmiddle" /></a>';}?></td>
	<td align="center">
		<?if ($data['sendyn'] == 'y' && $data['couponyn'] == 'y') { echo '<a onclick="popup(\'popup.coupon_user.php?couponcd='.$data['couponcd'].'&applysno='.$data['applysno'].'\',650,850)" class="hand"><img src="../img/btn_comeback_coupon.gif" align="absmiddle" /></a>';}?>
		<?if ($data['sendyn'] == 'y' && $data['smsyn'] == 'y') { echo '<a onclick="popup(\'../member/popup.sms.sendList.php?sms_logNo='.$data['sms_logNo'].'\',800,750)" class="hand"><img src="../img/btn_comeback_sms.gif" align="absmiddle" /></a>';}?>
	</td>
	<td align="center"><?if ($data['sendyn'] == 'n') { echo '<a href="./comeback_coupon_indb.php?sno='.$data['sno'].'&mode=send"><img src="../img/btn_comeback_send.gif" align="absmiddle" /></a>';}?></td>
	<td align="center">
		<a onclick="comeback_coupon_copy('<?=$data['sno']?>')" class="hand"><img src="../img/btn_comeback_copy.gif" align="absmiddle" /></a>
		<?if ($data['sendyn'] == 'n') {?><a href="./comeback_coupon_form.php?sno=<?=$data['sno']?>"><img src="../img/buttons/btn_modify_small.gif" align="absmiddle" /></a><?}?>
		<a onclick="comeback_coupon_delete('<?=$data['sendyn']?>','<?=$data['sno']?>')" class="hand"><img src="../img/i_del.gif" align="absmiddle" /></a>
	</td>
</tr>
<tr><td colspan=11 class=rndline></td></tr>
<? } ?>
</table>
<div class="pageNavi" align=center><font class=ver8><?=$pg->page[navi]?></div>

<? include "../_footer.php"; ?>