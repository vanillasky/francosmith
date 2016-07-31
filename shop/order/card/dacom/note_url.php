<?php
//이 페이지는 수정하지 마십시요. 수정시 html태그나 자바스크립트가 들어가는 경우 동작을 보장할 수 없습니다.
//관련 db처리는 write.php에서 참조하는 함수 write_success(),write_failure(),write_hasherr()에서 관련 루틴을 추가하시면 됩니다.
//위의 각 함수에는 현재 결제에 관한 log남기게 됩니다. 상점서버에서 남기실 절대경로로 맞게 수정하여 주세요

//hash데이타값이 맞는 지 확인 하는 루틴은 데이콤에서 받은 데이타가 맞는지 확인하는 것이므로 꼭 사용하셔야 합니다
//정상적인 결제 건임에도 불구하고 노티 페이지의 오류나 네트웍 문제 등으로 인한 hash 값의 오류가 발생할 수도 있습니다.
//그러므로 hash 오류건에 대해서는 오류 발생시 원인을 파악하여 즉시 수정 및 대처해 주셔야 합니다.

//정상적으로 처리한 경우에도 데이콤에서 응답을 받지 못한 경우는 결제결과가 중복해서 나갈 수 있으므로 관련한 처리도 고려되어야 합니다.
//이 페이지는 상점연동성공 여부에 따라 'OK' 또는 'FAIL' 이라는 문자를 표시하도록 되었습니다.
//PHP에서는 되도록이면 error_reporting() 함수를 이용하여 개발 후에는 단순한 경고메세지는 출력이 되지 않도록 해주십시요.

	// 상점연동 function page
	include("./note_write.php");



	// 데이콤에서 받은 value
	$respcode="";		// 응답코드: 0000(성공) 그외 실패
	$respmsg="";		// 응답메세지
	$hashdata="";		// 해쉬값
	$transaction="";	// 데이콤이 부여한 거래번호
	$mid="";			// 상점아이디
	$oid="";			// 주문번호
	$amount="";			// 거래금액
	$currency="";		// 통화코드('410':원화, '840':달러)
	$paytype="";		// 결제수단코드
	$msgtype="";		// 거래종류에 따른 데이콤이 정의한 코드
	$paydate="";		// 거래일시(승인일시/이체일시)
	$buyer="";			// 구매자명
	$productinfo="";	// 상품정보
	$buyerssn="";		// 구매자주민등록번호
	$buyerid="";		// 구매자ID
	$buyeraddress="";	// 구매자주소
	$buyerphone="";		// 구매자전화번호
	$buyeremail="";		// 구매자이메일주소
	$receiver="";		// 수취인명
	$receiverphone="";	// 수취인전화번호
	$deliveryinfo="";	// 배송정보
	$producttype="";	// 상품유형
	$productcode="";	// 상품코드
	$financecode="";	// 결제기관코드(카드종류/은행코드)
	$financename="";	// 결제기관이름(카드이름/은행이름)
	$useescrow="";		// 최종 에스크로 적용 여부 - Y:적용, N:미적용

	$authnumber="";		// 승인번호(신용카드)
	$cardnumber="";		// 카드번호(신용카드)
	$cardexp="";		// 유효기간(신용카드)
	$cardperiod="";		// 할부개월수(신용카드)
	$nointerestflag="";	// 무이자할부여부(신용카드) - '1'이면 무이자할부 '0'이면 일반할부
	$transamount="";	// 환율적용금액(신용카드)
	$exchangerate="";	// 환율(신용카드)

	$pid="";			// 예금주/휴대폰소지자 주민등록번호(계좌이체/휴대폰)
	$accountowner="";	// 계좌소유주이름(계좌이체)
	$accountnumber="";	// 계좌번호(계좌이체, 무통장입금)

	$telno="";			// 휴대폰번호(휴대폰)

	$payer="";			// 입금인(무통장입금)
	$cflag="";			// 무통장입금 플래그(무통장입금) - 'R':계좌할당, 'I':입금, 'C':입금취소
	$tamount="";		// 입금총액(무통장입금)
	$camount="";		// 현입금액(무통장입금)
	$bankdate="";		// 입금또는취소일시(무통장입금)
	$seqno="";			// 입금순서(무통장입금)
	$receiptnumber="";	// 현금영수증 승인번호


	$resp = false;		// 결과연동 성공여부

	$respcode = get_param("respcode");
	$respmsg = get_param("respmsg");
	$hashdata = get_param("hashdata");
	$transaction = get_param("transaction");
	$mid = get_param("mid");
	$oid = get_param("oid");
	$amount = get_param("amount");
	$currency = get_param("currency");
	$paytype = get_param("paytype");
	$msgtype = get_param("msgtype");
	$paydate = get_param("paydate");
	$buyer = get_param("buyer");
	$productinfo = get_param("productinfo");
	$buyerssn = get_param("buyerssn");
	$buyerid = get_param("buyerid");
	$buyeraddress = get_param("buyeraddress");
	$buyerphone = get_param("buyerphone");
	$buyeremail = get_param("buyeremail");
	$receiver = get_param("receiver");
	$receiverphone = get_param("receiverphone");
	$deliveryinfo = get_param("deliveryinfo");
	$producttype = get_param("producttype");
	$productcode = get_param("productcode");
	$financecode = get_param("financecode");
	$financename = get_param("financename");
	$useescrow = get_param("useescrow");
	$authnumber = get_param("authnumber");
	$cardnumber = get_param("cardnumber");
	$cardexp = get_param("cardexp");
	$cardperiod = get_param("cardperiod");
	$nointerestflag = get_param("nointerestflag");
	$transamount = get_param("transamount");
	$exchangerate = get_param("exchangerate");
	$pid = get_param("pid");
	$accountnumber = get_param("accountnumber");
	$accountowner = get_param("accountowner");
	$telno = get_param("telno");
	$payer = get_param("payer");
	$cflag = get_param("cflag");
	$tamount = get_param("tamount");
	$camount = get_param("camount");
	$bankdate = get_param("bankdate");
	$seqno= get_param("seqno");
	$receiptnumber= get_param("receiptnumber");


	$mertkey = $pg['mertkey']; //데이콤에서 발급한 상점키로 변경해 주시기 바랍니다.

	$hashdata2 = md5($transaction.$mid.$oid.$paydate.$mertkey);

	$value = array( "msgtype"		=> $msgtype,
					"transaction"	=> $transaction,
					"mid"			=> $mid,
					"oid"			=> $oid,
					"amount"		=> $amount,
					"currency"		=> $currency,
					"paytype"		=> $paytype,
					"paydate"		=> $paydate,
					"buyer"			=> $buyer,
					"productinfo"	=> $productinfo,
					"respcode"		=> $respcode,
					"respmsg"		=> $respmsg,
					"buyerssn"		=> $buyerssn,
					"buyerid"		=> $buyerid,
					"buyeraddress"	=> $buyeraddress,
					"buyerphone"	=> $buyerphone,
					"buyeremail"	=> $buyeremail,
					"receiver"		=> $receiver,
					"receiverphone"	=> $receiverphone,
					"deliveryinfo"	=> $deliveryinfo,
					"producttype"	=> $producttype,
					"productcode"	=> $productcode,
					"financecode"	=> $financecode,
					"financename"	=> $financename,
					"useescrow"		=> $useescrow,
					"authnumber"	=> $authnumber,
					"cardnumber"	=> $cardnumber,
					"cardexp"		=> $cardexp,
					"cardperiod"	=> $cardperiod,
					"nointerestflag"=> $nointerestflag,
					"transamount"	=> $transamount,
					"exchangerate"	=> $exchangerate,
					"pid"			=> $pid,
					"accountnumber"	=> $accountnumber,
					"accountowner"	=> $accountowner,
					"telno"			=> $telno,
					"payer"			=> $payer,
					"cflag"			=> $cflag,
					"tamount"		=> $tamount,
					"camount"		=> $camount,
					"bankdate"		=> $bankdate,
					"hashdata"		=> $hashdata,
					"hashdata2"		=> $hashdata2,
					"seqno"			=> $seqno,
					"receiptnumber"	=> $receiptnumber);

	### 전자보증보험 발급
	$eggs = get_param("eggs");
	if ($value[paytype] == 'SC0040' && $value[cflag] != 'R');
	else if (isset($eggs[o]) === true && $respcode == "0000" && $hashdata2 == $hashdata){
		if ($eggs[o] == $value[oid] && $eggs[r1] != '' && $eggs[r2] != '' && $eggs[a] == 'Y'){
			include '../../../lib/egg.class.usafe.php';
			$eggData = array('ordno' => $eggs[o], 'issue' => $eggs[i], 'resno1' => $eggs[r1], 'resno2' => $eggs[r2], 'agree' => $eggs[a]);
			switch ($value[paytype]){
				case "SC0010":
					$eggData[payInfo1] = $value[financename]; # (*) 결제정보(카드사)
					$eggData[payInfo2] = $value[authnumber]; # (*) 결제정보(승인번호)
					break;
				case "SC0030":
					$eggData[payInfo1] = $value[financename]; # (*) 결제정보(은행명)
					$eggData[payInfo2] = $value[transaction]; # (*) 결제정보(승인번호 or 거래번호)
					break;
				case "SC0040":
					$eggData[payInfo1] = $value[financename]; # (*) 결제정보(은행명)
					$eggData[payInfo2] = $value[accountnumber]; # (*) 결제정보(계좌번호)
					break;
			}
			$eggCls = new Egg( 'create', $eggData );
			if ( $eggCls->isErr == true && $value[paytype] == "SC0040" ){
				$respcode = '';
			}
			else if ( $eggCls->isErr == true && in_array($value[paytype], array("SC0010","SC0030")) );
		}
	}

	if ($hashdata2 == $hashdata) { //해쉬값 검증이 성공하면
		$ordno = $value['oid'];

		### 가상계좌 결제의 재고 체크 단계 설정
		$res_cstock = true;
		if($value['paytype'] == 'SC0040' && $cfg['stepStock'] == '0' && $value['cflag'] != 'R') $res_cstock = false;
		if($value['paytype'] == 'SC0040' && $cfg['stepStock'] == '1' && $value['cflag'] != 'I') $res_cstock = false;

		### item check stock
		include "../../../lib/cardCancel.class.php";
		$cancel = new cardCancel();
		if(!$cancel->chk_item_stock($ordno) && $res_cstock){
			$respcode = "OUTOFSTOCK";
			$cancel->cancel_db_proc($ordno,$transaction);
		}else{
			$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
			if($oData['step'] > 0 || ($oData['vAccount'] != '' && $value['cflag'] != 'I') || $respcode == 'S007'){ //결제가 중복결제하면
				$resp = write_overlap($value);
			}else if($respcode == "0000"){ //결제가 성공이면
				### Ncash 거래 확정 API
				include "../../../lib/naverNcash.class.php";
				$naverNcash = new naverNcash();
				$naverNcash->deal_done($ordno);
				$resp = write_success($value);
			}else { //결제가 실패이면
				$resp = write_failure($value);
			}
		}
	} else { //해쉬값 검증이 실패이면
		write_hasherr($value);
	}

	if($respcode == "OUTOFSTOCK"){
		echo "ROLLBACK";
	}else if($resp){ //결과연동이 성공이면
		echo "OK";
	}else{ //결과연동이 실패이면
		echo "FAIL";
	}
?>
