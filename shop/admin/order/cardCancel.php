<?php
include "../lib.php";
include "../../conf/config.php";
include "../../lib/cardCancel.class.php";
include "../../lib/cardCancel_social.class.php";

if($_GET[ordno]){
	$todayshop_noti = Core::loader('todayshop_noti');
	$ts_orderdata = $todayshop_noti->getorderinfo($_GET['ordno']);
	if ($ts_orderdata) {
		//	투데이샵 주문 취소
		$cancel = new cardCancel_social();
	}
	else {
		// 일반 주문 취소
		$cancel = new cardCancel();		
	}
	unset($todayshop_noti, $ts_orderdata);

	if (empty($_GET[sno]) === false) {
		$cancel->no_cancel = $_GET[sno];
	}
	$res = $cancel -> cancel_pg($_GET[ordno]);
	if($res){
		msg('결제승인취소완료');
		echo("<script>parent.location.reload();</script>");
	} else {
		msg('결제승인취소실패');
		echo("<script>
		parent.document.getElementById('canceltype".$_GET['idx']."').innerHTML='<a href=\"javascript:cardSettleCancel(\'$_GET[ordno]\',\'$_GET[sno]\',\'$_GET[idx]\')\"><img src=\"../img/cardcancel_btn.gif\" ></a>';</script>");
	}
}
?>
