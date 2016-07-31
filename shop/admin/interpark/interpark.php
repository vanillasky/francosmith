<?

$location = "인터파크 오픈스타일 입점 > 샵플러스 환경설정";
include "../_header.php";

### 환경설정
@include "../../conf/interpark.php";
$checked[ippSubmitYn][$inpkCfg[ippSubmitYn]] = "checked";

### 특이사항
ob_start();
@include "../../conf/interpark_spcaseEd.php";
$spcaseEd = ob_get_contents();
ob_end_clean();

?>

<div class="title title_top">샵플러스 환경설정 <span>인터파크 상점정보 및 환경을 설정할 수 있습니다.</div>

<div style="padding-top:5px"></div>


<div style="padding:8px 13px;background:#f7f7f7;border:3px solid #C6C6C6;margin-bottom:18px;" id="goodsInfoBox">
<div style="padding-top:2"><font color="#EA0095"><b>ㆍ인터파크와 입점계약을 맺은 후 승인이 되어야 아래 상점정보가 보이게 됩니다.</b></font></div>
<div style="padding-top:2"><font color="#EA0095"><b>ㆍ가격비교등록여부 또한 승인이 되어야 가격비교 제휴사로의 상품전송이 가능합니다.</b></font></div>
<!--<div style="padding-top:2"><font  color=777777>인터파크와 입점계약을 맺은 이후 상점은 모든 상품들의 분류를 인터파크분류와 매칭시켜야만 합니다.</div>-->
</div>


<form method="post" action="../interpark/indb.php">
<input type="hidden" name="mode" value="set">
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>인터파크 상점정보</td>
	<td>
	<table>
	<col width="130">
	<tr><td colspan=2 height=5></td></tr>
	<tr>
		<td>ㆍ접속도메인</td>
		<td><font color=EA0095 face=verdana><b><?=($inpkCfg[domain] ? "<a href='http://{$inpkCfg[domain]}' target='_blank'><font color=EA0095>{$inpkCfg[domain]}</font></a>" : '―')?></b></font></td>
	</tr>
	<tr><td colspan=2 height=5></td></tr>
	<tr>
		<td>ㆍ업체번호</td>
		<td><font color=EA0095 face=verdana><b><?=($inpkCfg[entrNo] ? $inpkCfg[entrNo] : '―')?></b></font></td>
	</tr>
	<tr><td colspan=2 height=5></td></tr>
	<tr>
		<td>ㆍ공급계약일련번호</td>
		<td><font color=EA0095 face=verdana><b><?=($inpkCfg[ctrtSeq] ? $inpkCfg[ctrtSeq] : '―')?></b></font></td>
	</tr>
	<tr><td colspan=2 height=5></td></tr>
	</table>

	<div style="color:#0074BA; padding:5 0 7 10px;" >
	(인터파크 상점정보는 인터파크로 상품을 등록할 때 사용되므로 반드시 필요합니다.<br>
	'서비스시작' 후에도 상점정보가 반영되지 않았다면 인터파크로 연락주세요.)
	</div>
	</td>
</tr>
<tr height=35>
	<td>가격비교등록여부</td>
	<td><input type="checkbox" name="inpkCfg[ippSubmitYn]" value="Y" <?=$checked[ippSubmitYn][Y]?> class=null> 인터파크로 상품을 등록할 때, 가격비교 제휴사에도 상품정보를 자동으로 전송합니다.</td>
</tr>
<tr>
	<td>특이사항<br>(안내문구)</td>
	<td>
	<textarea name=spcaseEd style="width:100%;height:250px" type=editor><?=htmlspecialchars($spcaseEd)?></textarea>
	<!-- 웹에디터 활성화 스크립트 -->
	<script src="../../lib/meditor/mini_editor.js"></script>
	<script>mini_editor("../../lib/meditor/");</script>

	<div style="color:#0074BA; padding:5 0 7 10px;" >
	(인터파크 상품상세페이지에서 "특이사항" 코너에 출력되는 안내문구입니다.<br>
	상품상세정보 외에 주의사항, 배송/교환/반품안내 등 고객에게 전달할 사항을 기입합니다.)
	</div>
	</td>
</tr>
</table>

<div style="height:20px"></div>

<table cellpadding=0 cellspacing=0 width=650>
<tr><td align=center><input type=image src="../img/btn_confirm.gif" class=null></td>
</tr></table>

<div style="height:20px"></div>

</form>

<? include "../_footer.php"; ?>