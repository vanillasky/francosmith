<?

$location = "���¸��� ���̷�Ʈ ���� > �ǸŻ�ǰ �����ϱ�";
$scriptLoad='<link rel="styleSheet" href="./js/style.css">';
$scriptLoad.='<script src="./js/common.js"></script>';
include "../_header.php";
include "../../lib/page.class.php";

### ���� ����
$_GET['sword'] = trim($_GET['sword']);

list ($total) = $db->fetch("select count(*) from ".GD_OPENMARKET_GOODS);

if (!$_GET['page_num']) $_GET['page_num'] = 10;
$selected['page_num'][$_GET['page_num']] = "selected";
$selected['skey'][$_GET['skey']] = "selected";
$checked['open'][$_GET['open']] = "checked";

$orderby = ($_GET['sort']) ? $_GET['sort'] : "regdt desc";
$div = explode(" ",$orderby);
$flag['sort'][$div[0]] = (!preg_match("/desc$/i",$orderby)) ? "��" : "��";

if ($_GET['cate']){
	$category = array_notnull($_GET['cate']);
	$category = implode("|", $category);
}

$db_table = GD_OPENMARKET_GOODS." a";

if ($category) $where[] = "category like '$category%'";
if ($_GET['sword']) $where[] = "{$_GET['skey']} like '%{$_GET['sword']}%'";
if ($_GET['open']) $where[] = "open=".substr($_GET['open'],-1);

$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->field = "
a.goodsno,a.goodsnm,a.regdt,a.goodscd,a.origin_kind,a.origin_name,a.maker,a.brandnm,a.shortdesc,a.price,a.category,a.runout,a.usestock
";
$pg->setQuery($db_table,$where,$orderby);
$pg->exec();

$res = $db->query($pg->query);

?>

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

function sort(sort)
{
	var fm = document.frmList;
	fm.sort.value = sort;
	fm.submit();
}
function sort_chk(sort)
{
	if (!sort) return;
	sort = sort.replace(" ","_");
	var obj = document.getElementsByName('sort_'+sort);
	if (obj.length){
		div = obj[0].src.split('list_');
		for (i=0;i<obj.length;i++){
			chg = (div[1]=="up_off.gif") ? "up_on.gif" : "down_on.gif";
			obj[i].src = div[0] + "list_" + chg;
		}
	}
}

window.onload = function(){ sort_chk('<?=$_GET[sort]?>'); }
</script>

<div class="title title_top">�ǸŻ�ǰ �����ϱ� <span>���¸��� �ǸŰ����� ��ϵ� ��ǰ�� �� �� �ְ�, ������ �� ���� �� �� �ֽ��ϴ�.</span></div>
<div id="useMsg"><script>callUseable('useMsg');</script></div>

<form name="frmList">
<input type="hidden" name="sort" value="<?=$_GET['sort']?>">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>���¸��� ǥ�غз�</td>
	<td>
	<div id="cateSrchForm">
		<span id="cat_div1"></span>
		<span id="cat_div2"></span>
		<span id="cat_div3"></span>
		<span id="cat_div4"></span>
	</div>
	<script>callStepCate('<?=$category?>');</script>
	</td>
</tr>
<tr>
	<td>�˻���</td>
	<td>
	<select name="skey">
	<option value="goodsnm" <?=$selected[skey][goodsnm]?>>��ǰ��
	<option value="goodsno" <?=$selected[skey][goodsno]?>>������ȣ
	<option value="goodscd" <?=$selected[skey][goodscd]?>>�𵨸�
	</select>
	<input type="text" name="sword" class="lline" value="<?=$_GET[sword]?>">
	</td>
</tr>
</table>
<div class="button_top"><input type="image" src="../img/btn_search2.gif"></div>


<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td class="pageInfo"><font class="ver8">�� <b><?=$total?></b>��, �˻� <b><?=$pg->recode['total']?></b>��, <b><?=$pg->page['now']?></b> of <?=$pg->page['total']?> Pages</td>
	<td align="right">

	<table cellpadding="0" cellspacing="0" border="0" width="500">
	<tr>
		<td valign="bottom"><img src="../img/sname_date.gif"><a href="javascript:sort('regdt')"><img name="sort_regdt" src="../img/list_up_off.gif"></a><a href="javascript:sort('regdt desc')"><img name="sort_regdt_desc" src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_product.gif"><a href="javascript:sort('goodsnm')"><img name="sort_goodsnm" src="../img/list_up_off.gif"></a><a href="javascript:sort('goodsnm desc')"><img name="sort_goodsnm_desc" src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_price.gif"><a href="javascript:sort('price')"><img name="sort_price" src="../img/list_up_off.gif"></a><a href="javascript:sort('price desc')"><img name="sort_price_desc" src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_brand.gif"><a href="javascript:sort('brandnm')"><img name="sort_brandnm" src="../img/list_up_off.gif"></a><a href="javascript:sort('brandnm desc')"><img name="sort_brandnm_desc" src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_company.gif"><a href="javascript:sort('maker')"><img name="sort_maker" src="../img/list_up_off.gif"></a><a href="javascript:sort('maker desc')"><img name="sort_maker_desc" src="../img/list_down_off.gif"></a></td>
		<td style="padding-left:20px">
		<img src="../img/sname_output.gif" align="absmiddle">
		<select name="page_num" onchange="this.form.submit()">
		<? foreach (array(10,20,40,60,100) as $v){ echo "<option value='{$v}' {$selected['page_num'][$v]}>{$v}�� ���"; } ?>
		</select>
		</td>
	</tr>
	</table>

	</td>
</tr>
</table>
</form>


<form name="form">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<col width="50" align="center"><col><col width="370"><col width="40"><col width="40">
<tr><td class="rnd" colspan="12"></td></tr>
<tr class="rndbg">
	<th><input type="checkbox" onclick="chkBoxAll(document.getElementsByName('chk[]'),'rev')" class="null"></th>
	<th>��ǰ��</th>
	<th>�Ӽ�</th>
	<th>����</th>
	<th>����</th>
</tr>
<tr><td class="rnd" colspan="12"></td></tr>
<?
while ($data=$db->fetch($res))
{
	$catnmid = "catnm". $pg->idx;
	$data['origin'] = ($data['origin_kind'] == '1' ? '����' : $data['origin_name']);
	list($optCnt, $stock) = $db->fetch("select count(*),sum(stock) from ".GD_OPENMARKET_GOODS_OPTION." where goodsno='{$data['goodsno']}'");

	if ($data['runout'] == 1) $stock = 'ǰ��';
	else if ($data['usestock'] != 'o') $stock = '�������Ǹ�';
	else if ($optCnt > 1) $stock = '�ɼǻ�ǰ';
	if (is_numeric($stock) === true) $able = ' style="width:100%"';
	else $able = 'readonly style="width:100%; background:#eeeeee;" title="'. $stock .'�� ���⼭ ������ �� �����ϴ�."';

	$data = array_map("htmlspecialchars",$data);
?>
<tr><td height="4" colspan="12"></td></tr>
<tr height="25" bgcolor="#ffffff" bg="#ffffff">
	<td class="noline"><input type="checkbox" name="chk[]" value="<?=$data['goodsno']?>" subject="<?=strip_tags($data['goodsnm'])?>" onclick="iciSelect(this)"><br><font class="ver8" color="#616161"><?=$pg->idx--?></font></td>
	<td valign="top">
	<div><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data['goodsno']?>',825,600)"><font class="small1" color="#616161">��ǰ��ȣ : <?=$data['goodsno']?></font></a></div>
	<input type="text" name="goodsnm" value="<?=$data['goodsnm']?>" style="width:100%">

	<input type="hidden" name="category" value="<?=$data['category']?>" id="<?=$catnmid?>">
	<div style="margin-top:5px; letter-spacing:-1px;" id="<?=$catnmid?>_text" class=small1>
	<script>callCateNm('<?=$data['category']?>','<?=$catnmid?>','link');</script>
	</div>
	</td>
	<td align="center" valign="top">
	<table cellpadding="2" cellspacing="0" border="1" bordercolor="#dedede" style="border-collapse:collapse">
	<col width="45"><col width="65"><col width="40"><col width="65"><col width="40"><col width="65">
	<tr bgcolor="#E1F4D2">
		<th><font class="small1" color="#1D8E0D">������</th>
		<td><input type="text" name="origin" value="<?=$data['origin']?>" style="width:100%"></td>
		<th><font class="small1" color="#1D8E0D">�ǸŰ�</th>
		<td><input type="text" name="price" value="<?=$data['price']?>" style="width:100%"></td>
		<th><font class="small1" color="#1D8E0D">���</th>
		<td><input type="text" name="stock" value="<?=$stock?>" <?=$able?>></td>
	</tr>
	<tr bgcolor="#FFEFDF">
		<th><font class="small1" color="#F07800">������</th>
		<td><input type="text" name="maker" value="<?=$data['maker']?>" style="width:100%"></td>
		<th><font class="small1" color="#F07800">�귣��</th>
		<td><input type="text" name="brandnm" value="<?=$data['brandnm']?>" style="width:100%"></td>
		<th><font class="small1" color="#F07800">�𵨸�</th>
		<td><input type="text" name="goodscd" value="<?=$data['goodscd']?>" style="width:100%"></td>
	</tr>
	<tr>
		<th><font class="small1" color="#444444">ȫ������</th>
		<td colspan="5"><input type="text" name="shortdesc" value="<?=$data['shortdesc']?>" style="width:100%" maxlength="25"></td>
	</tr>
	</table>
	</td>
	<td align="center"><a href="javascript:popup('../openmarket/popup.register.php?goodsno=<?=$data['goodsno']?>',825,700)"><img src="../img/i_edit.gif" alt="����"></a></td>
	<td align="center"><a href="../openmarket/indb.php?mode=delGoods&goodsno=<?=$data['goodsno']?>" onclick="return confirm('������ �����Ͻðڽ��ϱ�?')"><img src="../img/i_del.gif" alt="����"></a></td>
</tr>
<tr><td height="4"></td></tr>
<tr><td colspan="12" class="rndline"></td></tr>
<? } ?>
</table>

<div align="center" class="pageNavi"><font class="ver8"><?=$pg->page[navi]?></font></div>

<div class="button"><a href="javascript:callQuickModify();"><img src="../img/btn_openmarket_reregister.gif" alt="�����ϱ�"></a></div>

</form>

<? include "../_footer.php"; ?>