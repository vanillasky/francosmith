<?
include "../_header.popup.php";

if($_POST['action'] == 'ok') {

	unset($_POST['action'], $_POST['x'], $_POST['y']);

	$choice_range = explode('_', $_POST['choice_range']);
	$_POST['range'] = $choice_range[0];
	$_POST['range_month'] = $choice_range[1];
	unset($_POST['choice_range']);

	$choice_collect = explode('_', $_POST['choice_collect']);
	$_POST['collect'] = $choice_collect[0];
	$_POST['collect_month'] = $choice_collect[1];
	unset($_POST['choice_collect']);

	require_once("../../lib/qfile.class.php");
	$qfile = new qfile();

	$qfile->open("../../conf/config.populate.php");
	$qfile->write("<? \n");
	$qfile->write("\$cfg_populate = array( \n");
	foreach ($_POST as $k=>$v) $qfile->write("'$k' => '$v', \n");
	$qfile->write(") \n;");
	$qfile->write("?>");
	$qfile->close();

	// 캐시 초기화
	@unlink('../../data/statistics/populate_goods_data.cached.txt');

	echo "
	<script>
	alert('저장되었습니다');
	self.location.href='iframe.populate.setting.php';
	</script>
	";
	exit;

}

include "../../lib/populate.class.php";
$cfg_populate = populate::getConf();
?>
<script>
function copy_txt(val){
	window.clipboardData.setData('Text', val);
	alert( '치환코드를 복사했습니다. \n원하는 곳에 붙여넣기(Ctrl+V)를 하시면 됩니다.' );
}
function chkType() {
	var objTypeLabel = document.getElementById('typeLabel');
	var eleType = document.getElementsByName('type');
	if (eleType[0].checked) {
		objTypeLabel.innerHTML = '상품 판매 데이터';
		document.getElementsByName('choice_collect')[0][0].disabled = false;
	}
	else {
		objTypeLabel.innerHTML = '페이지뷰 데이터';
		document.getElementsByName('choice_collect')[0][0].disabled = true;
	}
}
</script>
<form id="frmPopulate" action="<?=$_SERVER['PHP_SELF']?>" method="post">
<input type="hidden" name="action" value="ok">

<div class="title title_top">인기상품 노출설정<span> 쇼핑몰에 상품 판매 순위 또는 가장 많이 본 상품순위 노출을 설정합니다. </span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=20')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table class="tb">
<col class="cellC">
<col class="cellL">
<tr>
	<td>순위타입 선택</td>
	<td>
		<label><input type="radio" name="type" onclick='chkType()' style="border:0px" <?=$cfg_populate['type'] == 'order' ? 'checked' : '' ?> value="order"> 상품 판매 순위</label>
		<label><input type="radio" name="type" onclick='chkType()' style="border:0px" <?=$cfg_populate['type'] == 'pageview' ? 'checked' : '' ?> value="pageview"> 페이지뷰(많이본 상품) 순위</label>
	</td>
</tr>
<tr>
	<td>노출 순위 선택</td>
	<td>
		1위 ~ <select name="limit"><? for ($i=1;$i<=20;$i++) { ?><option value="<?=$i?>" <?=$cfg_populate['limit'] == $i ? 'selected' : '' ?>><?=$i?></option><? } ?></select>
	</td>
</tr>
<tr>
	<td>갱신주기 선택</td>
	<td>
		등록 시점을 기준으로
		<select name="choice_range">
		<option <?=$cfg_populate['range'] == 'hour' ? 'selected' : '' ?> value="hour"> 1시간</option>
		<option <?=$cfg_populate['range'] == 'week' ? 'selected' : '' ?> value="week"> 1주일</option>
		<? for ($i=1;$i<=12;$i++) { ?><option value="month_<?=$i?>" <?=$cfg_populate['range'] == 'month' && $cfg_populate['range_month'] == $i ? 'selected' : '' ?>><?=$i?>개월</option><? } ?>
		</select>
		마다 순위를 갱신함.
	</td>
</tr>
<tr>
	<td>수집기간 선택</td>
	<td>
		갱신 시점을 기준으로
		<select name="choice_collect">
		<option <?=$cfg_populate['collect'] == 'hour' ? 'selected' : '' ?> value="hour"> 1시간</option>
		<option <?=$cfg_populate['collect'] == 'week' ? 'selected' : '' ?> value="week"> 1주일</option>
		<? for ($i=1;$i<=12;$i++) { ?><option value="month_<?=$i?>" <?=$cfg_populate['collect'] == 'month' && $cfg_populate['collect_month'] == $i ? 'selected' : '' ?>><?=$i?>개월</option><? } ?>
		</select>
		동안의 '<span id="typeLabel" style="font-weight:bold;">상품 판매 데이터</span>'를 수집하여 순위를 결정함.
	</td>
</tr>
<tr>
	<td>품절상품 선택</td>
	<td>
		<label><input type="radio" name="include_soldout" style="border:0px" <?=$cfg_populate['include_soldout'] == '0' ? 'checked' : '' ?> value="0"> 품절상품 제외</label>
		<label><input type="radio" name="include_soldout" style="border:0px"<?=$cfg_populate['include_soldout'] == '1' ? 'checked' : '' ?>  value="1"> 품절상품 포함</label>
	</td>
</tr>

<tr>
	<td>템플릿 형태</td>
	<td>
		<fieldset><legend><label><input type="radio" name="design" style="border:0px" <?=$cfg_populate['design'] == 'expand' ? 'checked' : '' ?> value="expand"> 펼침형</label></legend>
		<span class="extext">타이틀 부분과 순위부분이 펼쳐진 상태로 고정되어 노출됩니다.</span>
		</fieldset>

		<fieldset><legend><label><input type="radio" name="design" style="border:0px" <?=$cfg_populate['design'] == 'rollover' ? 'checked' : '' ?> value="rollover"> 롤오버형</label></legend>
		<span class="extext">타이틀 부분은 순위가 자동으로 돌아가면서 보여지며 타이틀에 마우스오버시 순위부분이 펼쳐보여집니다.</span>
		</fieldset>
	</td>
</tr>

<tr>
	<td>치환코드</td>
	<td>
		<div style="padding-top:5;">{#populate} <img class="hand" src="../img/i_copy.gif" onclick="copy_txt('{#populate}')" alt="복사하기" align="absmiddle"/></div>
		<div style="padding-top:5;" class="extext">치환코드를 복사하여 원하는 페이지 위치에 '붙여넣기(Ctrl+V)'하여 사용하시면 편리합니다.</div>

	</td>
</tr>
</table>

<div id="MSG01">
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">인기상품 순위를 쇼핑몰에 노출하여  주력상품을 더욱 부각시키고 인지도와  판매율을 높일 수 있는 기능입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품홍보 및 마케팅시 활용하시면 더욱 효과적입니다.</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">템플릿 디자인은 디자인페이지 > 기타페이지 > 인기상품 순위 펼침형 / 인시강품 순위 롤오버형 페이지에서 수정 및 편집이 가능 합니다.</td></tr>
</table>
</div>
<script>
cssRound('MSG01')
</script>


<div class="button">
<input type=image src="../img/btn_register.gif">
</div>

</form>
<script>
chkType();
table_design_load();
setHeight_ifrmCodi();
</script>