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
		// �ֹ��� �� ��������.
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

		if (!$row) exit('alert("�ֹ������� �����ϴ�.");');

		// sms �߼� ���� üũ
		if ($sms->smsPt < 1) exit('alert("sms �߼� �Ǽ� ����.");');

		$_unique = false;

		// ���� ��ȣ ������ �����ϰ� ���� ���ΰ� �ƴ� ������ �� ���ΰ�.
		if (! $row['cp_sno']) {
			$row['cp_sno'] = $todayshop->publishCoupon($ordno);
		}

		// sms ����(=����ó��)
		if ($phone = $formatter->get($row['mobileReceiver'],'dial','-')) {
			$db->query("UPDATE ".GD_TODAYSHOP_ORDER_COUPON." SET cp_publish = 1 WHERE cp_sno = '$row[cp_sno]'");	// �߱� ó��
			ctlStep($row['ordno'],4,$row['ea']);
		}
		else {
			exit('alert("�޴� ���� �޴��� ��ȣ�� �ùٸ��� �ʽ��ϴ�.");');
		}
		break;

	case 'cancel_all' :
		$cancel = Core::loader('cardCancel_social');
		$cancel->cancel_code = 10;	// �ŷ��̼��� (gd_code ���̺� cancel Ű�� �߰��ؾߴ�)

		if (!in_array($cancel->cfg['settlePg'],array('lgdacom','agspay','inicis'))) {	// lg u+, �ô�����Ʈ, �̴Ͻý��� �����Ҳ���.
			msg('���� �̿����̽� PG ���񽺴� �ϰ� ��Ҹ� �������� �ʽ��ϴ�.');
			exit;
		}

		// �ֹ��� ��������
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
		msg("�� {$_cnt}�� �� {$_cnt_o}���� �ֹ���Ұ� �Ϸ� �Ǿ����ϴ�.");
		echo("<script>parent.location.reload();</script>");
		break;
	case 'cancel':
		$cancel = Core::loader('cardCancel_social');
		$cancel->cancel_code = 10;	// �ŷ��̼��� (gd_code ���̺� cancel Ű�� �߰��ؾߴ�)
		$res = $cancel -> cancel_pg($ordno);

		if($res){
			// �����̼� ��� �˸�
			msg('�ֹ���Ұ� �Ϸ� �Ǿ����ϴ�.');
			echo("<script>parent.location.reload();</script>");
		} else {
			/*
			// �׳� �ֹ���ҷ� ���� ���ΰ�.
			### �ֹ����
			chkCancel($ordno,$_POST);

			### �������
			setStock($ordno);
			set_prn_settleprice($ordno);

			### ���ݿ�����(�ڵ����-����)
			if (is_object($cashreceipt)){
				$cashreceipt->autoAction('cancel');
			}
			*/
			msg('�ֹ���Ұ� ���� �Ǿ����ϴ�.\n\n���� �ֹ������� �̵��� ��� ó���� �ּ���.');
		}
		break;

	case 'publish':
		// ��� �ֹ��� �� �����ͼ� �����߱�.
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

		// sms �߼� ���� üũ
		if (mysql_num_rows($rs) > $sms->smsPt) exit('alert("sms �߼۰Ǽ� ����..");');

		while ($row = $db->fetch($rs)) {
			$_unique = false;

			// ���� ��ȣ ������ ����
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

			// ���� �߱�
			if (($phone = $formatter->get($row['mobileReceiver'],'dial','-')) && ! $row['cp_publish']) {
				$db->query("UPDATE ".GD_TODAYSHOP_ORDER_COUPON." SET cp_publish = 1 WHERE cp_sno = $row[cp_sno]");	// �߱� ó��
				ctlStep($row['ordno'],4,$row['ea']);
			}
		}
		break;
	case 'status': {
		@include "../../lib/cashreceipt.class.php";
		if (class_exists('cashreceipt')) $cashreceipt = new cashreceipt();

		// �ֹ����� (���� ������ �����ϹǷ� OR ����)
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
					break; //��ȯ�Ϸ�
					case "61" : $_SQL['WHERE']['OR'][] = "oldordno != ''";break; //���ֹ�
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
			### �����Ȳ�� ó��
			ctlStep($data['ordno'],$_POST['status'],'stock');
			setStock($data['ordno']);
			set_prn_settleprice($data['ordno']);
		}

		### ���ݿ�����(�ڵ��߱�-����)
		if (is_object($cashreceipt)){
			$cashreceipt->autoAction('approval');
		}

		msg('���°� ����Ǿ����ϴ�.');
		echo("<script>parent.location.reload();</script>");
		break;
	}
}
?>
