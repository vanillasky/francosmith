<?
$location = "���� > ���/��ǰ/��ȯ����";
$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
include "../_header.php";
require_once ('./_inc/config.inc.php');

// ���� �Ǹ�����
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

<div class="title title_top">���/��ǰ/��ȯ���� <span>11�������� �߻��� ���/��ǰ/��ȯ ������ ������ �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shople&no=7')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form name="frmListOption" id="frmListOption" target="ifrmHidden">
<input type="hidden" name="page" value="">
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>�˻���</td>
		<td>
		<select name="skey">
			<option value="prdNm">��ǰ��</option>
			<option value="prdNo">��ǰ��ȣ</option>
		</select>
		<input type="text" name="sword" class="lline" value="">
		</td>
		<td>Ŭ���ӻ���</td>
		<td>
		<select name="method">
			<optgroup label="��Ұ���">
				<option value="GET_CLAIMCANCEL_REQUEST_LIST">��ҽ�û���</option>
				<option value="GET_CLAIMCANCEL_COMPLETE_LIST">��ҿϷ���</option>
			</optgroup>
			<optgroup label="��ǰ����">
				<option value="GET_CLAIMRETURN_REQUEST_LIST">��ǰ��û���</option>
				<option value="GET_CLAIMRETURN_COMPLETE_LIST">��ǰ�Ϸ���</option>
				<option value="GET_CLAIMRETURN_CANCEL_LIST">��ǰöȸ���</option>
			</optgroup>
			<optgroup label="��ȯ����">
				<option value="GET_CLAIMEXCHANGE_REQUEST_LIST">��ȯ��û���</option>
				<option value="GET_CLAIMEXCHANGE_COMPLETE_LIST">��ȯ�Ϸ���</option>
				<option value="GET_CLAIMEXCHANGE_CANCEL_LIST">��ȯöȸ���</option>
			</optgroup>

		</select>
		</td>
	</tr>
	<tr>
		<td>�Ⱓ</td>
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
	<a href="javascript:nsShople.claim.download();"><img src="../img/btn_excel_download.gif" alt="�����ٿ�ε�"></a>
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
	���ó�� :


				<a href="javascript:nsShople.claim.cancel.accept();"><img src="../img/btn_cancel_o.gif" alt="��ҽ���"></a>
				<a href="javascript:nsShople.claim.cancel.getReason('reject');"><img src="../img/btn_cancel_x.gif" alt="��Ұź�"></a>
	</div>

	<div class="button-group CLAIMRETURN" style="display:none;">
	��ǰó�� :
				<a href="javascript:nsShople.claim.return_.accept();"><img src="../img/btn_return_ok.gif" alt="��ǰ����"></a>
				<a href="javascript:nsShople.claim.return_.getReason('hold');"><img src="../img/btn_return_holding.gif" alt="��ǰ����"></a>
				<a href="javascript:nsShople.claim.return_.getReason('reject');"><img src="../img/btn_return_cancel.gif" alt="��ǰ�ź�"></a>
				<a href="if (confirm('��ǰ�ϷẸ���� �Ͻø� �ڵ����� ��ǰ�Ϸ�ó�� ���� �ʽ��ϴ�.\n��ǰ�ϷẸ�� ó�� �� �ݵ�� ��ǰ�Ϸ� ó���� ���ֽñ� �ٶ��ϴ�.\n��ǰ�Ϸᰡ ��Ⱓ ��ó�� �Ǹ� ������ Ȯ�� �� ���� ȯ�� ó���� �� �ֽ��ϴ�.\n�ڵ� ��ǰ�Ϸ� ������ �Ͻðڽ��ϱ�?')) { nsShople.claim.return_.getReason('accepthold');}"><img src="../img/btn_return_holdok.gif" alt="��ǰ�ϷẸ��"></a>
	</div>

	<div class="button-group CLAIMEXCHANGE" style="display:none;">
	��ȯó��
				<a href="javascript:nsShople.claim.exchange.getReason('accept');"><img src="../img/btn_change_ok.gif" alt="��ȯ����"></a>
				<a href="javascript:nsShople.claim.exchange.reject();"><img src="../img/btn_change_cancel.gif" alt="��ȯ�ź�"></a>
	</div>
</div>

</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">11�������� �߻��� �ֹ� ���/��ǰ/��ȯ ������ Ȯ���� �� ������, ������ �������� ���ǸźҰ�ó���� �� ���뵵 Ȯ���� �� �ֽ��ϴ�.</td></tr>
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
