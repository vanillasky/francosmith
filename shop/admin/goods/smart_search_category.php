<?php
include "../_header.popup.php";
include "../../lib/page.class.php";

if(!$_GET) exit;

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$page = ((int)$_GET['page']?(int)$_GET['page']:1);
if (!$_GET['page_num']) $_GET['page_num'] = 10; // 페이지 레코드수

$cat_ret = array();

$query = "
select category from
	".GD_CATEGORY."
where
	themeno = '".$_GET['themeno']."'
order by category
";
$res = $db->query($query);

$CatList = $db->_select_page($_GET['page_num'],$page,$query);
?>
<div class="title title_top">테마 적용 카테고리 리스트</div>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=10></td></tr>
<tr class=rndbg>
	<th width=60>번호</th>
	<th>적용 카테고리</th>
</tr>
<tr><td class=rnd colspan=10></td></tr>
<? foreach($CatList['record'] as $data):
	$cat_query = $db->query("select catnm, category from ".GD_CATEGORY." where category in (left('$data[category]',3),left('$data[category]',6),left('$data[category]',9),'$data[category]') order by category");

	$pos = array();

	$lastCount = $db->count_($cat_query);
	$idxNo = 1;
	while($cat_data=$db->fetch($cat_query)) {
		$pos[] = "<a href=\"javascript:parent.location.replace('../goods/category.php?ifrmScroll=1&category=".$cat_data['category']."','parent');\"".(($idxNo == $lastCount) ? " style=\"font-weight:bold;\"" : "").">".$cat_data['catnm']."</a>";
		$idxNo++;
	}
	$cat_ret = @implode(" > ",$pos);
?>
<tr><td height=4 colspan=12></td></tr>
<tr height=25>
	<td align=center><font class=ver8 color=616161><?=$data['_rno']?></td>
	<td align=left><?=$cat_ret?></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=10 class=rndline></td></tr>
<? endforeach; ?>
</table>

<? $pageNavi = &$CatList['page']; ?>
<div align="center" class="pageNavi ver8">
	<? if($pageNavi['prev']): ?>
		<a href="?<?=getvalue_chg('page',$pageNavi['prev'])?>">◀ </a>
	<? endif; ?>
	<? foreach($pageNavi['page'] as $v): ?>
		<? if($v==$pageNavi['nowpage']): ?>
			<a href="?<?=getvalue_chg('page',$v)?>"><?=$v?></a>
		<? else: ?>
			<a href="?<?=getvalue_chg('page',$v)?>">[<?=$v?>]</a>
		<? endif; ?>
	<? endforeach; ?>
	<? if($pageNavi['next']): ?>
		<a href="?<?=getvalue_chg('page',$pageNavi['next'])?>">▶</a>
	<? endif; ?>
</div>