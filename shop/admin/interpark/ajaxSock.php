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
		echo ""; # ��������
		exit;

		break;

	case "getShopCategory":

		header("Content-type: text/xml; charset=euc-kr");
		$interpark = new interpark();
		$out = $interpark->getShopCategory($_GET);
		echo $out;

		break;

	case "getDispSrch": # ����ī�װ��˻� ��û

		if ( $_GET[srchName] == '' )
		{
			header("Status: �˻�� �Է��ϼž� �մϴ�.", true, 400);
			echo "";
			exit;
		}
		header("Content-type: text/xml; charset=euc-kr");
		$interpark = new interpark();
		$out = $interpark->getDispSrch($_GET);
		echo $out;

		break;

	case "getDispNm": # ����ī�װ��� ���

		header("Content-type: text/html; charset=euc-kr");
		$interpark = new interpark();
		$out = $interpark->getDispNm($_GET);
		echo $out;

		break;

	case "putTransmitGoods": # ��ǰ���/���� ��� ������ũ�� ����

		### ��������
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

	case "cnclOutOfStockForComm": # ǰ���ֹ���ҿ�û
	case "cnclReqAcceptForComm": # ������ֹ���ҿ�û����
	case "clmReqAcceptForComm": # ��ǰ��û����
	case "exchangeReqAcceptForComm": # ��ȯ��û����
	case "clmReqRefuseForComm": # ��ǰ��û�ź�
	case "exchangeReqRefuseForComm": # ��ȯ��û�ź�
	case "clmStoreCompForComm": # ��ǰ/��ȯ�԰�Ȯ��
	case "exchangeCompForComm": # ��ȯȮ��

		header("Content-type: text/html; charset=euc-kr");
		include dirname(__FILE__)."/../../lib/interpark.e2i_order.class.php";
		$e2i_order_api = new e2i_order_api($mode, $_GET);

		break;
	//�ű�API
	case "openstyle_cnclOutOfStockReqForComm": # ǰ���ֹ���ҿ�û
	case "openstyle_cnclReqAcceptForComm": # ������ֹ���ҿ�û����[����API��� ����]
	case "openstyle_rtnReqAcceptForComm": # ��ǰ��û����
	case "openstyle_exchReqAcceptForComm": # ��ȯ��û����
	case "openstyle_rtnReqRefuseForComm": # ��ǰ��û�ź�
	case "openstyle_exchReqRefuseForComm": # ��ȯ��û�ź�
	case "openstyle_clmEnterWhCompForComm": # ��ǰ/��ȯ�԰�Ȯ��
	case "openstyle_exchOutWhCompForComm": # ��ȯȮ��
		header("Content-type: text/html; charset=euc-kr");
		include dirname(__FILE__)."/../../lib/interpark.e2i_openstyle_order.class.php";
		$e2i_order_api = new e2i_order_api($mode, $_GET);

	break;
}
?>