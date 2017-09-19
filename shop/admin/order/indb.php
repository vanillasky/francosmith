<?

include "../lib.php";
include "../../conf/config.php";

$ordno = $_POST[ordno];
$mode  = ($_POST[mode]) ? $_POST[mode] : $_GET[mode];

### 현금영수증 클래스선언
if (in_array($mode, array('chgAll', 'modOrder', 'chkCancel', 'repay', 'integrate_action', 'integrate_multi_action')))
{
	@include "../../lib/cashreceipt.class.php";
	if (class_exists('cashreceipt')) $cashreceipt = new cashreceipt();
}

### order item 변경 로그
function setlogItem($ordno,$arr){
	global $db;
	$res = $db->query("select * from ".GD_ORDER_ITEM." where ordno='$ordno' order by sno");
	$i = 0;
	while($item = $db->fetch($res)){
		unset($log);
		if($item['ea'] != $arr['ea'][$i]){
			$log[] = "수량  변경 : " . $item['ea'] . "개 -> " . $arr['ea'][$i]."개";
		}
		if($item['price'] != $arr['price'][$i]){
			$log[] = "가격  변경 : " . number_format($item['price']) . "원->" . number_format($arr['price'][$i])."원";
		}
		if($item['supply'] != $arr['supply'][$i]){
			$log[] = "공급가변경 : " . number_format($item['supply']) . "원 -> " . number_format($arr['supply'][$i])."원";
		}
		if($log){
			$setqry = "ordno='".$item['ordno']."', item_sno='".$item['sno']."', goodsnm='".$item['goodsnm']."', log='".@implode('\n',$log)."', regdt = now()";
			$db->query("insert into ".GD_ORDER_ITEM_LOG." set ".$setqry);
		}
		$i++;
	}
}

## 주문 상품 재고 체크
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

## 되돌아갈 경로
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
		// 네이버체크아웃 반품완료처리
		$naverCheckoutAPI = Core::loader('naverCheckoutAPI');
		foreach((array)$_POST['checkoutNo'] as $v) {
			$orderNo = (int)$v;
			$db->query("update gd_navercheckout_order set confirmReturn='y' where orderNo='{$orderNo}'");
			$naverCheckoutAPI->backStock($orderNo);
		}

		include dirname(__FILE__).'/../../lib/integrate_order_processor.model.ipay.class.php';

		// 반품완료처리
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
						// 판매자에게 송금완료(구매자 구매결정)이후 단예게서 취소
						case '990':
							$result = $auctionIpay->DoIpayOrderDecisionCancel($row['ipay_ordno']);
							break;
						// 반품처리(동시에 환불)
						default:
							$auctionIpay->DoIpayReturnApproval($row['ipay_ordno']);
							break;
					}
				}

				// 취소된 주문상품의 상태를 반품완료, 배송상태를 반품완료, 결제상태를 환불완료로 수정
				$query = "UPDATE `".GD_ORDER_ITEM."` SET `istep`=44, `dyn`='r', `cyn`='r' WHERE `cancel`='".$v."' AND `ordno`='".$ordno."'";
				$db->query($query);

				// 취소된 주문건의 상태를 반품완료, 배송상태를 반품완료, PG결제상태를 부분취소로 변경
				$query = "UPDATE `".GD_ORDER."` SET `step2`=44, `dyn`='r', `pgcancel`='r' WHERE `ordno`='".$ordno."' AND `step2`=41";
				$db->query($query);

				// 주문취소건의 PG결제상태를 부분취소로 변경하고 취소일시 입력
				$query = "UPDATE `".GD_ORDER_CANCEL."` SET `pgcancel`='r', `ccdt`='".date('Y-m-d H:i:s')."' WHERE `sno`=".$cancel_sno;
				$db->query($query);

				$naverNcash = Core::loader('naverNcash', true);
				$naverNcash->deal_cancel($ordno, $cancel_sno);
			}
			else
			{
				### 주문아이템 처리
				$query = "update ".GD_ORDER_ITEM." set istep=42,dyn='r' where cancel='$v' and ordno='$ordno'";
				$db->query($query);

				### 주문 일괄 처리
				$query = "update ".GD_ORDER." set step2=42,dyn='r' where ordno='$ordno' and step2=41";
				$db->query($query);
			}

			### 재고조정
			setStock($ordno);

		}
		break;

	case "exc_ok": //교환완료

		foreach ($_POST[chk] as $v){
			// iPay주문건은 교환처리가 안되기 때문에 iPay주문건이 아닌 주문건만 필터링
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
				### 주문아이템 처리
				$query = "update ".GD_ORDER_ITEM." set istep=44,dyn='e',cyn='e' where cancel='$v' and ordno='$ordno'";
				$db->query($query);

				### 주문 일괄 처리
				$query = "update ".GD_ORDER." set step2=44,dyn='e',cyn='e' where ordno='$ordno' and step2=41";
				$db->query($query);

				### 재주문
				$newOrdno = reorder($ordno,$v);
			}
		}
		break;

	case "repay":

		foreach ($_POST[chk] as $v){

			### 환불수수료 환불금액저장

			$rprice = $_POST[repay][$v];
			$rfee = $_POST[repayfee][$v];
			$remoney = $_POST[remoney][$v];
			$m_no = $_POST[m_no][$v];
			$ordno = $_POST[ordno][$v];
			$bankcode = $_POST[bankcode][$v];
			$bankaccount = $_POST[bankaccount][$v];
			$bankuser = $_POST[bankuser][$v];
			$sno = $_POST[sno][$v];

			### 페이코 주문 예외처리
			$order_cancel_query = 'SELECT c.pgcancel, o.settleInflow FROM '.GD_ORDER_CANCEL.' c LEFT JOIN '.GD_ORDER.' o ON c.ordno=o.ordno WHERE c.sno='.$sno;
			$order_cancel_res = $db->fetch($order_cancel_query, true);

			if($order_cancel_res['settleInflow'] === 'payco' && $order_cancel_res['pgcancel'] === 'n') {
				msg('페이코 결제취소 후 다시 시도해 주세요.', -1);
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

			### 주문아이템 처리
			$query = "update ".GD_ORDER_ITEM." set istep=44,cyn='r' where cancel='$sno' and ordno='$ordno'";
			$db->query($query);
			### 주문 일괄 처리
			list($ordno) = $db->fetch("select ordno from ".GD_ORDER_CANCEL." where sno=$sno");
			$query = "update ".GD_ORDER." set step2=44,cyn='r' where ordno='$ordno' and step2 in (41,42)";
			$db->query($query);

			### 재고조정
			setStock($ordno);

			### 적립금 환불
			if ($m_no && $remoney) setEmoney($m_no,$remoney,"주문 환불로 인한 사용적립금 환원",$ordno);

			### sms발송
			$query = "select * from ".GD_ORDER." where ordno='$ordno'";
			$pre = $db->fetch($query);
			$GLOBALS[dataSms] = $pre;
			sendSmsCase('repay',$pre[mobileOrder]);

			### 현금영수증(자동취소-데이터취합)
			if (is_object($cashreceipt)){
				$cashreceipt->autoCancel($ordno);
			}

			$naverNcash = Core::loader('naverNcash', true);
			if($naverNcash->useyn == 'Y'){
				// Ncash 거래 재승인 API
				$naverNcash->deal_reapproval($ordno, $sno);
			}
		}

		### 현금영수증(자동취소-실행)
		if (is_object($cashreceipt)){
			$cashreceipt->autoAction('cancel');
		}

		break;

	case "recovery":
		$query = "select * from ".GD_ORDER." a,".GD_ORDER_ITEM." b where a.ordno=b.ordno and b.sno='".$_GET[sno]."'";
		$data = $db->fetch($query,1);

		$data[goodsnm] = addslashes($data[goodsnm]);

		// 주문접수 단계에서 취소된 주문건만 사용적립금 환원
		if ($data['step'] < 1 && $data['step2'] && $data['m_no'] && $data['emoney']){
			list($member_emoney) = $db->fetch("select emoney from ".GD_MEMBER." where m_no='".$data['m_no']."' limit 1");
			if($data[emoney] > $member_emoney){
				msg('회원이 가진 적립금이, 주문건 복원으로 인해 다시 사용할 적립금보다 부족해 복원할 수 없습니다.', -1);
			}
		}

		### 복원처리가능한 조건이 아닐경우
		if($data[istep]!=41 && ($data[istep]!=44 || $data[cyn].$data[dyn]!="nn") ) {
			msg("잘못된 경로입니다. 중복된 복원처리입니다.",-1);
			exit;
		}

		### 복원시 동일단계의 아이템이 존재하는지 체크
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

		### 주문복원 정보 저장
		$query = "
		insert into ".GD_ORDER_CANCEL." set
			ordno	= '$data[ordno]',
			name	= '{$_COOKIE[member][name]}',
			regdt	= now()
		";
		$db->query($query);
		$no_cancel = $db->lastID();

		### 취소번호 재정의
		list($max_cancel) = $db->fetch("select max(cancel)+1 from gd_order_item where cancel>0");
		if ($max_cancel > $no_cancel) {
			$db->query("update ".GD_ORDER_CANCEL." set sno='$max_cancel' where sno='$no_cancel'");
			$no_cancel = $max_cancel;
		}

		### 취소(복원) 로그 저장
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


		### 전체 주문단계가 취소단계시 일반 주문단계로 단계복원
		if ($data[step2]){
			$query = "update ".GD_ORDER." set step2='' where ordno='$data[ordno]'";
			$db->query($query);

			// 주문접수 단계에서 취소된 주문건만 사용적립금 재사용
			if ($data['step'] < 1 && $data['m_no'] && $data['emoney']){
				setEmoney($data[m_no],-$data[emoney],"주문복원으로 인한 사용적립금 재사용",$data[ordno]);
			}
		}

		### 재고조정
		setStock($data[ordno]);
		set_prn_settleprice($data[ordno]);

		// 네이버 마일리지 복원
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

			### 취소상품 구매적립금 환원

			if(($data['reserve'] || $data['extra_reserve']) && $data['m_no'] && $data['reserve_status'] == 'CANCEL'){

				$msg = "주문 복원으로 인해 구매적립금 적립";
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
			msg('구매수량 1 이상 입력해야 합니다.', -1);
			exit;
		}

		### 아이템 수정 로그 저장
		setlogItem($ordno,$_POST);

		### 주문아이템 내역 수정
		foreach ($_POST[sno] as $k=>$sno){
			### 주문상품 수량 변경시 재고 수정
			$pre = $db->fetch("select * from ".GD_ORDER_ITEM." where sno = '{$_POST[sno][$k]}'");

			if($_POST[step] == 0 && ($pre[ea] != $_POST[ea][$k] || $pre[price] != $_POST[price][$k] || $pre[supply] != $_POST[supply][$k] && !$_POST[step2])){
			//주문내역변경은 주문접수상태에서 가능

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

					### 전체 재고 수정
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

		### 진행상황별 처리
		if (isset($_POST[step])) ctlStep($ordno,$_POST[step]);

		### 재고조정
		setStock($_POST[ordno]);
		set_prn_settleprice($_POST[ordno]);

		### 현금영수증(자동발급-실행)
		if (is_object($cashreceipt)){
			$cashreceipt->autoAction('approval');
		}
		break;

	case "chgAllBanking":	// 입금대기자 -> 입금확인
	case "chgAll":

		if ($_POST[chk]){ foreach ($_POST[chk] as $ordno){

			### 진행상황별 처리
			ctlStep($ordno,$_POST['case'],'stock');
			setStock($ordno);
			set_prn_settleprice($ordno);

		}}

		### 현금영수증(자동발급-실행)
		if (is_object($cashreceipt)){
			$cashreceipt->autoAction('approval');
		}

		if ($mode == 'chgAllBanking') {
			echo '
			<script>
				if (confirm("선택한 주문건이 입금확인 처리 되었습니다.\n\n주문 리스트에서 확인 하실 수 있습니다.\n\n주문리스트로 이동하시겠습니까?")) {
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

		### 주문취소
		chkCancel($ordno,$_POST);

		### 재고조정
		setStock($ordno);
		set_prn_settleprice($ordno);

		### 현금영수증(자동취소-실행)
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

		### 백업데이타 저장
		foreach ($data as $k=>$v) $tmp[] = "`$k`='".addslashes($v)."'";
		$tmp = implode(",",$tmp);
		$query = "insert into ".GD_ORDER_DEL." set $tmp,regdt=now()";

		$db->query($query);


		### 주문데이타 삭제
		$query = "delete from ".GD_ORDER." where ordno='$_GET[ordno]'";
		$db->query($query);

		### 통합 주문데이타 삭제
		$db->query("delete from ".GD_INTEGRATE_ORDER." where ordno='$_GET[ordno]' AND channel = 'enamoo'");
		$db->query("delete from ".GD_INTEGRATE_ORDER_ITEM." where ordno='$_GET[ordno]' AND channel = 'enamoo'");

		### 쿠폰내역이 있으면 삭제합니다.
		list($applysno) = $db->fetch("select applysno  from ".GD_COUPON_ORDER." where ordno='$_GET[ordno]'");
		if($applysno){
			$db->query("update ".GD_COUPON_APPLY." set status = '0' where sno='$applysno'");
			$db->query("delete from ".GD_COUPON_ORDER." where ordno='$_GET[ordno]'");
		}

		msg("선택하신 주문이 정상적으로 삭제되었습니다");
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
			msg('이미 굿스플로로 부터 송장번호가 발급되어\r\n송장번호를 수정하실 수 없습니다.', -1);
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
				## 주문 상품 송장번호 삭제
				$db->query("update ".GD_ORDER_ITEM." set dvno='',dvcode=''  where sno='$v'");
			}
		}else{
			### 숫자를 제외한 문자제거
			$_POST['deliverycode'] = str_replace('-','',$_POST['deliverycode']);
			foreach($_POST[sno] as $k=>$v) {
				### order_item의 송장번호 입력
				$db->query("update ".GD_ORDER_ITEM." set dvno='".$_POST['deliveryno']."',dvcode='".$_POST['deliverycode']."' where sno='$v'");
			}
		}

		## order의 송장번호 입력
		if($data[deliverycode]) {
			list($cnt) = $db->fetch("select count(*) from ".GD_ORDER_ITEM." where dvno='".$data['deliveryno']."' and dvcode='".$data['deliverycode']."' and ordno='".$_POST[ordno]."'");
		}
		if(!$cnt){
			list($deliveryno,$deliverycode) = $db->fetch("select dvno,dvcode from ".GD_ORDER_ITEM." where ordno='".$_POST['ordno']."' and dvno and dvcode!='' limit 1");
			$db->query("update ".GD_ORDER." set deliveryno='$deliveryno',deliverycode='$deliverycode' where ordno='".$_POST['ordno']."'");
		}

		// 주문상태가 배송중 이상일때 개별송장 변경되면 배송변경 API호출
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
			msg('주문상품의 재고가 부족하여 주문을 복원하실 수 없습니다.',-1);
			exit;
		}
		## 디비처리
		$db->query("update ".GD_ORDER." set step='',step2='0' where ordno='".$_GET['ordno']."'");
		$db->query("update ".GD_ORDER_ITEM." set istep='0' where ordno='".$_GET['ordno']."'");
		// 사용 적립금 처리
		$strSQL = "SELECT emoney, m_no FROM ".GD_ORDER." WHERE ordno = '".$_GET['ordno']."'";
		$ordData= $db->fetch($strSQL);
		if ($ordData['emoney'] > 0) {
			setEmoney($ordData['m_no'],-$ordData['emoney'],"상품구입시 적립금 결제 사용",$_GET['ordno']);
		}
		ctlStep($_GET[ordno],1,1);
		set_prn_settleprice($_GET[ordno]);

		break;

	case "requestSMS" :	// 입금요청 SMS 발송

		$arOrderNo = isset($_POST['chk']) ? $_POST['chk'] : false;

		if (! is_array($arOrderNo)) break;

		if (isset($arOrderNo['enamoo'])) $arOrderNo = $arOrderNo['enamoo'];	// 통합리스트에서는 이나무 주문건만 처리 하기 위해 걸러냄.

		$in_orderno = preg_replace("/,$/","",implode(",",$arOrderNo));

		// 주문정보
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

			// 전화번호 체크.. 등등등
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

			sendSmsCase('account',$mobile);	// /shop/lib/lib.func.php 내 sendSmsCase 함수 사용하여 SMS 발송.

			$cnt++;

		}	// while--

		echo "<script>alert('".$cnt." / ".mysql_num_rows($rs)."건 발송 되었습니다.');</script>";

		break;

	case "restoreDiscount" :
		//쿠폰 복원
		restore_coupon($_POST[ordno]);
		//적립금 복원
		restore_emoney($_POST[ordno]);
		break;

	case 'integrate_action' :

		$extra = array();

		foreach ($_POST as $k => $v) {
			if (in_array($k,array('mode','ord_status','ordno','channel','x','y'))) continue;
			$extra[$k] = $v;
		}

		$integrate_order -> setOrder($_POST['channel'], $_POST['ordno'], $_POST['ord_status'],$extra);

		// 이나무 인 경우에만 현금영수증 발행
		if ($channel == 'enamoo' && is_object($cashreceipt)) {
			### 현금영수증(자동발급-실행)
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

			// 이나무 인 경우에만 현금영수증 발행
			if ($channel == 'enamoo' && is_object($cashreceipt)) {
				### 현금영수증(자동발급-실행)
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