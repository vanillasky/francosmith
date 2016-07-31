<?php

include "../../../../lib/library.php";
include "../../../../conf/config.mobileShop.php";
include "../../../../conf/config.php";
include "../../../../conf/pg_mobile.lgdacom.php";


    /*
     * [최종결제요청 페이지(STEP2-2)]
     *
     * LG텔레콤으로 부터 내려받은 LGD_PAYKEY(인증Key)를 가지고 최종 결제요청.(파라미터 전달시 POST를 사용하세요)
     */

	$configPath = $_SERVER['DOCUMENT_ROOT'].$cfg['rootDir']."/conf/lgdacom_mobile"; //LG텔레콤에서 제공한 환경파일("/conf/lgdacom.conf,/conf/mall.conf") 위치 지정. 

    /*
     *************************************************
     * 1.최종결제 요청 - BEGIN
     *  (단, 최종 금액체크를 원하시는 경우 금액체크 부분 주석을 제거 하시면 됩니다.)
     *************************************************
     */
//	$CST_PLATFORM               = $HTTP_POST_VARS["CST_PLATFORM"];
//	$CST_MID                    = $HTTP_POST_VARS["CST_MID"];
	$CST_PLATFORM               = $pg_mobile['serviceType'];
    $CST_MID                    = $pg_mobile['id'];

    $LGD_MID                    = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;
    $LGD_PAYKEY                 = $HTTP_POST_VARS["LGD_PAYKEY"];

   require_once("./XPayClient.php");
    $xpay = &new XPayClient($configPath, $CST_PLATFORM);
    $xpay->Init_TX($LGD_MID);
    
    $xpay->Set("LGD_TXNAME", "PaymentByKey");
    $xpay->Set("LGD_PAYKEY", $LGD_PAYKEY);
    
    //금액을 체크하시기 원하는 경우 아래 주석을 풀어서 이용하십시요.
	//$DB_AMOUNT = "DB나 세션에서 가져온 금액"; //반드시 위변조가 불가능한 곳(DB나 세션)에서 금액을 가져오십시요.
	//$xpay->Set("LGD_AMOUNTCHECKYN", "Y");
	//$xpay->Set("LGD_AMOUNT", $DB_AMOUNT);
	    
    /*
     *************************************************
     * 1.최종결제 요청(수정하지 마세요) - END
     *************************************************
     */

    /*
     * 2. 최종결제 요청 결과처리
     *
     * 최종 결제요청 결과 리턴 파라미터는 연동메뉴얼을 참고하시기 바랍니다.
     */

	$ordno	= $_POST['LGD_OID'];

    if ($xpay->TX()) {

		if($xpay->Response("LGD_PAYTYPE",0)=='SC0010') $payTypeStr = "신용카드";
		if($xpay->Response("LGD_PAYTYPE",0)=='SC0030') $payTypeStr = "계좌이체";
		if($xpay->Response("LGD_PAYTYPE",0)=='SC0040') $payTypeStr = "가상계좌";
		if($xpay->Response("LGD_PAYTYPE",0)=='SC0060') $payTypeStr = "핸드폰";

		$tmp_log[] = "LGU+ SmartXPay 결제요청에 대한 결과";
		$tmp_log[] = "TX Response_code : ".$xpay->Response_Code();
		$tmp_log[] = "TX Response_msg : ".$xpay->Response_Msg();
		$tmp_log[] = "결과코드 : ".$xpay->Response("LGD_RESPCODE",0)." (0000(성공) 그외 실패)";
		$tmp_log[] = "결과내용 : ".$xpay->Response("LGD_RESPMSG",0);
		$tmp_log[] = "해쉬데이타 : ".$xpay->Response("LGD_HASHDATA",0);
		$tmp_log[] = "결제금액 : ".$xpay->Response("LGD_AMOUNT",0);
		$tmp_log[] = "상점아이디 : ".$xpay->Response("LGD_MID",0);
		$tmp_log[] = "거래번호 : ".$xpay->Response("LGD_TID",0);
		$tmp_log[] = "주문번호 : ".$xpay->Response("LGD_OID",0);
		$tmp_log[] = "결제방법 : ".$payTypeStr;
		$tmp_log[] = "결제일시 : ".$xpay->Response("LGD_PAYDATE",0);

		$card_nm	= $xpay->Response("LGD_FINANCENAME",0);
        
		/*           
        $keys = $xpay->Response_Names();
        foreach($keys as $name) {
            echo $name . " = " . $xpay->Response($name, 0) . "<br>";
        }
		*/
           
        if( "0000" == $xpay->Response_Code() ) {
			$tmp_log[] = "거래번호 : ".$xpay->Response("LGD_TID",0);
			$tmp_log[] = "에스크로 적용 여부 : ".$xpay->Response("LGD_ESCROWYN",0);
			$tmp_log[] = "결제기관코드 : ".$xpay->Response("LGD_FINANCECODE",0);
			$tmp_log[] = "결제기관명 : ".$xpay->Response("LGD_FINANCENAME",0);

			switch ($xpay->Response("LGD_PAYTYPE",0)){
				case "SC0010":	// 신용카드
					$tmp_log[] = "결제기관승인번호 : ".$xpay->Response("LGD_FINANCEAUTHNUM",0);
					$tmp_log[] = "신용카드번호 : ".$xpay->Response("LGD_CARDNUM",0)." (일반 가맹점은 *처리됨)";
					$tmp_log[] = "신용카드할부개월 : ".$xpay->Response("LGD_CARDINSTALLMONTH",0);
					$tmp_log[] = "신용카드무이자여부 : ".$xpay->Response("LGD_CARDNOINTYN",0)." (1:무이자, 0:일반)";
					break;
				case "SC0030":	// 계좌이체
					$tmp_log[] = "현금영수증승인번호 : ".$xpay->Response("LGD_CASHRECEIPTNUM",0);
					$tmp_log[] = "현금영수증자진발급제유무 : ".$xpay->Response("LGD_CASHRECEIPTSELFYN",0)." Y: 자진발급";
					$tmp_log[] = "현금영수증종류 : ".$xpay->Response("LGD_CASHRECEIPTKIND",0)." 0:소득공제, 1:지출증빙";
					$tmp_log[] = "계좌소유주이름 : ".$xpay->Response("LGD_ACCOUNTOWNER",0);
					break;
				case "SC0040":	// 가상계좌
					$tmp_log[] = "현금영수증승인번호 : ".$xpay->Response("LGD_CASHRECEIPTNUM",0);
					$tmp_log[] = "현금영수증자진발급제유무 : ".$xpay->Response("LGD_CASHRECEIPTSELFYN",0)." Y: 자진발급";
					$tmp_log[] = "현금영수증종류 : ".$xpay->Response("LGD_CASHRECEIPTKIND",0)." 0:소득공제, 1:지출증빙";
					$tmp_log[] = "가상계좌발급번호 : ".$xpay->Response("LGD_ACCOUNTNUM",0);
					$tmp_log[] = "가상계좌입금자명 : ".$xpay->Response("LGD_PAYER",0);
					$tmp_log[] = "입금누적금액 : ".$xpay->Response("LGD_CASTAMOUNT",0);
					$tmp_log[] = "현입금금액 : ".$xpay->Response("LGD_CASCAMOUNT",0);
					$tmp_log[] = "거래종류 : ".$xpay->Response("LGD_CASFLAG",0)." (R:할당,I:입금,C:취소)";
					$tmp_log[] = "가상계좌일련번호 : ".$xpay->Response("LGD_CASSEQNO",0);
					break;
				case "SC0060":	// 핸드폰
					break;
			}
         	//최종결제요청 결과 성공 DB처리
           	//echo "최종결제요청 결과 성공 DB처리하시기 바랍니다.<br>";

            //최종결제요청 결과 성공 DB처리 실패시 Rollback 처리
			/*	$isDBOK = true; //DB처리 실패시 false로 변경해 주세요.
          	if( !$isDBOK ) {
           		echo "<p>";
           		$xpay->Rollback("상점 DB처리 실패로 인하여 Rollback 처리 [TID:" . $xpay->Response("LGD_TID",0) . ",MID:" . $xpay->Response("LGD_MID",0) . ",OID:" . $xpay->Response("LGD_OID",0) . "]");            		            		
            		
                echo "TX Rollback Response_code = " . $xpay->Response_Code() . "<br>";
                echo "TX Rollback Response_msg = " . $xpay->Response_Msg() . "<p>";
            		
                if( "0000" == $xpay->Response_Code() ) {
                  	echo "자동취소가 정상적으로 완료 되었습니다.<br>";
                }else{
          			echo "자동취소가 정상적으로 처리되지 않았습니다.<br>";
                }
          	}  
			*/
        }else{
          	//최종결제요청 결과 실패 DB처리
			//echo "최종결제요청 결과 실패 DB처리하시기 바랍니다.<br>";
			exit;
        }
    }else {
        //2)API 요청실패 화면처리
       
		echo "결제요청이 실패하였습니다.  <br>";
        echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
        echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";
            
        //최종결제요청 결과 실패 DB처리
        echo "최종결제요청 결과 실패 DB처리하시기 바랍니다.<br>";
		exit;
    }

	$settlelog = "{$ordno} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . implode( "\n", $tmp_log ) . "\n-----------------------------------\n";

	// DB 처리
	$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
	if($oData['step'] > 0 || $oData['vAccount'] != '' || !strcmp($xpay->Response_Code(),"S007")){		// 중복결제

		### 로그 저장
		$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");

	} else if( "0000" == $xpay->Response_Code() ) {

		$query = "
		select * from
			".GD_ORDER." a
			left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
		where
			a.ordno='$ordno'
		";
		$data = $db->fetch($query);

		include "../../../../lib/cart.class.php";

		$cart = new Cart($_COOKIE[gd_isDirect]);
		$cart->chkCoupon();
		$cart->delivery = $data[delivery];
		$cart->dc = $member[dc]."%";
		$cart->calcu();

		### 주문확인메일
		$data[cart] = $cart;
		$data[str_settlekind] = $r_settlekind[$data[settlekind]];
		sendMailCase($data[email],0,$data);

		### 에스크로 여부 확인
		if($xpay->Response("LGD_ESCROWYN",0) == 'Y'){
			$escrowyn = "y";
			$escrowno = $xpay->Response("LGD_TID",0);
		}else{
			$escrowyn = "n";
			$escrowno = "";
		}

		### 결제 정보 저장
		$step = 1;
		$qrc1 = "cyn='y', cdt=now(), cardtno='".$xpay->Response("LGD_TID",0)."',";
		$qrc2 = "cyn='y',";

		### 가상계좌 결제시 계좌정보 저장
		if ($xpay->Response("LGD_PAYTYPE",0) == 'SC0040'){
			$vAccount = $xpay->Response("LGD_FINANCENAME",0)." ".$xpay->Response("LGD_ACCOUNTNUM",0)." ".$xpay->Response("LGD_PAYER",0);
			$step = 0; $qrc1 = $qrc2 = "";
		}

		### 현금영수증 저장
		if ($xpay->Response("LGD_CASHRECEIPTNUM",0)){
			$qrc1 .= "cashreceipt='".$xpay->Response("LGD_CASHRECEIPTNUM",0)."',";
		}

		### 실데이타 저장
		$db->query("
		update ".GD_ORDER." set $qrc1
			step		= '$step',
			step2		= '',
			escrowyn	= '$escrowyn',
			escrowno	= '$escrowno',
			vAccount	= '$vAccount',
			settlelog	= concat(ifnull(settlelog,''),'$settlelog')
		where ordno='$ordno'"
		);
		$db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$ordno'");

		### 주문로그 저장
		orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

		### 재고 처리
		setStock($ordno);

		### 상품구입시 적립금 사용
		if ($sess[m_no] && $data[emoney]){
			setEmoney($sess[m_no],-$data[emoney],"상품구입시 적립금 결제 사용",$ordno);
		}

		### SMS 변수 설정
		$dataSms = $data;

		if ($xpay->Response("LGD_PAYTYPE",0) != "SC0040"){
			sendMailCase($data[email],1,$data);			### 입금확인메일
			sendSmsCase('incash',$data[mobileOrder]);	### 입금확인SMS
		} else {
			sendSmsCase('order',$data[mobileOrder]);	### 주문확인SMS
		}

		go($cfgMobileShop['mobileShopRootDir']."/ord/order_end.php?ordno=$ordno&card_nm=$card_nm","parent");
	}else{
		$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno' and step2=50");
		$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno' and istep=50");
		go($cfgMobileShop['mobileShopRootDir']."/ord/order_fail.php?ordno=$ordno","parent");
	}
?>
