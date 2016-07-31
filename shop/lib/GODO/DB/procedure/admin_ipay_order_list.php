<?
class admin_ipay_order_list extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder->from(array('IPAY' => GD_AUCTIONIPAY));

		// 키워드 검색
		if ($param['sword']) {
			switch($param['skey']) {
				case 'goodsnm': {

					$builder->where(
						$this->db->expression("EXISTS(SELECT * FROM ".GD_AUCTIONIPAY_ITEM." WHERE ipaysno=IPAY.ipaysno AND goodsnm LIKE '%".$param['sword']."%')")
					);

					break;
				}
				case '' : {

					$subWhere = array();
					$subWhere[] = $builder->parse($this->db->expression( "(EXISTS(SELECT * FROM ".GD_AUCTIONIPAY_ITEM." WHERE ipaysno=IPAY.ipaysno AND goodsnm LIKE '%".$param['sword']."%'))" ));
					$subWhere[] = $builder->parse($this->db->expression( "(IPAY.auctionpayno LIKE '%" . $param['sword'] . "%')" ));
					$subWhere[] = $builder->parse($this->db->expression( "(IPAY.buyername LIKE '%" . $param['sword'] . "%')" ));

					$builder->where( '('.implode(' OR ',$subWhere).')' );
					unset($tmpWhere);
					break;
				}
				default: {
					$_clause = sprintf('IPAY.%s like ?', $param['skey']);
					$builder->where($_clause, $this->db->wildcard($param['sword']));
					break;
				}
			}
		}

		// 결제방법
		if ($param['paymenttype']) {
			$builder->where('IPAY.paymenttype = ?', $param['paymenttype']);
		}
		else {
			$builder->where('IPAY.paymenttype > ?','');
		}

		// 처리상태
		switch($param['responsetype']) {
			case 'orderComplete' : {
				$builder->where(
					$this->db->expression("NOT EXISTS(SELECT * FROM ".GD_AUCTIONIPAY_ITEM." WHERE ipaysno=IPAY.ipaysno AND responsetype='1')")
				);

				$builder->where(
					$this->db->expression("EXISTS(SELECT * FROM ".GD_AUCTIONIPAY_ITEM." WHERE ipaysno=IPAY.ipaysno AND responsetype='0' AND paydate IS NULL)")
				);
				break;
			}
			case 'payComplete' : {

				$builder->where(
					$this->db->expression("NOT EXISTS(SELECT * FROM ".GD_AUCTIONIPAY_ITEM." WHERE ipaysno=IPAY.ipaysno AND responsetype='1')")
				);

				$builder->where(
					$this->db->expression("EXISTS(SELECT * FROM ".GD_AUCTIONIPAY_ITEM." WHERE ipaysno=IPAY.ipaysno AND responsetype='0' AND paydate IS NOT NULL)")
				);

				break;
			}
			case 'orderCancel' : {

				$builder->where(
					$this->db->expression("EXISTS(SELECT * FROM ".GD_AUCTIONIPAY_ITEM." WHERE ipaysno=IPAY.ipaysno AND responsetype='1')")
				);

				$builder->where(
					$this->db->expression("NOT EXISTS(SELECT * FROM ".GD_AUCTIONIPAY_ITEM." WHERE ipaysno=IPAY.ipaysno AND responsetype='0')")
				);

				break;
			}
			case 'partCancel' : {

				$builder->where(
					$this->db->expression("EXISTS(SELECT * FROM ".GD_AUCTIONIPAY_ITEM." WHERE ipaysno=IPAY.ipaysno AND responsetype='0')")
				);

				$builder->where(
					$this->db->expression("EXISTS(SELECT * FROM ".GD_AUCTIONIPAY_ITEM." WHERE ipaysno=IPAY.ipaysno AND responsetype='1')")
				);
				break;
			}
		}

		if ($param['start_dt']) {
			$builder->where('IPAY.orderdate >= ?', Core::helper('Date')->min($param['start_dt']) );
		}

		if ($param['end_dt']) {
			$builder->where('IPAY.orderdate <= ?', Core::helper('Date')->max($param['end_dt']) );
		}

		$builder->group('IPAY.ipaysno');
		$builder->order('IPAY.ipaysno desc');

		$param['page_num'] = !$param['page_num'] ? 10 : $param['page_num'];
		$param['page'] = !$param['page'] ? 1 : $param['page'];

		return $this->db->utility()->getPaging($builder, $param['page_num'], $param['page']);

	}
}

?>