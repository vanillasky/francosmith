<?

$location = "SMS���� > SMS �Ϲ� �ּҷ�";
include "../_header.php";
include "../../lib/page.class.php";

$now = time();

### �׷�� ��������
$query = "SELECT sms_grp FROM ".GD_SMS_ADDRESS." GROUP BY sms_grp ORDER BY sms_grp ASC";
$res = $db->query($query);
while ($data=$db->fetch($res)) $r_grp[] = $data['sms_grp'];

### �� ���ڵ��
list ($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_SMS_ADDRESS."");

if (!$_GET['page_num']) $_GET['page_num'] = 10;
$orderby = ($_GET['sort']) ? $_GET['sort'] : "regdt desc"; # ���� ����

### �����Ҵ�
$selected['page_num'][$_GET['page_num']]	= "selected";
$selected['sort'][$orderby]					= "selected";
$selected['skey'][$_GET['skey']]			= "selected";
$selected['slevel'][$_GET['slevel']]		= "selected";
$checked['sex'][$_GET['sex']]				= "checked";
$checked['mailing'][$_GET['mailing']]		= "checked";

### ���
$db_table = GD_SMS_ADDRESS;

### �˻� ����
if ($_GET['skey'] && $_GET['sword']){
	if ( $_GET['skey']== 'all' ){
		$where[] = "concat( sms_name , sms_mobile ) LIKE '%".$_GET['sword']."%'";
	}
	else $where[] = $_GET['skey']." LIKE '%".$_GET['sword']."%'";
}
if ($_GET['slevel']!='') $where[] = "sms_grp='".$_GET['slevel']."'";
if ($_GET['sregdt'][0] && $_GET['sregdt'][1]) $where[] = "regdt between date_format(".$_GET['sregdt'][0].",'%Y-%m-%d 00:00:00') and date_format(".$_GET['sregdt'][1].",'%Y-%m-%d 23:59:59')";
if ($_GET['sex']) $where[] = "sex = '".$_GET['sex']."'";

$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->setQuery($db_table,$where,$orderby);
$pg->exec();

$res = $db->query($pg->query);
?>
<script language="JavaScript" type="text/JavaScript">
function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
}

function delSMSAddress(fm)
{
	if (!isChked(document.getElementsByName('chk[]'))) return;
	if (!confirm('SMS �ּҷϿ��� ������ ȸ���� �����Ͻðڽ��ϱ�?')) return;
	fm.target = "_self";
	fm.mode.value = "sms_address_del";
	fm.action = "indb.php";
	fm.submit();

}

function allDelSMSAddress(fm)
{
	if(confirm('SMS �Ϲ� �ּҷϿ� ��ϵ� ������ ��� ���� �˴ϴ�.\n����Ͻðڽ��ϱ�?')){
		fm.target = "_self";
		fm.mode.value = "sms_address_allDel";
		fm.action = "indb.php";
		fm.submit();
	}
}

function sendSMS(sno) {

	var x = (window.screen.width - 800) / 2;
	var y = (window.screen.height - 600) / 2;

	var smswin = window.open('about:blank', "smswin", "width=800, height=600, scrollbars=yes, left=" + x + ", top=" + y);

	var f = document.fmList;
	f.target = 'smswin';
	f.action = '../member/popup.sms.php';

	if (sno)	// ���� �߼�
	{
		f.sno.value = sno;
		f.type.value = 1;
	}
	else {
		f.type.value = f.target_type.value;
	}
	f.submit();

}

</script>

<form>

<div class="title title_top">SMS �Ϲ� �ּҷ�<span>���� �� ���θ��� SMS �ּҷ��� �ľ��ϰ� SMS�� ���� �� �ֽ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=16')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a></div>
<table class="tb">
<col class="cellC"><col class="cellL" style="width:250">
<col class="cellC"><col class="cellL">
<tr>
	<td>Ű����˻�</td>
	<td>
	<select name="skey">
	<option value="all" <?=$selected['skey']['all']?>> ���հ˻� </option>
	<option value="sms_name" <?=$selected['skey']['sms_name']?>> �̸� </option>
	<option value="sms_mobile" <?=$selected['skey']['sms_mobile']?>> ������ȣ </option>
	</select> <input type="text" NAME="sword" value="<?=$_GET['sword']?>"  class="line"/>
	</td>
	<td>�׷�</td>
	<td>
	<select name="slevel">
	<option value="">==�׷켱��==</option>
	<? foreach( $r_grp as $v ){ ?>
	<option value="<?=$v?>" <?=$selected['slevel'][$v]?>><?=$v?></option>
	<? } ?>
	</select>
	</td>
</tr>
<tr>
	<td>�ۼ���</td>
	<td colspan="3">
	<input type="text" name="sregdt[]" value="<?=$_GET[sregdt][0]?>" onclick="calendar(event);" class="cline" /> -
	<input type="text" name="sregdt[]" value="<?=$_GET[sregdt][1]?>" onclick="calendar(event);" class="cline" />
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd", $now)?>,<?=date("Ymd", $now)?>);"><img src="../img/sicon_today.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-7 day", $now))?>,<?=date("Ymd", $now)?>);"><img src="../img/sicon_week.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-15 day", $now))?>,<?=date("Ymd", $now)?>);"><img src="../img/sicon_twoweek.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-1 month", $now))?>,<?=date("Ymd", $now)?>);"><img src="../img/sicon_month.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-2 month", $now))?>,<?=date("Ymd", $now)?>);"><img src="../img/sicon_twomonth.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]');"><img src="../img/sicon_all.gif" align="absmiddle" /></a>
	</td>
</tr>
<tr>
	<td>����</td>
	<td class="noline">
	<input type="radio" name="sex" value="" <?=$checked['sex']['']?> />��ü
	<input type="radio" name="sex" value="M" <?=$checked['sex']['M']?> />����
	<input type="radio" name="sex" value="F" <?=$checked['sex']['F']?> />����
	</td>
	<td>���� SMS �Ǽ�</td>
	<td>
		<span style="font-weight:bold"><font class="ver9" color="0074ba"><b id="span_sms2"><?=number_format(getSmsPoint())?></b></span><font color="262626">��</font>
		<a href="javascript:location.href='../member/sms.pay.php';"><img src="../img/btn_smspoint.gif" align="absmiddle"></a>
	</td>
</tr>
</table>
<div class="button_top">
<input type="image" src="../img/btn_search2.gif" />
<a href="javascript:popupLayer('../member/popup.sms_address.php?mode=regist',600,330)"><img src="../img/btn_address_add.gif" /></a>
<a href="javascript:popupLayer('../member/popup.sms_address.php?mode=excel',600,330)"><img src="../img/btn_address_add_by_excel.gif" /></a>
</div>

<table width="100%">
<tr>
	<td class="pageInfo">
	�� <font class="ver8"><b><?=number_format($total)?></b>��, �˻� <b><?=number_format($pg->recode['total'])?></b>��, <b><?=number_format($pg->page['now'])?></b> of <?=number_format($pg->page['total'])?> Pages
	</td>
	<td align="right">
	<select name="sort" onchange="this.form.submit();">
	<option value="regdt desc" <?=$selected['sort']['regdt desc']?>>- �ۼ��� ���ġ�</option>
	<option value="regdt asc" <?=$selected['sort']['regdt asc']?>>- �ۼ��� ���ġ�</option>
    <optgroup label="------------"></optgroup>
    <option value="sms_grp desc" <?=$selected['sort']['sms_grp desc']?>>- �׷� ���ġ�</option>
	<option value="sms_grp asc" <?=$selected['sort']['sms_grp asc']?>>- �׷� ���ġ�</option>
	<option value="sms_name desc" <?=$selected['sort']['sms_name desc']?>>- �̸� ���ġ�</option>
	<option value="sms_name asc" <?=$selected['sort']['sms_name asc']?>>- �̸� ���ġ�</option>
	<option value="sms_mobile desc" <?=$selected['sort']['sms_mobile desc']?>>- �ڵ�����ȣ ���ġ�</option>
	<option value="sms_mobile asc" <?=$selected['sort']['sms_mobile asc']?>>- �ڵ�����ȣ ���ġ�</option>
	</select>&nbsp;
	<select name="page_num" onchange="this.form.submit();">
	<?
	$r_pagenum = array(10,20,40,60,100);
	foreach ($r_pagenum as $v){
	?>
	<option value="<?=$v?>" <?=$selected[page_num][$v]?> /><?=$v?>�� ���
	<? } ?>
	</select>
	</td>
</tr>
</table>
</form>

<form name="fmList" id="fmList" method="post">
<input type="hidden" name="mode" value="addressbook" />
<input type="hidden" name="query" value="<?=substr($pg->query,0,strpos($pg->query,"limit"))?>" />
<input type="hidden" name="type" value="" />
<input type="hidden" name="sno" value="" /><!-- �ּҷ� sno -->
<input type="hidden" name="group" value="<?=$_GET[slevel]?>" /><!-- �ּҷ� �׷� -->

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="14"></td></tr>
<tr class="rndbg">
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev');" class="white">����</a></th>
	<th>��ȣ</th>
	<th>�̸�</th>
	<th>�׷�</th>
	<th>�ڵ�����ȣ</th>
	<th>����</th>
	<th>���</th>
	<th>�ۼ�����</th>
	<th>����</th>
	<th>SMS������</th>

</tr>
<tr><td class="rnd" colspan="14"></td></tr>
<col width="5%" align="center">
<col width="5%" align="center">
<col width="10%" align="center">
<col width="10%" align="center">
<col width="15%" align="left">
<col width="5%" align="center">
<col width="27%" align="left">
<col width="10%" align="center">
<col width="5%" align="center">
<col width="8%" align="center">
<?
while ($data=$db->fetch($res)){
	if($data['sex'] == "F") $sexStr	= "����";
	if($data['sex'] == "M") $sexStr	= "����";
	
	//SMS �߼� ���� ����
	$smsFailCheck = smsFailCheck('single', $data['sms_mobile']);
?>
<tr height="30" align="center">
	<td class="noline"><input type="checkbox" name="chk[]" value="<?=$data['sno']?>" onclick="iciSelect(this);" /></td>
	<td><font class="ver81" color="616161"><?=$pg->idx--?></font></td>
	<td><font color="0074ba"><b><?=$data['sms_name']?></b></font></td>
	<td><font color="0074ba"><b><?=$data['sms_grp']?></b></font></td>
	<!--td><a href="javascript:popupLayer('../member/popup.sms.php?sno=<?=$data['sno']?>',780,600)"><img src="../img/btn_smsmailsend.gif" align="absmiddle" /></a> <font class=ver71 color="0074ba"><?=$data['sms_mobile']?></font></td-->
	<td><font class=ver71 color="0074ba"><?=$data['sms_mobile']?></font>&nbsp;<img src="../img/btn_sms_sendinfo.gif" style="vertical-align: middle; cursor:pointer; border: 0px; padding-left: 3px;" onclick="javascript:popup('./popup.sms.sendView.php?sms_phoneNumber=<?php echo $data['sms_mobile']; ?>', '700', '500');" /><?php if($smsFailCheck === true){ ?><br /><div style="color: red; padding-top: 3px;">SMS �߼۽��� ��ȣ</div> <?php } ?></td>
	<td><font class="ver81" color="616161"><?=$sexStr?></font></td>
	<td><font class="small" color="616161"><?=$data['sms_etc']?></font></td>
	<td><font class="ver81" color="616161"><?=$data['regdt']?></font></td>
	<td><a href="javascript:popupLayer('../member/popup.sms_address.php?sno=<?=$data['sno']?>',600,260)"><img src="../img/i_edit.gif" /></a></td>
	<td><a href="javascript:void(0);" onClick="sendSMS(<?=$data['sno']?>);"><img src="../img/btn_smsmailsend.gif" align="absmiddle" /></a></td>
</tr>
<tr><td colspan="14" class="rndline"></td></tr>
<? } ?>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td width="20%" height="35" style="padding-left:13px">
<a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev');" class="white"><img src="../img/btn_allchoice.gif" border="0" /></a>
<a href="javascript:delSMSAddress(document.fmList)"><img src="../img/btn_all_delet.gif" border="0" /></a>
<a href="javascript:allDelSMSAddress(document.fmList)"><img src="../img/btn_address_allcancel.gif" border="0" /></a>
</td>
<td width="60%" align="center"><font class="ver8"><?=$pg->page[navi]?></font></td>
<td width="20%"></td>
</tr></table>

<div style='font:0;height:10'></div>
<div align=center>
<table bgcolor=F7F7F7 width=100%>
<tr>
	<td class=noline width=57% align=right>
	<select name=target_type>
		<option value="5">������ ��󿡰� SMS ������</option>
		<option value="4">�˻��� ��󿡰� SMS ������</option>
	</select>
	</td>
	<td width=43% style="padding-left:10px">
	<a href="javascript:void(0)" onClick="sendSMS()"><img src="../img/btn_today_email_sm.gif"></a>
	</td>
</tr>
</table>
</div>


</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />�⺻���� ȸ�� �����̿ܿ� ��ü,����ó, ģ������ �ڵ�����ȣ�� ���� �Ҽ� ������, ����, �˻��� ���ؼ� SMS�� ���� �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script language="JavaScript" type="text/JavaScript">cssRound('MSG01');</script>

<script language="JavaScript" type="text/JavaScript">window.onload = function(){ UNM.inner();};</script>

<? include "../_footer.php"; ?>