<?php
$location = "���θ� App���� > �޴� ������";
include "../_header.php";
@include "../../lib/pAPI.class.php";
@include_once "../../lib/json.class.php";
$pAPI = new pAPI();
$json = new Services_JSON(16);

$expire_dt = $pAPI->getExpireDate();
if(!$expire_dt) {
	msg('���� ��û�Ŀ� ��밡���� �޴��Դϴ�.', -1);
}

$now_date = date('Y-m-d 23:59:59');
$tmp_now_date = date('Y-m-d 23:59:59', mktime(0,0,0, substr($now_date, 5, 2), substr($now_date, 8, 2) - 30, substr($now_date, 0, 4)));
if($expire_dt < $tmp_now_date) {
	msg('���� ���Ⱓ ������ 30���� ���� ���񽺰� ���� �Ǿ����ϴ�.\n���񽺸� �ٽ� ��û�� �ֽñ� �ٶ��ϴ�.', -1);
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

<div class="title title_top">���θ� App ����ȭ�� ���ø� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=12')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>���θ� Ÿ��Ʋ</td>
	<td>
		<div style="float:left;margin-right:10px;"><input type="text" name="title" value="<?=$arr_basicscreen['navigator']['title']?>" size="50" maxlength="50"></div>
		<script type="text/javascript">initPicker("title_color","<?=$arr_basicscreen['navigator']['title_color']?>",24);</script>
	</td>
</tr>
<tr>
	<td>Ÿ��Ʋ �̹���</td>
	<td class="noline">
		<label><input type="radio" name="use_title_img" value="true" class="" <?=$checked['navigator']['use_title_img']['true']?>/>���</label>
		<label><input type="radio" name="use_title_img" value="false" class="" <?=$checked['navigator']['use_title_img']['false']?>/>������</label><br />
		<input type="file" name="title_img_up[]" size="50" class=line><input type="hidden" name="title_img" value="<?=$arr_basicscreen['navigator']['title_img']?>"> <span class="small" style="margin-left:10px;"><font class="extext">���� ������ 100px X 30px</font></span>
		<? if($arr_basicscreen['navigator']['title_img']){ ?>
		<div><img src="<?=$arr_basicscreen['navigator']['title_img']?>" onError="this.style.display='none';" /></div>
		<? } ?>
	</td>
</tr>
</table>
<div style="height:10px;"></div>
<div class="title title_top">���θ� �޴� ���� ������</div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>�޴���</td>
	<td>
		<table class="noline">
		<tr>
			<td>����</td>
			<td><script type="text/javascript">initPicker("bg_color","<?=$arr_basicscreen['navigator']['bg_color']?>",24);</script></td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td>���� 1�� �޴�</td>
	<td>
		<table class="noline">
		<tr>
			<td>�ؽ�Ʈ</td>
			<td><script type="text/javascript">initPicker("menu1_text_color","<?=$arr_basicscreen['main_menu1']['text_color']?>",24);</script></td>
		</tr>
		<tr>
			<td>����</td>
			<td><script type="text/javascript">initPicker("menu1_bg_color","<?=$arr_basicscreen['main_menu1']['bg_color']?>",24);</script></td>
		</tr>
		<tr>
			<td>������</td>
			<td>
				<label><input type="radio" name="menu1_icon_pos" value="left" <?=$checked['main_menu1']['icon_pos']['left']?> />����</label>
				<label><input type="radio" name="menu1_icon_pos" value="right" <?=$checked['main_menu1']['icon_pos']['right']?> />������</label>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td>���� 2�� �޴�</td>
	<td>
		<table class="noline">
		<tr>
			<td>�ؽ�Ʈ</td>
			<td><script type="text/javascript">initPicker("menu2_text_color","<?=$arr_basicscreen['main_menu2']['text_color']?>",24);</script></td>
		</tr>
		<tr>
			<td>����</td>
			<td><script type="text/javascript">initPicker("menu2_bg_color","<?=$arr_basicscreen['main_menu2']['bg_color']?>",24);</script></td>
		</tr>
		<tr>
			<td>������</td>
			<td>
				<label><input type="radio" name="menu2_icon_pos" value="left" <?=$checked['main_menu2']['icon_pos']['left']?> />����</label>
				<label><input type="radio" name="menu2_icon_pos" value="right" <?=$checked['main_menu2']['icon_pos']['right']?> />������</label>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td>���̼�</td>
	<td>
		<table class="noline">
		<tr>
			<td>�ؽ�Ʈ</td>
			<td><script type="text/javascript">initPicker("mymenu_text_color","<?=$arr_basicscreen['my_menu']['text_color']?>",24);</script></td>
		</tr>
		<tr>
			<td>����</td>
			<td><script type="text/javascript">initPicker("mymenu_bg_color","<?=$arr_basicscreen['my_menu']['bg_color']?>",24);</script></td>
		</tr>
		<tr>
			<td>������</td>
			<td>
				<label><input type="radio" name="mymenu_icon_pos" value="left" <?=$checked['my_menu']['icon_pos']['left']?> />����</label>
				<label><input type="radio" name="mymenu_icon_pos" value="right" <?=$checked['my_menu']['icon_pos']['right']?> />������</label>
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
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���θ� App�� Ÿ��Ʋ, �޴��� �� �޴�����Ʈ�� ���� �� ������ ��ġ�� ������ �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">Ÿ��Ʋ �̹����� ���� ������� 100px * 30px �̸�, Ÿ��Ʋ �̹����� �����Ͻ� ��� ���θ� Ÿ��Ʋ�� ����˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�޴��� ������ ��� �� ������ ���θ� App ī�װ� �������� �Ͻ� �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>