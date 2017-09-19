<?php
include "../lib.php";
include "../../conf/config.php";

//header("Content-Type: text/html; charset=utf-8");

$gf = Core::loader('goodsflow_v2');

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST); stripslashes_all($_GET); stripslashes_all($_COOKIE);
}

switch ($_POST['process']) {
	case 'invoice':

		$target = array();

		if ($_POST['target_type'] == 'query') {
			$query = base64_decode($_POST['query']);
			$rs = $db->query($query);
			while ($row = $db->fetch($rs,1)) {
				$target[] = $row['ordno'];
			}
		}
		else {
			$target = $_POST['target']['ordno'];
		}

		if (($gf->invoice($target,$_POST['mode'])) === true) {
			// 굿스플로 송장 출력 페이지로 이동되므로, 처리할것 없음.
		}
		else {
			if(!$cfg[compName] || !$cfg[address] || !$cfg[compPhone]) $message = "기본정보 설정에서 회사 정보란의 상호명 / 전화번호 / 주소를 확인해주세요. ";
			msg($message.'주문정보 전송에 실패했습니다.','close');
			exit;
		}
		break;

	case 'delivery' :

		$target = array();

		if ($_POST['target_type'] == 'query') {
			$query = base64_decode($_POST['query']);
			$rs = $db->query($query);
			while ($row = $db->fetch($rs,1)) {
				$target[] = $row['ordno'];
			}
		}
		else {
			$target = $_POST['TransUniqueCd'];
		}

		if (($rs = $gf->delvering($target)) === true) {
			msg('배송중 처리되었습니다.',$_SERVER['HTTP_REFERER']);
			exit;
		}
		else {
			msg('주문정보 전송에 실패했습니다.',-1);
			exit;
		}
		break;

}	// switch ($_POST['process'])


switch ($_GET['process']) {

	case 'cancel':
		if (($rs = $gf->cancel($_GET['TransUniqueCd'])) === true) {
			msg('발급 취소처리 되었습니다.', $_SERVER['HTTP_REFERER']);
			exit;
		}
		else {
			msg('취소처리에 실패했습니다.',-1);
			exit;
		}
		break;

	case 'reinvoice':
		if (($rs = $gf->reinvoice($_GET['TransUniqueCd'])) === true) {
			// 굿스플로 송장 출력 페이지로 이동되므로, 처리할것 없음.
		}
		else {
			echo '
			<script>
				try {
					opener.location.reload();
				} catch (e) { }
			</script>
			';
			msg('재발송 처리에 실패했습니다.','close');
			exit;
		}
		break;
}
?>
