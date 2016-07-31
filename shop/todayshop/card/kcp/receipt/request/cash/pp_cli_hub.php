<?
/* ============================================================================== */
/* =   PAGE : 등록/변경 처리 PAGE                                               = */
/* = -------------------------------------------------------------------------- = */
/* =   Copyright (c)  2007   KCP Inc.   All Rights Reserverd.                   = */
/* ============================================================================== */


/* ============================================================================== */
/* = 라이브러리 및 사이트 정보 include                                          = */
/* = -------------------------------------------------------------------------- = */
if (isset($_GET['crno']) === false)
{
	include dirname(__FILE__).'/../../../../../../lib/library.php';
}
include dirname(__FILE__).'/../../../../../../conf/config.php';
include dirname(__FILE__).'/../../../../../../conf/pg.kcp.php';
require dirname(__FILE__).'/pp_cli_hub_lib.php';
include dirname(__FILE__).'/../configure/site.php';
/* ============================================================================== */

/* ============================================================================== */
/* =   01. 요청 정보 설정                                                       = */
/* = -------------------------------------------------------------------------- = */
if ($_POST['req_tx'] == 'pay' && isset($_GET['crno']) === false)
{
	$req_tx = $_POST['req_tx']; // 요청 종류
	/* = -------------------------------------------------------------------------- = */
	$ordr_idxx = $_POST['ordr_idxx']; // 주문 번호
	$good_name = $_POST['good_name']; // 상품 정보
	$buyr_name = $_POST['buyr_name']; // 주문자 이름
	$buyr_mail = $_POST['buyr_mail']; // 주문자 E-Mail
	$buyr_tel1 = $_POST['buyr_tel1']; // 주문자 전화번호
	$comment = ''; // 비고
	/* = -------------------------------------------------------------------------- = */
	$corp_type = '0'; // 사업장 구분
	//$corp_tax_type = ''; // 과세/면세 구분
	//$corp_tax_no = ''; // 발행 사업자 번호
	//$corp_nm = ''; // 상호
	//$corp_owner_nm = ''; // 대표자명
	//$corp_addr = ''; // 사업장 주소
	//$corp_telno = ''; // 사업장 대표 연락처
	/* = -------------------------------------------------------------------------- = */
	$trad_time = date('YmdHis'); // 원거래 시각
	$tr_code = $_POST['tr_code']; // 발행용도
	$id_info = $_POST['id_info']; // 신분확인 ID
	$amt_tot = $_POST['amt_tot']; // 거래금액 총 합
	$amt_sup = $_POST['amt_sup']; // 공급가액
	$amt_svc = '0'; // 봉사료
	$amt_tax = $_POST['amt_tax']; // 부가가치세
	/* = -------------------------------------------------------------------------- = */
	$ordno = $ordr_idxx;

	$data = $db->fetch("select * from gd_order where ordno='{$ordno}'",1);

	// 발급상태체크(기존시스템고려)
	if ($data['cashreceipt'] != '' && file_exists(dirname(__FILE__).'/../../../../../../lib/cashreceipt.class.php') === false) {
		msg('현금영수증 발행요청실패!! \\n['.$ordno.'] 주문은 이미 발행되었습니다.');
		exit;
	}

	### 현금영수증신청내역 추가
	@include dirname(__FILE__).'/../../../../../../lib/cashreceipt.class.php';
	if (class_exists('cashreceipt'))
	{
		// 발급상태체크
		list($crno) = $db->fetch("select crno from gd_cashreceipt where ordno='{$ordno}' and status='ACK' order by crno desc limit 1");
		if ($crno != '') {
			msg('현금영수증 발행요청실패!! \\n['.$ordno.'] 주문은 이미 발행되었습니다.');
			exit;
		}

		$indata = array();
		$indata['ordno'] = $ordno;
		$indata['goodsnm'] = $good_name;
		$indata['buyername'] = $buyr_name;
		$indata['buyeremail'] = $buyr_mail;
		$indata['buyerphone'] = $buyr_tel1;
		$indata['useopt'] = $tr_code;
		$indata['certno'] = $id_info;
		$indata['amount'] = $amt_tot;
		$indata['supply'] = $amt_sup;
		$indata['surtax'] = $amt_tax;
		$indata['regdt'] = $trad_time;

		$cashreceipt = new cashreceipt();
		$crno = $cashreceipt->putReceipt($indata);
	}
}
else if ($crdata['req_tx'] == 'pay')
{
	$req_tx = $crdata['req_tx']; // 요청 종류
	/* = -------------------------------------------------------------------------- = */
	$ordr_idxx = $crdata['ordno']; // 주문 번호
	$good_name = $crdata['goodsnm']; // 상품 정보
	$buyr_name = $crdata['buyername']; // 주문자 이름
	$buyr_mail = $crdata['buyeremail']; // 주문자 E-Mail
	$buyr_tel1 = $crdata['buyerphone']; // 주문자 전화번호
	$comment = ''; // 비고
	/* = -------------------------------------------------------------------------- = */
	$corp_type = '0'; // 사업장 구분
	//$corp_tax_type = ''; // 과세/면세 구분
	//$corp_tax_no = ''; // 발행 사업자 번호
	//$corp_nm = ''; // 상호
	//$corp_owner_nm = ''; // 대표자명
	//$corp_addr = ''; // 사업장 주소
	//$corp_telno = ''; // 사업장 대표 연락처
	/* = -------------------------------------------------------------------------- = */
	$trad_time = date('YmdHis'); // 원거래 시각
	$tr_code = $crdata['useopt']; // 발행용도
	$id_info = $crdata['certno']; // 신분확인 ID
	$amt_tot = $crdata['amount']; // 거래금액 총 합
	$amt_sup = $crdata['supply']; // 공급가액
	$amt_svc = '0'; // 봉사료
	$amt_tax = $crdata['surtax']; // 부가가치세
	/* = -------------------------------------------------------------------------- = */
	$ordno = $ordr_idxx;
	$crno = $_GET['crno'];
}
else if ($crdata['req_tx'] == 'mod')
{
	$req_tx = $crdata['req_tx']; // 요청 종류
	/* = -------------------------------------------------------------------------- = */
	$mod_type = 'STSC'; // 변경 타입
	$mod_gubn = 'MG01'; // 변경 요청 거래번호 구분
	$mod_value = $crdata['tid']; // 변경 요청 거래번호
	preg_match("/승인번호 : {$crdata['receiptnumber']}.*\n원거래시간 : ([^(\n)]*)\n/s", $crdata['receiptlog'], $log);
	$trad_time = $log[1]; // 원거래 시각
	//$mod_mny = ''; // 변경 요청 금액
	//$rem_mny = ''; // 변경처리 이전 금액
	/* = -------------------------------------------------------------------------- = */
	$ordno = $crdata['ordno'];
}
$cust_ip = getenv( 'REMOTE_ADDR' ); // 요청 IP
/* ============================================================================== */


/* ============================================================================== */
/* =   02. 인스턴스 생성 및 초기화                                              = */
/* = -------------------------------------------------------------------------- = */
$c_PayPlus  = new C_PAYPLUS_CLI;
$c_PayPlus->mf_clear();
/* ============================================================================== */


/* ============================================================================== */
/* =   03. 처리 요청 정보 설정, 실행                                            = */
/* = -------------------------------------------------------------------------- = */

/* = -------------------------------------------------------------------------- = */
/* =   03-1. 승인 요청                                                          = */
/* = -------------------------------------------------------------------------- = */
// 업체 환경 정보
if ( $req_tx == 'pay' )
{
	$tx_cd = '07010000'; // 현금영수증 등록 요청

	// 현금영수증 정보
	$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'user_type',      $g_conf_user_type );
	$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'trad_time',      $trad_time        );
	$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'tr_code',        $tr_code          );
	$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'id_info',        $id_info          );
	$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'amt_tot',        $amt_tot          );
	$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'amt_sup',        $amt_sup          );
	$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'amt_svc',        $amt_svc          );
	$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'amt_tax',        $amt_tax          );
	$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'pay_type',       'PAXX'            ); // 선 결제 서비스 구분(PABK - 계좌이체, PAVC - 가상계좌, PAXX - 기타)
	//$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'pay_trade_no',   $pay_trade_no ); // 결제 거래번호(PABK, PAVC일 경우 필수)
	//$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'pay_tx_id',      $pay_tx_id    ); // 가상계좌 입금통보 TX_ID(PAVC일 경우 필수)

	// 주문 정보
	$c_PayPlus->mf_set_ordr_data( 'ordr_idxx',  $ordr_idxx );
	$c_PayPlus->mf_set_ordr_data( 'good_name',  preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "",  $good_name) );
	$c_PayPlus->mf_set_ordr_data( 'buyr_name',  $buyr_name );
	$c_PayPlus->mf_set_ordr_data( 'buyr_tel1',  $buyr_tel1 );
	$c_PayPlus->mf_set_ordr_data( 'buyr_mail',  $buyr_mail );
	$c_PayPlus->mf_set_ordr_data( 'comment',    $comment   );

	// 가맹점 정보
	$corp_data_set .= $c_PayPlus->mf_set_data_us( 'corp_type',       $corp_type     );

	if ( $corp_type == '1' ) // 입점몰인 경우 판매상점 DATA 전문 생성
	{
		$corp_data_set .= $c_PayPlus->mf_set_data_us( 'corp_tax_type',   $corp_tax_type );
		$corp_data_set .= $c_PayPlus->mf_set_data_us( 'corp_tax_no',     $corp_tax_no   );
		$corp_data_set .= $c_PayPlus->mf_set_data_us( 'corp_sel_tax_no', $corp_tax_no   );
		$corp_data_set .= $c_PayPlus->mf_set_data_us( 'corp_nm',         $corp_nm       );
		$corp_data_set .= $c_PayPlus->mf_set_data_us( 'corp_owner_nm',   $corp_owner_nm );
		$corp_data_set .= $c_PayPlus->mf_set_data_us( 'corp_addr',       $corp_addr     );
		$corp_data_set .= $c_PayPlus->mf_set_data_us( 'corp_telno',      $corp_telno    );
	}

	$c_PayPlus->mf_set_ordr_data( 'rcpt_data', $rcpt_data_set );
	$c_PayPlus->mf_set_ordr_data( 'corp_data', $corp_data_set );
}

/* = -------------------------------------------------------------------------- = */
/* =   03-2. 취소 요청                                                          = */
/* = -------------------------------------------------------------------------- = */
else if ( $req_tx == 'mod' )
{
	if ( $mod_type == 'STSQ' )
	{
		$tx_cd = '07030000'; // 조회 요청
	}
	else
	{
		$tx_cd = '07020000'; // 취소 요청
	}

	$c_PayPlus->mf_set_modx_data( 'mod_type',   $mod_type   );      // 원거래 변경 요청 종류
	$c_PayPlus->mf_set_modx_data( 'mod_value',  $mod_value  );
	$c_PayPlus->mf_set_modx_data( 'mod_gubn',   $mod_gubn   );
	$c_PayPlus->mf_set_modx_data( 'trad_time',  $trad_time  );

	if ( $mod_type == 'STPC' ) // 부분취소
	{
		$c_PayPlus->mf_set_modx_data( 'mod_mny',  $mod_mny  );
		$c_PayPlus->mf_set_modx_data( 'rem_mny',  $rem_mny  );
	}
}
/* ============================================================================== */


/* ============================================================================== */
/* =   03-3. 실행                                                               = */
/* ------------------------------------------------------------------------------ */
if ( strlen($tx_cd) > 0 )
{
	$c_PayPlus->mf_do_tx( '',                $g_conf_home_dir, $g_conf_site_id,
	                      '',                $tx_cd,           '',
	                      $g_conf_pa_url,    $g_conf_pa_port,  'payplus_cli_slib',
	                      $ordr_idxx,        $cust_ip,         $g_conf_log_level,
	                      '',                $g_conf_tx_mode );
}
else
{
	$c_PayPlus->m_res_cd  = '9562';
	$c_PayPlus->m_res_msg = '연동 오류';
}
$res_cd  = $c_PayPlus->m_res_cd;                      // 결과 코드
$res_msg = $c_PayPlus->m_res_msg;                     // 결과 메시지
/* ============================================================================== */


/* ============================================================================== */
/* =   04. 승인 결과 처리                                                       = */
/* = -------------------------------------------------------------------------- = */
if ( $req_tx == 'pay' )
{
	if ( $res_cd == '0000' )
	{
		$cash_no    = $c_PayPlus->mf_get_res_data( 'cash_no'    );       // 현금영수증 거래번호
		$receipt_no = $c_PayPlus->mf_get_res_data( 'receipt_no' );       // 현금영수증 승인번호
		$app_time   = $c_PayPlus->mf_get_res_data( 'app_time'   );       // 승인시간(YYYYMMDDhhmmss)
		$reg_stat   = $c_PayPlus->mf_get_res_data( 'reg_stat'   );       // 등록 상태 코드
		$reg_desc   = $c_PayPlus->mf_get_res_data( 'reg_desc'   );       // 등록 상태 설명

		/* = -------------------------------------------------------------------------- = */
		/* =   04-1. 승인 결과를 업체 자체적으로 DB 처리 작업하시는 부분입니다.         = */
		/* = -------------------------------------------------------------------------- = */
		/* =         승인 결과를 DB 작업 하는 과정에서 정상적으로 승인된 건에 대해      = */
		/* =         DB 작업을 실패하여 DB update 가 완료되지 않은 경우, 자동으로       = */
		/* =         승인 취소 요청을 하는 프로세스가 구성되어 있습니다.                = */
		/* =         DB 작업이 실패 한 경우, bSucc 라는 변수(String)의 값을 'false'     = */
		/* =         로 세팅해 주시기 바랍니다. (DB 작업 성공의 경우에는 'false' 이외의 = */
		/* =         값을 세팅하시면 됩니다.)                                           = */
		/* = -------------------------------------------------------------------------- = */
		$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= '현금영수증 발급 성공'."\n";
		$settlelog .= '결과코드 : '.$res_cd."\n";
		$settlelog .= '결과내용 : '.$res_msg."\n";
		$settlelog .= '승인번호 : '.$receipt_no."\n";
		$settlelog .= '거래번호 : '.$cash_no."\n";
		$settlelog .= '승인시간 : '.$app_time."\n";
		$settlelog .= '등록상태코드 : '.$reg_stat."\n";
		$settlelog .= '등록상태설명 : '.$reg_desc."\n";
		$settlelog .= '원거래시간 : '.$trad_time."\n";
		$settlelog .= '-----------------------------------'."\n";
		echo nl2br($settlelog);

		if (empty($crno) === true)
		{
			$db->query("update gd_order set cashreceipt='{$cash_no}',settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
		}
		else {
			# 현금영수증신청내역 수정
			$db->query("update gd_cashreceipt set pg='kcp',cashreceipt='{$cash_no}',receiptnumber='{$receipt_no}',tid='{$cash_no}',moddt=now(),status='ACK',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
			$db->query("update gd_order set cashreceipt='{$cash_no}' where ordno='{$ordno}'");
		}

		$bSucc = ''; // DB 작업 실패일 경우 'false' 로 세팅

		/* = -------------------------------------------------------------------------- = */
		/* =   04-2. DB 작업 실패일 경우 자동 승인 취소                                 = */
		/* = -------------------------------------------------------------------------- = */
		if ( $bSucc == 'false' )
		{
			$c_PayPlus->mf_clear();

			$tx_cd = '07020000'; // 취소 요청

			$c_PayPlus->mf_set_modx_data( 'mod_type',  'STSC'     );                    // 원거래 변경 요청 종류
			$c_PayPlus->mf_set_modx_data( 'mod_value', $cash_no   );
			$c_PayPlus->mf_set_modx_data( 'mod_gubn',  'MG01'     );
			$c_PayPlus->mf_set_modx_data( 'trad_time', $trad_time );

			$c_PayPlus->mf_do_tx( '',                $g_conf_home_dir, $g_conf_site_id,
			                      '',                $tx_cd,           '',
			                      $g_conf_pa_url,    $g_conf_pa_port,  'payplus_cli_slib',
			                      $ordr_idxx,        $cust_ip,         $g_conf_log_level,
			                      '',                $g_conf_tx_mode );

			$res_cd  = $c_PayPlus->m_res_cd;
			$res_msg = $c_PayPlus->m_res_msg;
		}

		$c_PayPlus->mf_clear(); // 인스턴스 CleanUp
		if (isset($_GET['crno']) === false)
		{
			msg('현금영수증이 정상발급되었습니다');
			echo '<script>parent.location.reload();</script>';
		}
		else {
			echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
		}
	}
	else {
		/* = -------------------------------------------------------------------------- = */
		/* =   04-3. 등록 실패를 업체 자체적으로 DB 처리 작업하시는 부분입니다.         = */
		/* = -------------------------------------------------------------------------- = */
		$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= '현금영수증 발급 실패'."\n";
		$settlelog .= '결과코드 : '.$res_cd."\n";
		$settlelog .= '결과내용 : '.$res_msg."\n";
		$settlelog .= '-----------------------------------'."\n";
		echo nl2br($settlelog);

		if (empty($crno) === true)
		{
			$db->query("update gd_order set settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
		}
		else {
			# 현금영수증신청내역 수정
			$db->query("update gd_cashreceipt set pg='kcp',errmsg='{$res_cd}:{$res_msg}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
		}

		$c_PayPlus->mf_clear(); // 인스턴스 CleanUp
		if (isset($_GET['crno']) === false)
		{
			msg($res_msg);
			exit;
		}
		else {
			echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
		}
	}
}
/* ============================================================================== */


/* ============================================================================== */
/* =   05. 변경 결과 처리                                                       = */
/* = -------------------------------------------------------------------------- = */
else if ( $req_tx == 'mod' )
{
	if ( $res_cd == '0000' )
	{
		$cash_no    = $c_PayPlus->mf_get_res_data( 'cash_no'    );       // 현금영수증 거래번호
		$receipt_no = $c_PayPlus->mf_get_res_data( 'receipt_no' );       // 현금영수증 승인번호
		$app_time   = $c_PayPlus->mf_get_res_data( 'app_time'   );       // 승인시간(YYYYMMDDhhmmss)
		$reg_stat   = $c_PayPlus->mf_get_res_data( 'reg_stat'   );       // 등록 상태 코드
		$reg_desc   = $c_PayPlus->mf_get_res_data( 'reg_desc'   );       // 등록 상태 설명

		$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= '현금영수증 취소 성공'."\n";
		$settlelog .= '결과코드 : '.$res_cd."\n";
		$settlelog .= '결과내용 : '.$res_msg."\n";
		$settlelog .= '승인번호 : '.$receipt_no."\n";
		$settlelog .= '거래번호 : '.$cash_no."\n";
		$settlelog .= '승인시간 : '.$app_time."\n";
		$settlelog .= '등록상태코드 : '.$reg_stat."\n";
		$settlelog .= '등록상태설명 : '.$reg_desc."\n";
		$settlelog .= '-----------------------------------'."\n";
		echo nl2br($settlelog);

		$db->query("update gd_cashreceipt set moddt=now(),status='CCR',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'{$settlelog}') where crno='{$_GET['crno']}'");
		$c_PayPlus->mf_clear(); // 인스턴스 CleanUp
		echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
	}
	else {
		/* = -------------------------------------------------------------------------- = */
		/* =   05-1. 변경 실패를 업체 자체적으로 DB 처리 작업하시는 부분입니다.         = */
		/* = -------------------------------------------------------------------------- = */
		$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= '현금영수증 취소 실패'."\n";
		$settlelog .= '결과코드 : '.$res_cd."\n";
		$settlelog .= '결과내용 : '.$res_msg."\n";
		$settlelog .= '-----------------------------------'."\n";
		echo nl2br($settlelog);

		$db->query("update gd_cashreceipt set errmsg='{$res_cd}:{$res_msg}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$_GET['crno']}'");
		$c_PayPlus->mf_clear(); // 인스턴스 CleanUp
		echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
	}
}
/* ============================================================================== */

?>