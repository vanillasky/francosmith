<?php

include "../_header.popup.php";
@include "../../conf/pg_cell.danal.cfg.php";

$config = Core::loader('config');
$danal = Core::loader('Danal');

$shopConfig = $config->load('config');
if (empty($danalCfg)) {
	$danalCfg = $config->load('danal');
}
$checked = array(
	'serviceType' => array($danalCfg['serviceType'] => ' checked="checked"'),
);
?>
<script type="text/javascript">
window.onload = function()
{
	resizeFrame();
};

var IntervarId;
function resizeFrame()
{

	var oBody = document.body;
	var oFrame = parent.document.getElementById("pgifrm");
	var i_height = oBody.scrollHeight + (oFrame.offsetHeight-oFrame.clientHeight);
	oFrame.style.height = i_height;
	oFrame.height = i_height;

	if ( IntervarId ) clearInterval( IntervarId );
}
</script>
<div class="title title_top">
다날 설정 <a href="javascript:manual('<?php echo $guideUrl; ?>board/view.php?id=basic&no=46')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>
<form action="<?php echo $shopConfig['rootDir']; ?>/admin/basic/adm_basic_pgCell.danal.indb.php" method="post">
	<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
		<col class="cellC"><col class="cellL">
		<tr>
			<td height="40">결제 사용 여부</td>
			<td class="noline">
				<div style="margin: 8px 0; padding-left: 10px;">
					<input id="svc-type-10" type="radio" name="serviceType" value="10"<?php echo $checked['serviceType']['10']; ?> required label="서비스환경"/>
					<label for="svc-type-10">사용함</label>
					<input id="svc-type-no" type="radio" name="serviceType" value="no"<?php echo $checked['serviceType']['no']; ?>/>
					<label for="svc-type-no">사용안함</label>
				</div>

				<div style="margin: 8px 0; padding: 10px 10px 0 0;" class="red">
					<ul style="padding: 0 0 0 20px; margin: 5px 0 0 0;">
						<li style="margin-bottom: 5px;">
							사용함 : 쇼핑몰에 접속한 모든 사용자들이 서비스를 사용할 수 있으며, 실제로 PC와 모바일에서 결제가 이루어집니다.
						</li>
						<li>
							사용안함 : 서비스를 비활성화시켜 결제가 노출되지 않습니다.
						</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<td height="40">
				다날 <span class="blue">PG ID</span>
			</td>
			<td style="padding-left:10px;">
				<? if ($danalCfg['S_CPID']) { 
					echo $danalCfg['S_CPID']; ?> &nbsp; <? if ($danalCfg['pg-centersetting'] === 'Y') { ?><span class="extext">자동설정 완료 <?php } ?></span>
				<? } else {?>
					<span class="extext">다날 서비스 신청시 발급되는 아이디입니다. <a href='http://www.godo.co.kr/echost/power/add/payment/mobile-pg-intro.gd' target="_blank">[다날 신청 바로가기]</a> <? } ?></span>
			</td>
		</tr>
	</table>

	<div class="button">
		<input type="image" src="../img/btn_save.gif">
		<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
	</div>
</form>
<!-- 궁금증 해결 : Start -->
<div id="MSG01">
	<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
		<tr>
			<td>
				<ol>
					<li style="margin-bottom: 5px;">
						2015년 08월 27일 이전 신청의 경우 '사용함' 설정 시 모바일샵(v1/v2) 스킨패치를 하시기 바랍니다. (PC는 스킨수정없이 사용 가능) 스킨패치 방법은 매뉴얼을 참고하시기 바랍니다. <a href="javascript:manual('<?php echo $guideUrl; ?>board/view.php?id=basic&no=46')"><img src="../img/btn_q.gif" border="0" align="absmiddle"/></a>
					</li>
					<li>
						결제취소 가능시간 : 결제 월 말일까지만 취소 처리 가능하므로 결제 월 이후에는 쇼핑몰(관리자)이 구매자에게 다른 수단(무통장, 적립금 등)으로<br/>
						환불 처리해야 합니다.<br/>
						Ex1) 2015년 8월 01일 승인 > 8월 31일 24시까지만 결제 취소 가능<br/>
						Ex2) 2015년 8월 31일 승인 > 9월 1일 결제 취소 요청 할 경우 결제 취소 처리 불가 > 구매자에게 다른수단(무통장,적립금 등)으로 환불처리만 가능
					</li>
				</ol>
			</td>
		</tr>
	</table>
</div>
<script type="text/javascript">cssRound('MSG01');</script>