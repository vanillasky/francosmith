<?php
$location = "��ǰ�ϰ����� > ���� �ؽ��±� ����";
include '../_header.php';
include '../../lib/page.class.php';

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
	'hashtag' => str_replace(" ", "_", trim(Clib_Application::request()->get('hashtag'))),
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
<link href="<?php echo $cfg['rootDir']; ?>/lib/js/jquery-ui-1.10.4.custom.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo $cfg['rootDir']; ?>/proc/hashtag/hashtagControl.js?actTime=<?php echo time(); ?>"></script>
<script type="text/javascript">
function chkFormList(fObj){
	if (chkForm(fObj) === false) {
		return false;
	}
	return true;
}
</script>

<h2 class="title">���� �ؽ��±� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=53');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

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
	<th>�ؽ��±�</th>
	<td colspan="3">
		<div style="border: 1px #BDBDBD solid; width: 170px; float: left; height: 19px;">#<?php echo $searchForm->getTag('hashtag'); ?></div>
	</td>
</tr>

</table>

<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div>

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

<form class="admin-form" name="fmList" method="post" action="indb_adm_goods_manage_hashtag.php" target="ifrmHidden" onsubmit="return chkFormList(this)">
<input type="hidden" name="query" value="<?=base64_encode(substr($pg->query,0,stripos($pg->query,"limit")))?>" required msgR="�ϰ����� �� ��ǰ�� ���� �˻��ϼ���.">

<table class="admin-list-table" id="admin-list-table">
<colgroup>
	<col style="width:35px;">
	<col style="width:100px;">
	<col >
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
	<td><?=number_format($goods->getStock())?></td>
	<td><?=Core::helper('date')->format($goods['regdt'],'Y-m-d')?></td>
	<td><img src="../img/icn_<?=$goods[open]?>.gif"></td>
</tr>
<tr>
	<td colspan="8" area-data-goodsno="<?php echo $goods['goodsno']; ?>">
	<?php
	$hashtagData = array();
	$hashtagData = $goods->getHashtag('admin_speed_goods');
	if(count($hashtagData) > 0){
		foreach($hashtagData as $hashtag){
			echo $hashtag;
		}
	}
	?>
	</td>
</tr>
<? } ?>
</tbody>
</table>

<div class="admin-list-toolbar">
	<div class="left-buttons"><a href="javascript:void(0)" onclick="chkBox(document.getElementsByName('chk[]'),'rev')"><img src="../img/btn_allchoice.gif" /></a></div>
	<div class="paging"><?=$pg->page['navi']?></div>
</div>

<table class="admin-form-table">
<tr>
	<th>�ؽ��±� ����</th>
	<td>
		<div style="margin:5px 0">
			<span class="noline"><input type="radio" name="hashtagMethod" value="all_add_goods" required label="�ϰ�������"></span>
			���õ� ��ǰ�鿡
			<div style="border: 1px #BDBDBD solid; width: 160px; height: 20px; display: inline-block;">
			#<input type="text" name="hashtagName1" value="<?php echo $_GET['hashtagName1']; ?>" class="hashtagInputListSearch" style="width:150px; border:none; height:16px;" maxlength="20" label="�ؽ��±�">
			</div>
			�� �ϰ������� ����մϴ�.
			<div style="margin: 5px 0px 10px 20px;">
				<span class="noline extext"><input type="checkbox" name="all_add_goods_del" value="y" label="���� �� �߰�" align="absmiddle" /> ��� ������ �ؽ��±� ����(10��)�� �ʰ��� ��ǰ�� ������ ��ϵ� �ؽ��±׸� �����ϰ� ����մϴ�. (�� üũ �� �����ϰ� ����մϴ�.)</span>
			</div>
		</div>
		<div style="margin:5px 0">
			<span class="noline"><input type="radio" name="hashtagMethod" value="all_add" required label="�ϰ�������"></span>
			<div style="border: 1px #BDBDBD solid; width: 160px; height: 20px; display: inline-block;">
			#<input type="text" name="hashtagName2" value="<?php echo $_GET['hashtagName2']; ?>" class="hashtagInputListSearch" style="width:150px; border:none; height:16px;" maxlength="20" class="ar" label="�ؽ��±�">
			</div>
			�� ���ο� �ؽ��±׷� �߰��ϰ�, ���õ� ��ǰ�鿡 �ϰ������� ����մϴ�.
			<div style="margin: 5px 0px 10px 20px;">
				<span class="noline extext"><input type="checkbox" name="all_add_del" value="y" label="���� �� �߰�" align="absmiddle" /> ��� ������ �ؽ��±� ����(10��)�� �ʰ��� ��ǰ�� ������ ��ϵ� �ؽ��±׸� �����ϰ� ����մϴ�. (�� üũ �� �����ϰ� ����մϴ�.)</span>
			</div>
		</div>
		<div style="margin:5px 0" class="noline">
			<span class="noline"><input type="radio" name="hashtagMethod" value="tag_del" required label="�ϰ�������"></span>
			<input type="hidden" name="hashtagName3" value="<?php echo $_GET['hashtag']; ?>" />
			�˻��� <span style="color: blue;">#<?php echo ($_GET['hashtag'])? $_GET['hashtag'] : 'Ư��_�ؽ��±�'; ?></span> �� ���õ� ��ǰ�鿡�� �ϰ������� �����մϴ�.
		</div>
	</td>
</tr>
</table>

<div class="button_top"><input type="image" src="../img/btn_save.gif" /></div>

</form>

<script type="text/javascript">
// onload events
Event.observe(document, 'dom:loaded', function(){
	nsAdminGoodsList.sortInit('<?=Clib_Application::request()->get('sort')?>');
	nsAdminForm.init($('el-admin-goods-search-form'));
});
jQuery(document).ready(HashtagManageListController);
</script>

<? include "../_footer.php"; ?>