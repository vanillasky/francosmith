<?

$location = "인터파크 샵플러스 입점 > 샵플러스 입점신청 / 진행상황";
$scriptLoad='<script src="./js/request.js"></script>';
include "../_header.php";

include_once "../../lib/json.class.php";
$json = new Services_JSON();
$param = array();
$param['shopUrl'] = $cfg[shopUrl];
$param['shopName'] = $cfg[compName];
$param['ceoName'] = $cfg[ceoName];
$param['compSerial'] = $cfg[compSerial];
$param['email'] = $cfg[adminEmail];
$param['phone'] = array($tmp[(count($tmp = explode("-",$cfg[compPhone])) - 3)], $tmp[(count($tmp) - 2)], $tmp[(count($tmp) - 1)]);
$param['fax'] = array($tmp[(count($tmp = explode("-",$cfg[compFax])) - 3)], $tmp[(count($tmp) - 2)], $tmp[(count($tmp) - 1)]);
$param = $json->encode($param);

?>

<form name=form onsubmit="return ( IRS.putMerchant() ? false : false );">
<input type=hidden name=godosno value="<?=$godo[sno]?>">
<input type=hidden name=shopName required label="상호(쇼핑몰)명">
<input type=hidden name=ceoName required label="대표자명">


<div class="title title_top">샵플러스 입점신청하기 <span>인터파크 샵플러스 서비스를 신청합니다.</span></div>
<table class=tb>
<col class=cellC><col class=cellL width=330><col class=cellC><col class=cellL>
<tr>
	<td>상점아이디</td>
	<td colspan=3><?=sprintf("GODO%05d", $godo[sno])?></td>
</tr>
<tr>
	<td>상호(쇼핑몰)명</td>
	<td id=shopName0></td>
	<td>도메인</td>
	<td id=domain0></td>
</tr>
<tr>
	<td>회사명</td>
	<td><input type=text name=compName required label="회사명"> <input type=checkbox onclick="IRS.ctrl_field(this.checked)" checked class=null> 상호(쇼핑몰)명과 동일합니다</td>
	<td>전화</td>
	<td><input type=text name=phone[] style="width:40px;" required label="전화" onkeydown="onlynumber()">―<input type=text name=phone[] style="width:40px;" required label="전화" onkeydown="onlynumber()">―<input type=text name=phone[] style="width:40px;" required label="전화" onkeydown="onlynumber()"></td>
</tr>
<tr>
	<td>대표자명</td>
	<td id=ceoName0></td>
	<td>핸드폰</td>
	<td><input type=text name=mobile[] style="width:40px;" onkeydown="onlynumber()">―<input type=text name=mobile[] style="width:40px;" onkeydown="onlynumber()">―<input type=text name=mobile[] style="width:40px;" onkeydown="onlynumber()"></td>
</tr>
<tr>
	<td>사업자번호</td>
	<td><input type=text name=compSerial required label="사업자번호"> <font class=small color=444444>예) 123-45-67890</font></td>
	<td>팩스</td>
	<td><input type=text name=fax[] style="width:40px;" onkeydown="onlynumber()">―<input type=text name=fax[] style="width:40px;" onkeydown="onlynumber()">―<input type=text name=fax[] style="width:40px;" onkeydown="onlynumber()"></td>
</tr>
<tr>
	<td>이메일</td>
	<td colspan=3><input type=text name=email class=lline required label="이메일"> <font class=small color=444444>(승인된 후 계약서와 입점사항들이 발송되오니 정확히 기재하세요.)</font></td>
</tr>
<tr>
	<td>카테고리</td>
	<td colspan=3>
		<select name="cate[]" required label="카테고리" onchange="IRS.getShopCategory(1, this.value)"><option value="">-- 카테고리 선택 --</option></select>
		<select name="cate[]" required label="세부유형"><option value="">-- 세부유형 선택 --</option></select>
	</td>
</tr>
<tr>
	<td>인터파크 주문<br>배송비설정</td>
	<td colspan=3>
		총 구매액이 <input type=text name=delvCostCondition style="width:60px;text-align:center" onkeydown="onlynumber()"> 원 이상인 경우 무료, 미만인 경우 <input type=text name=delvCostBasic style="width:60px;text-align:center" onkeydown="onlynumber()"> 원 구매자 부담<br>
		<font class=small color=444444>예) 총 구매액이 30,000원 이상인 경우 무료, 미만인 경우 2,500원 구매자 부담</font>
	</td>
</tr>
</table>

<div class="button" id="avoidSubmit">
<input type=image src="../img/btn_confirm.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">본 입점 신청서에 기재하신 내용은 입점승인을 위한 참고 자료로만 사용됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><b>인터파크 담당자가 연락할 수 있는 담당자 연락처를 입력</b>하여 주시기 바랍니다.</td></tr>
<tr><td>&nbsp; 담당자 연락처가 다를 경우 입점 탈락 및 입점 승인의 시일이 많이 소요됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><b>필수 서류를 꼭 확인하시고 준비</b>하여 주시기 바랍니다.</td></tr>
<tr><td>&nbsp; 입점 신청을 하신 쇼핑몰은 <b>인터파크쇼핑(샵플러스)에서 심사를 통하여 입점 필요서류 요청 연락</b>을 드립니다.</td></tr>
<tr><td>&nbsp; 정확한 서류를 준비하여 보내주시지 않으면 입점 시 많은 시일이 소요될 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<script language="javascript"><!--
var param = eval( '(<?=$param?>)' );
IRS.init_set();
--></script>


<? include "../_footer.php"; ?>