<?

$location = "���ڼ��ݰ�꼭 ���� > ���ڼ��ݰ�꼭 �������";
$scriptLoad='<script src="../tax.sugi.js"></script>';
include "../_header.php";

### ��û�� default 7��
if ( $_GET[sbm_tm][0]=='' && $_GET[sbm_tm][1] == '' )
{
	$_GET[sbm_tm][0] = date("Ymd",strtotime("-7 day"));
	$_GET[sbm_tm][1] = date("Ymd");
}

### �����Ҵ�
if (!$_GET[page_num]) $_GET[page_num] = 10; # ������ ���ڵ��
$selected[page_num][$_GET[page_num]] = "selected";

$orderby = ($_GET[sort]) ? $_GET[sort] : "SBM_TM desc"; # ���� ����
$selected[sort][$orderby] = "selected";

$selected[skey][$_GET[skey]] = "selected";
$selected[tax_type][$_GET[tax_type]] = "selected";
$selected[bill_type][$_GET[bill_type]] = "selected";
$selected[status][$_GET[status]] = "selected";

?>

<form name=frmList onsubmit="return ( TLM.list() ? false : false );">
<div class="title title_top">���ڼ��ݰ�꼭 �������<span>���ڼ��ݰ�꼭�� ����� �ۼ��Ͽ� �����û�� �� �� �ֽ��ϴ�.</span></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL width=35%>
<tr>
	<td>Ű����˻�</td>
	<td>
	<select name="skey">
	<option value="all" <?=$selected[skey]['all']?>> ���հ˻� </option>
	<option value="DOC_NUMBER" <?=$selected[skey]['DOC_NUMBER']?>> ������ȣ</option>
	<option value="BUY_REGNUM" <?=$selected[skey]['BUY_REGNUM']?>> �����ü����ڹ�ȣ </option>
	<option value="BUY_COMPANY" <?=$selected[skey]['BUY_COMPANY']?>> �����üȸ��� </option>
	</select> <input type="text" NAME="sword" value="<?=$_GET['sword']?>" class=line>
	</td>
	<td>��������</td>
	<td>
	<select name="tax_type">
	<option value=""> ��ü </option>
	<option value="VAT" <?=$selected[tax_type]['VAT']?>>����(���ݰ�꼭)</option>
	<option value="FRE" <?=$selected[tax_type]['FRE']?>>�鼼(��꼭)</option>
	<option value="RCP" <?=$selected[tax_type]['RCP']?>>������</option>
	</select>
	</td>
</tr>
<tr>
	<td>û��������</td>
	<td>
	<select name="bill_type">
	<option value=""> ��ü </option>
	<option value="T01" <?=$selected[bill_type]['T01']?>>������</option>
	<option value="T02" <?=$selected[bill_type]['T02']?>>û����</option>
	</select>
	</td>
	<td>�������</td>
	<td>
	<select name="status">
	<option value=""> ��ü </option>
	<option value="RDY" <?=$selected[status]['RDY']?>>�����غ�</option>
	<option value="SND" <?=$selected[status]['SND']?>>����</option>
	<option value="RCV" <?=$selected[status]['RCV']?>>����</option>
	<option value="ACK" <?=$selected[status]['ACK']?>>����</option>
	<option value="CAN" <?=$selected[status]['CAN']?>>�ݷ�</option>
	<option value="CCR" <?=$selected[status]['CCR']?>>���</option>
	<option value="ERR" <?=$selected[status]['ERR']?>>����</option>
	<option value="DEL" <?=$selected[status]['DEL']?>>����</option>
	</select>
	</td>
</tr>
<tr>
	<td>��û��</td>
	<td colspan="3">
	<input type=text name=sbm_tm[] value="<?=$_GET[sbm_tm][0]?>" onclick="calendar(event)" class=cline> -
	<input type=text name=sbm_tm[] value="<?=$_GET[sbm_tm][1]?>" onclick="calendar(event)" class=cline>
	<a href="javascript:setDate('sbm_tm[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('sbm_tm[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('sbm_tm[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('sbm_tm[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('sbm_tm[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('sbm_tm[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
</table>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td height=15 colspan=3></td></tr>
<tr>
<td width=50% align=right><A HREF="javascript:popupLayer('../order/etaxsugi.register.php',700,650);"><img src="../img/btn_tax_hand_apply.gif" alt="���ڼ��ݰ�꼭 ����� �ۼ��ϱ�" border=0></a></td>
<td>&nbsp;&nbsp;&nbsp;</td>
<td width=50%><input type=image src="../img/btn_tax_hand_search.gif" alt="����� �ۼ��� ���ڼ��ݰ�꼭 ��ȸ" class=null></td></tr></table>

<table width=100%>
<tr>
	<td class=pageInfo><font class=ver8>
	�� <b><span id="page_rtotal">0</span></b>��, �˻� <b><span id="page_recode">0</span></b>��, <b><span id="page_now">0</span></b> of <span id="page_total">0</span> Pages
	</td>
	<td align=right>
	<select name="sort" onchange="TLM.list();">
	<option value="GEN_TM desc" <?=$selected[sort]['issuedate desc']?>>- ������ ���ġ�</option>
	<option value="GEN_TM asc" <?=$selected[sort]['issuedate asc']?>>- ������ ���ġ�</option>
    <optgroup label="------------"></optgroup>
	<option value="SBM_TM desc" <?=$selected[sort]['SBM_TM desc']?>>- ��û�� ���ġ�</option>
	<option value="SBM_TM asc" <?=$selected[sort]['SBM_TM asc']?>>- ��û�� ���ġ�</option>
	</select>&nbsp;
	</td>
</tr>
</table>
</form>

<div style="position:relative; height:0;">
<div id="listcover" style="position:absolute; width:100%; height:100%; display:none"><!--Ŀ��--></div>
<form method="post" action="" name="fmList">
<table width=100% cellspacing=0 cellpadding=0 border=1 bordercolor="#D9D9D9" style="border-collapse: collapse; word-break:break-all;" id="listing">
<col width=35><col><col width=15%><col width=10%><col width=75><col width=75><col width=75><col width=86>
<tr class=rndbg>
	<th rowspan=2>��ȣ</th>
	<th colspan=3>���������</th>
	<th>������</th>
	<th>������ȣ</th>
	<th>�ĺ���ȣ</th>
	<th rowspan=2>�������<br>�μ��ϱ�</th>
</tr>
<tr class=rndbg>
	<th>��ǰ��</th>
	<th>����ݾ�</th>
	<th>��������</th>
	<th>û��������</th>
	<th>��û��</th>
	<th>����/�ݷ���</th>
</tr>
</table>

<table cellpadding=0 cellspacing=0 border=0 width=100%>
<tr><td height=5 colspan=2></td></tr>
<tr>
	<td align=center><font class=ver8><span id="page_navi"><!-- ����¡ ���--></span></font></td>
</tr>
</table>

</form>
</div>
<script>if ( !document.all ) document.getElementById('listcover').parentNode.style.height = ''; // ����ó�� : ������</script>

<div style="padding-top:15px;"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>
<dl style="margin:0;">
<dt style="padding-bottom:3"><img src="../img/icon_list.gif" align="absmiddle">������� ����</dt>
<dd style="margin-left:8px;">
	<ol style="list-style-type:none; margin:0; padding:0;">
	<li style="padding-bottom:3">�� �����غ� : ���ݰ�꼭 ������ �غ����Դϴ�.</li>
	<li style="padding-bottom:3">�� ���� : ������������ ������� �������� ���ڼ����� �� ������ ���� ���·� ����(����)�Ǿ����ϴ�.</li>
	<li style="padding-bottom:3">�� ���� : �����ڰ� ���ݰ�꼭 ������ Ȯ���Ͽ����ϴ�.</li>
	<li style="padding-bottom:3">�� ���� : ���޹޴��ڰ� ���ݰ�꼭 ������ �����Ͽ����ϴ�.</li>
	<li style="padding-bottom:3">�� �ݷ� : ���޹޴��ڰ� ���ݰ�꼭 ������ �ݷ��Ͽ����ϴ�.</li>
	<li style="padding-bottom:3">�� ��� : ����, ���� �Ǵ� ���� ���¿��� ������ ����Ͽ����ϴ�.</li>
	<li style="padding-bottom:6">�� ���� : �����غ� �� �������� ��ȯ�߿� ������ �����Ͽ����ϴ�.</li>
	</ol>
</dd>
</dl>
</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�����ڿ� ���ݰ�꼭�� Ȯ���Ͻ÷��� �������� �޴��� �ִ� ���ڼ��ݰ�꼭 �Ŵ���'�� �����ϼż� Ȯ���ϼž� �մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ڼ��ݰ�꼭 ��������û
<ol type="a" style="margin:0px 0px 0px 40px;">
<li>���ڼ��ݰ�꼭�� ����� �ۼ��մϴ�.</li>
<li>����Ʈ�� �־�߸� �����û�� �����ϸ� ������ 1point �����˴ϴ�.</li>
<li>���޹޴����� ���ݰ�꼭�� �����ۼ��ÿ� �Է��� �̸��ϰ� �޴������� ���� �߼۵Ǹ� �ȳ��Ǿ����ϴ�.</li>
<li>[����] ��������� ���� �������� �ԷµǴ� �ۼ����ڰ� �����ۼ����� �������� <b>30�� �̳���߸�</b> ����Ǿ����ϴ�.</li>
</ol>
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>