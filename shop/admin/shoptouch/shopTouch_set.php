<?php
$location = "쇼핑몰 App관리 > 쇼핑몰 App 사용여부 설정";
include "../_header.php";
@include "../../lib/pAPI.class.php";
@include_once "../../lib/json.class.php";

$pAPI = new pAPI();
$json = new Services_JSON(16);

$use_query_android = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', 'shoptouch', 'use_android');
$res_android = $db->_select($use_query_android);
$use_android = $res_android[0]['value'];

$use_query_apple = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', 'shoptouch', 'use_apple');
$res_apple = $db->_select($use_query_apple);
$use_apple = $res_apple[0]['value'];

if($use_android == '') $use_android = '1';
if($use_apple == '') $use_apple = '1';

$checked['use_android'][$use_android] = 'checked';
$checked['use_apple'][$use_apple] = 'checked';
?>

<form name=form method=post action="indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="set">

<div class="title title_top">쇼핑몰 App 사용여부 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>애플</td>
	<td class="noline">
		<?if (!$pAPI->chkExpireDate('apple')) { ?>
		<span class="small"><font class="extext">서비스 신청후에 설정 가능합니다.</font></span>
		<? } else {?>
		<input type="radio" name="use_apple" value="1" <?=$checked['use_apple'][1]?> />허용 <input type="radio" name="use_apple" value="0" <?=$checked['use_apple'][0]?> />허용안함
		<span class="small"><font class="extext">쇼핑몰 App 접근 여부를 설정합니다.</font></span>
		<? } ?>
	</td>
</tr>
<tr>
	<td>안드로이드</td>
	<td class="noline">
		<?if (!$pAPI->chkExpireDate('android')) { ?>
		<span class="small"><font class="extext">서비스 신청후에 설정 가능합니다.</font></span>
		<? } else {?>
		<input type="radio" name="use_android" value="1" <?=$checked['use_android'][1]?> />허용 <input type="radio" name="use_android" value="0" <?=$checked['use_android'][0]?> />허용안함
		<span class="small"><font class="extext">쇼핑몰 App 접근 여부를 설정합니다.</font></span>
		<? } ?>
	</td>
</tr>
</table>

<div class="button">
<input type=image src="../img/btn_modify.gif">
</div>

</form>

<? include "../_footer.php"; ?>