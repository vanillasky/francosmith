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
	$_GET['regdt'] = isset($_GET['regdt']) ? $_GET['regdt'] : array(date('Ymd'),date('Ymd'));
?>


<div class="title title_top">��ǰ Q&A ���� <span>��ǰ�� ���� ���� ������ �亯�� ������ �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shople&no=8c')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

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
			<select name="qnacd">
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
			<select name="stats">
				<option value="">��ü</option>
				<option value="Y">�亯�Ϸ�</option>
				<option value="N">�̴亯</option>
			</select>
		</td>
	</tr>
	</table>
	<div class="button_top"><input type="image" src="../img/btn_search2.gif"></div>


</form>


<div class="title title_top">��ǰ Q&A ���</div>
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
			<th>�����Ͻ�</th>
			<td>#{createDt}</td>
			<th>ó���Ͻ�</th>
			<td>#{answerDt}</td>
		</tr>
		<tr>
			<th>��������</th>
			<td colspan="3">#{qnaDtlsCdNm}</td>
		</tr>
		<tr>
			<th>����</th>
			<td colspan="3">#{brdInfoSbjct}</td>
		</tr>
		<tr>
			<th>��������</th>
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
			<th>�亯����</th>
			<td>
				<textarea name="answerCont" style="">#{answerCont}</textarea>
				<div class="buttons" style="display:#{buttonDisplay}">
				<a href="javascript:nsShople.qna.answer(#{brdInfoNo})"><img src="../img/btn_answer_ok.gif" alt="�亯���"></a>
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
<tr><td><img src="../img/icon_list.gif" align="absmiddle">11���� ��ǰ���������� ��ϵ� ������ ������ ���� �� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��������� ���� ���ų����� Ȯ���� �� ������, ������ ���� �亯�� �ۼ��� �� �ֽ��ϴ�.</td></tr>
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
