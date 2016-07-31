<?
require_once dirname(__FILE__)."/shople.class.php";

class integrate_order_processor_shople extends integrate_order_processor {

	// 쇼플 데이터를 가져온다.
	function _getShopleOrderList($date) {
		$rs = $this->_execApi('GET_ORDERLIST','',array('date'=>$date));
		return $rs['body'];
	}

	function _getShopleOrderItemList($date) {
		$rs = $this->_execApi('GET_ORDERITEMLIST','',array('date'=>$date));
		return $rs['body'];
	}

	function _getShopleCSList($date) {
		$rs = $this->_execApi('GET_CSLIST','',array('date'=>$date));
		return $rs['body'];
	}

	function _getShopleServerTime() {
		static $date = null;

		if ($date === null) {
			$rs = $this->_execApi('GET_NOW');
			$date = $rs['body'];
		}

		return $date;
	}

	function extractData($var = null) {

		$_date = $this->_getShopleServerTime();
		if (empty($_date)) return;

		// 주문 데이터
			$list = $this->_getShopleOrderList($_date);
			$tmp = array();
			$m = sizeof($list);

			if ($m < 1)
				return false;

			for ($i=0;$i<$m;$i++) {
				$row = $list[$i];

				if (($i % 20) == 0) {

					if (!empty($tmp)) {

						// 주문정보
						$query = "
						INSERT INTO $this->temp_table_order
							(
								channel,ordno,old_ordno,m_no,m_id_out,ord_name,ord_email,ord_phone,ord_mobile,rcv_name,rcv_phone,rcv_mobile,rcv_zipcode,rcv_address,pay_amount,ord_amount,dis_amount,res_amount,dlv_amount,dlv_type,dlv_company,dlv_no,dlv_message,ord_date,dlv_date,pay_date,fin_date,pay_bank_name,pay_bank_account,pay_method,dlv_method,flg_escrow,flg_egg,flg_cashbag,flg_inflow,ori_status,
								reg_date,mod_date,
								cs_regdt,cs_confirmdt,cs_reason,cs_reason_type,cs_type
							)
						VALUES
						".implode(",",$tmp);

						$this->db->query($query);

						$tmp = array();
					}
				}

				$cs_regdt = '';
				$cs_confirmdt = '';
				$cs_reason = '';
				$cs_reason_type = '';
				$cs_type = '';

				$tmp[] = "(
							'shople','$row[ordNo]','','','$row[memID]','$row[ordNm]','$row[ordEmail]','$row[ordPrtblTel]','$row[ordPrtblTel]','$row[rcvrNm]','$row[rcvrTlphn]','$row[rcvrPrtblNo]','$row[rcvrMailNo]','$row[rcvrBaseAddr] $row[rcvrDtlsAddr]','$row[ordPayAmt]','$row[ordAmt]','','','$row[dlvCst]','$row[dlvCstType]','$row[dlvEtprsCd]','$row[invcNo]','$row[ordDlvReqCont]','$row[ordDt]',STR_TO_DATE('$row[sendDt]','%Y%m%d%H%i'),'$row[ordStlEndDt]','$row[pocnfrmDt]','','','','$row[dlvMthdCd]','','','','','$row[stats]',
							NOW(), null,
							'$cs_regdt','$cs_confirmdt','$cs_reason','$cs_reason_type','$cs_type'
							)";
			}

			if (!empty($tmp)) {
				$query = "
				INSERT INTO $this->temp_table_order
					(
						channel,ordno,old_ordno,m_no,m_id_out,ord_name,ord_email,ord_phone,ord_mobile,rcv_name,rcv_phone,rcv_mobile,rcv_zipcode,rcv_address,pay_amount,ord_amount,dis_amount,res_amount,dlv_amount,dlv_type,dlv_company,dlv_no,dlv_message,ord_date,dlv_date,pay_date,fin_date,pay_bank_name,pay_bank_account,pay_method,dlv_method,flg_escrow,flg_egg,flg_cashbag,flg_inflow,ori_status,
						reg_date,mod_date,
						cs_regdt,cs_confirmdt,cs_reason,cs_reason_type,cs_type
					)
				VALUES
				".implode(",",$tmp);
				$this->db->query($query);

			}

		// 주문 상품 데이터
			$list = $this->_getShopleOrderItemList($_date);
			$tmp = array();

			for ($i=0,$m=sizeof($list);$i<$m;$i++) {
				$row = $list[$i];

				if (($i % 20) == 0) {

					if (!empty($tmp)) {

						// 주문 상품
						$query = "
						INSERT INTO $this->temp_table_item
							(channel,ordno,ord_seq,goodsnm,goodsno,`option`,ea,price)
						VALUES
						".implode(",",$tmp);
						$this->db->query($query);

						$tmp = array();
					}

				}

				$tmp[] = "(
							'shople','$row[ordNo]','$row[ordPrdSeq]','$row[prdNm]','$row[prdNo]','$row[slctPrdOptNm]','$row[ordQty]','$row[selPrc]'
							)";
			}

			if (!empty($tmp)) {
				$query = "
				INSERT INTO $this->temp_table_item
					(channel,ordno,ord_seq,goodsnm,goodsno,`option`,ea,price)
				VALUES
				".implode(",",$tmp);
				$this->db->query($query);
			}

		// cs 데이터
			$list = $this->_getShopleCSList($_date);

			$tmp = array();

			for ($i=0,$m=sizeof($list);$i<$m;$i++) {
				$row = $list[$i];

				$query = "
				UPDATE $this->temp_table_order SET
					cs_regdt		= '$row[csDt]',
					cs_confirmdt	= '',
					cs_reason		= '$row[csReasonStr]',
					cs_reason_type	= '$row[csReasonCode]'
				WHERE channel = 'shople' AND ordno = '$row[ordNo]'
				";
				$this->db->query($query);

				$query = "
				UPDATE $this->temp_table_item SET
					cs			= 'y',
					cs_status	= '$row[stats]'
				WHERE channel = 'shople' AND ordno = '$row[ordNo]' AND ord_seq = '$row[ordPrdSeq]'
				";
				$this->db->query($query);

			}

		$this->adjustData();
		return true;

	}

	function adjustData() {

		$_tmp = array(
						0	=> array(),			// 주문접수
						1	=> array(''),		// 입금확인 (반드시 공백으로 입력)
						2	=> array('발주확인'),	// 배송준비중
						3	=> array('배송중'),	// 배송중
						4	=> array('판매완료'),	// 배송완료(판매완료, 송금완료 등등등)

						// 취소
						10	=> array('취소요청'),	// 신청,접수,진행중,등등 완료가 아닌 경우
						11	=> array('취소완료','판매불가'),	// 완료

						// 환불
						20	=> array(),	// 신청,접수,진행중,등등 완료가 아닌 경우
						21	=> array(),	// 완료

						// 반품
						30	=> array('반품접수','반품접수거부','반품보류','반품신청','반품거부','반품철회','반품완료보류'),	// 신청,접수,진행중,등등 완료가 아닌 경우
						31	=> array('반품완료'),		// 완료

						// 교환
						40	=> array('교환신청','교환접수','교환발송완료','교환승인','교환보류','교환접수거부','교환거부','교환철회'),	// 신청,접수,진행중,등등 완료가 아닌 경우
						41	=> array('교환완료'),	// 완료

						// 결제오류
						50	=> array(),
						51	=> array(),
						54	=> array()
		);

		// 주문상태 조정
		foreach ($_tmp as $_status => $_cond) {

			if (empty($_cond)) continue;

			$_cond = array_map(create_function('$var','return "\'".$var."\'";'), $_cond);

			$query = "
			UPDATE ".$this->temp_table_order." SET
				ord_status = $_status
			WHERE ori_status IN (".implode(',',$_cond).")
			";
			$this->db->query($query);

			if (($_status % 10) == 1) {
				$query = "
				UPDATE ".$this->temp_table_item." SET
					cs = 'y'
				WHERE cs = 'y' AND cs_status IN (".implode(',',$_cond).")
				";
				$this->db->query($query);
			}

		}

		$this->db->query($query);

	}

	function setSyncComplete() {

		if ($this->update('shople')) {
			$this->db->query("TRUNCATE TABLE ".$this->temp_table_order);
			$this->db->query("TRUNCATE TABLE ".$this->temp_table_item);
		}

	}

	// shople 객체 리턴.
	function &getApiInstance() {
        static $ins = null;

        if ($ins === null)
			$ins = Core::loader('shople');

        return $ins;

	}

	function _execApi($method, $param='', $data='') {
		$api = $this->getApiInstance();
		return $api->request($method, $param, $data);
	}

	// 주문 처리 관련 메서드 (사용하지 않거나 지원되지 않는 메서드는 삭제해도 무방함)
	function setOrderDeliveryReady($ordno) {

		$rs = $this->_execApi('GET_DLVNO','',array('ordNo'=>$ordno));

		if ($rs['result'] === true) {
			$dlvnos = $rs['body'];

			$param = array(
				'ordNo' => $ordno,
			);

			foreach ($dlvnos as $dlvNo) {
				$param['ordPrdSeq'] = $dlvNo['ordPrdSeq'];
				$param['addPrdYn'] = $dlvNo['addPrdYn'];
				$param['addPrdNo'] = 'null';
				$param['dlvNo'] = $dlvNo['dlvNo'];

				$this->_execApi('SET_ORDER_CONFIRM',$param,'');
			}

		}
	}

	function setOrderDelivery($ordno, $extra) {

		$rs = $this->_execApi('GET_DLVNO','',array('ordNo'=>$ordno));

		if ($rs['result'] === true) {
			$dlvnos = $rs['body'];

			$param = array(
				'sendDt' => date('YmdHi'),
				'dlvMthdCd' => '01',
				'dlvEtprsCd' => $extra['dlv_company'],
				'invcNo' => $extra['dlv_no']
			);

			foreach ($dlvnos as $dlvNo) {
				$param['dlvNo'] = $dlvNo['dlvNo'];
				$this->_execApi('SET_ORDER_DELIVERY',$param,'');
			}
		}

	}

	// 취소 승인
    function setOrderCancelFin($ordno,$extra) {

		$rs = $this->_execApi('GET_DLVNO','',array('ordNo'=>$ordno));

		if ($rs['result'] === true) {
			$dlvnos = $rs['body'];

			foreach ($dlvnos as $dlvNo) {

				$param = array();

				if ($extra['reject'] == 1) {
					// 판매거부
					$method = 'SET_ORDER_REJECT';

					$param = array(
						'ordNo' => $ordno,
						'ordPrdSeq' => $dlvNo['ordPrdSeq'],
						'ordCnRsnCd' => $extra['cs_reason_code'],
						'ordCnDtlsRsn' => urlencode($extra['cs_reason'])
					);
				}
				else {
					// 취소 승인
					$method = 'SET_CLAIMCANCEL';

					$param = array(
						'ordPrdCnSeq'	=> $dlvNo['cs_seq'],	// 취소 클레임번호
						'ordNo'			=> $ordno,				// 주문번호
						'ordPrdSeq'		=> $dlvNo['ordPrdSeq'],	// 주문순번
					);
				}

				$this->_execApi($method,$param,'');
			}
		}

    }

	// 반품 승인
    function setOrderReturnFin($ordno,$extra) {

		$rs = $this->_execApi('GET_DLVNO','',array('ordNo'=>$ordno));

		if ($rs['result'] === true) {
			$dlvnos = $rs['body'];

			foreach ($dlvnos as $dlvNo) {

				$method = 'SET_CLAIMRETURN';
				$param = array(
					'clmReqSeq'		=> $extra['cs_seq'],	// 클레임번호
					'ordNo'			=> $ordno,		// 주문번호
					'ordPrdSeq'		=> $extra['ordPrdSeq']	// 주문순번
				);

				$this->_execApi($method,$param,'');
			}
		}
    }

	// 교환 승인
    function setOrderExchangeFin($ordno,$extra) {

		// 처리 프로세스가 달라 일괄 처리 못함.
		// 쇼플 페이지로 이동후 처리하도록 안내.

    }

}
?>
