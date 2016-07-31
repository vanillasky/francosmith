<?

$location = "전자세금계산서 관리 > 전자세금계산서 가입하기";
include "../_header.php";

if ( isset($_POST[agree]) === false && isset($_POST[agreeDacom]) === false ) // 이용약관 동의를 구한다.
{
	include dirname(__FILE__) . '/etax.requestAgree.php';
	exit;
}

include_once "../../lib/json.class.php";
$json = new Services_JSON();
$param = array();
$param['compName'] = $cfg[compName];
$param['ceoName'] = $cfg[ceoName];
$param['compSerial'] = $cfg[compSerial];
$param['service'] = $cfg[service];
$param['item'] = $cfg[item];
$param['email'] = $cfg[adminEmail];
$param['phone'] = array($tmp[(count($tmp = explode("-",$cfg[compPhone])) - 3)], $tmp[(count($tmp) - 2)], $tmp[(count($tmp) - 1)]);
$param['address'] = $cfg[address];
$param['return_url']	= "http://{$_SERVER[HTTP_HOST]}" . str_replace(basename($_SERVER[PHP_SELF]), "tax_indb.php?mode=request", $_SERVER[PHP_SELF]); # 결과 수신 URL
$param = $json->encode($param);

?>
<script src="../tax.ajax.js"></script>

<form name="form" onsubmit="return WRS.request();">
<input type="hidden" name="godosno" value="<?=$godo[sno]?>">
<input type="hidden" name="userid" value="<?=sprintf("GODO%05d", $godo[sno])?>">

<div class="title title_top">전자세금계산서 가입하기 <span>LG데이콤 전자세금계산서 웹택스21에 가입합니다.</span></div>
<table class="tb">
<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
<tr>
	<td>위탁자 아이디</td>
	<td><?=sprintf("CGO_GODO%05d", $godo[sno])?> <span class=small style="margin-left:43px"><font color=#5B5B5B>(WebTax21 로그인정보)</font></span></td>
	<td>비밀번호</td>
	<td><input type="password" name="password" required label="비밀번호"> <span class=small><font color=#5B5B5B>(WebTax21 로그인정보)</font></span></td>
</tr>
<tr>
	<td>사업자번호</td>
	<td colspan=3><input type="text" name="compSerial" required label="사업자번호"> <span class=small>ex) 123-45-67890</span></td>
</tr>
<tr>
	<td>상호명</td>
	<td><input type="text" name="compName" required label="상호명"></td>
	<td>대표자명</td>
	<td><input type="text" name="ceoName" required label="대표자명"></td>
</tr>
<tr>
	<td>업태</td>
	<td><input type="text" name="service" required label="업태"></td>
	<td>종목</td>
	<td><input type="text" name="item" required label="종목"></td>
</tr>
<tr>
	<td>이메일</td>
	<td colspan=3><input type="text" name="email" class="lline" required label="이메일"></td>
</tr>
<tr>
	<td>전화</td>
	<td><input type="text" name="phone[]" maxlength="4" style="width:40px;" required label="전화" onkeydown="onlynumber()">―<input type="text" name="phone[]" maxlength="4" style="width:40px;" required label="전화" onkeydown="onlynumber()">―<input type="text" name="phone[]" maxlength="4" style="width:40px;" required label="전화" onkeydown="onlynumber()"></td>
	<td>핸드폰</td>
	<td><input type="text" name="mobile[]" maxlength="4" style="width:40px;" required label="핸드폰" onkeydown="onlynumber()">―<input type="text" name="mobile[]" maxlength="4" style="width:40px;" required label="핸드폰" onkeydown="onlynumber()">―<input type="text" name="mobile[]" maxlength="4" style="width:40px;" required label="핸드폰" onkeydown="onlynumber()"></td>
</tr>
<tr>
	<td>주소</td>
	<td colspan=3><input type="text" name="address" style="width:60%" value="" required label="주소"></td>
</tr>
</table>

<div class="button" id="avoidSubmit">
<input type="image" src="../img/btn_confirm.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_tip">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">위 기재된 항목을 빠짐없이 기재해 주세요.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><b>비밀번호</b>는 LG데이콤webtax21 홈페이지에 로그인할 때 사용됩니다. 원하시는 비밀번호를 기재해 주세요.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>


<script language="javascript"><!--
var param = eval( '(<?=$param?>)' );
WRS.init_set();
--></script>


<? include "../_footer.php"; ?>