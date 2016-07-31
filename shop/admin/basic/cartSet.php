<?
$location = "기본관리 > 장바구니 관련설정";
include "../_header.php";

include  "../../lib/cart.class.php";
$cart = new Cart;

// 인감 이미지 확장자
$ext = strtolower(array_pop(explode('.',$cart->estimateSeal)));

// \제거
$cart->estimateMessage = stripslashes($cart->estimateMessage);

if($cart->keepPeriod>0)
	$checked[keepPeriod][1]='checked';
else
	$checked[keepPeriod][0]='checked';

$checked[runoutDel][$cart->runoutDel]='checked';
$checked[redirectType][0]=($cart->redirectType=='Direct')?'checked':'';
$checked[redirectType][1]=($cart->redirectType=='Confirm')?'checked':'';
$checked[redirectType][2]=($cart->redirectType=='Keep')?'checked':'';
$checked['estimateUse'][0]=($cart->estimateUse==null)?'checked':'';
$checked['estimateUse'][$cart->estimateUse]='checked';
?>

<form method="post" action="indb.php" enctype="multipart/form-data">
<input type="hidden" name="mode" value="cartSet">
<div class="title title_top">고객 장바구니 상품 보관 설정  <span>고객 장바구니에 담긴 상품의 보관기간을 설정합니다.</span>
<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=28')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>상품 보관 기간 설정</td>
	<td class=noline>
	<input type=radio name=keepPeriodYn value="Y" <?=$checked[keepPeriod][0]?>>고객이 삭제 시 까지 보관&nbsp;&nbsp;
	<input type=radio name=keepPeriodYn value="N" <?=$checked[keepPeriod][1]?>>
	<select name="keepPeriod">
	<? 
	if($cart->keepPeriod>0)
		$tmp[keepPeriod]=$cart->keepPeriod;
	else 
		$tmp[keepPeriod]='7';

	for($i=1; $i<=30; $i++){
		$selected[keepPeriod]="";
		if($i==$tmp[keepPeriod])
			$selected[keepPeriod]="selected";
	echo "<option value='".$i."' ".$selected[keepPeriod].">".$i."</option>";
	}?>
	</select>
	
	일 까지 보관 후 자동삭제
	</td>
</tr>

<tr>
	<td>품절상품 보관설정</td>
	<td class=noline>
		<input type=radio name="runoutDel" value="1" <?=$checked[runoutDel][1]?>>보관상품 품절 시 자동삭제&nbsp;&nbsp;
		<input type=radio name="runoutDel" value="0" <?=$checked[runoutDel][0]?>>보관상품 품절 시 남겨둠
	</td>
</tr>

</table>
<div class="extext" style="padding: 5px 0 0 5px;line-height:140%;">
※회원 장바구니에만 보관설정이 적용되며, 비회원일 경우 보관설정이 적용되지 않습니다.<br/>
※고객 장바구니 상품보관 개수는 최대 300개 까지 제공되며, 초과시 안내 멘트가 출력 됩니다.
</div>

<div class=title>장바구니 페이지 이동 설정 <span>장바구니담기 실행시 장바구니 페이지로의 화면 이동여부를 설정합니다.  </span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=28')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td rowspan="2">장바구니 담기<br/>페이지 이동여부 <br/>설정</td>
	<td class="noline">
		<label><input type="radio" name="redirectType" value="Direct" <?=$checked[redirectType][0]?> >장바구니 페이지 바로 이동</label>
		<div class="extext" style="padding: 5px 0 0 5px;line-height:140%;">장바구니 담기 버튼 클릭시 장바구니 페이지로 바로 이동됩니다.</div>
	</td>
</tr>
<tr>
	<td class="noline">
		<label><input type="radio" name="redirectType" value="Confirm" <?=$checked[redirectType][1]?>>장바구니 페이지 이동여부 선택</label>
		<div class="extext" style="padding: 5px 0 0 5px;line-height:140%;">장바구니 담기 버튼 클릭시 확인 팝업이 뜨며, 장바구니 페이지 이동여부를 선택할 수 
    있습니다.<br/>
    “장바구니에 담았습니다. 지금 확인 하시겠습니까? [장바구니보기] [계속쇼핑하기]” <br/>
     [장바구니보기] -> 장바구니 페이지로 이동합니다.<br/>
     [계속쇼핑하기] -> 현재페이지 유지상태에서 옵션선택 부분이 초기화 됩니다.<br/>
</div>
	</td>
</tr>
	</td>
</tr>
</table>

<div class=title> 견적서 설정  <span>견적서 기능 사용 시 장바구니에서 고객이 직접 견적서를 출력할 수 있습니다. </span>
<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=28')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>사용여부</td>
	<td class=noline>
	<input type=radio name=estimateUse value="1" <?=$checked['estimateUse'][1]?>>사용&nbsp;&nbsp;
	<input type=radio name=estimateUse value="0" <?=$checked['estimateUse'][0]?>>사용안함
	<div class="extext" style="padding: 5px 0 0 5px;line-height:140%;">사용으로 설정 시 고객이 장바구니에서 견적서를 직접 출력할 수 있습니다. </div>
	</td>
</tr>
<tr>
	<td>인감이미지</td>
	<td>
		<input type="file" name="seal" style="width:300px">
		<a href="javascript:webftpinfo( '<?=$cart->estimateSeal?>' );">
		<img src="../img/codi/icon_imgview.gif" border="0" alt="이미지 보기" align="absmiddle"></a>
		<? if($ext != null) {?><span>(estimateSeal.<?=$ext?>)</span> <? }?>
		<span class="noline"><input type="checkbox" name="sealDel" value="Y">삭제</span>
		<span class="extext">기본사이즈 74*74px으로 등록하세요.</span>
	</td>
</tr>
<tr>
	<td rowspan="2">비고 메세지 </td>
	<td>
	<textarea name="estimateMessage" class="line" style="width:100%; height:50px;"><?=$cart->estimateMessage?></textarea>
	<div class="extext" style="padding: 5px 0 0 5px;line-height:140%;">고객이 견적서 출력 시 비고란에 들어갈 메시지 입니다.<br/>
	예 ) 입금계좌 : 1001-111-111111 예금주 홍길동 <br/>
	<div style="padding:0px 0px 0px 20px;">위 견적서는 일주일간 유효합니다.</div>
	</div>
	</td>
</tr>
</table>
<div class="extext" style="padding: 5px 0 0 5px;line-height:140%;">※견적서에는 할인가격이 적용되지 않습니다. 판매가격 기준으로 출력합니다.</div>

<div class=button><input type=image src="../img/btn_save.gif"></div>
</form>

<p>
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">※ 장바구니담기 확인 팝업 디자인 수정</font></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">위의 장바구니 페이지 이동설정에서 ‘장바구니 페이지로 이동여부 선택’으로 설정시 열리는 ‘장바구니 담기 확인 </td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">팝업’ 디자인은 디자인관리 좌측 트리 항목의 <a href="../design/codi.php?design_file=goods/popup_cart_add.htm"><font color=white><b>[ 상품 > 장바구니담기 팝업 ]</b></font></a> 에서 디자인 수정이 가능합니다. 

</td></tr>
 
</table>
</div>
<script>cssRound('MSG01')</script>
 


<? include "../_footer.php"; ?>