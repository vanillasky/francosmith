<?
include "../lib.php";
require_once ('./_inc/config.inc.php');

$mode = isset($_POST['mode']) ? $_POST['mode'] : '';

$shople = Core::loader('shople');

if ($mode == 'qnalist') {

	// 11���� ��ǰ Q&A ����Ʈ ��������

	//  �Ķ����
		$_POST['skey']	= isset($_POST['skey'])  ? $_POST['skey'] : '';
		$_POST['sword'] = isset($_POST['sword']) ? trim($_POST['sword']) : '';
		$_POST['regdt'] = isset($_POST['regdt']) ? $_POST['regdt'] : array(date('Ymd'),date('Ymd'));
		$_POST['qnacd'] = isset($_POST['qnacd']) ? $_POST['qnacd'] : '';
		$_POST['stats'] = isset($_POST['stats']) ? $_POST['stats'] : '';

		$method = 'GET_QNA';
		$param = array(
			'startTime'=>$_POST['regdt'][0],	// YYYYMMDD
			'endTime'=>$_POST['regdt'][1],		// YYYYMMDD
			'answerStatus'=>$_POST['stats'],				// 00 : ��ü��ȸ, 01 : �亯�Ϸ���ȸ, 02 : �̴亯��ȸ
		);
		$data = array();

		$rs = $shople->request($method,$param,$data);

		if ($rs['result'] === true) {

			$_arRow = $rs['body'];
			$arRow = array();

			// �˻� ���͸� �� seq ����.
			foreach($_arRow as $v) {
				// ���͸�
					if ($_POST['qnacd'] != '') if ($_POST['qnacd'] != $v['qnaDtlsCd']) continue;
					if ($_POST['stats'] != '') if ($_POST['stats'] != $v['answerYn']) continue;

					if ($_POST['sword'] != '') {
						if		($_POST['skey'] == 'prdNm') if (strpos($v['prdNm'], iconv('UTF-8','EUC-KR',$_POST['sword'])) === false) continue;
						elseif	($_POST['skey'] == 'prdNo') if (strpos($v['brdInfoClfNo'],$_POST['sword']) === false) continue;
					}

				$arRow[ 'SEQ'.$v['brdInfoNo'] ] = $v;

			}

			$rs['body'] = $arRow;
		}

}
else if ($mode == 'reviewlist') {

	// ���� �ޱ�
	$_POST['skey'] = isset($_POST['skey']) ? $_POST['skey'] : '';
	$_POST['sword'] = isset($_POST['sword']) ? trim($_POST['sword']) : '';

	$_POST['selStatCd'] = isset($_POST['selStatCd']) ? $_POST['selStatCd'] : '';

	$_POST['regdt'] = isset($_POST['regdt']) ? $_POST['regdt'] : array(date('Ymd'),date('Ymd'));

	$_POST['page_num'] = isset($_POST['page_num']) ? $_POST['page_num'] : 10;
	$_POST['page'] = isset($_POST['page']) ? $_POST['page'] : 1;


// 11���� ��ǰ �ı� ����Ʈ ��������

/*
prdNo String O ��ǰ��ȣ.
pageNo String O ��������ȣ
null : ��������ȣ ���� �� null �Է�. Default 0
pageSize String O ������ ������
null : ������ ������ ���� �� null �Է�. Default 10
evlPnt String O ��ǰ��
null : ��ǰ�� ���� �� null �Է�. null �� ��� ��ü ��ȸ
1 : �Ҹ�
2 : ����
3 : ����
buyGrdCd String O �ۼ��ڵ��
null : �ۼ��� ��� ���� �� null �Է�. null �� ��� ��ü ��ȸ
1 : VVIP���
2 : VIP���
3 : TOP���
4 : BEST���
5 : FAMILY���
6 : NEW���

*/
	$method = 'GET_REVIEW';
	$param = array(
	'prdNo'=>'',		// YYYYMMDD
	'pageNo'=>'',		// YYYYMMDD
	'pageSize'=>'',		// 00 : ��ü��ȸ, 01 : �亯�Ϸ���ȸ, 02 : �̴亯��ȸ
	'evlPnt'=>'',		// 00 : ��ü��ȸ, 01 : �亯�Ϸ���ȸ, 02 : �̴亯��ȸ
	'buyGrdCd'=>'',		// 00 : ��ü��ȸ, 01 : �亯�Ϸ���ȸ, 02 : �̴亯��ȸ
	);
	$data = array();

	$rs = $shople->request($method,$param,$data);

	$_arRow = $rs['body'];
	$arRow = array();

	foreach($_arRow as $v) {
		$arRow[ 'SEQ'.$v['brdInfoNo'] ] = $v;
	}

	$rs['body'] = $arRow;

}
else if ($mode == 'qna') {

	$answerCont = isset($_POST['answerCont']) ? iconv('UTF-8','EUC-KR',trim($_POST['answerCont'])) : '';
	$brdInfoNo = isset($_POST['brdInfoNo']) ? $_POST['brdInfoNo'] : '';
	$brdInfoClfNo = isset($_POST['brdInfoClfNo']) ? $_POST['brdInfoClfNo'] : '';

	$method = 'ANSWER_QNA';
	$param = array(
		'brdInfoNo' => $brdInfoNo,
		'prdNo' => $brdInfoClfNo
	);
	$data = array(
		'answerCont'=>$answerCont
	);

	$rs = $shople->request($method,$param,$data);

	if ($rs['result'] === true) {

	}

}

//

echo $shople->json_encode($rs);
?>
