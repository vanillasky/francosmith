<?
/*********************************************************
* 파일명     :  pOrderIndb.php
* 프로그램명 :	pad 주문처리 API
* 작성자     :  dn
* 생성일     :  2011.10.22
**********************************************************/
include "../../lib/library.php";
include "../../conf/config.php";
require_once "../../lib/pAPI.class.php";
require_once "../../lib/json.class.php";

$pAPI = new pAPI();
$json = new Services_JSON(16);

### 인증키 Check (실제로는 아이디와 비번 임) 시작 ###
if(!$_POST['authentic']) {
	$res_data['code'] = '302';
	$res_data['msg'] = '인증키가 없습니다.';
	echo ($json->encode($res_data));
	exit;
}

if(!($user_name = $pAPI->keyCheck($_POST['authentic']))) {
	$res_data['code'] = '302';
	$res_data['msg'] = '인증키가 맞지 않습니다.';
	echo ($json->encode($res_data));
	exit;
}

unset($_POST['authentic']);
### 인증키 Check 끝 ###

$mode = $_POST['mode'];

### 현금영수증 클래스선언
if (in_array($mode, array('chgAll', 'modOrder', 'order_cancel', 'repay')))
{
	@include "../../lib/cashreceipt.class.php";
	if (class_exists('cashreceipt')) $cashreceipt = new cashreceipt();
}

unset($_POST['mode']);
if(!$mode) {
	$res_data['code'] = '301';
	$res_data['msg'] = '잘못된 접근 입니다.';
	echo ($json->encode($res_data));
	exit;
}

switch($mode) {

	case 'chkCancel' :	// 아이템별 주문취소
		$ordno = $_POST['ordno'];
		unset($_POST['ordno']);

		$tmp_arr = $_POST;
		$tmp_arr['sno'] = explode('|', $tmp_arr['arr_sno']);
		$tmp_arr['ea'] = explode('|', $tmp_arr['arr_ea']);

		unset($tmp_arr['arr_sno']);
		unset($tmp_arr['arr_ea']);

		### 주문취소
		chkCancel($ordno, $tmp_arr);

		### 재고조정
		setStock($ordno);
		set_prn_settleprice($ordno);

		### 현금영수증(자동취소-실행)
		if (is_object($cashreceipt)){
			$cashreceipt->autoAction('cancel');
		}

		$res_data['code'] = '000';
		$res_data['msg'] = '성공';

		break;

	case 'recovery' :	//아이템별 주문복원
		$sno = $_POST['sno'];
		$ord_query = $db->_query_print('SELECT * FROM '.GD_ORDER.' a, '.GD_ORDER_ITEM.' b WHERE a.ordno=b.ordno AND b.sno=[i]', $sno);
		$res_ord = $db->_select($ord_query);
		$row_ord = $res_ord[0];

		$row_ord['goodsnm'] = addslashes($row_ord['goodsnm']);

		// 주문접수 단계에서 취소된 주문건만 사용적립금 환원
		if ($row_ord['step'] < 1 && $row_ord['step2'] && $row_ord['m_no'] && $row_ord['emoney']){
			$emoney_query = $db->_query_print('SELECT emoney FROM '.GD_MEMBER.' WHERE m_no=[i] LIMIT 1', $row_ord['m_no']);
			$res_emoney = $db->_select($emoney_query);
			$row_emoney = $res_emoney[0];
			$member_emoney = $row_emoney['emoney'];

			if($row_ord['emoney'] > $member_emoney){
				$res_data['code'] = '300';
				$res_data['msg'] = '회원이 가진 적립금이, 주문건 복원으로 인해 다시 사용할 적립금보다 부족해 복원할 수 없습니다.';
				echo ($json->encode($res_data));
				exit;
			}
		}

		### 복원시 동일단계의 아이템이 존재하는지 체크
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

		### 주문복원 정보 저장
		$recovery_query = $db->_query_print('INSERT INTO '.GD_ORDER_CANCEL.' SET ordno=[i], name=[s], regdt=now()', $row_ord['ordno'], $user_name);
		$db->query($recovery_query);
		$no_cancel = $db->_last_insert_id();

		### 취소번호 재정의
		$max_query = $db->_query_print('SELECT max(cancel)+1 as max_cancel FROM ',GD_ORDER_ITEM.' WHERE cancel > [i]', 0);
		$res_max = $db->_select($max_query);
		$row_max = $res_max[0];
		$max_cancel = $row_max['max_cancel'];

		if ($max_cancel > $no_cancel) {
			$upd_query = $db->_query_print('UPDATE '.GD_ORDER_CANCEL.' SET sno=[i] WHERE sno=[i]', $max_cancel, $no_cancel);
			$db->query($upd_query);
			$no_cancel = $max_cancel;
		}

		### 취소(복원) 로그 저장
		$log_arr = Array();
		$log_arr['ordno'] = $row_ord['ordno'];
		$log_arr['itemno'] = $row_ord['sno'];
		$log_arr['cancel'] = $no_cancel;
		$log_arr['goodsnm'] = $row_ord['goodsnm'];
		$log_arr['ea'] = $row_ord['ea'];

		$log_query = $db->_query_print('INSERT INTO '.GD_LOG_CANCEL.' SET [cv], `prev`=[i], `next`=[i]', $log_arr, $row_ord['istep'], $row_ord['step']);
		$db->query($log_query);

		### 전체 주문단계가 취소단계시 일반 주문단계로 단계복원
		if ($row_ord['step2']){
			$upd_query = $db->_query_print('UPDATE '.GD_ORDER.' SET step2=[s] WHERE ordno=[i]', '', $row_ord['ordno']);
			$db->query($upd_query);

			// 주문접수 단계에서 취소된 주문건만 사용적립금 재사용
			if ($row_ord['step'] < 1 && $row_ord['m_no'] && $row_ord['emoney']){
				setEmoney($row_ord['m_no'],-$row_ord['emoney'],"주문복원으로 인한 사용적립금 재사용",$row_ord['ordno']);
			}
		}

		### 재고조정
		setStock($row_ord['ordno']);
		set_prn_settleprice($row_ord['ordno']);

		if($row_ord['step'] > 3){

			### 취소상품 구매적립금 환원
			if($row_ord['reserve'] && $row_ord['m_no'] && $row_ord['reserve_status'] == 'CANCEL'){

				$msg = "주문 복원으로 인해 구매적립금 적립";
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
		$res_data['msg'] = '성공';

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

		### 아이템 수정 로그 저장
		$item_select_query = $db->_query_print('SELECT * FROM '.GD_ORDER_ITEM.' WHERE ordno=[i] ORDER BY sno', $ordno);
		$res_item_select = $db->_select($item_select_query);

		if(!empty($res_item_select) && is_array($res_item_select)) {
			$i = 0;
			foreach($res_item_select as $row_item) {
				$log = Array();
				if($row_item['ea'] != $tmp_arr['ea'][$i]){
					$log[] = "수량  변경 : " . $row_item['ea'] . "개 -> " . $tmp_arr['ea'][$i]."개";
				}
				if($row_item['price'] != $tmp_arr['price'][$i]){
					$log[] = "가격  변경 : " . number_format($row_item['price']) . "원->" . number_format($tmp_arr['price'][$i])."원";
				}
				if($row_item['supply'] != $tmp_arr['supply'][$i]){
					$log[] = "공급가변경 : " . number_format($row_item['supply']) . "원 -> " . number_format($tmp_arr['supply'][$i])."원";
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

		### 주문아이템 내역 수정
		foreach ($tmp_arr['sno'] as $k=>$sno){
			### 주문상품 수량 변경시 재고 수정
			$item_query = $db->_query_print('SELECT * FROM '.GD_ORDER_ITEM.' WHERE sno=[i]', $sno);
			$res_item = $db->_select($item_query);
			$row_item = $res_item[0];

			if($tmp_arr['step'] == 0 && ($row_item['ea'] != $tmp_arr['ea'][$k] || $row_item['price'] != $tmp_arr['price'][$k] || $row_item['supply'] != $tmp_arr['supply'][$k] && !$tmp_arr['step2'])){
			//주문내역변경은 주문접수상태에서 가능

				if ($tmp_arr['ea'][$k]!=$row_item['ea']){
					$imode =  ($row_item['stockyn']=='n') ? 1 : -1;

					$stock_query = $db->_query_print('SELECT stock FROM '.GD_GOODS_OPTION.' WHERE goodsno=[i] AND opt1=[s] AND opt2=[s]', $row_item['goodsno'], $row_item['opt1'], $row_item['opt2']);
					$res_stock = $db->_select($stock_query);
					$cstock = $res_stock[0]['stock'];

					$cstock = $cstock + ( $imode * ( $tmp_arr['ea'][$k]-$row_item['ea'] ) );

					if($cstock < 0) $cstock = 0;

					$opt_upd_query = $db->_query_print('UPDATE '.GD_GOODS_OPTION.' SET stock=[i] WHERE goodsno=[i] AND opt1=[s] AND opt2=[s]', $cstock, $row_item['goodsno'], $row_item['opt1'], $row_item['opt2']);
					$db->query($opt_upd_query);

					### 전체 재고 수정
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

		### 진행상황별 처리
		if (isset($tmp_arr['step'])) ctlStep($ordno, $tmp_arr['step']);

		### 재고조정
		setStock($ordno);
		set_prn_settleprice($ordno);

		### 현금영수증(자동발급-실행)
		if (is_object($cashreceipt)){
			$cashreceipt->autoAction('approval');
		}

		$res_data['code'] = '000';
		$res_data['msg'] = '성공';

		break;

	case "chgAllBanking":	// 입금대기자 -> 입금확인
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
				### 진행상황별 처리
				ctlStep($ordno, $tmp_arr['case'], 'stock');
				setStock($ordno);
				set_prn_settleprice($ordno);
			}
		}

		### 현금영수증(자동발급-실행)
		if (is_object($cashreceipt)){
			$cashreceipt->autoAction('approval');
		}

		$res_data['code'] = '000';
		$res_data['msg'] = '성공';

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

		// 반품완료처리

		foreach ($tmp_arr['chk'] as $v){
			$ord_query = $db->_query_print('SELECT ordno FROM '.GD_ORDER_CANCEL.' WHERE sno=[i]', $v);
			$res_ord = $db->_select($ord_query);
			$ordno = $res_ord[0]['ordno'];

			### 주문아이템 처리
			$arr_upd = Array();
			$arr_upd['istep'] = 42;
			$arr_upd['dyn'] = 'r';

			$upd_query = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET [cv] WHERE cancel=[i] AND ordno=[i]', $arr_upd, $v, $ordno);
			$db->query($upd_query);
			unset($arr_upd, $upd_query);

			### 주문 일괄 처리
			$arr_upd = Array();
			$arr_upd['step2'] = 42;
			$arr_upd['dyn'] = 'r';

			$upd_query = $db->_query_print('UPDATE '.GD_ORDER.' SET [cv] WHERE ordno=[i] AND step2=[i]', $arr_upd, $ordno, 41);
			$db->query($upd_query);
			unset($arr_upd, $upd_query);

			### 재고조정
			setStock($ordno);
		}

		$res_data['code'] = '000';
		$res_data['msg'] = '성공';

		break;

	case "exc_ok": //교환완료

		foreach($_POST as $key=>$val) {
			if(strstr($key, 'arr_')) {
				$tmp_arr[str_replace('arr_', '', $key)] = explode('|', $val);
			}
			else  {
				$tmp_arr[$key] = $val;
			}
		}

		// 교환완료
		foreach ($tmp_arr['chk'] as $v){
			$ord_query = $db->_query_print('SELECT ordno FROM '.GD_ORDER_CANCEL.' WHERE sno=[i]', $v);
			$res_ord = $db->_select($ord_query);
			$ordno = $res_ord[0]['ordno'];

			### 주문아이템 처리
			$arr_upd = Array();
			$arr_upd['istep'] = 44;
			$arr_upd['dyn'] = 'e';
			$arr_upd['cyn'] = 'e';

			$query = "update ".GD_ORDER_ITEM." set istep=44,dyn='e',cyn='e' where cancel='$v' and ordno='$ordno'";
			$db->query($query);
			unset($arr_upd, $upd_query);

			### 주문 일괄 처리
			$arr_upd = Array();
			$arr_upd['step2'] = 44;
			$arr_upd['dyn'] = 'e';
			$arr_upd['cyn'] = 'e';

			$upd_query = $db->_query_print('UPDATE '.GD_ORDER.' SET [cv] WHERE ordno=[i] AND step2=[i]', $arr_upd, $ordno, 41);
			$db->query($upd_query);
			unset($arr_upd, $upd_query);

			### 재주문
			$newOrdno = reorder($ordno,$v);
		}

		$res_data['code'] = '000';
		$res_data['msg'] = '성공';

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

			### 환불수수료 환불금액저장
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

			### 주문아이템 처리
			$upd_arr = Array();
			$upd_arr['istep'] = 44;
			$upd_arr['cyn'] = r;

			$upd_query = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET [cv] WHERE cancel=[i] AND ordno=[i]', $upd_arr, $sno, $ordno);
			$db->query($upd_query);
			unset($upd_arr);

			### 주문 일괄 처리
			$select_query = $db->_query_print('SELECT ordno FROM '.GD_ORDER_CANCEL.' WHERE sno=[i]', $sno);
			$res_select = $db->_select($select_query);
			$ordno = $res_select[0]['ordno'];

			$upd_arr = Array();
			$upd_arr['step2'] = 44;
			$upd_arr['cyn'] = 'r';

			$upd_query = $db->_query_print('UPDATE '.GD_ORDER.' SET [cv] WHERE ordno=[i] AND (step2=[i] OR step2=[i])', $upd_arr, $ordno, 41, 42);
			$db->query($upd_query);
			unset($upd_arr);

			### 재고조정
			setStock($ordno);

			### 적립금 환불
			if ($m_no && $remoney) setEmoney($m_no,$remoney,"주문 환불로 인한 사용적립금 환원",$ordno);

			### sms발송
			$select_query = $db->_query_print('SELECT * FROM '.GD_ORDER.' WHERE ordno=[i]', $ordno);
			$res_select = $db->_select($select_query);
			$row_select = $res_select[0];

			$GLOBALS[dataSms] = $row_select;
			sendSmsCase('repay',$row_select['mobileOrder']);

			### 현금영수증(자동취소-데이터취합)
			if (is_object($cashreceipt)){
				$cashreceipt->autoCancel($ordno);
			}

			$naverNcash = Core::loader('naverNcash');
			if($naverNcash->useyn == 'Y'){
				// Ncash 거래 재승인 API
				$naverNcash->deal_reapproval($ordno, $sno);
			}
		}

		### 현금영수증(자동취소-실행)
		if (is_object($cashreceipt)){
			$cashreceipt->autoAction('cancel');
		}

		$res_data['code'] = '000';
		$res_data['msg'] = '성공';

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
				$res_data['code'] = '000';
				$res_data['msg'] = '성공';
			} else {
				$res_data['code'] = '300';
				$res_data['msg'] = '카드결제 취소 실패';
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

		//쿠폰 복원
		restore_coupon($ordno);
		//적립금 복원
		restore_emoney($ordno);

		$res_data['code'] = '000';
		$res_data['msg'] = '성공';
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
			$res_data['msg'] = '송장입력 실패';
			break;
		}

		$ord_query = $db->_query_print('SELECT * FROM '.GD_ORDER.' WHERE ordno=[i]', $tmp_arr['ordno']);
		$res_ord = $db->_select($ord_query);
		$row_ord = $res_ord[0];

		if($tmp_arr['chkDelDelivery']) {
			foreach($tmp_arr['sno'] as $v){
				## 주문 상품 송장번호 삭제
				$upd_query = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET dvno=[i], dvcode=[s] WHERE sno=[i]', 0, '', $v);
				$db->query($upd_query);
			}
		}else{
			### 숫자를 제외한 문자제거
			$tmp_arr['deliverycode'] = preg_replace('/[^0-9]+/','',$tmp_arr['deliverycode']);
			foreach($tmp_arr['sno'] as $k=>$v) {
				### order_item의 송장번호 입력
				$upd_query = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET dvno=[i], dvcode=[s] WHERE sno=[i]', $tmp_arr['deliveryno'], $tmp_arr['deliverycode'], $v);
				$db->query($upd_query);
			}
		}

		## order의 송장번호 입력
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
		$res_data['msg'] = '성공';
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

		### 백업데이타 저장
		$bak_ins_query = $db->_query_print('INSERT INTO '.GD_ORDER_DEL.' [cv], regdt=now()', $row_ord);
		$db->query($bak_ins_query);

		### 주문데이타 삭제
		$del_query = $db->_query_print('DELETE FROM '.GD_ORDER.' WHERE ordno=[i]', $ordno);
		$db->query($del_query);

		### 쿠폰내역이 있으면 삭제합니다.
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
		$res_data['msg'] = '성공';
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
			$res_data['msg'] = '주문상품의 재고가 부족하여 주문을 복원하실 수 없습니다';
			break;
		}
		## 디비처리
		$upd_query = $db->_query_print('UPDATE '.GD_ORDER.' SET step=[i], step2=[i] WHERE ordno=[i]', 0, 0, $ordno);
		$upd_item_query = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET istep=[i]', 0, $ordno);

		ctlStep($ordno,1,1);
		set_prn_settleprice($ordno);

		$res_data['code'] = '000';
		$res_data['msg'] = '성공';
		break;
}

## 주문 상품 재고 체크
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
