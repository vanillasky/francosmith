<?
$location = "���̹� ���� > ���̹� ���� ��ǰ ����";
include "../_header.php";
include "../../lib/page.class.php";
include "../../lib/naverPartner.class.php";

$goodsHelper = Clib_Application::getHelperClass('admin_goods');
$naver = new naverPartner();

// �Ķ���� ����
$params = array(
	'page' => Clib_Application::request()->get('page', 1),
	'page_num' => Clib_Application::request()->get('page_num', 10),
	'cate' => Clib_Application::request()->get('cate'),
	'skey' => Clib_Application::request()->get('skey'),
	'sword' => Clib_Application::request()->get('sword'),
	'open' => Clib_Application::request()->get('open'),
	'soldout' => Clib_Application::request()->get('soldout'),
	'brandno' => Clib_Application::request()->get('brandno'),
	'sort' => Clib_Application::request()->get('sort', 'goodsno desc'),
	'naver_shopping_yn' => Clib_Application::request()->get('naver_shopping_yn'),
);

// ��ǰ ���
$goodsList = $goodsHelper->getGoodsCollection($params);

// ����¡
$pg = $goodsList->getPaging();

// ��ǰ �˻� ��
$searchForm = Clib_Application::form('admin_goods_search')->setData(Clib_Application::request()->gets('get'));
?>
<link rel="stylesheet" type="text/css" href="../goods/css/css.css">
<script type="text/javascript" src="../js/adm_form.js"></script>
<script type="text/javascript" src="../goods/js/goods_list.js"></script>
<script type="text/javascript" src="../godo.loading.indicator.js"></script>
<? if ($naver->migrationCheck() == false) { ?>
<div style="width:100%; height:1350px; filter:alpha(opacity=80); opacity:0.95; background:#44515b; position:absolute; text-align:center; display:table;">
<span style="display:table-cell; vertical-align:middle; color:white; font-size:12pt;"><b>���̹� ���� EP���� ���� ����� �����Ͽ����ϴ�.<br>���̱׷��̼��� �Ͻø� ������ ���� ���� ���������� ���̹� ���� EP������ ������ �� �ֽ��ϴ�.<br>�Ʒ� ���̱׷��̼� ��ư�� Ŭ���Ͻþ� ���̱׷��̼��� �������ֽñ� �ٶ��ϴ�.<br>�� ���̱׷��̼� �۾����� ���� �ð��� �ҿ�˴ϴ�.<br>�� ���̱׷��̼� �Ŀ��� ���̹� ���� ��ǰ ���� �޴����� �ش� ����� ����Ͻ� �� �ֽ��ϴ�.<br></b>
<a href="javascript:migration();"><img style="margin-top:20px;" src="../img/btn_naver_shopping_migration.png"></a>
</span>
</div>
<?}?>
<div class="title title_top">���̹� ���� ���� ��ǰ ��Ȳ <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=35')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table border=1 bordercolor=#dce1e1 style="border-collapse:collapse; width:100%">
	<tr>
		<td width=800 height=100 align=center bgcolor=#E6FFFF>
			<a href="javascript:window.open('naver_shopping_goods_status.php','','width=800,height=800');void(0);"><img src="../img/btn_naver_shopping_goods_status.png"></a>
		</td>
	</tr>
</table>

<div class="title title_top">���̹� ���� ���� ��ǰ ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=35')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class="admin-form-table" border=4 bordercolor=#dce1e1 style="border-collapse:collapse;">
<tr><td style="padding:7 0 10 10">
<div style="padding-top:5"><b><font color="#bf0000">*�ʵ�*</div>
<div style="padding-top:7"><font class=g9 color=666666>���̹� ���� ���ؿ� ����, ������ ������ ��ǰ�� �ִ� 50���� �Դϴ�.</font></div>
<div style="padding-top:5"><font class=g9 color=666666>����, �Ʒ��� ������ ���� <b>���̹� ���� ��ǰ DB�� 499,000�� ���Ϸ� �����ϴ� ����� �����ϰ� �ֽ��ϴ�.</b></font></div>
<div style="padding-top:5"><font class=g9 color=666666>(50������ �ʰ��ϸ� ���̹� ���� ���񽺰� �����Ǿ� ������ ����� ���Ͽ� 499,000�� ���� ����Ͻ� �� �ֽ��ϴ�.)</font></div>
<div style="padding-top:5"><font size=2 color=#627dce><b><br>�� �� ��ǰ���� 499,000���� ���� �ʴ� ��쿡�� ���� ���� ���̵� ���������� ���̹� ������ �̿��Ͻ� �� �ֽ��ϴ�.</b></font></div>
</td></tr>
</table>

<div style="margin-top:5px"></div>

<!-- ��ǰ������� : start -->
<form class="admin-form" method="get" name="frmList" id="el-admin-goods-search-form">
<input type="hidden" name="sort" value="<?=Clib_Application::request()->get('sort')?>">

<table class="admin-form-table">
<tr>
	<th>�з�����</th>
	<td colspan="3">
	<script type="text/javascript" src="../../lib/js/categoryBox.js"></script>
	<script type="text/javascript">new categoryBox('cate[]',4,'<?=array_pop(array_notnull(Clib_Application::request()->get('cate')))?>');</script>
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
<tr>
	<th>���̹����� ���� ����</th>
	<td colspan=3>
		<?php
		foreach ($searchForm->getTag('naver_shopping_yn') as $label => $tag) {
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

<form name="fmList" class="admin-form">
<input type=hidden name=mode value="naverShoppingGoods">
<input type=hidden name=param[page] value="0">
<input type=hidden name=param[page_num] value="3000">
<input type=hidden name=param[cate][0] value="<?=$params['cate'][0]?>">
<input type=hidden name=param[cate][1] value="<?=$params['cate'][1]?>">
<input type=hidden name=param[cate][2] value="<?=$params['cate'][2]?>">
<input type=hidden name=param[cate][3] value="<?=$params['cate'][3]?>">
<input type=hidden name=param[skey] value="<?=$params['skey']?>">
<input type=hidden name=param[sword] value="<?=$params['sword']?>">
<input type=hidden name=param[open] value="<?=$params['open']?>">
<input type=hidden name=param[soldout] value="<?=$params['soldout']?>">
<input type=hidden name=param[brandno] value="<?=$params['brandno']?>">
<input type=hidden name=param[sort] value="<?=$params['sort']?>">
<input type=hidden name=param[naver_shopping_yn] value="<?=$params['naver_shopping_yn']?>">

<table class="admin-list-table">
<colgroup>
	<col style="width:35px;">
	<col style="width:100px;">
	<col>
	<col style="width:100px;">
	<col style="width:100px;">
	<col style="width:100px;">
</colgroup>
<thead>
<tr>
	<th><a href="javascript:void(0)" onclick="chkBox(document.getElementsByName('chk[]'),'rev')" class="white">����</a></th>
	<th>��ȣ</th>
	<th>��ǰ��</th>
	<th>���̹� ����<br>���⿩��</th>
	<th>��ǰ����</th>
	<th>ǰ������</th>
</tr>
</thead>
<tbody>
<?
foreach ($goodsList as $goods) {
?>
<tr class="ac">
	<td><input type="checkbox" name="chk[]" value="<?=$goods['goodsno']?>" ></td>
	<td><?=$pg->idx--?></td>
	<td class="al">
		<div>
			<a href="../../goods/goods_view.php?goodsno=<?=$goods->getId()?>" target=_blank><?=goodsimg($goods[img_s],40,'style="vertical-align:middle;border:1px solid #e9e9e9;"',1)?></a>
			<a href="../goods/adm_goods_form.php?mode=modify&goodsno=<?=$goods->getId()?>"><?=$goods->getGoodsName()?></a>
		</div>
	</td>
	<td><img src="../img/icn_<?=$goods['naver_shopping_yn']=='Y'?1:0?>.gif"></td>
	<td><img src="../img/icn_<?=$goods['open']?>.gif"></td>
	<td><img src="../img/icn_<?=$goods['runout']==1||($goods['usestock']=='o'&&$goods['totstock']<1)?1:0?>.gif"></td>
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
	<th>���̹� ���� ���� ����</th>
	<td>
		<div style="margin:5px 0"><label><input type="radio" name="naver_shopping_yn" checked value="0">������ ��ǰ�� 
		<select name="checked">
			<option value="Y"> = ������ = </option>
			<option value="N"> = ������� =</option>
		</select> ���� ����</label></div>
		<div style="margin:5px 0"><label><input type="radio" name="naver_shopping_yn" value="1">�˻��� ��ǰ�� 
		<select name="searched">
			<option value="Y"> = ������ = </option>
			<option value="N"> = ������� = </option>
		</select> ���� ����</label></div>
	</td>
</tr>
</table>

<div class=button_top><a href="javascript:settingSave();"><img src="../img/btn_save.gif"></a></div>
</form>

<script type="text/javascript">
// onload events
Event.observe(document, 'dom:loaded', function(){
	nsAdminGoodsList.sortInit('<?=Clib_Application::request()->get('sort')?>');
	nsAdminForm.init($('el-admin-goods-search-form'));
});

function settingSave() {
	var naver_shopping_yn =  document.getElementsByName("naver_shopping_yn");
	for (var i=0; i<naver_shopping_yn.length; i++) {
		if (naver_shopping_yn[i].checked == true && naver_shopping_yn[i].value == '0') {
			if (isChked(document.getElementsByName('chk[]')) == false) {
				return;
			}
		}
	}

	// �ε� ó��
	nsGodoLoadingIndicator.init({});
	nsGodoLoadingIndicator.show();

	ajaxSave();
}

function ajaxSave() {
	var data = document.fmList.serialize(true);
	var ajax = new Ajax.Request('indb.php',
	{
		method: 'post',
		parameters: data,
		onComplete: function (response)
		{
			var res = response.responseText;
			if (res == 'end'){
				nsGodoLoadingIndicator.hide();	// �ε���
				alert('���������� ����Ǿ����ϴ�.');
				window.location.reload(true);
			}
			else if (res == 'ok') {
				ajaxSave();
			}
			else {
				nsGodoLoadingIndicator.hide();	// �ε���
				alert("������ �����Ͽ����ϴ�.\n�����Ϳ� �����Ͽ� �ּ���.");
			}
		},
		onFailure : function() {
			nsGodoLoadingIndicator.hide();	// �ε���
			alert("������ �����Ͽ����ϴ�.\n�����Ϳ� �����Ͽ� �ּ���.");
		}
	});
}

function migration() {
	if (confirm('���̱׷��̼��� �۾� �ð��� �ټ� �ҿ�˴ϴ�. ����Ͻðڽ��ϱ�?')) {
		popupLayer('naver_shopping_migration.php',1000,800);
	}
	else {
		return false;
	}
}
</script>

<? include "../_footer.php"; ?>