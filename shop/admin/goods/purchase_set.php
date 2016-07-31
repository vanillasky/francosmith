<?
	$location = "사입처관리 > 사입처 관리 사용 설정";
	include "../_header.php";
	@include "../../conf/config.purchase.php";
?>

<div class="title title_top">사입처 관리 사용 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=30')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>

<form method="post" action="./indb.purchase.php">
<input type="hidden" name="mode" value="pchs_set">
<table cellpadding="5" cellspacing="1" bgcolor="#E6E6E6" width="100%" border="0">
<colgroup>
	<col style="width:160px; color:#333333; background:#F6F6F6; font-weight:bold;"><col style="color:#000000; background:#FFFFFF;">
<colgroup>
<tr>
	<td>상품 사입처 연동</td>
	<td>
		<input type="radio" name="usePurchase" id="usePurchase1" style="border:0px;" value="Y"<?=($purchaseSet['usePurchase'] == "Y") ? " checked" : ""?> /> <label for="usePurchase1">사용</label>
		<input type="radio" name="usePurchase" id="usePurchase2" style="border:0px;" value="N"<?=($purchaseSet['usePurchase'] != "Y") ? " checked" : ""?> /> <label for="usePurchase2">사용 안 함</label>
		&nbsp; &nbsp; <span class="small" style="color:#6D6D6D;">상품의 사입처 연동 사용여부를 설정합니다.</span>
	</td>
</tr>
</table>

<div style="height:20px;"></div>

<div class="title title_top">상품 매진 알림 사용 설정 <span>상품 매진 알림 설정 시 사입처가 연동된 제품에만 적용 됩니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=30')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>

<table cellpadding="5" cellspacing="1" bgcolor="#E6E6E6" width="100%" border="0">
<col style="width:160px; color:#333333; background:#F6F6F6; font-weight:bold;"><col style="color:#000000; background:#FFFFFF;">
<tr>
	<td>상품 매진 알림 사용설정</td>
	<td>
		<input type="radio" name="soldoutAlarm" id="soldoutAlarm1" style="border:0px;" value="Y"<?=($purchaseSet['soldoutAlarm'] == "Y") ? " checked" : ""?> /> <label for="soldoutAlarm1">사용</label>
		<input type="radio" name="soldoutAlarm" id="soldoutAlarm2" style="border:0px;" value="N"<?=($purchaseSet['soldoutAlarm'] != "Y") ? " checked" : ""?> /> <label for="soldoutAlarm2">사용 안 함</label>
		&nbsp; &nbsp; <span class="small" style="color:#6D6D6D;">알람 사용여부를 설정합니다.</span>
	</td>
</tr>
<tr>
	<td>팝업 알림 사용 <input type="checkbox" name="popYn" id="popYn" style="border:0px;" value="1"<?=($purchaseSet['popYn'] == 1) ? " checked" : ""?> /></td>
	<td>
		관리자 로그인시 재고가 <input type="text" name="popStock" id="popStock" value="<?=$purchaseSet['popStock']?>" size="3" /> 개 미만인 경우 팝업창을 띄웁니다.
	</td>

</tr>
</table>

<div style="height:20px;"></div>

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td align="center"><input type="image" src="../img/btn_confirm.gif" class="null"></td>
</tr>
</table>

<div style="height:20px"></div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr>
	<td>사입처 연동 설정 시  [가격/적립금/재고 수정] , [빠른 재고 수정] 메뉴의 재고 입력 항목 대신 [사입 이력 등록] 기능이 사용 됩니다.</td>
</tr>
</table>
</div>
<script>cssRound('MSG01')</script>

</form>
<? include "../_footer.php"; ?>