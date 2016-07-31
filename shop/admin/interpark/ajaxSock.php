<?

include "../lib.php";
include dirname(__FILE__)."/../../lib/interpark.class.php";

$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

switch ( $mode ){

	case "isExists": case "putMerchant":

		header("Content-type: text/html; charset=euc-kr");
		$interpark = new interpark();
		$out = $interpark->$mode(array_merge($_GET, $_POST));
		$rCode = ($out[0] ? $out[0] : 400);
		if (!preg_match("/^[true|false|join]/i",$out[1])) unset($out[1]);
		if (preg_match("/^false/i",$out[1])) $out[1] = trim(preg_replace("/^false[ |]*-[ |]*/i", "", $out[1]));

		if ($mode == 'isExists'){
			if ($out[1] == 'true' || $out[1] == 'join') echo $out[1];
			else header("Status: {$out[1]}", true, $rCode);
		}
		else if ($mode == 'putMerchant'){
			if ($out[1] == 'true') echo $out[1];
			else header("Status: {$out[1]}", true, $rCode);
		}
		echo ""; # 삭제마요
		exit;

		break;

	case "getShopCategory":

		header("Content-type: text/xml; charset=euc-kr");
		$interpark = new interpark();
		$out = $interpark->getShopCategory($_GET);
		echo $out;

		break;

	case "getDispSrch": # 전시카테고리검색 요청

		if ( $_GET[srchName] == '' )
		{
			header("Status: 검색어를 입력하셔야 합니다.", true, 400);
			echo "";
			exit;
		}
		header("Content-type: text/xml; charset=euc-kr");
		$interpark = new interpark();
		$out = $interpark->getDispSrch($_GET);
		echo $out;

		break;

	case "getDispNm": # 전시카테고리명 출력

		header("Content-type: text/html; charset=euc-kr");
		$interpark = new interpark();
		$out = $interpark->getDispNm($_GET);
		echo $out;

		break;

	case "putTransmitGoods": # 상품등록/수정 즉시 인터파크에 전송

		### 구간정의
		$num = 10;
		$glist = array();
		if ($_POST['section']) $glist = explode(":", $_POST['section']);
		else {
			$first = $num * ($_POST[point] - 1);
			$_POST['query'] = stripslashes($_POST['query']) . " limit {$first}, {$num}";
			$res = $db->query($_POST['query']);
			while ($data = $db->fetch($res)) $glist[] =  $data[goodsno];
		}

		header("Content-type: text/xml; charset=euc-kr");
		include dirname(__FILE__)."/../../lib/interpark.e2i_openstyle_goods.class.php";
		$e2i_goods_api = new e2i_goods_openstyle_api($glist);
		echo $e2i_goods_api->strXml;

		break;

	case "cnclOutOfStockForComm": # 품절주문취소요청
	case "cnclReqAcceptForComm": # 출고전주문취소요청승인
	case "clmReqAcceptForComm": # 반품요청승인
	case "exchangeReqAcceptForComm": # 교환요청승인
	case "clmReqRefuseForComm": # 반품요청거부
	case "exchangeReqRefuseForComm": # 교환요청거부
	case "clmStoreCompForComm": # 반품/교환입고확정
	case "exchangeCompForComm": # 교환확정

		header("Content-type: text/html; charset=euc-kr");
		include dirname(__FILE__)."/../../lib/interpark.e2i_order.class.php";
		$e2i_order_api = new e2i_order_api($mode, $_GET);

		break;
	//신규API
	case "openstyle_cnclOutOfStockReqForComm": # 품절주문취소요청
	case "openstyle_cnclReqAcceptForComm": # 출고전주문취소요청승인[기존API명과 동일]
	case "openstyle_rtnReqAcceptForComm": # 반품요청승인
	case "openstyle_exchReqAcceptForComm": # 교환요청승인
	case "openstyle_rtnReqRefuseForComm": # 반품요청거부
	case "openstyle_exchReqRefuseForComm": # 교환요청거부
	case "openstyle_clmEnterWhCompForComm": # 반품/교환입고확정
	case "openstyle_exchOutWhCompForComm": # 교환확정
		header("Content-type: text/html; charset=euc-kr");
		include dirname(__FILE__)."/../../lib/interpark.e2i_openstyle_order.class.php";
		$e2i_order_api = new e2i_order_api($mode, $_GET);

	break;
}
?>