<?php
class coupon_price
{
	var $db;
	var $cfgCoupon;
	var $item = array();
	var $arCoupon;
	var $arCouponGoods;

	function coupon_price(){
		$this->db=Core::loader('db');
	}

	function set_config($cfgCoupon){
		$this->cfgCoupon = $cfgCoupon;
	}

	function set_item($goodsno,$price,$ea,$arCategory,$opt1='',$opt2='',$addopt='',$goodsnm=''){

		$Goods = Core::loader('Goods');
		//$arrCategory = $Goods->get_goods_category($goodsno);	// @확인 : $arCategory 파라미터와 값이 같음.

		$idx = count($this->item);
		$this->item[] = array(
			'goodsno'=>$goodsno,
			'price'=>$price,
			'ea'=>$ea,
			'opt1'=>$opt1,
			'opt2'=>$opt2,
			'addopt'=>$addopt,
			'goodsnm'=>$goodsnm,
			'category'=>$arCategory
		);

		$this->item[$idx]['orderPrice'] = $this->item[$idx]['price']*$this->item[$idx]['ea'];
	}

	function reset_item(){
		$this->item = array();
		$this->arCoupon = array();
		$this->arCouponGoods = array();
	}

	function get_count_order($applysno,$mode='apply'){
		$m_no = (int) $_SESSION[sess][m_no];
		if($mode == 'apply'){
			$query = "SELECT count(*)
					FROM gd_coupon_apply apply,gd_coupon_order ord
					WHERE apply.sno=ord.applysno
						AND apply.sno='$applysno'
						AND ord.m_no='$m_no'";
		}else{
			$query = "SELECT count(*)
					FROM gd_offline_download down,gd_coupon_order ord
					WHERE down.sno=ord.downloadsno
						AND down.sno='$applysno'
						AND ord.m_no='$m_no'";
		}
		list($cnt) = $this->db->fetch($query);
		return $cnt;
	}

	/* 온라인 쿠폰 정보가져오기
	 * mode : view - 상품상세
	 *
	 * 유효한 발급된 모든 쿠폰을 가져와서,
	 * 각각 사용여부를 체크한다.  -->  로직 개선 필요함.
	 * 모바일샵 전용 쿠폰은 피씨에서는 사용할 수 없다
	 * */
	function get_coupon($goodsno,$arrCategory,$mode){

		static $_coupons = array();

		$_dna = md5(serialize(func_get_args()));

		if (!isset($_coupons[$_dna])) {

			$today = date("Y-m-d H:i:s");
			$m_no = (int) $_SESSION['sess']['m_no'];
			$groupsno = (int) $_SESSION['sess']['groupsno'];
			if($arrCategory && $goodsno){
				foreach($arrCategory as $k=>$v) $arrCategory[$k]="'$v'";
				$where1 = "AND (((cate.category in(".implode(',',$arrCategory).") OR goods.goodsno = '$goodsno') AND coupon.goodstype='1') OR coupon.goodstype='0')";
			}else if($goodsno){
				$where2 = "AND ((goods.goodsno = '$goodsno' AND coupon.goodstype='1') OR coupon.goodstype='0')";
			}

		if($mode == "view"){
			$where3 = "AND coupon.coupontype = '1'";
		}

			$where5 = "coupon.priodtype='1'";
			$where5 .= " AND (coupon.edate >= '$today' OR  coupon.edate = '')";
			if($m_no && $mode != "view"){
				$where4 = " AND ((mem.m_no='$m_no') OR (membertype='0') OR (apply.membertype='1' AND apply.member_grp_sno='$groupsno')) AND ((apply.goodsno='$goodsno' AND coupon.coupontype='1') OR (apply.goodsno='0' AND coupon.coupontype!='1')) AND apply.status='0'";
				$where5 .= " AND ADDDATE(apply.regdt,INTERVAL coupon.sdate DAY) >= '".date("Y-m-d")." 00:00:00'";
				$table = "INNER JOIN gd_coupon_apply apply ON apply.couponcd=coupon.couponcd LEFT JOIN gd_coupon_applymember mem ON apply.sno=mem.applysno";
				$fields = ",apply.sno applysno";
			}

		$where6 = " AND (coupon.c_screen != 'm' OR coupon.c_screen is null)";

		$query = "SELECT coupon.*
						$fields
					FROM gd_coupon coupon
							LEFT JOIN gd_coupon_category cate ON coupon.couponcd = cate.couponcd
							LEFT JOIN gd_coupon_goodsno goods ON coupon.couponcd = goods.couponcd
						$table
					WHERE ((coupon.sdate <= '$today' AND coupon.edate >= '$today' AND coupon.priodtype='0') OR ($where5))
						$where1
						$where2
						$where3
						$where4
						$where6";

		$result = $this->db->_select($query);
		if($result){
			foreach($result as $k=>$data){
				$data['orderCnt']=0;
				$data['sno'] = $data['couponcd'];
				$data['goodsno'] = $goodsno;
				$data['coupon_name'] = $data['coupon'];
				$data['coupon_detail'] = $data['summa'];
				$data['coupon_priodtype'] = $data['priodtype'];
				$data['coupon_sdate'] = $data['sdate'];
				$data['coupon_edate'] = $data['edate'];
				if($data['payMethod'] =='1') {
					$data['pay_method'] = 'cash';
					$data['payMethodStr'] = "<br>(무통장 입금에서만 사용가능)";
				}

					if(!$data['duplctl'] && $m_no){
						$data['orderCnt'] = $this->get_count_order($data['applysno']);
					}
					if($data['orderCnt']==0 || $mode=='mypage')$loop[] = $data;
				}

				$_coupons[$_dna] = $loop;
			}
			else $_coupons[$_dna] = false;

		}

		return $_coupons[$_dna];

	}

	/* 페이퍼 쿠폰 정보가져오기  */
	function get_offline($goodsno,$arrCategory){

		$today = date("YmdH");
		$m_no = (int) $_SESSION['sess']['m_no'];
		if(!$m_no) return false;

		if($arrCategory){
			foreach($arrCategory as $k=>$v) $arrCategory[$k]="'$v'";
			$cate_where = "OR goods.category in (".implode(',',$arrCategory).")";
		}
		if($goodsno){
			$where = "AND (goods.goodsno='$goodsno' $cate_where OR coupon.goods_apply='all')";
		}
		$query = "
		SELECT coupon.*,down.sno downsno
		FROM gd_offline_download down,gd_offline_coupon coupon
			LEFT JOIN gd_offline_goods goods ON coupon.sno=goods.coupon_sno
		WHERE coupon.sno=down.coupon_sno
			$where
			AND concat(coupon.start_year,coupon.start_mon,coupon.start_day,coupon.start_time) <= '$today'
			AND concat(coupon.end_year,coupon.end_mon,coupon.end_day,coupon.end_time) >= '$today'
			AND down.m_no='$m_no'
			AND	coupon.`status`!='disuse'";

		$result=$this->db->_select($query);
		if($result){
			foreach($result as $k=>$data){
				$data['orderCnt'] = 0;
				$data['orderCnt'] = $this->get_count_order($data['downsno'],'download');
				if($data['orderCnt']){
					unset($result[$k]);
					continue;
				}
				$result[$k]['couponcd'] = "off_".$data['sno'];
				$result[$k]['sno'] = "off_".$data['sno'];
				$result[$k]['goodsno'] = $goodsno;
				$result[$k]['coupon'] = $data['coupon_name'];
				$result[$k]['sdate'] = $data['start_year'].'-'.$data['start_mon'].'-'.$data['start_day'];
				$result[$k]['edate'] = $data['end_year'].'-'.$data['end_mon'].'-'.$data['end_day'];
				$result[$k]['ability'] = ($data['coupon_type']=="sale")?0:1;
				$result[$k]['price'] = $data['coupon_price'].$data['currency'];
				$result[$k]['excPrice'] = ($data['pay_limit']=='limited')?$data['limit_amount']:0;
				$result[$k]['offline'] = 1;
			}
			return $result;
		}
	}

	/* 쿠폰가격을 불러옴*/
	function get_goods_coupon($mode=''){
		if($this->cfgCoupon['use_yn'] != '1') return false;
		$m_no = (int) $_SESSION['sess']['m_no'];
		foreach($this->item as $item){
			if($mode=='list'){
				$tmp = array_merge((array)$tmp,(array)$this->get_coupon($item['goodsno'],$item['category'],'view'));
			}else{
				$tmp = array_merge((array)$tmp,(array)$this->get_coupon($item['goodsno'],$item['category'],$mode));
			}
		}

		if($m_no && $mode == "order") foreach($this->item as $item){
			$tmp = array_merge((array)$tmp,(array)$this->get_offline($item['goodsno'],$item['category']));
		}

		if($tmp)foreach($tmp as $k => $v){
			if (empty($v)) continue;
			$this->arCoupon[$v['couponcd']] = $v;
			$this->arCouponGoods[$v['couponcd']]['arGoodsno'][$v['goodsno']] = $v['goodsno'];
		}

		if($this->arCoupon){
			foreach($this->arCoupon as $cidx => $data){
				$sale = $reserve = 0;
				if(!$data['ability']) $ability = 'sale';
				else $ability = 'reserve';
				foreach($this->item as $idx => $item){
					$arItem[$cidx][$item['goodsno']]['price'] = $item['price'];
					if(!in_array($item['goodsno'],$this->arCouponGoods[$cidx]['arGoodsno']))continue;
					if(($data['eactl']=='1' && $data['coupontype']=='1')|| (substr($data['price'],-1) == '%'&&$data['coupontype']!='1') ){
						$this->arCoupon[$cidx][$ability][$item['goodsno']] += getDcprice($item['price'],$data['price'])*$item['ea'];
					}else{
						if($mode == 'list'){
							$this->arCoupon[$cidx][$ability][$item['goodsno']] = getDcprice($item['price'],$data['price']);
						}else{
							 $this->arCoupon[$cidx][$ability]['order'] = getDcprice($item['price'],$data['price']);
						}
					}
				}

				if(substr($data['price'],-1) != '%'&&$data['coupontype']!='1'){
					if($this->arCoupon[$cidx][$ability]){
						$i=0;
						foreach($this->arCoupon[$cidx][$ability] as $k=>$v){
							if($i!=0)unset($this->arCoupon[$cidx][$ability][$k]);
							$i++;
						}
					}
				}
			}
		}
	}


	function check_coupon($couponSale,$couponReserve,$settlekind,$arApply){
		$totSale = $totReserve = 0;

		if(!$arApply) return true;

		if ( is_array($this->arCoupon)) {	// 2012-10-24 khs
			foreach($this->arCoupon as $idx => $coupon){
				if(!in_array($coupon[sno],$arApply)) continue;
				if($coupon['pay_method']=='cash' && $settlekind!='a') return "cash";

				if(is_array($coupon['sale'])){
					$totSale += (int) array_sum($coupon['sale']);
				}else if($coupon['sale']){
					 $totSale += (int) $coupon['sale'];
				}

				if(is_array($coupon['reserve'])){
					$totReserve += (int) array_sum($coupon['reserve']);
				}else if($coupon['reserve']){
					 $totReserve += (int) $coupon['reserve'];
				}
			}
		}
		if($couponSale!=$totSale) return "sale";
		if($couponReserve!=$totReserve) return "reserve";
		return true;
	}

	/* 온라인 쿠폰 정보가져오기 - 모바일샵
	 * mode : view - 상품상세
	 *
	 * 유효한 발급된 모든 쿠폰을 가져와서,
	 * 각각 사용여부를 체크한다.  -->  로직 개선 필요함.
	 * 모바일샵에서는 모든 쿠폰을 다 쓸수 있다 *
	 * */
	function get_coupon_mobile($goodsno,$arrCategory,$mode){

		static $_coupons = array();

		$_dna = md5(serialize(func_get_args()));

		if (!isset($_coupons[$_dna])) {

			$today = date("Y-m-d H:i:s");
			$m_no = (int) $_SESSION['sess']['m_no'];
			$groupsno = (int) $_SESSION['sess']['groupsno'];
			if($arrCategory && $goodsno){
				foreach($arrCategory as $k=>$v) $arrCategory[$k]="'$v'";
				$where1 = "AND (((cate.category in(".implode(',',$arrCategory).") OR goods.goodsno = '$goodsno') AND coupon.goodstype='1') OR coupon.goodstype='0')";
			}else if($goodsno){
				$where2 = "AND ((goods.goodsno = '$goodsno' AND coupon.goodstype='1') OR coupon.goodstype='0')";
			}

		if($mode == "view"){
			$where3 = "AND coupon.coupontype = '1'";
		}

			$where5 = "coupon.priodtype='1'";
			$where5 .= " AND (coupon.edate >= '$today' OR  coupon.edate = '')";
			if($m_no && $mode != "view"){
				$where4 = " AND ((mem.m_no='$m_no') OR (membertype='0') OR (apply.membertype='1' AND apply.member_grp_sno='$groupsno')) AND ((apply.goodsno='$goodsno' AND coupon.coupontype='1') OR (apply.goodsno='0' AND coupon.coupontype!='1')) AND apply.status='0'";
				$where5 .= " AND ADDDATE(apply.regdt,INTERVAL coupon.sdate DAY) >= '".date("Y-m-d")." 00:00:00'";
				$table = "INNER JOIN gd_coupon_apply apply ON apply.couponcd=coupon.couponcd LEFT JOIN gd_coupon_applymember mem ON apply.sno=mem.applysno";
				$fields = ",apply.sno applysno";
			}

		$query = "SELECT coupon.*
						$fields
					FROM gd_coupon coupon
							LEFT JOIN gd_coupon_category cate ON coupon.couponcd = cate.couponcd
							LEFT JOIN gd_coupon_goodsno goods ON coupon.couponcd = goods.couponcd
						$table
					WHERE ((coupon.sdate <= '$today' AND coupon.edate >= '$today' AND coupon.priodtype='0') OR ($where5))
						$where1
						$where2
						$where3
						$where4
						$where6";

		$result = $this->db->_select($query);
		if($result){
			foreach($result as $k=>$data){
				$data['orderCnt']=0;
				$data['sno'] = $data['couponcd'];
				$data['goodsno'] = $goodsno;
				$data['coupon_name'] = $data['coupon'];
				$data['coupon_detail'] = $data['summa'];
				$data['coupon_priodtype'] = $data['priodtype'];
				$data['coupon_sdate'] = $data['sdate'];
				$data['coupon_edate'] = $data['edate'];
				if($data['payMethod'] =='1') {
					$data['pay_method'] = 'cash';
					$data['payMethodStr'] = "<br>(무통장 입금에서만 사용가능)";
				}

					if(!$data['duplctl'] && $m_no){
						$data['orderCnt'] = $this->get_count_order($data['applysno']);
					}
					if($data['orderCnt']==0 || $mode=='mypage')$loop[] = $data;
				}

				$_coupons[$_dna] = $loop;
			}
			else $_coupons[$_dna] = false;

		}

		return $_coupons[$_dna];

	}

	/* 쿠폰가격을 불러옴-모바일*/
	function get_goods_coupon_mobile($mode=''){
		if($this->cfgCoupon['use_yn'] != '1') return false;
		$m_no = (int) $_SESSION['sess']['m_no'];
		foreach($this->item as $item){
			if($mode=='list'){
				$tmp = array_merge((array)$tmp,(array)$this->get_coupon_mobile($item['goodsno'],$item['category'],'view'));
			}else{
				$tmp = array_merge((array)$tmp,(array)$this->get_coupon_mobile($item['goodsno'],$item['category'],$mode));
			}
		}

		if($m_no && $mode == "order") foreach($this->item as $item){
			$tmp = array_merge((array)$tmp,(array)$this->get_offline($item['goodsno'],$item['category']));
		}

		if($tmp)foreach($tmp as $k => $v){
			if (empty($v)) continue;
			$this->arCoupon[$v['couponcd']] = $v;
			$this->arCouponGoods[$v['couponcd']]['arGoodsno'][$v['goodsno']] = $v['goodsno'];
		}

		if($this->arCoupon){
			foreach($this->arCoupon as $cidx => $data){
				$sale = $reserve = 0;
				if(!$data['ability']) $ability = 'sale';
				else $ability = 'reserve';
				foreach($this->item as $idx => $item){
					$arItem[$cidx][$item['goodsno']]['price'] = $item['price'];
					if(!in_array($item['goodsno'],$this->arCouponGoods[$cidx]['arGoodsno']))continue;
					if(($data['eactl']=='1' && $data['coupontype']=='1')|| (substr($data['price'],-1) == '%'&&$data['coupontype']!='1') ){
						$this->arCoupon[$cidx][$ability][$item['goodsno']] += getDcprice($item['price'],$data['price'])*$item['ea'];
					}else{
						if($mode == 'list'){
							$this->arCoupon[$cidx][$ability][$item['goodsno']] = getDcprice($item['price'],$data['price']);
						}else{
							 $this->arCoupon[$cidx][$ability]['order'] = getDcprice($item['price'],$data['price']);
						}
					}
				}

				if(substr($data['price'],-1) != '%'&&$data['coupontype']!='1'){
					if($this->arCoupon[$cidx][$ability]){
						$i=0;
						foreach($this->arCoupon[$cidx][$ability] as $k=>$v){
							if($i!=0)unset($this->arCoupon[$cidx][$ability][$k]);
							$i++;
						}
					}
				}
			}
		}
	}


}
 ?>
