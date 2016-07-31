<?
/**
	2011-05-17 by x-ta-c

*/
$location = "상품관리 > 상품 재입고 알림 설정";
include "../_header.php";
?><div class="title title_top">상품 재입고 알림 설정<span>상품 재입고 알림 서비스를 신청한 고객들에게 SMS 메시지를 발송 및 설정할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=32')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div><?
include "_form.stocked_noti_config.php";
?>
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품 재입고 알림 서비스를 신청한 고객들에게 발송할 SMS 메시지를 설정할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">설정된 SMS 메시지 기본값은 상품 재입고 알림 신청자 리스트에 기본으로 노출되며, 상품 재입고 현황에 따라 수정할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />SMS 포인트가 충전되어 있어야 발송이 가능합니다. <a href="../member/sms.pay.php"><font color=white><u>[SMS 포인트 충전하기]</u></font></a> 에서 충전하세요</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<?
include "../_footer.php"; ?>



