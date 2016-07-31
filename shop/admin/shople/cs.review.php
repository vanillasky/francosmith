<?
$location = "쇼플 > 상품 Q&A 관리";

$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
include "../_header.php";
require_once ('./_inc/config.inc.php');

// 쇼플 판매정보
$shople = Core::loader('shople');
$shopleCfg = $shople->cfg['shople'];

// get 파라미터
	$_GET['skey'] = isset($_GET['skey']) ? $_GET['skey'] : '';
	$_GET['sword'] = isset($_GET['sword']) ? trim($_GET['sword']) : '';

	$_GET['selStatCd'] = isset($_GET['selStatCd']) ? $_GET['selStatCd'] : '';

	$_GET['regdt'] = isset($_GET['regdt']) ? $_GET['regdt'] : array(date('Ymd'),date('Ymd'));

	$_GET['page_num'] = isset($_GET['page_num']) ? $_GET['page_num'] : 10;
	$_GET['page'] = isset($_GET['page']) ? $_GET['page'] : 1;

?>

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

</script>

<div class="title title_top">후기/리뷰 관리 <span>상품에 대한 고객의 후기/리뷰를 확인할 수 있습니다.</span></div>

<form name="frmListOption" id="frmListOption" target="ifrmHidden">
<input type="hidden" name="page" value="">
	<table class="tb">
	<col class="cellC" width="150"><col class="cellL" width="350">
	<col class="cellC" width="150"><col class="cellL">
	<tr>
		<td>작성자</td>
		<td>
			<select name="skey">
			<option value="prdNm" <?=($_GET['skey']=='prdNm') ? 'selected' : ''?>>상품명</option>
			<option value="prdNo" <?=($_GET['skey']=='prdNo') ? 'selected' : ''?>>상품번호</option>
			</select>

			<input type="text" name="sword" class="lline" value="<?=$_GET['sword']?>">
		</td>

		<td>문의유형</td>
		<td>
			<select>
				<option>전체</option>
				<option value="">전체</option>
				<option value="01">상품</option>
				<option value="02">배송</option>
				<option value="03">반품/환불/취소</option>
				<option value="04">교환/변경</option>
				<option value="05">기타</option>
			</select>
		</td>

	</tr>
	<tr>
		<td>기간</td>
		<td>
			<input type=text name=regdt[] value="<?=$_GET[regdt][0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline" style="width:70px"> -
			<input type=text name=regdt[] value="<?=$_GET[regdt][1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline" style="width:70px">
			<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>

		</td>

		<td>처리상태</td>
		<td>
			<select>
				<option>전체</option>
				<option value="">전체</option>
				<option value="Y">답변완료</option>
				<option value="N">미답변</option>
			</select>
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


<div class="title title_top">상품리뷰/ 구매후기 목록</div>
	<form name="frmList" method="post" target="_blank">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" id="oReviewList" class="gd_grid">
	<thead>
	</thead>
	<tbody>
	</tbody>
	</table>
	</form>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">11번가 상품페이지에서 등록된 고객문의 상품후기를 확인 할 수 있습니다.</td></tr>
</table>
</div>

<script type="text/javascript" src="./_inc/common.js?<?=time()?>"></script>
<script type="text/javascript" src="./_inc/godogrid.js?<?=time()?>"></script>
<script type="text/javascript">
var g_jsonData;

function _fnInit() {
	nsShople.review.init();
}
Event.observe(document, 'dom:loaded', _fnInit, false);
</script>
<script type="text/javascript">cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
