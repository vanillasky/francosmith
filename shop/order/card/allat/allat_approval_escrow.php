<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005,  Flyfox All right reserved.
@파일내용: All@Pay™ Plus 2.0 (Version 1.0.0.5) [2006-04-06]
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/

	// 환경변수 정의
	include_once "../../lib/library.php";
	$dbconn=connectdb();
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
	
	// 결제인터페이스의 결과값 Get : 이전 주문결제페이지에서 Request Get
	//-------------------------------------------------------------------------
	$at_cross_key	= $g_cardsettle_CrossKey;	//설정필요
	$at_shop_id		= $g_cardsettle_ID;			//설정필요
	
	$at_data   = "allat_shop_id=".urlencode($at_shop_id).
                 "&allat_enc_data=".$_POST["allat_enc_data"].
                 "&allat_cross_key=".$at_cross_key ;  
	$at_txt = EscrowChkReq($at_data,"SSL"); //설정 필요 https(SSL),http(NOSSL)
	         
	$REPLYCD   =getValue("reply_cd",$at_txt);		//결과코드
	$REPLYMSG  =getValue("reply_msg",$at_txt);		//결과메세지
	
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
	
	# 주문 번호
	$orderNumber	=$_POST['allat_order_no'];
	
	$Memo=getinfo("SELECT settlememo FROM ".$OrdTable." WHERE ordno='".$orderNumber."'");
	$msgadmemos	=$Memo['settlememo'].chr(10);
	$msgadmemos.="배송등록 확인 : 배송등록 확인시간(".date("Y-m-d H:i:s").")".chr(10);
	$msgadmemos.="결과코드 : ".$REPLYCD." (0000이면 지불 성공)".chr(10);
	$msgadmemos.="결과내용 : ".$REPLYMSG.chr(10);
	
	if( !strcmp($REPLYCD,"0000") ){
		// reply_cd "0000" 일때만 성공
		$ESCROWCHECK_YMDSHMS=getValue("escrow_check_ymdhms",$at_txt);
		$msgadmemos.="에스크로 배송 개시일 : ".$ESCROWCHECK_YMDSHMS.chr(10);
		
		// 주문서에 업데이트
		$strSQL = "UPDATE ".$OrdTable." SET escrowTrans='".$_POST['allat_escrow_express_nm']."', escrowInvno='".$_POST['allat_escrow_send_no']."', settlememo='$msgadmemos' WHERE ordno='".$orderNumber."'";
		getinfo($strSQL,"handle");
		
		echo "<script>alert('Escrow 배송등록이 완료 되었습니다.');location.replace('".$_POST['returnOrderUrl']."');</script>";
		
	}else{
		
		// 주문서에 업데이트
		$strSQL = "UPDATE ".$OrdTable." SET settlememo='$msgadmemos' WHERE ordno='".$orderNumber."'";
		getinfo($strSQL,"handle");
		
		echo "<script>alert('".$REPLYMSG."의 이유로 인해 Escrow 배송등록이 실패하였습니다.');location.replace('".$_POST['returnOrderUrl']."');</script>";
	}
?>