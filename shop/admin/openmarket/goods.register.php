<?

$location = "���¸��� ���̷�Ʈ ���� > �ǸŻ�ǰ ����ϱ�";
$scriptLoad='<link rel="styleSheet" href="./js/style.css">';
$scriptLoad.='<script src="./js/common.js"></script>';
include "../_header.php";
include "../../lib/page.class.php";

### ���� ����
$_GET['sword'] = trim($_GET['sword']);

list ($total) = $db->fetch("select count(*) from ".GD_GOODS." a left join ".GD_OPENMARKET_GOODS." b on a.goodsno=b.goodsno where b.goodsno IS NULL");

if (!$_GET['page_num']) $_GET['page_num'] = 10;
$selected['page_num'][$_GET['page_num']] = "selected";
$selected['skey'][$_GET['skey']] = "selected";
$checked['open'][$_GET['open']] = "checked";

$orderby = ($_GET['sort']) ? $_GET['sort'] : "-a.goodsno";
$div = explode(" ",$orderby);
$flag['sort'][$div[0]] = (!preg_match("/desc$/i",$orderby)) ? "��" : "��";

if ($_GET['cate']){
	$category = array_notnull($_GET['cate']);
	$category = $category[count($category)-1];
}

$db_table = "
".GD_GOODS." a
left join ".GD_OPENMARKET_GOODS." b on a.goodsno=b.goodsno
";

$where[] = "b.goodsno IS NULL";
if ($category){
	$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";
	$where[] = "c.category like '$category%'";
}
if ($_GET['sword']) $where[] = "a.{$_GET['skey']} like '%{$_GET['sword']}%'";
if ($_GET['open']) $where[] = "a.open=".substr($_GET['open'],-1);

$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->field = "
distinct a.goodsno,a.goodsnm,a.open,a.regdt,a.goodscd,a.origin,a.maker,a.brandno,a.shortdesc,a.runout,a.usestock
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

<div class="title title_top">�ǸŻ�ǰ ����ϱ� <span>�� ���θ� ��ǰ�� ���¸��� �ǸŰ����� �����մϴ�.</span></div>
<div id="useMsg"><script>callUseable('useMsg');</script></div>

<form name="frmList">
<input type="hidden" name="sort" value="<?=$_GET['sort']?>">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�з�����</td>
	<td><script>new categoryBox('cate[]',4,'<?=$category?>');</script></td>
</tr>
<tr>
	<td>�˻���</td>
	<td>
	<select name="skey">
	<option value="goodsnm" <?=$selected[skey][goodsnm]?>>��ǰ��
	<option value="goodsno" <?=$selected[skey][goodsno]?>>������ȣ
	<option value="goodscd" <?=$selected[skey][goodscd]?>>��ǰ�ڵ�
	<option value="keyword" <?=$selected[skey][keyword]?>>����˻���
	</select>
	<input type="text" name="sword" class="lline" value="<?=$_GET[sword]?>">
	</td>
</tr>
<tr>
	<td>��ǰ��¿���</td>
	<td class="noline">
	<input type="radio" name="open" value="" <?=$checked[open]['']?>>��ü
	<input type="radio" name="open" value="11" <?=$checked[open][11]?>>��»�ǰ
	<input type="radio" name="open" value="10" <?=$checked[open][10]?>>����»�ǰ
	</td>
</tr>
</table>
<div class="button_top"><input type="image" src="../img/btn_search2.gif"></div>


<table cellpadding="0" cellspacing="0" border="0" width=100% style="margin:20px 0;" class=small1>
<tr><td style="padding:10px 0 0 15px" bgcolor="#F7F7F7"><img src="../img/icn_open_chkpoint.gif"></td></tr>
<tr><td style="padding:10px 0 0 15px" bgcolor="#F7F7F7">* <font color="#444444">�Ʒ� ��ǰ����Ʈ�� �� ���θ� ��ǰ�� <font color="#627DCE">���¸��� �ǸŰ����� ���۵��� ����</font> ��ǰ����Ʈ�Դϴ�.</td></tr>
<tr><td style="padding:3px 0 0 15px" bgcolor="#F7F7F7">* <font color="#444444">�Ʒ� ��ǰ����Ʈ���� <font color="#627DCE">������ ��ǰ�� üũ�ϰ� �Ʒ� ��ǰ���۹�ư</font>�� ��������. <font color="#627DCE">�������۵� ����</font>�մϴ�.</td></tr>
<tr><td style="padding:3px 0 0 15px" bgcolor="#F7F7F7">* <font color="#444444">��ǰ��ȭ�� <font color="#627DCE">����� ��ǰ�̹���(Thumb Image)</font>�� �� ���θ��� <font color="#627DCE">���̹���</font>�� ���۵Ǿ� ���¸��� �ǸŰ����� ����˴ϴ�.</font></td></tr>
<tr><td style="padding:3px 0 0px 15px" bgcolor="#F7F7F7">* <font color="#444444">��, ��ǰ��ȭ�� <font color="#627DCE">�ϴ��� ��ǰ�󼼼��� ���ԵǴ� �̹���</font>�� <font color="#627DCE">���� �� ��ũ���� �ʽ��ϴ�.</font> (<font color="#627DCE">�ܺ� ����Ʈ��ũ�Ұ�</font>) <font class=small1> (�̿��� ��17��)</font></td></tr>
<tr><td style="padding:3px 0 10px 15px" bgcolor="#F7F7F7">*</font> <font color="#444444">��ǰ��ȭ�� �ϴ��� ��ǰ�󼼼��� ���ԵǴ� �̹�������� <a href="http://hosting.godo.co.kr/imghosting/intro.php" target="_blank"><font color="#627dce"><b>[<u>�� �̹���ȣ���� ����</u>]</b></font></a>�� �̿����ֽñ� �ٶ��ϴ�.</td></tr>
</table>


<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td class="pageInfo"><font class="ver8">�� <b><?=$total?></b>��, �˻� <b><?=$pg->recode['total']?></b>��, <b><?=$pg->page['now']?></b> of <?=$pg->page['total']?> Pages</td>
	<td align="right">

	<table cellpadding="0" cellspacing="0" border="0" width="500">
	<tr>
		<td valign="bottom"><img src="../img/sname_date.gif"><a href="javascript:sort('regdt')"><img name="sort_regdt" src="../img/list_up_off.gif"></a><a href="javascript:sort('regdt desc')"><img name="sort_regdt_desc" src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_product.gif"><a href="javascript:sort('goodsnm')"><img name="sort_goodsnm" src="../img/list_up_off.gif"></a><a href="javascript:sort('goodsnm desc')"><img name="sort_goodsnm_desc" src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_price.gif"><a href="javascript:sort('price')"><img name="sort_price" src="../img/list_up_off.gif"></a><a href="javascript:sort('price desc')"><img name="sort_price_desc" src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_brand.gif"><a href="javascript:sort('brandno')"><img name="sort_brandno" src="../img/list_up_off.gif"></a><a href="javascript:sort('brandno desc')"><img name="sort_brandno_desc" src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_company.gif"><a href="javascript:sort('maker')"><img name="sort_maker" src="../img/list_up_off.gif"></a><a href="javascript:sort('maker desc')"><img name="sort_maker_desc" src="../img/list_down_off.gif"></a></td>
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
<col width="50" align="center"><col><col width="370"><col width="70">
<tr><td class="rnd" colspan="12"></td></tr>
<tr class="rndbg">
	<th><input type="checkbox" onclick="chkBoxAll(document.getElementsByName('chk[]'),'rev')" class="null"></th>
	<th>��ǰ��</th>
	<th>�Ӽ�</th>
	<th>��������</th>
</tr>
<tr><td class="rnd" colspan="12"></td></tr>
<?
while ($data=$db->fetch($res))
{
	$catnmid = "catnm". $pg->idx;

	list($data['price']) = $db->fetch("select price from ".GD_GOODS_OPTION." where goodsno='{$data['goodsno']}' and link");
	list($optCnt, $stock) = $db->fetch("select count(*),sum(stock) from ".GD_GOODS_OPTION." where goodsno='{$data['goodsno']}'");
	list($data['category']) = $db->fetch("select openmarket from ".GD_GOODS_LINK." as a left join ".GD_CATEGORY." as b on a.category = b.category  where openmarket!='' and goodsno='{$data['goodsno']}' order by a.category limit 1");
	list($data['brandnm']) = $db->fetch("select brandnm from ".GD_GOODS_BRAND." where sno='{$data['brandno']}'");

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
	<td align="center"><a href="javascript:popup('../openmarket/popup.register.php?goodsno=<?=$data['goodsno']?>',825,700)"><img src="../img/btn_openmarket_indiregist.gif" alt="��������"></a></td>
</tr>
<tr><td height="4"></td></tr>
<tr><td colspan="12" class="rndline"></td></tr>
<? } ?>
</table>

<div align="center" class="pageNavi"><font class="ver8"><?=$pg->page[navi]?></font></div>

<div class="button"><a href="javascript:callQuickRegister();"><img src="../img/btn_openmarket_register.gif" alt="���¸����ǸŰ����� ��ǰ�����ϱ�"></a></div>

</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�� ���¸��� ��ǰ������ ���¸��� ���޻�(����, G����, ����, ���� ��) �� ��� ��ϵ��� �ʽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�̰����� ��ǰ���� �� ���¸��� �ǸŰ������� �ٽ� �ѹ� ��ǰ������ �ٽ��ѹ� �Ĳ��� Ȯ���ϼ���.</td></tr>

<tr><td height="15"></td></tr>
<tr><td style="padding-left:2px"><font class="def1"><b>[��ǰ���۽� ���ǻ���]</b></font></td></tr>
<tr><td height="2>"</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰ�̹��� ��δ� �� ���� ��ϵ� '���̹���'�� �����ɴϴ�. (���¸��� �ǸŰ������� �̹��� �߰� ����)</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰ��ȭ�� �ϴ��� ��ǰ�󼼼��� ���ԵǴ� �̹����� ���� �� ��ũ���� �ʽ��ϴ�. (�ܺ� ����Ʈ��ũ�Ұ�) <font class=small1> (�̿��� ��17��)</font></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰ��ȭ�� �ϴ��� ��ǰ�󼼼��� ���ԵǴ� �̹�������� ���� �̹���ȣ���� ��ü�� ����ϰ� �̿����ֽñ� �ٶ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">'���¸��Ϻз�'�� '�з���Ī'�� ���� ��Ī�۾��� �з��� ���մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�� ���θ� ��ǰ�� ���ߺз��� ��� �� �߿� ù��° �з��� ��Ī�� ���¸��Ϻз��� �����ɴϴ�. (��ǰ���� ��Ī����)</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ݿɼ� ����� ����ϴ� ��ǰ�� ��쿡�� <b>�ɼǺ� ������</b> �����մϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>