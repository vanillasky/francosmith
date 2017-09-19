<?

include "../lib.php";
include "../../conf/config.php";

$ordno = $_POST[ordno];
$mode  = ($_POST[mode]) ? $_POST[mode] : $_GET[mode];

### ���ݿ����� Ŭ��������
if (in_array($mode, array('chgAll', 'modOrder', 'chkCancel', 'repay', 'integrate_action', 'integrate_multi_action')))
{
	@include "../../lib/cashreceipt.class.php";
	if (class_exists('cashreceipt')) $cashreceipt = new cashreceipt();
}

### order item ���� �α�
function setlogItem($ordno,$arr){
	global $db;
	$res = $db->query("select * from ".GD_ORDER_ITEM." where ordno='$ordno' order by sno");
	$i = 0;
	while($item = $db->fetch($res)){
		unset($log);
		if($item['ea'] != $arr['ea'][$i]){
			$log[] = "����  ���� : " . $item['ea'] . "�� -> " . $arr['ea'][$i]."��";
		}
		if($item['price'] != $arr['price'][$i]){
			$log[] = "����  ���� : " . number_format($item['price']) . "��->" . number_format($arr['price'][$i])."��";
		}
		if($item['supply'] != $arr['supply'][$i]){
			$log[] = "���ް����� : " . number_format($item['supply']) . "�� -> " . number_format($arr['supply'][$i])."��";
		}
		if($log){
			$setqry = "ordno='".$item['ordno']."', item_sno='".$item['sno']."', goodsnm='".$item['goodsnm']."', log='".@implode('\n',$log)."', regdt = now()";
			$db->query("insert into ".GD_ORDER_ITEM_LOG." set ".$setqry);
		}
		$i++;
	}
}

## �ֹ� ��ǰ ��� üũ
function chk_stock_recovery($ordno){
	global $db;
	$query = "select a.ea,b.totstock,b.usestock,b.goodsno,c.stock,c.sno from ".GD_ORDER_ITEM." a left join ".GD_GOODS." b on a.goodsno=b.goodsno left join ".GD_GOODS_OPTION." c on a.goodsno=c.goodsno and a.opt1=c.opt1 and a.opt2=c.opt2 and go_is_deleted <> '1' where a.ordno='".$ordno."'";
	$res = $db->query($query);
	while($data = $db->fetch($res)){
		if($data['goodsno']&&$data['usestock']=="o"&&($data['totstock']<$data['ea']||($data['sno']&&$data['stock']<$data['ea']))){
			return false;
		}
	}
	return true;
}

## �ǵ��ư� ���
if (!empty($_REQUEST['referer'])) {
	$_tmp = parse_url($_SERVER[HTTP_REFERER]);
	$_SERVER[HTTP_REFERER] .= $_tmp['query'] ? '&' : '?';
	$_SERVER[HTTP_REFERER] .= 'referer='.urlencode($_REQUEST['referer']);
}

$integrate_order = Core::loader('integrate_order');
register_shutdown_function(array(&$integrate_order, 'reserveSync'));

$goodsflow = Core::loader('goodsflow_v2', false);

switch ($mode) {



	case "regoods":
		// ���̹�üũ�ƿ� ��ǰ�Ϸ�ó��
		$naverCheckoutAPI = Core::loader('naverCheckoutAPI');
		foreach((array)$_POST['checkoutNo'] as $v) {
			$orderNo = (int)$v;
			$db->query("update gd_navercheckout_order set confirmReturn='y' where orderNo='{$orderNo}'");
			$naverCheckoutAPI->backStock($orderNo);
		}

		include dirname(__FILE__).'/../../lib/integrate_order_processor.model.ipay.class.php';

		// ��ǰ�Ϸ�ó��
		foreach ((array)$_POST['chk'] as $v){
			list($ordno, $pg, $ipay_cartno, $cancel_sno) = $db->fetch("
			SELECT `o`.`ordno`, `o`.`pg`, `o`.`ipay_cartno`, `oc`.`sno` AS `cancel_sno`
			FROM `".GD_ORDER."` AS `o`
			INNER JOIN `".GD_ORDER_CANCEL."` AS `oc`
			ON `o`.`ordno`=`oc`.`ordno`
			WHERE `oc`.`sno`=".$v);

			if($pg=='ipay')
			{
				$res = $db->query("
				SELECT `oi`.`sno`, `oi`.`goodsno`, `oi`.`ipay_ordno`
				FROM `".GD_ORDER_CANCEL."` AS `oc`
				INNER JOIN `".GD_ORDER_ITEM."` AS `oi`
				ON `oc`.`sno`=`oi`.`cancel`
				WHERE `oc`.`sno`=".$v);
				while($row = $db->fetch($res, 1))
				{
					$auctionIpay = new integrate_order_processor_ipay();
					$status = $auctionIpay->GetIpayReceiptStatus($ipay_cartno, $row['goodsno'].'_'.$row['sno']);
					switch($status)
					{
						// �Ǹ��ڿ��� �۱ݿϷ�(������ ���Ű���)���� �ܿ��Լ� ���
						case '990':
							$result = $auctionIpay->DoIpayOrderDecisionCancel($row['ipay_ordno']);
							break;
						// ��ǰó��(���ÿ� ȯ��)
						default:
							$auctionIpay->DoIpayReturnApproval($row['ipay_ordno']);
							break;
					}
				}

				// ��ҵ� �ֹ���ǰ�� ���¸� ��ǰ�Ϸ�, ��ۻ��¸� ��ǰ�Ϸ�, �������¸� ȯ�ҿϷ�� ����
				$query = "UPDATE `".GD_ORDER_ITEM."` SET `istep`=44, `dyn`='r', `cyn`='r' WHERE `cancel`='".$v."' AND `ordno`='".$ordno."'";
				$db->query($query);

				// ��ҵ� �ֹ����� ���¸� ��ǰ�Ϸ�, ��ۻ��¸� ��ǰ�Ϸ�, PG�������¸� �κ���ҷ� ����
				$query = "UPDATE `".GD_ORDER."` SET `step2`=44, `dyn`='r', `pgcancel`='r' WHERE `ordno`='".$ordno."' AND `step2`=41";
				$db->query($query);

				// �ֹ���Ұ��� PG�������¸� �κ���ҷ� �����ϰ� ����Ͻ� �Է�
				$query = "UPDATE `".GD_ORDER_CANCEL."` SET `pgcancel`='r', `ccdt`='".date('Y-m-d H:i:s')."' WHERE `sno`=".$cancel_sno;
				$db->query($query);

				$naverNcash = Core::loader('naverNcash', true);
				$naverNcash->deal_cancel($ordno, $cancel_sno);
			}
			else
			{
				### �ֹ������� ó��
				$query = "update ".GD_ORDER_ITEM." set istep=42,dyn='r' where cancel='$v' and ordno='$ordno'";
				$db->query($query);

				### �ֹ� �ϰ� ó��
				$query = "update ".GD_ORDER." set step2=42,dyn='r' where ordno='$ordno' and step2=41";
				$db->query($query);
			}

			### �������
			setStock($ordno);

		}
		break;

	case "exc_ok": //��ȯ�Ϸ�

		foreach ($_POST[chk] as $v){
			// iPay�ֹ����� ��ȯó���� �ȵǱ� ������ iPay�ֹ����� �ƴ� �ֹ��Ǹ� ���͸�
			list($ordno) = $db->fetch("
			SELECT `o`.`ordno`
			FROM `".GD_ORDER_CANCEL."` AS `oc`
			INNER JOIN `".GD_ORDER."` AS `o`
			ON `oc`.`ordno`=`o`.`ordno`
			WHERE `oc`.`sno`=".$v."
			AND (`o`.`ipay_payno` IS NULL OR `o`.`ipay_payno`<1)
			AND (`o`.`ipay_cartno` IS NULL OR `o`.`ipay_cartno`<1)
			");

			if((int)$ordno>0)
			{
				### �ֹ������� ó��
				$query = "update ".GD_ORDER_ITEM." set istep=44,dyn='e',cyn='e' where cancel='$v' and ordno='$ordno'";
				$db->query($query);

				### �ֹ� �ϰ� ó��
				$query = "update ".GD_ORDER." set step2=44,dyn='e',cyn='e' where ordno='$ordno' and step2=41";
				$db->query($query);

				### ���ֹ�
				$newOrdno = reorder($ordno,$v);
			}
		}
		break;

	case "repay":

		foreach ($_POST[chk] as $v){

			### ȯ�Ҽ����� ȯ�ұݾ�����

			$rprice = $_POST[repay][$v];
			$rfee = $_POST[repayfee][$v];
			$remoney = $_POST[remoney][$v];
			$m_no = $_POST[m_no][$v];
			$ordno = $_POST[ordno][$v];
			$bankcode = $_POST[bankcode][$v];
			$bankaccount = $_POST[bankaccount][$v];
			$bankuser = $_POST[bankuser][$v];
			$sno = $_POST[sno][$v];

			### ������ �ֹ� ����ó��
			$order_cancel_query = 'SELECT c.pgcancel, o.settleInflow FROM '.GD_ORDER_CANCEL.' c LEFT JOIN '.GD_ORDER.' o ON c.ordno=o.ordno WHERE c.sno='.$sno;
			$order_cancel_res = $db->fetch($order_cancel_query, true);

			if($order_cancel_res['settleInflow'] === 'payco' && $order_cancel_res['pgcancel'] === 'n') {
				msg('������ ������� �� �ٽ� �õ��� �ּ���.', -1);
			}

			if($order_cancel_res['settleInflow'] === 'payco' && $remoney > 0) {
				$query = "update ".GD_ORDER_CANCEL." set
							remoney ='$remoney'
							where sno=$sno";
			}
			else {
				$query = "update ".GD_ORDER_CANCEL." set
							rprice='$rprice',
							rfee='$rfee',
							remoney ='$remoney',
							ccdt=now(),
							bankcode='$bankcode',
							bankaccount=HEX(AES_ENCRYPT('".$bankaccount."', '".$ordno."')),
							bankuser='$bankuser' where sno=$sno";
			}

			$db->query($query);

			### �ֹ������� ó��
			$query = "update ".GD_ORDER_ITEM." set istep=44,cyn='r' where cancel='$sno' and ordno='$ordno'";
			$db->query($query);
			### �ֹ� �ϰ� ó��
			list($ordno) = $db->fetch("select ordno from ".GD_ORDER_CANCEL." where sno=$sno");
			$query = "update ".GD_ORDER." set step2=44,cyn='r' where ordno='$ordno' and step2 in (41,42)";
			$db->query($query);

			### �������
			setStock($ordno);

			### ������ ȯ��
			if ($m_no && $remoney) setEmoney($m_no,$remoney,"�ֹ� ȯ�ҷ� ���� ��������� ȯ��",$ordno);

			### sms�߼�
			$query = "select * from ".GD_ORDER." where ordno='$ordno'";
			$pre = $db->fetch($query);
			$GLOBALS[dataSms] = $pre;
			sendSmsCase('repay',$pre[mobileOrder]);

			### ���ݿ�����(�ڵ����-����������)
			if (is_object($cashreceipt)){
				$cashreceipt->autoCancel($ordno);
			}

			$naverNcash = Core::loader('naverNcash', true);
			if($naverNcash->useyn == 'Y'){
				// Ncash �ŷ� ����� API
				$naverNcash->deal_reapproval($ordno, $sno);
			}
		}

		### ���ݿ�����(�ڵ����-����)
		if (is_object($cashreceipt)){
			$cashreceipt->autoAction('cancel');
		}

		break;

	case "recovery":
		$query = "select * from ".GD_ORDER." a,".GD_ORDER_ITEM." b where a.ordno=b.ordno and b.sno='".$_GET[sno]."'";
		$data = $db->fetch($query,1);

		$data[goodsnm] = addslashes($data[goodsnm]);

		// �ֹ����� �ܰ迡�� ��ҵ� �ֹ��Ǹ� ��������� ȯ��
		if ($data['step'] < 1 && $data['step2'] && $data['m_no'] && $data['emoney']){
			list($member_emoney) = $db->fetch("select emoney from ".GD_MEMBER." where m_no='".$data['m_no']."' limit 1");
			if($data[emoney] > $member_emoney){
				msg('ȸ���� ���� ��������, �ֹ��� �������� ���� �ٽ� ����� �����ݺ��� ������ ������ �� �����ϴ�.', -1);
			}
		}

		### ����ó�������� ������ �ƴҰ��
		if($data[istep]!=41 && ($data[istep]!=44 || $data[cyn].$data[dyn]!="nn") ) {
			msg("�߸��� ����Դϴ�. �ߺ��� ����ó���Դϴ�.",-1);
			exit;
		}

		### ������ ���ϴܰ��� �������� �����ϴ��� üũ
		$query = "
		select sno from
			".GD_ORDER_ITEM."
		where
			ordno = '$data[ordno]'
			and istep = '$data[step]'
			and goodsno = '$data[goodsno]'
			and opt1 = '$data[opt1]'
			and opt2 = '$data[opt2]'
			and addopt = '$data[addopt]'
			and price = '$data[price]'
		";
		list ($sno) = $db->fetch($query);

		if ($sno){
			$db->query("update ".GD_ORDER_ITEM." set ea=ea+$data[ea] where sno='$sno'");
			$db->query("delete from ".GD_ORDER_ITEM." where sno='$data[sno]'");
		} else {
			$db->query("update ".GD_ORDER_ITEM." set istep=$data[step],cancel=0 where sno='$data[sno]'");
		}

		### �ֹ����� ���� ����
		$query = "
		insert into ".GD_ORDER_CANCEL." set
			ordno	= '$data[ordno]',
			name	= '{$_COOKIE[member][name]}',
			regdt	= now()
		";
		$db->query($query);
		$no_cancel = $db->lastID();

		### ��ҹ�ȣ ������
		list($max_cancel) = $db->fetch("select max(cancel)+1 from gd_order_item where cancel>0");
		if ($max_cancel > $no_cancel) {
			$db->query("update ".GD_ORDER_CANCEL." set sno='$max_cancel' where sno='$no_cancel'");
			$no_cancel = $max_cancel;
		}

		### ���(����) �α� ����
		$query = "
		insert into ".GD_LOG_CANCEL." set
			ordno	= '$data[ordno]',
			itemno	= '$data[sno]',
			cancel	= '$no_cancel',
			`prev`	= '$data[istep]',
			`next`	= '$data[step]',
			goodsnm	= '$data[goodsnm]',
			ea		= '$data[ea]'
		";
		$db->query($query);


		### ��ü �ֹ��ܰ谡 ��Ҵܰ�� �Ϲ� �ֹ��ܰ�� �ܰ躹��
		if ($data[step2]){
			$query = "update ".GD_ORDER." set step2='' where ordno='$data[ordno]'";
			$db->query($query);

			// �ֹ����� �ܰ迡�� ��ҵ� �ֹ��Ǹ� ��������� ����
			if ($data['step'] < 1 && $data['m_no'] && $data['emoney']){
				setEmoney($data[m_no],-$data[emoney],"�ֹ��������� ���� ��������� ����",$data[ordno]);
			}
		}

		### �������
		setStock($data[ordno]);
		set_prn_settleprice($data[ordno]);

		// ���̹� ���ϸ��� ����
		list($rncash_emoney, $rncash_cash) = $db->fetch("SELECT rncash_emoney, rncash_cash FROM ".GD_ORDER_CANCEL." WHERE sno=".$data['cancel']);
		list($remain_cancel_price) = $db->fetch("SELECT SUM(price) FROM ".GD_ORDER_ITEM." WHERE cancel=".$data['cancel']);
		if($remain_cancel_price>0)
		{
			$recov_ncash_emoney = 0;
			$recov_ncash_cash = 0;
			if($rncash_cash>0)
			{
				if($rncash_cash<$remain_cancel_price)
				{
					$recov_ncash_cash = $rncash_cash;
					$remain_cancel_price -= $rncash_cash;
				}
				else
				{
					$recov_ncash_cash = $remain_cancel_price;
					$remain_cancel_price = 0;
				}
			}
			if($rncash_emoney>0)
			{
				if($rncash_emoney<$remain_cancel_price) $recov_ncash_emoney = $rncash_emoney;
				else $recov_ncash_emoney = $remain_cancel_price;
			}
			if($recov_ncash_emoney || $recov_ncash_cash)
			{
				$db->query("UPDATE ".GD_ORDER." SET ncash_emoney=".($rncash_emoney-$recov_ncash_emoney).", ncash_cash=".($rncash_cash-$recov_ncash_cash)." WHERE ordno=".$data['ordno']);
				$db->query("UPDATE ".GD_ORDER_CANCEL." SET rncash_emoney=".$recov_ncash_emoney.", rncash_cash=".$recov_ncash_cash." WHERE sno=".$data['cancel']);
			}
		}
		else
		{
			if($rncash_emoney || $rncash_cash)
			{
				$db->query("UPDATE ".GD_ORDER." SET ncash_emoney=ncash_emoney+".$rncash_emoney.", ncash_cash=ncash_cash+".$rncash_cash." WHERE ordno=".$data['ordno']);
				$db->query("UPDATE ".GD_ORDER_CANCEL." SET rncash_emoney=0, rncash_cash=0 WHERE sno=".$data['cancel']);
			}
		}

		if($data[step] > 3){

			### ��һ�ǰ ���������� ȯ��

			if(($data['reserve'] || $data['extra_reserve']) && $data['m_no'] && $data['reserve_status'] == 'CANCEL'){

				$msg = "�ֹ� �������� ���� ���������� ����";
				$reserve = ($data['reserve'] + $data['extra_reserve']) * $data['ea'];
				$query = "update ".GD_MEMBER." set emoney = emoney + $reserve where m_no='$data[m_no]'";

				$db->query($query);
				$query = "
				insert into ".GD_LOG_EMONEY." set
					m_no	= '$data[m_no]',
					ordno	= '$data[ordno]',
					emoney	= '$reserve',
					memo	= '$msg',
					regdt	= now()
				";

				$db->query($query);

				$query = 'UPDATE '.GD_ORDER_ITEM.' SET `reserve_status` = "NORMAL" WHERE `sno`='.($sno ? $sno : $data['sno']);
				$db->query($query);
			}
		}

		break;

	case "modOrder":

		$chk_ea = 0;
		foreach($_POST[ea] as $val) {
			if($val == "" || $val < 1)	$chk_ea = 1;
		}

		if($chk_ea) {
			msg('���ż��� 1 �̻� �Է��ؾ� �մϴ�.', -1);
			exit;
		}

		### ������ ���� �α� ����
		setlogItem($ordno,$_POST);

		### �ֹ������� ���� ����
		foreach ($_POST[sno] as $k=>$sno){
			### �ֹ���ǰ ���� ����� ��� ����
			$pre = $db->fetch("select * from ".GD_ORDER_ITEM." where sno = '{$_POST[sno][$k]}'");

			if($_POST[step] == 0 && ($pre[ea] != $_POST[ea][$k] || $pre[price] != $_POST[price][$k] || $pre[supply] != $_POST[supply][$k] && !$_POST[step2])){
			//�ֹ����������� �ֹ��������¿��� ����

				if ($_POST[ea][$k]!=$pre[ea]){
					$imode =  ($pre[stockyn]=="n") ? 1 : -1;

					list($cstock) = $db->fetch("select stock from ".GD_GOODS_OPTION." where goodsno = '$pre[goodsno]'	and opt1 = '$pre[opt1]'	and opt2 = '$pre[opt2]' and go_is_deleted <> '1'");
					$cstock = $cstock +( $imode*( $_POST[ea][$k]-$pre[ea] ) );
					if($cstock < 0) $cstock = 0;

					$query = "
					update ".GD_GOODS_OPTION." set
						stock= '".$cstock."'
					where
						goodsno = '$pre[goodsno]'
						and opt1 = '$pre[opt1]'
						and opt2 = '$pre[opt2]'
					";
					$db->query($query);

					### ��ü ��� ����
					list($totstock) = $db->fetch("select totstock from ".GD_GOODS." where goodsno = '".$pre['goodsno']."'");
					$totstock = $totstock +( $imode*( $_POST[ea][$k]-$pre[ea] ) );
					$query = "update ".GD_GOODS." set totstock='".$totstock."' where goodsno = '".$pre['goodsno']."'";
					$db->query($query);
				}

				$gap = $_POST[ea][$k] - $pre[ea];
				if($gap != 0){
					if($pre[coupon]) $gcoupon += $pre[coupon] * $gap;
					if($pre[coupon_emoney]) $gcoupon_emoney += $pre[coupon_emoney] * $gap;
				}

				$query = "
				update ".GD_ORDER_ITEM." set
					ea			= '{$_POST[ea][$k]}',
					price		= '{$_POST[price][$k]}',
					supply		= '{$_POST[supply][$k]}',
					dvno		= '{$_POST[dvno][$k]}',
					dvcode		= '{$_POST[dvcode][$k]}'
				where
					sno			= '{$_POST[sno][$k]}'
				";
				$db->query($query);
			}
		}

		//settleprice	= goodsprice + delivery - coupon - emoney - memberdc - enuri
		$_POST[deliverycode] = str_replace('-','',$_POST[deliverycode]);

		$gcoupon_emoney += 0;
		$gcoupon += 0;

		$deliveryField = '';
		if ($goodsflow->isGoodsflowOrder($ordno) === false) {
			$deliveryField = 'deliveryno = "'.$_POST['deliveryno'].'", deliverycode = "'.$_POST['deliverycode'].'",';
		}
		$query = "
		update ".GD_ORDER." set
			enuri			= '$_POST[enuri]',
			zipcode			= '".implode("-",$_POST[zipcode])."',
			zonecode		= '$_POST[zonecode]',
			address			= '$_POST[address]',
			road_address	= '$_POST[road_address]',
			memo			= '$_POST[memo]',
			adminmemo		= '$_POST[adminmemo]',
			bankAccount		= '$_POST[bankAccount]',
			bankSender		= '$_POST[bankSender]',
			nameReceiver	= '$_POST[nameReceiver]',
			phoneReceiver	= '$_POST[phoneReceiver]',
			mobileReceiver	= '$_POST[mobileReceiver]',
			".$deliveryField."
			coupon			= coupon  + $gcoupon,
			coupon_emoney	= coupon_emoney  + $gcoupon_emoney,
			cashreceipt_ectway	= '$_POST[cashreceipt_ectway]'
		where
			ordno		= '$ordno'
		";
		$db->query($query);

		### �����Ȳ�� ó��
		if (isset($_POST[step])) ctlStep($ordno,$_POST[step]);

		### �������
		setStock($_POST[ordno]);
		set_prn_settleprice($_POST[ordno]);

		### ���ݿ�����(�ڵ��߱�-����)
		if (is_object($cashreceipt)){
			$cashreceipt->autoAction('approval');
		}
		break;

	case "chgAllBanking":	// �Աݴ���� -> �Ա�Ȯ��
	case "chgAll":

		if ($_POST[chk]){ foreach ($_POST[chk] as $ordno){

			### �����Ȳ�� ó��
			ctlStep($ordno,$_POST['case'],'stock');
			setStock($ordno);
			set_prn_settleprice($ordno);

		}}

		### ���ݿ�����(�ڵ��߱�-����)
		if (is_object($cashreceipt)){
			$cashreceipt->autoAction('approval');
		}

		if ($mode == 'chgAllBanking') {
			echo '
			<script>
				if (confirm("������ �ֹ����� �Ա�Ȯ�� ó�� �Ǿ����ϴ�.\n\n�ֹ� ����Ʈ���� Ȯ�� �Ͻ� �� �ֽ��ϴ�.\n\n�ֹ�����Ʈ�� �̵��Ͻðڽ��ϱ�?")) {
					location.href = "./list.php?mode=group&skey=all&sgkey=goodsnm&step[]=1&dtkind=orddt&settlekind=a";
				}
				else {
					location.href = "'.$_SERVER[HTTP_REFERER].'";
				}
			</script>
			';

			exit;
		}

		break;

	case "chkCancel":

		### �ֹ����
		chkCancel($ordno,$_POST);

		### �������
		setStock($ordno);
		set_prn_settleprice($ordno);

		### ���ݿ�����(�ڵ����-����)
		if (is_object($cashreceipt)){
			$cashreceipt->autoAction('cancel');
		}

		echo "<script>parent.location.reload();</script>";
		exit;
		break;

	case "delOrder":

		$difference = array_diff(
			$db->desc(GD_ORDER),
			$db->desc(GD_ORDER_DEL)
		);

		foreach($difference as $column) {
			$row = $db->fetch(sprintf("SHOW FULL COLUMNS FROM `%s` LIKE '%s'", GD_ORDER, $column), 1);
			$sql = sprintf("ALTER TABLE `%s` ADD COLUMN `%s` %s %s", GD_ORDER_DEL, $row['Field'], $row['Type'], ($row['Null'] == 'NO' ? 'NOT NULL' : 'NULL'));
			$db->query($sql);
		}

		$data = $db->fetch("select * from ".GD_ORDER." where ordno='$_GET[ordno]'",1);

		### �������Ÿ ����
		foreach ($data as $k=>$v) $tmp[] = "`$k`='".addslashes($v)."'";
		$tmp = implode(",",$tmp);
		$query = "insert into ".GD_ORDER_DEL." set $tmp,regdt=now()";

		$db->query($query);


		### �ֹ�����Ÿ ����
		$query = "delete from ".GD_ORDER." where ordno='$_GET[ordno]'";
		$db->query($query);

		### ���� �ֹ�����Ÿ ����
		$db->query("delete from ".GD_INTEGRATE_ORDER." where ordno='$_GET[ordno]' AND channel = 'enamoo'");
		$db->query("delete from ".GD_INTEGRATE_ORDER_ITEM." where ordno='$_GET[ordno]' AND channel = 'enamoo'");

		### ���������� ������ �����մϴ�.
		list($applysno) = $db->fetch("select applysno  from ".GD_COUPON_ORDER." where ordno='$_GET[ordno]'");
		if($applysno){
			$db->query("update ".GD_COUPON_APPLY." set status = '0' where sno='$applysno'");
			$db->query("delete from ".GD_COUPON_ORDER." where ordno='$_GET[ordno]'");
		}

		msg("�����Ͻ� �ֹ��� ���������� �����Ǿ����ϴ�");
		if ($_GET[popup]){
			echo "<script>opener.location.reload();window.close();</script>";
			exit;
		}

		go($_GET[returnUrl]);
		break;

	case "partDelivery" :
		if($_POST['deliveryno']=='100') {
			break;
		}

		if ($goodsflow->isGoodsflowOrder($ordno)) {
			msg('�̹� �½��÷η� ���� �����ȣ�� �߱޵Ǿ�\r\n�����ȣ�� �����Ͻ� �� �����ϴ�.', -1);
			exit;
		}

		include dirname(__FILE__).'/../../lib/integrate_order_processor.model.ipay.class.php';
		$ipayOrderSet = array();
		$res = $db->query("SELECT `sno`, `dvcode`, `istep`, `ipay_ordno` FROM `".GD_ORDER_ITEM."` WHERE `ordno`=".$_POST['ordno']." AND `istep`>2 AND LENGTH(`dvcode`)>0 AND `ipay_ordno`>0 AND LENGTH(`ipay_itemno`)>0");
		while($row = $db->fetch($res)) $ipayOrderSet[$row['sno']] = $row;
		foreach($_POST['sno'] as $sno)
		{
			$auctionIpay = new integrate_order_processor_ipay();
			$auctionIpay->IpayChangeShippingType($ipayOrderSet[$sno]['ipay_ordno'], $_POST['deliveryno'], $_POST['deliverycode']);
		}

		$data = $db->fetch("select * from ".GD_ORDER." where ordno='$_POST[ordno]'");
		if($_POST['chkDelDelivery']) {
			foreach($_POST[sno] as $v){
				## �ֹ� ��ǰ �����ȣ ����
				$db->query("update ".GD_ORDER_ITEM." set dvno='',dvcode=''  where sno='$v'");
			}
		}else{
			### ���ڸ� ������ ��������
			$_POST['deliverycode'] = str_replace('-','',$_POST['deliverycode']);
			foreach($_POST[sno] as $k=>$v) {
				### order_item�� �����ȣ �Է�
				$db->query("update ".GD_ORDER_ITEM." set dvno='".$_POST['deliveryno']."',dvcode='".$_POST['deliverycode']."' where sno='$v'");
			}
		}

		## order�� �����ȣ �Է�
		if($data[deliverycode]) {
			list($cnt) = $db->fetch("select count(*) from ".GD_ORDER_ITEM." where dvno='".$data['deliveryno']."' and dvcode='".$data['deliverycode']."' and ordno='".$_POST[ordno]."'");
		}
		if(!$cnt){
			list($deliveryno,$deliverycode) = $db->fetch("select dvno,dvcode from ".GD_ORDER_ITEM." where ordno='".$_POST['ordno']."' and dvno and dvcode!='' limit 1");
			$db->query("update ".GD_ORDER." set deliveryno='$deliveryno',deliverycode='$deliverycode' where ordno='".$_POST['ordno']."'");
		}

		// �ֹ����°� ����� �̻��϶� �������� ����Ǹ� ��ۺ��� APIȣ��
		if((int)$data['step']>2)
		{
			$naverNcash = Core::loader('naverNcash', true);
			if($naverNcash->useyn=='Y')
			{
				$naverNcash->delivery_invoice($_POST['ordno']);
			}
		}

		echo "<script>parent.location.reload();</script>";
		break;

	case "faileRcy" :
		if(!chk_stock_recovery($_GET[ordno])){
			msg('�ֹ���ǰ�� ��� �����Ͽ� �ֹ��� �����Ͻ� �� �����ϴ�.',-1);
			exit;
		}
		## ���ó��
		$db->query("update ".GD_ORDER." set step='',step2='0' where ordno='".$_GET['ordno']."'");
		$db->query("update ".GD_ORDER_ITEM." set istep='0' where ordno='".$_GET['ordno']."'");
		// ��� ������ ó��
		$strSQL = "SELECT emoney, m_no FROM ".GD_ORDER." WHERE ordno = '".$_GET['ordno']."'";
		$ordData= $db->fetch($strSQL);
		if ($ordData['emoney'] > 0) {
			setEmoney($ordData['m_no'],-$ordData['emoney'],"��ǰ���Խ� ������ ���� ���",$_GET['ordno']);
		}
		ctlStep($_GET[ordno],1,1);
		set_prn_settleprice($_GET[ordno]);

		break;

	case "requestSMS" :	// �Աݿ�û SMS �߼�

		$arOrderNo = isset($_POST['chk']) ? $_POST['chk'] : false;

		if (! is_array($arOrderNo)) break;

		if (isset($arOrderNo['enamoo'])) $arOrderNo = $arOrderNo['enamoo'];	// ���ո���Ʈ������ �̳��� �ֹ��Ǹ� ó�� �ϱ� ���� �ɷ���.

		$in_orderno = preg_replace("/,$/","",implode(",",$arOrderNo));

		// �ֹ�����
		$query = "

			SELECT
				A.nameOrder, A.mobileOrder, A.phoneOrder, A.settleprice,
				B.bank, B.account, B.name

			FROM ".GD_ORDER." AS A
			INNER JOIN ".GD_LIST_BANK." AS B
			ON A.bankAccount  = B.sno
			WHERE A.ordno IN ($in_orderno) /* AND A.step=0 AND A.settlekind = 'a' */

		";

		$rs = $db->query($query);


		$pattern = "/^([0]{1}[1]{1}[0-9]{1})-?([1-9]{1}[0-9]{2,3})-?([0-9]{4})$/";	// 01x-xxx(x)-xxxx

		$cnt = 0;

		while ($row = mysql_fetch_assoc($rs)) {

			// ��ȭ��ȣ üũ.. ����
			if (preg_match($pattern,$row['mobileOrder'])) {
				$mobile = $row['mobileOrder'];
			}
			elseif (preg_match($pattern,$row['phoneOrder'])) {
				$mobile = $row['phoneOrder'];
			}
			else {
				continue;
			}

			$dataSms['account']		= $row['bank']." ".$row['account']." ".$row['name'];
			$dataSms['nameOrder']	= $row['nameOrder'];
			$dataSms['settleprice']	= number_format($row['settleprice']);

			$GLOBALS['dataSms']		= $dataSms;

			sendSmsCase('account',$mobile);	// /shop/lib/lib.func.php �� sendSmsCase �Լ� ����Ͽ� SMS �߼�.

			$cnt++;

		}	// while--

		echo "<script>alert('".$cnt." / ".mysql_num_rows($rs)."�� �߼� �Ǿ����ϴ�.');</script>";

		break;

	case "restoreDiscount" :
		//���� ����
		restore_coupon($_POST[ordno]);
		//������ ����
		restore_emoney($_POST[ordno]);
		break;

	case 'integrate_action' :

		$extra = array();

		foreach ($_POST as $k => $v) {
			if (in_array($k,array('mode','ord_status','ordno','channel','x','y'))) continue;
			$extra[$k] = $v;
		}

		$integrate_order -> setOrder($_POST['channel'], $_POST['ordno'], $_POST['ord_status'],$extra);

		// �̳��� �� ��쿡�� ���ݿ����� ����
		if ($channel == 'enamoo' && is_object($cashreceipt)) {
			### ���ݿ�����(�ڵ��߱�-����)
			$cashreceipt->autoAction('approval');

		}
		header('location:'.$_SERVER[HTTP_REFERER]);
		exit;
		break;

	case 'integrate_multi_action' :


		if ($_POST['chk']){ foreach ($_POST['chk'] as $channel => $ordnos) {
			settype($ordnos, "array");

			foreach ($ordnos as $ordno) {

				$extra = array();
				foreach ($_POST as $k => $v) {
					if (in_array($k,array('mode','ord_status','ordno','channel','x','y'))) continue;
					$extra[$k] = $_POST[$k][$channel][$ordno];
				}

				$integrate_order -> setOrder($channel, $ordno, $_POST['ord_status'],$extra);
			}

			// �̳��� �� ��쿡�� ���ݿ����� ����
			if ($channel == 'enamoo' && is_object($cashreceipt)) {
				### ���ݿ�����(�ڵ��߱�-����)
				$cashreceipt->autoAction('approval');

			}
		}}
		header('location:'.$_SERVER[HTTP_REFERER]);
		exit;
		break;
}
//$db->viewLog();
go($_SERVER[HTTP_REFERER]);
?>