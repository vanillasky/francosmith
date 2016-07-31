<?
include "../lib.php";
require_once ('./_inc/config.inc.php');

$shople = Core::loader('shople');

$mode = isset($_POST['mode']) ? $_POST['mode'] : '';


$rs = array(
	'result' => false,
	'body' => ''
);

// ����Ʈ ��������
if ($mode == 'list' || $mode == 'download') {

	// �Ķ����
	$_POST['regdt'] = isset($_POST['regdt']) ? $_POST['regdt'] : array(date('Ymd'),date('Ymd'));
	$_POST['method'] = isset($_POST['method']) ? strtoupper($_POST['method']) : 'GET_ORDER_CONFIRM_LIST';
	$_POST['method'] = in_array($_POST['method'], array( 'GET_ORDER_CONFIRM_LIST','GET_ORDER_DELIVERY_LIST','GET_ORDER_DELIVERING_LIST','GET_ORDER_COMPLETE_LIST' ) ) ? $_POST['method'] : 'GET_ORDER_CONFIRM_LIST';

	$method = $_POST['method'];
	$param = array(
		'startTime'=>$_POST['regdt'][0].'0000',		// YYYYMMDDhhmm
		'endTime'=>$_POST['regdt'][1].'2359',		// YYYYMMDDhhmm

		// ����¡ ����
		'page' => isset($_POST['page']) ? $_POST['page'] : 1,
		'page_num' => isset($_POST['page']) ? $_POST['page_num'] : 10,
	);





	$rs = $shople->request($method,$param,$data);

	if ($rs['result'] === true) {

	}

	if ($mode == 'download') {

		// ������ ����.
		header("Content-Type: application/vnd.ms-excel; charset=euc-kr");
		header("Content-Disposition: attachment; filename=shople_order_".date("YmdHi").".xls");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");

		echo '<table border=1>';
		echo '
		<tr>
			<td>��ȣ</td>
			<td>�ֹ��Ͻ�</td>
			<td>�����Ϸ��Ͻ�</td>
			<td>��۹��</td>
			<td>�ù���ڵ�</td>
			<td>����/����ȣ</td>
			<td>��۹�ȣ</td>
			<td>�ֹ���ȣ</td>
			<td>��ǰ��ȣ</td>
			<td>��ǰ��</td>
			<td>�ɼ�/�߰�����</td>
			<td>�Ǹ��ڻ�ǰ�ڵ�</td>
			<td>�ǸŴܰ�</td>
			<td>�ɼǰ�</td>
			<td>����</td>
			<td>�����ݾ�</td>
			<td>�ֹ��Ѿ�</td>
			<td>��ۺ񱸺�</td>
			<td>��ۺ�</td>
			<td>������</td>
			<td>������ID</td>
			<td>������</td>
			<td>��ȭ��ȣ</td>
			<td>�ڵ���</td>
			<td>�����ȣ</td>
			<td>������ּ�</td>
			<td>��۽ÿ䱸����</td>
			<td>�ֹ��󼼹�ȣ</td>
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
// �Ǹ� �ź� ó��
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
// �߼� ó��
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
// ����Ȯ�� ó��
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
	$rs['body'] = '�׽�Ʈ ������';

}
elseif ($mode == 'excel') {
	// ajax �ƴ�.

	$file = $_FILES['excel'];

	if ($file['size'] > 0) {

		$excel = file($file['tmp_name']);

		for ($i=1,$max=sizeof($excel);$i<$max;$i++) {
			$row = explode(",",$excel[$i]);

			$method = 'SET_ORDER_DELIVERY';
			$param = array(
				'sendDt' => date('YmdHi'),
				'dlvMthdCd' => '01',
				'dlvEtprsCd' => sprintf('%05s',$row[4]),	// 5�ڸ� �ù�� �ڵ�
				'invcNo' => $row[5],
				'dlvNo' => $row[6]
			);

			$rs = $shople->request($method,$param,'');

			if ($rs['result'] === true) {

			}

		}

		?>
		<script type="text/javascript">
			alert('�ϰ� �߼�ó�� �Ǿ����ϴ�.');
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
