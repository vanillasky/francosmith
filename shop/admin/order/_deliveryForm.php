<?
switch (basename($_SERVER['PHP_SELF'])) {
	case 'list.integrate.php':
		$_manual_url = $guideUrl.'board/view.php?id=order&no=24';
		break;
	case 'list.step2.php':
		$_manual_url = $guideUrl.'board/view.php?id=order&no=27';
		break;
	case 'list.step3.php':
		$_manual_url = $guideUrl.'board/view.php?id=order&no=28';
		break;
	default :
		$_manual_url = $guideUrl.'board/view.php?id=order&no=2';
		break;
}
?>
<div>
<div style="padding-top:15px"></div>
<div class="title title_top">배송완료처리 및 송장 일괄등록<span>대량의 송장번호 등록 과 배송처리를 일괄로 등록하실 수 있습니다.</span> <a href="javascript:manual('<?=$_manual_url?>')"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle"/></a></div>

<div style="padding-top:15px"></div>

<form name=deliveryfm method=post action="../order/data_delivery_indb.php" target='ifrmHidden'  enctype="multipart/form-data" onsubmit="return chkForm(this)">

<div style="padding-top:5px;padding-left:10px;"><font class=extext>* 작성완료된 송장CSV파일을 올리세요.</div>


<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td width=240 height=35>송장 CSV 파일 올리기</td>
	<td><input type="file" name="file_excel" size="45" required label="CSV 파일"> &nbsp;&nbsp; <span class="noline"><input type=image src="../img/btn_regist_s.gif" align="absmiddle"></span></td>
</tr>
</table>
</form>


<div style="padding-top:15px"></div>

<div id=MSG02>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><b>[배송완료 처리 하기]</b></td></tr>
<tr><td>&nbsp;- 배송완료 처리를 엑셀파일로 업로드하여 일괄적으로 등록할 수 있습니다.</td></tr>
<tr><td>&nbsp;- 주문통합 리스트에서 다운받은 엑셀파일 양식대로 배송완료일 항목에 배송완료일자를 입력, 저장후(CSV 파일) [등록하기] 버튼을 클릭하여 파일을 업로드 합니다.</td></tr>
<tr><td>&nbsp;- 배송완료일 표기는 반드시 "YYYY-MM-DD HH:mm:ss" 형식으로 입력하셔야 합니다. 예) 2012-12-30 13:00:00</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td><b>[송장등록은 이렇게 하세요.]</b></td></tr>
<tr><td>① 송장입력방법 설정</td></tr>
<tr><td>&nbsp;- 택배송장을 등록하시려면 먼저 관리자페이지 > 쇼핑몰 기본관리 > 주문 설정에서 "송장입력방법 설정" 을 해주셔야합니다. <a href="../basic/order_set.php"><font color="#ffffff"><b>[주문설정 바로가기]</b></font></a></td></tr>
<tr><td>&nbsp;- 송장입력방법 설정 </td></tr>
<tr><td>&nbsp;&nbsp;&nbsp;  주문에 상품이 여러 개일 경우 하나의 송장번호로만 관리하거나, 각각의 상품마다 송장번호를 입력하게 하는 기능입니다</td></tr>
<tr><td>&nbsp;&nbsp;&nbsp;  a. 한 개의 송장번호만 입력 - 주문별로 송장번호를 입력할 때 설정</td></tr>
<tr><td>&nbsp;&nbsp;&nbsp;  b. 상품별로 송장번호를 입력 - 상품별로 송장번호를 따로 입력할 때 설정</td></tr>
<tr><td>② 엑셀항목설정</td></tr>
<tr><td>&nbsp;- 송장입력방법이 "한 개의 송장번호만 입력"이면 "주문별 항목설정"을 설정하고 "상품별로 송장번호를 입력"이면 "상품별 항목설정"을 먼저 설정합니다.</td></tr>
<tr><td>&nbsp;- 주문별 항목 설정시 주문번호, 송장번호, 배송사코드는 필수 항목입니다.</td></tr>
<tr><td>&nbsp;- 상품별 항목 설정시 일련번호, 주문번호, 송장번호, 배송사코드는 필수 항목입니다.</td></tr>
<tr><td>③ 엑셀다운로드</td></tr>
<tr><td>&nbsp;- 송장입력방법이 "한 개의 송장번호만 입력"이면 "주문별 엑셀파일" 을 받으시고 "상품별로 송장번호를 입력"이면 "상품별 엑셀파일" 을 다운로드 받습니다.</td></tr>
<tr><td>④ 송장등록</td></tr>
<tr><td>&nbsp;- 다운로드 받으신 엑셀파일에서 배송코드와 송장번호를 입력합니다.</td></tr>
<tr><td>&nbsp;- 배송사코드는 쇼핑몰기본관리 > 배송/택배정책에서 해당 택배사를 선택 후 수정버튼을 클릭하면 고유번호로 표시되는 번호입니다 <a href="../basic/delivery.php"><font color="#ffffff"><b>[배송/택배사 설정 바로가기]</b></font></td></tr>
<tr><td>&nbsp;-【주의】엑셀파일에 있는 필드종류와 순서가 위에서 설정한 엑셀항목과 반드시 동일해야 합니다. <br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;다른 내용으로 변경할 경우 오류가 발생하게 됩니다.</td></tr>
<tr><td>&nbsp;-【주의】처음 이용하시는 분은 반드시 주문 1건만을 대상으로 테스트를 진행해 보시길 바랍니다.<br/>
<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;올바르게 업로드되면 전체적인 주문건을 대상으로 진행해주시기 바랍니다.</td></tr>
<tr><td>&nbsp;- 입력이 완료된 엑셀파일을 CSV 형식으로 저장한 후  송장 CSV 파일 올리기를 이용해 송장파일을 올립니다.</td></tr>
<tr><td>&nbsp;- 주문관리(주문상세내역)를 이용해서 송장정보가 정확히 입력되었는지 확인하실 수 있습니다.</td></tr>
<tr><td>⑤ 주문자, 받는이 정보도 엑셀파일에서 수정하여 등록할 수 있습니다. (단, 결제정보는 송장등록 기능에서 수정하실 수 없습니다.)</td></tr>
<tr><td>&nbsp;- 수정가능 정보 : 주문자명, 이메일, 주문자전화번호, 주문자핸드폰, 받는분이름, 받는분전화번호, 받는분핸드폰, (구)우편번호, (새)우편번호, (구)지번주소, (신)도로명주소</td></tr>
</table>
</div>
</div>
<script>cssRound('MSG02')</script>