<?

$location = "�ڵ��Ա�Ȯ�� ���� > �Ա���ȸ / �ǽð��Ա�Ȯ��";
include "../_header.php";

### �Ա��� default 7��
if ( $_GET[bkdate][0]=='' && $_GET[bkdate][1] == '' )
{
	$_GET[bkdate][0] = date("Ymd",strtotime("-7 day"));
	$_GET[bkdate][1] = date("Ymd");
}

### �����Ҵ�
if (!$_GET[page_num]) $_GET[page_num] = 10; # ������ ���ڵ��
$selected[page_num][$_GET[page_num]] = "selected";

$orderby = ($_GET[sort]) ? $_GET[sort] : "bkdate desc"; # ���� ����
$selected[sort][$orderby] = "selected";

$selected[skey][$_GET[skey]] = "selected";
$selected[gdstatus][$_GET[gdstatus]] = "selected";
$selected[bkname][$_GET[bkname]] = "selected";

$r_bank = array('�������','��������','��ȯ����','��������','�����߾�ȸ','��������','�츮����','��������','��������','��������','��������','�ѹ�����','�뱸����','�λ�����','��������','��������','��������','�泲����','�������ݰ�','��ü��','�ϳ�����');

?>
<script src="../bankmatch.ajax.js"></script>

<script>

function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F9FFF0" : row.getAttribute('bg');
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
<form name=frmList onsubmit="return ( accountList() ? false : false );">
<div class="title title_top">�Ա���ȸ / �ǽð��Ա�Ȯ��<span>���忡 �Աݵ� ������ �ǽð����� ��ȸ�ϸ�, �Աݵ� ������ �ǽð����� �Ա�Ȯ��ó���մϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=17')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL width=35%>
<tr>
	<td>Ű����˻�</td>
	<td>
	<select name="skey">
	<option value="all" <?=$selected[skey]['all']?>> ���հ˻� </option>
	<option value="bkjukyo" <?=$selected[skey]['bkjukyo']?>> �Ա��ڸ� </option>
	<option value="bkinput" <?=$selected[skey]['bkinput']?>> �Աݿ����ݾ� </option>
	<option value="bkmemo4" <?=$selected[skey]['bkmemo4']?>> �ֹ���ȣ </option>
	</select> <input type="text" NAME="sword" value="<?=$_GET['sword']?>" class="line">
	</td>
	<td>�������<font class=small color=444444>/</font>�����</td>
	<td>
	<select name="gdstatus">
	<option value=""> ��ü </option>
	<option value="N" <?=$selected[gdstatus]['N']?>>Ȯ����</option>
	<option value="T" <?=$selected[gdstatus]['T']?>>��Ī����(by�ý���)</option>
	<option value="B" <?=$selected[gdstatus]['B']?>>��Ī����(by������)</option>
	<option value="F" <?=$selected[gdstatus]['F']?>>��Ī����(����ġ)</option>
	<option value="S" <?=$selected[gdstatus]['S']?>>��Ī����(��������)</option>
	<option value="A" <?=$selected[gdstatus]['A']?>>�������Ա�Ȯ�οϷ�</option>
	<option value="U" <?=$selected[gdstatus]['U']?>>�����ڹ�Ȯ��</option>
	</select>

	<select name="bkname">
	<option value="">������˻�</option>
	<? foreach ($r_bank as $v){ ?>
	<option value="<?=$v?>" <?=$selected[bkname][$v]?>><?=$v?>
	<? } ?>
	</select>
	</td>
</tr>
<tr>
	<td>�Ա���</td>
	<td colspan="3">
	<input type=text name=bkdate[] value="<?=$_GET[bkdate][0]?>" onclick="calendar(event)" class="cline"> ~
	<input type=text name=bkdate[] value="<?=$_GET[bkdate][1]?>" onclick="calendar(event)" class="cline">
	<a href="javascript:setDate('bkdate[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('bkdate[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('bkdate[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('bkdate[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('bkdate[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('bkdate[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
<tr>
	<td>���� ��Ī��</td>
	<td colspan="3">
	<input type=text name=gddate[] value="<?=$_GET[gddate][0]?>" onclick="calendar(event)" class="cline"> ~
	<input type=text name=gddate[] value="<?=$_GET[gddate][1]?>" onclick="calendar(event)" class="cline">
	<a href="javascript:setDate('gddate[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('gddate[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('gddate[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('gddate[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('gddate[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('gddate[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
</table>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td height=7 colspan=3></td></tr>
<tr>
<td width=50% align=right><A HREF="javascript:bankMatching();"><img src="../img/btn_man_banking.gif" alt="�ǽð��Ա�Ȯ�� �����ϱ�" border=0></a></td>
<td>&nbsp;&nbsp;&nbsp;</td>
<td width=50%><input type=image src="../img/btn_bank_search.gif" alt="�����Աݳ��� �ǽð���ȸ" class=null></td></tr></table>

<table width=100%>
<tr>
	<td class=pageInfo><font class=ver8>
	�� <b><span id="page_rtotal">0</span></b>��, �˻� <b><span id="page_recode">0</span></b>��, <b><span id="page_now">0</span></b> of <span id="page_total">0</span> Pages
	</td>
	<td align=right>
	<select name="sort" onchange="accountList();">
	<option value="bkdate desc" <?=$selected[sort]['bkdate desc']?>>- �Ա��� ���ġ�</option>
	<option value="bkdate asc" <?=$selected[sort]['bkdate asc']?>>- �Ա��� ���ġ�</option>
	<option value="gddatetime desc" <?=$selected[sort]['gddatetime desc']?>>- ������Ī�� ���ġ�</option>
	<option value="gddatetime asc" <?=$selected[sort]['gddatetime asc']?>>- ������Ī�� ���ġ�</option>
	</select>&nbsp;
	<select name=page_num onchange="accountList();">
	<?
	$r_pagenum = array(10,20,40,60,100);
	foreach ($r_pagenum as $v){
	?>
	<option value="<?=$v?>" <?=$selected[page_num][$v]?>><?=$v?>�� ���
	<? } ?>
	</select>
	</td>
</tr>
</table>
</form>

<div style="position:relative; height:150px;">
<div id="listcover" style="position:absolute; width:100%; height:100%; display:none"><!--Ŀ��--></div>
<form method="post" action="" name="fmList">
<table width=100% cellpadding=0 cellspacing=0 border=0 id="listing">
<col width="60"><col width="10%"><col width="13%"><col width="10%"><col width="12%"><col><col width="10%"><col width="10%"><col width="13%">
<tr><td class=rnd colspan=10></td></tr>
<tr class=rndbg>
	<th>��ȣ</th>
	<th>�ԱݿϷ���</th>
	<th>���¹�ȣ</th>
	<th>�����</th>
	<th>�Աݱݾ�</th>
	<th>�Ա��ڸ�</th>
	<th>�������</th>
	<th>���� ��Ī��</th>
	<th>�ֹ���ȣ</th>
</tr>
<tr><td class=rnd colspan=10></td></tr>
</table>

<table cellpadding=0 cellspacing=0 border=0 width=100%>
<tr><td height=5 colspan=2></td></tr>
<tr>
	<td align=center><font class=ver8><span id="page_navi"><!-- ����¡ ���--></span></font></td>
</tr>
</table>

<div class=button><a href="javascript:batchUpdate.begin();"><img src="../img/btn_editall.gif"></a></div>
<INPUT TYPE="hidden" style="width:300" NAME="nolist">
</form>
</div>
<script>document.getElementById('listcover').parentNode.style.height = ''; // ����ó�� : ������</script>

<div style="padding-top:15px;"></div>

<div id=MSG02>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">'�������'  �׸� ���� (��Ī���¸� �����ִ� �׸��Դϴ�)</td></tr>
<tr><td style="padding-left: 10">
<div>- ��Ī���� (����ġ) : �Ա������� �����ʾ� ��Ī���е� �ֹ����Դϴ�. �����ڴ� �ش� �ֹ����� ã�� ó���ؾ� �մϴ�.</div>
<div>- ��Ī���� (��������) : �Ա������� ������ �ֹ��� 2�� �̻��� �ִ� �ֹ����Դϴ�. �����ڴ� �ش� �ֹ����� ã�� ó���ؾ� �մϴ�.</div>
<div>- �������Ա�Ȯ�� : ��Ī���а��� ���� ��� �����ڴ� �ش� �ֹ����� ã�� ���� �Ա�Ȯ������ ó���� �� '�������Ա�Ȯ��' ���·� �����س�������.</div>
<div>- �����ڹ�Ȯ�� : ��Ī���а��� ���� ��� �����ڰ� �Ա��ڸ� ã�� ���ϰ� ��Ī���������� ���ܽ�Ű���� '�����ڹ�Ȯ��' ���·� �����س�������.</div>
<div>- ��Ī���� (by�ý���) : �ý���(�ڵ�ó��/�ǽð�ó��)�� ���� �Ա�Ȯ��ó���� �Ϸ�� �ֹ����Դϴ�.</div>
<div>- ��Ī���� (by������) : ��Ī������ �ֹ����� �����ڰ� �ֹ�����Ʈ���� �̹� �Ա�Ȯ�δܰ�� ó���� �ֹ����Դϴ�.</div>
</td></tr>


<tr><td height=3></td></tr>
</table>
</div>
<script>cssRound('MSG02')</script>

<div style="clear:both; padding-top:1px;"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>* �Ա���ȸ / �ǽð��Ա�Ȯ�� �޴��� '<b>�ֹ����� > �ڵ��Ա�Ȯ�� ���� ��û</b>' �޴��� ���� ���� ��û �� �̿��Ͻ� �� �ֽ��ϴ�. ���� ��û �� �̿��� ������¸� ����� �ּ���.</td></tr>
<tr><td height=3></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><b>�ڵ��Ա�Ȯ�� ����</b> : ���񽺰����Ϸκ��� 1�ð� �������� �Աݳ����� ��ȸ�Ͽ� �ڵ����� �Ա�Ȯ�� ó���մϴ�. (���񽺰����� ���� �ֹ��� ��Ī�ȵ�)
<ol type="a" style="margin:0px 0px 0px 40px;">
<li>�� ���θ� ������µ��� �Աݳ����� �ֹ������� �ڵ����� ���Ͽ� ���忡 �Աݵ� ������ �ڵ����� �Ա�Ȯ�� ó���ϴ� �����Դϴ�.</li>
<li>��(Matching) ���� : �⺻������ 7�ϰ��� �Աݳ����� 37�ϰ��� �ֹ������� ��ȸ�Ͽ� ��Ī �۾��մϴ�.</li>
<li>��(Matching) ���� : ����, ���¹�ȣ, �ݾ�, �Ա��ڸ����� ��Ī �۾��մϴ�.</li>
<li>�����ֹ��� ��� : ����, ���¹�ȣ, �ݾ�, �Ա��ڸ��� ������ �ֹ��� ��� '��������' ���� ó���Ǹ�, �ݵ�� ���۾����� �Ա�Ȯ�� ó���ؾ� �մϴ�.</li>
<li>��(Matching) �ֱ� : 1�ð� �������� �ڵ� ó���մϴ�.</li>
</ol>
</td></tr>
<tr><td height=3></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><b>�ǽð� �Ա�Ȯ�� ����</b> : �ڵ� ó���Ǵ� 1�ð� ���ݺ��� ������ �Ա�Ȯ��ó���� �ʿ��� ��� ��ڰ� ���� �������� �Ա�Ȯ�� ó���� ������ �� �ֽ��ϴ�
<ol type="a" style="margin:0px 0px 0px 40px;">
<li>�Աݳ����� �ֹ������� ��(Matching)�� �������� ���� ó���մϴ�.</li>
<li>��(Matching) ���� : �Ա��� �˻��׸� �Ⱓ�� �Աݳ����� +30�ϰ��� �ֹ������� ��ȸ�Ͽ� ��Ī �۾��մϴ�. (�⺻������ 7�ϰ��� �Աݳ���, �Ա����� �����Ͽ� ����ó���� ����)</li>
<li>��(Matching) ���� : �ڵ��Ա�Ȯ�� ���񽺿� ����</li>
</ol>
</td></tr>
<tr><td height=3></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><b>�����Աݳ��� �ǽð� ��ȸ</b> : �Ա����� �������� �Ա�Ȯ�ε� ������ ��ȸ�մϴ�. �ܼ��� ��ȸ�� �ϴ� ����Դϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>