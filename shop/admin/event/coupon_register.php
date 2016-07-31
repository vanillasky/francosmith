<?
if (isset($_GET['couponcd'])) $_GET['mode'] = "modify";
if (! isset($_GET['mode'])) $_GET['mode'] = "register";

if($_GET['mode'] == 'register'){
	$hidden['sort'] = "style='display:none'";
	$location = "쿠폰발행관리 > 쿠폰만들기";
	$msg = "<div class='title title_top'>쿠폰만들기<span>고객에게 발급할 쿠폰을 만듭니다.";
}else{
	$location = "쿠폰발행관리 > 쿠폰발급내역관리";
	$msg = "<div class='title title_top'>쿠폰수정하기<span>고객에게 발급할 쿠폰을 수정합니다.";
}

include "../_header.php";
?>
<?=$msg?> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=12')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<?
include "_form.coupon.php";
?>
<div style="padding-top:10px"></div>
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">회원직접다운로드쿠폰을 제외한 다른 쿠폰들은 발급받은 회원1명 당 쿠폰사용은 1회로 제한 됩니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<? include "../_footer.php"; ?>