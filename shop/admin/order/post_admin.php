<?php

$location = "택배연동 서비스 > 우체국택배정보";

include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";
include "../../lib/godopost.class.php";

$godopost = new godopost();

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}
$godopost->linked=1;
?>
<script type="text/javascript">
function popupGodoPostManualConfirm(ordno) {
	popupLayer('popup.godopost.manualconfirm.php');
}
</script>
<div class="title title_top">우체국택배 신청/관리<span>우체국택배  자동 연동서비스를 신청/ 관리하는 페이지 입니다.</span></div>

<br><br>

<iframe name="requestPostIfrm" src="http://www.godo.co.kr/service/godopost/regist.php?shopSno=<?=$godo['sno']?>&shopHost=<?=$_SERVER['HTTP_HOST']?>" frameborder="0" style="width:100%;height;500px" width="100%" height="500"></iframe>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">우체국 택배연동 서비스를 신청하시면, 담당자가 확인 후 승인처리를 해드립니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">신청 후, 승인완료까지 약 2~3일 정도 소요됩니다. </td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">우체국 택배연동 서비스 신청 전에 우체국 기업택배 가입 및 우체국 사업자회원가입이 완료되셔야 합니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<br><br>
<? if($godopost->linked): ?>
<div class="title">우체국택배 배송상태 자동 업데이트<span></span> </div>
- 우체국택배 배송상태를 쇼핑몰에 2시간마다 자동으로 업데이트 합니다.<br>
- 2시간마다 자동으로 배송상태를 확인하여 배송이 완료된 주문은 ‘배송완료’로 업데이트 됩니다.<br>
<br>
<br>
<br>
<div class="title">우체국택배 배송상태 수동 업데이트<span></span> </div>

<div style="margin:5px;text-indent:5px">
- 우체국택배 배송상태를 쇼핑몰에 수동 업데이트하려면, 아래 버튼을 클릭해 주세요.<br>
- 수동으로 배송상태를 확인하여 배송이 완료된 주문은 ‘배송완료’로 업데이트 됩니다.<br> 
</div>

<input type="button" value=" 배송상태 수동 업데이트 " onclick="popupGodoPostManualConfirm()">
<? endif; ?>


<? include "../_footer.php"; ?>