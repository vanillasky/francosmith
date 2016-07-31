<?
include "../_header.popup.php";
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
	'stock_type' => Clib_Application::request()->get('stock_type'),
	'stock_amount' => Clib_Application::request()->get('stock_amount'),
	'open' => Clib_Application::request()->get('open'),
	'soldout' => Clib_Application::request()->get('soldout'),
	'brandno' => Clib_Application::request()->get('brandno'),
	'origin' => Clib_Application::request()->get('origin'),
	'sort' => Clib_Application::request()->get('sort', 'goodsno desc'),
);

// ��ǰ ���
$goodsList = $goodsHelper->getGoodsCollection($params);

// ����¡
$pg = $goodsList->getPaging();

// ��ǰ �˻� ��
$searchForm = Clib_Application::form('admin_goods_search')->setData(Clib_Application::request()->gets('get'));
?>

<script language="javascript">
function toggleGoods(goodsno) {
	brd = document.getElementById("selectBoard_" + goodsno);
	ifr = document.getElementById("selectIframe_" + goodsno);

	if(brd.style.display == "none") {
		brd.style.display = "";
		ifr.src = "../order/self_order_goods_view.php?goodsno=" + goodsno + "&memID=" + $('memID').value;
	}
	else {
		brd.style.display = "none";
	}
}
</script>

<script type="text/javascript" src="../js/adm_form.js"></script>
<script type="text/javascript" src="../godo.loading.indicator.js"></script>
<form class="admin-form" method="get" name="frmList" id="el-admin-goods-search-form">
	<input type="hidden" name="sort" value="<?=Clib_Application::request()->get('sort')?>">
	<input type="hidden" name="memID" id="memID" value="<?=Clib_Application::request()->get('memID')?>">

	<div class="title title_top">�����ֹ���� ��ǰ�˻� <span>���� �ֹ��ϰ��� �ϴ� ��ǰ�� �˻��Ͽ� ����մϴ�.</span></div>

	<table class="admin-form-table">
	<tr>
		<th>�з�����</th>
		<td colspan=3><script type="text/javascript">new categoryBox('cate[]',4,'<?=array_pop(array_notnull(Clib_Application::request()->get('cate')))?>');</script></td>
	</tr>
	<tr>
		<th>�˻���</th>
		<td>
			<?=$searchForm->getTag('skey');?>
			<?=$searchForm->getTag('sword');?>
		</td>
		<th>������</th>
		<td>
			<?=$searchForm->getTag('origin');?>
		</td>
	</tr>
	<tr>
		<th>��ǰ����</th>
		<td>
			<?
			$goods_price = (array)Clib_Application::request()->get('goods_price');
			?>
			<input type=text name=goods_price[] value="<?=$goods_price[0]?>" onkeydown="onlynumber()" size="15" class="ar"> �� -
			<input type=text name=goods_price[] value="<?=$goods_price[1]?>" onkeydown="onlynumber()" size="15" class="ar"> ��
		</td>
		<th>�귣��</th>
		<td>
			<?=$searchForm->getTag('brandno');?>
		</td>
	</tr>
	<tr>
		<th>��ǰ������</th>
		<td colspan=3>
			<?php
			foreach ($searchForm->getTag('stock_type') as $label => $tag) {
				echo sprintf('<label class="noline">%s%s</label> ',$tag, $label);
			}

			$stock_amount = (array)Clib_Application::request()->get('stock_amount');
			?>
			<div>
				<input type=text name=stock_amount[] value="<?=$stock_amount[0]?>" onkeydown="onlynumber()" size="15" class="ar"> �� -
				<input type=text name=stock_amount[] value="<?=$stock_amount[1]?>" onkeydown="onlynumber()" size="15" class="ar"> ��
			</div>

			<p class="help">
				<font color="blue">��ǰ���:</font> ��ǰ�� ǰ��(���ݿɼ�)�� ��� ������ ������ ���մϴ�. �ֹ��� �������(�������)�� ��ǰ�� ��ȸ����� �˴ϴ�. <br/>
				<font color="blue">ǰ�����:</font> ǰ��(���ݿɼ�) ���� ��� ������ ���մϴ�. �ֹ��� �������(�������)�� ��ǰ�� ��ȸ����� �˴ϴ�.
			</p>
		</td>
	</tr>
	<tr>
		<th>��ǰ�����</th>
		<td colspan=3>
			<?
			$regdt = (array)Clib_Application::request()->get('regdt');
			?>
			<input type="text" name="regdt[]" value="<?=$regdt[0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="ac"> -
			<input type="text" name="regdt[]" value="<?=$regdt[1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="ac">
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
				echo sprintf('<label class="noline">%s%s</label> ',$tag, $label);
			}
			?>
		</td>
		<th>ǰ����ǰ</th>
		<td>
			<?php
			foreach ($searchForm->getTag('soldout') as $label => $tag) {
				echo sprintf('<label class="noline">%s%s</label> ',$tag, $label);
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
			<li><img src="../img/sname_price.gif"><a href="javascript:nsAdminGoodsList.sort('price desc')"><img name="sort_price_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('price')"><img name="sort_price" src="../img/list_down_off.gif"></a></li>
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

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="12"></td></tr>
<tr class="rndbg">
	<th width="60">��ȣ</th>
	<th></th>
	<th width="10"></th>
	<th>��ǰ��</th>
	<th>�����</th>
	<th>����</th>
	<th>���</th>
	<th>����</th>
	<th>��ǰ����</th>
</tr>
<tr><td class="rnd" colspan="12"></td></tr>
<col width="40" span="2" align="center">
<? foreach($goodsList as $goods) {
	$icon = $goods->getIconHtml('../');
?>
<tr><td height="4" colspan="12"></td></tr>
<tr height="25">
	<td><font class="ver8" color="616161"><?=$pg->idx--?></td>
	<td style="border:1px #e9e9e9 solid;"><a href="../../goods/goods_view.php?goodsno=<?=$goods->getId()?>" target="_blank"><?=goodsimg($goods['img_s'], 40, '', 1)?></a></td>
	<td></td>
	<td>
	<a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$goods->getId()?>',850,600)"><font color="303030"><?=$goods['goodsnm']?></font></a>
	<? if ($icon){ ?><div style="padding-top:3px"><?=$icon?></div><? } ?>
	<? if ($goods->getSoldout()){ ?><div style="padding-top:3px"><img src="../../data/skin/<?=$cfg['tplSkin']?>/img/icon/good_icon_soldout.gif"></div><? } ?>
	</td>
	<td align="center"><font class="ver81" color="444444"><?=substr($goods['regdt'], 0, 10)?></td>
	<td align="center">
	<font color="4B4B4B"><font class="ver81" color="444444"><b><?=number_format($goods->getPrice())?></b></font>
	<div style="padding-top:2px"></div>
	<img src="../img/good_icon_point.gif" align="absmiddle"><font class="ver8"><?=number_format($goods->getReserve())?></font>
	</td>
	<td align="center"><font class="ver81" color="444444"><?=number_format($goods->getStock())?></td>
	<td align="center"><img src="../img/icn_<?=$goods['open']?>.gif"></td>
	<td align="center"><a href="javascript:;" onclick="toggleGoods('<?=$goods->getId()?>')"><img src="../img/btn_openmarket_cateselect.gif"></a></td>
</tr>
<tr><td height="4"></td></tr>
<tr><td colspan="12" class="rndline"></td></tr>
<tr id="selectBoard_<?=$goods->getId()?>" style="display:none;">
	<td colspan="12" style="padding:10px; border-bottom:#DCD8D6 solid 1px;">
		<iframe src="about:blank" id="selectIframe_<?=$goods->getId()?>" frameborder="0" style="width:100%; height:100%;"></iframe>
	</td>
</tr>
<? } ?>
</table>
<div align="center" class="pageNavi"><font class="ver8"><?=$pg->page['navi']?></font></div>

<script>
table_design_load();
</script>