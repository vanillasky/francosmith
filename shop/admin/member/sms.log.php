<?
header("location:./sms.sendList.php");
exit;
$location = "SMS���� > SMS �߼۳���";
include "../_header.php";
include "../../lib/page.class.php";

### �׷�� ��������
$query = "SELECT sms_grp FROM ".GD_SMS_ADDRESS." GROUP BY sms_grp ORDER BY sms_grp ASC";
$res = $db->query($query);
while ($data=$db->fetch($res)) $r_grp[] = $data['sms_grp'];

include "znd_sms.log.php";

?>
<style>
table.sms-log {word-break:break-all;}
table.sms-log tr {height:23px;}
table.sms-log tr td {color:#262626;font-family:Tahoma,Dotum;font-size:11px;}
table.sms-log tr td.status {color:#0070C0;}
table.sms-log tr.res td {color:#C00000}
table.sms-log tr.res td.status {}

</style>

<div class="title title_top"><font face="����" color="black"><b>SMS</b></font> �߼۳���<span>���۵� SMS �߼۳����� �����մϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=18')"><img src="../img/btn_q.gif" align="absmiddle" hspace="2" /></a></div>

<table border="4" bordercolor="#dce1e1" style="border-collapse:collapse; margin-bottom:10px" width="700">
<tr><td style="padding:7 0 10 10">
<div style="padding-top:5"><b>�� SMS �߼۳��� Ȯ�� �ȳ�</b></div>
<div style="padding-top:7px; color:#666666" class="g9">�� �߼ۿϷ�� �Ǽ��� ����Ʈ�����Ǹ�, �߼۽��е� �Ǽ��� �Ϸ翡 �ѹ� ���� 1�ð濡 ����˴ϴ�.<br/>
&nbsp;&nbsp;&nbsp; (�׷���, ����1�� ���� ó�� sms �߼۵� �� ��Ȯ�� ���� �Ǽ��� �������� �˴ϴ�.)
</div>
<div style="padding-top:5px; color:#666666" class="g9">�� <span style="color:#627dce">���� ��Ȯ�� SMS �߼۳��� �����ʹ� ������ �α��� �Ͻ� ��, ���̰����� �ٿ�ε尡 �����մϴ�.<br/>
&nbsp;&nbsp;&nbsp; �޴� : ���� �α��� > ���̰� > ���� ���θ� > [������/����] Ŭ�� > SMS �߼� �������� �ٿ�ε�</span><br/>
&nbsp;&nbsp;&nbsp; <a href="http://www.godo.co.kr/mygodo/index.html" target="_blank"><font class=extext_l>[���̰� �ٷΰ��� > ]</font></a>
</div>
</td></tr>
</table>

<form>
<input type="hidden" name="search" value="yes" />

<table class="tb">
<col class="cellC" /><col class="cellL" />
<tr>
	<td>�߼ۻ���</td>
	<td class="noline">
	<label><input type="radio" name="status" value="" <?=$_GET['status'] == '' ? 'checked' : ''?>>��ü</label>
	<label><input type="radio" name="status" value="send" <?=$_GET['status'] == 'send' ? 'checked' : ''?>>�߼ۿϷ�</label>
	<label><input type="radio" name="status" value="res" <?=$_GET['status'] == 'res' ? 'checked' : ''?>>�߼ۿ���</label>
	</td>
</tr>
<tr>
	<td>�߼۱Ⱓ</td>
	<td>
	<input type="text" name="regdt[]" value="<?=$_GET['regdt'][0]?>" size="8" maxlength="8" onkeydown="onlynumber();" class="cline" /> -
	<input type="text" name="regdt[]" value="<?=$_GET['regdt'][1]?>" size="8" maxlength="8" onkeydown="onlynumber();" class="cline" />
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align="absmiddle" /></a>
</tr>
<tr>
	<td>���Ź�ȣ</td>
	<td><input type="text" name="tran_phone" value="<?=$_GET['tran_phone']?>" class="line"></td>
</tr>
</table>
<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div><p>

</form>

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="sms-log">
<tr><td class="rnd" colspan="14"></td></tr>
<tr class="rndbg">
	<th>��ȣ</th>
	<th>���۽ð�/���ۿ���ð�</th>
	<th>��������</th>
	<th>���Ź�ȣ</th>
	<th>����</th>
	<th>�޼���</th>
	<th>�������Ʈ</th>
	<th>����</th>
</tr>
<tr><td class="rnd" colspan="14"></td></tr>
<col width="35" align="center">
<col width="130" align="center">
<col width="80" align="center">
<col width="100" align="center">
<col width="200" align="center">
<col style="padding-left:10px">
<col width="80" align="center">
<col width="70" align="center">

<?
// ���� �߼��� ���, �߼� ���� ���θ� �˾ƿ� �� �����Ƿ�, ���簡 ������ �׳� �߼�����;;
$now = date('Y-m-d H:i:s');
if ($loop){ foreach ($loop as $data){
$reserved = ($data['reservedt'] > $now) ? true : false ;?>

<tr class="<?=$reserved ? 'res' : 'send'?>">
	<td><?=$pg->idx--?></td>
	<td><?=$data['reservedt'] != '0000-00-00 00:00:00' ? $data['reservedt'] : $data['regdt']?></td>
	<td><?=$data['sms_type']?></td>
	<td><?=$data['to_tran']?></td>
	<td><?=$data['subject']?></td>
	<td><?=$data['msg']?></td>
	<td><?=number_format($data['cnt'])?>����Ʈ</td>
	<td class="status"><?=$reserved ? '�߼ۿ���' : '�߼ۿϷ�'?></td>
</tr>
<tr><td colspan="14" class="rndline"></td></tr>
<? }} ?>
</table>

<div class="pageNavi" align="center"><font class="ver8"><?=$pg->page['navi']?></div>



<div id="MSG01">
<table cellpadding=1 cellspacing=0 border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />�߼ۿϷ�� �Ǽ��� ����Ʈ�����˴ϴ�.</td></tr>
<!--<tr><td><img src="../img/icon_list.gif" align="absmiddle" />������ <font color=0074BA>SMS ����Ʈ�� ȯ�ҵ��� �ʽ��ϴ�.</font></td></tr>-->
</table>
</div>
<script>cssRound('MSG01');</script>

<? include "../_footer.php"; ?>