<?php
if (isset($_GET['crno']) === false)
{
	include dirname(__FILE__).'/../../../lib/library.php';
	include dirname(__FILE__).'/../../../conf/config.pay.php';
	@include_once(dirname(__FILE__).'/../../../lib/cashreceipt.class.php');
	extract($_POST);

	if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
		$_POST = validation::xssCleanArray($_POST, array(
			validation::DEFAULT_KEY	=> 'text'
		));
	}

	if(!is_object($cashreceipt) && class_exists('cashreceipt')) $cashreceipt = new cashreceipt();

	### 금액 데이타 유효성 체크
	$data = $cashreceipt->getCashReceiptCalCulate($ordno);
	if ($data['supply']!=$_POST['sup_price'] || $data['vat']!=$_POST['tax']) msg('금액이 일치하지 않습니다',-1);

	// 발급상태체크(기존시스템고려)
	if ($data['cashreceipt'] != '' && file_exists(dirname(__FILE__).'/../../../../lib/cashreceipt.class.php') === false) {
		msg('현금영수증 발행요청실패!! \\n['.$ordno.'] 주문은 이미 발행되었습니다.');
		exit;
	}

	### 현금영수증신청내역 추가
	if (is_object($cashreceipt))
	{
		// 발급상태체크
		list($crno) = $db->fetch("select crno from gd_cashreceipt where ordno='{$ordno}' and status='ACK' order by crno desc limit 1");
		if ($crno != '') {
			msg('현금영수증 발행요청실패!! \\n['.$ordno.'] 주문은 이미 발행되었습니다.');
			exit;
		}
		//추가
		if($_POST['EP_issue_type']=='01')
			$useopt="0";
		else
			$useopt="1";


		$indata = array();
		$indata['ordno'] = $_POST['ordno'];
		$indata['goodsnm'] = $_POST['goodname'];
		$indata['buyername'] = $_POST['buyername'];
		$indata['useopt'] = $useopt;
		$indata['certno'] = $_POST['EP_auth_value'];
		$indata['amount'] = $_POST['cr_price'];
		$indata['supply'] = $_POST['sup_price'];
		$indata['surtax'] = $_POST['tax'];

		$crno = $cashreceipt->putReceipt($indata);
	}
}
else {
	$ordno = $crdata['ordno'];
	$goodname = $crdata['goodsnm'];
	$cr_price = $crdata['amount'];
	$sup_price = $crdata['supply'];
	$tax = $crdata['surtax'];
	$srvc_price = 0;
	$buyername = $crdata['buyername'];
	$buyeremail = $crdata['buyeremail'];
	$buyertel = $crdata['buyerphone'];
	$reg_num = $crdata['certno'];
	$useopt = $crdata['useopt'];
	$crno = $_GET['crno'];
}

// 현금영수증 발행 페이지
	$shopdir = dirname(__FILE__).'/../../..';
	include($shopdir.'/conf/config.php');
	include($shopdir.'/conf/pg.'.$cfg[settlePg].'.php');
	require_once($shopdir.'/order/card/easypay/inc/easypay_config.php');
    require_once($shopdir.'/order/card/easypay/easypay_client.php');

/* -------------------------------------------------------------------------- */
/* ::: 처리구분 설정                                                          */
/* -------------------------------------------------------------------------- */
$ISSUE    = "issue" ;  // 발행
$CANCL    = "cancel" ; // 취소


$tr_cd            = $_POST["EP_tr_cd"];            // [필수]요청구분
$pay_type         = $_POST["EP_pay_type"];         // [필수]결제수단
$req_type         = $_POST["EP_req_type"];         // [필수]요청타입

if(is_null($tr_cd))
	$tr_cd=$crdata['EP_tr_cd'];
if(is_null($pay_type))
	$pay_type=$crdata['EP_pay_type'];
if(is_null($req_type))
	$req_type=$crdata['EP_req_type'];




/* -------------------------------------------------------------------------- */
/* ::: 현금영수증 발행정보 설정                                               */
/* -------------------------------------------------------------------------- */

$user_id          = $_POST["EP_user_id"];          // [선택]고객 ID
$user_nm          = $_POST["EP_user_nm"];          // [선택]고객명
$issue_type       = $_POST["EP_issue_type"];       // [필수]현금영수증발행용도
$auth_type        = $_POST["EP_auth_type"];        // [필수]인증구분
$auth_value       = $_POST["EP_auth_value"];       // [필수]인증번호
$sub_mall_yn      = $_POST["EP_sub_mall_yn"];      // [필수]하위가맹점사용여부
$sub_mall_buss    = $_POST["EP_sub_mall_buss"];    // [선택]하위가맹점사업자번호
$tot_amt          = $_POST["EP_tot_amt"];          // [필수]총거래금액
$service_amt      = $_POST["EP_service_amt"];      // [필수]봉사료
$vat              = $_POST["EP_vat"];              // [필수]부가세

//--- 이지페이 경로
$easypayHome	= dirname($_SERVER['SCRIPT_FILENAME']);      // 이니페이 홈디렉터리
$logfile		= fopen( $easypayHome . '/log/easypay_receipt_log_'.date('Ymd').'.log', 'a+' );

// 로그 저장
$logInfo	 = '------------------------------------------------------------------------------'.chr(10);
$logInfo	.= 'INFO	['.date('Y-m-d H:i:s').']	START Order log'.chr(10);
foreach ($_POST as $key => $val) {
	$logInfo	.= 'DEBUG	['.date('Y-m-d H:i:s').']	'.$key.'	: '.$val.chr(10);
}
$logInfo	.= 'DEBUG	['.date('Y-m-d H:i:s').']	IP	: '.$_SERVER['REMOTE_ADDR'].chr(10);
$logInfo	.= 'INFO	['.date('Y-m-d H:i:s').']	END Order log'.chr(10);
$logInfo	.= '------------------------------------------------------------------------------'.chr(10).chr(10);
fwrite( $logfile, $logInfo);
fclose( $logfile );



if(is_null($order_no))
	$order_no=$ordno;         // [필수]주문번호
else
	$order_no         = $_POST["EP_order_no"];

if(is_null($issue_type)) {
	if($useopt=="0")
		$issue_type="01";
	else
		$issue_type="02";
}



if(is_null($auth_value)) {
	$auth_value=$reg_num;
}

if(is_null($auth_type)) {
	$auth_type="02";
	if(strlen($auth_value)==13) {
		$auth_type="02";//주민번호
	}
	else if(substr($auth_value, 0, 2)=="01" && (strlen($auth_value)==10 || strlen($auth_value)==11)) {
		$auth_type="03";//휴대전화
	}
	else{
		$auth_type="04";//사업자등록번호
	}

}


if(is_null($sub_mall_yn)) {
	$sub_mall_yn="0";
}

if(is_null($tot_amt)) {
	$tot_amt=$cr_price;
}

if(is_null($service_amt)) {
	$service_amt="0";
}

if(is_null($vat)) {
	$vat=$tax;
}
/* -------------------------------------------------------------------------- */
/* ::: 현금영수증 취소정보 설정                                               */
/* -------------------------------------------------------------------------- */
$mgr_txtype       = $_POST["mgr_txtype"];          // [필수]거래구분
$org_cno          = $_POST["org_cno"];             // [필수]원거래고유번호
$req_id           = $_SESSION['sess']['m_id'];//$_POST["req_id"];              // [필수]가맹점 관리자 로그인 아이디
$mgr_msg          = $_POST["mgr_msg"];             // [선택]변경 사유

/* -------------------------------------------------------------------------- */
/* ::: IP 정보 설정                                                           */
/* -------------------------------------------------------------------------- */
$client_ip         = $_SERVER['REMOTE_ADDR'];      // [필수]결제고객 IP

/* -------------------------------------------------------------------------- */
/* ::: 결제 결과                                                              */
/* -------------------------------------------------------------------------- */
$res_cd     = "";
$res_msg    = "";

/* -------------------------------------------------------------------------- */
/* ::: EasyPayClient 인스턴스 생성 [변경불가 !!].                             */
/* -------------------------------------------------------------------------- */
$easyPay = new EasyPay_Client;         // 전문처리용 Class (library에서 정의됨)
$easyPay->clearup_msg();

$easyPay->set_home_dir($g_home_dir);
$easyPay->set_gw_url($g_gw_url);
$easyPay->set_gw_port($g_gw_port);
$easyPay->set_log_dir($g_log_dir);
$easyPay->set_log_level($g_log_level);
$easyPay->set_cert_file($g_cert_file);

if( $ISSUE == $req_type )
{
	/* ---------------------------------------------------------------------- */
    /* ::: 인증요청 전문 설정                                                 */
    /* ---------------------------------------------------------------------- */
    // 결제 주문 전문
    $cash_data = $easyPay->set_easypay_item("cash_data");
    $easyPay->set_easypay_deli_us( $cash_data, "order_no"      , $order_no     );
    $easyPay->set_easypay_deli_us( $cash_data, "user_id"       , $user_id      );
    $easyPay->set_easypay_deli_us( $cash_data, "user_nm"       , $user_nm      );
    $easyPay->set_easypay_deli_us( $cash_data, "issue_type"    , $issue_type   );
    $easyPay->set_easypay_deli_us( $cash_data, "auth_type"     , $auth_type    );
    $easyPay->set_easypay_deli_us( $cash_data, "auth_value"    , $auth_value   );
    $easyPay->set_easypay_deli_us( $cash_data, "sub_mall_yn"   , $sub_mall_yn  );
    if( $sub_mall_yn =="1" )
        $easyPay->set_easypay_deli_us( $cash_data, "sub_mall_buss"   , $sub_mall_buss   );

    $easyPay->set_easypay_deli_us( $cash_data, "tot_amt"      , $tot_amt      );
    $easyPay->set_easypay_deli_us( $cash_data, "service_amt"  , $service_amt  );
    $easyPay->set_easypay_deli_us( $cash_data, "vat"          , $vat          );
}
else if( $CANCL == $req_type )
{
    $mgr_data = $easyPay->set_easypay_item("mgr_data");
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_txtype"    , $mgr_txtype   );
    $easyPay->set_easypay_deli_us( $mgr_data, "org_cno"       , $org_cno      );
    $easyPay->set_easypay_deli_us( $mgr_data, "client_ip"     , $client_ip    );
    $easyPay->set_easypay_deli_us( $mgr_data, "req_id"        , $req_id       );
    $easyPay->set_easypay_deli_us( $cash_data, "mgr_msg"      , $mgr_msg      );
}
/* -------------------------------------------------------------------------- */
/* ::: 실행                                                                   */
/* -------------------------------------------------------------------------- */
$opt = "option value";
$easyPay->easypay_exec($g_mall_id, $tr_cd, $order_no, $client_ip, $opt);
$res_cd  = $easyPay->_easypay_resdata["res_cd"];    // 응답코드
$res_msg = $easyPay->_easypay_resdata["res_msg"];   // 응답메시지

/* -------------------------------------------------------------------------- */
/* ::: 결과 처리                                                              */
/* -------------------------------------------------------------------------- */
$r_cno             = $easyPay->_easypay_resdata[ "cno"             ];    // PG거래번호
$r_amount          = $easyPay->_easypay_resdata[ "amount"          ];    //총 결제금액
$r_auth_no         = $easyPay->_easypay_resdata[ "auth_no"         ];    //승인번호
$r_tran_date       = $easyPay->_easypay_resdata[ "tran_date"       ];    //승인일시
$r_pnt_auth_no     = $easyPay->_easypay_resdata[ "pnt_auth_no"     ];    //포인트승인번호
$r_pnt_tran_date   = $easyPay->_easypay_resdata[ "pnt_tran_date"   ];    //포인트승인일시
$r_cpon_auth_no    = $easyPay->_easypay_resdata[ "cpon_auth_no"    ];    //쿠폰승인번호
$r_cpon_tran_date  = $easyPay->_easypay_resdata[ "cpon_tran_date"  ];    //쿠폰승인일시
$r_card_no         = $easyPay->_easypay_resdata[ "card_no"         ];    //카드번호
$r_issuer_cd       = $easyPay->_easypay_resdata[ "issuer_cd"       ];    //발급사코드
$r_issuer_nm       = $easyPay->_easypay_resdata[ "issuer_nm"       ];    //발급사명
$r_acquirer_cd     = $easyPay->_easypay_resdata[ "acquirer_cd"     ];    //매입사코드
$r_acquirer_nm     = $easyPay->_easypay_resdata[ "acquirer_nm"     ];    //매입사명
$r_install_period  = $easyPay->_easypay_resdata[ "install_period"  ];    //할부개월
$r_noint           = $easyPay->_easypay_resdata[ "noint"           ];    //무이자여부
$r_bank_cd         = $easyPay->_easypay_resdata[ "bank_cd"         ];    //은행코드
$r_bank_nm         = $easyPay->_easypay_resdata[ "bank_nm"         ];    //은행명
$r_account_no      = $easyPay->_easypay_resdata[ "account_no"      ];    //계좌번호
$r_deposit_nm      = $easyPay->_easypay_resdata[ "deposit_nm"      ];    //입금자명
$r_expire_date     = $easyPay->_easypay_resdata[ "expire_date"     ];    //계좌사용만료일
$r_cash_res_cd     = $easyPay->_easypay_resdata[ "cash_res_cd"     ];    //현금영수증 결과코드
$r_cash_res_msg    = $easyPay->_easypay_resdata[ "cash_res_msg"    ];    //현금영수증 결과메세지
$r_cash_auth_no    = $easyPay->_easypay_resdata[ "cash_auth_no"    ];    //현금영수증 승인번호
$r_cash_tran_date  = $easyPay->_easypay_resdata[ "cash_tran_date"  ];    //현금영수증 승인일시
$r_auth_id         = $easyPay->_easypay_resdata[ "auth_id"         ];    //PhoneID
$r_billid          = $easyPay->_easypay_resdata[ "billid"          ];    //인증번호
$r_mobile_no       = $easyPay->_easypay_resdata[ "mobile_no"       ];    //휴대폰번호
$r_ars_no          = $easyPay->_easypay_resdata[ "ars_no"          ];    //전화번호
$r_cp_cd           = $easyPay->_easypay_resdata[ "cp_cd"           ];    //포인트사/쿠폰사
$r_used_pnt        = $easyPay->_easypay_resdata[ "used_pnt"        ];    //사용포인트
$r_remain_pnt      = $easyPay->_easypay_resdata[ "remain_pnt"      ];    //잔여한도
$r_pay_pnt         = $easyPay->_easypay_resdata[ "pay_pnt"         ];    //할인/발생포인트
$r_accrue_pnt      = $easyPay->_easypay_resdata[ "accrue_pnt"      ];    //누적포인트
$r_remain_cpon     = $easyPay->_easypay_resdata[ "remain_cpon"     ];    //쿠폰잔액
$r_used_cpon       = $easyPay->_easypay_resdata[ "used_cpon"       ];    //쿠폰 사용금액
$r_mall_nm         = $easyPay->_easypay_resdata[ "mall_nm"         ];    //제휴사명칭
$r_escrow_yn       = $easyPay->_easypay_resdata[ "escrow_yn"       ];    //에스크로 사용유무
$r_complex_yn      = $easyPay->_easypay_resdata[ "complex_yn"      ];    //복합결제 유무
$r_canc_acq_date   = $easyPay->_easypay_resdata[ "canc_acq_date"   ];    //매입취소일시
$r_canc_date       = $easyPay->_easypay_resdata[ "canc_date"       ];    //취소일시
$r_refund_date     = $easyPay->_easypay_resdata[ "refund_date"     ];    //환불예정일시


if( $ISSUE == $req_type ){	// 발행
	if($res_cd == '0000'){	// 성공
		$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= '현금영수증 발급 성공'."\n";
		$settlelog .= '결과코드 : '.$res_cd."\n";
		$settlelog .= '결과메세지 : '.$res_msg."\n";
		$settlelog .= '승인번호 : '.$r_auth_no."\n";
		$settlelog .= '승인일시 : '.$r_tran_date."\n";
		$settlelog .= 'PG거래번호 : '.$r_cno."\n";
		$settlelog .= '-----------------------------------'."\n";


		if (empty($crno) === true)
		{
			$db->query("update gd_order set cashreceipt='$r_cno',settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
		}
		else {
			# 현금영수증신청내역 수정
			$db->query("update gd_cashreceipt set pg='easypay',cashreceipt='$r_cno',receiptnumber='$r_cash_auth_no',tid='$r_cno',moddt=now(),status='ACK',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
			$db->query("update gd_order set cashreceipt='$r_cno' where ordno='{$ordno}'");

		}

		if (isset($_GET['crno']) === false)
		{
			msg('현금영수증이 정상발급되었습니다');
			echo '<script>parent.location.reload();</script>';
		}



	}else{	// 실패
				$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
				$settlelog .= '-----------------------------------'."\n";
				$settlelog .= '현금영수증 발급 실패'."\n";
				$settlelog .= '결과코드 : '.$res_cd."\n";
				$settlelog .= '결과내용 : '.$res_msg."\n";
				$settlelog .= '-----------------------------------'."\n";

				if (empty($crno) === true)
				{
					$db->query("update gd_order set settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
				}
				else {
					# 현금영수증신청내역 수정
					$db->query("update gd_cashreceipt set pg='easypay',errmsg='{$res_cd}:{$res_msg}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
				}

				if (isset($_GET['crno']) === false)
				{
					msg($res_msg);
					exit;
				}
	}
}
exit;
?>