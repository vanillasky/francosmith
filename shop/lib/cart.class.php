<?

class old_Cart
{
	var $isDirect = 0;
	var $item;
	var $dc;
	var $delivery = 0;
	var $item_keys = array("goodsno","opt","addopt","ea");
	var $coupon;
	var $excep;
	var $excate;
	var $dcprice;
	var $tot_about_dc_price;

	var $dcStdAmt;
	var $dcType;
	var $addEmoneyType;
	var $addEmoneyStdAmt;
	var $freeDeliveryFee;
	var $freeDeliveryFeeStdAmt;

	function Cart($isDirect=0)
	{
		$this->isDirect = $isDirect;
		$this->gd_cart = (!$isDirect) ? "gd_cart" : "gd_cart_direct";

		if ($_COOKIE[$this->gd_cart]){
			$this->ritem = unserialize(base64_decode($_COOKIE[$this->gd_cart]));
			$this->getItems();
		}
	}

	function reset()
	{
		$this->getItems();
		setcookie($this->gd_cart,base64_encode(serialize($this->ritem)),0,'/');
	}

	function emptyCart()
	{
		setcookie($this->gd_cart,'',time() - 3600,'/');
	}

	function getItems()
	{
		global $set;
		if (!$this->ritem) return;
		foreach ($this->ritem as $k=>$v){
			$v = array_combine($this->item_keys,$v);
			if($v[goodsno]){
				$query = "
				select
					goodsnm,img_s img,price,reserve,delivery_type,goods_delivery,use_emoney,todaygoods,tg.tgsno,tg.goodstype,tg.usememberdc
				from
					".GD_GOODS." as a
					left join ".GD_GOODS_OPTION." as b on a.goodsno=b.goodsno
					left join ".GD_TODAYSHOP_GOODS." as tg on a.goodsno=tg.goodsno
				where
					a.goodsno=$v[goodsno]
					and opt1='".mysql_real_escape_string($v[opt][0])."'
					and opt2='".mysql_real_escape_string($v[opt][1])."'
				";

				if ($v[addopt]) foreach ($v[addopt] as $v2) $v[addprice] += $v2[price];
				$data = $GLOBALS[db]->fetch($query,1);

				### 상품 적립금 계산
				if($set['emoney']['useyn'] != 'n'){
					if(!$data['use_emoney']){
						if($set['emoney']['chk_goods_emoney'] == 0){
							$data['reserve'] = getDcprice($data[price],($set['emoney']['goods_emoney']+0).'%');
						}else{
							$data['reserve'] = $set['emoney']['goods_emoney']+0;
						}
					}
				}else{
					$data['reserve'] = 0;
				}

				$v = array_merge($v,$data);
				$this->item[$k] = $v;
			}
		}
	}

	function addCart($goodsno,$opt,$addopt,$ea)
	{
		if ($opt){
			$opt = get_magic_quotes_gpc() ? array_map("stripslashes",$opt) : $opt;
			$opt = explode("|",implode("|",$opt));
		}
		if ($addopt){
			foreach ($addopt as $v){
				list ($tmp[sno],$tmp[optnm],$tmp[opt],$tmp[price]) = explode("^",$v);
				$r_addopt[] = $tmp;
				$addprice += $tmp[price];
			}
			$addopt = $r_addopt;
		}

		if (!$this->isDirect){
			for ($i=0;$i<count($this->item);$i++){ $data = $this->item[$i];
				if ($goodsno==$data[goodsno] && $opt==$data[opt] && $addopt==$data[addopt]){
					return;
				}
				if ($goodsno==$data[goodsno] && $opt==$data[opt]){
					$tmp_ea += $data[ea];
				}
			}
		}

		if($tmp_ea)$new_ea = $tmp_ea + $ea;
		else $new_ea = $ea;

		### 재고체크
		$this->chkStock($goodsno,$opt[0],$opt[1],$new_ea,'add');

		$arr	= array($goodsno,$opt,$addopt,$ea);

		if (!$this->isDirect) $this->ritem[] = $arr;
		else $this->ritem[0] = $arr;

		$this->reset();
	}

	function modCart($ea)
	{
		for ($i=0;$i<count($this->item);$i++){
			$data = $this->item[$i];
			$ea_new = ($ea[$i]) ? $ea[$i] : 1;
			$tmp_ea[$data['goodsno']] += $ea_new;
		}
		for ($i=0;$i<count($this->item);$i++){ $ici = $this->item[$i];
			$ea_new = ($ea[$i]) ? $ea[$i] : 1;

			### 잔여재고체크
			$this->chkStock($ici[goodsno],$ici[opt][0],$ici[opt][1],$tmp_ea[$ici['goodsno']]);
			$this->ritem[$i][3] = $ea_new;
		}
		$this->reset();
	}

	function chkStock($goodsno,$opt1='',$opt2='',$ea=1,$mode='')
	{
		if (!$ea) $ea = 1;

		// 쿼리 수정 2011-01-26 by extacy
		$query = "
		select

			a.goodsnm, a.usestock, a.runout, a.todaygoods, a.min_ea, a.max_ea,
			b.stock,
			tg.tgsno, tg.startdt, tg.enddt

		from ".GD_GOODS." as a
		left join ".GD_GOODS_OPTION." as b on a.goodsno=b.goodsno and go_is_deleted <> '1' and go_is_display = '1'
		left join ".GD_TODAYSHOP_GOODS." AS tg ON a.goodsno=tg.goodsno
		where
			a.goodsno='$goodsno' and opt1='".mysql_real_escape_string($opt1)."' and opt2='".mysql_real_escape_string($opt2)."'
		";

		extract($GLOBALS[db]->fetch($query)); //list ($goodsnm,$usestock,$runout, $todaygoods,$min_ea,$max_ea,$stock, $tgsno) = $GLOBALS[db]->fetch($query);	//2011-01-26 by extacy

		$goodsnm = addslashes($goodsnm);

		### 기존 장바구니 상품에서 동일상품 구매 개수 체크
		if (!$this->isDirect || !$ea){
			for ($i=0;$i<count($this->item);$i++){ $data = $this->item[$i];
				if ($goodsno==$data[goodsno] && $opt1==$data[opt][0] && $opt2==$data[opt][1]) $tmp = $i;
			}
		}

		// 오류 발생시 리턴 url
		if ($todaygoods == 'y') {
			$goodsurl = '../todayshop/today_goods.php?tgsno='.$tgsno;
		}
		else {
			$goodsurl = '../goods/goods_view.php?goodsno='.$goodsno;
		}

		// 구매 가능 기간 체크 2011-01-26
		if ($todaygoods == 'y') {
			$_now = time();

			$_startdt = ($startdt) ? strtotime($startdt) : 0;	// 투데이샵 상품의 진행기간 미입력시 DB값은 null 이어야 함
			$_enddt = ($enddt) ? strtotime($enddt) : 0;

			if ($_startdt > 0 && $_startdt > $_now) {
				msg("{$goodsnm} 상품은 {$startdt} 부터 구매 가능합니다.",$goodsurl);
			}

			if ($_enddt > 0 && $_enddt < $_now) {
				msg("{$goodsnm} 상품은 판매마감 되었습니다.",$goodsurl);
			}

		}	// if 2011-01-26

		### 최소,최대구매수량체크
		if($ea < $min_ea) {
			if($mode == 'add' || $this->isDirect){
				msg("{$goodsnm} 상품의 최소구매수량은 {$min_ea}개 입니다.",$goodsurl);
			}else{
				msg("{$goodsnm} 상품의 최소구매수량은 {$min_ea}개 입니다.",'../goods/goods_cart.php');
			}
		}
		else if($max_ea!='0' && $ea > $max_ea) {
			if($mode == 'add' || $this->isDirect){
				msg("{$goodsnm} 상품의 최대구매수량은 {$max_ea}개 입니다.",$goodsurl);
			}else{
				msg("{$goodsnm} 상품의 최대구매수량은 {$max_ea}개 입니다.",'../goods/goods_cart.php');
			}
		}

		if( basename($_SERVER['PHP_SELF']) != "cartMove_proc.php" ){
			if ($runout) msg("{$goodsnm}은 품절된 상품입니다",$goodsurl);
			if ($usestock && $ea > $stock){
				if ($stock>0){
					$this->ritem[$tmp][3] = $stock;
					$this->reset();

					if($mode == 'add' || $this->isDirect){
						msg("{$goodsnm} 상품의 잔여 재고는 {$stock}개입니다",$goodsurl);
					}else{
						msg("{$goodsnm} 상품의 잔여 재고는 {$stock}개입니다",'../goods/goods_cart.php');
					}

				}else msg("{$goodsnm} 상품의 잔여 재고가 존재하지 않습니다",$goodsurl);
			}
		}
	}

	function delCart($idx)
	{
		array_splice($this->ritem,$idx,1);
		$this->reset();
	}

	function chkCategory($category,$arr)
	{
		if($category)foreach($category as $v){
			$len = strlen($v);
			for($i=3;$i <= $len;$i=$i+3){
				$cate = substr($v,0,$i);
				if(in_array($cate,$arr)){
					return true;
				}
			}
		}
		return false;
	}

	##2011/09/08 회원그룹별 할인율 kmn
	function getAddGrpStdAmt( $level)
	{
		global $db;
		$query = "select dc_type, dc_std_amt, add_emoney_type, add_emoney_std_amt, free_deliveryfee, free_deliveryfee_std_amt from gd_member_grp where level='".$level."'";
		$ret = $db->_select($query);

		$this->dcType = $ret[0]['dc_type'];
		$this->dcStdAmt= $ret[0]['dc_std_amt'];
		$this->addEmoneyType = $ret[0]['add_emoney_type'];
		$this->addEmoneyStdAmt= $ret[0]['add_emoney_std_amt'];
		$this->freeDeliveryFee = $ret[0]['free_deliveryfee'];
		$this->freeDeliveryFeeStdAmt= $ret[0]['free_deliveryfee_std_amt'];
	}

	function calcu()
	{
		global $sess;

		include dirname(__FILE__)."/../conf/config.pay.php";
		$this->special_discount_amount = $this->totalprice = $this->goodsprice = $this->bonus = $this->dcprice = 0;
		$arr_excep = $arr_excate = array();
		if($this->excep) $arr_excep=@explode(',',$this->excep);
		if($this->excate) $arr_excate=@explode(',',$this->excate);
		for ($i=0;$i<count($this->item);$i++){
			$data = $this->item[$i];
			$goodsprice = ($data[price] + $data[addprice]) * $data[ea];
			$this->goodsprice += $goodsprice;
			$this->bonus += $data[reserve] * $data[ea];
			if(!$this->dc) $this->dc = 0;

			// 회원 할인 적용 여부 체크
			$_able_member_dc = true;

			// 투데이샵 상품 중 회원 할인 지원 여부 체크
			if ($data['todaygoods']=='y' && $data['usememberdc']=='n') {
				$_able_member_dc = false;
			}

			// 상품별 회원 혜택 제외 설정
			if ($data['exclude_member_discount']) {
				$_able_member_dc = false;
			}

			// 회원 할인금액 계산
			if ($_able_member_dc) {
				### 회원 할인 계산
				$query = "select category from ".GD_GOODS_LINK." where goodsno='".$data['goodsno']."' and category";
				$res = $GLOBALS['db'] ->query($query);
				while($tmp = $GLOBALS['db'] -> fetch($res)){
					$data['category'][] = $tmp['category'];
				}
				if( !in_array($data['goodsno'],$arr_excep) && !$this ->chkCategory($data['category'],$arr_excate) )
				{
					$this->item[$i]['memberdc'] = getDcprice($data['price'],$this->dc);
					$this->dcprice += $this->item[$i]['memberdc'] * $data['ea'];
					$tmp_goods_price += $data['price'] * $data['ea'];
				}
			}
			else {
				$this->item[$i]['memberdc'] = 0;
			}

			// 상품별 할인
			$discount = Clib_Application::getModelClass('goods_discount')->load($data['goodsno'])->getDiscountAmount($data, $sess['level']);
			$this->item[$i]['special_discount_amount'] = (int)$discount;
			$this->special_discount_amount += (int)$discount * $data['ea'];

			//페이코 간편구매 주문시 할인값 초기화
			if($GLOBALS['_POST']['paycoType']=='CHECKOUT'){
				$this->item[$i]['memberdc'] = $this->item[$i]['special_discount_amount'] = $this->special_discount_amount = $this->dcprice = 0;
			}
		}
		if(!$this -> coupon) $this -> coupon = 0;
		if(!$this -> dcprice) $this -> dcprice = $this -> dc =  0;

		### 쿠폰 & 회원할인 중복 사용여부
		@include dirname(__FILE__)."/../conf/coupon.php";
		$ableDc = $ableCoupon =  true;
		if( $this -> coupon  &&  $this -> dc ){
			if( !$cfgCoupon['use_yn'] || ( $cfgCoupon['range'] != '2' && $cfgCoupon['use_yn'] ) )$ableDc = true;
			else $ableDc = false;
			if( $cfgCoupon['range'] != '1' && $cfgCoupon['use_yn'] )$ableCoupon = true;
			else $ableCoupon = false;
		}
		if(!$ableDc){
			$this -> dcprice = 0;
			foreach($this->item as $k => $v) $this->item[$k]['memberdc'] = 0;
		}
		if(!$ableCoupon) $this -> coupon = 0;

		$this->totalprice = $this->goodsprice;

		##2011/09/08 회원그룹별 할인율 kmn
		global $sess;
		$this->getAddGrpStdAmt($sess['level'] );

		switch($this->dcType){
			case 'goods': $tmp_price = $tmp_goods_price; break;
			case 'settle_amt': $tmp_price = $this->totalprice; break;
			default: $tmp_price = 0; break;
		}
		if( $tmp_price < $this->dcStdAmt ) {
			$this->dcprice = 0;
			for($i=0, $il = count($this->item); $i<$il; $i++){
				$this->item[$i]['memberdc'] = 0;
			}
		}

		### 어바웃 쿠폰 금액 계산
		$this->calcuAboutDc();
	}

	function chkCoupon()
	{
		/*
		// @임시 : 사용되지 않음
		foreach ($this->item as $k=>$v){
			$query = "
			select coupon from
				".GD_COUPON_GOODS." a,
				".GD_GOODS." b
			where
				a.goodsno=b.goodsno
				and a.m_no='{$GLOBALS[sess][m_no]}'
				and a.goodsno='$v[goodsno]'
				and (!b.coupon_date || b.coupon_date>=curdate()+0)
			";
			list($goodsCoupon) = $GLOBALS[db]->fetch($query);
			$this->item[$k][goodsCoupon] = $goodsCoupon;
		}
		*/
	}

	function chkOrder()
	{
		if(!$_POST['settlekind']) $err = "결제수단을 선택해주세요";
		if(count($this->item) == 0) $err = "장바구니가 비어있습니다.";
		if(!$_POST['nameOrder']) $err = "주문자명을 입력해주세요.";
		if( !$_POST['phoneOrder'][0] || !$_POST['phoneOrder'][1] || !$_POST['phoneOrder'][2] ) $err = "주문자 전화번호를 입력해주세요.";
		if( !$_POST['mobileOrder'][0] || !$_POST['mobileOrder'][1] || !$_POST['mobileOrder'][2] ) $err = "주문자 휴대폰번호를 입력해주세요.";
		if( !$_POST['email'] ) $err = "주문자 이메일을 입력해주세요.";
		if( !$_POST['nameReceiver'] ) $err = "수령자명을 입력해주세요.";
		// 투데이샵에서 쿠폰 주문시 시작
		if($this->item[0]['goodstype']=='goods') {

			if( !$_POST['zipcode'][0] || !$_POST['zipcode'][1] || !$_POST['address'] || !$_POST['address_sub'] ) $err = "수령자주소를 입력해주세요.";
			if( !$_POST['phoneReceiver'][0] || !$_POST['phoneReceiver'][1] || !$_POST['phoneReceiver'][2] ) $err = "수령자 전화번호를 입력해주세요.";
		}

		// 투데이샵에서 쿠폰 주문시 끝
		if( !$_POST['mobileReceiver'][0] || !$_POST['mobileReceiver'][1] || !$_POST['mobileReceiver'][2] ) $err = "수령자 휴대번호를 입력해주세요.";

		if($err) msg($err,-1);
	}

	function calcuAboutDc()
	{
		$about_coupon = Core::loader('about_coupon');
		if (!$about_coupon->use || !$_COOKIE['about_cp']) return 0;

		$this->tot_about_dc_price  = 0;
		$about_dc_price = 0;
		for ($i=0;$i<count($this->item);$i++){
			$data = $this->item[$i];

			$about_dc_price = (int) getDcprice($data['price'], $about_coupon->sale);
			$this->item[$i]['about_dc_price'] = $about_dc_price;
			$this->tot_about_dc_price  += (int) ($about_dc_price * $data['ea']);
		}
	}

	/**
		2011-09-28 by x-ta-c
		장바구니의 구매 상품의 적립금을 재 계산합니다.
		선택 구매가 가능하여 db 장바구니가 필수 이므로, 이곳에서는 method 만 생성함.
	 */
	function resetReserveAmount() { return; }
}


require_once(dirname(__FILE__).'/db_cart.class.php');

?>
