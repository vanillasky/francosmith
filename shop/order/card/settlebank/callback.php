<?php
    // 회원사 로직에 맞게 수정요함
    // input parameter 
    // $P_STATUS;      // 거래상태 : 0021(성공), 0031(실패), 0051(입금대기중)
    // $P_TR_NO;       // 거래번호
    // $P_AUTH_DT;     // 승인시간 
    // $P_AUTH_NO;     // 승인번호
    // $P_TYPE;        // 거래종류 (CARD, BANK)
    // $P_MID;         // 회원사아이디
    // $P_OID;         // 주문번호
    // $P_FN_CD1;      // 금융사코드 (은행코드, 카드코드)
    // $P_FN_CD2;      // 금융사코드 (은행코드, 카드코드)
    // $P_FN_NM;       // 금융사명 (은행명, 카드사명)
    // $P_UNAME;       // 주문자명
    // $P_AMT;         // 거래금액
    // $P_NOTI;        // 주문정보
    // $P_RMSG1;       // 메시지1
    // $P_RMSG2;       // 메시지2

    //	return value
	//  true  : 성공
	//  false : 실패
	//결제 성공 (가상계좌 제외)
	function noti_success($noti){
		
		$db = $GLOBALS['db'];
		$member = $GLOBALS['member'];
		$ordno = $noti['P_OID'];
		$resp = true;
		
		//현금영수증 조회
		$Cashreceipt = cash_receipt($noti);
		$noti['CASHRECEIPT'] = $Cashreceipt;

		// Ncash 결제 승인 API 네이버 마일리지
		include "../../../lib/naverNcash.class.php";
		$naverNcash = new naverNcash(true);
		if($naverNcash->useyn=='Y')
		{
			$ncashResult = $naverNcash->payment_approval($ordno, true);

			if($ncashResult===false)
			{
				$noti['LOGSTATUS'] = "네이버 마일리지 사용에 실패하였습니다.";
				$settlelog = basics_log($noti,'autoCancel');
				### 로그 저장
				proc_fail_db($noti, $settlelog);
				return false;
			}
		}

		### 전자보증보험 발급
		@session_start();
		if (session_is_registered('eggData') === true && $noti['P_STATUS'] == "0021" ){
			if ($_SESSION[eggData][ordno] == $noti['P_OID'] && $_SESSION[eggData][resno1] != '' && $_SESSION[eggData][resno2] != '' && $_SESSION[eggData][agree] == 'Y'){
				include '../../../lib/egg.class.usafe.php';
				$eggData = $_SESSION[eggData];
				switch ($noti['P_TYPE']){
					case "CARD":
						$eggData[payInfo1] = $noti['P_FN_NM']; # (*) 결제정보(카드사)
						$eggData[payInfo2] = $noti['P_AUTH_NO']; # (*) 결제정보(승인번호)
						break;
					case "BANK":
						$eggData[payInfo1] = $noti['P_FN_NM']; # (*) 결제정보(은행명)
						$eggData[payInfo2] = $noti['P_TR_NO']; # (*) 결제정보(승인번호 or 거래번호)
						break;
				}
				//$eggCls = new Egg( 'create', $eggData );
				//if ( $eggCls->isErr == true && $noti['P_TYPE'] == "HPP_" ){
					//$noti['P_STATUS'] = '';
				//}
				//else if ( $eggCls->isErr == true && in_array($xpay->Response("LGD_PAYTYPE",0), array("SC0010","SC0030")) );
			}
			session_unregister('eggData');
		}
		
		### item check stock
		$res_cstock = true;
		include "../../../lib/cardCancel.class.php";
		$cancel = new cardCancel();
		if(!$cancel->chk_item_stock($ordno) && $res_cstock){
			$step = 51;
		}
		
		// DB 처리
		$oData = $db->fetch("select cardtno, cyn, step, vAccount from ".GD_ORDER." where ordno='".$ordno."'");
		if($oData['cyn'] == 'y' && $oData['step'] > 0) { // 중복결제
				//재요청인지 확인한다.
				if (trim($noti['P_TR_NO']) == trim($oData['cardtno'])) {
					$noti['LOGSTATUS'] = "재전송";
					$settlelog = basics_log($noti);
					$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'".$settlelog."') where ordno='".$ordno."' ");
					$resp = true;
				} else { 
					$noti['LOGSTATUS'] = "중복결제";
					$settlelog = basics_log($noti,'autoCancel');
					### 결제취소
					proc_fail_db($noti, $settlelog);
					$resp = false;
				}

		// 결제성공
		} else if( $noti['P_STATUS'] == "0021" && $step != 51 ) {
			
			$query = "
			select * from
				".GD_ORDER." a
				left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
			where
				a.ordno='$ordno'
			";
			$data = $db->fetch($query);

			### 결제 정보 저장
			$step = 1;
			
			$qrc1 = "cyn='y', cdt=now(), pgAppNo='".$noti['P_AUTH_NO']."', pgAppDt='".$noti['P_AUTH_DT']."', cardtno='".$noti['P_TR_NO']."',";
			$qrc2 = "cyn='y',";

			### 현금영수증 저장
			if ($Cashreceipt[10]){ //현금영수증번호
				$qrc1 .= "cashreceipt='".$Cashreceipt[10]."',";
			}

			### 실데이타 저장
			$db->query("update ".GD_ORDER." set ".$qrc1." step = '".$step."', step2 = '', escrowyn = '', escrowno = '' where ordno='".$ordno."'");
			$db->query("update ".GD_ORDER_ITEM." set ".$qrc2." istep='".$step."' where ordno='".$ordno."'");

			### 주문로그 저장
			if($data[step2] == "") $data[step2]= "0"; 
			if($r_step[$step] == "") $r_step[$step]= "0"; 
			orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

			### 재고 처리
			setStock($ordno);

			### 상품구입시 적립금 사용
			if ($data[m_no] && $data[emoney]){
				setEmoney($data[m_no],-$data[emoney],"상품구입시 적립금 결제 사용",$ordno);
			}

			// 로그 저장 
			$noti['LOGSTATUS'] = "결제 성공";
			$settlelog = basics_log($noti);
			$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");

			$resp = true;

		} else {
			
			//상태 로그 생성 
			$noti['LOGSTATUS'] = "결제 실패";
			$settlelog = basics_log($noti);

			if ($step == '51') {
				$cancel->cancel_db_proc($ordno);
			} else {
				$db->query("update ".GD_ORDER." set step2='54', settlelog=concat(ifnull(settlelog,''),'".$settlelog."') where ordno='".$ordno."'");
				$db->query("update ".GD_ORDER_ITEM." set istep='54' where ordno='".$ordno."'");
			}
			
			// Ncash 결제 승인 취소 API 호출
			if($naverNcash->useyn=='Y') $naverNcash->payment_approval_cancel($ordno);

			$resp = false;
		}

	    noti_write("../../../log/settlebank/noti_success.".date('Ymd').".log", $noti);
	    return $resp;
	}

	//입금대기중
	function noti_waiting_pay($noti) {

		$db = $GLOBALS['db'];
		$member = $GLOBALS['member'];
		$ordno = $noti['P_OID'];
		$resp = true;
		
		//현금영수증 조회
		$Cashreceipt = cash_receipt($noti);
		$noti['CASHRECEIPT'] = $Cashreceipt;

		//가상계좌일경우 가상계좌와 입금기한이 같이 넘어옴 예)P_VACCT_NO=1234567|P_EXP_DT=20101025
		$exVal = P_rmesg1_explode($noti['P_RMESG1']);
		$pvacctno = $exVal[0];
		$pexpdt = $exVal[1];

		// Ncash 결제 승인 API 네이버 마일리지
		include "../../../lib/naverNcash.class.php";
		$naverNcash = new naverNcash(true);
		if($naverNcash->useyn=='Y')
		{
			if(trim($noti['P_TYPE'])=='VBANK') $ncashResult = $naverNcash->payment_approval($ordno, false);
			else $ncashResult = $naverNcash->payment_approval($ordno, true);
			if($ncashResult===false)
			{
				$noti['LOGSTATUS'] = "네이버 마일리지 사용에 실패하였습니다.";
				$settlelog = basics_log($noti);
				return true;
			}
		}

		### 가상계좌 결제의 재고 체크 단계 설정
		$res_cstock = true;
		if($cfg['stepStock'] == '1') $res_cstock = false;

		### item check stock
		include "../../../lib/cardCancel.class.php";
		$cancel = new cardCancel();
		if(!$cancel->chk_item_stock($ordno) && $res_cstock){
			$step = 51;
		}
		
		// DB 처리
		$oData = $db->fetch("select cyn, step, vAccount from ".GD_ORDER." where ordno='$ordno'");
		if($oData['cyn'] == 'y' && $oData['step'] > 0) { // 중복결제
					//기본 로그 
			$noti['LOGSTATUS'] = "가상계좌 중복 생성";
			$settlelog = basics_log($noti);
			### 로그 저장
			$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
			$resp = false;
		
		//상태가 입금대기이고 스텝이 51이 아니라며 
		} else if( $noti['P_STATUS'] == "0051" && $step != 51 ) {	// 입금대기
			
			$query = "
			select * from
				".GD_ORDER." a
				left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
			where
				a.ordno='$ordno'
			";
			$data = $db->fetch($query);

			### 결제 정보 저장
			$qrc1 = "cyn='y', cdt=now(),";
			$qrc2 = "cyn='y',";

			### 가상계좌 결제시 계좌정보 저장
			$vAccount = $noti['P_FN_NM']." ".$pvacctno." ".$noti['P_UNAME'];
			error_log("$vAccount :".$vAccount."\n", 3, "/www/s4qa/shop/log/sms_log.txt");
			$step = 0; $qrc1 = $qrc2 = "";

			### 현금영수증 저장
			if ($Cashreceipt[10]){ //현금영수증번호
				$qrc1 .= "cashreceipt='".$Cashreceipt[10]."',";
			}

			### 실데이타 저장
//				escrowyn	= '$escrowyn',
//				escrowno	= '$escrowno',
			$db->query("update ".GD_ORDER." set ".$qrc1." step = '".$step."' , step2 = '', vAccount = '".$vAccount."' where ordno='".$ordno."'");
			$db->query("update ".GD_ORDER_ITEM." set ".$qrc2." istep='".$step."' where ordno='".$ordno."'");

			### 주문로그 저장
			orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

			### 재고 처리
			setStock($ordno);

			### 상품구입시 적립금 사용
			if ($data[m_no] && $data[emoney]){
				setEmoney($data[m_no],-$data[emoney],"상품구입시 적립금 결제 사용",$ordno);
			}

			$noti['LOGSTATUS'] = "입금 대기";
			
			$settlelog = basics_log($noti);
			$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");

			$resp = true;

		} else {
			
			//상태 로그 생성 
			$noti['LOGSTATUS'] = "가상계좌 생성 실패";
			$settlelog = basics_log($noti);

			if ($step == '51') {
				$cancel->cancel_db_proc($ordno);
			} else {
				$db->query("update ".GD_ORDER." set step2='54', settlelog=concat(ifnull(settlelog,''),'".$settlelog."') where ordno='".$ordno."'");
				$db->query("update ".GD_ORDER_ITEM." set istep='54' where ordno='".$ordno."'");
			}

			// Ncash 결제 승인 취소 API 호출
			if($naverNcash->useyn=='Y') $naverNcash->payment_approval_cancel($ordno);
		}

	    noti_write("../../../log/settlebank/noti_success.".date('Ymd').".log", $noti);
	    return $resp;
    }
	

	//가상계좌 입금확인
	function noti_vbanksuccess($noti)
	{
		$db = $GLOBALS['db'];
		$member = $GLOBALS['member'];
		$ordno = $noti['P_OID'];
		$resp = true;
		
		//가상계좌일경우 가상계좌번호와 입금기한이 같이 넘어옴 예)P_VACCT_NO=1234567|P_EXP_DT=20101025
		$exVal = P_rmesg1_explode($noti['P_RMESG1']);
		$pvacctno = $exVal[0];
		$pexpdt = $exVal[1];

		### 전자보증보험 발급
		@session_start();
		if (session_is_registered('eggData') === true && $noti['P_STATUS'] == "0021" ){
			if ($_SESSION[eggData][ordno] == $noti['P_OID'] && $_SESSION[eggData][resno1] != '' && $_SESSION[eggData][resno2] != '' && $_SESSION[eggData][agree] == 'Y'){
				include '../../../lib/egg.class.usafe.php';
				$eggData = $_SESSION[eggData];

				$eggData[payInfo1] = $noti['P_FN_NM']; # (*) 결제정보(은행명)
				$eggData[payInfo2] = $pvacctno; # (*) 결제정보(계좌번호)
			}
			session_unregister('eggData');
		}

		$query = "
		select * from
			".GD_ORDER." a
			left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
		where
			a.ordno='$ordno'
		";
		$data = $db->fetch($query);

		### 결제 정보 저장
		$step = 1;
		$qrc1 = "pgAppNo='".$noti['P_AUTH_NO']."', pgAppDt='".$noti['P_AUTH_DT']."', cardtno='".$noti['P_TR_NO']."',";
		$qrc2 = "cyn='y',";

		### 실데이타 저장
		$db->query("update ".GD_ORDER." set ".$qrc1." step = '".$step."' , step2 = '', escrowyn = '', escrowno = '' where ordno='".$ordno."'");
		$db->query("update ".GD_ORDER_ITEM." set ".$qrc2." istep='".$step."' where ordno='".$ordno."'");
		
		### 주문로그 저장
		if($data[step2] == "") $data[step2]= "0"; 
		if($r_step[$step] == "") $r_step[$step]= "0"; 
		orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);


		### 재고 처리
		setStock($ordno);
		
		$noti['LOGSTATUS'] = "가상계좌 입금 완료";
		
		$settlelog = basics_log($noti);
		$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");

		$resp = true;

	    noti_write("../../../log/settlebank/noti_success.".date('Ymd').".log", $noti);
	    return $resp;
	}

	//결제 실패
	function noti_failure($noti){

		//기본 로그 
		$noti['LOGSTATUS'] = "결제 실패(PG사)";
		$settlelog = basics_log($noti);

		proc_fail_db($noti, $settlelog);
	    noti_write("../../../log/settlebank/noti_failure.".date('Ymd').".log", $noti);
	    return false;
	}

	//hash 에러시
	function noti_hash_err($noti) {

		$noti['LOGSTATUS'] = "데이터가 정상적이지 않습니다.";
		$settlelog = basics_log($noti);

		proc_fail_db($noti, $settlelog);
	    noti_write("../../../log/settlebank/noti_hash_err.".date('Ymd').".log", $noti);
		return false;
    }

	function noti_write($file, $noti) {
		$fp = fopen($file, "a+");
		ob_start();
		print_r($noti);
		$msg = ob_get_contents();
		ob_end_clean();
		fwrite($fp, $msg);
		fclose($fp);
	}
      
    function get_param($name){
		global $HTTP_POST_VARS, $HTTP_GET_VARS;
		if (!isset($HTTP_POST_VARS[$name]) || $HTTP_POST_VARS[$name] == "") {
			if (!isset($HTTP_GET_VARS[$name]) || $HTTP_GET_VARS[$name] == "") {
				return false;
			}
			else {
			 return $HTTP_GET_VARS[$name];
			}
		}
		return $HTTP_POST_VARS[$name];
	}

	//로그내용을 편집한다.
	function basics_log($logVal, $mode = null){
		$failReason = '';
		if($mode == 'autoCancel') {
			$failReason = '->자동 결제취소(15분이내 로 자동결제 취소처리가 완료됩니다.)';
		}

		$tmp_log[] = "세틀뱅크 결제요청에 대한 결과";
		$tmp_log[] = "결과내용 : ".$logVal['LOGSTATUS'].$failReason;
		$tmp_log[] = "결제방법 : ".$logVal['P_TYPE'];		// (카드(CARD), 계좌이체(BANK), 가상계좌(VBANK), 핸드폰(HPP_))";
		$tmp_log[] = "결과코드 : ".$logVal['P_STATUS']."(".P_status($logVal['P_STATUS']).")";		// (0021(성공), 0031(실패), 0051(입금대기중))";
		$tmp_log[] = "결제금액 : ".$logVal['P_AMT'];
		$tmp_log[] = "상점아이디 : ".$logVal['P_MID'];
		$tmp_log[] = "주문번호 : ".$logVal['P_OID'];
		$tmp_log[] = "결제일시 : ".$logVal['P_AUTH_DT'];
		$tmp_log[] = "거래번호 : ".$logVal['P_TR_NO'];
		$tmp_log[] = "결제기관명 : ".$logVal['P_FN_NM'];
		$tmp_log[] = "결제기관코드 : ".$logVal['P_FN_CD1'];
		$tmp_log[] = "결제기관코드영문 : ".$logVal['P_FN_CD2'];

		if($logVal['P_TYPE'] == 'CARD') {
			$tmp_log[] = "결제고객성명 : ".$logVal['P_UNAME'];
			$tmp_log[] = "결제기관승인번호 : ".$logVal['P_AUTH_NO'];

		} else if ($logVal['P_TYPE'] == 'BANK'){
			$tmp_log[] = "현금영수증승인번호 : ".$logVal['CASHRECEIPT'][10];
			$tmp_log[] = "현금영수증종류 : ".$logVal['CASHRECEIPT'][12];
			$tmp_log[] = "계좌소유주이름 : ".$logVal['P_UNAME'];
		} else if ($logVal['P_TYPE'] == 'VBANK'){
			$tmp_log[] = "현금영수증승인번호 : ".$logVal['CASHRECEIPT'][10];
			$tmp_log[] = "현금영수증종류 : ".$logVal['CASHRECEIPT'][12];

			$exVal = P_rmesg1_explode($logVal['P_RMESG1']);
			$tmp_log[] = "가상계좌발급번호 : ".$exVal[0];
			$tmp_log[] = "가상계좌 입금한도날짜 : ".$exVal[1];
			$tmp_log[] = "가상계좌입금자명 : ".$logVal['P_UNAME'];
			//$tmp_log[] = "입금누적금액 : ".$xpay->Response("LGD_CASTAMOUNT",0);
			//$tmp_log[] = "거래종류 : ".$xpay->Response("LGD_CASFLAG",0)." (R:할당,I:입금,C:취소)";
			
		} else if ($logVal['P_TYPE'] == 'HPP_'){
		
		}

		$tmp_log[] = "P_RMESG 내용 : ".$logVal['P_RMESG1'];
		$tmp_log[] = "세틀제공 HASH : ".$logVal['P_HASH'];
		$tmp_log[] = "검증용 HASH: ".$logVal['HashData'];
		$tmp_log[] = "사용자요청정보 : ".$logVal['P_NOTI'];

		$settlelog = "{$logVal['P_OID']} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . implode( "\n", $tmp_log ) . "\n-----------------------------------\n";

		return $settlelog;
	}

	//현금영수증 발급 조회
	function cash_receipt($logVal){
		$dt = substr($logVal['P_AUTH_DT'],0,8);
		$url="http://www.settlebank.co.kr/pgtrans/CashReceiptMultiAction.do?_method=getReceipt&mid=".$logVal['P_MID']."&trDt1=".$dt."&trDt2=".$dt."&trNo=".$logVal['P_TR_NO'];
				
		$ret = settle_Url_Reader($url);

		if( $ret != false ){
			$ret = preg_replace('/\<\!--.*--\>/','',$ret);
			$ret = str_replace('</br>','',$ret);
			$ret = str_replace('&nbsp;',' ',$ret);
			$receipt = explode('|',trim(iconv('UTF-8','EUC-KR',$ret)));
			if(!$receipt[10]) $receipt = false;
			
		}else{
			$receipt = false;
		}
		
		return $receipt;
	}
	
	//URl 조회값을 리턴합니다.
	function settle_Url_Reader($url,$post_data='')
	{
		$ret = "true";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER,  0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);

		
		if($post_data) {
			curl_setopt($ch, CURLOPT_POST,1); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
		}
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		//정보 조회
		$ret = curl_exec($ch);
		
		//에러 처리
		if( curl_error($ch) || $ret == false){
			//$ret = false;
		}
		
		//curl 세션닫기
		curl_close($ch);
		
		return $ret;
	}

	function P_rmesg1_explode($p_rmesg1) {
		$prmesg1 = explode('|',$p_rmesg1);
		$VbankNo = explode('=',$prmesg1[0]);
		$VbankDt = explode('=',$prmesg1[1]);

		$val[0] = $VbankNo[1];
		$val[1] = $VbankDt[1];
		
		return $val;
	}


	//계좌이체용 코드
	function VP_fn_cd1 ($code){
		$Vpfncd1 = array(
			'39' => '경남',
			'34' => '광주',
			'04' => '국민',
			'03' => '기업',
			'11' => '농협',
			'31' => '대구',
			'32' => '부산',
			'45' => '새마을',
			'07' => '수협',
			'88' => '신한',
			'05' => '외환',
			'20' => '우리',
			'71' => '우체국',
			'37' => '전북',
			'23' => 'SC제일',
			'35' => '제주',
			'21' => '조흥',
			'81' => '하나',
			'27' => '시티',
			'48' => '신협'
		);
		
		return $Vpfncd1[$code];
	}

	//계좌이체 영문명 코드
	function P_fn_cd2 ($code){
		$pfncd2 = array(
			'knb' => '경남',
			'kibank' => '광주',
			'kb' => '국민',
			'ibk' => '기업',
			'nacf' => '농협',
			'daegubank' => '대구',
			'psb' => '부산',
			'kfcc' => '새마을',
			'suhyup' => '수협',
			'shb' => '신한',
			'keb' => '외환',
			'woori' => '우리',
			'post' => '우체국',
			'jbbank' => '전북',
			'kfb' => 'SC제일',
			'cjb' => '제주',
			'chb' => '조흥',
			'hnb' => '하나',
			'citi' => '시티',
			'cu' => '신협'
		);
	
		return $pfncd2[$code];
	}

	//신용카드 코드값
	function CP_fn_cd1 ($code){
		$Cpfncd1 = array(
			'01' => '비씨',
			'02' => '국민',
			'03' => '외환',
			'04' => '삼성',
			'05' => '엘지',
			'07' => 'JCB',
			'08' => '현대',
			'09' => '롯데(구 아멕스)',
			'10' => '신한',
			'11' => '한미',
			'12' => '수협',
			'13' => '한미신세계',
			'14' => '우리',
			'15' => '축협',
			'16' => '제주',
			'17' => '광주',
			'18' => '전북',
			'20' => '롯데',
			'24' => '하나',
			'25' => '해외',
			'26' => '씨티',
			'74' => '조흥비자',
			'75' => '하나비자',
			'77' => '한양',
			'79' => '신세계',
			'80' => '농협',
		);
		
		return $Cpfncd1[$code];
	}

	//신용카드 코드값
	function P_status($code){
		$P_status = array(
			'0021' => '성공',
			'0031' => '실패',
			'0051' => '입금대기중',
		);
		
		return $P_status[$code];
	}

	 function proc_fail_db($noti, $settleLog) {
		$db = $GLOBALS['db'];

		$db->query("update ".GD_ORDER." set cardtno='".$noti['P_TR_NO']."', step2='54', settlelog=concat(ifnull(settlelog,''),'".$settleLog."') where ordno='".$noti['P_OID']."' ");
		$db->query("update ".GD_ORDER_ITEM." set istep='54' where ordno=".$noti['P_OID']." ");

		return true;
	}
?>