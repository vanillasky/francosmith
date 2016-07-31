<?php
$location = '기본관리 > 개인정보 유효기간제 설정';
include '../_header.php';

$dormant = Core::loader('dormant');
//사용여부
$dormantUse = $dormant->checkDormantAgree();
//설정일
$dormantAgreeDate = $dormant->getDormantAgreeDate();
//휴면회원 전환대상 수
$dormantMemberCount = $dormant->getDormantMemberCount('dormantMemberAll');
//전체회원수
$memberTotalCount = $dormant->getDormantMemberCount('memberTotal');
?>
<style type="text/css">
.admin_dormant_config_define {
	font-family: dotum;
	font-size: 13px;
	width: 800px;
	height: 157px;
	background-image: url('../img/bg_dormant.jpg');
	background-repeat:no-repeat;
	background-size: auto;
	padding-bottom: 40px;
}
.admin_dormant_config_define .admin_dormant_config_define_subject {
	font-weight: bold;
	color: red;
	font-size: 16px;
	padding: 60px 0px 0px 100px;
	float: left;
}
.admin_dormant_config_define .admin_dormant_config_define_content {
	padding: 40px 20px 0px 0px;
	float: right;
}
.admin_dormant_config_careful {
	width: 100%;
	font-family: dotum;
	font-size: 13px;
}
.admin_dormant_config_careful ol li{
	list-style: disc;
	line-height: 150%;
}
.admin_dormant_config_button {
	width: 800px;
	margin-top: 80px;
	text-align: center;
}
.admin_dormant_config_startInformation {
	width: 800px;
	text-align: center;
	margin-top: 80px;
	font-size: 16px;
	font-weight: bold;
	color: blue;
}
</style>

<div class="title title_top">개인정보 유효기간제 설정<span>개인정보 유효기간제를 설정할 수 있습니다.<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=48')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>

<div class="admin_dormant_config_define">
	<div class="admin_dormant_config_define_subject">
		<div>개인정보</div>
		<div>유효기간제란?</div>
	</div>
	<div class="admin_dormant_config_define_content">
		<div>정보통신망 이용촉진 및 정보보호등에 관한 법률 제29조 2항 시행령 제16조에 따라,</div>
		<div>1년 이상 서비스 이용 기록이 없는 고객의 개인정보를 삭제하거나 별도로 분리 저장하여야</div>
		<div>합니다.</div>
		<div>개인정보 유효기간 만료 30일전에 메일 등으로 고객에게 반드시 사전 안내해야 합니다.</div>
		<div>(시행일 : 2015년 8월 18일)</div>
	</div>
</div>

<div><img src="../img/dormantInfo.png" border="0" /></div>

<table cellpadding="0" cellspacing="0" class="admin_dormant_config_careful">
<tr>
	<td>
		<ol>
			<li style="font-weight: bold; color: red;">관련 법령에 따라 1년 이상 접속하지 않은 회원의 개인정보는 반드시 삭제 혹은 분리저장 하셔야 합니다. </li>
			<li><strong>개인정보 유효기간제 사용 설정에 동의 하셔야 회원 개인정보 분리 저장기능을 사용할 수 있습니다.</strong><br />(2015년 10월 29일 이후에 개설된 쇼핑몰은 별도의 사용설정 없이 이용할 수 있습니다.)</li>
			<li>기능 사용 설정 시점에 <strong>1년 이상 접속하지 않은 회원은 사전 안내 없이 모두 휴면회원으로 분리 저장</strong>됩니다.<br />사전 안내 메일을 발송하지 않은 쇼핑몰에서는 이 점 염두하시어 기능 사용 설정 하시기 바랍니다. </li>
			<li>‘휴면계정 사전 안내 메일’ 발송 설정을 하시면 휴면계정 처리 30일 전 안내 메일을 자동으로 발송합니다. <strong><br /><a href="../member/email.cfg.php?mode=40" target="_blank">[휴면 전환 사전 안내 메일 설정 바로가기]</a></strong></li>
			<li style="font-weight: bold; color: red;">기능 사용 설정 다음날부터 관리자페이지에 로그인 시 휴면 계정 전환 및  휴면계정 사전 안내 메일 발송을 자동으로 실행합니다. </li>
			<li style="font-weight: bold; color: red;">관리자 페이지에 로그인 하지 않으면 프로세스가 실행되지 않으므로 주의하시기 바랍니다.<br />(미 처리된건들은 이후 관리자 접속 시 한번에 처리합니다.) </li>
			<li>휴면회원으로 처리된 회원은 회원리스트 화면에 노출되지 않으며, ‘휴면회원 관리’ 메뉴에서 조회 가능합니다. <strong><br /><a href="../dormant/adm_dormant_dormantMemberList.php" target="_blank">[휴면 회원 관리 메뉴 바로가기]</a></strong></li>
			<li>휴면 회원으로 분리 보관된 회원 정보는 권한이 있는 관리자만 열람 가능합니다. <strong><br /><a href="../basic/adminGroup.php" target="_blank">[관리자 권한 설정 바로 가기]</a></strong></li>
			<?php if($dormantUse === false){ ?><li style="font-weight: bold; color: red;">개인정보유효기간제 기능 설정 시 전체회원 <?php echo number_format($memberTotalCount); ?>명 중 <strong><?php echo number_format($dormantMemberCount); ?></strong>명이 오늘 휴면계정으로 전환 될 예정입니다. </li><?php } ?>
			<li>휴면회원으로 분리 저장된 회원에게는 마케팅성 email이나 SMS를 발송할 수 없습니다.</li>
		</ol>
	</td>
</tr>
</table>
<?php
if($dormantUse === false){
?>
<table cellpadding="0" cellspacing="0" class="admin_dormant_config_button">
<tr>
	<td><img src='../img/btn_dormantSetting.gif' id="submitButton" class="hand" border="0" /></td>
</tr>
</table>
<?php
}
else {
?>
<div class="admin_dormant_config_startInformation">개인정보 유효기간제 기능 사용 설정 일 : <?php echo $dormantAgreeDate; ?></div>
<?php
}
?>

<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery("#submitButton").click( function(){
		if(confirm("개인정보 유효기간제 기능 사용 시 접속한지 365일 이상 된 회원을 모두 휴면계정으로 전환합니다.\n사전 안내메일을 발송하지 않은 운영자 께서는 기능 설정에 유의하시기 바랍니다. 최초 기능 설정 후 비활성화 할 수 없으며, 이 작업은 시간이 오래 걸릴 수 있어 새벽 시간에 작업하시길 권장합니다.\n기능을 적용하시겠습니까?")){

			var nav = navigator.userAgent.toLowerCase();
			jQuery(window).bind("keydown",function(e){
				var event = e || window.event;
				if(event.keyCode == 116){
					if(nav.indexOf("chrome") != -1){
						return "휴면회원 전환 처리 중입니다. 새로고침을 하거나 브라우저를 닫을 경우 정상처리 되지 않을 수 있습니다.\n계속하시겠습니까?";
						if(event.preventDefault){
							event.preventDefault();
						}
						else {
							event.returnValue = false;
						}
					}
					else {
						if(!confirm("휴면회원 전환 처리 중입니다. 새로고침을 하거나 브라우저를 닫을 경우 정상처리 되지 않을 수 있습니다.\n계속하시겠습니까?")){
							if(event.preventDefault){
								event.preventDefault();
							}
							else {
								event.returnValue = false;
							}
						}
					}
				}
			});

			jQuery(window).bind("beforeunload",function(e){
				var event = e || window.event;
				if(nav.indexOf("chrome") != -1){
					return "휴면회원 전환 처리 중입니다. 새로고침을 하거나 브라우저를 닫을 경우 정상처리 되지 않을 수 있습니다.\n계속하시겠습니까?";
					if(event.preventDefault){
						event.preventDefault();
					}
					else {
						event.returnValue = false;
					}
				}
				else {
					if(!confirm("휴면회원 전환 처리 중입니다. 새로고침을 하거나 브라우저를 닫을 경우 정상처리 되지 않을 수 있습니다.\n계속하시겠습니까?")){
						if(event.preventDefault){
							event.preventDefault();
						}
						else {
							event.returnValue = false;
						}
					}
				}
			});

			showDormantProgressBar();

			var ajaxTransfer =  jQuery.ajax({
				method: "POST",
				url: "indb.php",
				data: { mode: 'dormantConfig', actionMode: 'agree'}
			});
			ajaxTransfer.done(function( data ) {
				var result = new Array();
				result = data.split("|");

				if(result[1]){
					alert(result[1]);
				}
				window.location.reload();
			});
			ajaxTransfer.fail(function() {
				alert("통신에러가 발생하였습니다.\n다시한번 시도하여 주세요.");
			});
			ajaxTransfer.always(function() {
				jQuery(window).unbind("keydown beforeunload");
				hiddenDormantProgressBar();
			});
		}
	});

	function showDormantProgressBar(){
		var progressImgMarginTop = Math.round((jQuery(window).height() - 116) / 2);

		jQuery("body").append('<div id="dormantProgressBar" style="position:absolute;top:0;left:0;background:#44515b;filter:alpha(opacity=80);opacity:0.8;width:100%;height:'+jQuery('body').height()+'px;cursor:progress;z-index:100000;margin:0 auto;text-align: center;"><img src="../img/admin_progress.gif" border="0" style="margin-top:'+progressImgMarginTop+'px;" /><div style="color: white; font-weight: bold;">휴면회원 전환 처리중입니다.<br /> 수분 ~ 수십분이 걸릴 수 있습니다.</div></div>');
	}

	function hiddenDormantProgressBar(){
		jQuery("#dormantProgressBar").remove();
	}
});
</script>

<?php include '../_footer.php'; ?>