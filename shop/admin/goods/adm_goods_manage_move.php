<?

$location = "��ǰ�ϰ����� > ���� �̵�/����/����";
include "../_header.php";
include "../../lib/page.class.php";

// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
if (_CATEGORY_NEW_METHOD_ === false) {
	go('./adm_goods_manage_link.php');
}

$goodsHelper = Clib_Application::getHelperClass('admin_goods');

// �Ķ���� ����
$params = array(
	'page' => Clib_Application::request()->get('page', 1),
	'page_num' => Clib_Application::request()->get('page_num', 10),
	'cate' => Clib_Application::request()->get('cate'),
	'skey' => Clib_Application::request()->get('skey'),
	'sword' => Clib_Application::request()->get('sword'),
	'regdt' => Clib_Application::request()->get('regdt'),
	'goods_price' => Clib_Application::request()->get('goods_price'),
	'open' => Clib_Application::request()->get('open'),
	'soldout' => Clib_Application::request()->get('soldout'),
	'brandno' => Clib_Application::request()->get('brandno'),
	'sort' => Clib_Application::request()->get('sort', 'goodsno desc'),

	'unlink' => Clib_Application::request()->get('unlink'),
	'unbrand' => Clib_Application::request()->get('unbrand'),
);

// ��ǰ ���
$goodsList = $goodsHelper->getGoodsCollection($params);

// ����¡
$pg = $goodsList->getPaging();

// ��ǰ �˻� ��
$searchForm = Clib_Application::form('admin_goods_search')->setData(Clib_Application::request()->gets('get'));

// ���� �˻��� ī�װ�
$searchCategory	= array_pop(array_notnull(Clib_Application::request()->get('cate')));
?>
<link rel="stylesheet" type="text/css" href="./css/css.css">
<script type="text/javascript" src="../js/adm_form.js"></script>
<script type="text/javascript" src="./js/goods_list.js"></script>
<script type="text/javascript"><!--
function chkFormList(mode){
	var fObj = document.forms['fmList'];
	if (inArray(mode, new Array('move','copyGoodses','unlink')) && fObj.category.value == ''){
		if (mode == 'move') alert("�з��̵��� �з��� �˻����� ��츸 �����մϴ�.");
		else if (mode == 'copyGoodses') alert("��ǰ����� �з��� �˻����� ��츸 �����մϴ�.");
		else if (mode == 'unlink') alert("���������� �з��� �˻����� ��츸 �����մϴ�.");
		document.getElementsByName("cate[]")[0].focus();
		return;
	}
	if (isChked(document.getElementsByName('chk[]')) === false){
		if (document.getElementsByName('chk[]').length) document.getElementsByName('chk[]')[0].focus();
		return;
	}
	if (mode == 'delGoodses'){
		tobj = document.getElementsByName('chk[]');
		for(i=0; i< tobj.length; i++){
			if (tobj[i].checked === true && tobj[i].getAttribute('notDel') == 'notInpk'){
				alert("������ũ�� ��ϵ� ��ǰ�� ������ �� �����ϴ�.");
				tobj[i].focus();
				return;
			}
		}
	}

	if (mode == 'link' && document.getElementsByName("sCate[]")[0].value == '')
	{
		alert("������ ��ǰ�� ���� �� �з��� �������ּ���.");
		document.getElementsByName("sCate[]")[0].focus();
		return;
	}
	else if (mode == 'move' && document.getElementsByName("mCate[]")[0].value == '')
	{
		alert("������ ��ǰ�� �̵� �� �з��� �������ּ���.");
		document.getElementsByName("mCate[]")[0].focus();
		return;
	}
	else if (mode == 'copyGoodses' && document.getElementsByName("ssCate[]")[0].value == '')
	{
		alert("������ ��ǰ�� ���� �� �з��� �������ּ���.");
		return false;

	}
	else if (mode == 'linkBrand' && fObj.select('select[name="brandno"]')[0].value == '')
	{
		alert("������ ��ǰ�� ���� �� �귣�带 �������ּ���.");
		fObj.select('select[name="brandno"]')[0].focus();
		return;
	}

	var msg = '';
	if (mode == 'link') msg += '������ ��ǰ�� �ش� �з��� �����Ͻðڽ��ϱ�?';
	else if (mode == 'move') msg += '������ ��ǰ�� �ش� �з��� �̵��Ͻðڽ��ϱ�?';
	else if (mode == 'copyGoodses') msg += '������ ��ǰ�� �ش� �з��� �����Ͻðڽ��ϱ�?';
	else if (mode == 'unlink') msg += '������ ��ǰ�� �з��� �����Ͻðڽ��ϱ�?';
	else if (mode == 'delGoodses') msg += '������ ��ǰ�� ���� �����Ͻðڽ��ϱ�?' + "\n\n" + '[����] ���� �Ŀ��� ������ �ȵǹǷ� �����ϰ� �����Ͻñ� �ٶ��ϴ�.';
	else if (mode == 'linkBrand') msg += '������ ��ǰ�� �ش� �귣�带 �����Ͻðڽ��ϱ�?';
	else if (mode == 'unlinkBrand') msg += '������ ��ǰ�� �귣�带 �����Ͻðڽ��ϱ�?';
	if (!confirm(msg)) return;

	fObj.mode.value = mode;
	fObj.submit();
}
--></script>

<h2 class="title">���� �̵�/����/���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=15');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

<!-- ��ǰ������� : start -->
<form class="admin-form" method="get" name="frmList" id="el-admin-goods-search-form">
<input type="hidden" name="sort" value="<?=Clib_Application::request()->get('sort')?>">

<table class="admin-form-table">
<tr>
	<th>�з�����</th>
	<td colspan="3">
	<script type="text/javascript" src="../../lib/js/categoryBox.js"></script>
	<script type="text/javascript">new categoryBox('cate[]',4,'<?=$searchCategory?>');</script>
	&nbsp;&nbsp;&nbsp;<a href="?unlink=Y"><img src="../img/btn_without_cate.gif" alt="�̿����ǰ����" align=absmiddle></a>
	</td>
</tr>
<tr>
	<th>�˻���</th>
	<td>
		<?=$searchForm->getTag('skey');?>
		<?=$searchForm->getTag('sword');?>
	</td>
	<th>�귣��</th>
	<td>
		<?=$searchForm->getTag('brandno');?>
		&nbsp;&nbsp;&nbsp;<a href="?unbrand=Y"><img src="../img/btn_without_brand.gif" alt="�̿����ǰ����" align=absmiddle></a>
	</td>
</tr>
<tr>
	<th>��ǰ����</th>
	<td colspan="3">
	<input type="text" name="goods_price[]" value="<?=$_GET[goods_price][0]?>" onkeydown="onlynumber()" size="15" class="ar"> �� -
	<input type="text" name="goods_price[]" value="<?=$_GET[goods_price][1]?>" onkeydown="onlynumber()" size="15" class="ar"> ��
	</td>
</tr>
<tr>
	<th>��ǰ�����</th>
	<td colspan=3>
	<input type="text" name="regdt[]" value="<?=$_GET[regdt][0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="ac"> -
	<input type="text" name="regdt[]" value="<?=$_GET[regdt][1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="ac">
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
<tr>
	<th>��ǰ��������</th>
	<td>
		<?php
		foreach ($searchForm->getTag('open') as $label => $tag) {
			echo sprintf('<label>%s%s</label> ',$tag, $label);
		}
		?>
	</td>
	<th>ǰ����ǰ</th>
	<td>
		<?php
		foreach ($searchForm->getTag('soldout') as $label => $tag) {
			echo sprintf('<label>%s%s</label> ',$tag, $label);
		}
		?>
	</td>
</tr>
</table>
<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

<div class="admin-list-toolbar">
	<div class="list-information">
		�˻� <b><?=number_format($pg->recode['total'])?></b>�� / <b><?=number_format($pg->page['now'])?></b> of <?=number_format($pg->page['total'])?> Pages
	</div>

	<div class="list-tool">
	<ul>
		<li><img src="../img/sname_date.gif"><a href="javascript:nsAdminGoodsList.sort('regdt desc')"><img name="sort_regdt_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('regdt')"><img name="sort_regdt" src="../img/list_down_off.gif"></a></li>
		<li class="separater"></li>
		<li><img src="../img/sname_product.gif"><a href="javascript:nsAdminGoodsList.sort('goodsnm desc')"><img name="sort_goodsnm_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('goodsnm')"><img name="sort_goodsnm" src="../img/list_down_off.gif"></a></li>
		<li class="separater"></li>
		<li><img src="../img/sname_price.gif"><a href="javascript:nsAdminGoodsList.sort('goods_price desc')"><img name="sort_goods_price_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('goods_price')"><img name="sort_goods_price" src="../img/list_down_off.gif"></a></li>
		<li class="separater"></li>
		<li><img src="../img/sname_brand.gif"><a href="javascript:nsAdminGoodsList.sort('brandno desc')"><img name="sort_brandno_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('brandno')"><img name="sort_brandno" src="../img/list_down_off.gif"></a></li>
		<li class="separater"></li>
		<li><img src="../img/sname_company.gif"><a href="javascript:nsAdminGoodsList.sort('maker desc')"><img name="sort_maker_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('maker')"><img name="sort_maker" src="../img/list_down_off.gif"></a></li>
		<li class="separater"></li>
		<li>
		<img src="../img/sname_output.gif" align=absmiddle>
		<select name=page_num onchange="this.form.submit()">
		<?
		$r_pagenum = array(10,20,40,60,100);
		foreach ($r_pagenum as $v){
		?>
		<option value="<?=$v?>" <?=($v == Clib_Application::request()->get('page_num')) ? 'selected' : ''?>><?=$v?>�� ���
		<? } ?>
		</select>
		</li>
	</ul>
	</div>
</div>

</form>
<!-- ��ǰ������� : end -->

<form name="fmList" class="admin-form" method="post" onsubmit="return false" target="ifrmHidden" action="./indb_adm_goods_manage_move.php">
<input type=hidden name=mode>
<input type=hidden name=category value="<?=array_pop(array_notnull(Clib_Application::request()->get('cate')))?>">

<table class="admin-list-table">
<colgroup>
	<col style="width:35px;">
	<col style="width:100px;">
	<col >
	<col style="width:55px;">
	<col style="width:55px;">
	<col style="width:55px;">
	<col style="width:70px;">
	<col style="width:80px;">
	<col style="width:55px;">
</colgroup>
<thead>
<tr>
	<th><a href="javascript:void(0)" onclick="chkBox(document.getElementsByName('chk[]'),'rev')" class="white">����</a></th>
	<th>�ý��ۻ�ǰ�ڵ�</th>
	<th>��ǰ��</th>
	<th>�Ǹűݾ�</th>
	<th>������</th>
	<th>�Ǹ����</th>
	<th>�귣��</th>
	<th>�����</th>
	<th>����</th>
</tr>
</thead>
<tbody>
<?
foreach ($goodsList as $goods) {
?>
<tr class="ac">
	<td><input type="checkbox" name="chk[]" value="<?=$goods['goodsno']?>" ></td>
	<td><?=$goods->getReadableId()?> <br />(<?=$goods['goodscd']?>)</td>
	<td class="al">
		<div>
			<a href="../../goods/goods_view.php?goodsno=<?=$goods->getId()?>" target=_blank><?=goodsimg($goods[img_s],40,'style="vertical-align:middle;border:1px solid #e9e9e9;"',1)?></a>
			<a href="adm_goods_form.php?mode=modify&goodsno=<?=$goods->getId()?>"><?=$goods->getGoodsName()?></a>
			<a href="adm_goods_form.php?mode=modify&goodsno=<?=$goods->getId()?>" onclick="nsAdminGoodsList.edit('<?=$goods->getId()?>');return false;"><img src="../img/icon_popup.gif"></a>
		</div>
	</td>
	<td class="price"><?=number_format($goods->getPrice())?></td>
	<td><?=number_format($goods->getReserve())?></td>
	<td><?=number_format($goods->getStock())?></td>
	<td><?=$goods->brand->getBrandName()?></td>
	<td><?=Core::helper('date')->format($goods['regdt'],'Y-m-d')?></td>
	<td><img src="../img/icn_<?=$goods[open]?>.gif"></td>
</tr>
<tr class="info el-admin-goods-list-extra-info">
	<td colspan="9">
	<div class="admin-goods-list-category-wrap">
		<ul class="admin-goods-list-category">
		<? foreach($goods->getCategory() as $linkedCategory) { ?>
			<li><?=currPosition($linkedCategory['category'] , 1);?></li>
		<? } ?>
		</ul>
	</div>
	<div class="clear"></div>
	</td>
</tr>
<? } ?>
</tbody>
</table>

<div class="admin-list-toolbar">
	<div class="left-buttons">
	<a href="javascript:void(0)" onclick="chkBox(document.getElementsByName('chk[]'),'rev')"><img src="../img/btn_allchoice.gif"></a>
	</div>
	<div class="paging"><?=$pg->page['navi']?></div>
</div>

<table class="admin-form-table" style="margin:30px 0;">
<tr>
	<th>���� �˻� �з�</th>
	<td>
	<div style="margin:5px 0; color:#627dce; font-weight:bold;">
<?php
	if (empty($searchCategory) === false) {
		echo currPosition($searchCategory,1);
	}
	else {
		echo "�з��� �����ؼ� �˻��� �ּ���.";
	}
?>
	</div>
	</td>
</tr>
<tr>
	<th>�з�����</th>
	<td>
	<div style="margin:5px 0">
	������ ��ǰ�� <script type="text/javascript">new categoryBox('sCate[]',4,'','','fmList');</script> ����
	<a href="javascript:chkFormList('link')"><img src="../img/btn_cate_connect.gif" align="absmiddle" alt="����"></a>
	</div>
	<div style="margin:5px 0" class="noline">
	<input type="checkbox" name="isToday" value="Y" <?=$checked[isToday]['Y']?>>�ش� ��ǰ�� ������� ���� ��Ͻð����� �����մϴ�.
	</div>
	</td>
</tr>
<tr>
	<th>�з��̵�</th>
	<td>
	<div style="margin:5px 0">
	������ ��ǰ��
	<script type="text/javascript">new categoryBox('mCate[]',4,'','','fmList');</script> ����
	<a href="javascript:chkFormList('move')"><img src="../img/btn_cate_move.gif" align="absmiddle" alt="�̵�"></a>
	</div>
	<div style="margin:5px 0" class="noline">
	<font class="extext">�� �з��̵� �� �ش� ��ǰ�� ����� �з��� ��� �����Ǹ�, ���Ӱ� �̵��Ǵ� �з��� ����˴ϴ�.</font>
	</div>
	</td>
</tr>
<tr height=35>
	<th>�з�����</th>
	<td>
	<div style="margin:5px 0">
	������ ��ǰ�� ��� �з���
	<a href="javascript:chkFormList('unlink')"><img src="../img/btn_cate_unconnect.gif" align="absmiddle" alt="����"></a>
	</div>
	<div style="margin:5px 0" class="noline">
	<font class="extext">�� �ش� ��ǰ�� ����� �з��� ��� �����ǹǷ� ��ǰ�з����������� �ش� ��ǰ�� ��ȸ���� �ʽ��ϴ�.</font>
	</div>
	</td>
</tr>
<tr>
	<th>�귣�忬��</th>
	<td>
	<div style="margin:5px 0">
	������ ��ǰ�� <?=$searchForm->getTag('brandno');?> ����
	<a href="javascript:chkFormList('linkBrand')"><img src="../img/btn_cate_connect.gif" align="absmiddle" alt="����"></a>
	<a href="javascript:chkFormList('unlinkBrand')"><img src="../img/btn_cate_unconnect.gif" align="absmiddle" alt="����"></a>
	</div>
	</td>
</tr>
<tr height=35>
	<th>��ǰ����</th>
	<td>
	<div style="margin:5px 0">
	������ ��ǰ�� <a href="javascript:chkFormList('delGoodses')"><img src="../img/btn_cate_del.gif" align="absmiddle" alt="����"></a>
	</div>
	<div style="margin:5px 0" class="noline">
	<font class="extext">�� �����ϰ� �����ϼ���. ��ưŬ���� ������ ��ǰ���� �����˴ϴ�. �����Ǹ� �������� �ʽ��ϴ�.</font>
	</div>
	</td>
</tr>
</table>

<table class="admin-form-table" style="margin:30px 0;">
<tr>
	<th>����</th>
	<td>
	<div style="margin:5px 0">
	������ ��ǰ�� <script type="text/javascript">new categoryBox('ssCate[]',4,'','','fmList');</script> ����
	<a href="javascript:chkFormList('copyGoodses')"><img src="../img/btn_cate_copy.gif" align="absmiddle" alt="����"></a>
	</div>
	<div style="margin:5px 0" class="noline">
	<font class="extext">�� ������ ��쿡�� ��ǰ�� ������� ������ ����ð����� ����˴ϴ�</font>
	</div>
	</td>
</tr>
</table>
</form>

<ul class="admin-simple-faq">
	<li>�з����� : ��ǰ�� �з�(ī�װ�)�� �����ϴ� ����Դϴ�.(���ߺз��������)</li>
	<li>�з��̵� : ���� ����� �з����� �ٸ� �з��� �̵��ϴ� ����Դϴ�.</li>
	<li>�з����� : ����� �з��� �����ϴ� ����Դϴ�.</li>
	<li>��ǰ���� : �ٸ� �з��� �Ȱ��� ��ǰ�� �ϳ� �� ����(����)�ϴ� ����Դϴ�.</li>
	<li>��ǰ���� : ��ǰ�� �����ϴ� ������� ���� �Ŀ��� ������ �ȵǹǷ� �����ϰ� �����Ͻñ� �ٶ��ϴ�.</li>
	<li>[����] ��ǰ���� ��� ��ǰ����/��ǰ�ı�� ������� �ʽ��ϴ�.</li>
</ul>

<script type="text/javascript">
// onload events
Event.observe(document, 'dom:loaded', function(){
	nsAdminGoodsList.sortInit('<?=Clib_Application::request()->get('sort')?>');
	nsAdminForm.init($('el-admin-goods-search-form'));
});
</script>

<? include "../_footer.php"; ?>