<?
class admin_order_post_list extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder
		->from(
			array('ORD' => GD_ORDER)
			,array('ordno','nameOrder','step','step2','orddt','oldordno','settlekind','settleprice','delivery','phoneReceiver','mobileReceiver','deli_type','deliveryno','deliverycode','deli_msg')
		)
		->join(
			 array('ITM' => GD_ORDER_ITEM)
			,'ORD.ordno = ITM.ordno'
			,array('goodsnm')
		)
		->columns(array('goods_cnt' => $this->db->expression('COUNT(ITM.sno)')))
		//->order('ORD.ordno ASC')	// group 절에서 자동 정렬이 됨
		->group('ORD.ordno')
		;

		// 검색어
		if($param['sword'] && $param['skey']) {

			switch($param['skey']) {
				case 'all':
					$subWhere = array();
					$subWhere[] = $builder->parse('ORD.ordno = ?', $param['sword']);
					$subWhere[] = $builder->parse('ORD.nameOrder like ?', $this->db->wildcard($param['sword']));
					$subWhere[] = $builder->parse('ORD.bankSender like ?', $this->db->wildcard($param['sword']));

					$builder->where( '('.implode(' OR ',$subWhere).')' );
					break;
				case 'ordno': $builder->where('ORD.ordno = ?', $param['sword']); break;
				case 'nameOrder': $builder->where('ORD.nameOrder like ?', $this->db->wildcard($param['sword'])); break;
				case 'bankSender': $builder->where('ORD.bankSender like ?', $this->db->wildcard($param['sword'])); break;
			}
		}

		// 송장번호 발급 상태
		if($param['dvcodeflag']=='yes') {
			$builder->where('ORD.deliverycode <> ?', '');
		}
		elseif($param['dvcodeflag']=='no') {
			$builder->where('ORD.deliverycode = ?', '');
		}
		elseif($param['dvcodeflag']=='error') {
			$builder->where($this->db->expression('TRIM(ORD.mobileReceiver) NOT REGEXP \'^([0]{1}[0-9]{1,2})-?([1-9]{1}[0-9]{2,3})-?([0-9]{4})$\''));
		}

		// 주문상태 검색
		if(count($param['arStep'])) {
			$builder->where('ORD.step IN (?)', array($param['arStep']));
			$builder->where('ORD.step2 = 0');
		}

		// 주문일 검색
		if($param['regdt_start'] && $param['regdt_end']) {
			$builder->where('ORD.orddt BETWEEN ? AND ?', array(Core::helper('Date')->min($param['regdt_start']),Core::helper('Date')->max($param['regdt_end'])));
		}
		elseif($param['regdt_start']) {
			$builder->where('ORD.orddt >= ?', Core::helper('Date')->min($param['regdt_start']));
		}
		elseif($param['regdt_end']) {
			$builder->where('ORD.orddt <= ?', Core::helper('Date')->max($param['regdt_end']));
		}

		// 결제방법 검색
		if($param['settlekind']) {
			$builder->where('ORD.settlekind = ?', $param['settlekind']);
		}

		// 예약상태 검색관련
		if($param['reserved']=='yes' || $param['reserved']=='no') {

			$builder->leftjoin(
				 array('PST' => GD_GODOPOST_RESERVED)
				,'PST.deliverycode = ORD.dvcode AND ORD.deliveryno = 100'
				,null
			);

			if($param['reserved']=='yes') {
				$builder->where('PST.deliverycode > ?', '');
			}
			else {
				$builder->where('PST.deliverycode IS NULL');
			}

		}

		$param['page_size'] = isset($param['page_size']) ? $param['page_size'] : 10;

		$result = $this->db->utility()->getPaging($builder, $param['page_size'], $param['page']);

		// 송장번호 미발급 주문 갯수 알아내기
		$builder->reset('column');
		$builder->reset('group');
		$builder->reset('order');
		$builder->reset('limit');

		$builder->columns(array('cnt' => $this->db->expression('COUNT(ORD.ordno)')));
		$builder->where('ORD.deliverycode = ?','');

		list($result->unassign_count) = $builder->fetch();

		return $result;

	}

}
?>