<?

@include "./library.php";
@include "../conf/config.php";

if (!class_exists('cashreceipt', false)) @include (dirname(__FILE__)."/cashreceipt.class.php");
$cashreceipt = new cashreceipt;

$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

switch ( $mode ){

	case "receiptOfMoney": # 주문입금확인 처리

		@include dirname(__FILE__)."/./bank.class.php";
		$bk = new Bank( 'receive', $_GET[ordno] );

		break;

	case "filterOrderStep": # 주문상태기준으로 필터

		@include dirname(__FILE__)."/./bank.class.php";
		$bk = new Bank( 'filterOrderStep', '' );

		break;
}

?>
