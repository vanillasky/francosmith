<?
$location = "쇼플 > 상품리스트";

$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
include "../_header.php";
require_once ('./_inc/config.inc.php');

// 쇼플 판매정보
$shople = Core::loader('shople');
$shopleCfg = $shople->cfg['shople'];


// 카테고리
	// depth 1은 가져옴
	$query = "SELECT * FROM ".GD_SHOPLE_CATEGORY." WHERE depth = 1 ORDER BY dispno";
	$rs = $db->query($query);

	$category = array();
	while($row = $db->fetch($rs,1)) {
		$category[] = $row;
	}

?>

<script type="text/javascript">
	function iciSelect(obj)
	{
		var row = obj.parentNode.parentNode;
		row.style.background = (obj.checked) ? "#F9FFF0" : '';
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

</script>

<div class="title title_top">상품리스트 <span>11번가에 등록된 상품을 조회하고 수정할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shople&no=5')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form name="frmListOption" id="frmListOption" target="ifrmHidden" method="post">
<input type="hidden" name="page" value="">
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>분류선택</td>
		<td>
		<div id="stepCate">
			<ul>
				<select id="el-shople-category-1" class="el-shople-category" name="category1">
				<option value="null">대분류 선택</option>
				<? foreach ($category as $cate) { ?>
				<option value="<?=$cate['dispno']?>"><?=$cate['name']?></option>
				<? } ?>
				</select>
			</ul>
			<ul class="separator">▶</ul>
			<ul>
				<select id="el-shople-category-2" class="el-shople-category" name="category2">
				<option value="null">중분류 선택</option>

				</select>
			</ul>
			<ul class="separator">▶</ul>
			<ul>
				<select id="el-shople-category-3" class="el-shople-category" name="category3">
				<option value="null">소분류 선택</option>

				</select>
			</ul>
			<ul class="separator">▶</ul>
			<ul>
				<select id="el-shople-category-4" class="el-shople-category" name="category4">
				<option value="null">세분류 선택</option>

				</select>
			</ul>
		</div>

		</td>
	</tr>
	<tr>
		<td>검색어</td>
		<td>
		<select name="skey">
		<option value="prdNm" <?=($_GET['skey']=='prdNm') ? 'selected' : ''?>>상품명</option>
		<option value="prdNo" <?=($_GET['skey']=='prdNo') ? 'selected' : ''?>>상품번호</option>
		</select>
		<input type="text" name="sword" class="lline" value="<?=$_GET['sword']?>">
		</td>
	</tr>
	<tr>
		<td>판매상태</td>
		<td class="noline">

		<label><input type="radio" name="selStatCd" value=""	<?=($_GET['selStatCd']=='') ? 'checked' : ''?>>전체</label>
		<label><input type="radio" name="selStatCd" value="103" <?=($_GET['selStatCd']=='103') ? 'checked' : ''?>>판매중</label>
		<label><input type="radio" name="selStatCd" value="104" <?=($_GET['selStatCd']=='104') ? 'checked' : ''?>>품절</label>
		<label><input type="radio" name="selStatCd" value="105" <?=($_GET['selStatCd']=='105') ? 'checked' : ''?>>판매중지</label>
		<label><input type="radio" name="selStatCd" value="107" <?=($_GET['selStatCd']=='107') ? 'checked' : ''?>>판매종료</label>

		</td>
	</tr>
	<tr>
		<td>기간</td>
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
	<div class="button_top"><input type="image" src="../img/btn_search2.gif"></div>

	<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td align="right">

		<table cellpadding="0" cellspacing="0" border="0" width="500">
		<tr>
			<td style="padding-left:20px;padding-bottom:5px;" align="right">
			<img src="../img/sname_output.gif" align="absmiddle">
			<select name="page_num" onchange="this.form.submit()">
			<? foreach (array(10,20,40,60,100) as $v){ ?>
			<option value='<?=$v?>' <?=($_GET['page_num'] == $v ? 'selected' : '' )?>><?=$v?>개 출력</option>
			<? } ?>
			</select>
			</td>
		</tr>
		</table>

		</td>
	</tr>
	</table>
</form>


<form name="frmList" method="post" target="_blank">
<table width="100%" cellpadding="0" cellspacing="0" border="0" id="oGoodslist" class="gd_grid">
<thead>
</thead>
<tbody>
</tbody>
</table>

<div id="pageNavi" class="pageNavi">
</div>



<div class="buttons">
	<a href="javascript:nsShople.goods.stopdisplay();"><img src="../img/btn_product_cancel.gif" alt="판매중지 설정"></a>
	<a href="javascript:nsShople.goods.startdisplay();"><img src="../img/btn_product_ok.gif" alt="판매중지 해제"></a>
	<a href="javascript:fnSaveChanged();"><img src="../img/btn_product_save.gif" alt="수정상품 저장"></a>
</div>
</form>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">11번가에 등록되어 판매되고 있는 상품 리스트 정보 입니다.</td></tr>

<tr><td><img src="../img/icon_list.gif" align="absmiddle">1) 판매중지설정</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">&nbsp;&nbsp;상품을 개별적으로 선택하여 판매중지 상태로 변경할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">&nbsp;&nbsp;판매중지로 설정된 상품은 11번가에 노출되지 않습니다.</td></tr>

<tr><td><img src="../img/icon_list.gif" align="absmiddle">2) 판매중지해제</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">&nbsp;&nbsp;판매중지로 설정된 상품을 다시 판매 상태로 변경하는 기능입니다.</td></tr>

<tr><td><img src="../img/icon_list.gif" align="absmiddle">3) 수정상품 저장</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">&nbsp;&nbsp;상품 리스트에서 바로 상품 정보를 수정할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">&nbsp;&nbsp;상품명을 영역과, 가격 영역을 클릭하면 바로 수정이 가능하며, 수정 후 ‘수정상품 저장’버튼을 클릭하면 수정된 내용이</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">&nbsp;&nbsp;반영되어 11번가에 노출 됩니다.</td></tr>


</table>
</div>

<script type="text/javascript" src="./_inc/common.js?<?=time()?>"></script>
<script type="text/javascript" src="./_inc/godogrid.js?<?=time()?>"></script>
<script type="text/javascript">

function fnSaveChanged() {
	var data = nsGodogrid.getFormData();
	nsShople.goods.save(data);
}

function _fnInit() {
	nsShople.category.init();
	nsShople.goods.init();

	nsGodogrid.init('oGoodslist',{});
}

Event.observe(document, 'dom:loaded', _fnInit, false);
</script>
<script type="text/javascript">cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
