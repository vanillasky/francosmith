<?

include "../lib/library.php";
include "../conf/config.php";
@include_once "../conf/config.pay.php";

if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
	$_POST = validation::xssCleanArray($_POST, array(
		validation::DEFAULT_KEY	=> 'text',
		'contents' => array('html', 'ent_quotes'),
		'subject'=> array('html', 'ent_quotes'),
	));
}

$mobile		= @implode("-",$_POST[mobile]);
$mailling	= ($_POST[mailling]) ? "y" : "n";
$sms		= ($_POST[sms]) ? "y" : "n";
$_POST[mode] = ($_GET[mode] ? $_GET[mode] : $_POST[mode] );

if ( $_POST[mode] != 'taxprint' && $_POST[mode] != 'getTaxbill' ) echo '<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">';

switch ($_POST[mode]){

	### 고객 주문배송확인 처리
	case "confirm":
		include "../conf/pg.escrow.php";
		$order_confirm = "client";
		$data = $db->fetch("select * from ".GD_ORDER." where ordno='$_POST[ordno]'");
		if ($data[step]!=3 || $data[step2]) msg("배송확인이 가능한 주문상태가 아닙니다",-1);
		ctlStep($_POST[ordno],4);
		setStock($_POST[ordno]);
		set_prn_settleprice($_POST[ordno]);
		msg("주문확인처리가 완료되었습니다");
		if ($data[escrowyn]=="y"){
			$db->fetch("update ".GD_ORDER." set escrowconfirm=2 where ordno='$_POST[ordno]'");
			switch ($cfg[settlePg]){
				case "allat":
					echo "<script>window.open('https://www.allatpay.com/servlet/AllatBiz/helpinfo/escrow_buy_confirm.jsp?menu_id=idH26')</script>";
					break;
				case "allatbasic":
					echo "<script>window.open('https://www.allatpay.com/servlet/AllatBiz/helpinfo/escrow_buy_confirm.jsp?menu_id=idH26')</script>";
				break;
				case "inicis":
					if($escrow[type] == "INI") {
						echo "<script>window.open('../order/card/inicis/ini_escrow_confirm.php?tid=$data[escrowno]&ordno=$_POST[ordno]','','width=640,height=250')</script>";
					} else {
						echo "<script>window.open('../order/card/inicis/escrow_confirm.php?tid=$data[escrowno]','','width=520,height=550')</script>";
					}
					break;
				case "inipay":
					echo "<script>window.open('../order/card/inipay/escrow_confirm.php?ordno=$_POST[ordno]','','width=630,height=250')</script>";
					break;
				case "agspay":
					echo "<script>window.open('../order/card/agspay/escrow_confirm.php?ordno=$_POST[ordno]','','width=520,height=200')</script>";
					break;
				case "dacom":
					echo "<SCRIPT language=JavaScript>document.location.replace('../order/card/dacom/escrow_buy_gate.php?ordno={$_POST[ordno]}&ret_path=" . urlencode($_SERVER[HTTP_REFERER]) . "');</SCRIPT>";
					exit;
					break;
				case "lgdacom":
					echo "<SCRIPT language=JavaScript>document.location.replace('../order/card/lgdacom/escrow_buy_gate.php?ordno={$_POST[ordno]}&ret_path=" . urlencode($_SERVER[HTTP_REFERER]) . "');</SCRIPT>";
					exit;
					break;
				case "kcp":
					echo "<script>window.open('../order/card/kcp/escrow_confirm.php?ordno=".$_POST['ordno']."','','width=620,height=440')</script>";
					break;
			}
		}
		break;

	### 1:1 문의 게시판 관련
	case "add_qna":

		$query = "
		insert into ".GD_MEMBER_QNA." set
			itemcd		= '$_POST[itemcd]',
			subject		= '$_POST[subject]',
			contents	= '$_POST[contents]',
			m_no		= '$sess[m_no]',
			email		= '$_POST[email]',
			mobile		= '$mobile',
			mailling	= '$mailling',
			sms			= '$sms',
			ordno		= '$_POST[ordno]',
			regdt		= now(),
			ip			= '$_SERVER[REMOTE_ADDR]'
		";
		$db->query($query);

		$db->query("update ".GD_MEMBER_QNA." set parent=sno where sno='" . $db->lastID() . "'");

		/* 샵터치 push 기능 추가 2012-03-03 dn */
		@include_once "../lib/pAPI.class.php";
		$pAPI = new pAPI();
		$push_arr = Array();
		$item_arr = codeitem('question');
		$push_arr['title'] = $cfg['shopName'];
		$push_arr['msg'] = '['.$item_arr[$_POST['itemcd']].']1:1문의가 접수되었습니다.';
		$push_arr['msg_type'] = 'qna';
		$pAPI->noticePush($push_arr);


		if($cfg['ssl_type'] == "free") {//무료보안서버
			$write_end_url = $sitelink->link("mypage/indb.php?mode=wirte_end","regular");
			echo "<script>location.href='$write_end_url';</script>";
		} else {
			echo "<script>alert('정상적으로 등록되었습니다');opener.location.reload();opener.focus();window.close()</script>";
		}
		exit;

		break;

	case "mod_qna":
		// @qnibus 2015-06 회원아이디와 게시글 작성자 일치여부 확인
		list( $m_no ) = $db->fetch("select m_no from ".GD_MEMBER_QNA." where sno = '$_POST[sno]'");
		if ( isset($sess) && $sess['level'] < 80 && $sess['m_no'] != $m_no ) msg('본인이 작성한 1:1문의만 수정하실 수 있습니다.',$code=-1);
		
		$query = "
		update ".GD_MEMBER_QNA." set
			itemcd		= '$_POST[itemcd]',
			subject		= '$_POST[subject]',
			contents	= '$_POST[contents]',
			email		= '$_POST[email]',
			mobile		= '$mobile',
			mailling	= '$mailling',
			sms			= '$sms',
			ordno		= '$_POST[ordno]'
		where sno = '$_POST[sno]'
		";
		$db->query($query);


		if($cfg['ssl_type'] == "free") {//무료보안서버
			$write_end_url = $sitelink->link("mypage/indb.php?mode=modify_end","regular");
			echo "<script>location.href='$write_end_url';</script>";
		} else {
			echo "<script>alert('정상적으로 수정되었습니다');opener.location.reload();opener.focus();window.close()</script>";
		}
		exit;


		break;

	case "wirte_end":
		//무료보안서버 관련 부모창 새로고침을 위해 https 에서 http로 전환
		echo "<script>alert('정상적으로 등록되었습니다');opener.location.reload();opener.focus();window.close()</script>";
		exit;

		break;
	case "modify_end":
		//무료보안서버 관련 부모창 새로고침을 위해 https 에서 http로 전환
		echo "<script>alert('정상적으로 수정되었습니다');opener.location.reload();opener.focus();window.close()</script>";
		exit;

		break;

	case "del_qna":
		// @qnibus 2015-06 회원아이디와 게시글 작성자 일치여부 확인
		list( $m_no ) = $db->fetch("select m_no from ".GD_MEMBER_QNA." where sno = '$_POST[sno]'");
		if ( isset($sess) && $sess['level'] < 80 && $sess['m_no'] != $m_no ) msg('본인이 작성한 1:1문의만 삭제하실 수 있습니다.','close');

		$query = "delete from ".GD_MEMBER_QNA." where sno = '$_POST[sno]'";
		$db->query($query);
		echo "<script>alert('정상적으로 삭제되었습니다');opener.location.reload();opener.focus();window.close()</script>";
		exit;

		break;

	case "reply_qna":

		$query = "
		insert into ".GD_MEMBER_QNA." set
			subject		= '$_POST[subject]',
			contents	= '$_POST[contents]',
			parent		= '$_POST[sno]',
			m_no		= '$sess[m_no]',
			regdt		= now(),
			ip			= '$_SERVER[REMOTE_ADDR]'
		";
		$db->query($query);

		if($cfg['ssl_type'] == "free") {//무료보안서버
			$write_end_url = $sitelink->link("mypage/indb.php?mode=wirte_end","regular");
			echo "<script>location.href='$write_end_url';</script>";
		} else {
			echo "<script>alert('정상적으로 등록되었습니다');opener.location.reload();opener.focus();window.close()</script>";
		}
		exit;

		break;

	case "taxapp":
		//주문데이터
		if(!is_object($order)){
			$order = Core::loader('order');
			$order->load($_POST['ordno']);
		}

		### 가격 계산
		$goodsnm = '';
		$taxPrice = array();
		$price = $price = $supply = $surtax = 0;
		$taxPrice = $order->getRealTaxAmountsPaycoAdd(0, true); //과세정보
		$price = $taxPrice['taxall']; //과세금액
		$supply = $taxPrice['tax']; //공급가
		$surtax = $taxPrice['vat']; //부가세

		if($set['tax']['tax_delivery'] == 'n') {
			list($price, $supply, $surtax) = $order->getRedifineDeliveryExclude($taxPrice);
		}

		if($price < 0 || $supply < 0){
			$price = $supply = $surtax = 0;
		}

		foreach ($order->getOrderItems() as $item) {
			if ($item->hasCancelCompleted()) continue;

			if ( empty($goodsnm) ) $goodsnm = addslashes(strip_tags($item[goodsnm]));

			$cnt++;
		}

		### 제품명
		$cnt -= 1;
		$goodsnm .= ( $cnt ? " 외 {$cnt}건" : "" );

		### 사업자 번호 '-' 제거
		$_POST[busino] = str_replace('-','',$_POST[busino]);

		$query = "
		insert into ".GD_TAX." set
			ordno		= '$_POST[ordno]',
			m_no		= '$order[m_no]',
			name		= '$_POST[name]',
			company		= '$_POST[company]',
			service		= '$_POST[service]',
			item		= '$_POST[item]',
			busino		= '$_POST[busino]',
			address		= '$_POST[address]',
			goodsnm		= '$goodsnm',
			price		= '$price',
			supply		= '$supply',
			surtax		= '$surtax',
			issuedate	= curdate(),
			regdt		= now(),
			ip			= '$_SERVER[REMOTE_ADDR]'
		";
		$db->query($query);
		msg($msg="세금계산서 신청이 완료되었습니다." . "\\n" . "관리자 인증처리 후 인쇄하실 수 있습니다.",$code=$_SERVER[HTTP_REFERER]);
		exit;

		break;

	case "taxprint":

		$query = "
		update ".GD_TAX." set
			step		= '2',
			printdt		= now()
		where
			sno		= '$_GET[sno]'
		";
		$db->query($query);
		exit;

		break;

	case "getTaxbill":

		header("Content-type: text/html; charset=euc-kr");
		include_once dirname(__FILE__)."/../lib/tax.class.php";
		$etax = new eTax();
		$out = $etax->getTaxbill($_GET);
		if (preg_match("/^false/i",$out[1])) header("Status: " . trim(preg_replace("/^false[ |]*-[ |]*/i", "", $out[1])), true, $out[0]);
		else echo trim(preg_replace("/^true[ |]*-[ |]*/i", "", $out[1]));
		echo ""; # 삭제마요
		exit;

		break;

	case "eggcreate":

		### 전자보증보험 발급
		if ($_POST[resno][0] != '' && $_POST[resno][1] != '' && $_POST[eggAgree] == 'Y'){
			include '../lib/egg.class.usafe.php';
			$eggData = array('ordno' => $_POST[ordno], 'issue' => 'Y', 'resno1' => encode($_POST[resno][0],1), 'resno2' => encode($_POST[resno][1],2), 'agree' => $_POST[eggAgree]);
			$eggCls = new Egg( 'create', $eggData );
			if ( $eggCls->isErr == true ){
				msg($msg="보증서 발급이 실패되었습니다. 재발급 받으세요." . "\\n" . "[원인] : {$eggCls->errMsg}",$code=$_SERVER[HTTP_REFERER]);
				exit;
			}
		}

		break;

	case "modReceiver":

		$db->query("update ".GD_ORDER." set nameReceiver='".$_POST['nameReceiver']."',phoneReceiver='".implode('-',$_POST['phoneReceiver'])."',mobileReceiver='".implode('-',$_POST['mobileReceiver'])."',zipcode='".implode('-',$_POST['zipcode'])."',address='".trim($_POST['address']." ".$_POST['address_sub'])."',memo='".$_POST['memo']."' where ordno='".$_POST['ordno']."'");

		break;

	case "orderCancel":

		$res = $db->query("select ordno from ".GD_ORDER." where ordno='".$_POST['ordno']."' and step='0' and step2='0'");
		$arr = array(
			'name'=>'사용자',
			'code'=>'9',
			'memo'=>'사용자주문취소',
			'bankcode'=>'',
			'bankaccount'=>'',
			'bankuser'=>''
		);
		while($tmp = $db->fetch($res)){

			$arr[sno] = $arr[ea] = '';
			$res2 = $db->query("select sno,ea from ".GD_ORDER_ITEM." where ordno='".$_POST['ordno']."'");
			while($tmp2 = $db->fetch($res2)){
				$arr[sno][] = $tmp2[sno];
				$arr[ea][] = $tmp2[ea];
			}

			### 주문취소
			chkCancel($_POST['ordno'],$arr);
			### 재고조정
			setStock($_POST['ordno']);
			set_prn_settleprice($_POST['ordno']);

			$db->query("update ".GD_CASHRECEIPT." set moddt=now(),status='RFS' where ordno='{$_POST['ordno']}' and status='RDY'");
		}
		go('mypage_orderlist.php');

		break;

	### 현금영수증 신청
	case "add_cashreceipt":

		include '../lib/cashreceipt.class.php';
		$cashreceipt = new cashreceipt();
		$resid = $cashreceipt->putUserReceipt($_POST);

		if ($resid){
			echo "<script>alert('정상적으로 신청되었습니다'); parent.location.reload();</script>";
		}

		break;

	// 쿠폰복원 150129 추가
	case "recoverCoupon":

		restore_coupon($_POST[ordno]);
		break;
}

go($_SERVER[HTTP_REFERER]);

?>
