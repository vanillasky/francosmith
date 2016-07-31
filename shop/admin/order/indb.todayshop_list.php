<?

include "../lib.php";
include "../../conf/config.php";

$ordno = $_POST[ordno];
$mode  = ($_POST[mode]) ? $_POST[mode] : $_GET[mode];


### 현금영수증 클래스선언
if (in_array($mode, array('chgAll', 'modOrder', 'chkCancel', 'repay')))
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

switch ($mode){

	case "regoods":

		foreach ($_POST[chk] as $v){
			### 주문아이템 처리
			$query = "update ".GD_ORDER_ITEM." set istep=42,dyn='r' where cancel=$v";
			$db->query($query);

			### 주문 일괄 처리
			list ($ordno) = $db->fetch("select ordno from ".GD_ORDER_CANCEL." where sno=$v");
			$query = "update ".GD_ORDER." set step2=42,dyn='r' where ordno='$ordno' and step2=41";
			$db->query($query);

			### 재고조정
			setStock($ordno);

		}
		break;

	case "exc_ok": //교환완료

		foreach ($_POST[chk] as $v){
			### 주문아이템 처리
			$query = "update ".GD_ORDER_ITEM." set istep=44,dyn='e',cyn='e' where cancel='$v'";

			$db->query($query);

			### 주문 일괄 처리
			list ($ordno) = $db->fetch("select ordno from ".GD_ORDER_CANCEL." where sno=$v");
			$query = "update ".GD_ORDER." set step2=44,dyn='e',cyn='e' where ordno='$ordno' and step2=41";
			$db->query($query);


			### 재주문
			$newOrdno = reorder($ordno,$v);
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

			$query = "update ".GD_ORDER_CANCEL." set
						rprice='$rprice',
						rfee='$rfee',
						remoney ='$remoney',
						ccdt=now(),
						bankcode='$bankcode',
						bankaccount='$bankaccount',
						bankuser='$bankuser' where sno=$sno";


			$db->query($query);

			### 주문아이템 처리
			$query = "update ".GD_ORDER_ITEM." set istep=44,cyn='r' where cancel=$sno";
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

		### 전체 주문단계가 취소단계시 일반 주문단계로 단계복원
		if ($data['step'] < 1 && $data['step2'] && $data['m_no'] && $data['emoney']){
			list($member_emoney) = $db->fetch("select emoney from ".GD_MEMBER." where m_no='".$data['m_no']."' limit 1");
			if($data[emoney] > $member_emoney){
				msg('회원이 가진 적립금이, 주문건 복원으로 인해 다시 사용할 적립금보다 부족해 복원할 수 없습니다.',-1);
			}
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

		if($data[step] > 3){

			### 취소상품 구매적립금 환원

			if($data[reserve] && $data[m_no] && $data['reserve_status'] == 'CANCEL'){

				$msg = "주문 복원으로 인해 구매적립금 적립";
				$reserve = $data['reserve']*$data['ea'];
				$query = "update ".GD_MEMBER." set emoney = emoney + $reserve where m_no='$data[m_no]'";

				$db->query($query);
				$query = "
				insert into ".GD_LOG_EMONEY." set
					m_no	= '$data[m_no]',
					ordno	= '$ordno',
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

		$query = "
		update ".GD_ORDER." set
			enuri			= '$_POST[enuri]',
			zipcode			= '".implode("-",$_POST[zipcode])."',
			address			= '$_POST[address]',
			memo			= '$_POST[memo]',
			adminmemo		= '$_POST[adminmemo]',
			bankAccount		= '$_POST[bankAccount]',
			bankSender		= '$_POST[bankSender]',
			nameReceiver	= '$_POST[nameReceiver]',
			phoneReceiver	= '$_POST[phoneReceiver]',
			mobileReceiver	= '$_POST[mobileReceiver]',
			deliveryno		= '$_POST[deliveryno]',
			deliverycode	= '$_POST[deliverycode]',
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

		if (strpos($_SERVER[HTTP_REFERER],"referer")==false) $_SERVER[HTTP_REFERER] .= "&referer=".urlencode($_POST[referer]);
		go($_SERVER[HTTP_REFERER]."&returnUrl=$_GET[returnUrl]");
		break;
/*
	case "chgAll":

		// 투데이샵 pg 설정값 불러오기
			$todayshop_noti = Core::loader('todayshop_noti');
			$tsCfg = $todayshop_noti->cfg;
			$tsPG = ($tsCfg['pg'] != '') ? unserialize($tsCfg['pg']) : array();

		if ($_POST[chk]){ foreach ($_POST[chk] as $g){
			$g = explode(",",$g);

			foreach($g as $ordno) {
				$tmp = $todayshop_noti->getorderinfo($ordno);
				if ($tmp['step2'] >= 40) continue;
				ctlStep($ordno,$_POST['case'],'stock');
				setStock($ordno);
				set_prn_settleprice($ordno);
			}
		}}

		### 현금영수증(자동발급-실행)
		if (is_object($cashreceipt)){
			$cashreceipt->autoAction('approval');
		}
		break;
*/
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

		$data = $db->fetch("select * from ".GD_ORDER." where ordno='$_GET[ordno]'",1);

		### 백업데이타 저장
		foreach ($data as $k=>$v) $tmp[] = "`$k`='".addslashes($v)."'";
		$tmp = implode(",",$tmp);
		$query = "insert into ".GD_ORDER_DEL." set $tmp,regdt=now()";

		$db->query($query);


		### 주문데이타 삭제
		$query = "delete from ".GD_ORDER." where ordno='$_GET[ordno]'";
		$db->query($query);

		### 쿠폰내역이 있으면 삭제합니다.
		list($cnt) = $db->fetch("select count(*) from ".GD_COUPON_ORDER." where ordno='$_GET[ordno]'");
		if($cnt > 0){
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

		$data = $db->fetch("select * from ".GD_ORDER." where ordno='$_POST[ordno]'");
		if($_POST['chkDelDelivery']) {
			foreach($_POST[sno] as $v){
				## 주문 상품 송장번호 삭제
				$db->query("update ".GD_ORDER_ITEM." set dvno='',dvcode=''  where sno='$v'");
			}
		}else{
			### 숫자를 제외한 문자제거
			$_POST['deliverycode'] = preg_replace('/[^0-9]+/','',$_POST['deliverycode']);
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
			list($deliveryno,$deliverycode) = $db->fetch("select dvno,dvcode from ".GD_ORDER_ITEM." where ordno='".$_POST['ordno']."' and dvno and dvcode limit 1");
			$db->query("update ".GD_ORDER." set deliveryno='$deliveryno',deliverycode='$deliverycode' where ordno='".$_POST['ordno']."'");
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
		ctlStep($_GET[ordno],1,1);
		set_prn_settleprice($_GET[ordno]);

		break;

}
//$db->viewLog();
go($_SERVER[HTTP_REFERER]);
?>
