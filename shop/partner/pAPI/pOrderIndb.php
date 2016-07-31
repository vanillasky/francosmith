<?
/*********************************************************
* ���ϸ�     :  pOrderIndb.php
* ���α׷��� :	pad �ֹ�ó�� API
* �ۼ���     :  dn
* ������     :  2011.10.22
**********************************************************/
include "../../lib/library.php";
include "../../conf/config.php";
require_once "../../lib/pAPI.class.php";
require_once "../../lib/json.class.php";

$pAPI = new pAPI();
$json = new Services_JSON(16);

### ����Ű Check (�����δ� ���̵�� ��� ��) ���� ###
if(!$_POST['authentic']) {
	$res_data['code'] = '302';
	$res_data['msg'] = '����Ű�� �����ϴ�.';
	echo ($json->encode($res_data));
	exit;
}

if(!($user_name = $pAPI->keyCheck($_POST['authentic']))) {
	$res_data['code'] = '302';
	$res_data['msg'] = '����Ű�� ���� �ʽ��ϴ�.';
	echo ($json->encode($res_data));
	exit;
}

unset($_POST['authentic']);
### ����Ű Check �� ###

$mode = $_POST['mode'];

### ���ݿ����� Ŭ��������
if (in_array($mode, array('chgAll', 'modOrder', 'order_cancel', 'repay')))
{
	@include "../../lib/cashreceipt.class.php";
	if (class_exists('cashreceipt')) $cashreceipt = new cashreceipt();
}

unset($_POST['mode']);
if(!$mode) {
	$res_data['code'] = '301';
	$res_data['msg'] = '�߸��� ���� �Դϴ�.';
	echo ($json->encode($res_data));
	exit;
}

switch($mode) {

	case 'chkCancel' :	// �����ۺ� �ֹ����
		$ordno = $_POST['ordno'];
		unset($_POST['ordno']);

		$tmp_arr = $_POST;
		$tmp_arr['sno'] = explode('|', $tmp_arr['arr_sno']);
		$tmp_arr['ea'] = explode('|', $tmp_arr['arr_ea']);

		unset($tmp_arr['arr_sno']);
		unset($tmp_arr['arr_ea']);

		### �ֹ����
		chkCancel($ordno, $tmp_arr);

		### �������
		setStock($ordno);
		set_prn_settleprice($ordno);

		### ���ݿ�����(�ڵ����-����)
		if (is_object($cashreceipt)){
			$cashreceipt->autoAction('cancel');
		}

		$res_data['code'] = '000';
		$res_data['msg'] = '����';

		break;

	case 'recovery' :	//�����ۺ� �ֹ�����
		$sno = $_POST['sno'];
		$ord_query = $db->_query_print('SELECT * FROM '.GD_ORDER.' a, '.GD_ORDER_ITEM.' b WHERE a.ordno=b.ordno AND b.sno=[i]', $sno);
		$res_ord = $db->_select($ord_query);
		$row_ord = $res_ord[0];

		$row_ord['goodsnm'] = addslashes($row_ord['goodsnm']);

		// �ֹ����� �ܰ迡�� ��ҵ� �ֹ��Ǹ� ��������� ȯ��
		if ($row_ord['step'] < 1 && $row_ord['step2'] && $row_ord['m_no'] && $row_ord['emoney']){
			$emoney_query = $db->_query_print('SELECT emoney FROM '.GD_MEMBER.' WHERE m_no=[i] LIMIT 1', $row_ord['m_no']);
			$res_emoney = $db->_select($emoney_query);
			$row_emoney = $res_emoney[0];
			$member_emoney = $row_emoney['emoney'];

			if($row_ord['emoney'] > $member_emoney){
				$res_data['code'] = '300';
				$res_data['msg'] = 'ȸ���� ���� ��������, �ֹ��� �������� ���� �ٽ� ����� �����ݺ��� ������ ������ �� �����ϴ�.';
				echo ($json->encode($res_data));
				exit;
			}
		}

		### ������ ���ϴܰ��� �������� �����ϴ��� üũ
		$chk_query = $db->_query_print('SELECT sno FROM '.GD_ORDER_ITEM.' WHERE ordno=[i] AND istep=[i] AND goodsno=[i] AND opt1=[s] AND opt2=[s] AND addopt=[s] AND price=[i]', $row_ord['ordno'], $row_ord['step'], $row_ord['goodsno'], $row_ord['opt1'], $row_ord['opt2'], $row_ord['addopt'], $row_ord['price']);
		$res_chk = $db->_select($chk_query);
		$row_chk = $res_chk[0];
		if($row_chk['sno']) {

			$upd_query = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET ea=ea+[i] WHERE sno=[i]', $row_ord['ea'], $row_chk['sno']);
			$db->query($upd_query);
			$del_query = $db->_query_print('DELETE FROM '.GD_ORDER_ITEM.' WHERE sno=[i]', $row_ord['sno']);
			$db->query($del_query);
		}
		else {
			$upd_query = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET istep=[i], cancel=[i] WHERE sno=[i]', $row_ord['step'], 0, $row_ord['sno']);
			$db->query($upd_query);
		}

		### �ֹ����� ���� ����
		$recovery_query = $db->_query_print('INSERT INTO '.GD_ORDER_CANCEL.' SET ordno=[i], name=[s], regdt=now()', $row_ord['ordno'], $user_name);
		$db->query($recovery_query);
		$no_cancel = $db->_last_insert_id();

		### ��ҹ�ȣ ������
		$max_query = $db->_query_print('SELECT max(cancel)+1 as max_cancel FROM ',GD_ORDER_ITEM.' WHERE cancel > [i]', 0);
		$res_max = $db->_select($max_query);
		$row_max = $res_max[0];
		$max_cancel = $row_max['max_cancel'];

		if ($max_cancel > $no_cancel) {
			$upd_query = $db->_query_print('UPDATE '.GD_ORDER_CANCEL.' SET sno=[i] WHERE sno=[i]', $max_cancel, $no_cancel);
			$db->query($upd_query);
			$no_cancel = $max_cancel;
		}

		### ���(����) �α� ����
		$log_arr = Array();
		$log_arr['ordno'] = $row_ord['ordno'];
		$log_arr['itemno'] = $row_ord['sno'];
		$log_arr['cancel'] = $no_cancel;
		$log_arr['goodsnm'] = $row_ord['goodsnm'];
		$log_arr['ea'] = $row_ord['ea'];

		$log_query = $db->_query_print('INSERT INTO '.GD_LOG_CANCEL.' SET [cv], `prev`=[i], `next`=[i]', $log_arr, $row_ord['istep'], $row_ord['step']);
		$db->query($log_query);

		### ��ü �ֹ��ܰ谡 ��Ҵܰ�� �Ϲ� �ֹ��ܰ�� �ܰ躹��
		if ($row_ord['step2']){
			$upd_query = $db->_query_print('UPDATE '.GD_ORDER.' SET step2=[s] WHERE ordno=[i]', '', $row_ord['ordno']);
			$db->query($upd_query);

			// �ֹ����� �ܰ迡�� ��ҵ� �ֹ��Ǹ� ��������� ����
			if ($row_ord['step'] < 1 && $row_ord['m_no'] && $row_ord['emoney']){
				setEmoney($row_ord['m_no'],-$row_ord['emoney'],"�ֹ��������� ���� ��������� ����",$row_ord['ordno']);
			}
		}

		### �������
		setStock($row_ord['ordno']);
		set_prn_settleprice($row_ord['ordno']);

		if($row_ord['step'] > 3){

			### ��һ�ǰ ���������� ȯ��
			if($row_ord['reserve'] && $row_ord['m_no'] && $row_ord['reserve_status'] == 'CANCEL'){

				$msg = "�ֹ� �������� ���� ���������� ����";
				$reserve = $row_ord['reserve']*$row_ord['ea'];
				$upd_query = $db->_query_print('UPDATE '.GD_MEMBER.' SET emoney=emoney+[i] WHERE m_no=[i]', $reserve, $row_ord['m_no']);
				$db->query($upd_query);

				$emoney_arr = Array();
				$emoney_arr['m_no'] = $row_ord['m_no'];
				$emoney_arr['ordno'] = $row_ord['ordno'];
				$emoney_arr['emoney'] = $reserve;
				$emoney_arr['memo'] = $msg;

				$log_query = $db->_query_print('INSERT INTO '.GD_LOG_EMONEY.' SET [cv], regdt=now()', $emoney_arr);
				$db->query($log_query);
				
				$upd_query = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET `reserve_status` = "NORMAL" WHERE `sno`=[i]', $sno ? $sno : $row_ord['sno']);
				$db->query($upd_query);
			}
		}

		$res_data['code'] = '000';
		$res_data['msg'] = '����';

		break;

		case "modOrder":

		$tmp_arr = Array();

		foreach($_POST as $key=>$val) {
			if(strstr($key, 'arr_')) {
				$tmp_arr[str_replace('arr_', '', $key)] = explode('|', $val);
			}
			else  {
				$tmp_arr[$key] = $val;
			}
		}

		$ordno = $tmp_arr['ordno'];

		### ������ ���� �α� ����
		$item_select_query = $db->_query_print('SELECT * FROM '.GD_ORDER_ITEM.' WHERE ordno=[i] ORDER BY sno', $ordno);
		$res_item_select = $db->_select($item_select_query);

		if(!empty($res_item_select) && is_array($res_item_select)) {
			$i = 0;
			foreach($res_item_select as $row_item) {
				$log = Array();
				if($row_item['ea'] != $tmp_arr['ea'][$i]){
					$log[] = "����  ���� : " . $row_item['ea'] . "�� -> " . $tmp_arr['ea'][$i]."��";
				}
				if($row_item['price'] != $tmp_arr['price'][$i]){
					$log[] = "����  ���� : " . number_format($row_item['price']) . "��->" . number_format($tmp_arr['price'][$i])."��";
				}
				if($row_item['supply'] != $tmp_arr['supply'][$i]){
					$log[] = "���ް����� : " . number_format($row_item['supply']) . "�� -> " . number_format($tmp_arr['supply'][$i])."��";
				}
				if(!empty($log)){
					$arr_ins = Array();
					$arr_ins['ordno'] = $row_item['ordno'];
					$arr_ins['item_sno'] = $row_item['sno'];
					$arr_ins['goodsnm'] = $row_item['goodsnm'];
					$arr_ins['log'] = @implode('\n',$log);

					$ins_log_query = $db->_query_print('INSERT INTO '.GD_ORDER_ITEM_LOG.' SET [cv], regdt=now()', $arr_ins);
					$db->query($ins_log_query);

					unset($arr_ins);
				}
				unset($log);
				$i++;
			}
		}

		### �ֹ������� ���� ����
		foreach ($tmp_arr['sno'] as $k=>$sno){
			### �ֹ���ǰ ���� ����� ��� ����
			$item_query = $db->_query_print('SELECT * FROM '.GD_ORDER_ITEM.' WHERE sno=[i]', $sno);
			$res_item = $db->_select($item_query);
			$row_item = $res_item[0];

			if($tmp_arr['step'] == 0 && ($row_item['ea'] != $tmp_arr['ea'][$k] || $row_item['price'] != $tmp_arr['price'][$k] || $row_item['supply'] != $tmp_arr['supply'][$k] && !$tmp_arr['step2'])){
			//�ֹ����������� �ֹ��������¿��� ����

				if ($tmp_arr['ea'][$k]!=$row_item['ea']){
					$imode =  ($row_item['stockyn']=='n') ? 1 : -1;

					$stock_query = $db->_query_print('SELECT stock FROM '.GD_GOODS_OPTION.' WHERE goodsno=[i] AND opt1=[s] AND opt2=[s]', $row_item['goodsno'], $row_item['opt1'], $row_item['opt2']);
					$res_stock = $db->_select($stock_query);
					$cstock = $res_stock[0]['stock'];

					$cstock = $cstock + ( $imode * ( $tmp_arr['ea'][$k]-$row_item['ea'] ) );

					if($cstock < 0) $cstock = 0;

					$opt_upd_query = $db->_query_print('UPDATE '.GD_GOODS_OPTION.' SET stock=[i] WHERE goodsno=[i] AND opt1=[s] AND opt2=[s]', $cstock, $row_item['goodsno'], $row_item['opt1'], $row_item['opt2']);
					$db->query($opt_upd_query);

					### ��ü ��� ����
					$tot_stock_query = $db->_query_print('SELECT totstock FROM '.GD_GOODS.' WHERE goodsno=[i]', $row_item['goodsno']);
					$res_tot_stock = $db->_select($tot_stock_query);
					$totstock = $res_tot_stock[0]['totstock'];

					$totstock = $totstock + ( $imode*( $tmp_arr['ea'][$k]-$row_item['ea'] ) );

					$goods_upd_query = $db->_query_print('UPDATE '.GD_GOODS.' SET totstock=[i] WHERE goodsno=[i].', $totstock, $row_item['goodsno']);
					$db->query($goods_upd_query);
				}

				$gap = $tmp_arr['ea'][$k] - $row_item['ea'];
				if($gap != 0){
					if($row_item['coupon']) $gcoupon += $row_item['coupon'] * $gap;
					if($row_item['coupon_emoney']) $gcoupon_emoney += $row_item['coupon_emoney'] * $gap;
				}

				$arr_upd_item = Array();
				$arr_upd_item['ea'] = $tmp_arr['ea'][$k];
				$arr_upd_item['price'] = $tmp_arr['price'][$k];
				$arr_upd_item['supply'] = $tmp_arr['supply'][$k];
				$arr_upd_item['dvno'] = $tmp_arr['dvno'][$k];
				$arr_upd_item['dvcode'] = $tmp_arr['dvcode'][$k];

				$item_upd_query = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET [cv] WHERE sno=[i]', $arr_upd_item, $sno);
				$db->query($item_upd_query);

				unset($arr_upd_item);
			}
		}

		//settleprice	= goodsprice + delivery - coupon - emoney - memberdc - enuri
		$tmp_arr['deliverycode'] = str_replace('-','',$tmp_arr['deliverycode']);

		$gcoupon_emoney += 0;
		$gcoupon += 0;

		$arr_upd = Array();
		$arr_upd['enuri'] = $tmp_arr['enuri'];
		$arr_upd['zipcode'] = implode('-', $tmp_arr['zipcode']);
		$arr_upd['address'] = $tmp_arr['address'];
		$arr_upd['memo'] = $tmp_arr['memo'];
		$arr_upd['adminmemo'] = $tmp_arr['adminmemo'];
		$arr_upd['bankAccount'] = $tmp_arr['bankAccount'];
		$arr_upd['bankSender'] = $tmp_arr['bankSender'];
		$arr_upd['nameReceiver'] = $tmp_arr['nameReceiver'];
		$arr_upd['phoneReceiver'] = $tmp_arr['phoneReceiver'];
		$arr_upd['mobileReceiver'] = $tmp_arr['mobileReceiver'];
		$arr_upd['deliveryno'] = $tmp_arr['deliveryno'];
		$arr_upd['deliverycode'] = $tmp_arr['deliverycode'];
		$arr_upd['cashreceipt_ectway'] = $tmp_arr['cashreceipt_ectway'];

		$upd_query = $db->_query_print('UPDATE '.GD_ORDER.' SET [cv], coupon=coupon+[i], coupon_emoney=coupon_emoney+[i] WHERE ordno=[i]', $arr_upd, $gcoupon, $gcoupon_emoney, $ordno);
		$db->query($upd_query);

		### �����Ȳ�� ó��
		if (isset($tmp_arr['step'])) ctlStep($ordno, $tmp_arr['step']);

		### �������
		setStock($ordno);
		set_prn_settleprice($ordno);

		### ���ݿ�����(�ڵ��߱�-����)
		if (is_object($cashreceipt)){
			$cashreceipt->autoAction('approval');
		}

		$res_data['code'] = '000';
		$res_data['msg'] = '����';

		break;

	case "chgAllBanking":	// �Աݴ���� -> �Ա�Ȯ��
	case "chgAll":

		$tmp_arr = Array();

		foreach($_POST as $key=>$val) {
			if(strstr($key, 'arr_')) {
				$tmp_arr[str_replace('arr_', '', $key)] = explode('|', $val);
			}
			else  {
				$tmp_arr[$key] = $val;
			}
		}

		$ordno = $tmp_arr['ordno'];

		if (!empty($tmp_arr['chk']) && is_array($tmp_arr['chk'])){
			foreach ($tmp_arr['chk'] as $ordno){
				### �����Ȳ�� ó��
				ctlStep($ordno, $tmp_arr['case'], 'stock');
				setStock($ordno);
				set_prn_settleprice($ordno);
			}
		}

		### ���ݿ�����(�ڵ��߱�-����)
		if (is_object($cashreceipt)){
			$cashreceipt->autoAction('approval');
		}

		$res_data['code'] = '000';
		$res_data['msg'] = '����';

		break;

	case "regoods":

		$tmp_arr = Array();

		foreach($_POST as $key=>$val) {
			if(strstr($key, 'arr_')) {
				$tmp_arr[str_replace('arr_', '', $key)] = explode('|', $val);
			}
			else  {
				$tmp_arr[$key] = $val;
			}
		}

		// ��ǰ�Ϸ�ó��

		foreach ($tmp_arr['chk'] as $v){
			$ord_query = $db->_query_print('SELECT ordno FROM '.GD_ORDER_CANCEL.' WHERE sno=[i]', $v);
			$res_ord = $db->_select($ord_query);
			$ordno = $res_ord[0]['ordno'];

			### �ֹ������� ó��
			$arr_upd = Array();
			$arr_upd['istep'] = 42;
			$arr_upd['dyn'] = 'r';

			$upd_query = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET [cv] WHERE cancel=[i] AND ordno=[i]', $arr_upd, $v, $ordno);
			$db->query($upd_query);
			unset($arr_upd, $upd_query);

			### �ֹ� �ϰ� ó��
			$arr_upd = Array();
			$arr_upd['step2'] = 42;
			$arr_upd['dyn'] = 'r';

			$upd_query = $db->_query_print('UPDATE '.GD_ORDER.' SET [cv] WHERE ordno=[i] AND step2=[i]', $arr_upd, $ordno, 41);
			$db->query($upd_query);
			unset($arr_upd, $upd_query);

			### �������
			setStock($ordno);
		}

		$res_data['code'] = '000';
		$res_data['msg'] = '����';

		break;

	case "exc_ok": //��ȯ�Ϸ�

		foreach($_POST as $key=>$val) {
			if(strstr($key, 'arr_')) {
				$tmp_arr[str_replace('arr_', '', $key)] = explode('|', $val);
			}
			else  {
				$tmp_arr[$key] = $val;
			}
		}

		// ��ȯ�Ϸ�
		foreach ($tmp_arr['chk'] as $v){
			$ord_query = $db->_query_print('SELECT ordno FROM '.GD_ORDER_CANCEL.' WHERE sno=[i]', $v);
			$res_ord = $db->_select($ord_query);
			$ordno = $res_ord[0]['ordno'];

			### �ֹ������� ó��
			$arr_upd = Array();
			$arr_upd['istep'] = 44;
			$arr_upd['dyn'] = 'e';
			$arr_upd['cyn'] = 'e';

			$query = "update ".GD_ORDER_ITEM." set istep=44,dyn='e',cyn='e' where cancel='$v' and ordno='$ordno'";
			$db->query($query);
			unset($arr_upd, $upd_query);

			### �ֹ� �ϰ� ó��
			$arr_upd = Array();
			$arr_upd['step2'] = 44;
			$arr_upd['dyn'] = 'e';
			$arr_upd['cyn'] = 'e';

			$upd_query = $db->_query_print('UPDATE '.GD_ORDER.' SET [cv] WHERE ordno=[i] AND step2=[i]', $arr_upd, $ordno, 41);
			$db->query($upd_query);
			unset($arr_upd, $upd_query);

			### ���ֹ�
			$newOrdno = reorder($ordno,$v);
		}

		$res_data['code'] = '000';
		$res_data['msg'] = '����';

		break;

	case "repay":

		foreach($_POST as $key=>$val) {
			if(strstr($key, 'arr_')) {
				$tmp_arr[str_replace('arr_', '', $key)] = explode('|', $val);
			}
			else  {
				$tmp_arr[$key] = $val;
			}
		}

		foreach ($tmp_arr['chk'] as $v){

			### ȯ�Ҽ����� ȯ�ұݾ�����
			$rprice = $tmp_arr['repay'][$v];
			$rfee = $tmp_arr['repayfee'][$v];
			$remoney = $tmp_arr['remoney'][$v];
			$m_no = $tmp_arr['m_no'][$v];
			$ordno = $tmp_arr['ordno'][$v];
			$bankcode = $tmp_arr['bankcode'][$v];
			$bankaccount = $tmp_arr['bankaccount'][$v];
			$bankuser = $tmp_arr['bankuser'][$v];
			$sno = $tmp_arr['sno'][$v];

			$upd_arr = Array();
			$upd_arr['rprice'] = $rprice;
			$upd_arr['rfee'] = $rfee;
			$upd_arr['remoney'] = $remoney;
			$upd_arr['bankcode'] = $bankcode;
			$upd_arr['bankaccount'] = $bankaccount;
			$upd_arr['bankuser'] = $bankuser;

			$upd_query = $db->_query_print('UPDATE '.GD_ORDER_CANCEL.' SET [cv], ccdt=now() WHERE sno=[i]', $upd_arr, $sno);
			$db->query($upd_query);
			unset($upd_arr);

			### �ֹ������� ó��
			$upd_arr = Array();
			$upd_arr['istep'] = 44;
			$upd_arr['cyn'] = r;

			$upd_query = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET [cv] WHERE cancel=[i] AND ordno=[i]', $upd_arr, $sno, $ordno);
			$db->query($upd_query);
			unset($upd_arr);

			### �ֹ� �ϰ� ó��
			$select_query = $db->_query_print('SELECT ordno FROM '.GD_ORDER_CANCEL.' WHERE sno=[i]', $sno);
			$res_select = $db->_select($select_query);
			$ordno = $res_select[0]['ordno'];

			$upd_arr = Array();
			$upd_arr['step2'] = 44;
			$upd_arr['cyn'] = 'r';

			$upd_query = $db->_query_print('UPDATE '.GD_ORDER.' SET [cv] WHERE ordno=[i] AND (step2=[i] OR step2=[i])', $upd_arr, $ordno, 41, 42);
			$db->query($upd_query);
			unset($upd_arr);

			### �������
			setStock($ordno);

			### ������ ȯ��
			if ($m_no && $remoney) setEmoney($m_no,$remoney,"�ֹ� ȯ�ҷ� ���� ��������� ȯ��",$ordno);

			### sms�߼�
			$select_query = $db->_query_print('SELECT * FROM '.GD_ORDER.' WHERE ordno=[i]', $ordno);
			$res_select = $db->_select($select_query);
			$row_select = $res_select[0];

			$GLOBALS[dataSms] = $row_select;
			sendSmsCase('repay',$row_select['mobileOrder']);

			### ���ݿ�����(�ڵ����-����������)
			if (is_object($cashreceipt)){
				$cashreceipt->autoCancel($ordno);
			}

			$naverNcash = Core::loader('naverNcash');
			if($naverNcash->useyn == 'Y'){
				// Ncash �ŷ� ����� API
				$naverNcash->deal_reapproval($ordno, $sno);
			}
		}

		### ���ݿ�����(�ڵ����-����)
		if (is_object($cashreceipt)){
			$cashreceipt->autoAction('cancel');
		}

		$res_data['code'] = '000';
		$res_data['msg'] = '����';

		break;

	case 'cardCancel' :
		include "../../lib/cardCancel.class.php";
		include "../../lib/cardCancel_social.class.php";

		$tmp_arr = Array();

		foreach($_POST as $key=>$val) {
			if(strstr($key, 'arr_')) {
				$tmp_arr[str_replace('arr_', '', $key)] = explode('|', $val);
			}
			else  {
				$tmp_arr[$key] = $val;
			}
		}

		$_GET['ordno'] = $_POST['ordno'];

		if($_GET[ordno]){
			$todayshop_noti = Core::loader('todayshop_noti');
			$ts_orderdata = $todayshop_noti->getorderinfo($_GET['ordno']);
			if ($ts_orderdata) {
				//	�����̼� �ֹ� ���
				$cancel = new cardCancel_social();
			}
			else {
				// �Ϲ� �ֹ� ���
				$cancel = new cardCancel();
			}
			unset($todayshop_noti, $ts_orderdata);

			if (empty($_GET[sno]) === false) {
				$cancel->no_cancel = $_GET[sno];
			}
			$res = $cancel -> cancel_pg($_GET[ordno]);
			if($res){
				$res_data['code'] = '000';
				$res_data['msg'] = '����';
			} else {
				$res_data['code'] = '300';
				$res_data['msg'] = 'ī����� ��� ����';
			}
		}
		break;

	case "restoreDiscount" :
		$tmp_arr = Array();

		foreach($_POST as $key=>$val) {
			if(strstr($key, 'arr_')) {
				$tmp_arr[str_replace('arr_', '', $key)] = explode('|', $val);
			}
			else  {
				$tmp_arr[$key] = $val;
			}
		}

		$ordno = $tmp_arr['ordno'];

		//���� ����
		restore_coupon($ordno);
		//������ ����
		restore_emoney($ordno);

		$res_data['code'] = '000';
		$res_data['msg'] = '����';
		break;

	case "partDelivery" :
		$tmp_arr = Array();

		foreach($_POST as $key=>$val) {
			if(strstr($key, 'arr_')) {
				$tmp_arr[str_replace('arr_', '', $key)] = explode('|', $val);
			}
			else  {
				$tmp_arr[$key] = $val;
			}
		}

		if($tmp_arr['deliveryno']=='100') {
			$res_data['code'] = '998';
			$res_data['msg'] = '�����Է� ����';
			break;
		}

		$ord_query = $db->_query_print('SELECT * FROM '.GD_ORDER.' WHERE ordno=[i]', $tmp_arr['ordno']);
		$res_ord = $db->_select($ord_query);
		$row_ord = $res_ord[0];

		if($tmp_arr['chkDelDelivery']) {
			foreach($tmp_arr['sno'] as $v){
				## �ֹ� ��ǰ �����ȣ ����
				$upd_query = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET dvno=[i], dvcode=[s] WHERE sno=[i]', 0, '', $v);
				$db->query($upd_query);
			}
		}else{
			### ���ڸ� ������ ��������
			$tmp_arr['deliverycode'] = preg_replace('/[^0-9]+/','',$tmp_arr['deliverycode']);
			foreach($tmp_arr['sno'] as $k=>$v) {
				### order_item�� �����ȣ �Է�
				$upd_query = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET dvno=[i], dvcode=[s] WHERE sno=[i]', $tmp_arr['deliveryno'], $tmp_arr['deliverycode'], $v);
				$db->query($upd_query);
			}
		}

		## order�� �����ȣ �Է�
		if($row_ord['deliverycode']) {
			$cnt_query = $db->_query_print('SELECT count(*) cnt FROM '.GD_ORDER_ITEM.' WHERE dvno=[i] AND dvcode=[s] AND ordno=[i]', $row_ord['deliveryno'], $row_ord['deliverycode'], $tmp_arr['ordno']);
			$row_cnt = $db->_select($cnt_query);
			$cnt = $row_cnt[0]['cnt'];
		}

		if(!$cnt){
			$dv_query = $db->_query_print('SELECT dvno, dvcode FROM '.GD_ORDER_ITEM.' WHERE ordno=[i] AND dvno AND dvcode LIMIT1', $tmp_arr['ordno']);
			$res_dv = $db->_select($dv_query);
			$deliveryno = $res_dv[0]['dvno'];
			$deliverycode = $res_dv[0]['dvcode'];

			$upd_query = $db->_query_print('UDPATE '.GD_ORDER.' SET deliveryno=[i], deliverycode=[s] WHERE ordno=[i]', $deliveryno, $deliverycode, $tmp_arr['ordno']);
			$db->query($upd_query);
		}

		$res_data['code'] = '000';
		$res_data['msg'] = '����';
		break;

	case "delOrder":

		$tmp_arr = Array();

		foreach($_POST as $key=>$val) {
			if(strstr($key, 'arr_')) {
				$tmp_arr[str_replace('arr_', '', $key)] = explode('|', $val);
			}
			else  {
				$tmp_arr[$key] = $val;
			}
		}

		$ordno = $tmp_arr['ordno'];

		$ord_query = $db->_query_print('SELECT * FROM '.GD_ORDER.' WHERE ordno=[i]', $ordno);
		$res_ord = $db->_select($ord_query);
		$row_ord = $res_ord[0];

		### �������Ÿ ����
		$bak_ins_query = $db->_query_print('INSERT INTO '.GD_ORDER_DEL.' [cv], regdt=now()', $row_ord);
		$db->query($bak_ins_query);

		### �ֹ�����Ÿ ����
		$del_query = $db->_query_print('DELETE FROM '.GD_ORDER.' WHERE ordno=[i]', $ordno);
		$db->query($del_query);

		### ���������� ������ �����մϴ�.
		$coupon_query = $db->_query_print('SELECT applysno FROM '.GD_COUPON_ORDER.' WHERE ordno=[i]', $ordno);
		$res_coupon = $db->_select($coupon_query);
		$applysno = $res_coupon[0]['applysno'];

		if($applysno){
			$upd_cp_query = $db->_query_print('UPDATE '.GD_COUPON_APPLY.' SET status=[s] WHERE sno=[i]', 0, $applysno);
			$del_cp_query = $db->_query_print('DELETE FROM '.GD_COUPON_APPLY.' WHERE ordno=[i]', $ordno);
			$db->query($upd_cp_query);
			$db->query($del_cp_query);
		}

		$res_data['code'] = '000';
		$res_data['msg'] = '����';
		break;

	case "faileRcy" :

		$tmp_arr = Array();

		foreach($_POST as $key=>$val) {
			if(strstr($key, 'arr_')) {
				$tmp_arr[str_replace('arr_', '', $key)] = explode('|', $val);
			}
			else  {
				$tmp_arr[$key] = $val;
			}
		}

		$ordno = $tmp_arr['ordno'];

		if(!chk_stock_recovery($ordno)){
			$res_data['code'] = '300';
			$res_data['msg'] = '�ֹ���ǰ�� ��� �����Ͽ� �ֹ��� �����Ͻ� �� �����ϴ�';
			break;
		}
		## ���ó��
		$upd_query = $db->_query_print('UPDATE '.GD_ORDER.' SET step=[i], step2=[i] WHERE ordno=[i]', 0, 0, $ordno);
		$upd_item_query = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET istep=[i]', 0, $ordno);

		ctlStep($ordno,1,1);
		set_prn_settleprice($ordno);

		$res_data['code'] = '000';
		$res_data['msg'] = '����';
		break;
}

## �ֹ� ��ǰ ��� üũ
function chk_stock_recovery($ordno){
	global $db;
	$query = "select a.ea,b.totstock,b.usestock,b.goodsno,c.stock,c.sno from ".GD_ORDER_ITEM." a left join ".GD_GOODS." b on a.goodsno=b.goodsno left join ".GD_GOODS_OPTION." c on a.goodsno=c.goodsno and a.opt1=c.opt1 and a.opt2=c.opt2 where a.ordno='".$ordno."'";
	$res = $db->query($query);
	while($data = $db->fetch($res)){
		if($data['goodsno']&&$data['usestock']=="o"&&($data['totstock']<$data['ea']||($data['sno']&&$data['stock']<$data['ea']))){
			return false;
		}
	}
	return true;
}

echo ($json->encode($res_data));
