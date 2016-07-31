<?php
$location = "쇼핑몰 App관리 > 메뉴 디자인";
include "../_header.php";
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

$tmp_basicscreen = $pAPI->getBasicScreen($godo['sno']);

$arr_basicscreen = $json->decode($tmp_basicscreen);

$arr_basicscreen['shopTitle'] = $cfg['shopName'];

$basic['bg'] = '#000000';
$basic['text'] = '#FFFFFF';
$basic['pos'] = 'left';

if(!$arr_basicscreen['navigator']['bg_color']) $arr_basicscreen['navigator']['bg_color'] = $basic['bg'];
if(!$arr_basicscreen['navigator']['title']) $arr_basicscreen['navigator']['title'] = $cfg['shopName'];
if(!$arr_basicscreen['navigator']['title_color']) $arr_basicscreen['navigator']['title_color'] = $basic['text'];
if(!$arr_basicscreen['navigator']['use_title_img']) $arr_basicscreen['navigator']['use_title_img'] = 'false';

if(!$arr_basicscreen['main_menu1']['bg_color']) $arr_basicscreen['main_menu1']['bg_color'] = $basic['bg'];
if(!$arr_basicscreen['main_menu1']['text_color']) $arr_basicscreen['main_menu1']['text_color'] = $basic['text'];
if(!$arr_basicscreen['main_menu1']['icon_pos']) $arr_basicscreen['main_menu1']['icon_pos'] = $basic['pos'];

if(!$arr_basicscreen['main_menu2']['bg_color']) $arr_basicscreen['main_menu2']['bg_color'] = $basic['bg'];
if(!$arr_basicscreen['main_menu2']['text_color']) $arr_basicscreen['main_menu2']['text_color'] = $basic['text'];
if(!$arr_basicscreen['main_menu2']['icon_pos']) $arr_basicscreen['main_menu2']['icon_pos'] = $basic['pos'];

if(!$arr_basicscreen['my_menu']['bg_color']) $arr_basicscreen['my_menu']['bg_color'] = $basic['bg'];
if(!$arr_basicscreen['my_menu']['text_color']) $arr_basicscreen['my_menu']['text_color'] = $basic['text'];
if(!$arr_basicscreen['my_menu']['icon_pos']) $arr_basicscreen['my_menu']['icon_pos'] = $basic['pos'];

$checked['navigator']['use_title_img'][$arr_basicscreen['navigator']['use_title_img']] = 'checked';
$checked['main_menu1']['icon_pos'][$arr_basicscreen['main_menu1']['icon_pos']] = 'checked';
$checked['main_menu2']['icon_pos'][$arr_basicscreen['main_menu2']['icon_pos']] = 'checked';
$checked['my_menu']['icon_pos'][$arr_basicscreen['my_menu']['icon_pos']] = 'checked';

?>
<script type="text/javascript" src="../MiniColorPicker.js"></script>
<?
if($expire_dt < $now_date) {
	@include('shopTouch_expire_msg.php');
}
?>
<form name=form method=post action="indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="design_menu">

<div class="title title_top">쇼핑몰 App 메인화면 템플릿 선택 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=12')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>쇼핑몰 타이틀</td>
	<td>
		<div style="float:left;margin-right:10px;"><input type="text" name="title" value="<?=$arr_basicscreen['navigator']['title']?>" size="50" maxlength="50"></div>
		<script type="text/javascript">initPicker("title_color","<?=$arr_basicscreen['navigator']['title_color']?>",24);</script>
	</td>
</tr>
<tr>
	<td>타이틀 이미지</td>
	<td class="noline">
		<label><input type="radio" name="use_title_img" value="true" class="" <?=$checked['navigator']['use_title_img']['true']?>/>사용</label>
		<label><input type="radio" name="use_title_img" value="false" class="" <?=$checked['navigator']['use_title_img']['false']?>/>사용안함</label><br />
		<input type="file" name="title_img_up[]" size="50" class=line><input type="hidden" name="title_img" value="<?=$arr_basicscreen['navigator']['title_img']?>"> <span class="small" style="margin-left:10px;"><font class="extext">권장 사이즈 100px X 30px</font></span>
		<? if($arr_basicscreen['navigator']['title_img']){ ?>
		<div><img src="<?=$arr_basicscreen['navigator']['title_img']?>" onError="this.style.display='none';" /></div>
		<? } ?>
	</td>
</tr>
</table>
<div style="height:10px;"></div>
<div class="title title_top">쇼핑몰 메뉴 영역 디자인</div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>메뉴바</td>
	<td>
		<table class="noline">
		<tr>
			<td>배경색</td>
			<td><script type="text/javascript">initPicker("bg_color","<?=$arr_basicscreen['navigator']['bg_color']?>",24);</script></td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td>메인 1차 메뉴</td>
	<td>
		<table class="noline">
		<tr>
			<td>텍스트</td>
			<td><script type="text/javascript">initPicker("menu1_text_color","<?=$arr_basicscreen['main_menu1']['text_color']?>",24);</script></td>
		</tr>
		<tr>
			<td>배경색</td>
			<td><script type="text/javascript">initPicker("menu1_bg_color","<?=$arr_basicscreen['main_menu1']['bg_color']?>",24);</script></td>
		</tr>
		<tr>
			<td>아이콘</td>
			<td>
				<label><input type="radio" name="menu1_icon_pos" value="left" <?=$checked['main_menu1']['icon_pos']['left']?> />왼쪽</label>
				<label><input type="radio" name="menu1_icon_pos" value="right" <?=$checked['main_menu1']['icon_pos']['right']?> />오른쪽</label>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td>메인 2차 메뉴</td>
	<td>
		<table class="noline">
		<tr>
			<td>텍스트</td>
			<td><script type="text/javascript">initPicker("menu2_text_color","<?=$arr_basicscreen['main_menu2']['text_color']?>",24);</script></td>
		</tr>
		<tr>
			<td>배경색</td>
			<td><script type="text/javascript">initPicker("menu2_bg_color","<?=$arr_basicscreen['main_menu2']['bg_color']?>",24);</script></td>
		</tr>
		<tr>
			<td>아이콘</td>
			<td>
				<label><input type="radio" name="menu2_icon_pos" value="left" <?=$checked['main_menu2']['icon_pos']['left']?> />왼쪽</label>
				<label><input type="radio" name="menu2_icon_pos" value="right" <?=$checked['main_menu2']['icon_pos']['right']?> />오른쪽</label>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td>마이샵</td>
	<td>
		<table class="noline">
		<tr>
			<td>텍스트</td>
			<td><script type="text/javascript">initPicker("mymenu_text_color","<?=$arr_basicscreen['my_menu']['text_color']?>",24);</script></td>
		</tr>
		<tr>
			<td>배경색</td>
			<td><script type="text/javascript">initPicker("mymenu_bg_color","<?=$arr_basicscreen['my_menu']['bg_color']?>",24);</script></td>
		</tr>
		<tr>
			<td>아이콘</td>
			<td>
				<label><input type="radio" name="mymenu_icon_pos" value="left" <?=$checked['my_menu']['icon_pos']['left']?> />왼쪽</label>
				<label><input type="radio" name="mymenu_icon_pos" value="right" <?=$checked['my_menu']['icon_pos']['right']?> />오른쪽</label>
			</td>
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
<tr><td><img src="../img/icon_list.gif" align="absmiddle">쇼핑몰 App의 타이틀, 메뉴바 및 메뉴리스트의 색상 및 아이콘 위치를 지정할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">타이틀 이미지의 권장 사이즈는 100px * 30px 이며, 타이틀 이미지를 사용안하실 경우 쇼핑몰 타이틀이 노출됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">메뉴의 아이콘 등록 및 수정은 쇼핑몰 App 카테고리 설정에서 하실 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>