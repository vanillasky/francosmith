<?

$location = "������ũ ���½�Ÿ�� ���� > ����Ȯ������";
include "../_header.php";
include "../../lib/page.class.php";

list ($total) = $db->fetch("select count(*) from ".GD_ORDER_ITEM." where inpk_compdt>0");

### �����Ҵ�
if (!$_GET['page_num']) $_GET['page_num'] = 20; # ������ ���ڵ��
$selected['page_num'][$_GET['page_num']] = "selected";

$orderby = ($_GET['sort']) ? $_GET['sort'] : "inpk_compdt desc"; # ���� ����
$selected['sort'][$orderby] = "selected";

$selected['skey'][$_GET['skey']] = "selected";

### ���
$db_table = GD_ORDER_ITEM;

$where[] = "inpk_compdt>0";
if ($_GET['sword']){
	$_GET['sword'] = trim($_GET['sword']);
	$t_skey = ($_GET['skey']=="all") ? "concat(goodsnm, brandnm, maker, goodsno)" : $_GET['skey'];
	$where[] = "$t_skey like '%{$_GET['sword']}%'";
}
if ($_GET['regdt'][0]){
	if (!$_GET['regdt'][1]) $_GET['regdt'][1] = date("Ymd");
	$where[] = "inpk_compdt between date_format({$_GET['regdt'][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET['regdt'][1]},'%Y-%m-%d 23:59:59')";
}

$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "*";
$pg->setQuery($db_table,$where,$orderby);
$pg->exec();

$res = $db->query($pg->query);

?>

<div class="title title_top">����Ȯ������<span>������ũ�κ��� ����Ȯ���� �ֹ���ǰ �����Դϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=26')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<div style="padding:8px 13px;background:#f7f7f7;border:3px solid #C6C6C6;margin-bottom:18px;" id="goodsInfoBox">
<div><font color="#EA0095"><b>�ʵ�!</b></font></div>
<div style="padding-top:2">�����ڰ� <font color=EA0095>����Ȯ���� �ֹ��� ������ũ ���꿡 ����</font>�Ǹ�, ������� �� 14�� ���� �ڵ�����Ȯ���˴ϴ�.</div>
<div style="padding-top:2">�� ������ ������ũ�� <font color=0074BA>������ �� ���� �ڷ�θ� ���</font>�ϼ���.</div>
</div>


<!-- �˻����� : start -->
<form name=frmList onsubmit="return chkForm(this)">

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td><font class=small1>�˻� (����)</td>
	<td>
	<select name=skey>
	<option value="all"> = ���հ˻� =
	<option value="goodsnm" <?=$selected['skey']['goodsnm']?>> ��ǰ��
	<option value="brandnm" <?=$selected['skey']['brandnm']?>> �귣��
	<option value="maker" <?=$selected['skey']['maker']?>> ������
	<option value="goodsno" <?=$selected['skey']['goodsno']?>>������ȣ
	</select>
	<input type=text name=sword value="<?=$_GET['sword']?>">
	</td>
</tr>
<tr>
	<td><font class=small1>����Ȯ����</td>
	<td colspan=3>
	<input type=text name=regdt[] value="<?=$_GET['regdt'][0]?>" onclick="calendar()" size=12> -
	<input type=text name=regdt[] value="<?=$_GET['regdt'][1]?>" onclick="calendar()" size=12>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
</table>
<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

<table width=100%>
<tr>
	<td class=pageInfo><font class=ver8>
	�� <b><?=number_format($total)?></b>��, �˻� <b><?=number_format($pg->recode[total])?></b>��, <b><?=number_format($pg->page[now])?></b> of <?=number_format($pg->page[total])?> Pages
	</td>
	<td align=right>
	<select name="sort" onchange="this.form.submit();">
	<option value="inpk_compdt desc" <?=$selected[sort]['inpk_compdt desc']?>>- ����Ȯ���� ���ġ�</option>
	<option value="inpk_compdt asc" <?=$selected[sort]['inpk_compdt asc']?>>- ����Ȯ���� ���ġ�</option>
    <optgroup label="------------"></optgroup>
	<option value="ordno desc" <?=$selected[sort]['ordno desc']?>>- �ֹ���ȣ ���ġ�</option>
	<option value="ordno asc" <?=$selected[sort]['ordno asc']?>>- �ֹ���ȣ ���ġ�</option>
	</select>&nbsp;
	<select name=page_num onchange="this.form.submit()">
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
<!-- �˻����� : end -->


<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th><font class=small1><b>��ȣ</th>
	<th><font class=small1><b>�ֹ���ȣ</th>
	<th><font class=small1><b>��ǰ��</th>
	<th><font class=small1><b>����</th>
	<th><font class=small1><b>��ǰ����</th>
	<th><font class=small1><b>�Ұ�</th>
	<th><font class=small1><b>���԰�</th>
	<th><font class=small1><b>����Ȯ����</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<col width=35><col width=120><col><col width=60><col width=60 span=3><col width=80>
<?
while (is_resource($res) && $data=$db->fetch($res))
{
	$goodsnm = $data['goodsnm'];
	if ($data['opt1']) $goodsnm .= "[{$data['opt1']}" . ($data['opt2'] ? "/{$data['opt2']}" : "") . "]";
	if ($data['addopt']) $goodsnm .= "<div>[" . str_replace("^","] [",$data[addopt]) . "]</div>";
?>
<tr><td height=4 colspan=12></td></tr>
<tr height=18>
	<td align=center><font class="ver8" color="#616161"><?=$pg->idx--?></font></td>
	<td>
	<a href="../order/view.php?ordno=<?=$data['ordno']?>"><font class=ver81 color=0074BA><b><?=$data['ordno']?></b></font></a>
	<a href="javascript:popup('../order/popup.order.php?ordno=<?=$data['ordno']?>',800,600)"><img src="../img/btn_newwindow.gif" border=0 align=absmiddle></a>
	</td>
	<td>
	<font class=small><?=$goodsnm?></font>
	<div style="padding-top:3"><font class=small1 color=6d6d6d>������ : <?=$data[maker] ? $data[maker] : '����'?></div>
	<div><font class=small1 color=6d6d6d>�귣�� : <?=$data[brandnm] ? $data[brandnm] : '����'?></div>
	</td>
	<td align=center><?=number_format($data[ea])?></td>
	<td align=center><?=number_format($data[price])?></td>
	<td align=center><?=number_format($data[price]*$data[ea])?></td>
	<td align=center><?=number_format($data[supply])?></td>
	<td align=center><font class="small" color="#444444"><?=substr($data['inpk_compdt'],2,8)?></font></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>


<? include "../_footer.php"; ?>