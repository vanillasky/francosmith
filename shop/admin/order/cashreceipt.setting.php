<?

$location = '현금영수증 서비스 > 현금영수증 발급설정';
include '../_header.php';
include '../../conf/config.php';
include "../../conf/config.pay.php";
if ($cfg['settlePg'] !== '' && file_exists('../../conf/pg.'. $cfg['settlePg'] .'.php')){
	include '../../conf/pg.'. $cfg['settlePg'] .'.php';
}

$pgs = array('inicis' => 'KG이니시스', 'inipay' => 'KG이니시스', 'allat' => '삼성올앳', 'allatbasic' => '삼성올앳', 'dacom' => 'LG U+', 'lgdacom' => 'LG U+', 'kcp'=>'KCP', 'agspay'=>'올더게이트', 'easypay'=>'이지페이', 'settlebank'=>'세틀뱅크');
$pgCompany = $pgs[ $cfg['settlePg'] ];
if ($pgCompany == '') $pgCompany = strtoupper($cfg['settlePg']);

if ($set['receipt']['publisher'] == '') $set['receipt']['publisher'] = 'buyer';
if ($set['receipt']['order'] == '') $set['receipt']['order'] = 'N';
if ($set['receipt']['auto'] == '') $set['receipt']['auto'] = 'N';
if ($set['receipt']['compType'] == '') $set['receipt']['compType'] = '0';

// 마이페이지이고 자동발급인경우 수동발급으로 변경
if ($set['receipt']['order'] == 'N' && $set['receipt']['auto'] == 'Y') {
	$set['receipt']['auto']	= 'N';
}

$checked['receipt'][$pg['receipt']] = 'checked';
$checked['publisher'][$set['receipt']['publisher']] = 'checked';
$checked['order'][$set['receipt']['order']] = 'checked';
$checked['auto'][$set['receipt']['auto']] = 'checked';
$selected['period'][$set['receipt']['period']] = 'selected';
$checked['compType'][$set['receipt']['compType']] = 'checked';
?>

<form method="post" action="../order/cashreceipt.indb.php">
<input type="hidden" name="mode" value="manage">

<div class="title title_top">현금영수증 발급설정 <span>설정된 PG사의 현금영수증을 사용하며, 별도 계약 필요없음</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=11')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>전자지불(PG)</td>
	<td><?=($pgCompany != '' ? $pgCompany : '<font color="#EA0095"><b>전자지불(PG)를 먼저 신청/설정하세요.</b></font>')?> &nbsp;&nbsp;<font color=0864a5>(전자지불 안내는 <a href="../basic/pg.intro.php"><u><font color=ff4200><b>여기</b></font></u></a>를 클릭하세요)</font></td>
</tr>
<tr>
	<td><?=$pgCompany?> ID</td>
	<td><?=$pg['id']?></td>
</tr>
<tr>
	<td>발급 사용여부</td>
	<td>
	<input type="radio" name="pg[receipt]" value="N" class="null" <?=$checked['receipt']['N']?> onclick="setDisabled()"> 사용안함
	<input type="radio" name="pg[receipt]" value="Y" class="null" <?=$checked['receipt']['Y']?> onclick="setDisabled()"> 사용
	<span style="padding-left:20px">(※ 설정된 PG사의 현금영수증을 사용하며, 계좌이체/가상계좌 이용시 자동 신청/발급)</span>
	</td>
</tr>
<tr>
	<td>결제조건</td>
	<td style="padding:5px;">
	현금영수증은 1원 이상의 현금성거래(무통장입금, 실시간계좌이체, 에스크로)에 대해 발급이 됩니다.<br>
	<div class="small4" style="color:#6d6d6d">(국세청의 정책에 따라 변경 될 수 있습니다.)</div>
	</td>
</tr>
<tr>
	<td>발 급 자</td>
	<td style="padding:5px;">
	<div>
	<input type="radio" name="set[receipt][publisher]" value="buyer" class="null" <?=$checked['publisher']['buyer']?> onclick="setDisabled()"> 구매자 발급
	<span class="small4" style="color:#6d6d6d">(구매자가 마이페이지에서 영수증을 직접 발급합니다.)</span>
	</div>
	<div>
	<input type="radio" name="set[receipt][publisher]" value="seller" class="null" <?=$checked['publisher']['seller']?> onclick="setDisabled()"> 관리자 발급
	<span class="small4" style="color:#6d6d6d">(구매자가 주문서/마이페이지에서 영수증 발급을 신청하면 관리자가 발급합니다.)</span>
	</div>
	</td>
</tr>
</table>
<table class="tb" id="seller_option">
<col class="cellC"><col class="cellL">
<tr>
	<td>신청경로</td>
	<td>
	<div>
	<input type="radio" name="set[receipt][order]" value="N" class="null" <?=$checked['order']['N']?> onclick="setAutoCheck();"> 마이페이지
	<span class="small4" style="color:#6d6d6d">(마이페이지에서만 현금영수증을 신청합니다.)</span>
	</div>
	<div>
	<input type="radio" name="set[receipt][order]" value="Y" class="null" <?=$checked['order']['Y']?> onclick="setAutoCheck();"> 주문서+마이페이지
	<span class="small4" style="color:#6d6d6d">(주문서 작성시 신청하거나 마이페이지에서 신청합니다.)</span>
	</div>
	</td>
</tr>
<tr>
	<td>발급방법</td>
	<td>
	<div>
	<input type="radio" name="set[receipt][auto]" value="Y" class="null" <?=$checked['auto']['Y']?> onclick="setAutoCheck();"> 자동발급
	<span class="small4" style="color:#6d6d6d">(구매자가 현금영수증을 신청하면 입금확인/취소완료 단계에서 자동으로 발급/취소됩니다.)</span>
	</div>
	<div>
	<input type="radio" name="set[receipt][auto]" value="N" class="null" <?=$checked['auto']['N']?> onclick="setAutoCheck();"> 수동발급
	<span class="small4" style="color:#6d6d6d">(관리자가 건별로 발급버튼 / 취소버튼을 눌러서 발급 / 취소합니다.)</span>
	</div>
	</td>
</tr>
</table>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td><span id="periodStr1">신청</span>기간제한</td>
	<td style="padding:5px;">
	고객 주문일로부터
	<select name="set[receipt][period]">
	<option value="">제한없음</option>
	<option value="2" <?=$selected['period']['2']?>>2일</option>
	<option value="7" <?=$selected['period']['7']?>>7일</option>
	<option value="15" <?=$selected['period']['15']?>>15일</option>
	<option value="30" <?=$selected['period']['30']?>>30일</option>
	<option value="60" <?=$selected['period']['60']?>>60일</option>
	<option value="90" <?=$selected['period']['90']?>>90일</option>
	</select>이내 <span id="periodStr2">신청</span> 가능
	<div class="small4" style="color:#6d6d6d">(마이페이지의 주문내역상세에 설정일 이후에는 현금영수증 버튼이 표시되지 않습니다.)</div>
	</td>
</tr>
<tr>
	<td>사업자형태</td>
	<td style="padding:5px;">
	<div>
	<input type="radio" name="set[receipt][compType]" value="0" class="null" <?=$checked['compType']['0']?>> 일반 과세사업자
	<span class="small4" style="color:#6d6d6d">(판매물품에 부가세가 있음, 현금영수증에 공급가액,부가세가 분리되어 국세청통보 됩니다.)</span>
	</div>
	<div>
	<input type="radio" name="set[receipt][compType]" value="1" class="null" <?=$checked['compType']['1']?>> 면세/간이사업자
	<span class="small4" style="color:#6d6d6d">(판매물품에 부가세가 없음, 현금영수증에 공급가액 = 합계금액 (부가세없음)으로 국세청통보 됩니다)</span>
	</div>
	</td>
</tr>
<? if ($cfg['settlePg'] == 'dacom'){ ?>
<tr>
	<td>사업자번호</td>
	<td style="padding:5px;">
	&#149; <?=$cfg['compSerial']?>
	<span class="small4" style="color:#6d6d6d">(데이콤을 통해 현금영수증 발급시 필요합니다. <a href="../basic/default.php">[쇼핑몰기본관리]</a> 에서 설정합니다.)</span>
	</td>
</tr>
<? } ?>
</table>

<!-- 현금 영수증 관련 안내 : Start -->
<div style="border:solid 4px #dce1e1; border-collapse:collapse; margin:20px 0px 20px 0px; color:#666666;">
	<ul style="padding:0px 0px 0px 0px;">
		<li style="padding:3px; margin-left:10px; list-style-type:none; color:#ff0000; font-weight:bold;">※ 발급방법을 &quot;자동발급&quot;으로 설정시 반드시 확인해주세요!</li>
		<li style="padding:3px; margin-left:30px; list-style-type:disc;">아래의 조건에서는 현금영수증 자동발급이 어려울 수 있으므로, <a href="./cashreceipt.list.php" style="color:#627dce; font-weight:bold; text-decoration:underline;">[현금영수증 발급/조회]</a>를 통하여 반드시 확인 및 관리하여 주시기 바랍니다.</li>
		<li style="padding:3px; margin-left:30px; list-style-type:disc;">구매자가 마이페이지에서 현금영수증 발급 신청을 한 경우</li>
		<li style="padding:3px; margin-left:30px; list-style-type:disc;">무통장 자동입금 서비스로 인하여 주문서의 주문상태가 입금 확인으로 자동 변경 된 경우</li>
	</ul>
</div>
<!-- 현금 영수증 관련 안내 : End -->

<div class="button">
<input type="image" src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>


<div id="MSG01">
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr>
	<td>
		<img src="../img/icon_list.gif" align="absmiddle">현금영수증 제도는 소비자에게는 소득공제를, 가맹점에게는 세액공제의 혜택을 주며 건전한 소비문화 정착과 조세정의 실현을 위해 2005년 1월 1일부터 시행 되는 제도입니다.<br />
		&nbsp; &nbsp;(2008년 7월 1일부터 5,000원 미만 현금결제로 확대되어 1원 이상이면 발급됩니다.)
	</td>
</tr>
<tr>
	<td>
		<img src="../img/icon_list.gif" align="absmiddle">현금영수증 제도의 혜택을 받기 위해서 소비자는 현금(무통장입금) 구매시 판매자(현금영수증 가맹점)에게 현금영수증 발급을 요청한 후, 본인확인이 가능한 휴대전화 번호 등을 제시해야 합니다.
	</td>
</tr>
<tr>
	<td>
		<img src="../img/icon_list.gif" align="absmiddle">현금영수증 관련 거래내역은 주문일자와 관계없이 발급일자를 기준으로 발급되며 익일 오후5시 이후 국세청홈페이지 <a href="http://현금영수증.kr" target="_blank"><b>http://현금영수증.kr</b></a>를 통해 확인할 수 있습니다.<br />
		&nbsp; &nbsp;따라서, 실제 주문일자와 차이가 발생할 수 있으므로 부가세 신고 등 매출신고에 참고하시기 바랍니다.
	</td>
</tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">현금영수증 서비스를 이용하시려면 <b>전자지불(PG)를 먼저 신청하신 후 전자지불 상점관리자페이지에서 현금영수증 서비스를 신청합니다.</b></td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<script language="javascript"><!--
function setDisabled()
{
	var fobj = document.getElementsByName('pg[receipt]')[0].form;
	var disabled = fobj['pg[receipt]'][0].checked;

	var len = fobj.elements.length;
	for (i = 0; i < len; i++){
		if (fobj.elements[i].name == 'pg[receipt]' || fobj.elements[i].name == 'mode') continue;
		fobj.elements[i].disabled = disabled;
	}

	if (disabled === false){
		var disabled = fobj['set[receipt][publisher]'][0].checked;
		fobj['set[receipt][order]'][0].disabled = disabled;
		fobj['set[receipt][order]'][1].disabled = disabled;
		fobj['set[receipt][auto]'][0].disabled = disabled;
		fobj['set[receipt][auto]'][1].disabled = disabled;

		if (disabled === true){
			_ID('periodStr1').innerHTML = '발행';
			_ID('periodStr2').innerHTML = '발행';
			_ID('seller_option').style.display	='none';
		}
		else {
			_ID('periodStr1').innerHTML = '신청';
			_ID('periodStr2').innerHTML = '신청';
			_ID('seller_option').style.display	='block';
		}
	}
}

/**
 * 신청경로와 발급방법 체크
 */
function setAutoCheck()
{
	var fobj = document.getElementsByName('pg[receipt]')[0].form;
	var mypageCheck	= fobj['set[receipt][order]'][0].checked;
	var autoCheck	= fobj['set[receipt][auto]'][0].checked;

	if (mypageCheck === true && autoCheck === true) {
		alert('신청경로가 "마이페이지"인 경우에는 자동발급이 지원되지 않습니다.');
		fobj['set[receipt][auto]'][1].checked = 'checked';
	}
}

setDisabled();
//--></script>

<? include "../_footer.php"; ?>