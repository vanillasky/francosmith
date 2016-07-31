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
	$_GET['regdt'] = isset($_GET['regdt']) ? $_GET['regdt'] : array(date('Ymd'),date('Ymd'));
?>


<div class="title title_top">상품 Q&A 관리 <span>상품에 대한 고객의 질문과 답변을 관리할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shople&no=8c')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

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
			<select name="qnacd">
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
			<select name="stats">
				<option value="">전체</option>
				<option value="Y">답변완료</option>
				<option value="N">미답변</option>
			</select>
		</td>
	</tr>
	</table>
	<div class="button_top"><input type="image" src="../img/btn_search2.gif"></div>


</form>


<div class="title title_top">상품 Q&A 목록</div>
	<form name="frmList" method="post" target="_blank">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" id="oQnaList" class="gd_grid">
	<thead>
	</thead>
	<tbody>
	</tbody>
	</table>

<div id="pageNavi" class="pageNavi">
</div>
	</form>


<div id="answer_FORM" style="display:none;">
	<table border=0 cellpadding="0" cellspacing="0" width="100%" class="layout">
	<col width="50%">
	<col width="50%">
	<tr><td>
	<!-- S-->
		<table class="tb" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<th>문의일시</th>
			<td>#{createDt}</td>
			<th>처리일시</th>
			<td>#{answerDt}</td>
		</tr>
		<tr>
			<th>문의유형</th>
			<td colspan="3">#{qnaDtlsCdNm}</td>
		</tr>
		<tr>
			<th>제목</th>
			<td colspan="3">#{brdInfoSbjct}</td>
		</tr>
		<tr>
			<th>질문내용</th>
			<td colspan="3" height="111">#{brdInfoCont}</td>
		</tr>
		</table>

	<!-- E-->
	</td><td>
	<!-- S-->
		<form name="frmAnswer[#{brdInfoNo}]">
		<input type="hidden" name="brdInfoNo" value="#{brdInfoNo}">
		<input type="hidden" name="brdInfoClfNo" value="#{brdInfoClfNo}">
		<table class="tb" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<th>답변내용</th>
			<td>
				<textarea name="answerCont" style="">#{answerCont}</textarea>
				<div class="buttons" style="display:#{buttonDisplay}">
				<a href="javascript:nsShople.qna.answer(#{brdInfoNo})"><img src="../img/btn_answer_ok.gif" alt="답변등록"></a>
				</div>
			</td>
		</tr>
		</table>
		</form>
	<!-- E-->
	</td></tr>
	</table>

</div>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">11번가 상품페이지에서 등록된 고객문의 사항을 관리 할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">질문내용과 고객의 구매내역을 확인할 수 있으며, 질문에 대한 답변을 작성할 수 있습니다.</td></tr>
</table>
</div>

<script type="text/javascript" src="./_inc/common.js?<?=time()?>"></script>
<script type="text/javascript" src="./_inc/godogrid.js?<?=time()?>"></script>
<script type="text/javascript">
var g_jsonData;

function _fnInit() {
	nsShople.qna.init();
}
Event.observe(document, 'dom:loaded', _fnInit, false);
</script>
<script type="text/javascript">cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
