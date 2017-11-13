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
<div class="title title_top">���̹����� ���̱׷��̼�</div>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr class=rndbg>
<th style="width:300px;">ī�װ���</th>
<th>�����Ȳ</th>
</tr>
<?
for ($i=0; $i<count($category); $i++) {
?>
<tr>
<td height=4 colspan=12></td>
</tr>
<tr class="center">
	<td width="50" class="ver8"><?=$category[$i]['catnm']?></td>
	<td class="ver81"><img id="<?=$category[$i]['category']?>_img"/><span id="<?=$category[$i]['category']?>_span">�����<span></td>
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
var listCnt = 0;	// ī�װ� ī����
var limitCnt = 0;	// limit ī����
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

	document.getElementById(categoryList[listCnt]+'_span').innerHTML='�۾���';
	document.getElementById(categoryList[listCnt]+'_img').src='../img/ajax-loader.gif';

	var ajax = new Ajax.Request('indb.php',
	{
		method: 'post',
		parameters: 'mode=naverShoppingMigration&category='+categoryList[listCnt]+'&cnt='+limitCnt,
		onComplete: function (response)
		{
			var res = response.responseText;
			if (res == 'end') {	// ī�װ� ����
				document.getElementById(categoryList[listCnt]+'_span').innerHTML = '<font color=red>�Ϸ�</font>';
				document.getElementById(categoryList[listCnt]+'_img').src='';
				listCnt += 1;
				limitCnt = 0;
				migration();
			}
			else if (res == 'ok') {	// limitCnt ����
				limitCnt += 1;
				migration();
			}
			else {
				alert("���̱׷��̼��� �����Ͽ����ϴ�.\n�����Ϳ� �����Ͽ� �ּ���.");
			}
		},
		onFailure : function() {
			alert("���̱׷��̼��� �����Ͽ����ϴ�.\n�����Ϳ� �����Ͽ� �ּ���.");
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
				alert("���̱׷��̼��� �Ϸ� �Ǿ����ϴ�.");
				parent.location.href='naver_shopping_setting.php';
			}
			else if (res == 'ok') {
				limitCnt += 1;
				realMigration();
			}
			else {
				nsGodoLoadingIndicator.hide();
				alert("���̱׷��̼��� �����Ͽ����ϴ�.\n�����Ϳ� �����Ͽ� �ּ���.");
			}
		},
		onFailure : function() {
			nsGodoLoadingIndicator.hide();
			alert("���̱׷��̼��� �����Ͽ����ϴ�.\n�����Ϳ� �����Ͽ� �ּ���.");
		}
	});
}
</script>