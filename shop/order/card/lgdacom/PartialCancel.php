<?php
    /*
     * [결제 부분취소 요청 페이지]
     *
     * LG텔레콤으로 부터 내려받은 거래번호(LGD_TID)를 가지고 취소 요청을 합니다.(파라미터 전달시 POST를 사용하세요)
     * (승인시 LG텔레콤으로 부터 내려받은 PAYKEY와 혼동하지 마세요.)
     */

	$CST_PLATFORM	= $data['service'];								//LG데이콤 결제 서비스 선택(test:테스트, service:서비스)
	$CST_MID					= $data['mid'];									//상점아이디(LG데이콤으로 부터 발급받으신 상점아이디를 입력하세요)
	$LGD_MID					= (("test" == $CST_PLATFORM)?"t":"").$CST_MID;	//상점아이디(자동생성)
	$LGD_TID					= $data['tid'];									//LG데이콤으로 부터 내려받은 거래번호(LGD_TID)\
	$LGD_CANCELAMOUNT			= $data['price'];								//취소할 금액
	$LGD_CANCELTAXFREEAMOUNT	= $data['taxfree'];								//취소할 면세금액
	$LGD_CANCELREASON			= "관리자 취소";

 	$configPath					= $data['shopdir']."/conf/lgdacom";				//LG데이콤에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정.

	require_once(dirname(__FILE__)."/XPayClient.php");
	$xpay = &new XPayClient($configPath, $CST_PLATFORM);
	$xpay->Init_TX($LGD_MID);
	
	$xpay->Set("LGD_TXNAME", "PartialCancel");
	$xpay->Set("LGD_TID", $LGD_TID);
	$xpay->Set("LGD_CANCELAMOUNT", $LGD_CANCELAMOUNT);
    $xpay->Set("LGD_CANCELTAXFREEAMOUNT", $LGD_CANCELTAXFREEAMOUNT);
    $xpay->Set("LGD_CANCELREASON", $LGD_CANCELREASON);
    $xpay->Set("LGD_RFACCOUNTNUM", $LGD_RFACCOUNTNUM);
    $xpay->Set("LGD_RFBANKCODE", $LGD_RFBANKCODE);
    $xpay->Set("LGD_RFCUSTOMERNAME", $LGD_RFCUSTOMERNAME);
    $xpay->Set("LGD_RFPHONE", $LGD_RFPHONE);
    /*
     * 1. 결제 부분취소 요청 결과처리
     *
     */
	$xpay->TX();

	if( "0000" == $xpay->Response_Code() ){
		//1)결제취소결과 화면처리(성공,실패 결과 처리를 하시기 바랍니다.)
		$settlelog = '데이콤 XPay 카드 취소 결과'."\n";
		$settlelog .= '결과코드 : '.$xpay->Response_Code()."\n";
		$settlelog .= '결과내용 : '.$xpay->Response_Msg()."\n";
		$keys = $xpay->Response_Names();
            foreach($keys as $name) {
                $settlelog .=  $name . " = " . $xpay->Response($name, 0) . "<br>";
			}
		$cardCancelResult	= true;
    }else {
		//2)API 요청 실패 화면처리
		$settlelog = $data['oid'].' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= '데이콤 XPay 카드 취소 실패'."\n";
		$settlelog .= '결과코드 : '.$xpay->Response_Code()."\n";
		$settlelog .= '결과내용 : '.$xpay->Response_Msg()."\n";
		$settlelog .= '-----------------------------------'."\n";
		$cardCancelResult	= false;
    }
?>
