<?

@include "../lib/library.php";
@include "../conf/config.php";
@include dirname(__FILE__)."/interpark.class.php";

switch ($_GET['mode']){

	case "orderListForComm": # 주문내역조회
	case "orderListDelvForComm": # 주문내역조회2
	case "cnclNClmNExchReqListForComm": # 주문취소/반품/교환요청리스트
	case "clmListForComm": # 클레임리스트
	case "orderCompListForComm": # 구매확정조회
		include dirname(__FILE__)."/interpark.i2e_order.class.php";
		$i2e_order_api = new i2e_order_api();
		break;
}

?>