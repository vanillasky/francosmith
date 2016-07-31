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
	$_POST['method'] = isset($_POST['method']) ? strtoupper($_POST['method']) : 'GET_CLAIMCANCEL_REQUEST_LIST';
	$_POST['method'] = in_array($_POST['method'], array('GET_CLAIMCANCEL_REQUEST_LIST','GET_CLAIMCANCEL_COMPLETE_LIST','GET_CLAIMRETURN_REQUEST_LIST','GET_CLAIMRETURN_COMPLETE_LIST','GET_CLAIMRETURN_CANCEL_LIST','GET_CLAIMEXCHANGE_REQUEST_LIST','GET_CLAIMEXCHANGE_COMPLETE_LIST','GET_CLAIMEXCHANGE_CANCEL_LIST') ) ? $_POST['method'] : 'GET_CLAIMCANCEL_REQUEST_LIST';

	$method = $_POST['method'];
	$param = array(
		'startTime'=>$_POST['regdt'][0].'0000',		// YYYYMMDDhhmm
		'endTime'=>$_POST['regdt'][1].'2359',		// YYYYMMDDhhmm

		// ����¡ ����
		'page' => isset($_POST['page']) ? $_POST['page'] : 1,
		'page_num' => isset($_POST['page']) ? $_POST['page_num'] : 10,
	);
	$data = array();

	$rs = $shople->request($method,$param,$data);

	if ($rs['result'] === true) {

	}

	if ($mode == 'download') {

		// ������ ����.
		header("Content-Type: application/vnd.ms-excel; charset=euc-kr");
		header("Content-Disposition: attachment; filename=shople_claim_".date("YmdHi").".xls");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");

		echo '<table border=1>';
		echo '
		<tr>
			<td>�����ǰ ����</td>
			<td>��ǰ �����ڵ忡 ���� �󼼳���</td>
			<td>��ǰ ����</td>
			<td>��ǰ �����ڵ�</td>
			<td>�ܺθ� Ŭ���� ��ȣ</td>
			<td>Ŭ���� ����</td>
			<td>�ɼǸ�</td>
			<td>11���� �ֹ���ȣ</td>
			<td>�ֹ�����</td>
			<td>��ǰ��ȣ</td>
			<td>Ŭ���� ��û �Ͻ�</td>
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
// ��ҽ���
elseif ($mode == 'cancelaccept') {

	$method = 'SET_CLAIMCANCEL';
	$param = array(
		'ordPrdCnSeq'	=> $_POST['ordPrdCnSeq'],// ��� Ŭ���ӹ�ȣ
		'ordNo'			=> $_POST['ordNo'],		// �ֹ���ȣ
		'ordPrdSeq'		=> $_POST['ordPrdSeq'],	// �ֹ�����
	);

	$rs = $shople->request($method,$param,'');

	if ($rs['result'] === true) {

	}

}
// ��Ұź�
elseif ($mode == 'cancelreject') {

	$method = 'SET_CLAIMCANCEL_REJECT';
	$param = array(
		'ordNo'			=> $_POST['ordNo'],
		'ordPrdSeq'		=> $_POST['ordPrdSeq'],		// �ֹ�����
		'ordPrdCnSeq'	=> $_POST['ordPrdCnSeq'],	// ��� Ŭ���ӹ�ȣ
		'dlvMthdCd'		=> $_POST['dlvMthdCd'],		// ��۹��
		'sendDt'		=> $_POST['sendDt'],		// �������� YYYYMMDD
		'dlvEtprsCd'	=> $_POST['dlvEtprsCd'],	// �ù���ڵ�
		'invcNo'		=> $_POST['invcNo']			// �����ȣ
	);

	$rs = $shople->request($method,$param,'');

	if ($rs['result'] === true) {

	}

}
// ��ǰ����
elseif ($mode == 'returnaccept') {

	$method = 'SET_CLAIMRETURN';
	$param = array(
		'clmReqSeq'		=> $_POST['clmReqSeq'],	// Ŭ���ӹ�ȣ
		'ordNo'			=> $_POST['ordNo'],		// �ֹ���ȣ
		'ordPrdSeq'		=> $_POST['ordPrdSeq']	// �ֹ�����
	);

	$rs = $shople->request($method,$param,'');

	if ($rs['result'] === true) {

	}

}
// ��ǰ�ź�
elseif ($mode == 'returnreject') {

	$method = 'SET_CLAIMRETURN_REJECT';
	$param = array(
		'ordNo'		=> $_POST['ordNo'],		// �ֹ���ȣ
		'ordPrdSeq' => $_POST['ordPrdSeq'],	// �ֹ�����
		'clmReqSeq' => $_POST['clmReqSeq'],	// Ŭ���ӹ�ȣ
		'refsRsnCd' => $_POST['reasonCD'],	// �����ڵ� (101 : ��ǰ ��ǰ ���԰� ,102 : �� ��ǰ��û öȸ ���� ,103 : ��ǰ �Ұ� ��ǰ ,104 : ��Ÿ )
		'refsRsn'	=> $_POST['reasonCont']	// ����
	);

	$rs = $shople->request($method,$param,'');

	if ($rs['result'] === true) {

	}

}
// ��ǰ����
elseif ($mode == 'returnhold') {

	$method = 'SET_CLAIMRETURN_HOLD';
	$param = array(
		'ordNo'			=> $_POST['seq'],			// �ֹ���ȣ
		'ordPrdSeq'		=> $_POST['ordPrdSeq'],		// �ֹ�����
		'clmReqSeq'		=> $_POST['clmReqSeq'],		// Ŭ���ӹ�ȣ(��ǰ��û��ȣ)
		'deferRefsRsnCd'=> $_POST['reasonCD'],		// ���������ڵ�(101 : ��ǰ ��ǰ ���԰�,102 : ��ǰ ��ۺ� �̵���,103 : ��ǰ ��ǰ �Ѽ�,104 : ������ ���� ����,105 : ��Ÿ)
		'ordCnDtlsRsn'	=> $_POST['reasonCont']		// ����
	);

	$rs = $shople->request($method,$param,'');

	if ($rs['result'] === true) {

	}

}
// ��ǰ�ϷẸ��
elseif ($mode == 'returnaccepthold') {
// ordNo ordPrdSeq clmReqSeq deferRefsRsnCd ordCnDtlsRsn
	$method = 'SET_CLAIMRETURN_ACCEPTHOLD';
	$param = array(
		'ordNo'			=> $_POST['seq'],			// �ֹ���ȣ
		'ordPrdSeq'		=> $_POST['ordPrdSeq'],		// �ֹ�����
		'clmReqSeq'		=> $_POST['clmReqSeq'],		// Ŭ���ӹ�ȣ(��ǰ��û��ȣ)
		'deferRefsRsnCd'=> $_POST['reasonCD'],		// ���������ڵ�(101 : ��ǰ ��ǰ ���԰�,102 : ��ǰ ��ۺ� �̵���,103 : ��ǰ ��ǰ �Ѽ�,104 : ������ ���� ����,105 : ��Ÿ)
		'ordCnDtlsRsn'	=> $_POST['reasonCont']		// ����
	);

	$rs = $shople->request($method,$param,'');

	if ($rs['result'] === true) {

	}

}
// ��ȯ����
elseif ($mode == 'exchangeaccept') {

	$method = 'SET_CLAIMEXCHANGE';
	$param = array(
		'clmReqSeq'	=> $_POST['clmReqSeq'],		// Ŭ���ӹ�ȣ
		'ordNo'		=> $_POST['ordNo'],			// �ֹ���ȣ
		'ordPrdSeq' => $_POST['ordPrdSeq'],		// �ֹ�����
		'dlvEtprsCd'=> $_POST['dlvEtprsCd'],	// �ù���ڵ�
		'invcNo'	=> $_POST['invcNo']			// �����ȣ
	);

	$rs = $shople->request($method,$param,'');

	if ($rs['result'] === true) {

	}

}
// ��ȯ�ź�
elseif ($mode == 'exchangeareject') {

	$method = 'SET_CLAIMEXCHANGE_REJECT';
	$param = array(
		'ordNo'		=> $_POST['ordNo'],		// �ֹ���ȣ
		'ordPrdSeq' => $_POST['ordPrdSeq'],	// �ֹ�����
		'clmReqSeq' => $_POST['clmReqSeq'],	// Ŭ���ӹ�ȣ
		'refsRsnCd' => $_POST['reasonCD'],	// �����ڵ�	(201 : ��ȯ ��ǰ ���԰� ,202 : �� ��ȯ��û öȸ ���� ,203 : ��ȯ �Ұ� ��ǰ ,204 : ��Ÿ )
		'refsRsn'	=> $_POST['reasonCont']	// ����
	);

	$rs = $shople->request($method,$param,'');

	if ($rs['result'] === true) {

	}

}
elseif ($mode == 'test') {
	usleep( rand(100000,1000000) );
	$rs['result'] = false;
	$rs['body'] = '�׽�Ʈ ������';
}

echo $shople->json_encode($rs);
?>
