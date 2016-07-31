<?
$location = "쇼플 > 상품등록";

$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
include "../_header.php";
require_once ('./_inc/config.inc.php');
include "../../lib/page.class.php";

// 쇼플 판매정보
$shople = Core::loader('shople');
$shopleCfg = $shople->cfg;

// 변수 받기
	$_GET['sword'] = trim($_GET['sword']);

// 전체 상품수
	$query = "
		SELECT COUNT(G.goodsno) as cnt
		FROM ".GD_GOODS." AS G
		LEFT JOIN ".GD_SHOPLE_GOODS." AS GS
		ON G.goodsno = GS.goodsno
	";
	list ($total) = $db->fetch($query);

// 상품목록 가져오기
	if (!$_GET['page_num']) $_GET['page_num'] = 10;
	$selected['page_num'][$_GET['page_num']] = "selected";
	$selected['skey'][$_GET['skey']] = "selected";
	$checked['open'][$_GET['open']] = "checked";

	$orderby = ($_GET['sort']) ? $_GET['sort'] : "GS.11st, -G.goodsno";
	$div = explode(" ",$orderby);
	$flag['sort'][$div[0]] = (!preg_match("/desc$/i",$orderby)) ? "▲" : "▼";

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

<div class="title title_top">판매상품 등록하기 <span>내 쇼핑몰 상품을 11번가 판매관리로 전송합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shople&no=4')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table border=4 bordercolor=#dce1e1 style="border-collapse:collapse" width=750>
<tr><td style="padding:7 10 10 10">
<div style="padding-top:5"><b>※ 쇼플 플러그 상품등록 유의사항</b></div>
<div style="padding-top:5;padding-left:15px;"><font class=g9 color=#444444>쇼플플러그 서비스는 </font></div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>11번가 내에 쇼플이라는 판매자로 등록된 상점에 상품을 연동하여 판매하는 서비스 입니다. </font></div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>11번가로 연동하는 상품인 경우에는 상품명, 또는 상품에 대한 유의사항이 있습니다.</font></div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>11번가의 상품 등록 기준에 적합하지 않은 상품은 모니터링을 통해 판매정지 처리 되기 때문에 </font></div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>상품 등록시  다시 한번 확인하신 후 연동해주시길 바랍니다.</font></div>
<div style="padding-top:5;padding-left:15px;"><font class=g9 color=#444444><b>1. 특정 브랜드명 또는 유사 단어가 들어간 상품 등록시 판매정지 처리 됩니다.</b></font></div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;11번가에 연동하여 판매할 상품인 경우 상품명을 변경하여 주시길 바랍니다. </font></div>
<div style="padding-top:5;padding-left:15px;"><font class=g9 color=#444444><b>2. 특정브랜드의 모조품은 등록시에도 판매가 정지됩니다.</b></font></div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;11번가에 등록한 상품이 정품이 아닌 경우 11번가 또는 해당 브랜드의 모니터링을 통해 상품을 판매할 수  없게 됩니다</font></div>
</tr></tr>
</table>
<br>
<form name="frmListOption">
<input type="hidden" name="page" value="">
	<input type="hidden" name="sort" value="<?=$_GET['sort']?>">
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>분류선택</td>
		<td><script type="text/javascript">new categoryBox('cate[]',4,'<?=$category?>');</script></td>
	</tr>
	<tr>
		<td>검색어</td>
		<td>
		<select name="skey">
		<option value="goodsnm" <?=$selected[skey][goodsnm]?>>상품명
		<option value="goodsno" <?=$selected[skey][goodsno]?>>고유번호
		<option value="goodscd" <?=$selected[skey][goodscd]?>>상품코드
		<option value="keyword" <?=$selected[skey][keyword]?>>유사검색어
		</select>
		<input type="text" name="sword" class="lline" value="<?=$_GET[sword]?>">
		</td>
	</tr>
	<tr>
		<td>상품출력여부</td>
		<td class="noline">
		<input type="radio" name="open" value="" <?=$checked[open]['']?>>전체
		<input type="radio" name="open" value="11" <?=$checked[open][11]?>>출력상품
		<input type="radio" name="open" value="10" <?=$checked[open][10]?>>미출력상품
		</td>
	</tr>
	<tr>
		<td>11번가 등록여부</td>
		<td class="noline">
		<input type="radio" name="is11st" value=""	<?=($_GET['is11st'] == '' ? 'checked' : '')?>>전체
		<input type="radio" name="is11st" value="Y" <?=($_GET['is11st'] == 'Y' ? 'checked' : '')?>>등록상품
		<input type="radio" name="is11st" value="N" <?=($_GET['is11st'] == 'N' ? 'checked' : '')?>>미등록상품
		</td>
	</table>
	<div class="button_top"><input type="image" src="../img/btn_search2.gif"></div>

	<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td class="pageInfo"><font class="ver8">총 <b><?=$total?></b>개, 검색 <b><?=$pg->recode['total']?></b>개, <b><?=$pg->page['now']?></b> of <?=$pg->page['total']?> Pages</td>
		<td align="right">

		<table cellpadding="0" cellspacing="0" border="0" width="500">
		<tr>
			<td valign="bottom"><img src="../img/sname_date.gif"><a href="javascript:sort('regdt')"><img name="sort_regdt" src="../img/list_up_off.gif"></a><a href="javascript:sort('regdt desc')"><img name="sort_regdt_desc" src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_product.gif"><a href="javascript:sort('goodsnm')"><img name="sort_goodsnm" src="../img/list_up_off.gif"></a><a href="javascript:sort('goodsnm desc')"><img name="sort_goodsnm_desc" src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_price.gif"><a href="javascript:sort('price')"><img name="sort_price" src="../img/list_up_off.gif"></a><a href="javascript:sort('price desc')"><img name="sort_price_desc" src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_brand.gif"><a href="javascript:sort('brandno')"><img name="sort_brandno" src="../img/list_up_off.gif"></a><a href="javascript:sort('brandno desc')"><img name="sort_brandno_desc" src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_company.gif"><a href="javascript:sort('maker')"><img name="sort_maker" src="../img/list_up_off.gif"></a><a href="javascript:sort('maker desc')"><img name="sort_maker_desc" src="../img/list_down_off.gif"></a></td>
			<td style="padding-left:20px">
			<img src="../img/sname_output.gif" align="absmiddle">
			<select name="page_num" onchange="this.form.submit()">
			<? foreach (array(10,20,40,60,100) as $v){ echo "<option value='{$v}' {$selected['page_num'][$v]}>{$v}개 출력"; } ?>
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
	<th>번호</th>
	<th>상품명</th>
	<th>등록일</th>
	<th>판매가</th>
	<th>재고</th>
	<th>11번가등록여부</th>
	<th>개별전송</th>
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

	if ($data['runout'] == 1) $stock = '품절';
	else if ($data['usestock'] != 'o') $stock = '무한정판매';
	else if ($optCnt > 1) $stock = '옵션상품';
	if (is_numeric($stock) === true) $able = ' style="width:100%"';
	else $able = 'readonly style="width:100%; background:#eeeeee;" title="'. $stock .'은 여기서 수정할 수 없습니다."';

	$data = array_map("htmlspecialchars",$data);
?>
<tr><td height="4" colspan="12"></td></tr>
<tr height="25" bgcolor="#ffffff" bg="#ffffff">
	<td class="noline"><input type="checkbox" name="chk[]" value="<?=$data['goodsno']?>" subject="<?=strip_tags($data['goodsnm'])?>" onclick="iciSelect(this)"></td>
	<td><font class="ver8" color="#616161"><?=$pg->idx--?></font></td>
	<td valign="top" class="osd-<?=$data['goodsno']?>">
		<div><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data['goodsno']?>',825,600)"><font class="small1" color="#616161">상품번호 : <?=$data['goodsno']?></font></a></div>
		<?=($data['goodsnm'])?>
		<input type="hidden" name="category" value="<?=$data['category']?>" id="<?=$catnmid?>">
		<!--div style="" id="<?=$catnmid?>_text" class=small1>11번가 카테고리 : <?=$data[full_name]?></div-->
	</td>
	<td><?=$data[regdt]?></td>
	<td><?=number_format($data[price])?></td>
	<td><?=number_format($data[totstock])?></td>
	<td><span id="prdno-<?=$data['goodsno']?>"><?=($data[is11st] ? 'Y' : '미등록')?></span></td>
	<td align="center"><a href="javascript:nsShople.edit.goods(<?=$data['goodsno']?>);"><img src="../img/btn_openmarket_indiregist.gif" alt="개별전송"></a></td>
</tr>
<tr><td height="4"></td></tr>
<tr><td colspan="12" class="rndline"></td></tr>
<? } ?>
</table>

<div align="center" class="pageNavi"><font class="ver8"><?=$pg->page[navi]?></font></div>

<div class="buttons">
	<!--label><input type="radio" name="target" value="all">검색된 상품 전체 전송</label-->
	<label><input type="radio" name="target" value="checked" checked>선택한 상품 전송</label>
	<a href="javascript:nsShople.goods.register();"><img src="../img/btn_product_send.gif"></a>
</div>

</form>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">판매하는 상품 중  11번가에 노출 하고자 하는 상품을 선택해 주세요. </td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">선택한 상품이 11번가에 노출되어 판매 됩니다. </td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">전체 상품을 노출할 수 있으며, 카테고리별 특정상품을 선택하여 노출할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">검색된 상품 전체 전송 또는 선택상 상품전송 체크 후 ‘상품전송 ‘버튼을 클릭해 주시면 상품이 전송됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"> ‘개별등록’기능을 사용하여 각각의 상품 정보를 수정하여 전송할 수 있습니다.</td></tr>

<tr><td height="15"></td></tr>
<tr><td style="padding-left:2px"><font class="def1"><b>[상품전송시 유의사항]</b></font></td></tr>
<tr><td height="2>"</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품이미지 경로는 내 샵에 등록된 '상세이미지'를 가져옵니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품상세화면 하단의 상품상세설명에 포함되는 이미지는 전송 및 링크되지 않습니다. (외부 사이트링크불가) (이용약관 제17조)</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품상세화면 하단의 상품상세설명에 포함되는 이미지등록은 전문 이미지호스팅 업체와 계약하고 이용해주시기 바랍니다..</td></tr>

</table>
</div>
<script type="text/javascript">cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
