<?
// deprecated. redirect to new page;
header('location: ./adm_goods_manage_option.php?'.$_SERVER['QUERY_STRING']);
exit;
$location = "��ǰ�ϰ����� > ����/������/������";
include "../_header.php";
include "../../lib/page.class.php";
@include_once "../../conf/config.purchase.php";

### ���� ����
$_GET[sword] = trim($_GET[sword]);

list ($total) = $db->fetch("select count(a.goodsno) from ".GD_GOODS." as a left join ".GD_GOODS_OPTION." as b on a.goodsno = b.goodsno WHERE a.todaygoods='n'");

if (!$_GET[page_num]) $_GET[page_num] = 10;
$selected[page_num][$_GET[page_num]] = "selected";
$selected[skey][$_GET[skey]] = "selected";
$selected[brandno][$_GET[brandno]] = "selected";
$selected[sort][$_GET[sort]] = "selected";
$checked[open][$_GET[open]] = "checked";

if ($_GET[cate]){
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];
}

$db_table = "
".GD_GOODS." a
left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno
";

$where[] = "a.todaygoods='n'";
if ($category){
	$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";
	$where[] = "category like '$category%'";
}
if ($_GET[sword]) $where[] = "$_GET[skey] like '%$_GET[sword]%'";
if ($_GET[price][0] && $_GET[price][1]) $where[] = "price between {$_GET[price][0]} and {$_GET[price][1]}";
if ($_GET[brandno]) $where[] = "brandno='$_GET[brandno]'";
if ($_GET[regdt][0] && $_GET[regdt][1]) $where[] = "regdt between date_format({$_GET[regdt][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET[regdt][1]},'%Y-%m-%d 23:59:59')";
if ($_GET[open]) $where[] = "open=".substr($_GET[open],-1);

$orderby = ($_GET[sort]) ? $_GET[sort] : "a.goodsno desc";

$pg = new Page($_GET[page], $_GET[page_num]);
$pg->field = "a.*,b.*";
$pg->setQuery($db_table,$where,$orderby);
$pg->exec();

$res = $db->query($pg->query);
?>

<script>
function fnBatchInput(name) {

	var value = $('el-batch-'+name).value.trim().replace(/[^0-9]/g,'');

	var input;

	$$('input[name="chk[]"]:checked').each(function(el){
		input = $$('input[name="'+name+'['+ el.value+']"]')[0];

		if (! input.disabled) {
			input.value = value;
		}

	});


	return false;

}
</script>

<form name=frmList>

<div class=title style="margin-top:0">�ɼ� �ϰ�����(����/������/������) <span>��ϵ� ��ǰ��  �ɼǺ� ����, ������, ��� �ϰ� ����/���� �Ͻ� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=4')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<? if($purchaseSet['usePurchase'] == "Y") { ?><div style="margin-bottom:7px; font-weight:bold; color:#EA0095;">����ó ���� ������ ��ǰ ����� <a href="../goods/purchase_goods.php"><font color="#ea0095">[�԰� ��ǰ ���]</font></a> ���� ����, �����Ͻ� �� �ֽ��ϴ�.</div><? } ?>
<table class=tb>
<col class=cellC><col class=cellL style="width:250px">
<col class=cellC><col class=cellL>
<tr>
	<td>�з�����</td>
	<td colspan="3">
	<script src="../../lib/js/categoryBox.js"></script>
	<script>new categoryBox('cate[]',4,'<?=$category?>');</script>
	</td>
</tr>
<tr>
	<td>�˻���</td>
	<td>
	<select name=skey>
		<option value="goodsnm" <?=$selected[skey][goodsnm]?>>��ǰ��
		<option value="a.goodsno" <?=$selected[skey][goodsno]?>>������ȣ
		<option value="goodscd" <?=$selected[skey][goodscd]?>>��ǰ�ڵ�
		<option value="keyword" <?=$selected[skey][keyword]?>>����˻���
	</select>
	<input type=text name="sword" value="<?=$_GET[sword]?>" class="line" style="height:22px">
	</td>
	<td>�귣��</td>
	<td>
	<select name=brandno>
	<option value="">-- �귣�� ���� --
	<?
	$bRes = $db->query("select * from gd_goods_brand order by sort");
	while ($tmp=$db->fetch($bRes)) {
	?>
	<option value="<?=$tmp[sno]?>" <?=$selected[brandno][$tmp[sno]]?>><?=$tmp[brandnm]?>
	<? } ?>
	</select>
	</td>
</tr>
<tr>
	<td>��ǰ����</td>
	<td><font class=small color=444444>
	<input type=text name=price[] value="<?=$_GET[price][0]?>" onkeydown="onlynumber()" size="15" class="rline"> �� -
	<input type=text name=price[] value="<?=$_GET[price][1]?>" onkeydown="onlynumber()" size="15" class="rline"> ��
	</td>
	<td>��ǰ��¿���</td>
	<td class=noline>
	<input type=radio name=open value="" <?=$checked[open]['']?>>��ü
	<input type=radio name=open value="11" <?=$checked[open][11]?>>��»�ǰ
	<input type=radio name=open value="10" <?=$checked[open][10]?>>����»�ǰ
	</td>
</tr>
<tr>
	<td>��ǰ�����</td>
	<td colspan=3>
	<input type=text name=regdt[] value="<?=$_GET[regdt][0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline"> -
	<input type=text name=regdt[] value="<?=$_GET[regdt][1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline">
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

<div style="padding-top:15px"></div>

<table width=100% cellpadding=0 cellspacing=0>
<tr>
	<td class=pageInfo><font class=ver8>
	�� <b><?=$total?></b>��, �˻� <b><?=$pg->recode[total]?></b>��, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages
	</td>
	<td align=right>

	<table cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td align=right>
		<select name="sort" onchange="document.frmList.submit();">
			<option value="a.regdt desc" <?=$selected[sort]['a.regdt desc']?>>����ϼ�</option>
			<option value="b.stock asc" <?=$selected[sort]['b.stock asc']?>>�ܿ�����</option>
		</select>

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

	</td>
</tr>
</table>
</form>

<form method=post action="indb.php" target="ifrmHidden">
<input type=hidden name=mode value="stock">

<table width=100% cellpadding=2 cellspacing=0 border=0>
<col width=60>
<col width=45>
<col width=40>
<col width=10>
<col>
<col width=80>
<col width=80>
<col width=80>
<col width=80>
<col width=80>
<col width=70>
<col width=40>

<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg align=center>
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white>��ü����</a></th>
	<th>��ȣ</th>
	<th></th>
	<th></th>
	<th>��ǰ��</th>
	<th>�ɼ�1</th>
	<th>�ɼ�2</th>
	<th>����</th>
	<th>�ǸŰ�</th>
	<th>���԰�</th>
	<th>������</th>
	<th>���</th>
</tr>
<?
while ($data=$db->fetch($res)){

	if ($_GET[sort]=="stock") $data[link] = 1;

	//$disabled = ($pre[goodsno]==$data[goodsno] && $pre[opt1]==$data[opt1]) ? "disabled" : "";
	$disabledStock = ($purchaseSet['usePurchase'] == "Y") ? "disabled" : "";
	$pre[opt1] = $data[opt1];
	$pre[goodsno] = $data[goodsno];
?>
<tr>
	<td align=center class="noline"><input type=checkbox name=chk[] value="<?=$data[sno]?>"></td>
	<td align=center><font class=ver71 color=616161><?=$pg->idx--?></font></td>
	<td style="border:1px #e9e9e9 solid;"><a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],40,'',1)?></a></td>
	<td></td>
	<td><a href="javascript:popup('popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',825,600)"><font  color=0074BA><?=$data[goodsnm]?></a></td>

	<td align=center><font class=small color=555555><?=$data[opt1]?></td>
	<td align=center><font class=small color=555555><?=$data[opt2]?></td>
	<td><input type=text name=consumer[<?=$data[sno]?>] value="<?=$data[consumer]?>" style="text-align:right;width:80px" <?=$disabled?> class=rline></td>
	<td><input type=text name=price[<?=$data[sno]?>] value="<?=$data[price]?>" style="text-align:right;width:80px" <?=$disabled?> class=rline></td>
	<td><input type=text name=supply[<?=$data[sno]?>] value="<?=$data[supply]?>" style="text-align:right;width:80px" <?=$disabled?> class=rline></td>
	<td><input type=text name=reserve[<?=$data[sno]?>] value="<?=$data[reserve]?>" style="text-align:right;width:70px" <?=$disabled?> class=rline></td>
	<td><input type=text name=stock[<?=$data[sno]?>] value="<?=$data[stock]?>" style="text-align:right;width:40px" <?=$disabledStock?> class=rline></td>
</tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>
<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<div style="margin-top:20px;">
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td width="200">������ �ɼ� �ϰ� ����/����</td>
		<td>
		<table>
		<tr><td>����</td><td><input type="text" id="el-batch-consumer" value=""> <a href="javascript:void(0);" onClick="fnBatchInput('consumer');"><img src="../img/i_edit.gif" align="absmiddle"></a></td></tr>
		<tr><td>�ǸŰ�</td><td><input type="text" id="el-batch-price" value=""> <a href="javascript:void(0);" onClick="fnBatchInput('price');"><img src="../img/i_edit.gif" align="absmiddle"></a></td></tr>
		<tr><td>���԰�</td><td><input type="text" id="el-batch-supply" value=""> <a href="javascript:void(0);" onClick="fnBatchInput('supply');"><img src="../img/i_edit.gif" align="absmiddle"></a></td></tr>
		<tr><td>������</td><td><input type="text" id="el-batch-reserve" value=""> <a href="javascript:void(0);" onClick="fnBatchInput('reserve');"><img src="../img/i_edit.gif" align="absmiddle"></a></td></tr>
		<tr><td>���</td>
			<td>
			<input type="text" id="el-batch-stock" <?=($purchaseSet['usePurchase'] == "Y") ? 'disabled' : '' ?> value=""> <a href="javascript:void(0);" onClick="fnBatchInput('stock');"><img src="../img/i_edit.gif" align="absmiddle"></a>
			<? if($purchaseSet['usePurchase'] == "Y") { ?><div class="extext" style="margin-top:3px;">����ó ���� �������Դϴ�. ����� <a href="../goods/purchase_goods.php"><font class="extext_l">[�԰� ��ǰ ���]</font></a> ���� ������ �ּ���.</div><? } ?>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
</div>

<div class=button>
<input type=image src="../img/btn_save.gif">
</div>

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�� ��ǰ�� �ɼǺ� ���� �� ��� �����Ͻ÷��� �ش� �Է¹ڽ��� ���� ���� �� �ϰ�����/���� �� [����]��ư�� ���� �ּ���.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�� ��ǰ���� Ŭ���ϸ� ��ǰ������ �����Ͻ� �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>
