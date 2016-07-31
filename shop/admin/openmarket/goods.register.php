<?

$location = "오픈마켓 다이렉트 서비스 > 판매상품 등록하기";
$scriptLoad='<link rel="styleSheet" href="./js/style.css">';
$scriptLoad.='<script src="./js/common.js"></script>';
include "../_header.php";
include "../../lib/page.class.php";

### 공백 제거
$_GET['sword'] = trim($_GET['sword']);

list ($total) = $db->fetch("select count(*) from ".GD_GOODS." a left join ".GD_OPENMARKET_GOODS." b on a.goodsno=b.goodsno where b.goodsno IS NULL");

if (!$_GET['page_num']) $_GET['page_num'] = 10;
$selected['page_num'][$_GET['page_num']] = "selected";
$selected['skey'][$_GET['skey']] = "selected";
$checked['open'][$_GET['open']] = "checked";

$orderby = ($_GET['sort']) ? $_GET['sort'] : "-a.goodsno";
$div = explode(" ",$orderby);
$flag['sort'][$div[0]] = (!preg_match("/desc$/i",$orderby)) ? "▲" : "▼";

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

<div class="title title_top">판매상품 등록하기 <span>내 쇼핑몰 상품을 오픈마켓 판매관리로 전송합니다.</span></div>
<div id="useMsg"><script>callUseable('useMsg');</script></div>

<form name="frmList">
<input type="hidden" name="sort" value="<?=$_GET['sort']?>">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>분류선택</td>
	<td><script>new categoryBox('cate[]',4,'<?=$category?>');</script></td>
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
</table>
<div class="button_top"><input type="image" src="../img/btn_search2.gif"></div>


<table cellpadding="0" cellspacing="0" border="0" width=100% style="margin:20px 0;" class=small1>
<tr><td style="padding:10px 0 0 15px" bgcolor="#F7F7F7"><img src="../img/icn_open_chkpoint.gif"></td></tr>
<tr><td style="padding:10px 0 0 15px" bgcolor="#F7F7F7">* <font color="#444444">아래 상품리스트는 내 쇼핑몰 상품중 <font color="#627DCE">오픈마켓 판매관리에 전송되지 않은</font> 상품리스트입니다.</td></tr>
<tr><td style="padding:3px 0 0 15px" bgcolor="#F7F7F7">* <font color="#444444">아래 상품리스트에서 <font color="#627DCE">전송할 상품을 체크하고 아래 상품전송버튼</font>을 누르세요. <font color="#627DCE">개별전송도 가능</font>합니다.</td></tr>
<tr><td style="padding:3px 0 0 15px" bgcolor="#F7F7F7">* <font color="#444444">상품상세화면 <font color="#627DCE">상단의 상품이미지(Thumb Image)</font>는 내 쇼핑몰의 <font color="#627DCE">상세이미지</font>가 전송되어 오픈마켓 판매관리에 저장됩니다.</font></td></tr>
<tr><td style="padding:3px 0 0px 15px" bgcolor="#F7F7F7">* <font color="#444444">단, 상품상세화면 <font color="#627DCE">하단의 상품상세설명에 포함되는 이미지</font>는 <font color="#627DCE">전송 및 링크되지 않습니다.</font> (<font color="#627DCE">외부 사이트링크불가</font>) <font class=small1> (이용약관 제17조)</font></td></tr>
<tr><td style="padding:3px 0 10px 15px" bgcolor="#F7F7F7">*</font> <font color="#444444">상품상세화면 하단의 상품상세설명에 포함되는 이미지등록은 <a href="http://hosting.godo.co.kr/imghosting/intro.php" target="_blank"><font color="#627dce"><b>[<u>고도 이미지호스팅 서비스</u>]</b></font></a>를 이용해주시기 바랍니다.</td></tr>
</table>


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


<form name="form">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<col width="50" align="center"><col><col width="370"><col width="70">
<tr><td class="rnd" colspan="12"></td></tr>
<tr class="rndbg">
	<th><input type="checkbox" onclick="chkBoxAll(document.getElementsByName('chk[]'),'rev')" class="null"></th>
	<th>상품명</th>
	<th>속성</th>
	<th>개별전송</th>
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

	if ($data['runout'] == 1) $stock = '품절';
	else if ($data['usestock'] != 'o') $stock = '무한정판매';
	else if ($optCnt > 1) $stock = '옵션상품';
	if (is_numeric($stock) === true) $able = ' style="width:100%"';
	else $able = 'readonly style="width:100%; background:#eeeeee;" title="'. $stock .'은 여기서 수정할 수 없습니다."';

	$data = array_map("htmlspecialchars",$data);
?>
<tr><td height="4" colspan="12"></td></tr>
<tr height="25" bgcolor="#ffffff" bg="#ffffff">
	<td class="noline"><input type="checkbox" name="chk[]" value="<?=$data['goodsno']?>" subject="<?=strip_tags($data['goodsnm'])?>" onclick="iciSelect(this)"><br><font class="ver8" color="#616161"><?=$pg->idx--?></font></td>
	<td valign="top">
	<div><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data['goodsno']?>',825,600)"><font class="small1" color="#616161">상품번호 : <?=$data['goodsno']?></font></a></div>
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
		<th><font class="small1" color="#1D8E0D">원산지</th>
		<td><input type="text" name="origin" value="<?=$data['origin']?>" style="width:100%"></td>
		<th><font class="small1" color="#1D8E0D">판매가</th>
		<td><input type="text" name="price" value="<?=$data['price']?>" style="width:100%"></td>
		<th><font class="small1" color="#1D8E0D">재고</th>
		<td><input type="text" name="stock" value="<?=$stock?>" <?=$able?>></td>
	</tr>
	<tr bgcolor="#FFEFDF">
		<th><font class="small1" color="#F07800">제조사</th>
		<td><input type="text" name="maker" value="<?=$data['maker']?>" style="width:100%"></td>
		<th><font class="small1" color="#F07800">브랜드</th>
		<td><input type="text" name="brandnm" value="<?=$data['brandnm']?>" style="width:100%"></td>
		<th><font class="small1" color="#F07800">모델명</th>
		<td><input type="text" name="goodscd" value="<?=$data['goodscd']?>" style="width:100%"></td>
	</tr>
	<tr>
		<th><font class="small1" color="#444444">홍보문구</th>
		<td colspan="5"><input type="text" name="shortdesc" value="<?=$data['shortdesc']?>" style="width:100%" maxlength="25"></td>
	</tr>
	</table>
	</td>
	<td align="center"><a href="javascript:popup('../openmarket/popup.register.php?goodsno=<?=$data['goodsno']?>',825,700)"><img src="../img/btn_openmarket_indiregist.gif" alt="개별전송"></a></td>
</tr>
<tr><td height="4"></td></tr>
<tr><td colspan="12" class="rndline"></td></tr>
<? } ?>
</table>

<div align="center" class="pageNavi"><font class="ver8"><?=$pg->page[navi]?></font></div>

<div class="button"><a href="javascript:callQuickRegister();"><img src="../img/btn_openmarket_register.gif" alt="오픈마켓판매관리에 상품전송하기"></a></div>

</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">본 오픈마켓 상품전송은 오픈마켓 제휴사(옥션, G마켓, 온켓, 엠플 등) 에 즉시 등록되지 않습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">이곳에서 상품전송 후 오픈마켓 판매관리에서 다시 한번 상품정보를 다시한번 꼼꼼히 확인하세요.</td></tr>

<tr><td height="15"></td></tr>
<tr><td style="padding-left:2px"><font class="def1"><b>[상품전송시 유의사항]</b></font></td></tr>
<tr><td height="2>"</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품이미지 경로는 내 샵에 등록된 '상세이미지'를 가져옵니다. (오픈마켓 판매관리에서 이미지 추가 가능)</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품상세화면 하단의 상품상세설명에 포함되는 이미지는 전송 및 링크되지 않습니다. (외부 사이트링크불가) <font class=small1> (이용약관 제17조)</font></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품상세화면 하단의 상품상세설명에 포함되는 이미지등록은 전문 이미지호스팅 업체와 계약하고 이용해주시기 바랍니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">'오픈마켓분류'란 '분류매칭'을 통해 매칭작업한 분류를 말합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">내 쇼핑몰 상품이 다중분류일 경우 그 중에 첫번째 분류에 매칭된 오픈마켓분류를 가져옵니다. (상품개별 매칭가능)</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">가격옵션 기능을 사용하는 상품의 경우에는 <b>옵션별 수량만</b> 적용합니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>