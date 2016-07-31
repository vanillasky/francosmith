<?

$location = '현금영수증 서비스 > 현금영수증 개별발급';
include '../_header.php';

?>

<div class="title title_top">현금영수증 개별발급 <span>현금영수증을 주문서가 아닌 개별적으로 발급요청이 가능합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=19')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

<?
// 복합과세 적용된 LG+ 의 현금영수증 발급 (상품의 과세여부로 처리)
if ($cfg['settlePg'] =='lgdacom' || $cfg['settlePg'] =='inicis' || $cfg['settlePg'] =='inipay'){
	include './cashreceipt._form_multitax.singly.php';
} else {
	include './cashreceipt._form.php';
}
?>

<div id="MSG01">
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">무통장 외 결제수단으로 결제를 했거나 기타 오프라인 판매에 대해서 현금영수증 발급이 가능합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">현금영수증 발급시 국세청에 통보되기 때문에 정확한 자료를 입력합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">발급요청 후 <a href="../order/cashreceipt.list.php">[현금영수증 발급/조회]</a> 에서 발급하여야 합니다.</font></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><span style="font-size:9pt;">※</font> 주의) 현금영수증 개별발급 완료된 건은 주문건과 매칭되지 않기 때문에, 주문리스트>해당주문건 상세내역>결제정보>현금영수증 부분의<br>
&nbsp;&nbsp;&nbsp;<b>[현금영수증 개별발급 및 별도발행 되었음]</b>을 꼭 체크하셔야 주문자가 중복발행 신청을 할 수 없게 처리 됩니다.
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>