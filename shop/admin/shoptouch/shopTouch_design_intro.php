<?php
$location = "쇼핑몰 App관리 > 쇼핑몰 App 인트로 설정";
include "../_header.php";
@include "../../conf/config.shopTouch.php";
@include "../../lib/pAPI.class.php";
@include_once "../../lib/json.class.php";
$pAPI = new pAPI();
$json = new Services_JSON(16);

$expire_dt = $pAPI->getExpireDate();
if(!$expire_dt) {
	msg('서비스 신청후에 사용가능한 메뉴입니다.', -1);
}

$now_date = date('Y-m-d 23:59:59');
$tmp_now_date = date('Y-m-d 23:59:59', mktime(0,0,0, substr($now_date, 5, 2), substr($now_date, 8, 2) - 30, substr($now_date, 0, 4)));
if($expire_dt < $tmp_now_date) {
	msg('서비스 사용기간 만료후 30일이 지나 서비스가 삭제 되었습니다.\n서비스를 다시 신청해 주시기 바랍니다.', -1);
}


$tmp_intro = $pAPI->getStartScreen($godo['sno']);

$arr_intro = $json->decode($tmp_intro);

if(!$arr_intro['use']) $arr_intro['use'] = 'false';
if(!$arr_intro['effect']) $arr_intro['effect'] = 'none';

$checked['use'][$arr_intro['use']] = 'checked';
$checked['effect'][$arr_intro['effect']] = 'checked';

?>
<script type="text/javascript" src="../MiniColorPicker.js"></script>
<script type="text/javascript">
function showBackground(val) {
	document.getElementById('background_color').style.display = "none";
	document.getElementById('background_image').style.display = "none";
	document.getElementById('background_' + val).style.display = "";
}
</script>
<?
if($expire_dt < $now_date) {
	@include('shopTouch_expire_msg.php');
}
?>
<form name=form method=post action="indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="design_intro">

<div class="title title_top">인트로 이미지 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=11')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>사용여부</td>
	<td class="noline">
		<label><input type="radio" name="use" value="true" <?=$checked['use']['true']?>/>사용</label>
		<label><input type="radio" name="use" value="false" <?=$checked['use']['false']?>/>미사용</label>
	</td>
</tr>
<tr>
	<td>인트로 이미지</td>
	<td class="noline">
		<input type="file" name="intro_up[]" size="50" class=line><input type="hidden" name="intro" value="<?=$arr_intro['intro']?>"> <span class="small" style="margin-left:10px;"><font class="extext">권장 사이즈 1024px X 748px</font></span>
		<? if($arr_intro['intro']){ ?>
		<div><img src="<?=$arr_intro['intro']?>" style="width:512px; height:374px;"></div>
		<? } ?>

	</td>
</tr>
<tr>
	<td>인트로 효과</td>
	<td class="noline">
		<table class="noline">
		<tr>
			<td><label><input type="radio" name="effect" value="none" <?=$checked['effect']['none']?>/>효과없음</label></td>
			<td><label><input type="radio" name="effect" value="fadeout" <?=$checked['effect']['fadeout']?>/>페이드아웃</label></td>
		</tr>
		<tr>
			<td><label><input type="radio" name="effect" value="slide_up" <?=$checked['effect']['slide_up']?>/>스크린아웃-상</label></td>
			<td><label><input type="radio" name="effect" value="slide_down" <?=$checked['effect']['slide_down']?>/>스크린아웃-하</label></td>
		</tr>
		<tr>
			<td><label><input type="radio" name="effect" value="slide_left" <?=$checked['effect']['slide_left']?>/>스크린아웃-좌</label></td>
			<td><label><input type="radio" name="effect" value="slide_right" <?=$checked['effect']['slide_right']?>/>스크린아웃-우</label></td>
		</tr>
		</table>
	</td>
</tr>
</table>
<div class="button">
<input type=image src="../img/btn_modify.gif">
</div>

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">쇼핑몰 App 실행시에 구매고객님들께 보여질 첫화면 이미지를 설정합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">권장사이즈는 1024px * 748px 입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">인트로화면이 사라질때 적용할 효과를 설정할 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>