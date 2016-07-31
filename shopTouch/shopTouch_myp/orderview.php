<?php
	include dirname(__FILE__) . "/../_shopTouch_header.php";
	@include $shopRootDir . "/lib/page.class.php";

	if (!$sess && !$_COOKIE[v_guest_ordno]) msg("로그인 하셔야 본 서비스를 이용하실 수 있습니다.", "../shopTouch_mem/login.php?returnUrl=$_SERVER[PHP_SELF]");

	@include $shopRootDir . "/conf/pg.cashbag.php";
	$r_exc = $r_kind = array();
	$cashbagprice = 0;
	if($cashbag['paykind'])$r_kind = unserialize($cashbag['paykind']);
	if($cashbag['e_refer'])$r_exc = unserialize($cashbag['e_refer']);

	$query = "
	select * from
		".GD_ORDER." a
		left join ".GD_LIST_BANK." b on a.bankAccount=b.sno
		left join ".GD_LIST_DELIVERY." c on a.deliveryno=c.deliveryno
	where a.ordno = '$_GET[ordno]'
	";
	$data = $db->fetch($query,1);

	if (!$data) msg("해당 주문이 존재하지 않습니다",-1);

	### 권한 체크
	if ($sess[m_no]){
		if ($data[m_no]!=$sess[m_no]) msg("접근권한이 없습니다",-1);
	} else {
		if ($data[nameOrder]!=$_COOKIE[v_guest_nameOrder] || $data[m_no]) msg("접근권한이 없습니다",-1);
	}

	$query = "
	select count(*) from
		".GD_ORDER_ITEM."
	where
		ordno = '$_GET[ordno]' and istep < 40
		";
	list($icnt) = $db->fetch($query);

	$query = "
	select b.*, a.* from
		".GD_ORDER_ITEM." a
		left join ".GD_GOODS." b on a.goodsno=b.goodsno
	where
		a.ordno = '$_GET[ordno]'
	";
	$res = $db->query($query);
	while ($sub=$db->fetch($res)){
		$item[] = $sub;
		if(substr($sub[coupon],-1) == '%') $sub[coupon] = getDcprice($sub[price],$sub[coupon]);
		if(substr($sub[coupon_emoney],-1) == '%') $sub[coupon_emoney] = getDcprice($sub[price],$sub[coupon_emoney]);

		if($icnt == 0){ //모든 주문상품이 취소,환불일 경우
			$goodsprice += $sub[price] * $sub[ea];
			$memberdc += $sub[memberdc] * $sub[ea];
			$coupon += $sub[coupon] * $sub[ea];
			$coupon_emoney += $sub[coupon_emoney] * $sub[ea];

		}else if ($sub[istep]<40){
			$goodsprice += $sub[price] * $sub[ea];
			$memberdc += $sub[memberdc] * $sub[ea];
			$coupon += $sub[coupon] * $sub[ea];
			$coupon_emoney += $sub[coupon_emoney] * $sub[ea];

			if( in_array($sub['goodsno'],$r_exc) ) $minus += $sub[price] * $sub[ea];
			$cashbagprice += $sub[price] * $sub[ea];
		}
	}
	$cashbagprice -= $minus;

	@include $shopRootDir . "/conf/config.pay.php";
	@include $shopRootDir . "/conf/pg.$cfg[settlePg].php";
	@include $shopRootDir . "/conf/pg.escrow.php";
	@include $shopRootDir . "/conf/egg.usafe.php";

	if($set['delivery']['deliverynm'] == '')$set['delivery']['deliverynm'] = '기본배송';

	if($data[step2] == '50' || $data[step2] == '54'){

		$r_deli = explode('|',$set['r_delivery']['title']);

	}

	### 현금영수증
	$r_type = array(
			"a"	=> "NBANK",
			"o"	=> "ABANK",
			"v"	=> "VBANK",
			);
	$cashReceipt['type'] = $r_type[$data['settlekind']];

	if ($set['receipt']['compType'] == '1'){ // 면세/간이사업자
		$cashReceipt['supply'] = $data['prn_settleprice'];
		$cashReceipt['vat'] = 0;
	}
	else { // 과세사업자
		$cashReceipt['supply'] = round($data['prn_settleprice'] / 1.1);
		$cashReceipt['vat'] = $data['prn_settleprice'] - $cashReceipt['supply'];
	}

	if ($data['cashreceipt_ectway'] == 'Y')
	{
		$data['cashreceipt'] = '-';
		$tpl->define('cash_receipt',"proc/_cashreceipt.htm");
	}
	else if ($set['receipt']['publisher'] != 'seller'){
		$tpl->define('cash_receipt',"order/cash_receipt/{$cfg[settlePg]}.htm");
	}
	else if ($set['receipt']['publisher'] == 'seller'){
		@include $shopRootDir . '/lib/cashreceipt.class.php';
		if (class_exists('cashreceipt'))
		{
			$cashreceipt = new cashreceipt();
			$cashreceipt->prnUserReceipt($_GET['ordno']);
			$tpl->assign('receipt',$cashreceipt);
		}
		$tpl->define('cash_receipt',"proc/_cashreceipt.htm");
	}

	### 세금계산서
	if ( $set[tax][useyn] == 'y' ){
		$tmp = 0;
		if ( $set[tax][ "use_{$data[settlekind]}" ] == 'on' ) $tmp++;
		if ( in_array($set[tax][step], array('1', '2', '3', '4')) && $data[step] >= $set[tax][step] && !$data[step2] ) $tmp++;
		list($cnt) = $db->fetch("select count(*) from ".GD_ORDER_ITEM." where ordno='$_GET[ordno]' and tax='1'");
		if ( $cnt >= 1 ) $tmp++;
		if ( $tmp == 3 ) $data[taxapp] = 'Y';
		if (is_object($cashreceipt) && $cashreceipt->writeable != 'true') $data['taxapp'] = 'N'; // 현금영수증 발행중이면 세금계산서 신청불가
	}

	$data[taxmode] = '';
	$query = "select name, company, service, item, busino, address, regdt, agreedt, printdt, price, step, doc_number from ".GD_TAX." where ordno='$_GET[ordno]' order by sno desc limit 1";
	$res = $db->query($query);
	if ( $db->count_($res) ){
		$data[taxmode] = 'taxview';
		$taxed = $db->fetch($res);
	}
	else if ( $data[taxapp] == 'Y' ) $data[taxmode] = 'taxapp';

	$data[settleprice] = $data[prn_settleprice];
	$data[goodsprice] = $goodsprice; $data[memberdc] = $memberdc;
	if($coupon)$data[coupon] = $coupon;

	if($data[phoneReceiver])$data['phone'] = explode('-',$data['phoneReceiver']);
	if($data[mobileReceiver])$data['mobile'] = explode('-',$data['mobileReceiver']);
	if($data[zipcode])$data['postcode'] = explode('-',$data['zipcode']);

	if($cfg[settlePg]){
		$tmp = preg_split("/[\n]+/", $data[settlelog]);
		foreach($tmp as $v)if(preg_match('/KCP 거래번호 : /',$v))$data[tno] = str_replace('KCP 거래번호 : ','',$v);

		if($cfg['settlePg'] == 'allat' && preg_match('/거래번호 : (.*)/', $data['settlelog'], $matched)){
			$data['tno'] = $matched[1];
		}
	}

	if($data['step2'] == 50 || $data['step2'] == 54)$resettleAble = true;
	if($cfg['autoCancelFail'] > 0){
		$ltm = toTimeStamp($data['orddt']) + 3600 * $cfg['autoCancelFail'] ;
		if($ltm < time()){
			$resettleAble = false;
		}
	}else{
		$resettleAble = false;
	}

	### PG 결제실패사유
	if($data['step'] == 0 && $data['step2'] == 54 && $data['settlelog']){
		if(preg_match('/결과내용 : (.*)\n/',$data['settlelog'], $matched)){
			$data['pgfailreason'] = $matched[1];
		}
	}

	### 캐쉬백 적립
	if(
		$cashbag['use'] == "on" &&
		$cashbag['code'] != null &&
		$data['cbyn'] == 'N' &&
		$data['step'] == '4' &&
		$data['step2'] == '0' &&
		in_array($data['settlekind'],$r_kind) &&
		$cashbagprice > 0

	) $ableCashbag = 1;

	$r_savetype = array(
		'ord' => 'orddt',
		'inc' => 'cdt',
		'deli' => 'ddt'
	);
	$r_savepriod = array(
		'mon' => 'month',
		'day' => 'day'
	);
	if($cashbag['savetype'] && $cashbag['savepriodtype'] && $cashbag['savepriod'] && $ableCashbag){
		$tmp = $data[$r_savetype[$cashbag['savetype']]];
		$tmp = strtotime($tmp);
		$tmp = strtotime("+".$cashbag['savepriod']." ".$r_savepriod[$cashbag['savepriodtype']],$tmp);
		if($tmp < time()){
			$ableCashbag = 0;
		}
	}
	if( $data[orddt] && $cashbag[savedt] && $ableCashbag ){
		if( $cashbag[savedt] > str_replace('-','',substr($data[orddt],0,10)) ){
			$ableCashbag = 0;
		}
	}

	if($data[cbyn] == 'Y'){
		$query = "select tno, add_pnt, pnt_app_time from " . GD_ORDER_OKCASHBAG . " where ordno='".$_GET['ordno']."' limit 1";
		list($data[oktno], $data[add_pnt], $data[pnt_app_time]) = $db->fetch($query);
	}
	$authdata = md5($pg[id].$data[cardtno].$pg[mertkey]); // dacom 다이렉트 매출전표 출력 인증문자열 생성

	setcookie("v_guest_ordno", '', 0, '/');
	setcookie("v_guest_nameOrder", '', 0, '/');

	$tpl->assign('authdata',$authdata);  // dacom 다이렉트 매출전표 출력 인증문자열 생성
	$tpl->assign($data);
	$tpl->assign('item',$item);
	$tpl->assign('taxed',$taxed);
	$tpl->print_('tpl');

?>