<?
$location = "��ǰ�ϰ����� > ���� ��ۺ� ����";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";

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

	'delivery_type' => Clib_Application::request()->get('delivery_type'),
);

// ��ǰ ���
$goodsList = $goodsHelper->getGoodsCollection($params);

// ����¡
$pg = $goodsList->getPaging();

// ��ǰ �˻� ��
$searchForm = Clib_Application::form('admin_goods_search')->setData(Clib_Application::request()->gets('get'));

$_ar_delivery_type = array(
0=>'�⺻���',
1=>'������',
2=>'��ǰ�� ��ۺ�',
3=>'���� ��ۺ�',
4=>'���� ��ۺ�',
5=>'������ ��ۺ�',
);

?>
<link rel="stylesheet" type="text/css" href="./css/css.css">
<script type="text/javascript" src="../js/adm_form.js"></script>
<script type="text/javascript" src="./js/goods_list.js"></script>

<h2 class="title">���� ��ۺ� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=35');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

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
<tr>
	<th>��ǰ�� ��ۺ�</th>
	<td colspan="3" class="noline">
	<label><input type="radio" name="delivery_type" value="" checked>��ü</label>
	<label><input type="radio" name="delivery_type" value="0" <?=$_GET[delivery_type] == '0' ? 'checked' : ''?>>�⺻���</label>
	<label><input type="radio" name="delivery_type" value="1" <?=$_GET[delivery_type] == '1' ? 'checked' : ''?>>������</label>
	<label><input type="radio" name="delivery_type" value="2" <?=$_GET[delivery_type] == '2' ? 'checked' : ''?>>��ǰ�� ��ۺ�</label>
	<label><input type="radio" name="delivery_type" value="4" <?=$_GET[delivery_type] == '4' ? 'checked' : ''?>>���� ��ۺ�</label>
	<label><input type="radio" name="delivery_type" value="5" <?=$_GET[delivery_type] == '5' ? 'checked' : ''?>>������ ��ۺ�</label>
	<label><input type="radio" name="delivery_type" value="3" <?=$_GET[delivery_type] == '3' ? 'checked' : ''?>>���� ��ۺ�</label>
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

<form class="admin-form" name="fmList" method="post" action="./indb_adm_goods_manage_delivery.php" target="ifrmHidden">

<table class="admin-list-table">
<colgroup>
	<col style="width:35px;">
	<col style="width:100px;">
	<col >
	<col style="width:80px;">
	<col style="width:55px;">
	<col style="width:80px;">
	<col style="width:55px;">
	<col style="width:100px;">
</colgroup>
<thead>
<tr>
	<th><a href="javascript:void(0)" onclick="chkBox(document.getElementsByName('chk[]'),'rev')" class="white">����</a></th>
	<th>�ý��ۻ�ǰ�ڵ�</th>
	<th>��ǰ��</th>
	<th>�Ǹűݾ�</th>
	<th>�Ǹ����</th>
	<th>�����</th>
	<th>��������</th>
	<th>��ۺ�</th>
</tr>
</thead>
<tbody>
<?
foreach ($goodsList as $goods) {
?>
<tr class="ac">
	<td class="noline"><input type=checkbox name=chk[] value="<?=$goods[goodsno]?>"></td>
	<td><?=$goods->getReadableId()?> <br />(<?=$goods['goodscd']?>)</td>
	<td class="al">
		<div>
			<a href="../../goods/goods_view.php?goodsno=<?=$goods->getId()?>" target=_blank><?=goodsimg($goods[img_s],40,'style="vertical-align:middle;border:1px solid #e9e9e9;"',1)?></a>
			<a href="adm_goods_form.php?mode=modify&goodsno=<?=$goods->getId()?>"><?=$goods->getGoodsName()?></a>
			<a href="adm_goods_form.php?mode=modify&goodsno=<?=$goods->getId()?>" onclick="nsAdminGoodsList.edit('<?=$goods->getId()?>');return false;"><img src="../img/icon_popup.gif"></a>
		</div>
	</td>
	<td class="price"><?=number_format($goods->getPrice())?></td>
	<td><?=number_format($goods->getStock())?></td>
	<td><?=Core::helper('date')->format($goods['regdt'],'Y-m-d')?></td>
	<td><img src="../img/icn_<?=$goods[open]?>.gif"></td>
	<td>
	<?=$_ar_delivery_type[ $goods['delivery_type'] ]?>
	<?=($goods['delivery_type'] > 1 ? '<br />'.number_format($goods['goods_delivery']).'��' : '')?>
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

<table class="admin-form-table">
<tr>
	<td>��ǰ�� ��ۺ�<br />�ϰ� ����/����</td>
	<td>
		<div style="margin:5px 0"><label><input class="null" type="radio" name="set_delivery_type" value="0">������ ��ǰ�� '�⺻�����å�� ����' ���� �ϰ� ����</label></div>
		<div style="margin:5px 0"><label><input class="null" type="radio" name="set_delivery_type" value="1">������ ��ǰ�� '������' ���� �ϰ� ����</label></div>
		<div style="margin:5px 0;text-decoration: line-through;"><label><input class="null" type="radio" name="set_delivery_type" value="2" disabled>������ ��ǰ�� '��ǰ�� ��ۺ�' ���� �ϰ� ����</label></div>
		<div style="margin:5px 0"><label><input class="null" type="radio" name="set_delivery_type" value="4">������ ��ǰ�� '���� ��ۺ� : <input type="text" class="line" name="set_goods_delivery4" value="" size="8" onFocus="document.getElementsByName('set_delivery_type')[3].checked = true;" onkeydown="onlynumber()">��' ���� �ϰ� ����</label></div>
		<div style="margin:5px 0"><label><input class="null" type="radio" name="set_delivery_type" value="5">������ ��ǰ�� '������ ��ۺ� : <input type="text" class="line" name="set_goods_delivery5" value="" size="8" onFocus="document.getElementsByName('set_delivery_type')[4].checked = true;" onkeydown="onlynumber()">��' ���� �ϰ� ����</label></div>
		<div style="margin:5px 0"><label><input class="null" type="radio" name="set_delivery_type" value="3">������ ��ǰ�� '���� ��ۺ� : <input type="text" class="line" name="set_goods_delivery3" value="" size="8" onFocus="document.getElementsByName('set_delivery_type')[5].checked = true;" onkeydown="onlynumber()">��' ���� �ϰ� ����</label></div>
	</td>
</tr>
</table>

<div class=button_top><input type=image src="../img/btn_modify.gif"></div>

</form>

<ul class="admin-simple-faq">
	<li>��ǰ���� ������ ��ۺ񺰷� ��ǰ�� Ȯ���ϰ� �ϰ������� ��ǰ�� ��ۺ� ����/���� �մϴ�.</li>
	<li>�⺻�����å�� ��ǰ�� ��ۺ� ��å�� <a href="../basic/delivery.php"><font color="#ffffff"><b>[�⺻���� > ���/�ù�� ����]</b></font></a> ���� ���� �Ͻ� �� �ֽ��ϴ�.</li>
	<li>�� ��ǰ�� ��ۺ� ��å�� ����Ͽ� �����ϰ� �����Ͽ� �ּ���.</li>
</ul>

<script type="text/javascript">
// onload events
Event.observe(document, 'dom:loaded', function(){
	nsAdminGoodsList.sortInit('<?=Clib_Application::request()->get('sort')?>');
	nsAdminForm.init($('el-admin-goods-search-form'));
});
</script>

<? include "../_footer.php"; ?>
