<?

$location = "오픈마켓 다이렉트 서비스 > 배송정책";
$scriptLoad.='<script src="./js/common.js"></script>';
include "../_header.php";

### 환경설정
@include "../../conf/openmarket.php";

if (isset($omCfg) === false){
	$omCfg['ship_type'] = '0';
	$omCfg['ship_pay'] = 'Y';
}

$checked['ship_type'][$omCfg['ship_type']] = "checked";
$checked['ship_pay'][$omCfg['ship_pay']] = "checked";

if ($omCfg['ship_type'] == '0'){
	$omCfg['ship_price_0'] = $omCfg['ship_price'];
}
else if ($omCfg['ship_type'] == '5'){
	$omCfg['ship_price_5'] = $omCfg['ship_price'];
	$omCfg['ship_base_5'] = $omCfg['ship_base'];
}
else if ($omCfg['ship_type'] == '4'){
	$omCfg['ship_price_4'] = $omCfg['ship_price'];
	$omCfg['ship_base_4'] = $omCfg['ship_base'];
}

?>

<div class="title title_top">배송정책 <span>오픈마켓 판매관리로 상품전송시, 공통으로 들어가는 정보를 설정합니다.</div>
<div id="useMsg"><script>callUseable('useMsg');</script></div>

<form method="post" action="../openmarket/indb.php">
<input type="hidden" name="mode" value="set">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr height=120>
	<td>배송비 정책</td>
	<td>
	<table cellpadding="0" cellspacing="0">
	<col width="120">
	<tr height="25">
		<td><input type="radio" name="omCfg[ship_type]" value="3" class="null" <?=$checked['ship_type'][3]?> onclick="setShipDisabled();"> 무료</td>
		<td></td>
	</tr>
	<tr height="25">
		<td><input type="radio" name="omCfg[ship_type]" value="0" class="null" <?=$checked['ship_type'][0]?> onclick="setShipDisabled();"> 유료</td>
		<td><input type="text" name="omCfg[ship_price]" value="<?=$omCfg['ship_price_0']?>" size=8 class=right onkeydown="onlynumber()" disabled> 원 배송비 부과</td>
	</tr>
	<tr height="25">
		<td><input type="radio" name="omCfg[ship_type]" value="5" class="null" <?=$checked['ship_type'][5]?> onclick="setShipDisabled();"> 구매액설정</td>
		<td>
		총 구매액이 <input type="text" name="omCfg[ship_base]" value="<?=$omCfg['ship_base_5']?>" size=9 class=right onkeydown="onlynumber()" disabled> 원 이상일 때 배송비 무료, 미만일 때 <input type="text" name="omCfg[ship_price]" value="<?=$omCfg['ship_price_5']?>" size=8 class=right onkeydown="onlynumber()" disabled> 원 배송비 부과
		</td>
	</tr>
	<tr height="25">
		<td><input type="radio" name="omCfg[ship_type]" value="4" class="null" <?=$checked['ship_type'][4]?> onclick="setShipDisabled();"> 구매량설정</td>
		<td>
		총 구매량이 <input type="text" name="omCfg[ship_base]" value="<?=$omCfg['ship_base_4']?>" size=9 class=right onkeydown="onlynumber()" disabled> 개 이상일 때 배송비 무료, 미만일 때 <input type="text" name="omCfg[ship_price]" value="<?=$omCfg['ship_price_4']?>" size=8 class=right onkeydown="onlynumber()" disabled> 원 배송비 부과
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr height=35>
	<td>배송비 결제방식</td>
	<td>
		<input type="radio" name="omCfg[ship_pay]" value="Y" class="null" <?=$checked['ship_pay']['Y']?>> 선불
		<input type="radio" name="omCfg[ship_pay]" value="N" class="null" <?=$checked['ship_pay']['N']?>> 착불
	</td>
<tr height=60>
	<td>A/S 정보<br>(안내문구)</td>
	<td>
	<input name="omCfg[as_info]" style="width:500px;" class="line" maxlength="40" value="<?=htmlspecialchars($omCfg['as_info'])?>" onkeydown="chkLen(this, 40, 'vLength')" onkeyup="chkLen(this, 40, 'vLength')">
	(<span id="vLength">0</span>/40)
	<div class="small" style="color:#6d6d6d; padding-top:8px;">(A/S 연락처, 기간 등을 입력하세요. 한/영문 40자 이내로 입력하셔야 합니다.)</div>
	<script>_ID('vLength').innerHTML = document.getElementsByName('omCfg[as_info]')[0].value.length;</script>
	</td>
</tr>
</table>

<div style="height:20px"></div>

<table cellpadding="0" cellspacing="0" width="650">
<tr><td align="center"><input type="image" src="../img/btn_confirm.gif" class="null"></td>
</tr></table>

<div style="height:20px"></div>

</form>

<script language="javascript"><!--
function setShipDisabled()
{
	obj = document.getElementsByName('omCfg[ship_type]');
	for (i = 0; i < obj.length; i++){
		isDisabled = (obj[i].checked == true ? false : true);
		inputObj = obj[i].parentNode.parentNode.getElementsByTagName('td')[1].getElementsByTagName('input');
		for (j = 0; j < inputObj.length; j++){
			inputObj[j].disabled = isDisabled;
			inputObj[j].style.backgroundColor = (isDisabled ? '#DDDDDD' : '#FFFFFF');
		}
	}
}

setShipDisabled();
--></script>

<? include "../_footer.php"; ?>