<?

$location = "전자세금계산서 관리 > 전자세금계산서 설정";
include "../_header.php";
include "../../conf/config.pay.php";

$set = $set['tax'];

$checked['useyn'][$set[useyn]] = "checked";
$checked['step'][$set[step]] = "checked";

$checked['use_a'][$set[use_a]] = "checked";
$checked['use_c'][$set[use_c]] = "checked";
$checked['use_o'][$set[use_o]] = "checked";
$checked['use_v'][$set[use_v]] = "checked";

?>

<form method=post action="../order/tax_indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="tax">

<div class="title title_top">전자세금계산서설정<span>회원에게 발행되는 전자세금계산서에 대한 정책입니다</span></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>발행 사용여부</td>
	<td class=noline>
	<input type=radio name=useyn value='y' <?=$checked['useyn']['y']?>> 사용
	<input type=radio name=useyn value='n' <?=$checked['useyn']['n']?>> 사용안함
	</td>
</tr>
<tr>
	<td>발행 결제조건</td>
	<td class=noline>
	<input type=checkbox name=use_a <?=$checked['use_a']['on']?>> 무통장입금
	<input type=checkbox name=use_c <?=$checked['use_c']['on']?> disabled> 신용카드
	<input type=checkbox name=use_o <?=$checked['use_o']['on']?>> 계좌이체
	<input type=checkbox name=use_v <?=$checked['use_v']['on']?>> 가상계좌
	</td>
</tr>
<tr>
	<td>발행 시작단계</td>
	<td class=noline>
	<input type=radio name=step value='1' <?=$checked['step']['1']?>> 입금확인
	<input type=radio name=step value='2' <?=$checked['step']['2']?>> 배송준비중
	<input type=radio name=step value='3' <?=$checked['step']['3']?>> 배송중
	<input type=radio name=step value='4' <?=$checked['step']['4']?>> 배송완료
	</td>
</tr>
<tr height=30>
	<td class=ver81>현재 잔여 포인트</td>
	<td class=noline style="padding-left:15">
	<font size=4><b style="color:#FF6600"><?=number_format($godo[tax])?></b></font> point
	<a href="../order/etax.pay.php"><img src="../img/btn_addsms.gif" border=0 align=absmiddle hspace=2></a>
	<font class=extext>(전자세금계산서를 발행하려면 포인트 충전 후 사용해야 합니다)</font>
	</td>
</tr>
</table>

<div class=button>
<input type=image src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>


<div id=MSG01>
<table cellpadding=0 cellspacing=0 border=0 class=small_ex style="line-height:14px;">
<tr><td>
<dl style="margin:0;">
<dt><img src="../img/icon_list.gif" align="absmiddle">신용카드 결제주문은 세금계산서를 발행하지 않습니다.</dt>
<dd style="margin-left:8px;">2004년 개정된 부가가치세법에 의하면, 2004.7.1 이후 신용카드로 결제된 건에 대해서는 세금 계산서 발행이 불가하며 신용카드 매출전표로 부가가치세 신고를 하셔야 합니다.<br>
[ 부가가치세법 시행령 57조 관련법규 참조 ]</dd>
</dl>
</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">전자세금계산서 발행방식 안내</font>
<ol type="a" style="margin:0px 0px 0px 40px;">
<li>기존의 종이 세금계산서를 전자문서화하여 인터넷을 통해 빠르고 정확하게 전달하고 관리할 수 있게 하는 편리한 서비스입니다.</li>
<li>부가가치세법, 국세청 고시에 의거하여 공인인증서를 기반으로 발행자의 전자서명이 된 디지털 파일 형태로 인터넷을 통해 처리합니다.<br>
[ 부가가치세법 시행령 53조와 79조, 국세청 고시 제2001-4호 참조 ]</li>
<li>세금계산서 발행 및 전달 업무에 소요되는 시간과 비용을 대폭 절감시키며 보관(5년간)과 세무신고 업무의 편리성도 제공합니다.</li>
<li>고도몰을 통하여 전자세금계산서를 발행하면 별도 공인인증서를 구입하지 않아도 됩니다. 고도몰에서는 통합 공인인증서를 제공합니다.</li>
<li>전자세금계산서 발행서비스는 포인트충전식으로 포인트가 있어야만 발행이 가능합니다.</li>
<li>포인트를 충전하시려면 전자세금계산서를 지원하는 LG데이콤 웹택스21에 먼저 가입하셔야 합니다.</li>
</ol>
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>