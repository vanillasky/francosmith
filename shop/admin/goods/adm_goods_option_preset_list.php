<?
$location = "상품관리 > 옵션바구니(SET) 관리";
include "../_header.php";

$_page_num = Clib_Application::request()->get('page_num', 10);

$_keyword = Clib_Application::request()->get('keyword');
$_target  = Clib_Application::request()->get('target');

$_date_target  = Clib_Application::request()->get('date_target');
$_regdt = Clib_Application::request()->get('regdt');

$_order = Clib_Application::request()->get('sort', 'sno desc');

$where = array();

if ($_keyword) {
	switch($_target) {
		case 'title':
			$where[] = sprintf("title like '%%%s%%'", $_keyword);
			break;
		case 'value':
			$where[] = sprintf("(opt1 like '%%%s%%' or opt2 like '%%%s%%')", $_keyword, $_keyword);
			break;
		case 'name':
		default :
			$where[] = sprintf("(optnm1 like '%%%s%%' or optnm2 like '%%%s%%')", $_keyword, $_keyword);
			break;
	}
}

if ($_regdt[0] && $_regdt[1]) {
	switch($_date_target) {
		case 'regdt':
		default :
			$_format = "regdt between '%s' and '%s'";
			break;
	}

	$where[] = sprintf($_format, Core::helper('date')->min($_regdt[0]), Core::helper('date')->max($_regdt[1]));

}

$pg = new Page($_GET[page], $_page_num);
$pg->setQuery(GD_DOPT, $where, $_order);
$pg->exec();
$res = $db->query($pg->query);
?>
<script type="text/javascript" src="../js/adm_form.js"></script>
<script type="text/javascript" src="./js/goods_register.js"></script>
<script type="text/javascript" src="./js/goods_list.js"></script>
<h2 class="title">옵션 바구니(set) 관리 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=44');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>


<form class="admin-form" method="get" name="frmList" id="el-admin-goods-search-form">
<input type="hidden" name="sort" value="<?=Clib_Application::request()->get('sort')?>">

<table class="admin-form-table">
	<tr>
		<th>날짜검색</th>
		<td>
			<select name="date_target">
				<option value="regdt">등록/수정일</option>
			</select>

			<input type="text" name="regdt[]" value="<?=$_regdt[0]?>" onclick="calendar(event);" onkeydown="onlynumber();" class="ac"> -
			<input type="text" name="regdt[]" value="<?=$_regdt[1]?>" onclick="calendar(event);" onkeydown="onlynumber();" class="ac">
			<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
		</td>
	</tr>
	<tr>
		<th>검색어</th>
		<td>
			<select name="target">
				<option value="name" <?=$_target == 'name' ? 'selected' : ''?>>옵션명</option>
				<option value="value" <?=$_target == 'value' ? 'selected' : ''?>>옵션값</option>
				<option value="title" <?=$_target == 'title' ? 'selected' : ''?>>옵션세트명</option>
			</select>
			<input type="text" name="keyword" value="<?=$_keyword?>" >
		</td>
	</tr>
</table>
<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

<div style="padding-top:15px"></div>

<div class="admin-list-toolbar">
	<div class="list-information">
		검색 <b><?=number_format($pg->recode['total'])?></b>개 / <b><?=number_format($pg->page['now'])?></b> of <?=number_format($pg->page['total'])?> Pages
	</div>

	<div class="list-tool">
	<ul>
		<li><img src="../img/sname_date.gif"><a href="javascript:nsAdminGoodsList.sort('gop_register_date desc')"><img name="sort_gop_register_date_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('gop_register_date')"><img name="sort_gop_register_date" src="../img/list_down_off.gif"></a></li>
		<li class="separater"></li>
		<li>세트명<a href="javascript:nsAdminGoodsList.sort('title desc')"><img name="sort_gop_set_name_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('title')"><img name="sort_gop_set_name" src="../img/list_down_off.gif"></a></li>
		<li class="separater"></li>
		<li>
		<img src="../img/sname_output.gif" align=absmiddle>
		<select name=page_num onchange="this.form.submit()">
		<?
		$r_pagenum = array(10,20,40,60,100);
		foreach ($r_pagenum as $v){
		?>
		<option value="<?=$v?>" <?=($v == Clib_Application::request()->get('page_num')) ? 'selected' : ''?>><?=$v?>개 출력
		<? } ?>
		</select>
		</li>
	</ul>
	</div>
</div>
</form>

<table class="admin-list-table">
<colgroup>
	<col style="width:50px;">
	<col style="width:100px;">
	<col >
	<col style="width:150px;">
	<col style="width:55px;">
	<col style="width:55px;">
</colgroup>
<thead>
<tr>
	<th>번호</th>
	<th>옵션 세트명</th>
	<th>옵션명:옵션값</th>
	<th>등록/수정일</th>
	<th>수정</th>
	<th>삭제</th>
</tr>
</thead>
<tbody>
<?
$i = 0;
while ($data=$db->fetch($res)) {
	$values = array();
	if ($data['optnm1']) {
		$values[$data['optnm1']] = str_replace('^',',',$data['opt1']);
	}

	if ($data['optnm2']) {
		$values[$data['optnm2']] = str_replace('^',',',$data['opt2']);
	}
?>
<tr class="ac">
	<td><?=$pg->idx--?></td>
	<td><?=$data['title']?></td>
	<td class="al">
		<? foreach($values as $name=>$value) { ?>
		<?=$name?> : <?=$value?> <br />
		<? } ?>
	</td>
	<td><?=$data['regdt']?></td>
	<td><a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.preset.openForm('<?=$data['sno']?>');"><img src="../img/i_edit.gif" align="absmiddle" border="0"></a></td>
	<td><a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.preset.del('<?=$data['sno']?>');"><img src="../img/i_del.gif" align="absmiddle" border="0"></a></td>
</tr>
<? } ?>
</tbody>
</table>

<div class="button-container ar">
<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.preset.openForm();"><img src="../img/btn_optionbasket_new.gif" align="absmiddle" border="0"></a>
</div>

<div class="admin-list-toolbar">
	<div class="paging"><?=$pg->page['navi']?></div>
</div>

<script type="text/javascript">
// onload events
Event.observe(document, 'dom:loaded', function(){
	nsAdminGoodsList.sortInit('<?=Clib_Application::request()->get('sort')?>');
	nsAdminForm.init($('el-admin-goods-search-form'));
});
</script>

<?php
include "../_footer.php";
?>
