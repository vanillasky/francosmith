<?
class admin_order_goodsflow_invoice extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder
		->from(
			 array('ORD' => GD_ORDER)
			,'*'
		)
		->join(
			 array('ITM' => GD_ORDER_ITEM)
			,'ORD.ordno = ITM.ordno'
			,array('goodsnm','goodsno','sno','istep')
		)
		->leftjoin(
			 array('GFOM' => GD_GOODSFLOW_ORDER_MAP)
			,'ORD.ordno = GFOM.ordno AND GFOM.item_sno = ITM.sno'
			,null
		)
		->leftjoin(
			 array('GF' => GD_GOODSFLOW)
			,'GFOM.goodsflow_sno = GF.sno'
			,null
		)
		->leftjoin(
			 array('MB' => GD_MEMBER)
			,'ORD.m_no = MB.m_no'
			,array('m_id','m_no')
		)
		->columns(array('goods_cnt'=>$this->db->expression('COUNT(ITM.sno)')))
		->group('ORD.ordno');

		#0. 초기화
			$builder->where('GF.status = ? OR GF.status IS NULL', '');

		#1. 판매 채널

		#2. 주문 상태
			$builder->where('ORD.step2 < 40');

			if ($param['ord_status'] > -1) {
				$builder->where('ORD.step = ?',$param['ord_status']);
			}
			else {
				$builder->where('ORD.step IN (?)',array(array(1,2)));
			}

		#3. 결제 수단
			if($param['settlekind']) {
				$builder->where('ORD.settlekind = ?', $param['settlekind']);
			}

		#4. 통합 검색
			if($param['sword'] && $param['skey']) {
				$es_sword = $db->_escape($param['sword']);
				switch($param['skey']) {
					case 'all':
						$_where = array();

						// 이나무의 데이터를 가져오므로, 필드를 매핑해줌.
						$_skey_map = array(
							'o.ord_name' => 'o.nameOrder',
							'o.rcv_name' => 'o.nameReceiver',
							'o.pay_bank_name' => 'o.bankSender',
							'm.m_id' => 'm.m_id',
							'o.ord_phone' => 'o.phoneOrder',
							'o.rcv_phone' => 'o.phoneReceiver',
							'o.rcv_address' => 'o.address',
							'o.dlv_no' => 'o.deliverycode',
						);

						foreach($integrate_cfg['skey'] as $cond) {
							if (preg_match($cond['pattern'],$es_sword)) {

								$_condition = isset($_skey_map[$cond['field']]) ? $_skey_map[$cond['field']] : $cond['field'];

								if ($cond['condition'] == 'like') $_condition .= ' like \'%'.$es_sword.'%\'';
								else if ($cond['condition'] == 'equal') $_condition .= ' = \''.$es_sword.'\'';
								else continue;

								$_where[] = $_condition;
							}
						}

						if (sizeof($_where) > 0) $arWhere[] = "(".implode(' OR ',$_where).")";
						break;
					case 'ordno': $arWhere[] = "o.ordno = '{$es_sword}'"; break;
					case 'nameOrder': $arWhere[] = "o.nameOrder like '%{$es_sword}%'"; break;
					case 'nameReceiver': $arWhere[] = "o.nameReceiver like '%{$es_sword}%'"; break;
					case 'bankSender': $arWhere[] = "o.bankSender like '%{$es_sword}%'"; break;
					case 'm_id': $arWhere[] = "m.m_id = '{$es_sword}'"; break;
					case 'phoneOrder': $arWhere[] = "o.phoneOrder like '%{$es_sword}%'"; break;
					case 'phoneReceiver': $arWhere[] = "o.phoneReceiver like '%{$es_sword}%'"; break;
					case 'address': $arWhere[] = "o.address like '%{$es_sword}%'"; break;
					case 'deliverycode': $arWhere[] = "o.deliverycode like '%{$es_sword}%'"; break;
					case 'name': $arWhere[] = "a.name like '%{$es_sword}%'"; break;
				}
			}
		#5. 처리일자
			if($param['regdt'][0]) {
				if(!$param['regdt'][1]) $param['regdt'][1] = date('Ymd',G_CONST_NOW);

				if ((int)$param['regdt_time'][0] !== -1 && (int)$param['regdt_time'][1] !== -1) {
					$param['regdt'][0] .= sprintf('%02d',$param['regdt_time'][0]);
					$param['regdt'][1] .= sprintf('%02d',$param['regdt_time'][1]);
				}

				$tmp_start = Core::helper('Date')->min($param['regdt'][0]);
				$tmp_end = Core::helper('Date')->max($param['regdt'][1]);

				switch($param['dtkind']) {
					case 'orddt': $builder->where('ORD.orddt BETWEEN ? AND ?', array($tmp_start, $tmp_end)); break;
					//case 'cs_regdt': $arWhere[] = $db->_query_print('a.regdt between [s] and [s]',$tmp_start,$tmp_end); break;
					case 'ddt': $builder->where('DLV.ddt BETWEEN ? AND ?', array($tmp_start, $tmp_end)); break;
					case 'confirmdt': $builder->where('ORD.confirmdt BETWEEN ? AND ?', array($tmp_start, $tmp_end)); break;
				}
			}

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
			if($param['eggyn']) {
				$builder->where('ORD.eggyn = ?',$param['eggyn']);
			}

		#8. 결제시 적용 (or 검색인가?)
			$tmp_arWhere = array();

			if($param['escrowyn']) {
				$tmp_arWhere[] = $builder->parse('ORD.escrowyn = ?',$param['escrowyn']);
			}

			if($param['cashreceipt']) {
				$tmp_arWhere[] = $builder->parse('ORD.cashreceipt != ?','');
			}

			if($param['flg_coupon']) {

				$builder
				->join(
					 array('CUPN' => GD_COUPON_ORDER)
					,'CUPN.ordno = ORD.ordno'
					,null
				);

				$tmp_arWhere[] = $builder->parse('CUPN.ordno IS NOT NULL');
			}

			if($param['about_coupon_flag']=='1') {
				$tmp_arWhere[] = $builder->parse('ORD.about_coupon_flag = ?','Y');
			}

			if($param['pay_method_p']=='1') {
				$tmp_arWhere[] = $builder->parse('ORD.settlekind = ?','p');
			}

			if($param['cbyn']=='Y') {
				$tmp_arWhere[] = $builder->parse('ORD.cbyn = ?','Y');
			}

			if (sizeof($tmp_arWhere) > 0) {
				$arWhere[] = '('.implode(' OR ',$tmp_arWhere).')';
				unset($tmp_arWhere);
			}

		#9. 홍보채널
		if(count($param['chk_inflow'])) {
			$es_inflow = array();
			foreach($param['chk_inflow'] as $v) {
				if($v == 'naver_price') {
					$es_inflow[] = 'naver_elec';
					$es_inflow[] = 'naver_bea';
					$es_inflow[] = 'naver_milk';
				}
				else if($v == 'plus_cheese'){
					$builder->where('PTNR.pCheeseOrdNo <> ','');
				}
				else {
					$es_inflow[] = $v;
				}
			}
			if(!empty($es_inflow)){
				$builder->where('PTNR.inflow IN (?)', array($es_inflow));
			}
		}

		#10. 접수유형 (별도 필드가 없고, inflow 값이 sugi 인 레코드)
			if($param['ord_type'] == 'offline') {
				$builder->where('PTNR.inflow = ?', 'sugi');
			}
			else if ($param['ord_type'] == 'online') {
				$builder->where("PTNR.inflow != ? OR PTNR.inflow IS null", 'sugi');
			}

		# group, order

		if ($param['sort']) {
			$_tmp = explode(' ',$param['sort']);	// 0 : field, 1 : direction.

			switch ($_tmp[0]) {
				case 'o.orddt':
					$builder->order('ORD.orddt '.$_tmp[1]);
					break;
				case 'o.cdt':
					$builder->order('ORD.cdt '.$_tmp[1]);
					break;
				case 'o.settleprice':
					$builder->order('ORD.settleprice '.$_tmp[1]);
					break;
			}
		}
		else {
			$builder->order('ORD.orddt DESC');
		}

		if ($param['mode'] === 'goods') {

			// 상품별로 송장번호를 입력
			$builder->reset('column');
			$builder->columns('ordno','ORD');

			$query = $builder->toString();
			$_result = $this->db->utility()->getPaging($builder, $param['page_num'], $param['page']);

			$ordnos = array();

			foreach($_result as $row) {
				$ordnos[] = $row['ordno'];
			}

			if (sizeof($ordnos) == 0) return;

			$builder->reset('column');
			$builder->reset('where');
			$builder->reset('group');

			$builder->columns('*','ORD');
			$builder->columns(array('m_id','m_no'),'MB');
			$builder->columns(array('settlekind'),'ORD');
			$builder->columns(array('goodsnm','goodsno','sno','istep'),'ITM');

			$builder->where('GF.status = ? OR GF.status IS NULL', '');
			$builder->where('ORD.ordno IN (?)', array($ordnos));

			$result = $this->db->utility()->getAll($builder);
			$result->page = $_result->page;

		}
		else {

			$result = $this->db->utility()->getPaging($builder, $param['page_num'], $param['page']);

		}

		return $result;
	}

}
?>