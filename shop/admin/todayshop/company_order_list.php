<?

//$hiddenLeft = 1;
$location = "��ǰ���� > ��ǰ����Ʈ";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";

$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' ���� ��û�ȳ��� ���� �����ͷ� �������ֽñ� �ٶ��ϴ�.', -1);
}

### ���� ����
$_GET['sword'] = trim($_GET['sword']);
if ($_GET['category']) {
	for($i = 0; $i < count($_GET['category']); $i++) {
		if ($_GET['category'][$i]) $category = $_GET['category'][$i];
	}
	list($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_TODAYSHOP_GOODS." AS tg JOIN ".GD_TODAYSHOP_LINK." AS tc ON tg.tgsno=tc.tgsno WHERE tc.category LIKE '".$category."%'");
}
else list($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_TODAYSHOP_GOODS);

if (!$_GET['page_num']) $_GET['page_num'] = 10;
$selected['page_num'][$_GET['page_num']] = "selected";
$selected['skey'][$_GET['skey']] = "selected";
$checked['status'][$_GET['status']] = "checked";

$orderby = ($_GET['sort']) ? $_GET['sort'] : "-tg.tgsno";
$div = explode(" ",$orderby);
$flag['sort'][$div[0]] = (!preg_match("/desc$/i",$orderby)) ? "��" : "��";

$db_table = GD_TODAYSHOP_GOODS." AS tg JOIN ".GD_GOODS." AS g ON tg.goodsno=g.goodsno LEFT JOIN ".GD_GOODS_OPTION." AS go ON g.goodsno=go.goodsno AND link and go_is_deleted <> '1'";
if ($_GET['category']) {
	$db_table .= " JOIN ".GD_TODAYSHOP_LINK." AS tc ON tg.tgsno=tc.tgsno AND tc.category LIKE '".$category."%'";
}

if ($_GET['sword']) $where[] = $_GET['skey']." LIKE '%".$_GET['sword']."%'";
if ($_GET['price'][0]) $where[] = "price >= ".$_GET['price'][0];
if ($_GET['price'][1]) $where[] = "price <= ".$_GET['price'][1];
if ($_GET['regdt'][0] && $_GET['regdt'][1]) $where[] = "tg.regdt BETWEEN DATE_FORMAT(".$_GET['regdt'][0].",'%Y-%m-%d 00:00:00') AND DATE_FORMAT(".$_GET['regdt'][1].",'%Y-%m-%d 23:59:59')";
switch($_GET['status']) {
	case 'y' : {
		$where[] = "((now() BETWEEN tg.startdt AND tg.enddt) AND g.runout=0)";
		break;
	}
	case 'n' : {
		$where[] = "(NOT(now() BETWEEN tg.startdt AND tg.enddt) OR g.runout=1)";
		break;
	}
}

$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->field = " distinct tg.tgsno, tg.goodsno, tg.encor, tg.visible, tg.startdt, tg.enddt, tg.regdt, g.goodsnm, g.img_s, g.icon, g.runout, g.usestock, g.totstock, go.consumer, go.price, go.reserve, g.use_emoney ";
$pg->setQuery($db_table,$where,$orderby);

$pg->exec();
$res = $db->query($pg->query);
?>

<script type="text/javascript">
<!--
function eSort(obj,fld)
{
	var form = document.frmList;
	if (obj.innerText.charAt(1)=="��") fld += " desc";
	form.sort.value = fld;
	form.submit();
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

window.onload = function(){ sort_chk("<?=$_GET['sort']?>"); }
//-->
</script>
<script type="text/javascript" src="todayshop.js"></script>

<form name=frmList>
<input type=hidden name="sort" value="<?=$_GET['sort']?>">
	<div class="title title_top">��ü��ǰ����Ʈ<span>�����̼��� ����� ��� ��ǰ������ Ȯ���Ͻ� �� ������, ���ϰ� �����Ͻ� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>ī�װ�</td>
		<td>
			<select name="category[]" class="select" onchange="Category.change(this)">
				<option value="">= 1�� �з�=</option>
			</select>
			<select name="category[]" class="select" onchange="Category.change(this)">
				<option value="">= 2�� �з�=</option>
			</select>
			<select name="category[]" class="select" onchange="Category.change(this)">
				<option value="">= 3�� �з�=</option>
			</select>
			<select name="category[]" class="select">
				<option value="">= 4�� �з�=</option>
			</select>
			<script type="text/javascript">Category.set("category[]");</script>
		</td>
	</tr>
	<tr>
		<td>�˻���</td>
		<td>
			<select name="skey">
				<option value="goodsnm" <?=$selected['skey']['goodsnm']?>>��ǰ��
				<option value="tg.tgsno" <?=$selected['skey']['tg.tgsno']?>>������ȣ
				<option value="goodscd" <?=$selected['skey']['goodscd']?>>��ǰ�ڵ�
				<option value="keyword" <?=$selected['skey']['keyword']?>>����˻���
			</select>
			<input type=text name="sword" value="<?=$_GET['sword']?>" class="line" style="height:22px">
		</td>
	</tr>
	<tr>
		<td>��ǰ����</td>
		<td>
			<font class=small color=444444>
				<input type=text name="price[]" value="<?=$_GET['price'][0]?>" onkeydown="onlynumber()" size="15" class="rline"> �� -
				<input type=text name="price[]" value="<?=$_GET['price'][1]?>" onkeydown="onlynumber()" size="15" class="rline"> ��
			</font>
		</td>
	</tr>
	<tr>
		<td>��ǰ����ϱⰣ</td>
		<td>
			<input type=text name="regdt[]" value="<?=$_GET['regdt'][0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline"> -
			<input type=text name="regdt[]" value="<?=$_GET['regdt'][1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline">
			<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
		</td>
	</tr>
	<tr>
		<td>�ǸŻ�ǰ</td>
		<td class=noline>
			<input type=radio name=status value="" <?=$checked['status']['']?>>��ü
			<input type=radio name=status value="y" <?=$checked['status']['y']?>>�Ǹ���
			<input type=radio name=status value="n" <?=$checked['status']['n']?>>�ǸſϷ�
		</td>
	</tr>
	</table>
	<div class=button_top><input type=image src="../img/btn_search2.gif"></div>
	<div style="padding-top:15px"></div>
	<table width=100% cellpadding=0 cellspacing=0>
	<tr>
		<td class=pageInfo>
			<font class=ver8>�� <b><?=$total?></b>��, �˻� <b><?=$pg->recode[total]?></b>��, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages</font>
		</td>
		<td align=right>
			<table cellpadding=0 cellspacing=0 border=0>
			<tr>
				<td valign=bottom>
					<img src="../img/sname_date.gif"><a href="javascript:sort('regdt desc')"><img name=sort_regdt_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('regdt')"><img name=sort_regdt src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_product.gif"><a href="javascript:sort('goodsnm desc')"><img name=sort_goodsnm_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('goodsnm')"><img name=sort_goodsnm src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_price.gif"><a href="javascript:sort('price desc')"><img name=sort_price_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('price')"><img name=sort_price src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_brand.gif"><a href="javascript:sort('brandno desc')"><img name=sort_brandno_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('brandno')"><img name=sort_brandno src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"></td>
					<td style="padding-left:20px">
					<img src="../img/sname_output.gif" align=absmiddle>
					<select name=page_num onchange="this.form.submit()">
					<?
					$r_pagenum = array(10,20,40,60,100);
					foreach ($r_pagenum as $v){
					?>
					<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>�� ���
					<? } ?>
					</select>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
</form>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th width=60>��ȣ</th>
	<th></th>
	<th width=10></th>
	<th>��ǰ��</th>
	<th>�����</th>
	<th>����Ⱓ</th>
	<th>����</th>
	<th>�Ǹŷ�</th>
	<th>����</th>
	<th>����</th>
	<th>����</th>
	<th>����</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<col width=40 span=2 align=center>
<?
while ($data=$db->fetch($res)){
	$stock = $data['totstock'];

	### ������
	if(!$data['use_emoney']) {
		if(!$set['emoney']['chk_goods_emoney']) {
			if($set['emoney']['goods_emoney']) $data['reserve'] = getDcprice($data['price'],$set['emoney']['goods_emoney'].'%');
		}
		else {
			$data['reserve'] = $set['emoney']['goods_emoney'];
		}
	}
	$icon = setIcon($data['icon'],$data['regdt'],"../");

	### ����� ���� �ڵ� ǰ�� ó��
	if ($data['usestock'] && $stock==0) $data['runout'] = 1;
?>
<tr><td height=4 colspan=12></td></tr>
<tr height=25>
	<td><font class=ver8 color=616161><?=$pg->idx--?></font></td>
	<td style="border:1px #e9e9e9 solid;"><a href="../../todayshop/today_goods.php?tgsno=<?=$data['tgsno']?>" target=_blank><?=goodsimg($data['img_s'],40,'',1)?></a></td>
	<td></td>
	<td>
		<a href="./goods_reg.php?mode=modify&tgsno=<?=$data['tgsno']?>"><font color=303030><?=$data['goodsnm']?></a>
		<? if ($icon){ ?><div style="padding-top:3px"><?=$icon?></div><? } ?>
		<? if ($data['runout']){ ?><div style="padding-top:3px"><img src="../img/icon_open_soldout.gif"></div><? } ?>
	</td>
	<td align=center><font class=ver81 color=444444><?=substr($data['regdt'],0,10)?></font></td>
	<td align=center><font class=ver81 color=444444><?=$data['startdt']?> - <br/><?=$data['enddt']?></font></td>
	<td align=center><font class=ver81 color=444444><div style="text-decoration:line-through"><?=number_format($data['consumer'])?></div><b><?=number_format($data['price'])?></b></font></td>
	<td align=center><font class=ver81 color=444444><?=number_format($data['buyercnt'])?>/<?=number_format($stock)?></font></td>
	<td align=center><?=number_format($data['encor'])?></td>
	<td align=center><a href="indb.goods_list.php?mode=copyGoods&tgsno=<?=$data['tgsno']?>" onclick="return confirm('������ ��ǰ�� �ϳ� �� �ڵ�����մϴ�')"><img src="../img/i_copy.gif"></a></td>
	<td align=center><!--<a href="javascript:popup('popup.goods_reg.php?mode=modify&tgsno=<?=$data[tgsno]?>',825,600)">-->
		<a href="goods_reg.php?mode=modify&tgsno=<?=$data['tgsno']?>"><img src="../img/i_edit.gif"></a>
	</td>
	<td align=center>
		<a href="indb.goods_list.php?mode=delGoods&tgsno=<?=$data['tgsno']?>" onclick="return confirm('������ �����Ͻðڽ��ϱ�?\n\n���ε�� ��ǰ�̹����� �ڵ������˴ϴ�.\n��, �������� ���� �̹����� �ٸ� �������� ����ϰ� ���� �� �����Ƿ� �ڵ� �������� �ʽ��ϴ�. \n\'�����ΰ��� > webFTP�̹������� > data > editor\'���� �̹���üũ �� ���������ϼ���.')"><img src="../img/i_del.gif"></a>
	</td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>
<div align=center class=pageNavi><font class=ver8><?=$pg->page['navi']?></font></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">������� ����� ��ǰ�� ��ü��ǰ����Ʈ�Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�����ư�� ������ �ڵ����� �Ȱ��� ��ǰ�� �����˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰ������ �����Ϸ��� ������ư�� ��������.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰ�̹����� Ŭ���Ͻø� �ش� ��ǰ�� ��������</a>�� ��â���� ���� �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>