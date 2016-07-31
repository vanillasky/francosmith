<?
class cardCancel {
var $cfg;
var $cpg;
var $tpl;
var $shopdir;
var $cfgMobileShop;
var $cancel_code = 9;		// 주문 취소시 취소 사유 코드 (기본값 9)
var $pg_dir = 'order/card';
var $tax = 0;
var $vat = 0;
var $taxfree = 0;

function cardCancel(){
	$this->cfg = $GLOBALS['cfg'];
	$this->shopdir = substr(dirname(__FILE__),0,-4);
	if($GLOBALS['pg']['id']){
		$this->cpg = $GLOBALS['pg'];
	}else{
		include_once $this->shopdir.'/conf/pg.'.$this->cfg['settlePg'].'.php';
		$this->cpg = $pg;
	}
}

function cancel_pg($ordno){
		$row = $this->get_order_data($ordno);

		if($row['mobilepay']=='y' && in_array($this->cfg['settlePg'],array('allat','allatbasic','lgdacom'))){
			include_once $this->shopdir.'/conf/pg_mobile.'.$this->cfg['settlePg'].'.php';
			$this->cpg = $pg_mobile;
		}

		switch($this->cfg['settlePg']){
			case "allat" :
				if($row['mobilepay']=='y') return $this->cancel_allat_mobile_request($ordno);
				else {
					$this->cancel_allat_request($ordno,1);
					exit;
				}
			break;
			case "allatbasic" :
				if($row['mobilepay']=='y') return $this->cancel_allatbasic_mobile_request($ordno);
				else {
					$this->cancel_allatbasic_request($ordno,1);
					exit;
				}
			break;
			case "dacom" :
				$this->cancel_dacom_request($ordno);
				exit;
			break;
			case "lgdacom" :
				if($row['mobilepay']=='y') return $this->cancel_lgdacom_mobile($ordno);
				else return $this->cancel_lgdacom($ordno);
			break;
			case "inicis" :
				return $this->cancel_inicis($ordno,'관리자 승인취소');
			break;
			case "inipay" :
				return $this->cancel_inipay($ordno,'관리자 승인취소');
			break;
			case "kcp" :
				return $this->cancel_kcp($ordno,'관리자 승인취소');
			break;
			case "agspay" :
				return $this->cancel_agspay($ordno);
			break;
			case "easypay" :
				return $this->cancel_easypay($ordno);
			break;
			case "settlebank" :
				return $this->cancel_SettleBank($ordno);
			break;
		}
}

### 부분취소
function partCancel_pg($ordno,$sno){
		$query = "select pgcancel from ".GD_ORDER_CANCEL." where ordno='".$ordno."' and sno='".$sno."'";
		$data = $GLOBALS['db']->fetch($query);
		
		// 취소 처리 되었는지 체크
		if ($data['pgcancel'] !== 'n') {
			return false;
		}
		else {}

		$row = $this->get_order_data($ordno);

		// 복합과세 적용 - 15.04.28 - su
		$order = new order();
		$order->load($ordno);
		$multitax = $order->getCancelItemTaxWithSno($sno);
		$this->tax = $multitax[kTAX];
		$this->vat = $multitax[kVAT];
		$this->taxfree = $multitax[kTAXFREE];

		switch($this->cfg['settlePg']){
			case "inicis" :
				return $this->partcancel_inicis($ordno,'카드결제 부분취소',$sno,$this->repay);
			break;
			case "inipay" :
				return $this->partcancel_inipay($ordno,'카드결제 부분취소',$sno,$this->repay);
			break;
			case "agspay" :
				return $this->partcancel_agspay($ordno,$sno,$this->repay);
			break;
			case "lgdacom" :
				return $this->partcancel_lgdacom($ordno,'카드결제 부분취소',$sno,$this->repay);
			break;
			case "allat" :
				return $this->partcancel_allat($ordno,1,$sno,$this->repay);
			break;
			case "allatbasic" :
				return $this->partcancel_allatbasic($ordno,1,$sno,$this->repay);
			break;
			case "kcp" :
				return $this->partcancel_kcp($ordno,'카드결제 부분취소',$sno,$this->repay);
			break;
			case "easypay" :
				return $this->partcancel_easypay($ordno,'카드결제 부분취소',$sno,$this->repay);
			break;
		}
}

### 주문정보 불러오기
function get_order_data($ordno){
	if($ordno) return $GLOBALS['db'] -> fetch("select * from ".GD_ORDER." where ordno='".$ordno."'");
}

### 승인취소 요망 db처리
function cancel_db_proc($ordno,$tno=''){
	$settlelog = "{$ordno} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . "결과내용 : PG사 카드승인 취소 요망" . "\n-----------------------------------\n";
	if($tno)$field = ",cardtno='$tno'";
	$GLOBALS['db']->query("update ".GD_ORDER." set step='0',step2='51',settlelog=concat(ifnull(settlelog,''),'$settlelog')".$field." where ordno='$ordno'");
	$GLOBALS['db']->query("update ".GD_ORDER_ITEM." set istep='51' where ordno='".$ordno."'");
}

### 주문취소처리
function cancel_proc($ordno,$msg){
	$res = $GLOBALS['db'] -> query("select * from ".GD_ORDER_ITEM." where ordno='".$ordno."'");
	while($item = $GLOBALS['db'] -> fetch($res)){
		$data['sno'][] = $item['sno'];
		$data['ea'][] = $item['ea'];
	}
	$data['memo'] = $msg;
	$data['name'] = $_COOKIE['member']['name'];
	$data['code'] = $this->cancel_code; // 기타

	### 환불접수 sno
	if($this->no_cancel != '' && !is_null($this->no_cancel)){
		$data['no_cancel'] = $this->no_cancel;
	}

	### 주문 취소 처리
	chkCancel($ordno,$data);

	### 전체취소 일때 결과 저장
	$GLOBALS['db'] -> query("update ".GD_ORDER." set pgcancel='y' where ordno='".$ordno."'");
	$GLOBALS['db'] -> query("update ".GD_ORDER_CANCEL." set pgcancel='y' where ordno='".$ordno."'");

	## 결제 로그
	$settlelog = "{$ordno} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . $msg . "\n-----------------------------------\n";
	$GLOBALS['db'] -> query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'".$settlelog."') where ordno='".$ordno."'");
}

### pg 사별 취소폼 로딩
function cancel_set_req($data){
	$cfg = $this -> cfg;
	$pg = $this -> cpg;
	$cfgMobileShop = $this -> cfgMobileShop;

	if(!$this->tpl){
		include_once $this->shopdir."/Template_/Template_.class.php";
		$this->tpl = new Template_;
	}
	$this->tpl->template_dir = $this->shopdir."/".$this->pg_dir."/".$cfg['settlePg'];
	$this->tpl->compile_dir = $this->shopdir."/Template_/_compiles/".$cfg['tplSkin']."/".$this->pg_dir."/".$cfg['settlePg'];
	$this->tpl->define('cancel',"cancel.htm");

	$this->tpl->assign($data);
	return $this->tpl->fetch('cancel');
}

### 재고 체크
function chkstock($goodsno,$opt1,$opt2,$ea){
	list($usestock) =  $GLOBALS['db'] -> fetch("select usestock from ".GD_GOODS." where goodsno='$goodsno' limit 1");
	if($usestock == "o"){
		list($goodsea) =  $GLOBALS['db'] -> fetch("select stock from ".GD_GOODS_OPTION." where goodsno='$goodsno' and opt1='".mysql_real_escape_string($opt1)."' and opt2='".mysql_real_escape_string($opt2)."' and go_is_deleted <> '1' limit 1");
		if($goodsea >= $ea) return true;
		else return false;
	}else{
		return true;
	}
}

### order item check stock
function chk_item_stock($ordno,$settlekind=''){
	$query = "select sum(ea) as ea,goodsno,opt1,opt2 from ".GD_ORDER_ITEM." where ordno='$ordno' group by goodsno,opt1,opt2";
	$res = $GLOBALS['db'] -> query($query);
	while($data = $GLOBALS['db'] -> fetch($res)){
		if(!$this->chkstock($data['goodsno'],$data['opt1'],$data['opt2'],$data['ea']))return false;
	}
	return true;
}

### 올엣카드 승인취소신청
function cancel_allat_request($ordno,$actmode=0){
	$row = $this->get_order_data($ordno);
	$r_sk = array('c'=>'CARD','o'=>'ABANK','v'=>'VBANK','h'=>'HP');
	$data = array(
		'allat_shop_id' => $this->cpg['id'],
		'allat_order_no' => $ordno,
		'allat_amt' => $row['settleprice'],
		'allat_pay_type' => $r_sk[$row['settlekind']],
		'allat_seq_no' => $row['cardtno'],
		'rootDir' => $this->cfg[rootDir],
		'actmode' => $actmode
	);
	$out = $this->cancel_set_req($data);
	if($out) echo $out;
}

### 올엣카드 부분 승인취소신청
function partcancel_allat($ordno,$actmode=0,$sno,$repay){
	$row = $this->get_order_data($ordno);
	$r_sk = array('c'=>'CARD','o'=>'ABANK','v'=>'VBANK','h'=>'HP');
	$data = array(
		'allat_shop_id' => $this->cpg['id'],
		'allat_order_no' => $ordno,
		'sno' => $sno,
		'price' => $this->price,
		'repay' => $repay,
		'allat_amt' => $this->price,
		'allat_pay_type' => $r_sk[$row['settlekind']],
		'allat_seq_no' => $row['cardtno'],
		'rootDir' => $this->cfg[rootDir],
		'actmode' => $actmode
	);

	$out = $this->cancel_set_req($data);
	if($out) echo $out;
}

### 올엣카드 부분 승인취소신청 리턴
function partcancel_allat_return($ordno,$a_sno,$settlelog,$a_price, $a_repay){
	$row = $this->get_order_data($ordno);

	if (empty($row['repayprice']) === false && $row['pgcancel'] == 'r') {
		$confirm_price = $row[repayprice] - $a_price;
	}else{
		$confirm_price = $row[settleprice] - $a_price;
	}
	$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
	$GLOBALS['db']->query("update ".GD_ORDER." set pgcancel = 'r' , repayprice = '".$confirm_price."' where ordno='$ordno'");

	$rfee = (int)$a_repay-(int)$a_price;

	$GLOBALS['db']->query("update ".GD_ORDER_CANCEL." set
		rprice = '".$a_price."',
		rfee = '".$rfee."',
		ccdt = '".date('Y:m:d H:i:s')."',
		pgcancel = 'r'
		where sno = '".$a_sno."'
	");

	return true;
}

// 올엣카드 승인취소신청
function cancel_allatbasic_request($ordno,$actmode=0){
	$row = $this->get_order_data($ordno);
	$r_sk = array('c'=>'CARD','o'=>'ABANK','v'=>'VBANK','h'=>'HP');
	$data = array(
		'allat_shop_id' => $this->cpg['id'],
		'allat_order_no' => $ordno,
		'allat_amt' => $row['settleprice'],
		'allat_pay_type' => $r_sk[$row['settlekind']],
		'allat_seq_no' => $row['cardtno'],
		'rootDir' => $this->cfg[rootDir],
		'actmode' => $actmode
	);
	$out = $this->cancel_set_req($data);
	if($out) echo $out;
}

// 올엣카드 부분 승인취소신청
function partcancel_allatbasic($ordno,$actmode=0,$sno,$repay){
	$row = $this->get_order_data($ordno);
	$r_sk = array('c'=>'CARD','o'=>'ABANK','v'=>'VBANK','h'=>'HP');
	$data = array(
		'allat_shop_id' => $this->cpg['id'],
		'allat_order_no' => $ordno,
		'sno' => $sno,
		'price' => $this->price,
		'repay' => $repay,
		'allat_amt' => $this->price,
		'allat_pay_type' => $r_sk[$row['settlekind']],
		'allat_seq_no' => $row['cardtno'],
		'rootDir' => $this->cfg[rootDir],
		'actmode' => $actmode
	);

	$out = $this->cancel_set_req($data);
	if($out) echo $out;
}

// 올엣카드 부분 승인취소신청 리턴
function partcancel_allatbasic_return($ordno,$a_sno,$settlelog,$a_price, $a_repay){
	$row = $this->get_order_data($ordno);

	if (empty($row['repayprice']) === false && $row['pgcancel'] == 'r') {
		$confirm_price = $row[repayprice] - $a_price;
	}else{
		$confirm_price = $row[settleprice] - $a_price;
	}
	$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
	$GLOBALS['db']->query("update ".GD_ORDER." set pgcancel = 'r' , repayprice = '".$confirm_price."' where ordno='$ordno'");

	$rfee = (int)$a_repay-(int)$a_price;

	$GLOBALS['db']->query("update ".GD_ORDER_CANCEL." set
		rprice = '".$a_price."',
		rfee = '".$rfee."',
		ccdt = '".date('Y:m:d H:i:s')."',
		pgcancel = 'r'
		where sno = '".$a_sno."'
	");

	return true;
}

### 데이콤 승인취소신청
function cancel_dacom_request($ordno){
	$row = $this->get_order_data($ordno);
	$data = array(
		'mid'=>$this->cpg['id'],
		'oid'=>$ordno,
		'tid'=>$row['cardtno'],
		'ret_url'=>'http://'.$_SERVER['SERVER_NAME'].$this->cfg['rootDir'].'/'.$this->pg_dir.'/dacom/cancel_return.php',
		'hashdata'=> md5($this->cpg['id'].$ordno.$this->cpg['mertkey'])
	);

	$out = $this->cancel_set_req($data);

	if($out){
		echo $out;
		echo "<script>document.forms[0].submit();</script>";
	}
}

### 올엣카드(모바일) 승인취소신청
function cancel_allat_mobile_request($ordno){
	$row = $this->get_order_data($ordno);
	$r_sk = array('c'=>'CARD','o'=>'ABANK','v'=>'VBANK','h'=>'HP');
	$data = array(
		'allat_shop_id' => $this->cpg['id'],
		'allat_order_no' => $ordno,
		'allat_amt' => $row['settleprice'],
		'allat_pay_type' => $r_sk[$row['settlekind']],
		'allat_seq_no' => $row['cardtno']
	);

	require_once($this->shopdir.'/'.$this->pg_dir.'/allat/mobile/Cancel.php');

	if($cardCancelResult == true){
		$this -> cancel_proc($ordno,$settlelog);
		return true;
	}else{
		$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		return false;
	}
}

// 올엣카드(모바일) 승인취소신청
function cancel_allatbasic_mobile_request($ordno){
	$row = $this->get_order_data($ordno);
	$r_sk = array('c'=>'CARD','o'=>'ABANK','v'=>'VBANK','h'=>'HP');
	$data = array(
		'allat_shop_id' => $this->cpg['id'],
		'allat_order_no' => $ordno,
		'allat_amt' => $row['settleprice'],
		'allat_pay_type' => $r_sk[$row['settlekind']],
		'allat_seq_no' => $row['cardtno']
	);

	require_once($this->shopdir.'/'.$this->pg_dir.'/allatbasic/mobile/Cancel.php');

	if($cardCancelResult == true){
		$this -> cancel_proc($ordno,$settlelog);
		return true;
	}else{
		$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		return false;
	}
}

### LG데이콤 승인취소신청
function cancel_lgdacom($ordno){
	$row = $this->get_order_data($ordno);

	if(!$this->cpg['serviceType']) $this->cpg['serviceType'] = "service";
	$data = array(
		'mid'		=> $this->cpg['id'],
		'service'	=> $this->cpg['serviceType'],
		'oid'		=> $ordno,
		'tid'		=> $row['cardtno'],
		'shopdir'	=> $this->shopdir
	);

	if ($row['settlekind'] == 'u') $data['mid'] = $this->cpg['cup_id'];	// 중국 은련카드 결제시 상점 아이디 교체

	require_once($this->shopdir.'/'.$this->pg_dir.'/lgdacom/Cancel.php');

	if($cardCancelResult == true){
		$this -> cancel_proc($ordno,$settlelog);
		return true;
	}else{
		$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		return false;
	}
}

### LG데이콤 부분취소
function partcancel_lgdacom($ordno,$msg,$sno,$repay){
	$row = $this->get_order_data($ordno);

	if (empty($row['repayprice']) === false && $row['pgcancel'] == 'r') {
		$confirm_price = $row[repayprice] - $this->price;
	}else{
		$confirm_price = $row[settleprice] - $this->price;
	}

	if(!$this->cpg['serviceType']) $this->cpg['serviceType'] = "service";

	$data = array(
		'mid'		=> $this->cpg['id'],
		'service'	=> $this->cpg['serviceType'],
		'oid'		=> $ordno,
		'tid'		=> $row['cardtno'],
		'shopdir'	=> $this->shopdir,
		'taxfree'	=> $this->taxfree, // 취소할 면세금액
		'price'		=> $this->price // 취소할 금액
	);

	require_once($this->shopdir.'/'.$this->pg_dir.'/lgdacom/PartialCancel.php');

	$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");

	if($cardCancelResult == true){
		$GLOBALS['db']->query("update ".GD_ORDER." set pgcancel = 'r' , repayprice = '".$confirm_price."' where ordno='$ordno'");

		$rfee = (int)$repay-(int)$this->price;

		$GLOBALS['db']->query("update ".GD_ORDER_CANCEL." set
			rprice = '".$this->price."',
			rfee = '".$rfee."',
			ccdt = '".date('Y:m:d H:i:s')."',
			pgcancel = 'r'
			where sno = '".$sno."'
		");
		return true;
	}else{
		echo '<div style="font:9pt verdana;">'.nl2br($settlelog).'</div>';
		return false;
	}
}

### LG데이콤 (모바일) 승인취소신청
function cancel_lgdacom_mobile($ordno){
	$row = $this->get_order_data($ordno);

	if(!$this->cpg['serviceType']) $this->cpg['serviceType'] = "service";
	$data = array(
		'mid'		=> $this->cpg['id'],
		'service'	=> $this->cpg['serviceType'],
		'oid'		=> $ordno,
		'tid'		=> $row['cardtno'],
		'shopdir'	=> $this->shopdir
	);

	require_once($this->shopdir.'/'.$this->pg_dir.'/lgdacom/mobile/Cancel.php');

	if($cardCancelResult == true){
		$this -> cancel_proc($ordno,$settlelog);
		return true;
	}else{
		$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		return false;
	}
}

### 이니시스 승인취소
function cancel_inicis($ordno,$msg){
	$row = $this->get_order_data($ordno);

	require_once($this->shopdir.'/'.$this->pg_dir.'/inicis/sample/INIpay41Lib.php');
	$inipay = new INIpay41;
	$inipay->m_inipayHome = $this->shopdir.'/'.$this->pg_dir.'/inicis/'; // 이니페이 홈디렉터리
	$inipay->m_type = 'cancel'; // 고정
	$inipay->m_pgId = 'INIpayRECP'; // 고정
	$inipay->m_subPgIp = '203.238.3.10'; // 고정
	$inipay->m_keyPw = '1111'; // 키패스워드(상점아이디에 따라 변경)
	$inipay->m_debug = 'true'; // 로그모드('true'로 설정하면 상세로그가 생성됨.)
	$inipay->m_mid = $this->cpg['id']; // 상점아이디
	$inipay->m_tid = $row['cardtno']; // 취소할 거래의 거래아이디
	$inipay->m_cancelMsg = $msg; // 취소사유
	$inipay->m_uip = getenv('REMOTE_ADDR'); // 고정
	$inipay->startAction();

	##결제로그 저장
	$settlelog = "{$ordno} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . $inipay->m_resultMsg . "\n-----------------------------------\n";
	$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
	if( !strcmp($inipay->m_resultCode,'00') ){
		$this -> cancel_proc($ordno,$msg);
		return true;
	}
	return false;
}

### 이니시스 부분취소
function partcancel_inicis($ordno,$msg,$sno,$repay){ // 주문번호, 메세지, 환불주문건sno, 환불접수된금액
	$row = $this->get_order_data($ordno);

	if (empty($row['repayprice']) === false && $row['pgcancel'] == 'r') {
		$confirm_price = $row[repayprice] - $this->price;
	}else{
		$confirm_price = $row[settleprice] - $this->price;
	}

	require_once($this->shopdir.'/'.$this->pg_dir.'/inicis/sample/INIpay41Lib.php');
	$inipay = new INIpay41;
	$inipay->m_inipayHome = $this->shopdir.'/'.$this->pg_dir.'/inicis'; // 이니페이 홈디렉터리
	$inipay->m_type = 'repay'; // 고정
	$inipay->m_pgId = 'INIpayRPAY'; // 고정
	$inipay->m_subPgIp = '203.238.3.10'; // 고정
	$inipay->m_keyPw = '1111'; // 키패스워드(상점아이디에 따라 변경)
	$inipay->m_debug = 'true'; // 로그모드('true'로 설정하면 상세로그가 생성됨.)
	$inipay->m_mid = $this->cpg['id']; // 상점아이디
	$inipay->m_oldTid = $row['cardtno']; // 원거래 TID
	$inipay->m_uip = getenv('REMOTE_ADDR'); // 고정
	$inipay->m_currency = "WON"; // "WON" 으로 고정
	$inipay->m_price = $this->price; // 취소할 금액
	$inipay->m_merchantReserved1 = "Tax=".$this->vat."&TaxFree=".$this->taxfree; //부가세 & 면세 셋팅
	$inipay->m_confirm_price = $confirm_price; // 부분취소 후 승인될 금액
	$inipay->m_buyerEmail = $row['email']; // 구매자 이메일 주소(변경시에만 입력)
	$inipay->startAction();

	##결제로그 저장
	$settlelog = "\n{$ordno} (" . date('Y:m:d H:i:s') . ") ".$msg."\n-----------------------------------\n" .
				"결과내용 : ".$inipay->m_resultMsg."\n".
				"신거래번호 : ".$inipay->m_tid."\n".
				"재승인금액 : ".$inipay->m_remains."\n".
				"취소금액 : ".$inipay->m_resultprice."\n".
				"부분취소(재승인) 요청 횟수 : ".$inipay->m_cnt_partcancel."\n".
				"\n-----------------------------------\n";
	$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");

	if( !strcmp($inipay->m_resultCode,'00') ){

		$GLOBALS['db']->query("update ".GD_ORDER." set pgcancel = 'r' , repayprice = '".$inipay->m_remains."' where ordno='$ordno'");

		$rfee = (int)$repay-(int)$inipay->m_resultprice;

		$GLOBALS['db']->query("update ".GD_ORDER_CANCEL." set
			rprice = '".$inipay->m_resultprice."',
			rfee = '".$rfee."',
			ccdt = '".date('Y:m:d H:i:s')."',
			pgcancel = 'r'
			where sno = '".$sno."'
		");

		return true;
	} else {
		echo '<div style="font:9pt verdana;">'.nl2br($settlelog).'</div>';
		return false;
	}
}

/**
 * 이니시스(TX5) 승인취소
 *
 * @param int $ordno 주문번호
 * @param string $msg 취소사유
 * @return boolean true or false
 */
function cancel_inipay($ordno,$msg)
{
	//--- 주문 정보
	$row	= $this->get_order_data($ordno);

	//--- 라이브러리 인클루드
	require_once($this->shopdir.'/'.$this->pg_dir.'/inipay/libs/INILib.php');

	//--- INIpay50 클래스의 인스턴스 생성
	$inipay	= new INIpay50;

	//--- 취소 정보 설정
	$inipay->SetField('inipayhome',	$this->shopdir.'/'.$this->pg_dir.'/inipay');	// 이니페이 홈디렉터리
	$inipay->SetField('type', 'cancel');											// 고정 (절대 수정 불가)
	$inipay->SetField('debug', 'true');												// 로그모드('true'로 설정하면 상세로그가 생성됨.)
	$inipay->SetField('mid', $this->cpg['id']);										// 상점아이디
	$inipay->SetField('admin', '1111');												// 비대칭 사용키 키패스워드
	$inipay->SetField('tid', $row['cardtno']);										// 취소할 거래의 거래아이디
	$inipay->SetField('cancelmsg', $msg);											// 취소사유

	//--- 취소 요청
	$inipay->startAction();
	/********************************************************************
	* 취소 결과															*
	*																	*
	* 결과코드 : $inipay->getResult('ResultCode') ("00"이면 취소 성공)	*
	* 결과내용 : $inipay->getResult('ResultMsg') (취소결과에 대한 설명)	*
	* 취소날짜 : $inipay->getResult('CancelDate') (YYYYMMDD)			*
	* 취소시각 : $inipay->getResult('CancelTime') (HHMMSS)				*
	* 현금영수증 취소 승인번호 : $inipay->getResult('CSHR_CancelNum')	*
	* (현금영수증 발급 취소시에만 리턴됨)								*
	********************************************************************/

	//--- 로그 생성
	$settlelog	= '';
	$settlelog	.= '===================================================='.chr(10);
	$settlelog	.= '주문번호 : '.$ordno.chr(10);
	$settlelog	.= '거래번호 : '.$row['cardtno'].chr(10);
	$settlelog	.= '결과코드 : '.$inipay->GetResult('ResultCode').chr(10);
	$settlelog	.= '결과내용 : '.$inipay->GetResult('ResultMsg').chr(10);
	$settlelog	.= '취소날짜 : '.$inipay->GetResult('CancelDate').chr(10);
	$settlelog	.= '취소시각 : '.$inipay->GetResult('CancelTime').chr(10);

	//--- 승인여부 / 결제 방법에 따른 처리 설정
	if($inipay->GetResult('ResultCode') == "00"){
		// PG 결과
		$getPgResult	= true;

		$settlelog	= '===================================================='.chr(10).'PG 취소 처리 : 취소완료시간('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
	} else {
		// PG 결과
		$getPgResult	= false;

		$settlelog	= '===================================================='.chr(10).'PG 취소 처리 : 취소오류시간('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
	}

	//--- 결제로그 저장
	$GLOBALS['db']->query("UPDATE ".GD_ORDER." SET settlelog=concat(ifnull(settlelog,''),'$settlelog') WHERE ordno='$ordno'");

	//--- 디비 취소 처리
	if( $getPgResult === true ){
		$this -> cancel_proc($ordno,$msg);
		return true;
	}
	return false;
}

/**
 * 이니시스(TX5) 부분취소
 *
 * @param int $ordno 주문번호
 * @param string $msg 취소사유
 * @param int $sno 환불주문건sno
 * @param int $repay 환불접수된금액
 * @return boolean true or false
 */
function partcancel_inipay($ordno,$msg,$sno,$repay)
{
	//--- 주문 정보
	$row	= $this->get_order_data($ordno);

	//--- 주문 취소후 승인금액 처리
	if (empty($row['repayprice']) === false && $row['pgcancel'] == 'r') {
		$confirm_price	= $row['repayprice'] - $this->price;
	}else{
		$confirm_price	= $row['settleprice'] - $this->price;
	}

	//--- 라이브러리 인클루드
	require_once($this->shopdir.'/'.$this->pg_dir.'/inipay/libs/INILib.php');

	//--- INIpay50 클래스의 인스턴스 생성
	$inipay	= new INIpay50;

	//--- 취소 정보 설정
	$inipay->SetField('inipayhome',	$this->shopdir.'/'.$this->pg_dir.'/inipay');	// 이니페이 홈디렉터리
	$inipay->SetField('type', 'repay');												// 고정 (절대 수정 불가)
	$inipay->SetField('pgid', 'INIphpRPAY');										// 고정 (절대 수정 불가)
	$inipay->SetField('subpgip','203.238.3.10');									// 고정
	$inipay->SetField('debug', 'true');												// 로그모드('true'로 설정하면 상세로그가 생성됨.)
	$inipay->SetField('mid', $this->cpg['id']);										// 상점아이디
	$inipay->SetField('admin', '1111');												// 비대칭 사용키 키패스워드
	$inipay->SetField('oldtid', $row['cardtno']);									// 취소할 거래의 거래아이디
	$inipay->SetField('currency', 'WON');											// 화폐단위
	$inipay->SetField('price', $this->price);										// 취소금액
	$inipay->SetField('tax', $this->vat);											// 취소 부가세
	$inipay->SetField('taxfree', $this->taxfree);									// 취소 면세
	$inipay->SetField('confirm_price', $confirm_price);								// 승인요청금액
	$inipay->SetField('buyeremail', $row['email']);									// 구매자 이메일 주소

	//--- 재승인 요청
	$inipay->startAction();
	/*********************************************************************
	 * 5. 재승인 결과													 *
	 *                                                                   *
	 * 신거래번호 : $inipay->getResult('TID')                            *
	 * 결과코드 : $inipay->getResult('ResultCode') ("00"이면 재승인 성공)*
	 * 결과내용 : $inipay->getResult('ResultMsg') (결과에 대한 설명)     *
	 * 원거래 번호 : $inipay->getResult('PRTC_TID')                      *
	 * 최종결제 금액 : $inipay->getResult('PRTC_Remains')                *
	 * 부분취소 금액 : $inipay->getResult('PRTC_Price')                  *
	 * 부분취소,재승인 구분값 : $inipay->getResult('PRTC_Type')          *
	 *                          ("0" : 재승인, "1" : 부분취소)           *
	 * 부분취소 요청횟수 : $inipay->getResult('PRTC_Cnt')                *
	*********************************************************************/

	//--- 로그 생성
	$settlelog	= '';
	$settlelog	.= '===================================================='.chr(10);
	$settlelog	.= '주문번호 : '.$ordno.chr(10);
	$settlelog	.= '거래번호 : '.$row['cardtno'].chr(10);
	$settlelog	.= '결과코드 : '.$inipay->GetResult('ResultCode').chr(10);
	$settlelog	.= '결과내용 : '.$inipay->GetResult('ResultMsg').chr(10);
	$settlelog	.= '신거래번호 : '.$inipay->GetResult('TID').chr(10);
	$settlelog	.= '원거래 번호 : '.$inipay->GetResult('PRTC_TID').chr(10);
	$settlelog	.= '최종결제 금액 : '.$inipay->GetResult('PRTC_Remains').chr(10);
	$settlelog	.= '부분취소 금액 : '.$inipay->GetResult('PRTC_Price').chr(10);
	$settlelog	.= '부분취소 구분 : '.$inipay->GetResult('PRTC_Type').'("0" : 재승인, "1" : 부분취소)'.chr(10);
	$settlelog	.= '부분취소 요청횟수 : '.$inipay->GetResult('PRTC_Cnt').chr(10);
	$settlelog	.= '취소날짜 : '.date('Y-m-d H:i:s').chr(10);

	//--- 승인여부 / 결제 방법에 따른 처리 설정
	if($inipay->GetResult('ResultCode') == "00"){
		// PG 결과
		$getPgResult	= true;

		$settlelog	= '===================================================='.chr(10).'카드 부분 취소 처리 : 취소완료시간('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
	} else {
		// PG 결과
		$getPgResult	= false;

		$settlelog	= '===================================================='.chr(10).'카드 부분 취소 처리 : 취소오류시간('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
	}

	//--- 결제로그 저장
	$GLOBALS['db']->query("UPDATE ".GD_ORDER." SET settlelog=concat(ifnull(settlelog,''),'$settlelog') WHERE ordno='$ordno'");

	//--- 디비 취소 처리
	if( $getPgResult === true ){

		// 재 승인 금액
		$GLOBALS['db']->query("UPDATE ".GD_ORDER." SET pgcancel = 'r' , repayprice = '".$inipay->GetResult('PRTC_Remains')."' WHERE ordno='$ordno'");

		// 실제 취소후 금액
		$rfee	= (int)$repay - (int)$inipay->GetResult('PRTC_Price');

		// 취소 데이타 저장
		$GLOBALS['db']->query("UPDATE ".GD_ORDER_CANCEL." SET
			rprice = '".$inipay->GetResult('PRTC_Price')."',
			rfee = '".$rfee."',
			ccdt = '".date('Y:m:d H:i:s')."',
			pgcancel = 'r'
			WHERE sno = '".$sno."'
		");

		return true;
	} else {
		echo '<div style="font:9pt verdana;">'.nl2br($settlelog).'</div>';
		return false;
	}
}

### KCP 승인취소신청
function cancel_kcp($ordno,$msg){
	$row = $this->get_order_data($ordno);

	require_once $this->shopdir."/".$this->pg_dir."/kcp/pp_ax_hub_lib.php";                  // library [수정불가]
	$c_PayPlus = new C_PP_CLI;
	$c_PayPlus->mf_clear();

	$c_PayPlus->mf_set_modx_data( "tno",      $row['cardtno']      ); // KCP 원거래 거래번호
	$c_PayPlus->mf_set_modx_data( "mod_type", "STSC" ); // 원거래 변경 요청 종류
	$c_PayPlus->mf_set_modx_data( "mod_ip",   $_SERVER['REMOTE_ADDR']  ); // 변경 요청자 IP
	$c_PayPlus->mf_set_modx_data( "mod_desc", $mod_desc ); // 변경 사유

	// real url : paygw.kcp.co.kr , test url : testpaygw.kcp.co.kr
	$c_PayPlus->mf_do_tx( $trace_no, $this->shopdir."/".$this->pg_dir."/kcp/payplus", $this->cpg['id'], $this->cpg['key'], '00200000', "",
												'paygw.kcp.co.kr', 8090, "payplus_cli_slib", $ordno,
												$_SERVER['REMOTE_ADDR'], 3, 0, 0 );

	$res_cd  = $c_PayPlus->m_res_cd;  // 결과 코드
	$res_msg = $c_PayPlus->m_res_msg; // 결과 메시지

	if( !strcmp($res_cd,'0000') ){
		$this -> cancel_proc($ordno,$msg);
		return true;
	}
	return false;

}

### KCP 부분취소
function partcancel_kcp($ordno,$msg,$sno,$repay){ // 주문번호, 메세지, 환불주문건sno, 환불접수된금액
	$row = $this->get_order_data($ordno);

	### 취소가능 잔액 계산
	if (empty($row['repayprice']) === false && $row['pgcancel'] == 'r') {
		$repayprice = $row[repayprice];
	}else{
		$repayprice = $row[settleprice];
	}

	$data = array(
		'mod_type'	=> 'RN07',					// 부분취소요청 구분 변수 ( 신용카드 : RN07, 계좌이체 : STPA )
		'tno'		=> $row['cardtno'],			// 원거래번호
		'mod_desc'	=> $msg,					// 취소사유
		'mod_mny'	=> $this->price,			// 취소요청금액
		'rem_mny'	=> $repayprice,				// 취소 가능 잔액
	);

	$crdata['req_tx'] = 'repay';
	require_once $this->shopdir."/".$this->pg_dir."/kcp/receipt/request/cash/pp_cli_hub.php";

	$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");

	if($cardCancelResult == true){
		$GLOBALS['db']->query("update ".GD_ORDER." set pgcancel = 'r' , repayprice = '".$rem_mny."' where ordno='$ordno'");
		$rfee = (int)$repay-(int)$this->price;
		$GLOBALS['db']->query("update ".GD_ORDER_CANCEL." set
			rprice = '".$this->price."',
			rfee = '".$rfee."',
			ccdt = '".date('Y:m:d H:i:s')."',
			pgcancel = 'r'
			where sno = '".$sno."'
		");
		return true;
	}else{
		echo '<div style="font:9pt verdana;">'.nl2br($settlelog).'</div>';
		return false;
	}
}

### 올더게이트 승인취소신청
function cancel_agspay($ordno){
	$row = $this->get_order_data($ordno);

	if ($row['pgCardCd'] == '0100' || $row['pgCardCd'] == '0200') {
		$SubTy = 'isp'; // 국민,BC (ISP안전결제)
		$rApprTm = substr($row['pgAppDt'], 0, 8); //8자리
	} else {
		$SubTy = 'visa3d'; // 신한,삼성,외환,현대,롯데 및 기타카드 (안심클릭)
		$rApprTm = $row['pgAppDt']; //14자리
	}
	$data = array(
		'StoreId'	=> $this->cpg['id'],
		'SubTy'		=> $SubTy,
		'rApprNo'	=> $row['pgAppNo'],
		'rApprTm'	=> $rApprTm,
		'rDealNo'	=> $row['cardtno'],
		'rootDir'	=> $this->cfg[rootDir],
	);

	require_once($this->shopdir.'/'.$this->pg_dir.'/agspay/Cancel.php');

	if($cardCancelResult == true){
		$this -> cancel_proc($ordno,$settlelog);
		return true;
	}else{
		$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		return false;
	}
}

### 올더게이트 부분취소
function partcancel_agspay($ordno,$sno,$repay){ // 주문번호, 환불주문건sno, 환불접수된금액
	$row = $this->get_order_data($ordno);

	if ($row['pgCardCd'] == '0100' || $row['pgCardCd'] == '0200') {
		$SubTy = 'isp'; // 국민,BC (ISP안전결제)
		$rApprTm = substr($row['pgAppDt'], 0, 8); //8자리
	} else {
		$SubTy = 'visa3d'; // 신한,삼성,외환,현대,롯데 및 기타카드 (안심클릭)
		$rApprTm = $row['pgAppDt']; //14자리
	}
	$data = array(
		'StoreId'		=> $this->cpg['id'],
		'SubTy'			=> $SubTy,
		'rApprNo'		=> $row['pgAppNo'],
		'rApprTm'		=> $rApprTm,
		'rDealNo'		=> $row['cardtno'],
		'cancelPrice'	=> $this->price, // 취소할 금액
		'rootDir'		=> $this->cfg[rootDir],
	);

	require_once($this->shopdir.'/'.$this->pg_dir.'/agspay/Cancel.php');

	$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
	if($cardCancelResult == true){
		$GLOBALS['db']->query("update ".GD_ORDER." set pgcancel = 'r' where ordno='$ordno'");
		$rfee = (int)$repay-(int)$this->price;
		$GLOBALS['db']->query("update ".GD_ORDER_CANCEL." set
			rprice = '".$this->price."',
			rfee = '".$rfee."',
			ccdt = '".date('Y:m:d H:i:s')."',
			pgcancel = 'r'
			where sno = '".$sno."'
		");
		return true;
	}else{
		echo '<div style="font:9pt verdana;">'.nl2br($settlelog).'</div>';
		return false;
	}
}


### 이지페이 전체취소
function cancel_easypay($ordno){ // 주문번호, 메세지, 환불주문건sno, 환불접수된금액
	$row = $this->get_order_data($ordno);
	$tr_cd="00201000";	//거래구분
	$mgr_txtype="40";		//거래구분 40:즉시 20:매입요청 30:매입취소 31:부분매입취소
	$org_cno=$row[cardtno];	//PG거래번호
	$client_ip= $_SERVER['REMOTE_ADDR'];

	include($this->shopdir.'/conf/config.php');
	include($this->shopdir.'/conf/pg.'.$cfg[settlePg].'.php');
	require_once($this->shopdir.'/'.$this->pg_dir.'/easypay/inc/easypay_config.php');
	require_once($this->shopdir.'/'.$this->pg_dir.'/easypay/easypay_client.php');
	$easyPay = new EasyPay_Client;
	$easyPay->clearup_msg();

	$easyPay->set_home_dir($g_home_dir);
	$easyPay->set_gw_url($g_gw_url);
	$easyPay->set_gw_port($g_gw_port);
	$easyPay->set_log_dir($g_log_dir);
	$easyPay->set_log_level($g_log_level);
	$easyPay->set_cert_file($g_cert_file);

	 /* ::: 변경관리 요청                                                      */
    /* ---------------------------------------------------------------------- */
    $mgr_data = $easyPay->set_easypay_item("mgr_data");
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_txtype"      , $mgr_txtype       );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_subtype"     , $mgr_subtype      );
    $easyPay->set_easypay_deli_us( $mgr_data, "org_cno"         , $org_cno          );
    $easyPay->set_easypay_deli_us( $mgr_data, "pay_type"        , $pay_type         );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_amt"         , $mgr_amt          );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_bank_cd"     , $mgr_bank_cd      );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_account"     , $mgr_account      );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_depositor"   , $mgr_depositor    );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_socno"       , $mgr_socno        );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_telno"       , $mgr_telno        );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_corp_cd"    , $deli_corp_cd     );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_invoice"    , $deli_invoice     );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_rcv_nm"     , $deli_rcv_nm      );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_rcv_tel"    , $deli_rcv_tel     );
    $easyPay->set_easypay_deli_us( $mgr_data, "req_ip"          , $client_ip        );
    $easyPay->set_easypay_deli_us( $mgr_data, "req_id"          , $req_id           );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_msg"         , $mgr_msg          );

	/* -------------------------------------------------------------------------- */
	/* ::: 실행                                                                   */
	/* -------------------------------------------------------------------------- */
	$opt = "option value";
	$easyPay->easypay_exec($g_mall_id, $tr_cd, $ordno, $client_ip, $opt);
	$res_cd  = $easyPay->_easypay_resdata["res_cd"];    // 응답코드
	$res_msg = $easyPay->_easypay_resdata["res_msg"];   // 응답메시지
	$settlelog = "{$ordno} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . $res_msg . "\n-----------------------------------\n";
	$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
	if($res_cd=='0000') {						//결제성공
		##결제로그 저장
		$this -> cancel_proc($ordno,$settlelog);
		return true;
	}
	return false;

}


### 이지페이 부분승인취소
function partcancel_easypay($ordno,$msg,$sno,$repay){ // 주문번호, 메세지, 환불주문건sno, 환불접수된금액
	//--- 주문 정보
	$row = $this->get_order_data($ordno);

	//--- 주문 취소후 승인금액 처리
	if (empty($row['repayprice']) === false && $row['pgcancel'] == 'r') {
		$confirm_price	= $row['repayprice'] - $this->price;
	}else{
		$confirm_price	= $row['settleprice'] - $this->price;
	}


	$tr_cd="00201000";	//거래구분
	$mgr_txtype="31";		//거래구분 40:즉시 20:매입요청 30:매입취소 31:부분매입취소
	$org_cno=$row[cardtno];	//PG거래번호
	$mgr_amt=$repay;					//부분취소/환불 금액
	$mgr_rem_amt = $confirm_price		;		//부분취소	잔액
	$client_ip= $_SERVER['REMOTE_ADDR'];

	include($this->shopdir.'/conf/config.php');
	include($this->shopdir.'/conf/pg.'.$cfg[settlePg].'.php');
	require_once($this->shopdir.'/'.$this->pg_dir.'/easypay/inc/easypay_config.php');
	require_once($this->shopdir.'/'.$this->pg_dir.'/easypay/easypay_client.php');
	$easyPay = new EasyPay_Client;
	$easyPay->clearup_msg();

	$easyPay->set_home_dir($g_home_dir);
	$easyPay->set_gw_url($g_gw_url);
	$easyPay->set_gw_port($g_gw_port);
	$easyPay->set_log_dir($g_log_dir);
	$easyPay->set_log_level($g_log_level);
	$easyPay->set_cert_file($g_cert_file);

	 /* ::: 변경관리 요청                                                      */
    /* ---------------------------------------------------------------------- */
    $mgr_data = $easyPay->set_easypay_item("mgr_data");
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_txtype"      , $mgr_txtype       );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_subtype"     , $mgr_subtype      );
    $easyPay->set_easypay_deli_us( $mgr_data, "org_cno"         , $org_cno          );
    $easyPay->set_easypay_deli_us( $mgr_data, "pay_type"        , $pay_type         );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_amt"         , $mgr_amt          );
	$easyPay->set_easypay_deli_us( $mgr_data, "mgr_rem_amt"         , $mgr_rem_amt          );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_bank_cd"     , $mgr_bank_cd      );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_account"     , $mgr_account      );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_depositor"   , $mgr_depositor    );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_socno"       , $mgr_socno        );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_telno"       , $mgr_telno        );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_corp_cd"    , $deli_corp_cd     );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_invoice"    , $deli_invoice     );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_rcv_nm"     , $deli_rcv_nm      );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_rcv_tel"    , $deli_rcv_tel     );
    $easyPay->set_easypay_deli_us( $mgr_data, "req_ip"          , $client_ip        );
    $easyPay->set_easypay_deli_us( $mgr_data, "req_id"          , $req_id           );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_msg"         , $mgr_msg          );

	/* -------------------------------------------------------------------------- */
	/* ::: 실행                                                                   */
	/* -------------------------------------------------------------------------- */
	$opt = "option value";
	$easyPay->easypay_exec($g_mall_id, $tr_cd, $ordno, $client_ip, $opt);
	$res_cd  = $easyPay->_easypay_resdata["res_cd"];    // 응답코드
	$res_msg = $easyPay->_easypay_resdata["res_msg"];   // 응답메시지
	$amount=  $easyPay->_easypay_resdata["amount"];				// 취소결제금액
	$settlelog = "{$ordno} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . $msg . "\n-----------------------------------\n";
	$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");

	### 취소가능 잔액 계산
	if (empty($row['repayprice']) === false && $row['pgcancel'] == 'r') {
		$repayprice = $row[repayprice];
	}else{
		$repayprice = $row[settleprice];
	}


	if($res_cd=='0000') {						//결제성공
		##결제로그 저장
		$GLOBALS['db']->query("update ".GD_ORDER." set pgcancel = 'r', repayprice = '".$amount."' where ordno='$ordno'");
		$rfee = (int)$repay-(int)$this->price;
		$GLOBALS['db']->query("update ".GD_ORDER_CANCEL." set
			rprice = '".$amount."',
			rfee = '".$rfee."',
			ccdt = '".date('Y:m:d H:i:s')."',
			pgcancel = 'r'
			where sno = '".$sno."'
		");
		return true;
	}
	else{
		echo "응답코드 : $res_cd <br/>";
		echo "응답메시지 : $res_msg <br/>";


	}
	return false;
}

### 세틀뱅크 Spay 승인취소신청
function cancel_SettleBank($ordno){
	//주문데이터 
	$row = $this->get_order_data($ordno);
	
	$data = array(
		'mid'=>$this->cpg['id'],
		'p_tr_no'=>$row['cardtno'],
		'pg_auth_key'=>$this->cpg['key'],
		'passwd'=>md5($row['cardtno'].$this->cpg['key']),
		'refund_list'=>$row['cardtno'].":".$row['settleprice'],
		'rootDir'=>$this->cfg['rootDir']
	);

	$out = $this->cancel_set_req($data);

	if($out){
		echo $out;
		echo "<script>document.forms[0].submit();</script>";
	}
}

}
?>