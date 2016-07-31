<?

$location = "��ǰ�ϰ����� > ���� ������ ����";
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

	'sort' => Clib_Application::request()->get('sort', 'goodsno desc'),
);

// ��ǰ ���
$goodsList = $goodsHelper->getGoodsCollection($params);

// ����¡
$pg = $goodsList->getPaging();

// ��ǰ �˻� ��
$searchForm = Clib_Application::form('admin_goods_search')->setData(Clib_Application::request()->gets('get'));
?>
<link rel="stylesheet" type="text/css" href="./css/css.css">
<script type="text/javascript" src="../js/adm_form.js"></script>
<script type="text/javascript" src="./js/goods_list.js"></script>
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

<h2 class="title">���� ������ ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=14');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

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

<form class="admin-form" name="fmList" method="post" action="indb_adm_goods_manage_mileage.php" target="ifrmHidden" onsubmit="return chkFormList(this)">
<input type="hidden" name="query" value="<?=base64_encode(substr($pg->query,0,stripos($pg->query,"limit")))?>" required msgR="�ϰ����� �� ��ǰ�� ���� �˻��ϼ���.">

<table class="admin-list-table">
<colgroup>
	<col style="width:35px;">
	<col style="width:100px;">
	<col >
	<col style="width:55px;">
	<col style="width:55px;">
	<col style="width:55px;">
	<col style="width:55px;">
	<col style="width:80px;">
	<col style="width:55px;">
</colgroup>
<thead>
<tr>
	<th><a href="javascript:void(0)" onclick="chkBox(document.getElementsByName('chk[]'),'rev')" class="white">����</a></th>
	<th>�ý��ۻ�ǰ�ڵ�</th>
	<th>��ǰ��</th>
	<th>�Ǹűݾ�</th>
	<th>�Һ��ڰ�</th>
	<th>������</th>
	<th>�Ǹ����</th>
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
	<td class="price"><?=number_format($goods->getData('consumer'))?></td>
	<td><?=number_format($goods->getReserve())?></td>
	<td><?=number_format($goods->getStock())?></td>
	<td><?=Core::helper('date')->format($goods['regdt'],'Y-m-d')?></td>
	<td><img src="../img/icn_<?=$goods[open]?>.gif"></td>
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
	<th>���������Ǽ���</th>
	<td>
	<div style="margin:5px 0">
	<span class="noline"><input type="radio" name="method" value="direct" <?=$checked[method]['direct']?> required label="�ϰ�������"></span>�������� �ϰ�
	<input type="text" name="reserve" value="<?=$_GET[reserve]?>" size="6" class="ar" label="���ϸ���(������)"> ������ �����մϴ�.
	</div>
	<div style="margin:5px 0">
	<span class="noline"><input type="radio" name="method" value="price" <?=$checked[method]['price']?> required label="�ϰ�������"></span>�������� �ǸŰ���
	<select name="percent">
	<?
	$idx = 0;
	while (($idx += ($idx <= 0.9 ? 0.1 : 1)) <= 100) echo "<option value=\"{$idx}\" " . $selected[percent]["{$idx}"] . ">{$idx}</option>";
	?>
	</select>%��
	<select name="roundunit">
	<option value="1" <?=$selected[roundunit][1]?>>1</option>
	<option value="10" <?=$selected[roundunit][10]?>>10</option>
	<option value="100" <?=$selected[roundunit][100]?>>100</option>
	<option value="1000" <?=$selected[roundunit][1000]?>>1000</option>
	</select>
	�� ������
	<select name="roundtype">
	<option value="down" <?=$selected[roundtype][down]?>>����</option>
	<option value="halfup" <?=$selected[roundtype][halfup]?>>�ݿø�</option>
	<option value="up" <?=$selected[roundtype][up]?>>�ø�</option>
	</select>
	�Ͽ� �����մϴ�.
	</div>
	<div style="margin:5px 0" class="noline">
	<input type="checkbox" name="isall" value="Y" <?=$checked[isall]['Y']?>>�˻��� ��ǰ ��ü<?=($pg->recode[total]?"({$pg->recode[total]}��)":"")?>�� �����մϴ�. <span class="help">(��ǰ���� ���� ��� ������մϴ�. �����ϸ� �� �������� �����Ͽ� �����ϼ���)</span></div></td>
</tr>
</table>

<div class=button_top><input type=image src="../img/btn_save.gif"></div>

</form>

<ul class="admin-simple-faq">
	<li>�ϰ����� �� ��ǰ�� �˻� �� ��ǰ�������� �ϰ�ó�� ���ǿ� ���� �����մϴ�.</li>
	<li>[����1] �ϰ����� �Ŀ��� <b>�������·� ������ �ȵǹǷ� �����ϰ� �����Ͻñ� �ٶ��ϴ�.</b></li>
	<li>[����2] ���� ���ϵ� �������� ���񽺸� ���ؼ� �˻������ ���� ��쿡�� �˻���� ��ü������ ���Ͻñ� �ٶ��ϴ�.</li>
	<li><b>[�����ݼ��� ����]</b></li>
	<li>�ǸŰ��� 5.5% ���ε� �������� �������� �ϰ������� �����ϰ�, ���� ������ 100�� ������ �����Ͽ� �����Ѵٸ�,</li>
	<li>�ǸŰ� 10,000���� ��ǰ�� ������ ������ �����ϴ�.</li>
	<li>�� 10,000 �� (5.5 / 100) = 550���̸�,</li>
	<li>�� 100�� ���� �����ϸ� 500�� ���� ���� �����ݼ����� �˴ϴ�.</li>
</ul>

<script type="text/javascript">
// onload events
Event.observe(document, 'dom:loaded', function(){
	nsAdminGoodsList.sortInit('<?=Clib_Application::request()->get('sort')?>');
	nsAdminForm.init($('el-admin-goods-search-form'));
	getEventList(frmList.sevent, '<?=$_GET[sevent]?>');
});
</script>

<? include "../_footer.php"; ?>
