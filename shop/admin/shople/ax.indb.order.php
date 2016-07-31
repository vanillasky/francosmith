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
	$_POST['method'] = isset($_POST['method']) ? strtoupper($_POST['method']) : 'GET_ORDER_CONFIRM_LIST';
	$_POST['method'] = in_array($_POST['method'], array( 'GET_ORDER_CONFIRM_LIST','GET_ORDER_DELIVERY_LIST','GET_ORDER_DELIVERING_LIST','GET_ORDER_COMPLETE_LIST' ) ) ? $_POST['method'] : 'GET_ORDER_CONFIRM_LIST';

	$method = $_POST['method'];
	$param = array(
		'startTime'=>$_POST['regdt'][0].'0000',		// YYYYMMDDhhmm
		'endTime'=>$_POST['regdt'][1].'2359',		// YYYYMMDDhhmm

		// 페이징 변수
		'page' => isset($_POST['page']) ? $_POST['page'] : 1,
		'page_num' => isset($_POST['page']) ? $_POST['page_num'] : 10,
	);





	$rs = $shople->request($method,$param,$data);

	if ($rs['result'] === true) {

	}

	if ($mode == 'download') {

		// 엑셀로 조짐.
		header("Content-Type: application/vnd.ms-excel; charset=euc-kr");
		header("Content-Disposition: attachment; filename=shople_order_".date("YmdHi").".xls");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");

		echo '<table border=1>';
		echo '
		<tr>
			<td>번호</td>
			<td>주문일시</td>
			<td>결제완료일시</td>
			<td>배송방법</td>
			<td>택배사코드</td>
			<td>송장/등기번호</td>
			<td>배송번호</td>
			<td>주문번호</td>
			<td>상품번호</td>
			<td>상품명</td>
			<td>옵션/추가구성</td>
			<td>판매자상품코드</td>
			<td>판매단가</td>
			<td>옵션가</td>
			<td>수량</td>
			<td>결제금액</td>
			<td>주문총액</td>
			<td>배송비구분</td>
			<td>배송비</td>
			<td>구매자</td>
			<td>구매자ID</td>
			<td>수취인</td>
			<td>전화번호</td>
			<td>핸드폰</td>
			<td>우편번호</td>
			<td>배송지주소</td>
			<td>배송시요구사항</td>
			<td>주문상세번호</td>
		</tr>
		';

		if ( is_array($rs['body']) ) { foreach($rs['body'] as $k => $row) {
			echo '
			<tr>
				<td>'.++$k.'</td>
				<td>'.$row['ordDt'].'</td>
				<td>'.$row['ordStlEndDt'].'</td>
				<td></td>
				<td></td>
				<td></td>
				<td>'.$row['dlvNo'].'</td>
				<td>'.$row['ordNo'].'</td>
				<td>'.$row['prdNo'].'</td>
				<td>'.$row['prdNm'].'</td>
				<td>'.$row['slctPrdOptNm'].'</td>
				<td>'.$row['sellerPrdCd'].'</td>
				<td>'.$row['selPrc'].'</td>
				<td>'.$row['ordOptWonStl'].'</td>
				<td>'.$row['ordQty'].'</td>
				<td>'.$row['ordPayAmt'].'</td>
				<td>'.$row['ordAmt'].'</td>
				<td>'.$row['dlvCstType'].'</td>
				<td>'.$row['dlvCst'].'</td>
				<td>'.$row['ordNm'].'</td>
				<td>'.$row['memID'].'</td>
				<td>'.$row['rcvrNm'].'</td>
				<td>'.$row['rcvrTlphn'].'</td>
				<td>'.$row['rcvrPrtblNo'].'</td>
				<td>'.$row['rcvrMailNo'].'</td>
				<td>'.$row['rcvrBaseAddr'].' '.$row['rcvrDtlsAddr'].'</td>
				<td>'.$row['ordDlvReqCont'].'</td>
				<td>'.$row['ordPrdSeq'].'</td>
			</tr>
			';

		}}	// if, foreach

		echo '</table>';
		exit;
	}

}
// 판매 거부 처리
elseif ($mode == 'reject') {

	$method = 'SET_ORDER_REJECT';
	$param = array(
		'ordNo' => $_POST['ordNo'],
		'ordPrdSeq' => $_POST['ordPrdSeq'],
		'ordCnRsnCd' => $_POST['ordCnRsnCd'],
		'ordCnDtlsRsn' => $_POST['ordCnDtlsRsn']
	);

	$rs = $shople->request($method,$param,'');

	if ($rs['result'] === true) {

	}

}
// 발송 처리
elseif ($mode == 'delivery') {

	$method = 'SET_ORDER_DELIVERY';
	$param = array(
		'sendDt' => date('YmdHi'),
		'dlvMthdCd' => '01',
		'dlvEtprsCd' => $shople->cfg['shople']['dlv_company'],
		'invcNo' => $_POST['invcNo'],
		'dlvNo' => $_POST['seq']
	);

	$rs = $shople->request($method,$param,'');

	if ($rs['result'] === true) {

	}

}
// 발주확인 처리
elseif ($mode == 'confirm') {



	$method = 'SET_ORDER_CONFIRM';
	$param = array(
		'ordNo' => $_POST['ordNo'],
		'ordPrdSeq' => $_POST['ordPrdSeq'],
		'addPrdYn' => $_POST['addPrdYn'],
		'addPrdNo' => $_POST['addPrdNo'],
		'dlvNo' => $_POST['dlvNo']
	);

	$rs = $shople->request($method,$param,'');

	if ($rs['result'] === true) {

	}

}
elseif ($mode == 'test') {
	sleep(1);
	$rs['result'] = true;
	$rs['body'] = '테스트 동작임';

}
elseif ($mode == 'excel') {
	// ajax 아님.

	$file = $_FILES['excel'];

	if ($file['size'] > 0) {

		$excel = file($file['tmp_name']);

		for ($i=1,$max=sizeof($excel);$i<$max;$i++) {
			$row = explode(",",$excel[$i]);

			$method = 'SET_ORDER_DELIVERY';
			$param = array(
				'sendDt' => date('YmdHi'),
				'dlvMthdCd' => '01',
				'dlvEtprsCd' => sprintf('%05s',$row[4]),	// 5자리 택배사 코드
				'invcNo' => $row[5],
				'dlvNo' => $row[6]
			);

			$rs = $shople->request($method,$param,'');

			if ($rs['result'] === true) {

			}

		}

		?>
		<script type="text/javascript">
			alert('일괄 발송처리 되었습니다.');
			opener.nsShople.order.reload();
			self.close();
		</script>
		<?
		exit;

	}


	exit;
}
echo $shople->json_encode($rs);
?>
