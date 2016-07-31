<?

@include "../lib/library.php";
@include "../conf/config.php";
@include dirname(__FILE__)."/interpark.class.php";

switch ($_GET['mode']){

	case "orderListForMulti": # 주문내역조회
	case "orderListDelvForMulti": # 주문내역조회2
	case "cnclNClmReqListForMulti": # 주문취소/반품/교환요청리스트
	case "clmListForMulti": # 클레임리스트
	case "orderCompListForMulti": # 구매확정조회
		include dirname(__FILE__)."/interpark.i2e_openstyle_order.class.php";
		$i2e_order_api = new i2e_order_api();
		break;
}

?>