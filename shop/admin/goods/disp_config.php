<?
$location = "상품관리 > 진열페이지 설정";
include "../_header.php";
@include "../../conf/config.display.php";

if (!$displayCfg['displayType']) $displayCfg['displayType'] = 'consumer';

?>
<div class="title title_top">할인가격 노출설정<span> 진열 페이지에 노출될 가격 정보를 설정합니다. </span>
<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=49')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<form name="frm" method="post" action="indb.disp_config.php">
<table class=tb>
<col class=cellC>
<tr>
	<th rowspan=2>할인가격 노출설정</th>
	<td style="border-right-width: 0px; border-bottom-width: 0px; width:300px;">
		<div class="noline"><input type="radio" name="displayType" value="consumer" <?=$displayCfg['displayType'] === 'consumer' ? 'checked' : ''?> onclick="display_image()"/>소비자가격(취소선)과 판매가격으로 표시</div>
		<div class="noline"><input type="radio" name="displayType" value="discount" <?=$displayCfg['displayType'] === 'discount' ? 'checked' : ''?> onclick="display_image()"/>판매가격(취소선)과 상품별 할인가격으로 표시</div>
	</td>
	<td style="border-left-width: 0px; border-bottom-width: 0px;">
	<img name="displayImage">
	</td>
</tr>
<tr><td colspan=2 style="border-top-width: 0px;"><span class=small id="ext" style="display:none;"><font class=extext>※ 분류페이지 > 낮은/높은 가격순 상품정렬 시에는 판매가격 기준으로 정렬됩니다.</font></span></td></tr>
</table>
<div style="margin-top:15px"><span class=small><font class=extext><font style="color:red;"><b>※2015년 12월 23일 이전 제작 무료 스킨</b></font>을 사용하시는 경우 반드시 스킨패치를 적용해야 기능 사용이 가능합니다.</font></span><a href="http://www.godo.co.kr/customer_center/patch.php?sno=2287" class="extext" style="font-weight:bold"> [패치 바로가기]</a></div>
<div class="button">
<input type="image" src="../img/btn_save.gif" />
<a href="javascript:history.back();"><img src="../img/btn_cancel.gif" /></a>
</div>
</form>

<script type="text/javascript">
window.onload = function(){
	display_image();
}

function display_image() {
	var f = document.frm;
	if (f.displayType[0].checked) {
		f.displayImage.src = "../img/consumer_display.png";
		document.getElementById('ext').style.display = "none";
	}
	else if (f.displayType[1].checked) {
		f.displayImage.src = "../img/discount_display.png";
		document.getElementById('ext').style.display = "block";
	}
}

</script>

<? include "../_footer.php"; ?>