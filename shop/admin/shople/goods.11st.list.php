<?
$location = "���� > ��ǰ����Ʈ";

$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
include "../_header.php";
require_once ('./_inc/config.inc.php');

// ���� �Ǹ�����
$shople = Core::loader('shople');
$shopleCfg = $shople->cfg['shople'];


// ī�װ�
	// depth 1�� ������
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

<div class="title title_top">��ǰ����Ʈ <span>11������ ��ϵ� ��ǰ�� ��ȸ�ϰ� ������ �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shople&no=5')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form name="frmListOption" id="frmListOption" target="ifrmHidden" method="post">
<input type="hidden" name="page" value="">
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>�з�����</td>
		<td>
		<div id="stepCate">
			<ul>
				<select id="el-shople-category-1" class="el-shople-category" name="category1">
				<option value="null">��з� ����</option>
				<? foreach ($category as $cate) { ?>
				<option value="<?=$cate['dispno']?>"><?=$cate['name']?></option>
				<? } ?>
				</select>
			</ul>
			<ul class="separator">��</ul>
			<ul>
				<select id="el-shople-category-2" class="el-shople-category" name="category2">
				<option value="null">�ߺз� ����</option>

				</select>
			</ul>
			<ul class="separator">��</ul>
			<ul>
				<select id="el-shople-category-3" class="el-shople-category" name="category3">
				<option value="null">�Һз� ����</option>

				</select>
			</ul>
			<ul class="separator">��</ul>
			<ul>
				<select id="el-shople-category-4" class="el-shople-category" name="category4">
				<option value="null">���з� ����</option>

				</select>
			</ul>
		</div>

		</td>
	</tr>
	<tr>
		<td>�˻���</td>
		<td>
		<select name="skey">
		<option value="prdNm" <?=($_GET['skey']=='prdNm') ? 'selected' : ''?>>��ǰ��</option>
		<option value="prdNo" <?=($_GET['skey']=='prdNo') ? 'selected' : ''?>>��ǰ��ȣ</option>
		</select>
		<input type="text" name="sword" class="lline" value="<?=$_GET['sword']?>">
		</td>
	</tr>
	<tr>
		<td>�ǸŻ���</td>
		<td class="noline">

		<label><input type="radio" name="selStatCd" value=""	<?=($_GET['selStatCd']=='') ? 'checked' : ''?>>��ü</label>
		<label><input type="radio" name="selStatCd" value="103" <?=($_GET['selStatCd']=='103') ? 'checked' : ''?>>�Ǹ���</label>
		<label><input type="radio" name="selStatCd" value="104" <?=($_GET['selStatCd']=='104') ? 'checked' : ''?>>ǰ��</label>
		<label><input type="radio" name="selStatCd" value="105" <?=($_GET['selStatCd']=='105') ? 'checked' : ''?>>�Ǹ�����</label>
		<label><input type="radio" name="selStatCd" value="107" <?=($_GET['selStatCd']=='107') ? 'checked' : ''?>>�Ǹ�����</label>

		</td>
	</tr>
	<tr>
		<td>�Ⱓ</td>
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
	<a href="javascript:nsShople.goods.stopdisplay();"><img src="../img/btn_product_cancel.gif" alt="�Ǹ����� ����"></a>
	<a href="javascript:nsShople.goods.startdisplay();"><img src="../img/btn_product_ok.gif" alt="�Ǹ����� ����"></a>
	<a href="javascript:fnSaveChanged();"><img src="../img/btn_product_save.gif" alt="������ǰ ����"></a>
</div>
</form>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">11������ ��ϵǾ� �Ǹŵǰ� �ִ� ��ǰ ����Ʈ ���� �Դϴ�.</td></tr>

<tr><td><img src="../img/icon_list.gif" align="absmiddle">1) �Ǹ���������</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">&nbsp;&nbsp;��ǰ�� ���������� �����Ͽ� �Ǹ����� ���·� ������ �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">&nbsp;&nbsp;�Ǹ������� ������ ��ǰ�� 11������ ������� �ʽ��ϴ�.</td></tr>

<tr><td><img src="../img/icon_list.gif" align="absmiddle">2) �Ǹ���������</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">&nbsp;&nbsp;�Ǹ������� ������ ��ǰ�� �ٽ� �Ǹ� ���·� �����ϴ� ����Դϴ�.</td></tr>

<tr><td><img src="../img/icon_list.gif" align="absmiddle">3) ������ǰ ����</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">&nbsp;&nbsp;��ǰ ����Ʈ���� �ٷ� ��ǰ ������ ������ �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">&nbsp;&nbsp;��ǰ���� ������, ���� ������ Ŭ���ϸ� �ٷ� ������ �����ϸ�, ���� �� ��������ǰ ���塯��ư�� Ŭ���ϸ� ������ ������</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">&nbsp;&nbsp;�ݿ��Ǿ� 11������ ���� �˴ϴ�.</td></tr>


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
