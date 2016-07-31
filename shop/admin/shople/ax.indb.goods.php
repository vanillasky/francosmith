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

	if ($seq == '') exit('상품 번호 오류.');

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
	$rs['body'] = '테스트 동작임';

}
elseif ($mode == 'save') {

	// 이때 넘어오는 seq 는 고도몰 상품번호가 아닌, 11번가 상품번호 이므로, gd_shople_goods_map 테이블에서 고도몰 상품번호를 가져와야 합니다.
	list($seq) = $db->fetch("SELECT goodsno FROM ".GD_SHOPLE_GOODS_MAP." WHERE 11st = '".$seq."'");

	// 수정되는 필드만 샥샥.
	$prdNm	= isset($_POST['prdNm'])  ? iconv('UTF-8','EUC-KR',$_POST['prdNm']) : '';				// 상품명
	$selPrc = isset($_POST['selPrc']) ? preg_replace('/[^0-9]/','',$_POST['selPrc']) : '';		// 가격

	$data = $shople->getGoods($seq);

	$method = 'EDIT_PRODUCT';
	$param = array(
		'prdNo' => $data['is11st']
	);

	// 변경된 데이터
	if ($prdNm != '') $data['goodsnm'] = $prdNm;
	if ($selPrc != '') {
		$data['price'] = $selPrc;

		// 옵션이 있는 경우 판매가와 가격이 같은 상품이 반드시 1개 있어야 함.
		if (sizeof($data['options']) > 1) {

			$_flag = false;

			foreach($data['options'] as $opt)  {
				if ($opt['price'] == $data['price']) {
					$_flag = true;
					break;
				}
			}

			if (! $_flag) {
				$rs['body'] = '판매가와 가격이 같은 옵션상품이 반드시 1개 이상 존재해야 합니다.';
				echo $shople->json_encode($rs);
				exit;
			}

		}
	}

	// 상품정보 갱신
	$shople->setGoods($data);

	// 11번가 전송
	$rs = $shople->request($method,$param,$data);

}
elseif ($mode == 'list') {


	// get 파라미터
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


	// 11번가 상품 리스트 가져오기
		$method = 'GET_PRODUCTLIST_BYMULTI';
		$param = array(
		// 페이징 변수
		'page' => isset($_POST['page']) ? $_POST['page'] : 1,
		'page_num' => isset($_POST['page']) ? $_POST['page_num'] : 10,
		);
		$data = array();

		// 리스팅 조건
		if ($_POST['category1'] && $_POST['category1'] != 'null') $data['category1'] = $_POST['category1'];
		if ($_POST['category2'] && $_POST['category2'] != 'null') $data['category2'] = $_POST['category2'];
		if ($_POST['category3'] && $_POST['category3'] != 'null') $data['category3'] = $_POST['category3'];
		if ($_POST['category4'] && $_POST['category4'] != 'null') $data['category4'] = $_POST['category4'];

		if ($_POST['selStatCd']) $data['selStatCd'] = $_POST['selStatCd'];

		if ($_POST['skey'] && $_POST['sword']) $data[ $_POST['skey'] ] = iconv('UTF-8','EUC-KR',$_POST['sword']);
		if ($_POST['regdt'][0] && $_POST['regdt'][1]) {
			$data['schDateType'] = 1;	// 1 : 상품생성일 기준,2 : 상품판매일 기준,3 : 판매종료일 기준,4 : 상품수정일 기준
			$data['schBgnDt'] = $_POST['regdt'][0];
			$data['schEndDt'] = $_POST['regdt'][1];
		}

		// 페이징
		$data['limit'] = $_POST['page_num'] + 1;
		$data['start'] = ($_POST['page'] - 1) * $_POST['page_num'];
		$data['end'] = $data['start'] + $_POST['page_num']-1;

		$rs = $shople->request($method,$param,$data);

		if ($rs['result'] === true) {

		}

}

echo $shople->json_encode($rs);
?>
