<?
$location = "쇼플 > 취소/반품/교환관리";
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

<div class="title title_top">취소/반품/교환관리 <span>11번가에서 발생한 취소/반품/교환 내역을 관리할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shople&no=7')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form name="frmListOption" id="frmListOption" target="ifrmHidden">
<input type="hidden" name="page" value="">
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>검색어</td>
		<td>
		<select name="skey">
			<option value="prdNm">상품명</option>
			<option value="prdNo">상품번호</option>
		</select>
		<input type="text" name="sword" class="lline" value="">
		</td>
		<td>클레임상태</td>
		<td>
		<select name="method">
			<optgroup label="취소관리">
				<option value="GET_CLAIMCANCEL_REQUEST_LIST">취소신청목록</option>
				<option value="GET_CLAIMCANCEL_COMPLETE_LIST">취소완료목록</option>
			</optgroup>
			<optgroup label="반품관리">
				<option value="GET_CLAIMRETURN_REQUEST_LIST">반품신청목록</option>
				<option value="GET_CLAIMRETURN_COMPLETE_LIST">반품완료목록</option>
				<option value="GET_CLAIMRETURN_CANCEL_LIST">반품철회목록</option>
			</optgroup>
			<optgroup label="교환관리">
				<option value="GET_CLAIMEXCHANGE_REQUEST_LIST">교환신청목록</option>
				<option value="GET_CLAIMEXCHANGE_COMPLETE_LIST">교환완료목록</option>
				<option value="GET_CLAIMEXCHANGE_CANCEL_LIST">교환철회목록</option>
			</optgroup>

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
	<a href="javascript:nsShople.claim.download();"><img src="../img/btn_excel_download.gif" alt="엑셀다운로드"></a>
</div>



<table width="100%" cellpadding="0" cellspacing="0" border="0" id="oClaimList" class="gd_grid">
<thead>
</thead>

<tbody>
</tbody>
</table>

<div id="pageNavi" class="pageNavi">
</div>

<div class="buttons">
	<div class="button-group CLAIMCANCEL" style="display:none;">
	취소처리 :


				<a href="javascript:nsShople.claim.cancel.accept();"><img src="../img/btn_cancel_o.gif" alt="취소승인"></a>
				<a href="javascript:nsShople.claim.cancel.getReason('reject');"><img src="../img/btn_cancel_x.gif" alt="취소거부"></a>
	</div>

	<div class="button-group CLAIMRETURN" style="display:none;">
	반품처리 :
				<a href="javascript:nsShople.claim.return_.accept();"><img src="../img/btn_return_ok.gif" alt="반품승인"></a>
				<a href="javascript:nsShople.claim.return_.getReason('hold');"><img src="../img/btn_return_holding.gif" alt="반품보류"></a>
				<a href="javascript:nsShople.claim.return_.getReason('reject');"><img src="../img/btn_return_cancel.gif" alt="반품거부"></a>
				<a href="if (confirm('반품완료보류를 하시면 자동으로 반품완료처리 되지 않습니다.\n반품완료보류 처리 후 반드시 반품완료 처리를 해주시기 바랍니다.\n반품완료가 장기간 미처리 되면 고객센터 확인 후 강제 환불 처리될 수 있습니다.\n자동 반품완료 보류를 하시겠습니까?')) { nsShople.claim.return_.getReason('accepthold');}"><img src="../img/btn_return_holdok.gif" alt="반품완료보류"></a>
	</div>

	<div class="button-group CLAIMEXCHANGE" style="display:none;">
	교환처리
				<a href="javascript:nsShople.claim.exchange.getReason('accept');"><img src="../img/btn_change_ok.gif" alt="교환승인"></a>
				<a href="javascript:nsShople.claim.exchange.reject();"><img src="../img/btn_change_cancel.gif" alt="교환거부"></a>
	</div>
</div>

</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">11번가에서 발생한 주문 취소/반품/교환 내역을 확인할 수 있으며, 상점의 사정으로 ‘판매불가처리’ 한 내용도 확인할 수 있습니다.</td></tr>
</table>
</div>

<script type="text/javascript" src="./_inc/common.js?<?=time()?>"></script>
<script type="text/javascript" src="./_inc/godogrid.js?<?=time()?>"></script>
<script type="text/javascript">
var g_jsonData;

function _fnInit() {
	nsShople.claim.init();
	//nsGodogrid.init('oOrderlist',{});
}

Event.observe(document, 'dom:loaded', _fnInit, false);
</script>
<script type="text/javascript">cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
