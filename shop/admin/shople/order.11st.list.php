<?
$location = "���� > �ֹ�/��۰���";

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

<div class="title title_top">�ֹ�/��۰��� <span>11�������� �߻��� �ֹ�Ȯ�� �� ��۰����� �� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shople&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form name="frmListOption" id="frmListOption" target="ifrmHidden">
<!--input type="hidden" name="page" value=""-->
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<col class="cellC"><col class="cellL">

	<tr>
		<td>�˻���</td>
		<td>
		<select name="skey">
			<option value="prdNm" <?=($_GET['skey']=='prdNm') ? 'selected' : ''?>>��ǰ��</option>
			<option value="prdNo" <?=($_GET['skey']=='prdNo') ? 'selected' : ''?>>��ǰ��ȣ</option>
		</select>
		<input type="text" name="sword" class="lline" value="<?=$_GET['sword']?>">
		</td>
		<td>�ֹ�����</td>
		<td>
		<select name="method">
			<option value="GET_ORDER_CONFIRM_LIST">�����Ϸ�</option>
			<option value="GET_ORDER_DELIVERY_LIST">����غ���</option>
			<option value="GET_ORDER_DELIVERING_LIST">�����</option>
			<option value="GET_ORDER_COMPLETE_LIST">��ۿϷ�</option>
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
	<a href="javascript:nsShople.order.excel();"><img src="../img/btn_excel.gif" alt="�����߼�ó��"></a>
	<a href="javascript:nsShople.order.download();"><img src="../img/btn_excel_download.gif" alt="�����ٿ�ε�"></a>
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
		<a href="javascript:nsShople.order.confirm();"><img src="../img/btn_product_sendok.gif" alt="����Ȯ��"></a>
		<a href="javascript:nsShople.order.getReason('reject');"><img src="../img/btn_product_stop.gif" alt="�ǸźҰ�ó��"></a>
	</div>

	<div class="button-group DELIVERY" style="display:none;">
		<a href="javascript:nsShople.order.delivery();"><img src="../img/btn_sendok.gif" alt="�߼�ó��"></a>
		<a href="javascript:nsShople.order.getReason('reject');"><img src="../img/btn_product_stop.gif" alt="�ǸźҰ�ó��"></a>
	</div>

</div>



<div class="buttons">


</div>

</form>










<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">11������ ���� �ֹ��� ������ Ȯ���Ͻ� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�����Ϸ�(������ �Ա� ����)�� �ֹ� �Ǹ� ������ �˴ϴ�. </td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ֹ� ������ Ȯ���Ͻ� �� ����Ȯ�� ó�� �Ͻø� ����� ���·� ����˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� Ȯ�ε� �ֹ����� �����ȣ�� �Է��ؾ� �մϴ�. �������߼� ó������ư�� Ŭ���Ͻø� �����ȣ�� �Է��� �� �ִ� �˾�ȭ���� �����ϴ�.</td></tr>

<tr><td><img src="../img/icon_list.gif" align="absmiddle">*�ǸźҰ�ó��</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰ�� ���� ���ų� ������ �ִ� ��ǰ�� ��� ���ǸźҰ�ó������ Ŭ���ϸ� �ֹ��� �ڵ����� ��ҵǸ�, ������ ��� �ȳ��� �����ϴ�. </td></tr>
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
