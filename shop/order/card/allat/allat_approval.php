<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005,  Flyfox All right reserved.
@파일내용: All@Pay™ Plus 2.0 (Version 1.0.0.5) [2006-04-06]
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/

	include "../../../lib/library.php";
	include "../../../conf/pg.allat.php";
	include "./allatutil.php";

	### 결제인터페이스의 결과값 Get : 이전 주문결제페이지에서 Request Get
	$at_data	= "allat_shop_id=".urlencode($pg[id])."&allat_amt=$_POST[allat_amt]&allat_enc_data=$_POST[allat_enc_data]&allat_cross_key=$pg[crosskey]";
	$at_txt		= ApprovalReq($at_data,"NOSSL");	// 설정 필요 SSL
														// NOSSL - 에러 코드 0212 일경우 사용함.
	$REPLYCD	= getValue("reply_cd",$at_txt);			//결과코드
	$REPLYMSG	= getValue("reply_msg",$at_txt);			//결과메세지

	debug($_POST);
	debug($REPLYCD);
	debug($REPLYMSG);

	exit;
	/************************************************************************************************************/
	
	// 환경변수 정의
	include "../../lib/library.php";
	$dbconn = connectdb();
	$curr_path=rootpath()."shop/";
	session_start();
	
	// 쇼핑몰 기본 정보 호출
	$strSQL		= "SELECT * FROM tb_addmallinfo WHERE sno='1'";
	$getData	= getinfo($strSQL);
	$g_cardsettle_ID			=$getData[allat];				# All@Pay ID
	$g_cardsettle_FormKey		=$getData[allat_FormKey];		# All@Pay Form Key
	$g_cardsettle_CrossKey		=$getData[allat_CrossKey];		# All@Pay CrossKey
	
	// 올앳관련 함수 Include
	//----------------------
	include "./allatutil.php";
	
	// Request Value Define
	$at_cross_key	= $g_cardsettle_CrossKey;	//설정필요
	$at_shop_id		= $g_cardsettle_ID;			//설정필요
	$at_amt			= $_POST["allat_amt"];		// [중요]승인금액(allat_amt) 다시 Setting 
												// allat_amt input값을 그대로 Setting 하는 것 보다 
												// 해킹 방지를 위하여 장바구니의 Session 값을 이용하는 것을 권장
	
	// 결제인터페이스의 결과값 Get : 이전 주문결제페이지에서 Request Get
	//-------------------------------------------------------------------------
	$at_data	= "allat_shop_id=".urlencode($at_shop_id).                          
				"&allat_amt=".$at_amt. 
				"&allat_enc_data=".$_POST["allat_enc_data"].
				"&allat_cross_key=".$at_cross_key;
	$at_txt		= ApprovalReq($at_data,"SSL");		// 설정 필요 SSL
													// NOSSL - 에러 코드 0212 일경우 사용함.
	
	$REPLYCD	=getValue("reply_cd",$at_txt);		//결과코드
	$REPLYMSG	=getValue("reply_msg",$at_txt);		//결과메세지
	
	
	# 결제 모드별 처리
	$etc_CardMode	=$_POST['etc_CardMode'];		// 결제 모드
	if( !$etc_CardMode || $etc_CardMode == "" ){	// 일반결제
		$OrdTable	= "tb_order";
		$reorder	= "n";
	}else if( $etc_CardMode == "re" ){				// 재결제
		$OrdTable = "tb_order";
		$reorder	= "y";
	}else if( $etc_CardMode == "etc" ){				// 기타결제
		$OrdTable = "tb_order_etc";
		$reorder	= "n";
	}
	
	# 에스크로 여부
	$escrowUseYN	= $_POST['allat_escrow_yn'];	// 에스크로 여부
	
	# 주문 번호
	$orderNumber	=$_POST['allat_order_no'];
	
	if( !strcmp($REPLYCD,"0000") ){
		// reply_cd "0000" 일때만 성공
		$ORDER_NO       =getValue("order_no",$at_txt);			//주문번호
		$AMT            =getValue("amt",$at_txt);				//승인금액
		$PAY_TYPE       =getValue("pay_type",$at_txt);			//지불수단 - 3D, ISP, NOR, ABANK
		$APPROVAL_YMDHMS=getValue("approval_ymdhms",$at_txt);	//승인일시
		$SEQ_NO         =getValue("seq_no",$at_txt);			//거래일련번호
		$APPROVAL_NO    =getValue("approval_no",$at_txt);		//승인번호
		$CARD_ID        =getValue("card_id",$at_txt);			//카드ID - 카드종류코드(예:01,02,… … )
		$CARD_NM	    =getValue("card_nm",$at_txt);			//카드명 - 카드종류명(예:삼성, 국민, … … )
		$SELL_MM	    =getValue("sell_mm",$at_txt);			//할부개월
		$ZEROFEE_YN	    =getValue("zerofee_yn",$at_txt);		//무이자(Y),일시불(N)
		$CERT_YN	    =getValue("cert_yn",$at_txt);			//인증여부 - 인증(Y),미인증(N)
		$CONTRACT_YN	=getValue("contract_yn",$at_txt);		//직가맹여부 - 3자가맹점(Y),대표가맹점(N)
		$BANK_ID	    =getValue("bank_id",$at_txt);			//은행ID
		$BANK_NM	    =getValue("bank_nm",$at_txt);			//은행명
		$CASH_BILL_NO	=getValue("cash_bill_no",$at_txt);		//현금영수증일련번호 - 현금영수증 등록시
		$ESCROW_YN      =getValue("escrow_yn",$at_txt);			//에스크로여부 - Y(에스크로), N(미적용)
		
		$Memo=getinfo("SELECT settlememo FROM ".$OrdTable." WHERE ordno='".$orderNumber."'");
		
		$msgadmemos=$Memo['settlememo']."결제자동확인 : 결제확인시간(".date("Y-m-d H:i:s").")".chr(10);
		
		# 카드결제
		if( $PAY_TYPE != "ABANK"){
			$msgadmemos.="거래번호 : ".$SEQ_NO.chr(10);
			$msgadmemos.="결과코드 : ".$REPLYCD." (0000이면 지불 성공)".chr(10);
			$msgadmemos.="결과내용 : ".$REPLYMSG.chr(10);
			$msgadmemos.="지불방법 : ".$PAY_TYPE.chr(10);
			$msgadmemos.="승인번호 : ".$APPROVAL_NO.chr(10);
			$msgadmemos.="할부기간 : ".$SELL_MM.chr(10);
			$msgadmemos.="무이자할부 여부 : ".$ZEROFEE_YN." (무이자(Y),일시불(N))".chr(10);
			$msgadmemos.="신용카드사 코드 : ".$CARD_ID.chr(10);
			$msgadmemos.="신용카드명 : ".$CARD_NM.chr(10);
			$msgadmemos.="승인일시 : ".$APPROVAL_YMDHMS.chr(10);
			$msgadmemos.="인증여부 : ".$CERT_YN.chr(10);
			$msgadmemos.="에스크로여부 : ".$ESCROW_YN.chr(10);
			$msgadmemos.="상품 주문번호 : ".$orderNumber.chr(10).chr(10);
		}
		#계좌이체
		if( $PAY_TYPE == "ABANK"){
			$msgadmemos.="거래번호 : ".$SEQ_NO.chr(10);
			$msgadmemos.="결과코드 : ".$REPLYCD." (0000이면 지불 성공)".chr(10);
			$msgadmemos.="결과내용 : ".$REPLYMSG.chr(10);
			$msgadmemos.="지불방법 : ".$PAY_TYPE.chr(10);
			$msgadmemos.="은행이름 : ".$BANK_NM.chr(10);
			$msgadmemos.="은행코드 : ".$BANK_ID.chr(10);
			$msgadmemos.="승인일시 : ".$APPROVAL_YMDHMS.chr(10);
			$msgadmemos.="에스크로여부 : ".$ESCROW_YN.chr(10);
			$msgadmemos.="현금영수증 일련 번호 : ".$CASH_BILL_NO.chr(10);
			$msgadmemos.="상품 주문번호 : ".$orderNumber.chr(10).chr(10);
		}
		
		# 에스크로 여부
		$escrowUseYN	= $ESCROW_YN;
		
		# 포인트 차감
		if( !$etc_CardMode || $etc_CardMode == "" ){	// 일반결제
			include $rootpath."shop/proc/order_pointdown.php";
		}
		
		# 입금 확인처리
		$delstatuscd	= "03";
		
		# 카드결제성공
		$ApprNo		= $APPROVAL_NO;		// 승인번호
		$TidNo		= $SEQ_NO;			// 거래번호
		include $rootpath."shop/card/cardComplete.php";
		
		@mysql_Close();
		
		//echo "결과코드			: ".$REPLYCD."<br>";	
		//echo "결과메세지		: ".$REPLYMSG."<br>";	    
		//echo "주문번호			: ".$ORDER_NO."<br>";	    
		//echo "승인금액			: ".$AMT."<br>";	    
		//echo "지불수단			: ".$PAY_TYPE."<br>";	    
		//echo "승인일시			: ".$APPROVAL_YMDHMS."<br>";	    
		//echo "거래일련번호		: ".$SEQ_NO."<br>";	 
		//echo "=========== 신용 카드 ===========<br>";
		//echo "승인번호			: ".$APPROVAL_NO."<br>";
		//echo "카드ID			: ".$CARD_ID."<br>";
		//echo "카드명			: ".$CARD_NM."<br>";
		//echo "할부개월			: ".$SELL_MM."<br>";
		//echo "무이자여부		: ".$ZEROFEE_YN."<br>";
		//echo "인증여부			: ".$CERT_YN."<br>";
		//echo "직가맹여부		: ".$CONTRACT_YN."<br>";
		//echo "=========== 계좌 이체 ===========<br>";
		//echo "은행ID			: ".$BANK_ID."<br>";
		//echo "은행명			: ".$BANK_NM."<br>";
		//echo "현금영수증 일련 번호	: ".$CASH_BILL_NO."<br>";
		//echo "에스크로 적용 여부	: ".$ESCROW_YN."<br>";		
		
	}else{
		
		$Memo=getinfo("SELECT settlememo FROM ".$OrdTable." WHERE ordno='".$orderNumber."'");
		
		$msgadmemos=$Memo['settlememo']."결제실패로 인한 자동취소 : 자동취소시간(".date("Y-m-d H:i:s").")".chr(10);
		$msgadmemos.="결과코드 : ".$REPLYCD." (00이면 지불 성공)".chr(10);
		$msgadmemos.="결과내용 : ".$REPLYMSG.chr(10);
		
		# 카드결제실패
		$FailMeg	= $REPLYMSG;		// 실패메시지
		$TidNo		= $SEQ_NO;			// 거래번호
		include $rootpath."shop/card/cardComplete_fail.php";
		
		@mysql_Close();
		
		// reply_cd 가 "0000" 아닐때는 에러 (자세한 내용은 매뉴얼참조)
		// reply_msg 는 실패에 대한 메세지
		//echo "결과코드  : ".$REPLYCD."<br>";	
		//echo "결과메세지: ".$REPLYMSG."<br>";
	}
?>