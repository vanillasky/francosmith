<?
include "../lib.php";
require_once ('./_inc/config.inc.php');

$shople = Core::loader('shople');

$mode = isset($_POST['mode']) ? $_POST['mode'] : '';
$seq = isset($_POST['seq']) ? $_POST['seq'] : '';

$rs = array(
	'result' => false,
	'body' => ''
);

if ($mode == 'register') {

	if ($seq == '') exit('��ǰ ��ȣ ����.');

	$method = 'REGISTER_PRODUCT';
	$param = '';
	$data = $shople->getGoods($seq);
	$shople->setGoods($data);

	if ($data['is11st']) {
		$method = 'EDIT_PRODUCT';
		$param = array(
			'prdNo' => $data['is11st']
		);
	}
	$rs = $shople->request($method,$param,$data);

	if ($rs['result'] === true) {

		$_11stcode = $rs['body'];

		$db->query("DELETE FROM ".GD_SHOPLE_GOODS_MAP." WHERE goodsno = '$seq'");

		$query = "
		INSERT INTO ".GD_SHOPLE_GOODS_MAP." SET
			goodsno = '$seq',
			11st = '$_11stcode',
			regdt = NOW()
		";
		$db->query($query);
	}

}
elseif ($mode == 'stopdisplay') {

	$method = 'SET_DISPLAY_STOP';
	$param = array(
		'prdNo' => $seq
	);

	$rs = $shople->request($method,$param,'');

	if ($rs['result'] === true) {

	}

}
elseif ($mode == 'startdisplay') {

	$method = 'SET_DISPLAY_START';
	$param = array(
		'prdNo' => $seq
	);

	$rs = $shople->request($method,$param,'');

	if ($rs['result'] === true) {

	}

}
elseif ($mode == 'test') {

	$rs['result'] = true;
	$rs['body'] = '�׽�Ʈ ������';

}
elseif ($mode == 'save') {

	// �̶� �Ѿ���� seq �� ���� ��ǰ��ȣ�� �ƴ�, 11���� ��ǰ��ȣ �̹Ƿ�, gd_shople_goods_map ���̺��� ���� ��ǰ��ȣ�� �����;� �մϴ�.
	list($seq) = $db->fetch("SELECT goodsno FROM ".GD_SHOPLE_GOODS_MAP." WHERE 11st = '".$seq."'");

	// �����Ǵ� �ʵ常 ����.
	$prdNm	= isset($_POST['prdNm'])  ? iconv('UTF-8','EUC-KR',$_POST['prdNm']) : '';				// ��ǰ��
	$selPrc = isset($_POST['selPrc']) ? preg_replace('/[^0-9]/','',$_POST['selPrc']) : '';		// ����

	$data = $shople->getGoods($seq);

	$method = 'EDIT_PRODUCT';
	$param = array(
		'prdNo' => $data['is11st']
	);

	// ����� ������
	if ($prdNm != '') $data['goodsnm'] = $prdNm;
	if ($selPrc != '') {
		$data['price'] = $selPrc;

		// �ɼ��� �ִ� ��� �ǸŰ��� ������ ���� ��ǰ�� �ݵ�� 1�� �־�� ��.
		if (sizeof($data['options']) > 1) {

			$_flag = false;

			foreach($data['options'] as $opt)  {
				if ($opt['price'] == $data['price']) {
					$_flag = true;
					break;
				}
			}

			if (! $_flag) {
				$rs['body'] = '�ǸŰ��� ������ ���� �ɼǻ�ǰ�� �ݵ�� 1�� �̻� �����ؾ� �մϴ�.';
				echo $shople->json_encode($rs);
				exit;
			}

		}
	}

	// ��ǰ���� ����
	$shople->setGoods($data);

	// 11���� ����
	$rs = $shople->request($method,$param,$data);

}
elseif ($mode == 'list') {


	// get �Ķ����
		$_POST['category1'] = isset($_POST['category1']) ? $_POST['category1'] : '';
		$_POST['category2'] = isset($_POST['category2']) ? $_POST['category2'] : '';
		$_POST['category3'] = isset($_POST['category3']) ? $_POST['category3'] : '';
		$_POST['category4'] = isset($_POST['category4']) ? $_POST['category4'] : '';

		$_POST['skey'] = isset($_POST['skey']) ? $_POST['skey'] : '';
		$_POST['sword'] = isset($_POST['sword']) ? trim($_POST['sword']) : '';

		$_POST['selStatCd'] = isset($_POST['selStatCd']) ? $_POST['selStatCd'] : '';

		$_POST['regdt'] = isset($_POST['regdt']) ? $_POST['regdt'] : array('','');

		$_POST['page_num'] = isset($_POST['page_num']) ? $_POST['page_num'] : 1000;
		$_POST['page'] = isset($_POST['page']) ? $_POST['page'] : 1;
		$_POST['page_max'] = isset($_POST['page_max']) ? $_POST['page_max'] : 1;


	// 11���� ��ǰ ����Ʈ ��������
		$method = 'GET_PRODUCTLIST_BYMULTI';
		$param = array(
		// ����¡ ����
		'page' => isset($_POST['page']) ? $_POST['page'] : 1,
		'page_num' => isset($_POST['page']) ? $_POST['page_num'] : 10,
		);
		$data = array();

		// ������ ����
		if ($_POST['category1'] && $_POST['category1'] != 'null') $data['category1'] = $_POST['category1'];
		if ($_POST['category2'] && $_POST['category2'] != 'null') $data['category2'] = $_POST['category2'];
		if ($_POST['category3'] && $_POST['category3'] != 'null') $data['category3'] = $_POST['category3'];
		if ($_POST['category4'] && $_POST['category4'] != 'null') $data['category4'] = $_POST['category4'];

		if ($_POST['selStatCd']) $data['selStatCd'] = $_POST['selStatCd'];

		if ($_POST['skey'] && $_POST['sword']) $data[ $_POST['skey'] ] = iconv('UTF-8','EUC-KR',$_POST['sword']);
		if ($_POST['regdt'][0] && $_POST['regdt'][1]) {
			$data['schDateType'] = 1;	// 1 : ��ǰ������ ����,2 : ��ǰ�Ǹ��� ����,3 : �Ǹ������� ����,4 : ��ǰ������ ����
			$data['schBgnDt'] = $_POST['regdt'][0];
			$data['schEndDt'] = $_POST['regdt'][1];
		}

		// ����¡
		$data['limit'] = $_POST['page_num'] + 1;
		$data['start'] = ($_POST['page'] - 1) * $_POST['page_num'];
		$data['end'] = $data['start'] + $_POST['page_num']-1;

		$rs = $shople->request($method,$param,$data);

		if ($rs['result'] === true) {

		}

}

echo $shople->json_encode($rs);
?>
