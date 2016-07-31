<?

$location = '���ݿ����� ���� > ���ݿ����� �߱�/��ȸ';
include '../_header.php';
include '../../lib/page.class.php';
include '../../lib/cashreceipt.class.php';
$cashreceipt = new cashreceipt();

### ���� ����
$_GET['sword'] = trim($_GET['sword']);

list ($total) = $db->fetch("select count(*) from ".GD_CASHRECEIPT);

$selected['skey'][$_GET['skey']] = 'selected';
$checked['singly'][$_GET['singly']] = 'checked';

$db_table = GD_CASHRECEIPT;

if ($_GET['skey'] == 'certno' && strlen($_GET['sword']) == 13)
{
	$certno_encode = encode(substr($_GET['sword'],6,7),1);
	$certno = substr($_GET['sword'],0,6);
	$where[] = "certno = '{$certno}' and certno_encode = '{$certno_encode}'";
}
else if ($_GET['sword']) $where[] = "{$_GET['skey']} like '%{$_GET['sword']}%'";
if ($_GET['regdt'][0] && $_GET['regdt'][1]) $where[] = "regdt between date_format({$_GET['regdt'][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET['regdt'][1]},'%Y-%m-%d 23:59:59')";
if ($_GET['status']){
	foreach ($_GET['status'] as $v) {
		if ($v == 'FFF'){
			$whereAdd = "errmsg != ''";
		}
	}
	if ($whereAdd) {
		$where[] = "(status in ('".implode("','",$_GET['status'])."') OR ".$whereAdd.")";
	} else {
		$where[] = "status in ('".implode("','",$_GET['status'])."')";
		$where[] = "errmsg = ''";
	}
	foreach ($_GET['status'] as $v) $checked['status'][$v] = "checked";
}
if ($_GET['singly'] == 'Y') $where[] = "singly = 'Y'";
else if ($_GET['singly'] == 'N') $where[] = "singly != 'Y'";

$pg = new Page($_GET['page']);
$pg->field = "*";
$pg->setQuery($db_table,$where,'crno desc');
$pg->exec();

$res = $db->query($pg->query);

?>
<script>
function fnRequestReceipt() {

	var frm	= document.frmReceiptList;

	// ó���� �ֹ��� üũ.
	var cnt = 0, chk = document.getElementsByName('chk[]');

	for (var i =0;i<chk.length ;i++)
		if (chk[i].checked == true) cnt++;

	if (cnt == 0) {
		alert('ó���� �ֹ����� ������ �ּ���.');
		return;
	} else {
		if (confirm('������ '+ cnt + ' ���� �ֹ��� ���ؼ� ���ݿ������� �ϰ� �߱� �Ͻðڽ��ϱ�?') ) {
			frm.submit();
		} else {
			return;
		}
	}
}

function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F9FFA1" : "#ffffff";
}

function chkBoxAll(El,mode)
{
	if (!El || !El.length) return;
	for (i=0;i<El.length;i++){
		if (El[i].disabled) continue;
		El[i].checked = (mode=='rev') ? !El[i].checked : mode;
		iciSelect(El[i]);
	}
}
</script>
<form name="frmList">

<div class="title title_top">���ݿ����� �߱�/��ȸ<span>���ݿ����� �߱޽�û���� ��ȸ �� ���ݿ������� �߱��� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=18')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�˻���</td>
	<td>
	<select name="skey">
	<option value="buyername" <?=$selected['skey']['buyername']?>>�ֹ���
	<option value="ordno" <?=$selected['skey']['ordno']?>>�ֹ���ȣ
	<option value="receiptnumber" <?=$selected['skey']['receiptnumber']?>>���ι�ȣ
	<option value="certno" <?=$selected['skey']['certno']?>>��������
	<option value="goodsnm" <?=$selected['skey']['goodsnm']?>>��ǰ��
	<option value="buyerphone" <?=$selected['skey']['buyerphone']?>>��ȭ��ȣ
	<option value="buyeremail" <?=$selected['skey']['buyeremail']?>>�̸���
	</select>
	<input type="text" name="sword" class="lline" value="<?=$_GET[sword]?>">
	<span class="small4" style="color:#6d6d6d">* �������� - �ڵ�����ȣ/����ڹ�ȣ</span>
	</td>
</tr>
<tr>
	<td>�߱޽�û��</td>
	<td>
	<input type="text" name="regdt[]" value="<?=$_GET['regdt'][0]?>" onclick="calendar()" onkeydown="onlynumber()"> -
	<input type="text" name="regdt[]" value="<?=$_GET['regdt'][1]?>" onclick="calendar()" onkeydown="onlynumber()">
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle"></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle"></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle"></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle"></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle"></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align="absmiddle"></a>
	</td>
</tr>
<tr>
	<td>ó������</td>
	<td class="noline">
	<? $idx = 0; foreach ($cashreceipt->r_status as $k=>$v){ ?>
	<div style="float:left; padding-right:10px"><font class="small1" color="#5C5C5C"><input type="checkbox" name="status[]" value="<?=$k?>" <?=$checked['status'][$k]?>><?=$v?></div>
	<? } ?>
	<div style="float:left; padding-right:10px"><font class="small1" color="#5C5C5C"><input type="checkbox" name="status[]" value="FFF" <?=$checked['status']['FFF']?>>�߱޽���</div>
	</td>
</tr>
<tr>
	<td>�����߱�</td>
	<td class="noline">
		<div style="float:left; padding-right:10px"><font class="small1" color="#5C5C5C"><input type="radio" name="singly" value="" <?=$checked['singly']['']?>>��ü</div>
		<div style="float:left; padding-right:10px"><font class="small1" color="#5C5C5C"><input type="radio" name="singly" value="Y" <?=$checked['singly']['Y']?>>�����߱�</div>
		<div style="float:left; padding-right:10px"><font class="small1" color="#5C5C5C"><input type="radio" name="singly" value="N" <?=$checked['singly']['N']?>>�ֹ��߱�</div>
	</td>
</tr>
</table>
<div class="button_top"><input type="image" src="../img/btn_search2.gif"></div>

<div class="pageInfo ver8">�� <b><?=$total?></b>��, �˻� <b><?=$pg->recode['total']?></b>��, <b><?=$pg->page['now']?></b> of <?=$pg->page['total']?> Pages</div>
</form>

<form name="frmReceiptList" method="post" action="./cashreceipt.indb.php">
<input type="hidden" name="mode" value="chgAllApproval" />
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="12"></td></tr>
<tr class="rndbg">
	<th><a href="javascript:void(0)" onClick="chkBoxAll(document.getElementsByName('chk[]'),'rev')" class=white>����</a></th>
	<th>��ȣ</th>
	<th>��û����</th>
	<th>ó������</th>
	<th>�ֹ���ȣ</th>
	<th>�ֹ���</th>
	<th>�߱ޱݾ�</th>
	<th>�ֹ�����</th>
	<th>ó������</th>
	<th>����</th>
	<th>������</th>
	<th>���</th>
</tr>
<tr><td class="rnd" colspan="12"></td></tr>
<col width="4%" align="center">
<col width="4%" align="center">
<col width="10%" align="center">
<col width="10%" align="center">
<col width="17%" align="center">
<col width="10%" align="center">
<col width="10%" align="center">
<col width="10%" align="center">
<col width="10%" align="center">
<col width="5%" align="center">
<col width="5%" align="center">
<col width="5%" align="center">
<?
while ($data=$db->fetch($res))
{
	// �ϰ��߱� üũ�ڽ� ó��
	$disabled	= 'disabled="disabled"';

	# �ֹ�����
	if ($data['singly'] == 'Y')
	{
		$step = '�����߱�';
		$disabled	= '';
	}
	else
	{
		$order = $db->fetch("select ordno, step, step2 from ".GD_ORDER." where ordno='{$data['ordno']}'");
		if ($order['ordno'] == '') $step = '�����ֹ���';
		else $step = getStepMsg($order['step'],$order['step2'],$order['ordno']);
		if(strlen($step) > 10) $step = substr($step,10);
	}

	# ó������
	$status = $cashreceipt->r_status[ $data['status'] ];
	if ($data['errmsg']){
		$status = '<span class="red hand" onclick="alert(\''.addslashes($data['errmsg']).'\')">�߱޽���</span>';
	}

	// ������
	$receipt_button	= '';
	if ($data['receiptnumber'] != '')
	{
		$receipturl =  $cashreceipt->getReceipturl($data['crno']);
		if ($receipturl == '')
			$receipt_button	= '<span class="hand" onclick="alert(\'������ ����� �������� �ʽ��ϴ�.\');"><img src="../img/i_receipt_off.gif"></span>';
		else
			$receipt_button	= '<span class="hand" onclick="window.open(\''.$receipturl.'\',\'\',\'width=400,height=600,scrollbars=0\');"><img src="../img/i_receipt_on.gif"></span>';
	}

	# ���
	$button		= array();
	if ($data['status'] == 'RDY')
	{
		if ($order['step2'] == 0){
			$button[] = '<span class="hand" onclick="if(confirm(\'�߱��Ͻðڽ��ϱ�?\')) location.replace(\'./cashreceipt.indb.php?mode=approval&crno='.$data['crno'].'\');"><img src="../img/i_approval.gif"></span>';
			if ($order['step'] > 0){
				$disabled	= '';
			}
		}
		$button[] = '<a href="./cashreceipt.indb.php?mode=refuse&crno='.$data['crno'].'" onclick="return confirm(\'������ �����Ͻðڽ��ϱ�?\')"><img src="../img/i_refuse.gif"></a>';
	}

	if ($data['status'] == 'ACK')
	{
		$button[] = '<span class="hand" onclick="if(confirm(\'����Ͻðڽ��ϱ�?\')) location.replace(\'./cashreceipt.indb.php?mode=cancel&crno='.$data['crno'].'\');"><img src="../img/i_cancel.gif"></span>';
	}

	if (in_array($data['status'], array('CCR', 'RFS')))
	{
		$existOrder = 'N';
		if ($data['singly'] != 'Y'){
			list($cnt) = $db->fetch("select count(ordno) from ".GD_ORDER." where ordno='{$data['ordno']}' and cashreceipt='{$data['cashreceipt']}'");
			if ($cnt) $existOrder = 'Y';
		}
		$button[] = '<a href="./cashreceipt.indb.php?mode=del&crno='.$data['crno'].'"><img src="../img/i_del.gif" onclick="return del(this, \''.$existOrder.'\');"></a>';
	}
?>
<tr><td height="4" colspan="12"></td></tr>
<tr height="25">
	<td align="center" class=noline><input type="checkbox" name="chk[]" value="<?=$data['ordno']?>" onclick="iciSelect(this)" required label="���û����� �����ϴ�" <?=$disabled?>></td>
	<td align="center"><font class="ver8"><?=$pg->idx--?></font></td>
	<td align="center"><font class="ver81"><?=substr($data['regdt'],0,-3)?></font></td>
	<td align="center"><font class="ver811" style="color:#0074BA"><? if ($data['regdt'] != $data['moddt']) { echo substr($data['moddt'],0,-3); }?></font></td>
	<td>
	<? if ($data['singly'] == 'Y'){ ?>
	<font class="ver811 bluep"><b><?=$data['ordno']?></b></font>
	<? } else { ?>
	<a href="view.php?ordno=<?=$data['ordno']?>"><font class="ver811 blue"><b><?=$data['ordno']?></b></font></a>
	<a href="javascript:popup('popup.order.php?ordno=<?=$data['ordno']?>',800,600)"><img src="../img/btn_newwindow.gif" border="0" align="absmiddle"></a>
	<? } ?>
	</td>
	<td align="center"><?=$data['buyername']?></td>
	<td align="center"><font class="ver8 blue"><b><?=number_format($data['amount'])?></b></font></td>
	<td align="center"><?=$step?></td>
	<td align="center"><?=$status?></td>
	<td align="center"><span onclick="popupLayer('./cashreceipt.info.php?crno=<?=$data['crno']?>',650,500)" class="hand"><img src="../img/btn_vieworder.gif" alert="�ڼ��� ����" /></span></td>
	<td align="center"><?=$receipt_button?></td>
	<td align="center"><?=implode(' ', $button)?></td>
</tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
<tr>
	<td height=20 align="center"><a href="javascript:chkBoxAll(document.getElementsByName('chk[]'),'rev')"><img src="../img/btn_allchoice.gif" border=0></a></td>
	<td colspan=11 height=20></td>
</tr>

</table>
<div align="center" class="pageNavi ver8"><?=$pg->page['navi']?></div>

<div style="height:30px;"><span class="hand" onclick="fnRequestReceipt();"><img src="../img/btn_allapproval.gif" border="0" align="absmiddle" alert="���� �ֹ� �ϰ� �߱�" /></span></div>

</form>

<div id="MSG01">
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���̽��� ó����� (�ֹ�����/ó������)
<ol type="a" style="margin:0px 0px 0px 40px;">
<li>�Ա�Ȯ�� + �߱޿�û : �������� <b>[�߱�]</b> �մϴ�.</li>
<li>�Ա�Ȯ�� + �߱޿Ϸ� : �߱޵� <b>[������]</b> �� Ȯ���� �� �ֽ��ϴ�.</li>
<li>��ҿϷ� + �߱޿�û : �߱޿�û�� <b>[����]</b> �մϴ�.</li>
<li>��ҿϷ� + �߱ް��� : ��û������ �ʿ���ٸ� <b>[����]</b> �� �� �ֽ��ϴ�.</li>
<li>ȯ�ҿϷ� + �߱޿Ϸ� : �߱޵� �������� <b>[���]</b> �մϴ�.</li>
<li>ȯ�ҿϷ� + �߱���� : ��û������ �ʿ���ٸ� <b>[����]</b> �� �� �ֽ��ϴ�.</li>
</ol>
</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">ó������ �ȳ�
<ol type="a" style="margin:0px 0px 0px 40px;">
<li><b>[�߱޿�û]</b> : ���ݿ����� �߱��� ��û�� �����Դϴ�.</li>
<li><b>[�߱޿Ϸ�]</b> : ���ݿ������� �߱޵� �����Դϴ�.</li>
<li><b>[�߱����]</b> : ���ݿ����� �߱� �� ����� �����Դϴ�.</li>
<li><b>[�߱ް���]</b> : ���ݿ����� �߱� ��û�� ������ �����Դϴ�.</li>
<li><b>[�߱޽���]</b> : ���ݿ����� �߱� ��û�Ͽ����� ������ �����Դϴ�.</li>
</ol>
</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��� �ȳ�
<ol type="a" style="margin:0px 0px 0px 40px;">
<li><b>[�߱�]</b> : �ֹ����°� �Ա�Ȯ�� �����϶� �߱޹�ư�� ���� ���ݿ������� �߱��մϴ�.</li>
<li><b>[����]</b> : �����ÿ� ������ư�� �����ֽø� �˴ϴ�.</li>
<li><b>[���]</b> : ��ҽÿ� ��ҹ�ư�� ���� ���ݿ������� ����մϴ�.</li>
<li><b>[����]</b> : �����̳� ��Ұǿ� ���� ������ ������ �� �ֽ��ϴ�.</li>
<li><b>[�����׸� �ϰ��߱�]</b> : ���� ���� �߱޿�û �׸��� �����Ͽ� �ϰ��߱��մϴ�.</li>

</ol>
</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�� '����' ��ư�� Ŭ���Ͽ� �ش� ���ݿ������� ��û���� �� ó�����(�α�)�� Ȯ���� �� �ֽ��ϴ�.

</td></tr>

</table>
</div>
<script>cssRound('MSG01')</script>

<script language="javascript"><!--
function del(obj, existOrder)
{
	var orderinit = false;
	if (existOrder == 'Y' && confirm("�߱޽�û������ �����ϸ鼭 �ֹ��� ���ݿ�������ȣ�� �ʱ�ȭ�Ͻðڽ��ϱ�?\n�ʱ�ȭ�ϸ� �����ڰ� �������������� ���û�� �� �ֽ��ϴ�.")) orderinit = true;
	if (confirm("������ �����Ͻðڽ��ϱ�?"))
	{
		if (orderinit == true) obj.parentNode.href += '&orderinit=Y';
		return true;
	}
	return false;
}
//--></script>

<? include '../_footer.php'; ?>