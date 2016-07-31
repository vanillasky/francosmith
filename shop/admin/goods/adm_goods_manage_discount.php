<?
$location = "��ǰ�ϰ����� > ���� ��ǰ���� ����";
include "../_header.php";
include "../../lib/page.class.php";
@include "../../conf/design_main.$cfg[tplSkin].php";

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

	'indicate' => Clib_Application::request()->get('indicate'),
	'smain' => Clib_Application::request()->get('smain'),
	'sevent' => Clib_Application::request()->get('sevent'),

	'discount' => Clib_Application::request()->get('discount'),
	'discount_range' => Clib_Application::request()->get('discount_range'),

	'sort' => Clib_Application::request()->get('sort', 'goodsno desc'),
);

// ��ǰ ���
$goodsList = $goodsHelper->getGoodsCollection($params);

// ����¡
$pg = $goodsList->getPaging();

// ��ǰ �˻� ��
$searchForm = Clib_Application::form('admin_goods_search')->setData(Clib_Application::request()->gets('get'));

// ���� ��
$discountForm = Clib_Application::form('admin_goods_register');

// ȸ�� �׷�
$memberGroups = Clib_Application::getCollectionClass('member_group');
$memberGroups->load();
?>
<link rel="stylesheet" type="text/css" href="./css/css.css">
<script type="text/javascript" src="../js/adm_form.js"></script>
<script type="text/javascript" src="./js/goods_list.js"></script>
<script type="text/javascript" src="./js/goods_register.js"></script>
<script type="text/javascript"><!--
function __search(type) {

	var f = document.frmList;

	if (type) {
		f.indicate.value = type;
	}
	else {
		f.indicate.value = '';
	}

	f.submit();

}

function chkFormList(fObj){
	if (chkForm(fObj) === false) return false;
	return true;
}

/*** �̺�Ʈ��� ��û ***/
function getEventList(sobj, selValue)
{
	if (sobj.options[sobj.selectedIndex].getAttribute("call") == null) return;
	function setcallopt(idx, text, value, defaultSelected, selected, call){
		if (idx == 0) for (i = sobj.options.length; i > 0; i--) sobj.remove(i);
		sobj.options[idx] = new Option(text, value, defaultSelected, selected);
		if (call != null) sobj.options[idx].setAttribute('call', call);
	}
	var ajax = new Ajax.Request( "../goods/indb.php",
	{
		method: "post",
		parameters: "mode=getEvent&page=" + sobj.options[sobj.selectedIndex].getAttribute("call") + "&selValue=" + (selValue != null ? selValue : ''),
		onLoading: function (){ setcallopt(0, '== �� �� �� ... ==', ''); },
		onComplete: function ()
		{
			var req = ajax.transport;
			if ( req.status == 200 )
			{
				var jsonData = eval( '(' + req.responseText + ')' );
				var lists = jsonData.lists;
				var page = jsonData.page;
				var idx = 0;
				if (page.prev != null) setcallopt(idx++, '�� ó����Ϻ���', '', false, false, '1');
				if (page.prev != null) setcallopt(idx++, '�� ������Ϻ���', '', false, false, page.prev);
				if (lists.length == 0) setcallopt(idx++, '== �̺�Ʈ�� �����ϴ� ==', '', false, false);
				for (i = 0; i < lists.length; i++){
					if (i == 0 || (selValue != null && selValue == lists[i].sno)) selected = true; else selected = false;
					setcallopt(idx++, '[' + lists[i].sdate + ' ~ ' + lists[i].edate + '] ' + lists[i].subject, lists[i].sno, false, selected);
				}
				if (page.next != null) setcallopt(idx++, '�� ������Ϻ���', '', false, false, page.next);
				sobj.form['seventpage'].value = page.now;
			}
			else {
				setcallopt(0, '�� �ε� �����ϱ�', '', false, false, '1');
				setcallopt(1, '[�ε�����] ��ε��ϼ���.', '', true, true);
			}
		}
	} );
}
--></script>

<h2 class="title">���� ��ǰ���� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=46');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

<!-- ��ǰ������� : start -->
<form class="admin-form" method="get" name="frmList" id="el-admin-goods-search-form">
<input type="hidden" name="sort" value="<?=Clib_Application::request()->get('sort')?>">
<input type="hidden" name="indicate" value="<?=Clib_Application::request()->get('indicate')?>">

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
	<th>���ο���</th>
	<td>
		<?php
		foreach ($searchForm->getTag('discount') as $label => $tag) {
			echo sprintf('<label>%s%s</label> ',$tag, $label);
		}
		?>
	</td>
	<th>���αⰣ</th>
	<td>
		<input type="text" name="discount_range[]" value="<?=$_GET[discount_range][0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="ac"> -
		<input type="text" name="discount_range[]" value="<?=$_GET[discount_range][1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="ac">
	</td>
</tr>
</table>

<div class="button_top"><input type="image" src="../img/btn_search2.gif" onclick="__search();return false;"></div>

<table class="nude">
<tr>
	<td>
	<!-- ���λ�ǰ -->
	<table class="admin-form-table">
	<tr>
		<th>���λ�ǰ</th>
		<td class="vt">
			<select name="smain">
			<?
			$displayAreaKeys = array_keys($cfg_step);
			for ($i=0, $m=sizeof($displayAreaKeys);$i<$m;$i++) {
				$key = $displayAreaKeys[$i];
			?>
			<option value="<?=$key?>" <?=($key == Clib_Application::request()->get('smain')) ? 'selected' : ''?>><?=$cfg_step[$key]['title']?>
			<? } ?>
			</select>

			<a href="javascript:void(0);" onclick="__search('main'); return false;"><img src="../img/buttons/btn_go.gif"></a>

			<p class="help">
			(������������ ������ ��ǰ���� ����մϴ�)
			</p>
		</td>
	</tr>
	</table>
	<!-- ���λ�ǰ -->
	</td>
	<td class="vt">
	<!-- �̺�Ʈ ��ǰ -->
	<table class="admin-form-table">
	<tr>
		<th>�̺�Ʈ</th>
		<td>
			<select name="sevent" onchange="getEventList(this)">
			<option value="" call="<?=$_GET[seventpage]?>">�� �ε� �����ϱ�</option>
			</select>
			<input type="hidden" name="seventpage">

			<a href="javascript:void(0);" onclick="__search('event'); return false;"><img src="../img/buttons/btn_go.gif"></a>

			<p class="help">
			(�̺�Ʈ��ǰ���� �����س��� ��ǰ���� ����մϴ�)
			</p>
		</td>
	</tr>
	</table>
	<!-- �̺�Ʈ ��ǰ -->
	</td>
</tr>
</table>

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

<form class="admin-form" id="goods-discount-form" name="fmList" method="post" action="indb_adm_goods_manage_discount.php" target="ifrmHidden" onsubmit="return chkFormList(this)">

<table class="admin-list-table">
<colgroup>
	<col style="width:35px;">
	<col style="width:100px;">
	<col >
	<col style="width:55px;">
	<col style="width:55px;">
	<col style="width:55px;">

	<col style="width:140px;">
	<col style="width:140px;">
</colgroup>
<thead>
<tr>
	<th><a href="javascript:void(0)" onclick="chkBox(document.getElementsByName('chk[]'),'rev')" class="white">����</a></th>
	<th>�ý��ۻ�ǰ�ڵ�</th>
	<th>��ǰ��</th>
	<th>�Ǹűݾ�</th>
	<th>�Ǹ����</th>
	<th>����</th>

	<th>���ο���</th>
	<th>���δ�� : �ݾ�</th>
</tr>
</thead>
<tbody>
<?
foreach ($goodsList as $goods) {
	$discount = $goods->getDiscount();
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
	<td><?=number_format($goods->getStock())?></td>
	<td><img src="../img/icn_<?=$goods[open]?>.gif"></td>
	<?
	if ($discount->hasLoaded()) {
	?>
		<td>
			����
			<p>
			<? if ($discount->getGdStartDate() && $discount->getGdEndDate()) { ?>
				<?=Core::helper('date')->format($discount->getGdStartDate(), 'Y-m-d')?> ~
				<?=Core::helper('date')->format($discount->getGdEndDate(), 'Y-m-d')?>
			<? } else { ?>
				�Ⱓ����
			<? } ?>
			</p>
		</td>
		<td>
			<? foreach($discount->getLevel() as $idx => $level) { ?>
				<?=$discount->getLevelLabel($idx)?> <?=$discount->getAmount($idx)?> <?=($unit = $discount->getUnit($idx)) == '=' ? '��' : $unit?><br />
			<? } ?>
		</td>
		<?
	}
	else {
		echo '<td>-</td><td>-</td>';
	}
	?>
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
	<th>
		��ǰ���� <br />
		�ϰ� ����/����
	</th>
	<td>
		<table class="admin-form-table">
		<tr>
			<th>�Ⱓ</th>
			<td>
				<input type=text name="goods_discount_by_term_range_date[]" value="" onclick="calendar(event)" onkeydown="onlynumber()" style="width:80px;" class="ac">
				<select name="goods_discount_by_term_range_hour[]">
				<? for($i = 0; $i < 24; $i++) { ?>
					<option value="<? printf('%02d',$i)?>"><? printf('%02d',$i)?></option>
				<? } ?>
				</select>��
				<select name="goods_discount_by_term_range_min[]">
				<? for($i = 0; $i < 60; $i++) { ?>
					<option value="<? printf('%02d',$i)?>"><? printf('%02d',$i)?></option>
				<? } ?>
				</select>��
				 -
				<input type=text name="goods_discount_by_term_range_date[]" value="" onclick="calendar(event)" onkeydown="onlynumber()" style="width:80px;" class="ac">
				<select name="goods_discount_by_term_range_hour[]">
				<? for($i = 0; $i < 24; $i++) { ?>
					<option value="<? printf('%02d',$i)?>"><? printf('%02d',$i)?></option>
				<? } ?>
				</select>��
				<select name="goods_discount_by_term_range_min[]">
				<? for($i = 0; $i < 60; $i++) { ?>
					<option value="<? printf('%02d',$i)?>"><? printf('%02d',$i)?></option>
				<? } ?>
				</select>��

			</td>
		</tr>
		<tr>
			<th>��� �� �ݾ�</th>
			<td>
				<?php
				foreach ($discountForm->getTag('goods_discount_by_term_for_specify_member_group') as $label => $tag) {
					echo sprintf('<label>%s%s</label> ',$tag, $label);
				}
				?>
				<!-- ��ü -->
				<table class="nude padding-midium IF_goods_discount_by_term_for_specify_member_group_IS_0">
				<tr>
					<td>
						���αݾ� : <input type="text" name="goods_discount_by_term_amount_for_all" value="">
						<select name="goods_discount_by_term_amount_type_for_all">
							<option value="%">%</option>
							<option value="=">��</option>
						</select>
					</td>
				</tr>
				</table>

				<!-- Ư�� ȸ�� �׷� -->
				<table class="nude padding-midium IF_goods_discount_by_term_for_specify_member_group_IS_1" id="el-goods-discount-by-term">
				<tr>
					<td>
						��� :
						<select name="goods_discount_by_term_target[]">
							<? foreach ($memberGroups as $memberGroup) { ?>
							<option value="<?=$memberGroup['level']?>"><?=$memberGroup['grpnm']?></option>
							<? } ?>
						</select>
					</td>
					<td>
						���αݾ� : <input type="text" name="goods_discount_by_term_amount[]" value="">
						<select name="goods_discount_by_term_amount_type[]">
							<option value="%">%</option>
							<option value="=">��</option>
						</select>
					</td>
					<td><a href="javascript:void(0);" onclick="nsAdminGoodsForm.discount.addGroup();"><img src="../img/i_add.gif"></a></td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th>�������</th>
			<td>
				<?php
				foreach ($discountForm->getTag('goods_discount_by_term_use_cutting') as $label => $tag) {
					echo sprintf('<label>%s%s</label> ',$tag, $label);
				}
				?>

				<?=$discountForm->getTag('goods_discount_by_term_cutting_unit')?> �� ����
				<?=$discountForm->getTag('goods_discount_by_term_cutting_method')?>
				<p class="help">
					�Ǹűݾ��� %������ ��ǰ�� ���� ������ �߻��ϴ� 1�� ���� �� 10�� ���� ���αݾ��� �����Ͽ� �����մϴ�.<br/>
					Ex) �Ǹűݾ� 1,700���� 7% ���� ? ���αݾ� 119�� �߻�<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;=> 1�� ���� ����� ���� : 110��, �ݿø� : 120��, �ø� : 120�� ���αݾ� ����<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;=> 10�� ���� ����� ���� : 100��, �ݿø� : 100��, �ø� : 200�� ���αݾ� ����
				</p>
				<p class="help" style="color: #ff0000;">
					�� ����� ���αݾ��� %�� �����ÿ��� ���� �˴ϴ�.
				</p>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>

<div class=button_top><input type=image src="../img/btn_save.gif"></div>

</form>

<script type="text/javascript">
// onload events
Event.observe(document, 'dom:loaded', function(){
	nsAdminForm.init($('goods-discount-form'), $('el-admin-goods-search-form'));
	nsAdminGoodsList.sortInit('<?=Clib_Application::request()->get('sort')?>');
	getEventList(frmList.sevent, '<?=$_GET[sevent]?>');
});
</script>

<? include "../_footer.php"; ?>
