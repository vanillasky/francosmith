<?
include "../lib.php";
require_once ('./_inc/config.inc.php');

$mode = isset($_POST['mode']) ? $_POST['mode'] : '';

$shople = Core::loader('shople');

if ($mode == 'qnalist') {

	// 11번가 상품 Q&A 리스트 가져오기

	//  파라미터
		$_POST['skey']	= isset($_POST['skey'])  ? $_POST['skey'] : '';
		$_POST['sword'] = isset($_POST['sword']) ? trim($_POST['sword']) : '';
		$_POST['regdt'] = isset($_POST['regdt']) ? $_POST['regdt'] : array(date('Ymd'),date('Ymd'));
		$_POST['qnacd'] = isset($_POST['qnacd']) ? $_POST['qnacd'] : '';
		$_POST['stats'] = isset($_POST['stats']) ? $_POST['stats'] : '';

		$method = 'GET_QNA';
		$param = array(
			'startTime'=>$_POST['regdt'][0],	// YYYYMMDD
			'endTime'=>$_POST['regdt'][1],		// YYYYMMDD
			'answerStatus'=>$_POST['stats'],				// 00 : 전체조회, 01 : 답변완료조회, 02 : 미답변조회
		);
		$data = array();

		$rs = $shople->request($method,$param,$data);

		if ($rs['result'] === true) {

			$_arRow = $rs['body'];
			$arRow = array();

			// 검색 필터링 및 seq 정의.
			foreach($_arRow as $v) {
				// 필터링
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

	// 변수 받기
	$_POST['skey'] = isset($_POST['skey']) ? $_POST['skey'] : '';
	$_POST['sword'] = isset($_POST['sword']) ? trim($_POST['sword']) : '';

	$_POST['selStatCd'] = isset($_POST['selStatCd']) ? $_POST['selStatCd'] : '';

	$_POST['regdt'] = isset($_POST['regdt']) ? $_POST['regdt'] : array(date('Ymd'),date('Ymd'));

	$_POST['page_num'] = isset($_POST['page_num']) ? $_POST['page_num'] : 10;
	$_POST['page'] = isset($_POST['page']) ? $_POST['page'] : 1;


// 11번가 상품 후기 리스트 가져오기

/*
prdNo String O 상품번호.
pageNo String O 페이지번호
null : 페이지번호 없을 시 null 입력. Default 0
pageSize String O 페이지 사이즈
null : 페이지 사이즈 없을 시 null 입력. Default 10
evlPnt String O 상품평가
null : 상품평가 없을 시 null 입력. null 일 경우 전체 조회
1 : 불만
2 : 보통
3 : 만족
buyGrdCd String O 작성자등급
null : 작성자 등급 없을 시 null 입력. null 일 경우 전체 조회
1 : VVIP등급
2 : VIP등급
3 : TOP등급
4 : BEST등급
5 : FAMILY등급
6 : NEW등급

*/
	$method = 'GET_REVIEW';
	$param = array(
	'prdNo'=>'',		// YYYYMMDD
	'pageNo'=>'',		// YYYYMMDD
	'pageSize'=>'',		// 00 : 전체조회, 01 : 답변완료조회, 02 : 미답변조회
	'evlPnt'=>'',		// 00 : 전체조회, 01 : 답변완료조회, 02 : 미답변조회
	'buyGrdCd'=>'',		// 00 : 전체조회, 01 : 답변완료조회, 02 : 미답변조회
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
