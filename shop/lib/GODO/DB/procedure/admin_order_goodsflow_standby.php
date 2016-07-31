<?
class admin_order_goodsflow_standby extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		global $integrate_cfg;

		$builder = $this->db->builder()->select();

		$builder
		->from(
			 array('GF' => GD_GOODSFLOW)
			,null
		)
		->join(
			 array('GFOM' => GD_GOODSFLOW_ORDER_MAP)
			,'GF.sno = GFOM.goodsflow_sno'
			,null
		)
		->leftjoin(
			 array('ORD' => GD_ORDER)
			,'GFOM.ordno = ORD.ordno'
			,null
		)
		->leftjoin(
			 array('ITM' => GD_ORDER_ITEM)
			,'GFOM.ordno = ITM.ordno AND GFOM.item_sno = ITM.sno'
			,null
		)
		->leftjoin(
			 array('MB' => GD_MEMBER)
			,'ORD.m_no = MB.m_no'
			,null
		)
		->order('ORD.orddt DESC')
		;

		#0. 초기화
			$builder->where('GF.status = ?','print_invoice');

		#1. 판매 채널

		#2. 주문 상태
			$builder->where('ORD.step2 < 40');

			if ($param['ord_status'] > -1) {
				$builder->where('ORD.step = ?', $param['ord_status']);
			}
			else {
				$builder->where('ORD.step IN (?)', array( array(1,2)) );
			}

		#3. 결제 수단
			if($param['settlekind']) {
				$builder->where('ORD.settlekind = ?', $param['settlekind']);
			}

		#4. 통합 검색
			if($param['sword'] && $param['skey']) {

				switch($param['skey']) {
					case 'all':
						$_where = array();

						// 이나무의 데이터를 가져오므로, 필드를 매핑해줌.
						$_skey_map = array(
							'o.ord_name' => 'ORD.nameOrder',
							'o.rcv_name' => 'ORD.nameReceiver',
							'o.pay_bank_name' => 'ORD.bankSender',
							'm.m_id' => 'MB.m_id',
							'o.ord_phone' => 'ORD.phoneOrder',
							'o.rcv_phone' => 'ORD.phoneReceiver',
							'o.rcv_address' => 'ORD.address',
							'o.dlv_no' => 'ORD.deliverycode',
						);

						foreach($integrate_cfg['skey'] as $cond) {

							if (preg_match($cond['pattern'],$param['sword'])) {

								$_format = isset($_skey_map[$cond['field']]) ? $_skey_map[$cond['field']] : $cond['field'];

								if ($cond['condition'] == 'like') {
									$_format .= ' like ?';
									$_sword = $this->db->wildcard($param['sword']);
								}
								else if ($cond['condition'] == 'equal') {
									$_format .= ' = ?';
									$_sword = $param['sword'];
								}
								else continue;

								$_where[] = $builder->parse($_format, $_sword);
							}
						}

						if (sizeof($_where) > 0) $builder->where("(".implode(' OR ',$_where).")");
						break;
					case 'ordno': $builder->where('ORD.ordno = ?', $param['sword']); break;
					case 'nameOrder': $builder->where('ORD.nameOrder like ?', $this->db->wildcard($param['sword'])); break;
					case 'nameReceiver': $builder->where('ORD.nameReceiver like ?', $this->db->wildcard($param['sword'])); break;
					case 'bankSender': $builder->where('ORD.bankSender like ?', $this->db->wildcard($param['sword'])); break;
					case 'm_id': $builder->where('MB.m_id = ?', $param['sword']); break;
					case 'phoneOrder': $builder->where('ORD.phoneOrder like ?', $this->db->wildcard($param['sword'])); break;
					case 'phoneReceiver': $builder->where('ORD.phoneReceiver like ?', $this->db->wildcard($param['sword'])); break;
					case 'address': $builder->where('ORD.address like ?', $this->db->wildcard($param['sword'])); break;
					case 'deliverycode': $builder->where('ORD.deliverycode like ?', $this->db->wildcard($param['sword'])); break;
				}
			}

		#5. 처리일자

		#6. 상품검색
			if($param['sgword'] && $param['sgkey']) {
				switch($param['sgkey']) {
					case 'goodsnm': $builder->where('ITM.goodsnm like ?', $this->db->wildcard($param['sgword'])); break;
					case 'brandnm': $builder->where('ITM.brandnm like ?', $this->db->wildcard($param['sgword'])); break;
					case 'maker': $builder->where('ITM.maker like ?', $this->db->wildcard($param['sgword'])); break;
					case 'goodsno': $builder->where('ITM.goodsno like ?', $this->db->wildcard($param['sgword'])); break;
					case 'purchase':

						$builder
						->join(
							 array('PCS' => GD_PURCHASE)
							,'PCS.ordno = ITM.ordno'
							,null
						)
						->join(
							 array('PCSG' => GD_PURCHASE_GOODS)
							,'PCS.pchsno = PCSG.pchsno'
							,null
						)
						->where('PCS.comnm like ?', $this->db->wildcard($param['sgword']));

					break;
				}
			}

		#7. 소비자피해보상보험

		#8. 결제시 적용

		#9. 홍보채널

		#10. 접수유형 (별도 필드가 없고, inflow 값이 sugi 인 레코드)
			if($param['ord_type'] == 'offline') {
				$builder->where('PTNR.inflow = ?', 'sugi');
			}
			else if ($param['ord_type'] == 'online') {
				$builder->where("PTNR.inflow != ? OR PTNR.inflow IS null", 'sugi');
			}

		#xx. 페이징 query 생성
			$_paging_query = http_build_query($param);	// php5 전용함수. but! lib.func.php 안에 php4용 있음.

	// 쿼리 실행
		$builder->group(array('GF.TransUniqueCd', 'ORD.ordno'));
		$builder->columns('GF.TransUniqueCd');

		$result = $this->db->_select_page($param['page_num'],$param['page'], $builder->toString());

		$TransUniqueCds = array();

		foreach($result['record'] as $row) {
			$TransUniqueCds[] =$row['TransUniqueCd'];
		}

		$arRows = array();

		if (sizeof($TransUniqueCds) > 0) {

			$builder->reset('where')->where('GF.TransUniqueCd IN (?)', array($TransUniqueCds));
			$builder->reset('group');
			$builder
			->columns(
				array('TransUniqueCd','UniqueCd','type','status'), 'GF'
			)
			->columns(
				array('*'), 'ORD'
			)
			->columns(
				array('m_id','m_no'), 'MB'
			)
			->columns(
				array('goodsnm','goodsno','sno','istep'), 'ITM'
			);

			$res = $builder->query();

			// 배송번호 > 주문번호 > 주문정보 순으로 담는다.
			while ($row = $db->fetch($res,1)) {
				$arRows[$row['UniqueCd']][] = $row;
			}

		}

		return $arRow;

	}

}
?>