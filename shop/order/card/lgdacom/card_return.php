<?php
include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.lgdacom.php";

// PG결제 위변조 체크 및 유효성 체크
if (forge_order_check($_POST['LGD_OID'],$_POST['LGD_AMOUNT']) === false) {
	msg('주문 정보와 결제 정보가 맞질 않습니다. 다시 결제 바랍니다.','../../order_fail.php?ordno='.$_POST['LGD_OID'],'parent');
	exit();
}

// Ncash 결제 승인 API
include "../../../lib/naverNcash.class.php";
$naverNcash = new naverNcash();
if($naverNcash->useyn=='Y')
{
	if($_POST['LGD_CUSTOM_USABLEPAY']=="SC0040") $ncashResult = $naverNcash->payment_approval($_POST['LGD_OID'], false);
	else $ncashResult = $naverNcash->payment_approval($_POST['LGD_OID'], true);
	if($ncashResult===false)
	{
		msg('네이버 마일리지 사용에 실패하였습니다.', '../../order_fail.php?ordno='.$_POST['LGD_OID'],'parent');
		exit();
	}
}

	/*
	 * [최종결제요청 페이지(STEP2-2)]
	 *
	 * LG데이콤으로 부터 내려받은 LGD_PAYKEY(인증Key)를 가지고 최종 결제요청.(파라미터 전달시 POST를 사용하세요)
	 */

	$configPath					= $_SERVER['DOCUMENT_ROOT'].$cfg['rootDir']."/conf/lgdacom";		//LG데이콤에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정.

	/*
	 *************************************************
	 * 1.최종결제 요청 - BEGIN
	 *  (단, 최종 금액체크를 원하시는 경우 금액체크 부분 주석을 제거 하시면 됩니다.)
	 *************************************************
	 */
	$CST_PLATFORM				= $_POST['CST_PLATFORM'];
	$CST_MID					= $_POST['CST_MID'];
	$LGD_MID					= (("test" == $CST_PLATFORM)?"t":"").$CST_MID;
	$LGD_PAYKEY					= $_POST['LGD_PAYKEY'];
	$LGD_CUSTOM_PROCESSTIMEOUT	= $_POST['LGD_CUSTOM_PROCESSTIMEOUT'];

	require_once("./XPayClient.php");
	$xpay = &new XPayClient($configPath, $CST_PLATFORM);
	$xpay->Init_TX($LGD_MID);
	$amount_check = true;

	//최종 금액체크를 원하시면 주석을 제거해 주세요.
	/*
	$xpay->Set("LGD_TXNAME", "AmountCheck");
	$xpay->Set("LGD_PAYKEY", $LGD_PAYKEY);
	$xpay->Set("LGD_AMOUNT", $HTTP_POST_VARS["LGD_AMOUNT"]);
	if ($xpay->TX()) {
		if($xpay->Response_Code() != "0000" ) {
			$amount_check = false;
		}
	}else {
		$amount_check = false;
	}
	*/

	$xpay->Set("LGD_TXNAME", "PaymentByKey");
	$xpay->Set("LGD_PAYKEY", $LGD_PAYKEY);
	$xpay->Set("LGD_CUSTOM_PROCESSTIMEOUT", $LGD_CUSTOM_PROCESSTIMEOUT);
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

	// 데이콤에서 받은 value
	if ($amount_check) {
		if ($xpay->TX()) {
			if($xpay->Response("LGD_PAYTYPE",0)=='SC0010') $payTypeStr = "신용카드";
			if($xpay->Response("LGD_PAYTYPE",0)=='SC0030') $payTypeStr = "계좌이체";
			if($xpay->Response("LGD_PAYTYPE",0)=='SC0040') $payTypeStr = "가상계좌";
			if($xpay->Response("LGD_PAYTYPE",0)=='SC0060') $payTypeStr = "핸드폰";

			$tmp_log[] = "데이콤 XPay 결제요청에 대한 결과";
			$tmp_log[] = "TX Response_code : ".$xpay->Response_Code();
			$tmp_log[] = "TX Response_msg : ".$xpay->Response_Msg();
			$tmp_log[] = "결과코드 : ".$xpay->Response("LGD_RESPCODE",0)." (0000(성공) 그외 실패)";
			$tmp_log[] = "결과내용 : ".$xpay->Response("LGD_RESPMSG",0);
			$tmp_log[] = "해쉬데이타 : ".$xpay->Response("LGD_HASHDATA",0);
			$tmp_log[] = "결제금액 : ".$xpay->Response("LGD_AMOUNT",0);
			$tmp_log[] = "상점아이디 : ".$xpay->Response("LGD_MID",0);
			$tmp_log[] = "주문번호 : ".$xpay->Response("LGD_OID",0);
			$tmp_log[] = "결제방법 : ".$payTypeStr;
			$tmp_log[] = "결제일시 : ".$xpay->Response("LGD_PAYDATE",0);

			$card_nm	= $xpay->Response("LGD_FINANCENAME",0);

			/*
			$keys = $xpay->Response_Names();
			foreach($keys as $name) {
				echo $name . " = " . $xpay->Response($name, 0) . "<br>";
			}
			echo "<p>";
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
				/*$isDBOK = true; //DB처리 실패시 false로 변경해 주세요.
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
			}
		}else {
			//최종결제요청 결과 실패 DB처리
			//echo "최종결제요청 결과 실패 DB처리하시기 바랍니다.<br>";
		}
	}else{
		//결제금액 체크 오류 화면처리
		//echo "최종 결제요청 금액이 상이합니다. 금액을 확인하여 주십시오.";
	}

	$settlelog = "{$ordno} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . implode( "\n", $tmp_log ) . "\n-----------------------------------\n";

	### 전자보증보험 발급
	@session_start();
	if (session_is_registered('eggData') === true && $xpay->Response_Code() == "0000" ){
		if ($_SESSION[eggData][ordno] == $ordno && $_SESSION[eggData][resno1] != '' && $_SESSION[eggData][resno2] != '' && $_SESSION[eggData][agree] == 'Y'){
			include '../../../lib/egg.class.usafe.php';
			$eggData = $_SESSION[eggData];
			switch ($xpay->Response("LGD_PAYTYPE",0)){
				case "SC0010":
					$eggData[payInfo1] = $xpay->Response("LGD_FINANCENAME",0); # (*) 결제정보(카드사)
					$eggData[payInfo2] = $xpay->Response("LGD_FINANCEAUTHNUM",0); # (*) 결제정보(승인번호)
					break;
				case "SC0030":
					$eggData[payInfo1] = $xpay->Response("LGD_FINANCENAME",0); # (*) 결제정보(은행명)
					$eggData[payInfo2] = $xpay->Response("LGD_TID",0); # (*) 결제정보(승인번호 or 거래번호)
					break;
				case "SC0040":
					$eggData[payInfo1] = $xpay->Response("LGD_FINANCENAME",0); # (*) 결제정보(은행명)
					$eggData[payInfo2] = $xpay->Response("LGD_ACCOUNTNUM",0); # (*) 결제정보(계좌번호)
					break;
			}
			$eggCls = new Egg( 'create', $eggData );
			//if ( $eggCls->isErr == true && $xpay->Response("LGD_PAYTYPE",0) == "SC0060" ){
				//$xpay->Response("LGD_RESPCODE",0) = '';
			//}
			//else if ( $eggCls->isErr == true && in_array($xpay->Response("LGD_PAYTYPE",0), array("SC0010","SC0030")) );
		}
		session_unregister('eggData');
	}

	### 가상계좌 결제의 재고 체크 단계 설정
	$res_cstock = true;
	if($cfg['stepStock'] == '1' && $xpay->Response("LGD_PAYTYPE",0) == "SC0040") $res_cstock = false;

	### item check stock
	include "../../../lib/cardCancel.class.php";
	$cancel = new cardCancel();
	if(!$cancel->chk_item_stock($ordno) && $res_cstock){
		$step = 51;
	}

	// DB 처리
	$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
	if($oData['step'] > 0 || $oData['vAccount'] != '' || !strcmp($xpay->Response_Code(),"S007")){		// 중복결제

		### 로그 저장
		$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");

	} else if( "0000" == $xpay->Response_Code() && $step != 51 ) {	// 결제성공

		$query = "
		select * from
			".GD_ORDER." a
			left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
		where
			a.ordno='$ordno'
		";
		$data = $db->fetch($query);

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
		if ($data[m_no] && $data[emoney]){
			setEmoney($data[m_no],-$data[emoney],"상품구입시 적립금 결제 사용",$ordno);
		}

		### 주문확인메일
		if(function_exists('getMailOrderData')){
			sendMailCase($data['email'],0,getMailOrderData($ordno));
		}

		### SMS 변수 설정
		$dataSms = $data;

		if ($xpay->Response("LGD_PAYTYPE",0) != "SC0040"){
			sendMailCase($data[email],1,$data);			### 입금확인메일
			sendSmsCase('incash',$data[mobileOrder]);	### 입금확인SMS
		} else {
			sendSmsCase('order',$data[mobileOrder]);	### 주문확인SMS
		}

		go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");
	}else{
		if ($step == '51') {
			$cancel->cancel_db_proc($ordno);
		} else {
			$db->query("update ".GD_ORDER." set step2='54', settlelog=concat(ifnull(settlelog,''),'".$settlelog."') where ordno='".$ordno."'");
			$db->query("update ".GD_ORDER_ITEM." set istep='54' where ordno='".$ordno."'");
		}

		// Ncash 결제 승인 취소 API 호출
		if($naverNcash->useyn=='Y') $naverNcash->payment_approval_cancel($ordno);

		go("../../order_fail.php?ordno=$ordno","parent");
	}
?>
