<?

$location = "���ݰ�꼭���� > ���񽺰��� �� ����Ʈ����";
include "../_header.php";

$checked[idx][0] = "checked";

### TAX ���ݵ���Ÿ ��������
$out = readurl("http://www.godo.co.kr/userinterface/_godoConn/conf/tax.cfg");
$div = explode(chr(10),$out);
foreach ($div as $v){
	$div2 = explode("|",$v);
	$tax_price[$div2[0]] = $div2[1];
}
?>
<script src="../tax.ajax.js"></script>

<div class="title title_top"><font  face=���� color=black><b>���񽺰��� �� ����Ʈ����<span>���ڼ��ݰ�꼭 ����� ���� ����Ʈ�� �����մϴ�</span></div>

<table border=1 bordercolor=cccccc style="border-collapse:collapse" cellpadding=4 cellspacing=0>
<tr><td>
<table border=3 bordercolor=#cccccc style="border-collapse:collapse">
	<tr>
		<td width=762 height=50 align=center bgcolor=ADFFFE>�ܿ�����Ʈ : ���� <font face=���� size=5 color=#04062F><b><u><?=number_format($godo[tax])?></u></b></font></span> Point</td>
	</tr>
</table>
</td></tr></table>

<div style="padding-top:5px"></div>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ڼ��ݰ�꼭 ���༭�񽺴� ����Ʈ���������� ����Ʈ�� �־�߸� ������ �����մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">����Ʈ�� �����Ͻ÷��� ���ڼ��ݰ�꼭�� �����ϴ� LG������ ���ý�21�� ���� �����ϼž� �մϴ�. <a href="./etax.request.php"><img src="../img/btn_taxservice_apply_s.gif" align=absmiddle></a></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�����ݾ��� ����Ǽ��� ���� �Ǵ� ���� 200���Դϴ�. (�Ʒ� �����ݾ��� �ΰ��������Դϴ�)</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">������ ����Ʈ�� ȯ�ҵ��� �ʽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">����� ���ڼ��ݰ�꼭 �Ǵ� 1����Ʈ�� �����˴ϴ�. (���������� ���������� ���� ����� ���ڼ��ݰ�꼭�� �ݷ��� ��쿡�� ����Ǽ��� ���Ե˴ϴ�)</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">(��)�ö��������� (��)LG������ ��簡 ���� ���ݰ�꼭 ���񽺸� �����մϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<div style="padding-top:5px"></div>



<form name=frmTax method=post onsubmit="return TPR.popupPay(this);">
<input type=hidden name=sno value="<?=$godo[sno]?>">
<input type=hidden name=mode value="tax">

<table width=780 border=1 bordercolor="#cccccc" style="border-collapse:collapse" cellpadding=0 cellspacing=0>
<tr bgcolor=#f7f7f7 height=27 align=center>
	<th width=100>��������</th>
	<th>���� �Ǽ�/����Ʈ</th>
	<th>�����ݾ�</th>
	<th>�ܰ�</th>
</tr>
<? $idx=0; foreach ($tax_price as $k=>$v){ $v = $v * 10 / 11; ?>
<tr height=25 align=center>
	<td class=noline><input type=radio name=idx value="<?=$idx?>" <?=$checked[idx][$idx++]?>>
	<td><font class=ver8><b><?=number_format($k)?></b> ��/����Ʈ</td>
	<td><font class=ver8><b><?=number_format($v)?></b>�� <font color=6d6d6d>(�ΰ�������)</font></td>
	<td><font class=ver8><?=$v/$k?>��/1�� <font color=6d6d6d>(�ΰ�������)</font></td>
</tr>
<? } ?>
</table>

<div style="margin-top:5px; color:#5A5A5A;">&#149; <font class="small1">����Ʈ�� ������ ������ �� �� �ֽ��ϴ�</font> <a href="javascript:popupLayer('http://www.godo.co.kr/userinterface/_godoConn/Mytaxlog.php?sno=<?=$godo['sno']?>',700,350)"><img src="../img/btn_sattlelog.gif" align="absmiddle"></a></div>
<div style="padding-top:10px"></div>

<div class="button" id="avoidSubmit" style="width:780px;">
<input type="image" src="../img/btn_taxpoint_pay.gif">
<a href="./etax.request.php"><img src="../img/btn_taxservice_apply.gif"></a>
</div>
</form>


<? include "../_footer.php"; ?>