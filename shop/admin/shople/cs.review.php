<?
$location = "���� > ��ǰ Q&A ����";

$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
include "../_header.php";
require_once ('./_inc/config.inc.php');

// ���� �Ǹ�����
$shople = Core::loader('shople');
$shopleCfg = $shople->cfg['shople'];

// get �Ķ����
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

<div class="title title_top">�ı�/���� ���� <span>��ǰ�� ���� ���� �ı�/���並 Ȯ���� �� �ֽ��ϴ�.</span></div>

<form name="frmListOption" id="frmListOption" target="ifrmHidden">
<input type="hidden" name="page" value="">
	<table class="tb">
	<col class="cellC" width="150"><col class="cellL" width="350">
	<col class="cellC" width="150"><col class="cellL">
	<tr>
		<td>�ۼ���</td>
		<td>
			<select name="skey">
			<option value="prdNm" <?=($_GET['skey']=='prdNm') ? 'selected' : ''?>>��ǰ��</option>
			<option value="prdNo" <?=($_GET['skey']=='prdNo') ? 'selected' : ''?>>��ǰ��ȣ</option>
			</select>

			<input type="text" name="sword" class="lline" value="<?=$_GET['sword']?>">
		</td>

		<td>��������</td>
		<td>
			<select>
				<option>��ü</option>
				<option value="">��ü</option>
				<option value="01">��ǰ</option>
				<option value="02">���</option>
				<option value="03">��ǰ/ȯ��/���</option>
				<option value="04">��ȯ/����</option>
				<option value="05">��Ÿ</option>
			</select>
		</td>

	</tr>
	<tr>
		<td>�Ⱓ</td>
		<td>
			<input type=text name=regdt[] value="<?=$_GET[regdt][0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline" style="width:70px"> -
			<input type=text name=regdt[] value="<?=$_GET[regdt][1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline" style="width:70px">
			<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>

		</td>

		<td>ó������</td>
		<td>
			<select>
				<option>��ü</option>
				<option value="">��ü</option>
				<option value="Y">�亯�Ϸ�</option>
				<option value="N">�̴亯</option>
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
			<option value='<?=$v?>' <?=($_GET['page_num'] == $v ? 'selected' : '' )?>><?=$v?>�� ���</option>
			<? } ?>
			</select>
			</td>
		</tr>
		</table>

		</td>
	</tr>
	</table>
</form>


<div class="title title_top">��ǰ����/ �����ı� ���</div>
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
<tr><td><img src="../img/icon_list.gif" align="absmiddle">11���� ��ǰ���������� ��ϵ� ������ ��ǰ�ı⸦ Ȯ�� �� �� �ֽ��ϴ�.</td></tr>
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
