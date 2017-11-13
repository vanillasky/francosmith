<?
set_time_limit(0);
include "../_header.popup.php";
include "../../lib/naverPartner.class.php";
$naver = new naverPartner();
$naver->runLog('NaverShopping_Migration_Start');

$res = $db->query("select gnc.category,gc.catnm from gd_navershopping_category gnc left join gd_category gc on gnc.category=gc.category");
while ($data=$db->fetch($res,1)) {
	$category[] = $data;
}
?>
<script type="text/javascript" src="../godo.loading.indicator.js"></script>
<div class="title title_top">네이버쇼핑 마이그레이션</div>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr class=rndbg>
<th style="width:300px;">카테고리명</th>
<th>진행상황</th>
</tr>
<?
for ($i=0; $i<count($category); $i++) {
?>
<tr>
<td height=4 colspan=12></td>
</tr>
<tr class="center">
	<td width="50" class="ver8"><?=$category[$i]['catnm']?></td>
	<td class="ver81"><img id="<?=$category[$i]['category']?>_img"/><span id="<?=$category[$i]['category']?>_span">대기중<span></td>
</tr>
<tr>
<td height=4></td>
</tr>
<tr>
<td colspan=12 class=rndline></td>
</tr>
<? } ?>
</table>
<script>
var listCnt = 0;	// 카테고리 카운터
var limitCnt = 0;	// limit 카운터
var categoryList = new Array();
<?for ($i=0; $i<count($category); $i++) {?>
	categoryList[<?=$i?>] = '<?=$category[$i][category]?>';
<?}?>
window.onload = function() {
	migration();
}

function migration() {
	if (typeof(categoryList[listCnt]) == 'undefined') {
		limitCnt = 0;
		realMigration();
	}

	document.getElementById(categoryList[listCnt]+'_span').innerHTML='작업중';
	document.getElementById(categoryList[listCnt]+'_img').src='../img/ajax-loader.gif';

	var ajax = new Ajax.Request('indb.php',
	{
		method: 'post',
		parameters: 'mode=naverShoppingMigration&category='+categoryList[listCnt]+'&cnt='+limitCnt,
		onComplete: function (response)
		{
			var res = response.responseText;
			if (res == 'end') {	// 카테고리 변경
				document.getElementById(categoryList[listCnt]+'_span').innerHTML = '<font color=red>완료</font>';
				document.getElementById(categoryList[listCnt]+'_img').src='';
				listCnt += 1;
				limitCnt = 0;
				migration();
			}
			else if (res == 'ok') {	// limitCnt 증가
				limitCnt += 1;
				migration();
			}
			else {
				alert("마이그레이션을 실패하였습니다.\n고객센터에 문의하여 주세요.");
			}
		},
		onFailure : function() {
			alert("마이그레이션을 실패하였습니다.\n고객센터에 문의하여 주세요.");
		}
	});
}

function realMigration() {
	nsGodoLoadingIndicator.init({});
	nsGodoLoadingIndicator.show();

	var ajax = new Ajax.Request('indb.php',
	{
		method: 'post',
		parameters: 'mode=naverShoppingMigration&cnt='+limitCnt,
		onComplete: function (response)
		{
			var res = response.responseText;
			if (res == 'end') {
				nsGodoLoadingIndicator.hide();
				alert("마이그레이션이 완료 되었습니다.");
				parent.location.href='naver_shopping_setting.php';
			}
			else if (res == 'ok') {
				limitCnt += 1;
				realMigration();
			}
			else {
				nsGodoLoadingIndicator.hide();
				alert("마이그레이션을 실패하였습니다.\n고객센터에 문의하여 주세요.");
			}
		},
		onFailure : function() {
			nsGodoLoadingIndicator.hide();
			alert("마이그레이션을 실패하였습니다.\n고객센터에 문의하여 주세요.");
		}
	});
}
</script>