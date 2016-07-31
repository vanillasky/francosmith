<?php
include "../../../lib/library.php";
include "../../../conf/config.php";
//include "../../../conf/pg.lgdacom.php";

// 투데이샵 사용중인 경우 PG 설정 교체
resetPaymentGateway();

	/*
	 * [상점 결제결과처리(DB) 페이지]
	 *
	 * 1) 위변조 방지를 위한 hashdata값 검증은 반드시 적용하셔야 합니다.
	 *
	 */
	$LGD_RESPCODE			= $_POST['LGD_RESPCODE'];				// 응답코드: 0000(성공) 그외 실패
	$LGD_RESPMSG			= $_POST['LGD_RESPMSG'];				// 응답메세지
	$LGD_MID				= $_POST['LGD_MID'];					// 상점아이디
	$LGD_OID				= $_POST['LGD_OID'];					// 주문번호
	$LGD_AMOUNT				= $_POST['LGD_AMOUNT'];					// 거래금액
	$LGD_TID				= $_POST['LGD_TID'];					// 데이콤이 부여한 거래번호
	$LGD_PAYTYPE			= $_POST['LGD_PAYTYPE'];				// 결제수단코드
	$LGD_PAYDATE			= $_POST['LGD_PAYDATE'];				// 거래일시(승인일시/이체일시)
	$LGD_HASHDATA			= $_POST['LGD_HASHDATA'];				// 해쉬값
	$LGD_FINANCECODE		= $_POST['LGD_FINANCECODE'];			// 결제기관코드(은행코드)
	$LGD_FINANCENAME		= $_POST['LGD_FINANCENAME'];			// 결제기관이름(은행이름)
	$LGD_ESCROWYN			= $_POST['LGD_ESCROWYN'];				// 에스크로 적용여부
	$LGD_TIMESTAMP			= $_POST['LGD_TIMESTAMP'];				// 타임스탬프
	$LGD_ACCOUNTNUM			= $_POST['LGD_ACCOUNTNUM'];				// 계좌번호(무통장입금)
	$LGD_CASTAMOUNT			= $_POST['LGD_CASTAMOUNT'];				// 입금총액(무통장입금)
	$LGD_CASCAMOUNT			= $_POST['LGD_CASCAMOUNT'];				// 현입금액(무통장입금)
	$LGD_CASFLAG			= $_POST['LGD_CASFLAG'];				// 무통장입금 플래그(무통장입금) - 'R':계좌할당, 'I':입금, 'C':입금취소
	$LGD_CASSEQNO			= $_POST['LGD_CASSEQNO'];				// 입금순서(무통장입금)
	$LGD_CASHRECEIPTNUM		= $_POST['LGD_CASHRECEIPTNUM'];			// 현금영수증 승인번호
	$LGD_CASHRECEIPTSELFYN	= $_POST['LGD_CASHRECEIPTSELFYN'];		// 현금영수증자진발급제유무 Y: 자진발급제 적용, 그외 : 미적용
	$LGD_CASHRECEIPTKIND	= $_POST['LGD_CASHRECEIPTKIND'];		// 현금영수증 종류 0: 소득공제용 , 1: 지출증빙용

	/*
	 * 구매정보
	 */
	$LGD_BUYER				= $_POST['LGD_BUYER'];					// 구매자
	$LGD_PRODUCTINFO		= $_POST['LGD_PRODUCTINFO'];			// 상품명
	$LGD_BUYERID			= $_POST['LGD_BUYERID'];				// 구매자 ID
	$LGD_BUYERADDRESS		= $_POST['LGD_BUYERADDRESS'];			// 구매자 주소
	$LGD_BUYERPHONE			= $_POST['LGD_BUYERPHONE'];				// 구매자 전화번호
	$LGD_BUYEREMAIL			= $_POST['LGD_BUYEREMAIL'];				// 구매자 이메일
	$LGD_BUYERSSN			= $_POST['LGD_BUYERSSN'];				// 구매자 주민번호
	$LGD_PRODUCTCODE		= $_POST['LGD_PRODUCTCODE'];			// 상품코드
	$LGD_RECEIVER			= $_POST['LGD_RECEIVER'];				// 수취인
	$LGD_RECEIVERPHONE		= $_POST['LGD_RECEIVERPHONE'];			// 수취인 전화번호
	$LGD_DELIVERYINFO		= $_POST['LGD_DELIVERYINFO'];			// 배송지

	$LGD_MERTKEY = $pg['mertkey'];  //데이콤에서 발급한 상점키로 변경해 주시기 바랍니다.

	$LGD_HASHDATA2 = md5($LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_RESPCODE.$LGD_TIMESTAMP.$LGD_MERTKEY);

	/*
	 * 상점 처리결과 리턴메세지
	 *
	 * OK  : 상점 처리결과 성공
	 * 그외 : 상점 처리결과 실패
	 *
	 * ※ 주의사항 : 성공시 'OK' 문자이외의 다른문자열이 포함되면 실패처리 되오니 주의하시기 바랍니다.
	 */
	$resultMSG = "결제결과 상점 DB처리(LGD_CASNOTEURL) 결과값을 입력해 주시기 바랍니다.";

	$tmp_log[] = "데이콤 XPay 무통장입금에 대한 결과";
	$tmp_log[] = "결과코드 : ".$LGD_RESPCODE." (0000(성공) 그외 실패)";
	$tmp_log[] = "결과내용 : ".$LGD_RESPMSG;
	$tmp_log[] = "해쉬데이타 : ".$LGD_HASHDATA." (데이콤)";
	$tmp_log[] = "해쉬데이타 : ".$LGD_HASHDATA2." (상점)";
	$tmp_log[] = "결제금액 : ".$LGD_AMOUNT;
	$tmp_log[] = "상점아이디 : ".$LGD_MID;
	$tmp_log[] = "주문번호 : ".$LGD_OID;
	$tmp_log[] = "결제일시 : ".$LGD_PAYDATE;
	$tmp_log[] = "거래번호 : ".$LGD_TID;
	$tmp_log[] = "에스크로 적용 여부 : ".$LGD_ESCROWYN;
	$tmp_log[] = "결제기관코드 : ".$LGD_FINANCECODE;
	$tmp_log[] = "결제기관명 : ".$LGD_FINANCENAME;
	$tmp_log[] = "현금영수증승인번호 : ".$LGD_CASHRECEIPTNUM;
	$tmp_log[] = "현금영수증자진발급제유무 : ".$LGD_CASHRECEIPTSELFYN." Y: 자진발급";
	$tmp_log[] = "현금영수증종류 : ".$LGD_CASHRECEIPTKIND." 0:소득공제, 1:지출증빙";
	$tmp_log[] = "가상계좌발급번호 : ".$LGD_ACCOUNTNUM;
	$tmp_log[] = "가상계좌입금자명 : ".$LGD_PAYER;
	$tmp_log[] = "입금누적금액 : ".$LGD_CASTAMOUNT;
	$tmp_log[] = "현입금금액 : ".$LGD_CASCAMOUNT;
	$tmp_log[] = "거래종류 : ".$LGD_CASFLAG." (R:할당,I:입금,C:취소)";
	$tmp_log[] = "가상계좌일련번호 : ".$LGD_CASSEQNO;

	$ordno = $LGD_OID;

	$settlelog = "{$ordno} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . implode( "\n", $tmp_log ) . "\n-----------------------------------\n";

	$resultCHK	= true;
	if ( $LGD_HASHDATA2 == $LGD_HASHDATA ) { //해쉬값 검증이 성공이면
		### 가상계좌 결제의 재고 체크 단계 설정
		$res_cstock = true;
		if($cfg['stepStock'] == '0' && $LGD_CASFLAG != 'R') $res_cstock = false;
		if($cfg['stepStock'] == '1' && $LGD_CASFLAG != 'I') $res_cstock = false;

		### item check stock
		include "../../../lib/cardCancel.class.php";
		include "../../../lib/cardCancel_social.class.php";
		$cancel = new cardCancel_social();
		if(!$cancel->chk_item_stock($ordno) && $res_cstock){
			$resultMSG	= "상점 재고 부족으로 취소";
			$resultCHK	= false;
			$cancel->cancel_db_proc($ordno,$LGD_TID);
		}else{
			$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
			if($oData['step'] > 0 || ($oData['vAccount'] != '' && $LGD_CASFLAG != 'I') || $LGD_RESPCODE == 'S007'){ //결제가 중복결제하면

				$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");

				$resultMSG = "OK";

			}else if($LGD_RESPCODE == "0000"){ //결제가 성공이면

				if( "R" == $LGD_CASFLAG ) {
					/*
					 * 무통장 할당 성공 결과 상점 처리(DB) 부분
					 * 상점 결과 처리가 정상이면 "OK"
					 */
					//if( 무통장 할당 성공 상점처리결과 성공 ) $resultMSG = "OK";
					$resultMSG = "OK";
				}else if( "I" == $LGD_CASFLAG ) {
	 				/*
					 * 무통장 입금 성공 결과 상점 처리(DB) 부분
					 * 상점 결과 처리가 정상이면 "OK"
					 */

					### 결제 정보 저장
					$step = 1;
					$qrc1 = "cyn='y', cdt=now(), cardtno='".$LGD_TID."',";
					$qrc2 = "cyn='y',";

					$pre = $db->fetch("select step2, emoney, m_no from ".GD_ORDER." where ordno='$ordno'");
					$db->query("update ".GD_ORDER." set step='$step', step2='', $qrc1 settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
					$db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$ordno'");

					### 재고 처리
					setStock($ordno);

					### 상품구입시 적립금 사용
					if ($pre[m_no] && $pre[emoney]){
						setEmoney($pre[m_no],-$pre[emoney],"상품구입시 적립금 결제 사용",$ordno);
					}
					$resultMSG = "OK";
				}else if( "C" == $LGD_CASFLAG ) {
	 				/*
					 * 무통장 입금취소 성공 결과 상점 처리(DB) 부분
					 * 상점 결과 처리가 정상이면 "OK"
					 */
					//if( 무통장 입금취소 성공 상점처리결과 성공 ) $resultMSG = "OK";
					$resultMSG = "OK";
					$resultCHK	= false;
				}
			}else { //결제가 실패이면
				$resultMSG = "OK";
				$resultCHK	= false;
			}
		}
	} else { //해쉬값이 검증이 실패이면
		/*
		 * hashdata검증 실패 로그를 처리하시기 바랍니다.
		 */
		$resultMSG = "결제결과 상점 DB처리(LGD_CASNOTEURL) 해쉬값 검증이 실패하였습니다.";
		$resultCHK	= false;
	}

	if($resultCHK === false){
		//$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog'),cardtno='".$LGD_TID."' where ordno='$ordno'");
		$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno' and istep=50");
	}

	echo $resultMSG;
?>
