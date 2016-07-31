<?
include "../lib.php";
require_once ('./_inc/config.inc.php');

$shople = Core::loader('shople');

$mode = isset($_POST['mode']) ? $_POST['mode'] : '';

$rs = array(
	'result' => false,
	'body' => ''
);

// 리스트 가져오기
if ($mode == 'list' || $mode == 'download') {

	// 파라미터
	$_POST['regdt'] = isset($_POST['regdt']) ? $_POST['regdt'] : array(date('Ymd'),date('Ymd'));
	$_POST['method'] = isset($_POST['method']) ? strtoupper($_POST['method']) : 'GET_CLAIMCANCEL_REQUEST_LIST';
	$_POST['method'] = in_array($_POST['method'], array('GET_CLAIMCANCEL_REQUEST_LIST','GET_CLAIMCANCEL_COMPLETE_LIST','GET_CLAIMRETURN_REQUEST_LIST','GET_CLAIMRETURN_COMPLETE_LIST','GET_CLAIMRETURN_CANCEL_LIST','GET_CLAIMEXCHANGE_REQUEST_LIST','GET_CLAIMEXCHANGE_COMPLETE_LIST','GET_CLAIMEXCHANGE_CANCEL_LIST') ) ? $_POST['method'] : 'GET_CLAIMCANCEL_REQUEST_LIST';

	$method = $_POST['method'];
	$param = array(
		'startTime'=>$_POST['regdt'][0].'0000',		// YYYYMMDDhhmm
		'endTime'=>$_POST['regdt'][1].'2359',		// YYYYMMDDhhmm

		// 페이징 변수
		'page' => isset($_POST['page']) ? $_POST['page'] : 1,
		'page_num' => isset($_POST['page']) ? $_POST['page_num'] : 10,
	);
	$data = array();

	$rs = $shople->request($method,$param,$data);

	if ($rs['result'] === true) {

	}

	if ($mode == 'download') {

		// 엑셀로 조짐.
		header("Content-Type: application/vnd.ms-excel; charset=euc-kr");
		header("Content-Disposition: attachment; filename=shople_claim_".date("YmdHi").".xls");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");

		echo '<table border=1>';
		echo '
		<tr>
			<td>무료반품 여부</td>
			<td>반품 사유코드에 대한 상세내역</td>
			<td>반품 수량</td>
			<td>반품 사유코드</td>
			<td>외부몰 클레임 번호</td>
			<td>클레임 상태</td>
			<td>옵션명</td>
			<td>11번가 주문번호</td>
			<td>주문순번</td>
			<td>상품번호</td>
			<td>클레임 요청 일시</td>
		</tr>
		';

		foreach($rs['body'] as $k => $row) {

			echo '
			<tr>
				<td>'.$row['affliateBndlDlvSeq'].'</td>
				<td>'.$row['clmReqCont'].'</td>
				<td>'.$row['clmReqQty'].'</td>
				<td>'.$row['clmReqRsn'].'</td>
				<td>'.$row['clmReqSeq'].'</td>
				<td>'.$row['clmStat'].'</td>
				<td>'.$row['optName'].'</td>
				<td>'.$row['ordNo'].'</td>
				<td>'.$row['ordPrdSeq'].'</td>
				<td>'.$row['prdNo'].'</td>
				<td>'.$row['reqDt'].'</td>
			</tr>
			';

		}
		echo '</table>';
		exit;
	}

}
// 취소승인
elseif ($mode == 'cancelaccept') {

	$method = 'SET_CLAIMCANCEL';
	$param = array(
		'ordPrdCnSeq'	=> $_POST['ordPrdCnSeq'],// 취소 클레임번호
		'ordNo'			=> $_POST['ordNo'],		// 주문번호
		'ordPrdSeq'		=> $_POST['ordPrdSeq'],	// 주문순번
	);

	$rs = $shople->request($method,$param,'');

	if ($rs['result'] === true) {

	}

}
// 취소거부
elseif ($mode == 'cancelreject') {

	$method = 'SET_CLAIMCANCEL_REJECT';
	$param = array(
		'ordNo'			=> $_POST['ordNo'],
		'ordPrdSeq'		=> $_POST['ordPrdSeq'],		// 주문순번
		'ordPrdCnSeq'	=> $_POST['ordPrdCnSeq'],	// 취소 클레임번호
		'dlvMthdCd'		=> $_POST['dlvMthdCd'],		// 배송방식
		'sendDt'		=> $_POST['sendDt'],		// 보낸일자 YYYYMMDD
		'dlvEtprsCd'	=> $_POST['dlvEtprsCd'],	// 택배사코드
		'invcNo'		=> $_POST['invcNo']			// 송장번호
	);

	$rs = $shople->request($method,$param,'');

	if ($rs['result'] === true) {

	}

}
// 반품승인
elseif ($mode == 'returnaccept') {

	$method = 'SET_CLAIMRETURN';
	$param = array(
		'clmReqSeq'		=> $_POST['clmReqSeq'],	// 클레임번호
		'ordNo'			=> $_POST['ordNo'],		// 주문번호
		'ordPrdSeq'		=> $_POST['ordPrdSeq']	// 주문순번
	);

	$rs = $shople->request($method,$param,'');

	if ($rs['result'] === true) {

	}

}
// 반품거부
elseif ($mode == 'returnreject') {

	$method = 'SET_CLAIMRETURN_REJECT';
	$param = array(
		'ordNo'		=> $_POST['ordNo'],		// 주문번호
		'ordPrdSeq' => $_POST['ordPrdSeq'],	// 주문순번
		'clmReqSeq' => $_POST['clmReqSeq'],	// 클레임번호
		'refsRsnCd' => $_POST['reasonCD'],	// 사유코드 (101 : 반품 상품 미입고 ,102 : 고객 반품신청 철회 대행 ,103 : 반품 불가 상품 ,104 : 기타 )
		'refsRsn'	=> $_POST['reasonCont']	// 사유
	);

	$rs = $shople->request($method,$param,'');

	if ($rs['result'] === true) {

	}

}
// 반품보류
elseif ($mode == 'returnhold') {

	$method = 'SET_CLAIMRETURN_HOLD';
	$param = array(
		'ordNo'			=> $_POST['seq'],			// 주문번호
		'ordPrdSeq'		=> $_POST['ordPrdSeq'],		// 주문순번
		'clmReqSeq'		=> $_POST['clmReqSeq'],		// 클레임번호(반품신청번호)
		'deferRefsRsnCd'=> $_POST['reasonCD'],		// 보류사유코드(101 : 반품 상품 미입고,102 : 반품 배송비 미동봉,103 : 반품 상품 훼손,104 : 구매자 연락 두절,105 : 기타)
		'ordCnDtlsRsn'	=> $_POST['reasonCont']		// 사유
	);

	$rs = $shople->request($method,$param,'');

	if ($rs['result'] === true) {

	}

}
// 반품완료보류
elseif ($mode == 'returnaccepthold') {
// ordNo ordPrdSeq clmReqSeq deferRefsRsnCd ordCnDtlsRsn
	$method = 'SET_CLAIMRETURN_ACCEPTHOLD';
	$param = array(
		'ordNo'			=> $_POST['seq'],			// 주문번호
		'ordPrdSeq'		=> $_POST['ordPrdSeq'],		// 주문순번
		'clmReqSeq'		=> $_POST['clmReqSeq'],		// 클레임번호(반품신청번호)
		'deferRefsRsnCd'=> $_POST['reasonCD'],		// 보류사유코드(101 : 반품 상품 미입고,102 : 반품 배송비 미동봉,103 : 반품 상품 훼손,104 : 구매자 연락 두절,105 : 기타)
		'ordCnDtlsRsn'	=> $_POST['reasonCont']		// 사유
	);

	$rs = $shople->request($method,$param,'');

	if ($rs['result'] === true) {

	}

}
// 교환승인
elseif ($mode == 'exchangeaccept') {

	$method = 'SET_CLAIMEXCHANGE';
	$param = array(
		'clmReqSeq'	=> $_POST['clmReqSeq'],		// 클레임번호
		'ordNo'		=> $_POST['ordNo'],			// 주문번호
		'ordPrdSeq' => $_POST['ordPrdSeq'],		// 주문순번
		'dlvEtprsCd'=> $_POST['dlvEtprsCd'],	// 택배사코드
		'invcNo'	=> $_POST['invcNo']			// 송장번호
	);

	$rs = $shople->request($method,$param,'');

	if ($rs['result'] === true) {

	}

}
// 교환거부
elseif ($mode == 'exchangeareject') {

	$method = 'SET_CLAIMEXCHANGE_REJECT';
	$param = array(
		'ordNo'		=> $_POST['ordNo'],		// 주문번호
		'ordPrdSeq' => $_POST['ordPrdSeq'],	// 주문순번
		'clmReqSeq' => $_POST['clmReqSeq'],	// 클레임번호
		'refsRsnCd' => $_POST['reasonCD'],	// 사유코드	(201 : 교환 상품 미입고 ,202 : 고객 교환신청 철회 대행 ,203 : 교환 불가 상품 ,204 : 기타 )
		'refsRsn'	=> $_POST['reasonCont']	// 사유
	);

	$rs = $shople->request($method,$param,'');

	if ($rs['result'] === true) {

	}

}
elseif ($mode == 'test') {
	usleep( rand(100000,1000000) );
	$rs['result'] = false;
	$rs['body'] = '테스트 동작임';
}

echo $shople->json_encode($rs);
?>
