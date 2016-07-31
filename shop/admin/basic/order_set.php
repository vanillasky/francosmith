<?
$location = "기본관리 > 주문설정";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";

$cfg = array_map("slashes",$cfg);
$checked[stepStock][$cfg[stepStock]+0] = "checked";
$checked[basis][$set['delivery']['basis']] = "checked";

// 청약의사 재확인
$cfg['orderDoubleCheck'] = ($cfg['orderDoubleCheck'] == 'y' ? 'y' : 'n');
$checked['orderDoubleCheck'][$cfg['orderDoubleCheck']] = 'checked';
?>

<form method="post" action="indb.php">
<input type="hidden" name="mode" value="orderSet">
<div class="title title_top">재고삭감 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=17')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>재고삭감단계</td>
	<td class=noline>
	<input type=radio name=stepStock value=0 <?=$checked[stepStock][0]?>> 주문시 <font class=extext>(주문접수단계)</font>
	<input type=radio name=stepStock value=1 <?=$checked[stepStock][1]?>> 입금시 <font class=extext>(입금확인단계)</font>
	</td>
</tr>
<tr>
	<td>결제 시도/실패<br>쿠폰 복원 기능 사용</td>
	<td class="noline">
		<label><input type="radio" name="RecoverCoupon" value="y" <?=($cfg[RecoverCoupon] == 'y') ? 'checked' : ''?>>사용함</label>
		<label><input type="radio" name="RecoverCoupon" value="n" <?=($cfg[RecoverCoupon] != 'y') ? 'checked' : ''?>>사용안함</label>
		<div class="extext" style="padding: 5px 0 0 3px;line-height:140%;">
		결제시도/실패 주문서에 사용된 쿠폰을 고객이 복원하여 재주문 시 사용할 수 있습니다.
		</div>
	</td>
</tr>
</table>

<div class=title>주문내역 재주문 기능 사용 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=17')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>주문내역 재주문</td>
	<td class="noline">
		<label><input type="radio" name="reOrder" value="y" <?=($cfg[reOrder] == 'y') ? 'checked' : ''?>>사용함</label>
		<label><input type="radio" name="reOrder" value="n" <?=($cfg[reOrder] != 'y') ? 'checked' : ''?>>사용안함</label>
		<div class="extext" style="padding: 5px 0 0 3px;line-height:140%;">
		주문 내역에 있는 상품을 모두 장바구니에 다시 담는 기능으로, 기존 구매 고객의 재 주문을 유도할 수 있습니다.<br>
		상품의 품절, 진열여부 변경(미진열), 구매한 상품의 옵션 변경된 경우에는 장바구니로 이동되지 않습니다.
		</div>
	</td>
</tr>
</table>

<div class=title>자동 주문취소 설정 <span>입금되지 않은 주문건들의 자동주문취소에 대한 기간 및 자동 복구 내역을 설정합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=17')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>자동 주문취소 설정</td>
	<td>
	주문 이후 <input type="text" name="autoCancel" size="2" value="<?=$cfg[autoCancel]?>" onkeydown="onlynumber()" class="right line"> <select name="autoCancelUnit"><option value="d" <?=($cfg[autoCancelUnit] != 'h') ? 'selected' : ''?>>일</option><option value="h" <?=($cfg[autoCancelUnit] == 'h') ? 'selected' : ''?>>시간</option></select>동안 입금하지 않은 무통장입금 주문을 자동으로 주문취소합니다.
	<span class=small><font class=extext>'0'이나 공백으로 설정시 기능사용 않함</font></span>
	</td>
</tr>
<tr>
	<td rowspan="3">자동복원 설정</td>
	<td class="noline">
		<label><input type="radio" name="autoCancelRecoverStock" value="y" <?=($cfg[autoCancelRecoverStock] != 'n') ? 'checked' : ''?>>재고량 자동으로 복원</label>
		<label><input type="radio" name="autoCancelRecoverStock" value="n" <?=($cfg[autoCancelRecoverStock] == 'n') ? 'checked' : ''?>>개별 수정</label>
	</td>
</tr>
<tr>
	<!--td rowspan="3">자동복원 설정 설정</td-->
	<td class="noline">
		<label><input type="radio" name="autoCancelRecoverReserve" value="y" <?=($cfg[autoCancelRecoverReserve] != 'n') ? 'checked' : ''?>>적립금 사용내역 자동으로 복원</label>
		<label><input type="radio" name="autoCancelRecoverReserve" value="n" <?=($cfg[autoCancelRecoverReserve] == 'n') ? 'checked' : ''?>>복원 안됨</label>
	</td>
</tr>
<tr>
	<!--td rowspan="3">자동복원 설정 설정</td-->
	<td class="noline">
		<label><input type="radio" name="autoCancelRecoverCoupon" value="y" <?=($cfg[autoCancelRecoverCoupon] != 'n') ? 'checked' : ''?>>쿠폰 사용내역 자동으로 복원</label>
		<label><input type="radio" name="autoCancelRecoverCoupon" value="n" <?=($cfg[autoCancelRecoverCoupon] == 'n') ? 'checked' : ''?>>복원 안됨</label>
		<div class="extext" style="padding: 5px 0 0 5px;line-height:140%;">
		쿠폰 사용내역을 자동으로 복원 설정시, <br>
		무통장입금 주문건이 자동으로 취소된 이후 입금된 건에 대해서 주문복구 및 주문접수 처리를 하지 않으면, 복원된 쿠폰이 사용 가능하도록 남아 있습니다.<br>
		이 경우, 주문복구 및 주문접수 처리 전에 복원된 쿠폰이 다른 주문건에 사용될 수도 있으니 이점 유의해 주세요.
		</div>
	</td>
</tr>

</table>




<div class=title>주문리스트 조회일자 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=17')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>주문리스트<br>기본조회기간 설정</td>
	<td>
	'주문관리 > 주문리스트'를 열었을 때 기본조회 기간을 <input type="text" name="orderPeriod" size="2" value="<?=$cfg[orderPeriod]?>" onkeydown="onlynumber()" class="right line"> 일간으로  설정합니다.
	<div class=extext style="padding-top:3px">너무 긴 기간을 입력하면 주문리스트를 열 때마다 많은 부하가 걸릴 수 있습니다. 1일~5일 이내를 권장합니다</div>
	</td>
</tr>
</table>
<div class=title>주문리스트의 주문일 출력수 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=17')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>한 페이지당<br>주문건 출력수</td>
	<td>주문일로 볼 때 한 페이지당 주문건 <input type="text" name="orderPageNum" size="2" value="<?=$cfg[orderPageNum]?>" onkeydown="onlynumber()" class="right line"> 개를 출력합니다.</td>
</tr>
</table>
<div class=title>송장입력방법 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=17')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr height=40>
	<td>상품별 송장</td>
	<td>한 주문에 상품이 여러개일 경우 &nbsp;&nbsp;
	<input type=radio name=basis value='0' class=null <?=$checked[basis][0]?>>한개의 송장번호만 입력&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=basis class=null value='1' <?=$checked[basis][1]?>>상품별로 송장번호를 입력
	</td>
</tr>
</table>

<div class="title">결제페이지 청약의사 재확인 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=17')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>청약의사 재확인<br>설정</td>
	<td class="noline">
		<label><input type="radio" name="orderDoubleCheck" value="y" <?=$checked['orderDoubleCheck']['y']?>>사용함</label>
		<label><input type="radio" name="orderDoubleCheck" value="n" <?=$checked['orderDoubleCheck']['n']?>>사용안함</label>
		<div class="extext" style="padding: 5px 0 0 3px;line-height:140%;">
		전자상거래법 제8조 제2항에 의거하여 소비자의 구매의사가 진정한 의사표시인지 확인하기 위해 제품의 내용, 종류 및 가격, 용역의 제공기간을 <br/>
		명확히 고지하고, 고지한 사항에 대한 재확인 절차를 마련하여야 합니다.
		<a href="http://www.law.go.kr/LSW/lsEfInfoP.do?lsiSeq=140566#0000" class="extext" target="_blank"><strong>[관련법규 전문 보기]</strong></a>
		</div>
	</td>
</tr>
</table>

<div class=button><input type=image src="../img/btn_save.gif"></div>
</form>

<? include "../_footer.php"; ?>