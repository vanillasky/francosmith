<?
$location = "셀리 > 판매상품 등록하기";
$scriptLoad.='<script src="./js/selly.js"></script>';

include "../_header.php";
include "../../lib/page.class.php";
@include "../../conf/config.selly.php";
@include "../selly/code.php";

if(!file_exists("../../conf/config.selly.php")) {
	msg("먼저 환경설정을 등록해 주시기 바랍니다.");
	go("./setting.php");
}

$where = array();

// GET 방식으로 넘어온 값 정리
$sort		= ($_GET['sort'])		? trim($_GET['sort'])		: "G.regdt desc";	// 정렬
$skey		= ($_GET['skey'])		? trim($_GET['skey'])		: "";				// 검색 필드
$sword		= ($_GET['sword'])		? trim($_GET['sword'])		: "";				// 검색어
$brandno	= ($_GET['brandno'])	? trim($_GET['brandno'])	: "";				// 브랜드
$open		= ($_GET['open'])		? trim($_GET['open'])		: "";				// 상품출력여부
$page_num	= ($_GET['page_num'])	? trim($_GET['page_num'])	: 10;				// 페이지당 표시할 목록 수
$page		= ($_GET['page'])		? trim($_GET['page'])		: 1;				// 페이지
$x			= ($_GET['x'])			? trim($_GET['x'])			: "";				// 클릭x
$y			= ($_GET['y'])			? trim($_GET['y'])			: "";				// 클릭y
if($_GET['cate']) {																	// 카테고리
	$category = array_notnull($_GET['cate']);
	$cate = $category;
	$category = $category[count($category)-1];
}
if($_GET['regdt'][0] && $_GET['regdt'][1]) {										// 등록일 범위
	$regdt1 = ($_GET['regdt'][0] < $_GET['regdt'][1]) ? trim($_GET['regdt'][0]) : trim($_GET['regdt'][1]);
	$regdt2 = ($_GET['regdt'][0] > $_GET['regdt'][1]) ? trim($_GET['regdt'][0]) : trim($_GET['regdt'][1]);
	$tmpRegdt1 = substr($regdt1, 0, 4)."-".substr($regdt1, 4, 2)."-".substr($regdt1, 6, 2)." 00:00:00";
	$tmpRegdt2 = substr($regdt2, 0, 4)."-".substr($regdt2, 4, 2)."-".substr($regdt2, 6, 2)." 24:00:00";
	$where[] = "G.regdt >= '$tmpRegdt1' AND G.regdt <= '$tmpRegdt2'";
}
if(preg_match("/price/", $sort) || $_GET['price']) {
	$add_table .= " LEFT JOIN ".GD_GOODS_OPTION." AS O on G.goodsno = O.goodsno and go_is_deleted <> '1' ";

	if($_GET['price'][0]!='' && $_GET['price'][1]!='') {							// 가격 범위
		$price1 = ($_GET['price'][0] < $_GET['price'][1]) ? trim($_GET['price'][0]) : trim($_GET['price'][1]);
		$price2 = ($_GET['price'][0] > $_GET['price'][1]) ? trim($_GET['price'][0]) : trim($_GET['price'][1]);
		$where[] = "O.price >= '$price1' AND O.price <= '$price2'";
	}
}
if(preg_match("/brandnm/", $sort) || $brandno) {
	$add_table .= " LEFT JOIN ".GD_GOODS_BRAND." AS B on G.brandno = B.sno";

	if($brandno) $where[] = "B.sno = '$brandno'";
}

if($category) {
	$add_table .= " LEFT JOIN ".GD_GOODS_LINK." AS I on G.goodsno = I.goodsno";

	// 상품분류 연결방식 전환 여부에 따른 처리
	$where[]	= getCategoryLinkQuery('I.category', $category, 'where');
}
if($sword) $where[] = "G.{$skey} LIKE '%{$sword}%'";
if($open) $where[] = "G.open = '".substr($open, -1)."'";

// selectbox & checkbox !!
$selected['page_num'][$page_num] = "selected";
$selected['skey'][$skey] = "selected";
$selected['brandno'][$brandno] = "selected";
$checked['open'][$open] = "checked";

// 검색 관련 쿼리
$qr_where = "";
for($i = 0, $imax = count($where); $i < $imax; $i++) {
	if($where[$i]) {
		if($qr_where) $qr_where .= " AND";
		$qr_where .= " ".$where[$i];
	}
}
if($qr_where) $qr_where = " WHERE ".$qr_where;
$qr_group = " GROUP BY G.goodsno";

// 총 레코드 수 계산
$qr = "SELECT G.goodsno FROM ".GD_GOODS." AS G LEFT OUTER JOIN ".GD_GOODS_STLOG." AS L ON G.goodsno = L.goodsno AND L.code = '000' $add_table $qr_where $qr_group";
$rs = $db->query($qr);
$total = $db->count_($rs);

$qstr = "sort=$sort&skey=$skey&sword=$sword&brandno=$brandno&open=$open&blog=$blog&page_num=$page_num&cate[]=$cate[0]&cate[]=$cate[1]&cate[]=$cate[2]&cate[]=$cate[3]&price[]=$price1&price[]=$price2&regdt[]=$regdt1&regdt[]=$regdt2";

// 페이지 셋팅 ( 기존 class에 group by를 사용했을 때 총 레코드 수에 오류가 있어 직접 여기서 작성 )
$blockPerPage	= 10;									// 블록당 나타낼 페이지 수
$totalPage		= ceil($total / $page_num);				// 총 페이지
$totalBlock		= ceil($totalPage / $blockPerPage);		// 총 블럭 수
$block			= ceil($page / $blockPerPage);			// 현재 블록
$startRow		= ($page - 1) * $page_num;				// 시작 레코드
$pageDisplay	= "";									// 페이지 리스트
if($page > 1 && $block > 1) $pageDisplay .= "<a href=\"".$_SERVER['PHP_SELF']."?".$qstr."&page=1\" class=\"navi\">[1]</a> &nbsp; &nbsp; ";
if($page > 1) $pageDisplay .= " <a href=\"".$_SERVER['PHP_SELF']."?".$qstr."&page=".($page - 1)."\"><img src=\"../img/arrow_pre_year.gif\" align=\"absmiddle\" /></a>";
for($i = (($block - 1) * $blockPerPage) + 1; $i < (($block - 1) * $blockPerPage) + 11; $i++) {
	if($i < 1 || $i > $totalPage) break;
	if($page == $i) $pageDisplay .= " <b>$i</b>";
	else $pageDisplay .= " <a href=\"".$_SERVER['PHP_SELF']."?".$qstr."&page=$i\" class=\"navi\">[$i]</a>";
}
if($page < $totalPage) $pageDisplay .= " <a href=\"".$_SERVER['PHP_SELF']."?".$qstr."&page=".($page + 1)."\"><img src=\"../img/arrow_next_year.gif\" align=\"absmiddle\" /></a>";
if($page < $totalPage && $block < $totalBlock) $pageDisplay .= " &nbsp; &nbsp; <a href=\"".$_SERVER['PHP_SELF']."?".$qstr."&page=$totalPage\" class=\"navi\">[$totalPage]</a>";

// 정렬, 범위 쿼리
$qr_order = " ORDER BY ".$sort;
$qr_limit = " LIMIT $startRow, $page_num";

// 쿼리 조합 & 실행
$qr = "SELECT
	G.goodsno, G.goodsnm, G.open, G.regdt, G.goodscd, G.origin, G.maker, G.brandno, G.shortdesc, G.runout, G.usestock, L.regdt AS lregdt, L.code AS lcode
FROM ".GD_GOODS." AS G
	LEFT OUTER JOIN ".GD_GOODS_STLOG." AS L ON G.goodsno = L.goodsno AND L.code = '000'
	$add_table
$qr_where $qr_group $qr_order $qr_limit";
$rs = $db->query($qr);
?>

<style type="text/css">
.ST_codeInsertBorder {position:absolute;border:3px #FFFFFF solid;background-color:#78B300;padding:5px;display:none;visibility:hidden;}
.ST_codeInsertBox {background-color:#FFFFFF;padding:3px;}
.ST_codeInsertBox .ST_title {font-family:Dotum;font-size:8pt;color:#1D8E0D;}
.ST_codeInsertBox .ST_button img {margin:0px 0px 0px 5px;}
</style>

<script>
window.onload = function() {
	sort_chk('<?=$sort?>');
	if(!getCookie('sellyRegAlert')) document.getElementById("sellyRegMSG").style.display = "";
}
</script>

<div id="sellyRegMSG" style="position:absolute;left:550px;top:300px;display:none;background-color:#FFFFFF;">
<table width="500px" border="0" cellspacing="0" cellpadding="0" style="border:3px solid #000000;">
	<tr>
		<td style="padding:18px">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="22"><img src="../img/pop_bu.gif" /></td>
					<td style="color: #000000; font-weight: bold;">상품 카테고리 등록</td>
				</tr>
				<tr>
					<td></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td></td>
					<td>상점의 카테고리 정보를 SELLY로 전송합니다.<br />
						상점의 카테고리 정보가 수정되면 카테고리 등록을 다시 해야 하며,<br />
						카테고리를 등록하지 않은 경우 상품 DATA 전송시 오류가 발생합니다.<br />
						<br />
						<b>상품 카테고리는 항상 가장 최신의 카테고리가 등록되어야 합니다.</b></td>
				</tr>
				<tr>
					<td></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2" align="center" style="padding-bottom:10px"><a href="./indb.php?mode=category"><img src="../img/btn_cate_input.gif" border="0" /></a></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td bgcolor="#000000">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td style="padding-left:10px;"><a href="javascript:;" onclick="SetCookie('sellyRegAlert', 'off', 1); getElementById('sellyRegMSG').style.display='none'" style="font-size:11px; color: #ffffff;">오늘 하루 열지 않기</a></td>
					<td align="right" style="padding-right:10px"><a href="javascript:;" onclick="document.getElementById('sellyRegMSG').style.display='none'"><img src="../img/btn_close.gif" border="0" /></a></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>


<? // 국가,배송정책 코드 ?>
<div class="ST_codeInsertBorder" id="STdivOrigin" style="width:195px;"><div class="ST_codeInsertBox"><table align="center">
<tr>
	<td class="ST_title">원산지 선택</td>
</tr>
<tr>
	<td><select name="originInsert" id="originInsert">
		<option value="">= 원산지 =</option>
		<? foreach($selly['origin'] as $k => $v) { ?>
		<option value="<?=$k?>"<?=$selected['origin'][$k]?>><?=$v?></option>
		<? } ?>
	</select></td>
</tr>
<tr>
	<td colspan="2" align="right" class="ST_button"><a href="javascript:STinsertOrigin()"><img src="../img/btn_delinum_confirm.gif" /></a><a href="javascript:STdivCloser('STdivOrigin')"><img src="../img/btn_delinum_close.gif" /></a></td>
</tr>
</table></div></div>
<div class="ST_codeInsertBorder" id="STdivDeliveryType" style="width:130px;"><div class="ST_codeInsertBox"><table align="center">
<tr>
	<td class="ST_title">배송정책 선택</td>
</tr>
<tr>
	<td><select name="delivery_typeInsert" id="delivery_typeInsert">
		<option value="">= 배송정책 =</option>
		<? foreach($selly['delivery_type'] as $k => $v) { ?>
		<option value="<?=$k?>"<?=$selected['origin'][$k]?>><?=$v?></option>
		<? } ?>
	</select></td>
</tr>
<tr>
	<td colspan="2" align="right" class="ST_button"><a href="javascript:STinsertDeliveryType()"><img src="../img/btn_delinum_confirm.gif" /></a><a href="javascript:STdivCloser('STdivDeliveryType')"><img src="../img/btn_delinum_close.gif" /></a></td>
</tr>
</table></div></div>
<? // /국가,배송정책 코드 ?>

<div class="title title_top">판매상품 등록하기 <span>내 쇼핑몰 상품을 셀리로 전송합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=selly&no=3')"><img src="../img/btn_q.gif" align="absmiddle" /></a></div>

<form name="frmList">
<input type="hidden" name="sort" value="<?=$sort?>" />

<table class="tb">
<col class="cellC"><col class="cellL" style="width:250px">
<col class="cellC"><col class="cellL">
<tr>
	<td>분류선택</td>
	<td colspan="3"><script>new categoryBox('cate[]', 4, '<?=$category?>');</script></td>
</tr>
<tr>
	<td>검색어</td>
	<td colspan="3">
		<select name="skey">
			<option value="goodsnm" <?=$selected['skey']['goodsnm']?>>상품명</option>
			<option value="goodsno" <?=$selected['skey']['goodsno']?>>고유번호</option>
			<option value="goodscd" <?=$selected['skey']['goodscd']?>>상품코드</option>
			<option value="keyword" <?=$selected['skey']['keyword']?>>유사검색어</option>
		</select>
		<input type=text name="sword" value="<?=$sword?>" class="line" style="height:22px" />
	</td>
</tr>
<tr>
	<td>상품가격</td>
	<td>
		<font class="small" color="#444444">
		<input type="text" name="price[]" value="<?=$price1?>" onkeydown="onlynumber()" size="15" class="rline" /> 원 -
		<input type="text" name="price[]" value="<?=$price2?>" onkeydown="onlynumber()" size="15" class="rline" /> 원
		</font>
	</td>
	<td>브랜드</td>
	<td>
		<select name="brandno">
			<option value="">-- 브랜드 선택 --</option>
			<?
			$bRes = $db->query("SELECT * FROM gd_goods_brand ORDER BY sort");
			while($tmp = $db->fetch($bRes)) {
			?>
			<option value="<?=$tmp['sno']?>" <?=$selected['brandno'][$tmp['sno']]?>><?=$tmp['brandnm']?></option>
			<? } ?>
		</select>
	</td>
</tr>
<tr>
	<td>상품등록일</td>
	<td colspan="3">
		<input type=text name="regdt[]" value="<?=$regdt1?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline" /> -
		<input type=text name="regdt[]" value="<?=$regdt2?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline" />
		<a href="javascript:setDate('regdt[]', <?=date("Ymd")?>, <?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('regdt[]', <?=date("Ymd", strtotime("-7 day"))?>, <?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('regdt[]', <?=date("Ymd", strtotime("-15 day"))?>, <?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('regdt[]', <?=date("Ymd", strtotime("-1 month"))?>, <?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('regdt[]', <?=date("Ymd", strtotime("-2 month"))?>, <?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align="absmiddle" /></a>
	</td>
</tr>
<tr>
	<td>상품출력여부</td>
	<td colspan="3" class="noline">
		<input type="radio" name=open value="" <?=$checked['open']['']?> />전체
		<input type="radio" name=open value="11" <?=$checked['open'][11]?> />출력상품
		<input type="radio" name=open value="10" <?=$checked['open'][10]?> />미출력상품
	</td>
</tr>
</table>
<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div>

<div style="padding-top:15px"></div>

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td class="pageInfo ver8">총 <b><?=$total?></b>개</td>
	<td align="right">

		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td valign="bottom"><img src="../img/sname_date.gif" /><a href="javascript:sort('G.regdt desc')"><img name="sort_regdt_desc" src="../img/list_up_off.gif"></a><a href="javascript:sort('G.regdt')"><img name="sort_regdt" src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_product.gif"><a href="javascript:sort('G.goodsnm desc')"><img name="sort_goodsnm_desc" src="../img/list_up_off.gif"></a><a href="javascript:sort('G.goodsnm')"><img name="sort_goodsnm" src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_price.gif"><a href="javascript:sort('O.price desc')"><img name="sort_price_desc" src="../img/list_up_off.gif"></a><a href="javascript:sort('O.price')"><img name="sort_price" src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_brand.gif"><a href="javascript:sort('B.brandnm desc')"><img name="sort_brandno_desc" src="../img/list_up_off.gif"></a><a href="javascript:sort('B.brandnm')"><img name="sort_brandno" src="../img/list_down_off.gif"></a></td>
			<td style="padding-left:20px">
				<img src="../img/sname_output.gif" align="absmiddle" />
				<select name="page_num" onchange="this.form.submit()" />
					<?
					$r_pagenum = array(10, 20, 40, 60, 100);
					foreach ($r_pagenum as $v){
					?>
					<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>개 출력</option>
					<? } ?>
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
<col width="50" align="center"><col width="80" align="center"><col><col width="405"><col width="70">
<tr><td class="rnd" colspan="12"></td></tr>
<tr class="rndbg">
	<th><input type="checkbox" onclick="chkBoxAll(document.getElementsByName('chk[]'),'rev')" class="null" /></th>
	<th>상품번호</th>
	<th>상품명</th>
	<th>사용자 설정</th>
	<th>개별전송</th>
</tr>
<tr><td class="rnd" colspan="12"></td></tr>
<?
while($data=$db->fetch($rs)) {
	$catnmid = "catnm". $pg->idx;

	list($data['price']) = $db->fetch("SELECT price FROM ".GD_GOODS_OPTION." WHERE goodsno = '".$data['goodsno']."' AND link and go_is_deleted <> '1' ");
	list($optCnt, $stock) = $db->fetch("SELECT COUNT(*), SUM(stock) FROM ".GD_GOODS_OPTION." WHERE goodsno = '".$data['goodsno']."' and go_is_deleted <> '1' ");
	list($data['category']) = $db->fetch("SELECT openmarket FROM ".GD_GOODS_LINK." AS a LEFT JOIN ".GD_CATEGORY." AS b ON a.category = b.category WHERE openmarket != '' AND goodsno = '".$data['goodsno']."' ORDER BY a.category LIMIT 1");
	list($data['brandnm']) = $db->fetch("SELECT brandnm FROM ".GD_GOODS_BRAND." WHERE sno = '".$data['brandno']."'");

	if($data['runout'] == 1) $stock = '품절';
	else if($data['usestock'] != 'o') $stock = '무한정판매';
	else if($optCnt > 1) $stock = '옵션상품';
	if(is_numeric($stock) === true) $able = ' style="width:100%"';
	else $able = 'readonly style="width:100%; background:#EEEEEE;" title="'. $stock .'은 여기서 수정할 수 없습니다."';

	$data = array_map("htmlspecialchars", $data);

	$data['regStatus'] = ($data['lcode'] == "000") ? "등록일 : ".$data['lregdt'] : "미등록 상품";
	$data['regStatusColor'] = ($data['lcode'] == "000") ? "#0033FF" : "#AAAAAA";
?>
<tr><td height="4" colspan="12"></td></tr>
<tr height="25" bgcolor="#ffffff">
	<td class="noline"><input type="checkbox" name="chk[]" value="<?=$data['goodsno']?>" onclick="iciSelect(this)" /><br /></td>
	<td><?=$data['goodsno']?></td>
	<td valign="top">
		<div style="width:100%; height:16px; overflow:hidden;"><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data['goodsno']?>',825,600)" title="<?=strip_tags($data['goodsnm'])?>"><?=$data['goodsnm']?></a></div>
		<div>
			<font id="logBoard<?=$data['goodsno']?>" class="small1" color="<?=$data['regStatusColor']?>"><?=$data['regStatus']?></font>
			<span id="resBoard<?=$data['goodsno']?>"></span>
		</div>
	</td>
	<td align="center" valign="middle">
		<table cellpadding="2" cellspacing="0" border="1" bordercolor="#dedede" style="border-collapse:collapse">
		<col width="45"><col width="65"><col width="55"><col width="80"><col width="55"><col width="50">
		<tr bgcolor="#E1F4D2">
			<th><font class="small1" color="#1D8E0D">원산지</th>
			<td><input type="text" name="origin" id="origin<?=$data['goodsno']?>" value="<?=$selly['origin'][$selly['set']['origin']]?>" style="width:100%" readonly onclick="STdivOpener('STdivOrigin', this)" /></td>
			<th><font class="small1" color="#1D8E0D">배송정책</th>
			<td><input type="text" name="delivery_type" id="delivery_type<?=$data['goodsno']?>" value="<?=$selly['delivery_type'][$selly['set']['delivery_type']]?>" style="width:100%" onclick="STdivOpener('STdivDeliveryType', this)" /></td>
			<th><font class="small1" color="#1D8E0D">배송비</th>
			<td><input type="text" name="delivery_price" id="delivery_price<?=$data['goodsno']?>" value="<?=$selly['set']['delivery_price']?>" style="width:100%" /></td>
		</tr>
		</table>
	</td>
	<td align="center"><a href="javascript:ajaxGoods('<?=$data['goodsno']?>');"><img src="../img/btn_openmarket_indiregist.gif" title="개별전송"></a></td>
</tr>
<tr><td height="4"></td></tr>
<tr><td colspan="12" class="rndline"></td></tr>
<? } ?>
</table>

<div align="center" class="pageNavi"><font class="ver8"><?=$pageDisplay?></font></div>

<div style="margin:10px 0"><font class=extext>※ 셀리에 상품등록시, 연동에 필요한 상품정보 항목을 확인해 주세요.<br />
셀리에서 필요로하는 필수정보가 e나무 상품정보에 등록되어 있어야 셀리에 정상적으로 상품이 등록됩니다.<br />
<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=selly&no=3')">[상품정보 필수항목 확인하기]</a></font></div>

<div class="button" id="STButton"><a href="javascript:ajaxMultiGoods();"><img src="../img/btn_selly_input.gif" title="SELLY에 상품등록하기" /></a></div>

</form>
<div style="height:20px"></div>
<? include "../_footer.php"; ?>

