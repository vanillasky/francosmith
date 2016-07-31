<?
$location = "���� > ��ǰ���";

$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
include "../_header.php";
require_once ('./_inc/config.inc.php');
include "../../lib/page.class.php";

// ���� �Ǹ�����
$shople = Core::loader('shople');
$shopleCfg = $shople->cfg;

// ���� �ޱ�
	$_GET['sword'] = trim($_GET['sword']);

// ��ü ��ǰ��
	$query = "
		SELECT COUNT(G.goodsno) as cnt
		FROM ".GD_GOODS." AS G
		LEFT JOIN ".GD_SHOPLE_GOODS." AS GS
		ON G.goodsno = GS.goodsno
	";
	list ($total) = $db->fetch($query);

// ��ǰ��� ��������
	if (!$_GET['page_num']) $_GET['page_num'] = 10;
	$selected['page_num'][$_GET['page_num']] = "selected";
	$selected['skey'][$_GET['skey']] = "selected";
	$checked['open'][$_GET['open']] = "checked";

	$orderby = ($_GET['sort']) ? $_GET['sort'] : "GS.11st, -G.goodsno";
	$div = explode(" ",$orderby);
	$flag['sort'][$div[0]] = (!preg_match("/desc$/i",$orderby)) ? "��" : "��";

	$where[] = "G.todaygoods='n'";

	if ($_GET['cate']){
		$category = array_notnull($_GET['cate']);
		$category = $category[count($category)-1];
	}

	if ($category){
		$where[] = "GL.category like '$category%'";
	}
	if ($_GET['sword']) $where[] = "G.{$_GET['skey']} like '%{$_GET['sword']}%'";
	if ($_GET['open']) $where[] = "G.open=".substr($_GET['open'],-1);

	if ($_GET['is11st'] == 'Y') $where[] = "GS.11st IS NOT NULL";
	elseif ($_GET['is11st'] == 'N') $where[] = "GS.11st IS NULL";

	$db_table = "
	/* FROM */ ".GD_GOODS." AS G

	INNER JOIN ".GD_GOODS_OPTION." AS GO
	ON G.goodsno = GO.goodsno AND link=1 and go_is_deleted <> '1'

	LEFT JOIN ".GD_SHOPLE_GOODS_MAP." AS GS
	ON G.goodsno = GS.goodsno

	LEFT JOIN ".GD_GOODS_LINK." AS GL
	ON G.goodsno = GL.goodsno

	LEFT JOIN (

			SELECT
				SCM.category,
				SUB2.full_dispno,
				SUB2.full_name

			FROM ".GD_SHOPLE_CATEGORY_MAP." AS SCM

			INNER JOIN (
						SELECT

							CONCAT_WS('|', SC1.dispno, SC2.dispno, SC3.dispno, SC4.dispno ) as full_dispno,
							CONCAT_WS(' > ', SC1.name, SC2.name, SC3.name, SC4.name ) as full_name

						FROM	 ".GD_SHOPLE_CATEGORY." AS SC1

						LEFT JOIN ".GD_SHOPLE_CATEGORY." AS SC2
						ON SC1.dispno = SC2.p_dispno

						LEFT JOIN ".GD_SHOPLE_CATEGORY." AS SC3
						ON SC2.dispno = SC3.p_dispno

						LEFT JOIN ".GD_SHOPLE_CATEGORY." AS SC4
						ON SC3.dispno = SC4.p_dispno

						WHERE SC1.depth = 1
			) AS SUB2
			ON SCM.11st = SUB2.full_dispno

	) AS SUB
	ON GL.category = SUB.category
	";

	$pg = new Page($_GET['page'],$_GET['page_num']);

	$pg->field = "
		distinct G.goodsno, G.goodsnm, G.open, G.regdt,G.goodscd,G.origin,G.maker,G.brandno,G.shortdesc,G.runout,G.usestock, G.regdt, G.totstock,

		GO.price,

		GS.11st AS is11st,

		SUB.full_dispno, SUB.full_name, SUB.category
	";
	$pg->setQuery($db_table,$where,$orderby," GROUP BY G.goodsno ");
	$pg->exec();
	$res = $db->query($pg->query);

?>

<script src="./_inc/common.js?<?=time();?>"></script>
<script type="text/javascript">
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
		var fm = document.frmListOption;
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

<div class="title title_top">�ǸŻ�ǰ ����ϱ� <span>�� ���θ� ��ǰ�� 11���� �ǸŰ����� �����մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shople&no=4')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table border=4 bordercolor=#dce1e1 style="border-collapse:collapse" width=750>
<tr><td style="padding:7 10 10 10">
<div style="padding-top:5"><b>�� ���� �÷��� ��ǰ��� ���ǻ���</b></div>
<div style="padding-top:5;padding-left:15px;"><font class=g9 color=#444444>�����÷��� ���񽺴� </font></div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>11���� ���� �����̶�� �Ǹ��ڷ� ��ϵ� ������ ��ǰ�� �����Ͽ� �Ǹ��ϴ� ���� �Դϴ�. </font></div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>11������ �����ϴ� ��ǰ�� ��쿡�� ��ǰ��, �Ǵ� ��ǰ�� ���� ���ǻ����� �ֽ��ϴ�.</font></div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>11������ ��ǰ ��� ���ؿ� �������� ���� ��ǰ�� ����͸��� ���� �Ǹ����� ó�� �Ǳ� ������ </font></div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>��ǰ ��Ͻ�  �ٽ� �ѹ� Ȯ���Ͻ� �� �������ֽñ� �ٶ��ϴ�.</font></div>
<div style="padding-top:5;padding-left:15px;"><font class=g9 color=#444444><b>1. Ư�� �귣��� �Ǵ� ���� �ܾ �� ��ǰ ��Ͻ� �Ǹ����� ó�� �˴ϴ�.</b></font></div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;11������ �����Ͽ� �Ǹ��� ��ǰ�� ��� ��ǰ���� �����Ͽ� �ֽñ� �ٶ��ϴ�. </font></div>
<div style="padding-top:5;padding-left:15px;"><font class=g9 color=#444444><b>2. Ư���귣���� ����ǰ�� ��Ͻÿ��� �ǸŰ� �����˴ϴ�.</b></font></div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;11������ ����� ��ǰ�� ��ǰ�� �ƴ� ��� 11���� �Ǵ� �ش� �귣���� ����͸��� ���� ��ǰ�� �Ǹ��� ��  ���� �˴ϴ�</font></div>
</tr></tr>
</table>
<br>
<form name="frmListOption">
<input type="hidden" name="page" value="">
	<input type="hidden" name="sort" value="<?=$_GET['sort']?>">
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>�з�����</td>
		<td><script type="text/javascript">new categoryBox('cate[]',4,'<?=$category?>');</script></td>
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
	<tr>
		<td>11���� ��Ͽ���</td>
		<td class="noline">
		<input type="radio" name="is11st" value=""	<?=($_GET['is11st'] == '' ? 'checked' : '')?>>��ü
		<input type="radio" name="is11st" value="Y" <?=($_GET['is11st'] == 'Y' ? 'checked' : '')?>>��ϻ�ǰ
		<input type="radio" name="is11st" value="N" <?=($_GET['is11st'] == 'N' ? 'checked' : '')?>>�̵�ϻ�ǰ
		</td>
	</table>
	<div class="button_top"><input type="image" src="../img/btn_search2.gif"></div>

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


<form name="frmList" method="post" target="_blank">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<col width="50" align="center">
<col width="50" align="center">
<col>
<col width="130" align="center">
<col width="100" align="center">
<col width="100" align="center">
<col width="90" align="center">
<col width="70">

<tr><td class="rnd" colspan="12"></td></tr>
<tr class="rndbg">
	<th><input type="checkbox" onclick="chkBoxAll(document.getElementsByName('chk[]'),'rev')" class="null"></th>
	<th>��ȣ</th>
	<th>��ǰ��</th>
	<th>�����</th>
	<th>�ǸŰ�</th>
	<th>���</th>
	<th>11������Ͽ���</th>
	<th>��������</th>
</tr>
<tr><td class="rnd" colspan="12"></td></tr>
<?
while ($data=$db->fetch($res))
{
	$catnmid = "catnm". $pg->idx;

	//list($data['price']) = $db->fetch("select price from ".GD_GOODS_OPTION." where goodsno='{$data['goodsno']}' and link");
	list($optCnt, $stock) = $db->fetch("select count(*),sum(stock) from ".GD_GOODS_OPTION." where goodsno='{$data['goodsno']}' and go_is_deleted <> '1'");
	//list($data['category']) = $db->fetch("select openmarket from ".GD_GOODS_LINK." as a left join ".GD_CATEGORY." as b on a.category = b.category where openmarket!='' and goodsno='{$data['goodsno']}' order by a.category limit 1");
	//list($data['brandnm']) = $db->fetch("select brandnm from ".GD_GOODS_BRAND." where sno='{$data['brandno']}'");

	if ($data['runout'] == 1) $stock = 'ǰ��';
	else if ($data['usestock'] != 'o') $stock = '�������Ǹ�';
	else if ($optCnt > 1) $stock = '�ɼǻ�ǰ';
	if (is_numeric($stock) === true) $able = ' style="width:100%"';
	else $able = 'readonly style="width:100%; background:#eeeeee;" title="'. $stock .'�� ���⼭ ������ �� �����ϴ�."';

	$data = array_map("htmlspecialchars",$data);
?>
<tr><td height="4" colspan="12"></td></tr>
<tr height="25" bgcolor="#ffffff" bg="#ffffff">
	<td class="noline"><input type="checkbox" name="chk[]" value="<?=$data['goodsno']?>" subject="<?=strip_tags($data['goodsnm'])?>" onclick="iciSelect(this)"></td>
	<td><font class="ver8" color="#616161"><?=$pg->idx--?></font></td>
	<td valign="top" class="osd-<?=$data['goodsno']?>">
		<div><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data['goodsno']?>',825,600)"><font class="small1" color="#616161">��ǰ��ȣ : <?=$data['goodsno']?></font></a></div>
		<?=($data['goodsnm'])?>
		<input type="hidden" name="category" value="<?=$data['category']?>" id="<?=$catnmid?>">
		<!--div style="" id="<?=$catnmid?>_text" class=small1>11���� ī�װ� : <?=$data[full_name]?></div-->
	</td>
	<td><?=$data[regdt]?></td>
	<td><?=number_format($data[price])?></td>
	<td><?=number_format($data[totstock])?></td>
	<td><span id="prdno-<?=$data['goodsno']?>"><?=($data[is11st] ? 'Y' : '�̵��')?></span></td>
	<td align="center"><a href="javascript:nsShople.edit.goods(<?=$data['goodsno']?>);"><img src="../img/btn_openmarket_indiregist.gif" alt="��������"></a></td>
</tr>
<tr><td height="4"></td></tr>
<tr><td colspan="12" class="rndline"></td></tr>
<? } ?>
</table>

<div align="center" class="pageNavi"><font class="ver8"><?=$pg->page[navi]?></font></div>

<div class="buttons">
	<!--label><input type="radio" name="target" value="all">�˻��� ��ǰ ��ü ����</label-->
	<label><input type="radio" name="target" value="checked" checked>������ ��ǰ ����</label>
	<a href="javascript:nsShople.goods.register();"><img src="../img/btn_product_send.gif"></a>
</div>

</form>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Ǹ��ϴ� ��ǰ ��  11������ ���� �ϰ��� �ϴ� ��ǰ�� ������ �ּ���. </td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">������ ��ǰ�� 11������ ����Ǿ� �Ǹ� �˴ϴ�. </td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ü ��ǰ�� ������ �� ������, ī�װ��� Ư����ǰ�� �����Ͽ� ������ �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�˻��� ��ǰ ��ü ���� �Ǵ� ���û� ��ǰ���� üũ �� ����ǰ���� ����ư�� Ŭ���� �ֽø� ��ǰ�� ���۵˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"> ��������ϡ������ ����Ͽ� ������ ��ǰ ������ �����Ͽ� ������ �� �ֽ��ϴ�.</td></tr>

<tr><td height="15"></td></tr>
<tr><td style="padding-left:2px"><font class="def1"><b>[��ǰ���۽� ���ǻ���]</b></font></td></tr>
<tr><td height="2>"</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰ�̹��� ��δ� �� ���� ��ϵ� '���̹���'�� �����ɴϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰ��ȭ�� �ϴ��� ��ǰ�󼼼��� ���ԵǴ� �̹����� ���� �� ��ũ���� �ʽ��ϴ�. (�ܺ� ����Ʈ��ũ�Ұ�) (�̿��� ��17��)</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰ��ȭ�� �ϴ��� ��ǰ�󼼼��� ���ԵǴ� �̹�������� ���� �̹���ȣ���� ��ü�� ����ϰ� �̿����ֽñ� �ٶ��ϴ�..</td></tr>

</table>
</div>
<script type="text/javascript">cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
