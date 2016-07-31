<?
$location = "쇼플 > 주문/배송관리";

$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
include "../_header.php";
require_once ('./_inc/config.inc.php');

// 쇼플 판매정보
$shople = Core::loader('shople');
$shopleCfg = $shople->cfg['shople'];
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

<div class="title title_top">주문/배송관리 <span>11번가에서 발생한 주문확인 및 배송관리를 할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shople&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form name="frmListOption" id="frmListOption" target="ifrmHidden">
<!--input type="hidden" name="page" value=""-->
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<col class="cellC"><col class="cellL">

	<tr>
		<td>검색어</td>
		<td>
		<select name="skey">
			<option value="prdNm" <?=($_GET['skey']=='prdNm') ? 'selected' : ''?>>상품명</option>
			<option value="prdNo" <?=($_GET['skey']=='prdNo') ? 'selected' : ''?>>상품번호</option>
		</select>
		<input type="text" name="sword" class="lline" value="<?=$_GET['sword']?>">
		</td>
		<td>주문상태</td>
		<td>
		<select name="method">
			<option value="GET_ORDER_CONFIRM_LIST">결제완료</option>
			<option value="GET_ORDER_DELIVERY_LIST">배송준비중</option>
			<option value="GET_ORDER_DELIVERING_LIST">배송중</option>
			<option value="GET_ORDER_COMPLETE_LIST">배송완료</option>
		</select>
		</td>
	</tr>
	<tr>
		<td>기간</td>
		<td colspan=3>
		<input type=text name=regdt[] value="<?=date("Ymd",strtotime("-7 day"))?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline"> -
		<input type=text name=regdt[] value="<?=$today?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline">
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

	<br>

</form>


<form name="frmList" method="post" target="_blank">

<div class="buttons">
	<a href="javascript:nsShople.order.excel();"><img src="../img/btn_excel.gif" alt="엑셀발송처리"></a>
	<a href="javascript:nsShople.order.download();"><img src="../img/btn_excel_download.gif" alt="엑셀다운로드"></a>
</div>


<table width="100%" cellpadding="0" cellspacing="0" border="0" id="oOrderlist" class="gd_grid">
<thead>
</thead>

<tbody>
</tbody>
</table>

<div id="pageNavi" class="pageNavi">
</div>

<div class="buttons">
	<div class="button-group CONFIRM" style="display:none;">
		<a href="javascript:nsShople.order.confirm();"><img src="../img/btn_product_sendok.gif" alt="발주확인"></a>
		<a href="javascript:nsShople.order.getReason('reject');"><img src="../img/btn_product_stop.gif" alt="판매불가처리"></a>
	</div>

	<div class="button-group DELIVERY" style="display:none;">
		<a href="javascript:nsShople.order.delivery();"><img src="../img/btn_sendok.gif" alt="발송처리"></a>
		<a href="javascript:nsShople.order.getReason('reject');"><img src="../img/btn_product_stop.gif" alt="판매불가처리"></a>
	</div>

</div>



<div class="buttons">


</div>

</form>










<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">11번가를 통해 주문된 내역을 확인하실 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">결제완료(무통장 입금 포함)된 주문 건만 리스팅 됩니다. </td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">주문 내역을 확인하신 후 발주확인 처리 하시면 배송중 상태로 변경됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">발주 확인된 주문건은 송장번호를 입력해야 합니다. ‘엑셀발송 처리’버튼을 클릭하시면 송장번호를 입력할 수 있는 팝업화면이 열립니다.</td></tr>

<tr><td><img src="../img/icon_list.gif" align="absmiddle">*판매불가처리</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품의 제고가 없거나 문제가 있는 상품의 경우 ‘판매불가처리’를 클릭하면 주문이 자동으로 취소되며, 고객에게 취소 안내가 나갑니다. </td></tr>
</table>
</div>

<script type="text/javascript" src="./_inc/common.js?<?=time()?>"></script>
<script type="text/javascript" src="./_inc/godogrid.js?<?=time()?>"></script>
<script type="text/javascript">
var g_jsonData;

function _fnInit() {
	nsShople.order.init();
	nsGodogrid.init('oOrderlist',{});
}

Event.observe(document, 'dom:loaded', _fnInit, false);
</script>
<script type="text/javascript">cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
