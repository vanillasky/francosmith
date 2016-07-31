<?php


/* INIsecurepay.php
 *
 * 이니페이 플러그인을 통해 요청된 지불을 처리한다.
 * 지불 요청을 처리한다.
 * 코드에 대한 자세한 설명은 매뉴얼을 참조하십시오.
 * <주의> 구매자의 세션을 반드시 체크하도록하여 부정거래를 방지하여 주십시요.
 * 실시간 계좌이체 지불인 경우 이체결과 수신 WEB프로그램(INIpayresult.php)를 이용하여
 * 지불결과(이체결과) 데이타를 수신받도록 하십시요
 *  
 * http://www.inicis.com
 * Copyright (C) 2004 Inicis Co., Ltd. All rights reserved.
 */

	/**************************
	 * 1. 라이브러리 인클루드 *
	 **************************/
	require("INIpay41Lib.php");
	
	
	/***************************************
	 * 2. INIpay41 클래스의 인스턴스 생성 *
	 ***************************************/
	$inipay = new INIpay41;



	/*********************
	 * 3. 지불 정보 설정 *
	 *********************/
	$inipay->m_inipayHome = "/usr/local/INIpay41"; // 이니페이 홈디렉터리
	$inipay->m_type = "securepay"; // 고정
	$inipay->m_pgId = "INIpay".$pgid; // 고정
	$inipay->m_subPgIp = "203.238.3.10"; // 고정
	$inipay->m_keyPw = "1111"; // 키패스워드(상점아이디에 따라 변경)
	$inipay->m_debug = "true"; // 로그모드("true"로 설정하면 상세로그가 생성됨.)
	$inipay->m_mid = $mid; // 상점아이디
	$inipay->m_uid = $uid; // INIpay User ID
	$inipay->m_uip = getenv("REMOTE_ADDR"); // 고정
	$inipay->m_goodName = $goodname;
	$inipay->m_currency = $currency;
	$inipay->m_price = $price;
	$inipay->m_buyerName = $buyername;
	$inipay->m_buyerTel = $buyertel;
	$inipay->m_buyerEmail = $buyeremail;
	$inipay->m_payMethod = $paymethod;
	$inipay->m_encrypted = $encrypted;
	$inipay->m_sessionKey = $sessionkey;
	$inipay->m_url = "http://www.your_domain.co.kr"; // 실제 서비스되는 상점 SITE URL로 변경할것
	$inipay->m_cardcode = $cardcode; // 카드코드 리턴
	$inipay->m_ParentEmail = $parentemail; // 보호자 이메일 주소(핸드폰 , 전화결제시에 14세 미만의 고객이 결제하면  부모 이메일로 결제 내용통보 의무, 다른결제 수단 사용시에 삭제 가능)
	/*-----------------------------------------------------------------*
	 * 수취인 정보 *                                                   *
	 *-----------------------------------------------------------------*
	 * 실물배송을 하는 상점의 경우에 사용되는 필드들이며               *
	 * 아래의 값들은 INIsecurepay.html 페이지에서 포스트 되도록        *
	 * 필드를 만들어 주도록 하십시요.                                  *
	 * 컨텐츠 제공업체의 경우 삭제하셔도 무방합니다.                   *
	 *-----------------------------------------------------------------*/
	$inipay->m_recvName = $recvname;	// 수취인 명
	$inipay->m_recvTel = $recvtel;		// 수취인 연락처
	$inipay->m_recvAddr = $recvaddr;	// 수취인 주소
	$inipay->m_recvPostNum = $recvpostnum;  // 수취인 우편번호
	$inipay->m_recvMsg = $recvmsg;		// 전달 메세지
	
	
	/****************
	 * 4. 지불 요청 *
	 ****************/
	$inipay->startAction();
	
	
	/****************************************************************************************************************
	 * 5. 결제  결과                                                    						*
	 *      													*
	 *  가. 모든 결제 수단에 공통되는 결제 결과 내용                                                        	*
	 * 	거래번호 : $inipay->m_tid                                       					*
	 * 	결과코드 : $inipay->m_resultCode ("00"이면 지불 성공)           					*
	 * 	결과내용 : $inipay->m_resultMsg (지불결과에 대한 설명)          					*
	 * 	지불방법 : $inipay->m_payMethod (매뉴얼 참조)  								*
	 * 	상점주문번호 : $inipay->m_moid										*
	 *														*
	 *  나. 신용카드,ISP,핸드폰, 전화 결제, 은행계좌이체, OK CASH BAG Point 결제시에만 결제 결과 내용 		*
	 *              (무통장입금 , 문화 상품권) 								*
	 * 	이니시스 승인날짜 : $inipay->m_pgAuthDate (YYYYMMDD)            					*
	 * 	이니시스 승인시각 : $inipay->m_pgAuthTime (HHMMSS)              					*	 
	 *  														*
	 *  다. 신용카드  결제수단을 이용시에만  결제결과 내용          						*
         *														*
	 * 	신용카드 승인번호 : $inipay->m_authCode                         					*
	 * 	할부기간 : $inipay->m_cardQuota                                 					*
	 * 	무이자할부 여부 : $inipay->m_quotaInterest ("1"이면 무이자할부) 					*
	 * 	신용카드사 코드 : $inipay->m_cardCode (매뉴얼 참조)             					*
	 * 	카드발급사 코드 : $inipay->m_cardIssuerCode (매뉴얼 참조)       					*
	 * 	본인인증 수행여부 : $inipay->m_authCertain ("00"이면 수행)      					*
	 *      각종 이벤트 적용 여부 : $inipay->m_eventFlag                    					*	
	 *														*	
	 *      아래 내용은 "신용카드 및 OK CASH BAG 복합결제" 또는"신용카드 지불시에 OK CASH BAG적립"시에 추가되는 내용* 
	 * 	OK Cashbag 적립 승인번호 : $inipay->m_ocbSaveAuthCode           					*	
	 * 	OK Cashbag 사용 승인번호 : $inipay->m_ocbUseAuthCode            					*
	 * 	OK Cashbag 승인일시 : $inipay->m_ocbAuthDate (YYYYMMDDHHMMSS)   					*
	 * 	OCB 카드번호 : $inipay->m_ocbcardnumber			   						*
	 * 	OK Cashbag 복합결재시 신용카드 지불금액 : $inipay->m_price1     					*
	 * 	OK Cashbag 복합결재시 포인트 지불금액 : $inipay->m_price2       					*
	 *														*
	 * 라. OK CASH BAG 결제수단을 이용시에만  결제결과 내용	 출력							*
	 * 	OK Cashbag 적립 승인번호 : $inipay->m_ocbSaveAuthCode           					*	
	 * 	OK Cashbag 사용 승인번호 : $inipay->m_ocbUseAuthCode            					*
	 * 	OK Cashbag 승인일시 : $inipay->m_ocbAuthDate (YYYYMMDDHHMMSS)   					*
	 * 	OCB 카드번호 : $inipay->m_ocbcardnumber			   						*
	 *														*
         * 마. 무통장 입금 결제수단을 이용시에만  결제 결과 내용							*
	 * 	가상계좌 채번에 사용된 주민번호 : $inipay->m_perno              					*
	 * 	가상계좌 번호 : $inipay->m_vacct                                					*
	 * 	입금할 은행 코드 : $inipay->m_vcdbank                           					*
	 * 	입금예정일 : $inipay->m_dtinput (YYYYMMDD)                      					*
	 * 	송금자 명 : $inipay->m_nminput                                  					*
	 * 	예금주 명 : $inipay->m_nmvacct                                  					*
	 *														*	
	 * 바. 핸드폰, 전화결제시에만  결제 결과 내용 ( "실패 내역 자세히 보기"에서 필요 , 상점에서는 필요없는 정보임)  *
         * 	전화결제 사업자 코드 : $inipay->m_codegw                        					*
	 *														*	
	 * 사. 핸드폰 결제수단을 이용시에만  결제 결과 내용								*
	 * 	휴대폰 번호 : $inipay->m_nohpp (핸드폰 결제에 사용된 휴대폰번호)       					*
	 *														*
	 * 아. 전화 결제수단을 이용시에만  결제 결과 내용								*	
         * 	전화번호 : $inipay->m_noars (전화결제에  사용된 전화번호)      						*
         * 														*		
         * 자. 문화 상품권 결제수단을 이용시에만  결제 결과 내용							*
         * 	컬쳐 랜드 ID : $inipay->m_cultureid	                           					*
         *														*
         * 차. 모든 결제 수단에 대해 결제 실패시에만 결제 결과 내용 							*
         * 	에러코드 : $inipay->m_resulterrcode                             					*
         *														*
         ****************************************************************************************************************/

	
	
	/*******************************************************************
	 * 7. 강제취소                                                     *
	 *                                                                 *
	 * 지불 결과를 DB 등에 저장하거나 기타 작업을 수행하다가 실패하는  *
	 * 경우, 아래의 코드를 참조하여 이미 지불된 거래를 취소하는 코드를 *
	 * 작성합니다.                                                     *
	 *******************************************************************/
	/*
	var $cancelFlag = "false";

	// $cancelFlag를 "ture"로 변경하는 condition 판단은 개별적으로
	// 수행하여 주십시오.

	if($cancelFlag == "true")
	{
		$inipay->m_type = "cancel"; // 고정
		$inipay->m_msg = "DB FAIL"; // 취소사유
		$inipay->startAction();
		if($inipay->m_resultCode == "00")
		{
			$inipay->m_resultCode = "01";
			$inipay->m_resultMsg = "DB FAIL";
		}
	}
	*/
		
	
?>


<!-------------------------------------------------------------------------------------------------------
 *  													*
 *       												*
 *        												*
 *	아래 내용은 결제 결과에 대한 출력 페이지 샘플입니다. 				                *
 *	여러 결제 수단별 결과내용이 별도로 출력되도록 되어 있으므로 소스 파악이 힘드신 경우             *
 *      간단한 결과 내용을 보여 주는 개발자용 샘플페이지 (INIsecurepay_dev.php)를 참조 하시기 바랍니다.	*
 *													*
 *													*
 *													*
 -------------------------------------------------------------------------------------------------------->
 
<html>
<head>
<title>INIpay41 결제페이지 데모</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link rel="stylesheet" href="css/group.css" type="text/css">
<style>
body, tr, td {font-size:10pt; font-family:굴림,verdana; color:#433F37; line-height:19px;}
table, img {border:none}

/* Padding ******/ 
.pl_01 {padding:1 10 0 10; line-height:19px;}
.pl_03 {font-size:20pt; font-family:굴림,verdana; color:#FFFFFF; line-height:29px;}

/* Link ******/ 
.a:link  {font-size:9pt; color:#333333; text-decoration:none}
.a:visited { font-size:9pt; color:#333333; text-decoration:none}
.a:hover  {font-size:9pt; color:#0174CD; text-decoration:underline}

.txt_03a:link  {font-size: 8pt;line-height:18px;color:#333333; text-decoration:none}
.txt_03a:visited {font-size: 8pt;line-height:18px;color:#333333; text-decoration:none}
.txt_03a:hover  {font-size: 8pt;line-height:18px;color:#EC5900; text-decoration:underline}
</style>

<script>
	var openwin=window.open("childwin.html","childwin","width=300,height=160");
	openwin.close();
	
	/*-------------------------------------------------------------------------------------------------------
         * 1. $inipay->m_resultCode 										*
         *       가. 결 과 코 드: "00" 인 경우 결제 성공[무통장입금인 경우 - 고객님의 무통장입금 요청이 완료]	*
         *       나. 결 과 코 드: "00"외의 값인 경우 결제 실패  						*
         -------------------------------------------------------------------------------------------------------*/
	
	function show_receipt(tid) // 영수증 출력
	{
		if("<?php echo ($inipay->m_resultCode); ?>" == "00")
		{
			var receiptUrl = "https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/mCmReceipt_head.jsp?noTid=" + "<?php echo($inipay->m_tid); ?>" + "&noMethod=1";
			window.open(receiptUrl,"receipt","width=430,height=700");
		}
		else
		{
			alert("해당하는 결제내역이 없습니다");
		}
	}
		
	function errhelp() // 상세 에러내역 출력
	{
		var errhelpUrl = "http://www.inicis.com/ErrCode/Error.jsp?result_err_code=" + "<?php echo($inipay->m_resulterrcode); ?>" + "&mid=" + "<?php echo($inipay->m_mid); ?>" + "&tid=<?php echo($inipay->m_tid); ?>" + "&goodname=" + "<?php echo($inipay->m_goodName); ?>" + "&price=" + "<?php echo($inipay->m_price); ?>" + "&paymethod=" + "<?php echo($inipay->m_payMethod); ?>" + "&buyername=" + "<?php echo($inipay->m_buyerName); ?>" + "&buyertel=" + "<?php echo($inipay->m_buyerTel); ?>" + "&buyeremail=" + "<?php echo($inipay->m_buyerEmail); ?>" + "&codegw=" + "<?php echo($inipay->m_codegw); ?>";
		window.open(errhelpUrl,"errhelp","width=520,height=150, scrollbars=yes,resizable=yes");
	}
	
</script>

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>
</head>
<body bgcolor="#FFFFFF" text="#242424" leftmargin=0 topmargin=15 marginwidth=0 marginheight=0 bottommargin=0 rightmargin=0><center> 
<table width="632" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="85" background=<?php 

/*-------------------------------------------------------------------------------------------------------
 * 결제 방법에 따라 상단 이미지가 변경 된다								*
 * 	 가. 결제 실패 시에 "img/spool_top.gif" 이미지 사용						*
 *       가. 결제 방법에 따라 상단 이미지가 변경							*
 *       	ㄱ. 신용카드 	- 	"img/card.gif"							*
 *		ㄴ. ISP		-	"img/card.gif"							*
 *		ㄷ. 은행계좌	-	"img/bank.gif"							*
 *		ㄹ. 무통장입금	-	"img/bank.gif"							*
 *		ㅁ. 핸드폰	- 	"img/hpp.gif"							*
 *		ㅂ. 전화결제 (ars전화 결제)	-	"img/phone.gif"					*
 *		ㅅ. 전화결제 (받는전화결제)	-	"img/phone.gif"					*
 *		ㅇ. OK CASH BAG POINT		-	"img/okcash.gif"				*
 *		ㅈ. 문화상품권	-	"img/ticket.gif"						*
 -------------------------------------------------------------------------------------------------------*/
    					
    				if($inipay->m_resultCode == "01"){
					echo "img/spool_top.gif";
				}
				else{
					
    					switch($inipay->m_payMethod){
	
						case(Card): // 신용카드
							echo "img/card.gif";
							break;
						case(VCard): // ISP
							echo "img/card.gif";
							break;
						case(HPP): // 휴대폰
							echo "img/hpp.gif";
							break;
						case(Ars1588Bill): // 1588
							echo "img/phone.gif";
							break;
						case(PhoneBill): // 폰빌
							echo "img/phone.gif";
							break;
						case(OCBPoint): // OKCASHBAG
							echo "img/okcash.gif";
							break;
						case(DirectBank):  // 은행계좌이체
							echo "img/bank.gif";
							break;		
						case(VBank):  // 무통장 입금 서비스
							echo "img/bank.gif";
							break;
						case(Culture):  // 문화상품권 결제
							echo "img/ticket.gif";
							break;
						default: // 기타 지불수단의 경우
							echo "img/card.gif";
							break;

					}
				}
					
    				?> style="padding:0 0 0 64">
    				
<!-------------------------------------------------------------------------------------------------------
 *													*
 *  아래 부분은 모든 결제수단의 공통적인 결과메세지 출력 부분입니다.					*
 *  													*
 *	1. $inipay->m_resultCode 	(결 과 코 드) 							*
 *  	2. $inipay->m_resultMsg		(결과 메세지)							*
 *  	3. $inipay->m_payMethod		(결 제 수 단)							*
 *  	4. $inipay->m_tid		(거 래 번 호)							*
 *  	5. $inipay->m_moid  		(주 문 번 호)							*
 -------------------------------------------------------------------------------------------------------->
 
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="3%" valign="top"><img src="img/title_01.gif" width="8" height="27" vspace="5"></td>
          <td width="97%" height="40" class="pl_03"><font color="#FFFFFF"><b>결제결과</b></font></td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td align="center" bgcolor="6095BC">
      <table width="620" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td bgcolor="#FFFFFF" style="padding:0 0 0 56">
		  <table width="510" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td width="7"><img src="img/life.gif" width="7" height="30"></td>
                <td background="img/center.gif"><img src="img/icon03.gif" width="12" height="10">
                
                <!-------------------------------------------------------------------------------------------------------
                 * 1. $inipay->m_resultCode 										*	
                 *       가. 결 과 코 드: "00" 인 경우 결제 성공[무통장입금인 경우 - 고객님의 무통장입금 요청이 완료]	*
                 *       나. 결 과 코 드: "00"외의 값인 경우 결제 실패  						*
                 --------------------------------------------------------------------------------------------------------> 
                  <b><?php if($inipay->m_resultCode == "00" && $inipay->m_payMethod == "VBank"){ echo "고객님의 무통장입금 요청이 완료되었습니다.";}
                  	   else if($inipay->m_resultCode == "00"){ echo "고객님의 결제요청이 성공되었습니다.";}
                           else{ echo "고객님의 결제요청이 실패되었습니다.";} ?> </b></td>
                <td width="8"><img src="img/right.gif" width="8" height="30"></td>
              </tr>
            </table>
            <br>
            <table width="510" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td width="407"  style="padding:0 0 0 9"><img src="img/icon.gif" width="10" height="11"> 
                  <strong><font color="433F37">결제내역</font></strong></td>
                <td width="103">&nbsp;</td>                
              </tr>
              <tr> 
                <td colspan="2"  style="padding:0 0 0 23">
		  <table width="470" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                    
                <!-------------------------------------------------------------------------------------------------------
                 * 2. $inipay->m_payMethod 										*	
                 *       가. 결제 방법에 대한 값									*
                 *       	ㄱ. 신용카드 	- 	Card								*
                 *		ㄴ. ISP		-	VCard								*	
                 *		ㄷ. 은행계좌	-	DirectBank							*
                 *		ㄹ. 무통장입금	-	VBank								*
                 *		ㅁ. 핸드폰	- 	HPP								*
                 *		ㅂ. 전화결제 (ars전화 결제)	-	Ars1588Bill					*
                 *		ㅅ. 전화결제 (받는전화결제)	-	PhoneBill					*
                 *		ㅇ. OK CASH BAG POINT		-	OCBPoint					*
                 *		ㅈ. 문화상품권	-	Culture								*
                 -------------------------------------------------------------------------------------------------------->
                      <td width="18" align="center"><img src="img/icon02.gif" width="7" height="7"></td>
                      <td width="109" height="25">결 제 방 법</td>
                      <td width="343"><?php echo($inipay->m_payMethod); ?></td>
                    </tr>
                    <tr> 
                      <td height="1" colspan="3" align="center"  background="img/line.gif"></td>
                    </tr>
                    <tr> 
                      <td width="18" align="center"><img src="img/icon02.gif" width="7" height="7"></td>
                      <td width="109" height="26">결 과 코 드</td>
                      <td width="343"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr> 
                            <td><?php echo($inipay->m_resultCode); ?></td>
                            <td width='142' align='right'>
                          
                <!-------------------------------------------------------------------------------------------------------
                 * 2. $inipay->m_resultCode 값에 따라 "영수증 보기" 또는 "실패 내역 자세히 보기" 버튼 출력		*
                 *       가. 결제 코드의 값이 "00"인 경우에는 "영수증 보기" 버튼 출력					*
                 *       나. 결제 코드의 값이 "00" 외의 값인 경우에는 "실패 내역 자세히 보기" 버튼 출력			*
                 -------------------------------------------------------------------------------------------------------->
		<!-- 실패결과 상세 내역 버튼 출력 -->
                            	<?php
                            		if($inipay->m_resultCode == "00"){
                				echo "<a href='javascript:show_receipt();'><img src='img/button_02.gif' width='94' height='24' border='0'></a>";
                			}
                			else{
                            			echo "<a href='javascript:errhelp();'><img src='img/button_01.gif' width='142' height='24' border='0'></a>";
                            		}
                                                    	
                            	?>                    </td>
                          </tr>
                        </table></td>
                    </tr>
                
                <!-------------------------------------------------------------------------------------------------------
                 * 1. $inipay->m_resultMsg 										*
                 *       가. 결과 내용을 보여 준다 실패시에는 "[에러코드] 실패 메세지" 형태로 보여 준다.                *
                 *		예> [9121]서명확인오류									*
                 -------------------------------------------------------------------------------------------------------->
                    <tr> 
                      <td height="1" colspan="3" align="center"  background="img/line.gif"></td>
                    </tr>
                    <tr> 
                      <td width="18" align="center"><img src="img/icon02.gif" width="7" height="7"></td>
                      <td width="109" height="25">결 과 내 용</td>
                      <td width="343"><?php echo($inipay->m_resultMsg); ?></td>
                    </tr>
                    <tr> 
                      <td height="1" colspan="3" align="center"  background="img/line.gif"></td>
                    </tr>
                    
                <!-------------------------------------------------------------------------------------------------------
                 * 1. $inipay->m_tid											*
                 *       가. 이니시스가 부여한 거래 번호 -모든 거래를 구분할 수 있는 키가 되는 값			*
                 -------------------------------------------------------------------------------------------------------->
                    <tr> 
                      <td width="18" align="center"><img src="img/icon02.gif" width="7" height="7"></td>
                      <td width="109" height="25">거 래 번 호</td>
                      <td width="343"><?php echo($inipay->m_tid); ?></td>
                    </tr>
                    <tr> 
                      <td height="1" colspan="3" align="center"  background="img/line.gif"></td>
                    </tr>
                    
                <!-------------------------------------------------------------------------------------------------------
                 * 1. $inipay->m_moid											*
                 *       가. 상점에서 할당한 주문번호 									*
                 -------------------------------------------------------------------------------------------------------->
                    <tr> 
                      <td width="18" align="center"><img src="img/icon02.gif" width="7" height="7"></td>
                      <td width="109" height="25">주 문 번 호</td>
                      <td width="343"><?php echo($inipay->m_moid); ?></td>
                    </tr>
                    <tr> 
                      <td height="1" colspan="3" align="center"  background="img/line.gif"></td>
                    </tr>


<?php                    
                    

	/*-------------------------------------------------------------------------------------------------------
	 *													*
	 *  아래 부분은 결제 수단별 결과 메세지 출력 부분입니다.    						*	
	 *													*
	 *  1.  신용카드 , ISP 결제 결과 출력 (OK CASH BAG POINT 복합 결제 내역 )				*
	 -------------------------------------------------------------------------------------------------------*/

	if($inipay->m_payMethod == "Card" || $inipay->m_payMethod == "VCard" ){
		
		echo "		
				<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>신용카드번호</td>
                    		  <td width='343'>".$inipay->m_cardNumber."****</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
				<tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>승 인 날 짜</td>
                                  <td width='343'>".$inipay->m_pgAuthDate."</td>
                                </tr>
                                <tr> 
                                  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                                </tr>
                                <tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>승 인 시 각</td>
                                  <td width='343'>".$inipay->m_pgAuthTime."</td>
                                </tr>                	    
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>승 인 번 호</td>
                    		  <td width='343'>".$inipay->m_authCode."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>할 부 기 간</td>
                    		  <td width='343'>".$inipay->m_cardQuota."개월&nbsp;<b><font color=red>".$interest."</font></b></td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>카 드 종 류</td>
                    		  <td width='343'>".$inipay->m_cardCode."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>카드발급사</td>
                    		  <td width='343'>".$inipay->m_cardIssuerCode."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3'>&nbsp;</td>
                    		</tr>
                    		<tr> 
                		  <td style='padding:0 0 0 9' colspan='3'><img src='img/icon.gif' width='10' height='11'> 
        	          	  <strong><font color='433F37'>OK CASHBAG 적립 및 사용내역</font></strong></td>
                		</tr>
                		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>카 드 번 호</td>
                    		  <td width='343'>".$inipay->m_ocbcardnumber."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>적립 승인번호</td>
                    		  <td width='343'>".$inipay->m_ocbSaveAuthCode."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>사용 승인번호</td>
                    		  <td width='343'>".$inipay->m_ocbUseAuthCode."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>승 인 일 시</td>
                    		  <td width='343'>".$inipay->m_ocbAuthDate."</td>
                    		</tr>
                		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>포인트지불금액</td>
                    		  <td width='343'>".$inipay->m_price2."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>";
                    
          }
        
        /*-------------------------------------------------------------------------------------------------------
	 *													*
	 *  아래 부분은 결제 수단별 결과 메세지 출력 부분입니다.    						*	
	 *													*
	 *  2.  은행계좌결제 결과 출력 										*
	 -------------------------------------------------------------------------------------------------------*/
	 
          else if($inipay->m_payMethod == "DirectBank"){
          	
          	echo"		
          			<tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>승 인 날 짜</td>
                                  <td width='343'>".$inipay->m_pgAuthDate."</td>
                                </tr>
                                <tr> 
                                  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                                </tr>
                                <tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>승 인 시 각</td>
                                  <td width='343'>".$inipay->m_pgAuthTime."</td>
                                </tr>
                                <tr> 
                                  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                                </tr>";
          }
          
        /*-------------------------------------------------------------------------------------------------------
	 *													*
	 *  아래 부분은 결제 수단별 결과 메세지 출력 부분입니다.    						*	
	 *													*
	 *  3.  무통장입금 입금 예정 결과 출력 (결제 성공이 아닌 입금 예정 성공 유무)				*
	 -------------------------------------------------------------------------------------------------------*/
	 
          else if($inipay->m_payMethod == "VBank"){
          	
          	echo "		
          			<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>입금계좌번호</td>
                    		  <td width='343'>".$inipay->m_vacct."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>입금 은행코드</td>
                    		  <td width='343'>".$inipay->m_vcdbank."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>예금주 명</td>
                    		  <td width='343'>".$inipay->m_nmvacct."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>송금자 명</td>
                    		  <td width='343'>".$inipay->m_nminput."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>송금자 주민번호</td>
                    		  <td width='343'>".$inipay->m_perno."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>상품 주문번호</td>
                    		  <td width='343'>".$inipay->m_oid."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>송금 일자</td>
                    		  <td width='343'>".$inipay->m_dtinput."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>";
          }
          
        /*-------------------------------------------------------------------------------------------------------
	 *													*
	 *  아래 부분은 결제 수단별 결과 메세지 출력 부분입니다.    						*	
	 *													*
	 *  4.  핸드폰 결제 											*
	 -------------------------------------------------------------------------------------------------------*/
	 
          else if($inipay->m_payMethod == "HPP"){
          	
          	echo "		
          			
          			<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>휴대폰번호</td>
                    		  <td width='343'>".$inipay->m_nohpp."</td>
                    		</tr>
                    		<tr> 
                                  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                                </tr>
                    		<tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>승 인 날 짜</td>
                                  <td width='343'>".$inipay->m_pgAuthDate."</td>
                                </tr>
                                <tr> 
                                  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                                </tr>
                                <tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>승 인 시 각</td>
                                  <td width='343'>".$inipay->m_pgAuthTime."</td>
                                </tr>
				<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>";
          }
          
        /*-------------------------------------------------------------------------------------------------------
	 *													*
	 *  아래 부분은 결제 수단별 결과 메세지 출력 부분입니다.    						*	
	 *													*
	 *  4.  전화 결제 											*
	 -------------------------------------------------------------------------------------------------------*/
	 
         else if($inipay->m_payMethod == "Ars1588Bill" || $inipay->m_payMethod == "PhoneBill"){
                    	
                echo " 		
                		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>전 화 번 호</td>
                    		  <td width='343'>".$inipay->m_noars."</td>
                    		</tr>
                    		<tr> 
                                  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                                </tr>
                		<tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>승 인 날 짜</td>
                                  <td width='343'>".$inipay->m_pgAuthDate."</td>
                                </tr>
                                <tr> 
                                  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                                </tr>
                                <tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>승 인 시 각</td>
                                  <td width='343'>".$inipay->m_pgAuthTime."</td>
                                </tr>
                		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>";
         }
         
        /*-------------------------------------------------------------------------------------------------------
	 *													*
	 *  아래 부분은 결제 수단별 결과 메세지 출력 부분입니다.    						*	
	 *													*
	 *  4.  OK CASH BAG POINT 적립 및 지불 									*
	 -------------------------------------------------------------------------------------------------------*/
	 
         else if($inipay->m_payMethod == "OCBPoint"){
         	
                echo"		
                		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>카 드 번 호</td>
                    		  <td width='343'>".$inipay->m_ocbcardnumber."</td>
                    		</tr>
                    		<tr> 
                                  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                                </tr>
                		<tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>승 인 날 짜</td>
                                  <td width='343'>".$inipay->m_pgAuthDate."</td>
                                </tr>
                                <tr> 
                                  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                                </tr>
                                <tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>승 인 시 각</td>
                                  <td width='343'>".$inipay->m_pgAuthTime."</td>
                                </tr>
                                <tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>적립 승인번호</td>
                    		  <td width='343'>".$inipay->m_ocbSaveAuthCode."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>사용 승인번호</td>
                    		  <td width='343'>".$inipay->m_ocbUseAuthCode."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>승 인 일 시</td>
                    		  <td width='343'>".$inipay->m_ocbAuthDate."</td>
                    		</tr>
                		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>포인트지불금액</td>
                    		  <td width='343'>".$inipay->m_price2."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>";
         }
         
        /*-------------------------------------------------------------------------------------------------------
	 *													*
	 *  아래 부분은 결제 수단별 결과 메세지 출력 부분입니다.    						*	
	 *													*
	 *  4.  문화 상품권						                			*
	 -------------------------------------------------------------------------------------------------------*/
	 
         else if($inipay->m_payMethod == "Culture"){
         	
                echo"		
                		<tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>컬쳐랜드 ID</td>
                                  <td width='343'>".$inipay->m_cultureid."</td>
                                </tr>
                                <tr> 
                                  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                                </tr>";
         }
                		
         
?>
                    
                  </table></td>
              </tr>
            </table>
            <br>
            
<!-------------------------------------------------------------------------------------------------------
 *													*
 *  결제 성공시($inipay->m_resultCode == "00"인 경우 ) "이용안내"  보여주기 부분입니다.			*	    
 *  결제 수단별로 이용고객에게 결제 수단에 대한 주의 사항을 보여 줍니다. 				*
 *  switch , case문 형태로 결제 수단별로 출력 하고 있습니다.						*
 *  아래 순서로 출력 합니다.										*
 *													*
 *  1.	신용카드 											*
 *  2.  ISP 결제 											*
 *  3.  핸드폰 												*
 *  4.  전화 결제 (1588Bill)										*
 *  5.  전화 결제 (PhoneBill)										*
 *  6.	OK CASH BAG POINT										*
 *  7.  은행계좌이체											*
 *  8.  무통장 입금 서비스										*
 *  9.  문화상품권 결제											*	
 ------------------------------------------------------------------------------------------------------->
 
            <?php
            	
            	if($inipay->m_resultCode == "00"){
            		
            		switch($inipay->m_payMethod){
            		       /*--------------------------------------------------------------------------------------------------------
	 			*													*
	 			* 결제 성공시 이용안내 보여주기 			    						*	
				*													*
	 			*  1.  신용카드 						                			*
	 			--------------------------------------------------------------------------------------------------------*/
	
				case(Card): 
					echo "<table width='510' border='0' cellspacing='0' cellpadding='0'>
         					<tr> 
         					    <td height='25'  style='padding:0 0 0 9'><img src='img/icon.gif' width='10' height='11'> 
         					      <strong><font color='433F37'>이용안내</font></strong></td>
         					  </tr>
         					  <tr> 
         					    <td  style='padding:0 0 0 23'> 
         					      <table width='470' border='0' cellspacing='0' cellpadding='0'>
         					        <tr>          					          
         					          <td height='25'>(1) 신용카드 청구서에 <b>\"이니시스(inicis.com)\"</b>으로 표기됩니다.<br>
         					          (2) LG카드 및 BC카드의 경우 <b>\"이니시스(이용 상점명)\"</b>으로 표기되고, 삼성카드의 경우 <b>\"이니시스(이용상점 URL)\"</b>로 표기됩니다.</td>
         					        </tr>
         					        <tr> 
         					          <td height='1' colspan='2' align='center'  background='img/line.gif'></td>
         					        </tr>
         					        
         					      </table></td>
         					  </tr>
         				      </table>";
					break;
				
			       /*--------------------------------------------------------------------------------------------------------
	 			*													*
	 			* 결제 성공시 이용안내 보여주기 			    						*	
				*													*
	 			*  2.  ISP 						                				*
	 			--------------------------------------------------------------------------------------------------------*/
	 			
				case(VCard): // ISP
					echo "<table width='510' border='0' cellspacing='0' cellpadding='0'>
         					<tr> 
         					    <td height='25'  style='padding:0 0 0 9'><img src='img/icon.gif' width='10' height='11'> 
         					      <strong><font color='433F37'>이용안내</font></strong></td>
         					  </tr>
         					  <tr> 
         					    <td  style='padding:0 0 0 23'> 
         					      <table width='470' border='0' cellspacing='0' cellpadding='0'>
         					        <tr>          					          
         					          <td height='25'>(1) 신용카드 청구서에 <b>\"이니시스(inicis.com)\"</b>으로 표기됩니다.<br>
         					          (2) LG카드 및 BC카드의 경우 <b>\"이니시스(이용 상점명)\"</b>으로 표기되고, 삼성카드의 경우 <b>\"이니시스(이용상점 URL)\"</b>로 표기됩니다.</td>
         					        </tr>
         					        <tr> 
         					          <td height='1' colspan='2' align='center'  background='img/line.gif'></td>
         					        </tr>
         					        
         					      </table></td>
         					  </tr>
         				      </table>";
					break;
					
			       /*--------------------------------------------------------------------------------------------------------
	 			*													*
	 			* 결제 성공시 이용안내 보여주기 			    						*	
				*													*
	 			*  3. 핸드폰 						                				*
	 			--------------------------------------------------------------------------------------------------------*/
	 			
				case(HPP): // 휴대폰
					echo "<table width='510' border='0' cellspacing='0' cellpadding='0'>
         					<tr> 
         					    <td height='25'  style='padding:0 0 0 9'><img src='img/icon.gif' width='10' height='11'> 
         					      <strong><font color='433F37'>이용안내</font></strong></td>
         					  </tr>
         					  <tr> 
         					    <td  style='padding:0 0 0 23'> 
         					      <table width='470' border='0' cellspacing='0' cellpadding='0'>
         					        <tr>          					          
         					          <td height='25'>(1) 핸드폰 청구서에 <b>\"소액결제\"</b> 또는 <b>\"외부정보이용료\"</b>로 청구됩니다.<br>
         					          (2) 본인의 월 한도금액을 확인하시고자 할 경우 각 이동통신사의 고객센터를 이용해주십시오.
         					          </td>
         					        </tr>
         					        <tr> 
         					          <td height='1' colspan='2' align='center'  background='img/line.gif'></td>
         					        </tr>
         					        
         					      </table></td>
         					  </tr>
         				      </table>";
					break;				
			       /*--------------------------------------------------------------------------------------------------------
	 			*													*
	 			* 결제 성공시 이용안내 보여주기 			    						*	
				*													*
	 			*  4. 전화 결제 (ARS1588Bill)				                				*
	 			--------------------------------------------------------------------------------------------------------*/
	 			
				case(Ars1588Bill): 
					echo "<table width='510' border='0' cellspacing='0' cellpadding='0'>
         					<tr> 
         					    <td height='25'  style='padding:0 0 0 9'><img src='img/icon.gif' width='10' height='11'> 
         					      <strong><font color='433F37'>이용안내</font></strong></td>
         					  </tr>
         					  <tr> 
         					    <td  style='padding:0 0 0 23'> 
         					      <table width='470' border='0' cellspacing='0' cellpadding='0'>
         					        <tr>          					          
         					          <td height='25'>(1) 전화 청구서에 <b>\"컨텐츠 이용료\"</b>로 청구됩니다.<br>
                                                          (2) 월 한도금액의 경우 동일한 가입자의 경우 등록된 전화번호 기준이 아닌 주민등록번호를 기준으로 책정되어 있습니다.<br>
                                                          (3) 전화 결제취소는 당월에만 가능합니다.
         					          </td>
         					        </tr>
         					        <tr> 
         					          <td height='1' colspan='2' align='center'  background='img/line.gif'></td>
         					        </tr>
         					        
         					      </table></td>
         					  </tr>
         				      </table>";
					break;
					
			       /*--------------------------------------------------------------------------------------------------------
	 			*													*
	 			* 결제 성공시 이용안내 보여주기 			    						*	
				*													*
	 			*  5. 폰빌 결제 (PhoneBill)				                				*
	 			--------------------------------------------------------------------------------------------------------*/
				
				case(PhoneBill): 
					echo "<table width='510' border='0' cellspacing='0' cellpadding='0'>
         					<tr> 
         					    <td height='25'  style='padding:0 0 0 9'><img src='img/icon.gif' width='10' height='11'> 
         					      <strong><font color='433F37'>이용안내</font></strong></td>
         					  </tr>
         					  <tr> 
         					    <td  style='padding:0 0 0 23'> 
         					      <table width='470' border='0' cellspacing='0' cellpadding='0'>
         					        <tr>          					          
         					          <td height='25'>(1) 전화 청구서에 <b>\"인터넷 컨텐츠 (음성)정보이용료\"</b>로 청구됩니다.<br>
                                                          (2) 월 한도금액의 경우 동일한 가입자의 경우 등록된 전화번호 기준이 아닌 주민등록번호를 기준으로 책정되어 있습니다.<br>
                                                          (3) 전화 결제취소는 당월에만 가능합니다.
         					          </td>
         					        </tr>
         					        <tr> 
         					          <td height='1' colspan='2' align='center'  background='img/line.gif'></td>
         					        </tr>
         					        
         					      </table></td>
         					  </tr>
         				      </table>";
					break;
				
			       /*--------------------------------------------------------------------------------------------------------
	 			*													*
	 			* 결제 성공시 이용안내 보여주기 			    						*	
				*													*
	 			*  6. OK CASH BAG POINT					                				*
	 			--------------------------------------------------------------------------------------------------------*/
	 			
				case(OCBPoint): 
					echo "<table width='510' border='0' cellspacing='0' cellpadding='0'>
         					<tr> 
         					    <td height='25'  style='padding:0 0 0 9'><img src='img/icon.gif' width='10' height='11'> 
         					      <strong><font color='433F37'>이용안내</font></strong></td>
         					  </tr>
         					  <tr> 
         					    <td  style='padding:0 0 0 23'> 
         					      <table width='470' border='0' cellspacing='0' cellpadding='0'>
         					        <tr>          					          
         					          <td height='25'>(1) OK CASH BAG 포인트 결제취소는 당월에만 가능합니다.
         					          </td>
         					        </tr>
         					        <tr> 
         					          <td height='1' colspan='2' align='center'  background='img/line.gif'></td>
         					        </tr>
         					        
         					      </table></td>
         					  </tr>
         				      </table>";
					break;
					
			       /*--------------------------------------------------------------------------------------------------------
	 			*													*
	 			* 결제 성공시 이용안내 보여주기 			    						*	
				*													*
	 			*  7. 은행계좌이체					                				*
	 			--------------------------------------------------------------------------------------------------------*/
	 			
				case(DirectBank):  
					echo "<table width='510' border='0' cellspacing='0' cellpadding='0'>
         					<tr> 
         					    <td height='25'  style='padding:0 0 0 9'><img src='img/icon.gif' width='10' height='11'> 
         					      <strong><font color='433F37'>이용안내</font></strong></td>
         					  </tr>
         					  <tr> 
         					    <td  style='padding:0 0 0 23'> 
         					      <table width='470' border='0' cellspacing='0' cellpadding='0'>
         					        <tr>          					          
         					          <td height='25'>(1) 고객님의 통장에는 <b>\"이니시스\"</b>로 표기됩니다.<br>
         					          </td>
         					        </tr>
         					        <tr> 
         					          <td height='1' colspan='2' align='center'  background='img/line.gif'></td>
         					        </tr>
         					        
         					      </table></td>
         					  </tr>
         				      </table>";
					break;
					
			       /*--------------------------------------------------------------------------------------------------------
	 			*													*
	 			* 결제 성공시 이용안내 보여주기 			    						*	
				*													*
	 			*  8. 무통장 입금 서비스					                				*
	 			--------------------------------------------------------------------------------------------------------*/		
				case(VBank):  
					echo "<table width='510' border='0' cellspacing='0' cellpadding='0'>
         					<tr> 
         					    <td height='25'  style='padding:0 0 0 9'><img src='img/icon.gif' width='10' height='11'> 
         					      <strong><font color='433F37'>이용안내</font></strong></td>
         					  </tr>
         					  <tr> 
         					    <td  style='padding:0 0 0 23'> 
         					      <table width='470' border='0' cellspacing='0' cellpadding='0'>
         					        <tr>          					          
         					          (1) 상기 결과는 입금예약이 완료된 것일뿐 실제 입금완료가 이루어진 것이 아닙니다.<br>
         					          (2) 상기 입금계좌로 해당 상품금액을 무통장입금(창구입금)하시거나, 인터넷 뱅킹 등을 통한 온라인 송금을 하시기 바랍니다.<br>
                                                          (3) 반드시 입금기한 내에 입금하시기 바라며, 대금입금시 반드시 주문하신 금액만 입금하시기 바랍니다.
                                                          </td>
         					        </tr>
         					        <tr> 
         					          <td height='1' colspan='2' align='center'  background='img/line.gif'></td>
         					        </tr>
         					        
         					      </table></td>
         					  </tr>
         				      </table>";
					break;
					
			       /*--------------------------------------------------------------------------------------------------------
	 			*													*
	 			* 결제 성공시 이용안내 보여주기 			    						*	
				*													*
	 			*  9. 문화상품권 결제					                				*
	 			--------------------------------------------------------------------------------------------------------*/
	 			
				case(Culture):  
					echo "<table width='510' border='0' cellspacing='0' cellpadding='0'>
         					<tr> 
         					    <td height='25'  style='padding:0 0 0 9'><img src='img/icon.gif' width='10' height='11'> 
         					      <strong><font color='433F37'>이용안내</font></strong></td>
         					  </tr>
         					  <tr> 
         					    <td  style='padding:0 0 0 23'> 
         					      <table width='470' border='0' cellspacing='0' cellpadding='0'>
         					        <tr>          					          
         					          <td height='25'>(1) 문화상품권을 온라인에서 이용하신 경우 오프라인에서는 사용하실 수 없습니다.<br>
         					                          (2) 컬쳐캐쉬 잔액이 남아있는 경우, 고객님의 컬쳐캐쉬 잔액을 다시 사용하시려면 컬쳐랜드 ID를 기억하시기 바랍니다.
         					          </td>
         					        </tr>
         					        <tr> 
         					          <td height='1' colspan='2' align='center'  background='img/line.gif'></td>
         					        </tr>
         					        
         					      </table></td>
         					  </tr>
         				      </table>";
					break;
			}
		}
		
	    ?>		
            
            <!-- 이용안내 끝 -->
            
          </td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td><img src="img/bottom01.gif" width="632" height="13"></td>
  </tr>
</table>
</center></body>
</html>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
