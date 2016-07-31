<?php

$location = '회원관리 > 소셜로그인관리';

include '../_header.php';
include '../../lib/SocialMember/SocialMemberServiceLoader.php';

$requestVar = array(
	'code' => 'service_social_login'
);

?>

<script type="text/javascript">
jQuery(document).ready(function(){
	var isEnabled = <?php echo $socialMemberService->isEnabled() ? 'true' : 'false'; ?>;
	chgifrm("intro");
	<?php foreach ($socialMemberService->getEnabledServiceList() as $serviceCode) { ?>
	enableService("<?php echo strtolower($serviceCode); ?>");
	<?php } ?>
});

function chgifrm(name)
{
	var pgIfrm = document.getElementById("snsifrm");
	if (pgIfrm) {
		if (window.useTab) window.useTab.className = "";
		if (name === "intro") {
			pgIfrm.src = "../proc/remote_godopage.php?<?php echo http_build_query($requestVar); ?>";
		}
		else {
			pgIfrm.src = "adm_member_social_login." + name + ".php";
		}
		window.useTab = document.getElementById("tab-" + name);
		if (window.useTab) window.useTab.className = "active";
	}
}
function enableService(name)
{
	window.useService = document.getElementById("use-" + name);
	if (window.useService) window.useService.innerHTML = "[사용중]";
}
function disableService(name)
{
	window.useService = document.getElementById("use-" + name);
	if (window.useService) window.useService.innerHTML = "";
}
</script>

<style type="text/css">
#pgtab td{
	cursor: pointer;
	font-weight: bold;
	background: #ffffff;
	color: #627dce;
	border: solid #627dce 1px;
}
#pgtab td.active{
	background: #627dce;
	color: #ffffff;
}
#pgtab .use-sign{
	color: #ff0000;
	font-size: 11px;
	margin-left: 3px;
}
#pgtab .active .use-sign{
	color: #ffffff;
}
</style>

<div class="title title_top">
	소셜로그인 사용 설정 <span>SNS계정을 이용하여 쇼핑몰에 로그인할 수 있는 기능을 제공합니다.</span>
	<a href="javascript:manual('<?php echo $guideUrl; ?>board/view.php?id=member&no=22')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
</div>

<table style="border-collapse: collapse; border: solid 5px #627dce;" width="100%">
	<tr>
		<td colspan="10" align="center" style="padding: 10px 0px 10px 12px; color: #627dce">소셜로그인에 사용할 SNS를 선택해 주세요</td>
	</tr>
	<tr align="center" height="40" id="pgtab">
		<td id="tab-intro" width="50%" onclick="chgifrm('intro');">소셜로그인이란?<span id="use-intro" class="use-sign"></span></td>
		<td id="tab-facebook" width="50%" onclick="chgifrm('facebook');">페이스북<span id="use-facebook" class="use-sign"></span></td>
	</tr>
</table>

<div style="padding-top: 20px"></div>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>
			<iframe id="snsifrm" width="100%" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="10" scrolling="no"></iframe>
		</td>
	</tr>
</table>

<?php

include '../_footer.php';

?>