<?php
include "../lib.php";
include "../../conf/config.php";
include "../../lib/cardCancel.class.php";

$todayshop = Core::loader('todayshop');
$formatter = Core::loader('stringFormatter');
$couponGenerator = Core::loader('couponGenerator');

$sms = Core::loader('sms');
//$todayshop_noti = Core::loader('todayshop_noti');

$mode = isset($_POST['mode']) ? $_POST['mode'] : '';
$goodsno = isset($_POST['goodsno']) ? $_POST['goodsno'] : '';
$ordno = isset($_POST['ordno']) ? $_POST['ordno'] : '';

switch ($mode) {
	case 'sms' :
		// 주문서 를 가져오기.
		$query = "
			SELECT
				O.nameOrder, O.ordno, O.orddt, O.mobileReceiver,
				OI.ea,
				CP.cp_sno, CP.cp_num, CP.cp_publish
			FROM ".GD_ORDER." AS O
			INNER JOIN ".GD_ORDER_ITEM." AS OI
			ON OI.ordno = O.ordno
			INNER JOIN ".GD_GOODS." AS G
			ON G.goodsno = OI.goodsno /* AND G.todaygoods = 'y' */
			LEFT JOIN ".GD_TODAYSHOP_ORDER_COUPON." AS CP
			ON O.ordno = CP.ordno
			WHERE O.ordno = '$ordno'
		";
		$row = $db->fetch($query);

		if (!$row) exit('alert("주문정보가 없습니다.");');

		// sms 발송 갯수 체크
		if ($sms->smsPt < 1) exit('alert("sms 발송 건수 부족.");');

		$_unique = false;

		// 쿠폰 번호 없으면 생성하고 보낼 것인가 아님 오류를 낼 것인가.
		if (! $row['cp_sno']) {
			$row['cp_sno'] = $todayshop->publishCoupon($ordno);
		}

		// sms 전송(=개별처리)
		if ($phone = $formatter->get($row['mobileReceiver'],'dial','-')) {
			$db->query("UPDATE ".GD_TODAYSHOP_ORDER_COUPON." SET cp_publish = 1 WHERE cp_sno = '$row[cp_sno]'");	// 발급 처리
			ctlStep($row['ordno'],4,$row['ea']);
		}
		else {
			exit('alert("받는 분의 휴대폰 번호가 올바르지 않습니다.");');
		}
		break;

	case 'cancel_all' :
		$cancel = Core::loader('cardCancel_social');
		$cancel->cancel_code = 10;	// 거래미성사 (gd_code 테이블에 cancel 키로 추가해야댐)

		if (!in_array($cancel->cfg['settlePg'],array('lgdacom','agspay','inicis'))) {	// lg u+, 올더게이트, 이니시스만 가능할꺼임.
			msg('현재 이용중이신 PG 서비스는 일괄 취소를 지원하지 않습니다.');
			exit;
		}

		// 주문건 가져오기
		$query = "
			SELECT
				O.ordno
			FROM ".GD_ORDER." AS O
			INNER JOIN ".GD_ORDER_ITEM." AS OI
			ON OI.ordno = O.ordno
			INNER JOIN ".GD_GOODS." AS G
			ON G.goodsno = OI.goodsno /* AND G.todaygoods = 'y' */
			LEFT JOIN ".GD_TODAYSHOP_ORDER_COUPON." AS CP
			ON O.ordno = CP.ordno
			LEFT JOIN ".GD_LIST_DELIVERY." AS LD
			ON OI.dvno = LD.deliveryno
			LEFT JOIN ".GD_MEMBER." AS MB
			ON O.m_no=MB.m_no
			WHERE
				G.goodsno = '$goodsno'
			AND O.step <> 0
		";

		$rs = $db->query($query);

		$_cnt_o = 0;
		$_cnt = 0;

		while ($row = $db->fetch($rs)) {
			if ($cancel -> cancel_pg($row['ordno'])) {
				$_cnt_o++;
			}
			$_cnt++;
		}
		msg("총 {$_cnt}건 중 {$_cnt_o}건의 주문취소가 완료 되었습니다.");
		echo("<script>parent.location.reload();</script>");
		break;
	case 'cancel':
		$cancel = Core::loader('cardCancel_social');
		$cancel->cancel_code = 10;	// 거래미성사 (gd_code 테이블에 cancel 키로 추가해야댐)
		$res = $cancel -> cancel_pg($ordno);

		if($res){
			// 투데이샵 취소 알림
			msg('주문취소가 완료 되었습니다.');
			echo("<script>parent.location.reload();</script>");
		} else {
			/*
			// 그냥 주문취소로 돌릴 것인가.
			### 주문취소
			chkCancel($ordno,$_POST);

			### 재고조정
			setStock($ordno);
			set_prn_settleprice($ordno);

			### 현금영수증(자동취소-실행)
			if (is_object($cashreceipt)){
				$cashreceipt->autoAction('cancel');
			}
			*/
			msg('주문취소가 실패 되었습니다.\n\n개별 주문건으로 이동후 취소 처리해 주세요.');
		}
		break;

	case 'publish':
		// 모든 주문서 를 가져와서 쿠폰발급.
		$query = "
			SELECT
				O.nameOrder, O.ordno, O.orddt, O.mobileReceiver,
				OI.ea,
				CP.cp_sno, CP.cp_num, CP.cp_publish
			FROM ".GD_ORDER." AS O
			INNER JOIN ".GD_ORDER_ITEM." AS OI
			ON OI.ordno = O.ordno
			INNER JOIN ".GD_GOODS." AS G
			ON G.goodsno = OI.goodsno /* AND G.todaygoods = 'y' */
			LEFT JOIN ".GD_TODAYSHOP_ORDER_COUPON." AS CP
			ON O.ordno = CP.ordno
			WHERE G.goodsno = '$goodsno'
		";

		$rs = $db->query($query);

		// sms 발송 갯수 체크
		if (mysql_num_rows($rs) > $sms->smsPt) exit('alert("sms 발송건수 부족..");');

		while ($row = $db->fetch($rs)) {
			$_unique = false;

			// 쿠폰 번호 없으면 생성
			if (! $row['cp_num']) {
				do {
					$couponGenerator->make();
					$row[cp_num] = array_pop($couponGenerator->coupon);

					list($cnt) = $db->fetch("SELECT COUNT(cp_sno) FROM gd_todayshop_order_coupon WHERE cp_num = '$row[cp_num]'");

					if ($cnt < 1) {
						$query = "
						INSERT INTO ".GD_TODAYSHOP_ORDER_COUPON." SET
							ordno = '$row[ordno]', cp_num = '$row[cp_num]', cp_publish = 0, cp_ea = '$row[ea]', regdt = NOW()
						";

						if ($db->query($query)) {
							$row[cp_sno] = $db->lastID();
							$_unique = true;
						}
					}
				} while (!$_unique);
			}	// if

			// 쿠폰 발급
			if (($phone = $formatter->get($row['mobileReceiver'],'dial','-')) && ! $row['cp_publish']) {
				$db->query("UPDATE ".GD_TODAYSHOP_ORDER_COUPON." SET cp_publish = 1 WHERE cp_sno = $row[cp_sno]");	// 발급 처리
				ctlStep($row['ordno'],4,$row['ea']);
			}
		}
		break;
	case 'status': {
		@include "../../lib/cashreceipt.class.php";
		if (class_exists('cashreceipt')) $cashreceipt = new cashreceipt();

		// 주문상태 (다중 선택이 가능하므로 OR 연산)
		$_SQL['WHERE']['OR'] = array();
		if ($_POST[step]){
			$_SQL['WHERE']['OR'][] = "
					(step IN (".implode(",",$_POST[step]).") AND step2 = '')
					";
		}

		if ($_POST[step2]) {
			foreach ($_POST[step2] as $v) {
				switch ($v){
					case "1": $_SQL['WHERE']['OR'][] = "(O.step=0 and O.step2 between 1 and 49)"; break;
					case "2": $_SQL['WHERE']['OR'][] = "(O.step in (1,2) and O.step2!=0) OR (O.cyn='r' and O.step2='44' and O.dyn!='e')"; break;
					case "3": $_SQL['WHERE']['OR'][] = "(O.step in (3,4) and O.step2!=0)"; break;
					case "60" :
						$_SQL['WHERE']['OR'][] = "(OI.dyn='e' and OI.cyn='e')";
					break; //교환완료
					case "61" : $_SQL['WHERE']['OR'][] = "oldordno != ''";break; //재주문
					default:
						$_SQL['WHERE']['OR'][] = "O.step2=$v";
					break;
				}
			}
		}

		if (!empty($_SQL['WHERE']['OR'])) $subqry = "(".implode(" OR ",$_SQL['WHERE']['OR']).")";
		unset($_SQL['WHERE']['OR']);

		$sql = "SELECT O.ordno 
				FROM ".GD_ORDER." AS O
					INNER JOIN ".GD_ORDER_ITEM." AS OI
					ON OI.ordno = O.ordno
					INNER JOIN ".GD_GOODS." AS G
					ON G.goodsno = OI.goodsno /* AND G.todaygoods = 'y' */
					LEFT JOIN ".GD_TODAYSHOP_ORDER_COUPON." AS CP
					ON O.ordno = CP.ordno
					LEFT JOIN ".GD_LIST_DELIVERY." AS LD
					ON OI.dvno = LD.deliveryno
					LEFT JOIN ".GD_MEMBER." AS MB
					ON O.m_no=MB.m_no
				WHERE
					G.goodsno = '".$_POST['goodsno']."'";
		if ($subqry) $sql .= ' AND '.$subqry;

		$res = $db->query($sql);
		while($data = $db->fetch($res, 1)) {
			### 진행상황별 처리
			ctlStep($data['ordno'],$_POST['status'],'stock');
			setStock($data['ordno']);
			set_prn_settleprice($data['ordno']);
		}

		### 현금영수증(자동발급-실행)
		if (is_object($cashreceipt)){
			$cashreceipt->autoAction('approval');
		}

		msg('상태가 변경되었습니다.');
		echo("<script>parent.location.reload();</script>");
		break;
	}
}
?>
