<?php
/**********************************************************************************************
*
* 파일명 : AGS_pay_result.php
* 작성일자 : 2006/08/03
*
* 소켓결제결과를 처리합니다.
*
* Copyright 2005-2006 AEGISHYOSUNG.Co.,Ltd. All rights reserved.
*
**********************************************************************************************/

//공통사용
$AuthTy 		= trim( $_POST["AuthTy"] );				//결제형태
$SubTy 			= trim( $_POST["SubTy"] );				//서브결제형태
$rStoreId 		= trim( $_POST["rStoreId"] );			//업체ID
$rAmt 			= trim( $_POST["rAmt"] );				//거래금액
$rOrdNo 		= trim( $_POST["rOrdNo"] );				//주문번호
$rProdNm 		= trim( $_POST["rProdNm"] );			//상품명
$rOrdNm			= trim( $_POST["rOrdNm"] );				//주문자명

//소켓통신결제(신용카드,핸드폰,일반가상계좌)시 사용
$rSuccYn 		= trim( $_POST["rSuccYn"] );			//성공여부
$rResMsg 		= trim( $_POST["rResMsg"] );			//실패사유
$rApprTm 		= trim( $_POST["rApprTm"] );			//승인시각

//신용카드공통
$rBusiCd 		= trim( $_POST["rBusiCd"] );			//전문코드
$rApprNo 		= trim( $_POST["rApprNo"] );			//승인번호
$rCardCd 		= trim( $_POST["rCardCd"] );			//카드사코드

//신용카드(안심,일반)
$rCardNm 		= trim( $_POST["rCardNm"] );			//카드사명
$rMembNo 		= trim( $_POST["rMembNo"] );			//가맹점번호
$rAquiCd 		= trim( $_POST["rAquiCd"] );			//매입사코드
$rAquiNm 		= trim( $_POST["rAquiNm"] );			//매입사명
$rBillNo 		= trim( $_POST["rBillNo"] );			//전표번호

//신용카드(ISP)
$rDealNo 		= trim( $_POST["rDealNo"] );			//거래고유번호

//계좌이체
$ICHE_OUTBANKNAME	= trim( $_POST["ICHE_OUTBANKNAME"] );	//이체계좌은행명
$ICHE_OUTACCTNO 	= trim( $_POST["ICHE_OUTACCTNO"] );		//이체계좌번호
$ICHE_OUTBANKMASTER = trim( $_POST["ICHE_OUTBANKMASTER"] );	//이체계좌소유주
$ICHE_AMOUNT 		= trim( $_POST["ICHE_AMOUNT"] );		//이체금액

//핸드폰
$rHP_TID 		= trim( $_POST["rHP_TID"] );			//핸드폰결제TID
$rHP_DATE 		= trim( $_POST["rHP_DATE"] );			//핸드폰결제날짜
$rHP_HANDPHONE 	= trim( $_POST["rHP_HANDPHONE"] );		//핸드폰결제핸드폰번호
$rHP_COMPANY 	= trim( $_POST["rHP_COMPANY"] );		//핸드폰결제통신사명(SKT,KTF,LGT)

//가상계좌
$rVirNo 		= trim( $_POST["rVirNo"] );				//가상계좌번호 가상계좌추가
$VIRTUAL_CENTERCD = trim( $_POST["VIRTUAL_CENTERCD"] );	//가상계좌 입금은행코드

//우리에스크로
$mTId 		= trim( $_POST["mTId"] );					//에스크로 주문번호
?>
<html>
<head>
<title>올더게이트</title>
<style type="text/css">
<!--
body { font-family:"돋움"; font-size:9pt; color:#000000; font-weight:normal; letter-spacing:0pt; line-height:180%; }
td { font-family:"돋움"; font-size:9pt; color:#000000; font-weight:normal; letter-spacing:0pt; line-height:180%; }
.clsright { padding-right:10px; text-align:right; }
.clsleft { padding-left:10px; text-align:left; }
-->
</style>
<script language=javascript> // "지불처리중" 팝업창 닫기
<!--
var openwin = window.open("AGS_progress.html","popup","width=300,height=160");
openwin.close();
-->
</script>
<script language=javascript>
<!--
/***********************************************************************************
* ◈ 영수증 출력을 위한 자바스크립트
*		
*	영수증 출력은 [카드결제]시에만 사용하실 수 있습니다.
*  
*   ※당일 결제건에 한해서 영수증 출력이 가능합니다.
*     당일 이후에는 아래의 주소를 팝업(630X510)으로 띄워 내역 조회 후 출력하시기 바랍니다.
*	  ▷ 팝업용 결제내역조회 패이지 주소 : 
*	     	 http://www.allthegate.com/support/card_search.html
*		→ (반드시 스크롤바를 'yes' 상태로 하여 팝업을 띄우시기 바랍니다.) ←
*
***********************************************************************************/
function show_receipt() 
{
	if("<?=$rSuccYn?>"== "y" && "<?=$AuthTy?>"=="card")
	{
		url="http://www.allthegate.com/customer/receiptLast3.jsp"
		url=url+"?sRetailer_id="+sRetailer_id.value;
		url=url+"&approve="+approve.value;
		url=url+"&send_no="+send_no.value;
		
		window.open(url, "window","toolbar=no,location=no,directories=no,status=,menubar=no,scrollbars=no,resizable=no,width=420,height=700,top=0,left=150");
	}
	else
	{
		alert("해당하는 결제내역이 없습니다");
	}
}
-->
</script>
</head>
<body topmargin=0 leftmargin=0 rightmargin=0 bottommargin=0>
<table border=0 width=100% height=100% cellpadding=0 cellspacing=0>
	<tr>
		<td align=center>
		<table width=400 border=0 cellpadding=0 cellspacing=0>
			<tr>
				<td><hr></td>
			</tr>
			<tr>
				<td class=clsleft>지불 결과</td>
			</tr>
			<tr>
				<td><hr></td>
			</tr>
			<tr>
				<td>
				<table width=400 border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td class=clsright width=150>결제형태 : </td>
						<td class=clsleft width=250>
							<?php

							if($AuthTy == "card")
							{
								if($SubTy == "isp")
								{
									echo "신용카드결제-안전결제(ISP)";
								}	
								else if($SubTy == "visa3d")
								{
									echo "신용카드결제-안심클릭";
								}
								else if($SubTy == "normal")
								{
									echo "신용카드결제-일반결제";
								}
								
							}
							else if($AuthTy == "iche")
							{
								echo "계좌이체";
							}
							else if($AuthTy == "hp")
							{
								echo "핸드폰결제";
							}
							else if($AuthTy == "virtual")
							{
								echo "가상계좌결제";
							}
							else if($AuthTy == "eiche")
							{
								echo "에스크로-계좌이체";
							}
							else if($AuthTy == "evirtual")
							{
								echo "에스크로-가상계좌결제";
							}
							?>
						</td>
					</tr>
					<tr>
						<td class=clsright>상점아이디 : </td>
						<td class=clsleft><?=$rStoreId?></td>
					</tr>
					<tr>
						<td class=clsright>주문번호 : </td>
						<td class=clsleft><?=$rOrdNo?></td>
					</tr>
					<tr>
						<td class=clsright>주문자명 : </td>
						<td class=clsleft><?=$rOrdNm?></td>
					</tr>
					<tr>
						<td class=clsright>상품명 : </td>
						<td class=clsleft><?=$rProdNm?></td>
					</tr>
					<tr>
						<td class=clsright>결제금액 : </td>
						<td class=clsleft><?=$rAmt?></td>
					</tr>
<?				if($AuthTy == "card" || $AuthTy == "hp" || $AuthTy == "virtual" ) { ?>
					<tr>
						<td class=clsright>성공여부 : </td>
						<td class=clsleft><?=$rSuccYn?></td>
					</tr>
					<tr>
						<td class=clsright>처리메세지 : </td>
						<td class=clsleft><?=$rResMsg?></td>
					</tr>
<?				}
				if($AuthTy == "card" || $AuthTy == "virtual") { ?>
					<tr>
						<td class=clsright>승인시각 : </td>
						<td class=clsleft><?=$rApprTm?></td>
					</tr>
<?				}
				if($AuthTy == "card" ) {?>
					<tr>
						<td class=clsright>전문코드 : </td>
						<td class=clsleft><?=$rBusiCd?></td>
					</tr>
					<tr>
						<td class=clsright>승인번호 : </td>
						<td class=clsleft><?=$rApprNo?></td>
					</tr>
					<tr>
						<td class=clsright>카드사코드 : </td>
						<td class=clsleft><?=$rCardCd?></td>
					</tr>
<?				}
				if($AuthTy == "card" && ($SubTy == "visa3d" || $SubTy == "normal") ) {?>
					<tr>
						<td class=clsright>카드사명 : </td>
						<td class=clsleft><?=$rCardNm?></td>
					</tr>
					<tr>
						<td class=clsright>매입사코드 : </td>
						<td class=clsleft><?=$rAquiCd?></td>
					</tr>
					<tr>
						<td class=clsright>매입사명 : </td>
						<td class=clsleft><?=$rAquiNm?></td>
					</tr>
					<tr>
						<td class=clsright>가맹점번호 : </td>
						<td class=clsleft><?=$rMembNo?></td>
					</tr>
					<tr>
						<td class=clsright>전표번호 : </td>
						<td class=clsleft><?=$rBillNo?></td>
					</tr>
<?				}
				if($AuthTy == "card" && $SubTy == "isp" ) {?>
					<tr>
						<td class=clsright>거래고유번호 : </td>
						<td class=clsleft><?=$rDealNo?></td>
					</tr>
<?				}
				if($AuthTy == "iche" || $AuthTy == "eiche" ) {?>
					<tr>
						<td class=clsright>이체계좌은행명 : </td>
						<td class=clsleft><?=$ICHE_OUTBANKNAME?></td>
					</tr>
					<tr>
						<td class=clsright>이체금액 : </td>
						<td class=clsleft><?=$ICHE_AMOUNT?></td>
					</tr>
<?				}
				if($AuthTy == "iche" ) {?>
					<tr>
						<td class=clsright>이체계좌소유주 : </td>
						<td class=clsleft><?=$ICHE_OUTBANKMASTER?></td>
					</tr>
<?				}
				if($AuthTy == "hp" ) {?>
					<tr>
						<td class=clsright>핸드폰결제TID : </td>
						<td class=clsleft><?=$rHP_TID?></td>
					</tr>
					<tr>
						<td class=clsright>핸드폰결제날짜 : </td>
						<td class=clsleft><?=$rHP_DATE?></td>
					</tr>
					<tr>
						<td class=clsright>핸드폰결제핸드폰번호 : </td>
						<td class=clsleft><?=$rHP_HANDPHONE?></td>
					</tr>
					<tr>
						<td class=clsright>핸드폰결제통신사명 : </td>
						<td class=clsleft><?=$rHP_COMPANY?></td>
					</tr>
<?				}
				if($AuthTy == "eiche" || $AuthTy == "evirtual" ) {?>
					<tr>
						<td class=clsright>에스크로주문번호 : </td>
						<td class=clsleft><?=$mTId?></td>
					</tr>
<?				}
				if($AuthTy == "virtual" || $AuthTy == "evirtual" ) {?>
					<tr>
						<td class=clsright>입금계좌번호 : </td>
						<td class=clsleft><?=$rVirNo?></td>
					</tr>
                    <tr><!-- 은행코드(20) : 우리은행 -->
						<td class=clsright>입금은행 : </td>
						<td class=clsleft><?if($VIRTUAL_CENTERCD == "20"){echo "우리은행";}else{?><?=$VIRTUAL_CENTERCD?><?}?></td>
					</tr>
                    <tr>
						<td class=clsright>예금주명 : </td>
						<td class=clsleft>(주)이지스효성</td>
					</tr>
<?				}
				if($AuthTy == "card" ) {?>
					<tr>
						<td class=clsright>영수증 :</td>
						<!--영수증출력을위해서보내주는값-------------------->
						<input type=hidden name=sRetailer_id value="<?=$rStoreId?>"><!--상점아이디-->
						<input type=hidden name=approve value="<?=$rApprNo?>"><!---승인번호-->
						<input type=hidden name=send_no value="<?=$rOrdNo?>"><!--주문번호-->
						<!--영수증출력을위해서보내주는값-------------------->
						<td class=clsleft><input type="button" value="영수증" onclick="javascript:show_receipt();"></td>
					</tr>
<?				}	?>
					<tr>
						<td colspan=2>&nbsp;</td>
					</tr>
					<tr>
						<td align=center colspan=2>카드 이용명세서에 구입처가 <font color=red>이지스효성(주)</font>로 표기됩니다.</td>
					</tr>
					
				</table>
				</td>
			</tr>
			<tr>
				<td><hr></td>
			</tr>
			<tr>
				<td class=clsleft>Copyright 2005-2006 AEGISHYOSUNG.Co.,Ltd. All rights reserved.</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</body>
</html>
