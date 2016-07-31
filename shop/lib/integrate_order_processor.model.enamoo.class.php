<?
// 투데이샵, 모바일샵, 이나무 는 주문 테이블이 같으므로 특정 값에 따라 channel 을 치환하여 저장함.
class integrate_order_processor_enamoo extends integrate_order_processor {

    function extractData($var = null) {

        // 주문 데이터
        $query = "
			INSERT
			INTO ".$this->temp_table_order."
			(
				channel,ordno,old_ordno,m_no,m_id_out,ord_name,ord_email,ord_phone,ord_mobile,rcv_name,rcv_phone,rcv_mobile,rcv_zipcode,rcv_address,pay_amount,ord_amount,dis_amount,res_amount,dlv_amount,dlv_type,dlv_company,dlv_no,dlv_message,ord_date,dlv_date,pay_date,fin_date,pay_bank_name,pay_bank_account,pay_method,dlv_method,flg_escrow,flg_egg,flg_cashbag,flg_inflow,ori_status,pg,
				reg_date,mod_date,
				flg_cashreceipt, flg_aboutcoupon,flg_mobile, naver_mileage_save
			)

			SELECT
				'enamoo',O.ordno,O.oldordno,O.m_no,'',O.nameOrder,O.email,O.phoneOrder,O.mobileOrder,O.nameReceiver,O.phoneReceiver,O.mobileReceiver,O.zipcode,O.address,O.prn_settleprice,O.settleprice,O.memberdc,O.reserve,O.delivery,O.deli_type,O.deliveryno,O.deliverycode,O.memo,O.orddt,O.ddt,O.cdt,O.confirmdt,O.bankSender,IF (BNK.bank IS NOT NULL, CONCAT(BNK.bank,' ', BNK.account), ''),O.settlekind, O.deli_title,O.escrowyn,O.eggyn,O.cbyn,O.inflow,CONCAT(O.step,'_', O.step2),O.pg,
				NOW(), null,
				O.cashreceipt, O.about_coupon_flag,IF(O.mobilepay = 'y', 1 , 0), IF(O.ncash_save_yn='y', 1, 0)

			FROM ".GD_ORDER." AS O

			LEFT JOIN ".GD_LIST_BANK." AS BNK
				ON O.bankAccount = BNK.sno

			WHERE
				O.sync_ = 0 AND O.pCheeseOrdNo = ''
		";

        if ($var !== null) {
            $query .= "
			AND O.orddt >= '$var[startdt]' AND O.orddt < '$var[enddt]'
			";
        }
        $this->db->query($query);

		if ($this->db->affected() < 1)
			return false;



        // 주문 상품 정보
        $query = "
			INSERT
			INTO ".$this->temp_table_item."
			(
				channel,ordno,goodsnm,goodsno,`option`,ea,price,emoney,
				cs
			)
			SELECT
				'enamoo', B.ordno,C.goodsnm,C.goodsno,CONCAT(C.opt1,' / ', C.opt2, ' / ', C.addopt),C.ea,C.price,C.reserve,
			  	IF (C.istep = 41, 'y' ,
				IF (C.istep = 42, 'y' ,
				IF (C.istep = 44, 'f' , 'n'
				)))
			FROM ".$this->temp_table_order." AS B

			INNER JOIN ".GD_ORDER_ITEM." AS C
				ON B.ordno = C.ordno

		";
        $this->db->query($query);

		// 쿠폰 사용 정보
		$query = "
			UPDATE ".$this->temp_table_order." AS O

			INNER JOIN ".GD_COUPON_ORDER." AS CP
				ON O.ordno = CP.ordno
			SET
				O.flg_coupon = 1
		";
        $this->db->query($query);

        // 레코드 값 조정
        $this->adjustData();

        return true;

    }

    function adjustData() {

        $_tmp = array(
			0=>array('0_0'), // 주문접수
			1=>array('1_0'), // 입금확인 (반드시 공백으로 입력)
			2=>array('2_0'), // 배송준비중
			3=>array('3_0'), // 배송중
			4=>array('4_0'), // 배송완료(판매완료, 송금완료 등등등)
			// 취소
			10	=> array('0_40','0_41','0_42'),	// 신청,접수,진행중,등등 완료가 아닌 경우
			11	=> array('0_44'),	// 완료
			// 환불
			20	=> array('1_40','1_41','2_40','2_41','3_42','4_42'),	// 신청,접수,진행중,등등 완료가 아닌 경우
			21	=> array('1_44','2_44','3_44','4_44'),	// 완료
			// 반품
			30	=> array('3_40','3_41','4_40','4_41'),	// 신청,접수,진행중,등등 완료가 아닌 경우
			/*31	=> array('반품완료'),		// 완료 -> 환불로 넘어감*/
			// 교환
			/*40	=> array('교환신청','교환접수','교환발송완료','교환승인','교환보류','교환접수거부','교환거부','교환철회'),	// 신청,접수,진행중,등등 완료가 아닌 경우
			41	=> array('교환완료'),	// 완료*/

			// 결제오류
			50=>array('0_50'), 51=>array('0_51'), 54=>array('0_54'));

        // 주문상태 조정
        foreach ($_tmp as $_status=>$_cond) {
            if ( empty($_cond)) continue;

            $_cond = array_map(create_function('$var', 'return "\'".$var."\'";'), $_cond);
            $query = "
			UPDATE ".$this->temp_table_order." SET
				ord_status = $_status
			WHERE ori_status IN (".implode(',', $_cond).")";
			$this->db->query($query);
        }

      // CS 정보 업데이트
        $query = "
			UPDATE ".$this->temp_table_order." AS O

			INNER JOIN ".GD_ORDER_ITEM." AS OI
				ON OI.ordno = O.ordno

			INNER JOIN ".GD_ORDER_CANCEL." AS CS
				ON CS.ordno = O.ordno AND CS.sno = OI.cancel

			SET
				O.cs_regdt = CS.regdt,
				O.cs_confirmdt = CS.ccdt,
				O.cs_reason_type = CS.code,
				O.cs_reason = CS.memo

		";
        $this->db->query($query);
    }

    function setSyncComplete() {
        if ($this->update('enamoo')) {
            $query = "
			UPDATE ".GD_ORDER." AS A
			INNER JOIN ".$this->temp_table_order." AS B
			ON A.ordno = B.ordno

			SET A.sync_ = 1,A.uptdt_ = '".date('Y-m-d H:i:s',$this->now)."'
			";
            $this->db->query($query);
            $this->db->query("TRUNCATE TABLE ".$this->temp_table_order);
			$this->db->query("TRUNCATE TABLE ".$this->temp_table_item);
        }
    }

	// 주문 처리 관련 메서드 (사용하지 않거나 지원되지 않는 메서드는 삭제해도 무방함)
    function setOrderAccept($ordno) {
		ctlStep($ordno,0,'stock');
		setStock($ordno);
		set_prn_settleprice($ordno);
    }

    function setOrderPayConfirm($ordno) {
		ctlStep($ordno,1,'stock');
		setStock($ordno);
		set_prn_settleprice($ordno);
    }

    function setOrderDeliveryReady($ordno) {
		ctlStep($ordno,2,'stock');
		setStock($ordno);
		set_prn_settleprice($ordno);
    }

    function setOrderDelivery($ordno,$extra) {

		// 송장번호 저장
		if ($extra['dlv_company'] && $extra['dlv_no']) {
			$query = "UPDATE ".GD_ORDER." SET deliveryno = '".$extra['dlv_company']."', deliverycode = '".$extra['dlv_no']."' WHERE ordno = '".$ordno."'";
			$this->db->query($query);
		}

		ctlStep($ordno,3,'stock');
		setStock($ordno);
		set_prn_settleprice($ordno);
    }

    function setOrderComplete($ordno) {
		ctlStep($ordno,4,'stock');
		setStock($ordno);
		set_prn_settleprice($ordno);
    }

	// cs 처리
    function setOrderReturnFin($ordno,$extra) {

		// 반품완료처리
		$rs = $this->db->query("
		SELECT `oc`.`sno`, `o`.`pg`, `o`.`ipay_cartno`
		FROM `".GD_ORDER."` AS `o`
		INNER JOIN `".GD_ORDER_CANCEL."` AS `oc`
		ON `o`.`ordno`=`oc`.`ordno`
		WHERE `oc`.`ordno`=".$ordno);
		if(class_exists('integrate_order_processor_ipay', false)===false) include dirname(__FILE__).'/integrate_order_processor.model.ipay.class.php';

		while ($row = $this->db->fetch($rs,1)) {
			$v = $row['sno'];

			if (empty($v)) continue;

			if($row['pg']=='ipay')
			{
				$res = $this->db->query("
				SELECT `oi`.`sno`, `oi`.`goodsno`, `oi`.`ipay_ordno`
				FROM `".GD_ORDER_CANCEL."` AS `oc`
				INNER JOIN `".GD_ORDER_ITEM."` AS `oi`
				ON `oc`.`sno`=`oi`.`cancel`
				WHERE `oc`.`sno`=".$v);
				while($orderItem = $this->db->fetch($res, 1))
				{
					$auctionIpay = new integrate_order_processor_ipay();
					$status = $auctionIpay->GetIpayReceiptStatus($row['ipay_cartno'], $orderItem['goodsno'].'_'.$orderItem['sno']);
					switch($status)
					{
						// 판매자에게 송금완료(구매자 구매결정)이후 단예게서 취소
						case '990':
							$result = $auctionIpay->DoIpayOrderDecisionCancel($orderItem['ipay_ordno']);
							break;
						// 반품처리(동시에 환불)
						default:
							$auctionIpay->DoIpayReturnApproval($orderItem['ipay_ordno']);
							break;
					}
				}

				// 취소된 주문상품의 상태를 반품완료, 배송상태를 반품완료, 결제상태를 환불완료로 수정
				$query = "UPDATE `".GD_ORDER_ITEM."` SET `istep`=44, `dyn`='r', `cyn`='r' WHERE `cancel`='".$v."' AND `ordno`='".$ordno."'";
				$this->db->query($query);

				// 취소된 주문건의 상태를 반품완료, 배송상태를 반품완료, PG결제상태를 부분취소로 변경
				$query = "UPDATE `".GD_ORDER."` SET `step2`=44, `dyn`='r', `pgcancel`='r' WHERE `ordno`='".$ordno."' AND `step2`=41";
				$this->db->query($query);

				// 주문취소건의 PG결제상태를 부분취소로 변경하고 취소일시 입력
				$query = "UPDATE `".GD_ORDER_CANCEL."` SET `pgcancel`='r', `ccdt`='".date('Y-m-d H:i:s')."' WHERE `sno`=".$row['sno'];
				$this->db->query($query);

				$naverNcash = Core::loader('naverNcash');
				$naverNcash->deal_cancel($ordno, $cancel_sno);
			}
			else
			{
				### 주문아이템 처리
				$query = "update ".GD_ORDER_ITEM." set istep=42,dyn='r' where cancel='$v' and ordno='$ordno'";
				$this->db->query($query);

				### 주문 일괄 처리
				$query = "update ".GD_ORDER." set step2=42,dyn='r' where ordno='$ordno' and step2=41";
				$this->db->query($query);
			}

			### 재고조정
			setStock($ordno);
		}

    }

    function setOrderExchangeFin($ordno,$extra) {

    	// 반품 완료후 재주문 넣기.
		$rs = $this->db->query("
		SELECT `oc`.`sno`
		FROM `".GD_ORDER_CANCEL."` AS `oc`
		INNER JOIN `".GD_ORDER."` AS `o`
		ON `oc`.`ordno`=`o`.`ordno`
		WHERE `oc`.`ordno`=".$ordno."
		AND (`o`.`ipay_payno` IS NULL OR `o`.`ipay_payno`<1)
		AND (`o`.`ipay_cartno` IS NULL OR `o`.`ipay_cartno`<1)
		");

		while ($row = $this->db->fetch($rs,1)) {
			$v = $row['sno'];

			### 주문아이템 처리
			$query = "update ".GD_ORDER_ITEM." set istep=44,dyn='e',cyn='e' where cancel='$v' and ordno='$ordno'";
			$this->db->query($query);

			### 주문 일괄 처리
			$query = "update ".GD_ORDER." set step2=44,dyn='e',cyn='e' where ordno='$ordno' and step2=41";
			$this->db->query($query);

			### 재주문
			$newOrdno = reorder($ordno,$v);

		}

        return false;
    }


}
?>
