<?

include "../../../lib/library.php";
include "../../../conf/config.php";
//include "../../../conf/pg.allatbasic.php";
include "./allatutil.php";

// �����̼� ������� ��� PG ���� ��ü
resetPaymentGateway();

function return_allat($str){
	$tmp = explode("\n",trim($str));
	for($i=0;$i<sizeof($tmp);$i++){
		$div = explode("=",trim($tmp[$i]));
		$arr[$div[0]] = $div[1];
	}
	return $arr;
}

function allat_log_write($logMsg)
{
	$logInfo  = 'INFO ['.date('Y-m-d H:i:s').'] START Order log'.chr(10);
	$logInfo .= 'DEBUG ['.date('Y-m-d H:i:s').'] Connect IP : '.$_SERVER['REMOTE_ADDR'].chr(10);
	$logInfo .= 'DEBUG ['.date('Y-m-d H:i:s').'] Request URL : '.$_SERVER['REQUEST_URI'].chr(10);
	$logInfo .= 'DEBUG ['.date('Y-m-d H:i:s').'] User Agent : '.$_SERVER['HTTP_USER_AGENT'].chr(10);
	$logInfo .= $logMsg;
	$logInfo .= 'INFO ['.date('Y-m-d H:i:s').'] END Order log'.chr(10);
	$logInfo .= '------------------------------------------------------------------------------'.chr(10).chr(10);

	error_log($logInfo, 3, './log/allat_log_'.date('Ymd').'.log');
}

$ordno = $_POST['allat_order_no'];

### �����������̽��� ����� Get : ���� �ֹ��������������� Request Get
$at_data	= "allat_shop_id=".urlencode($pg[id])."&allat_amt=$_POST[allat_amt]&allat_enc_data=$_POST[allat_enc_data]&allat_cross_key=$pg[crosskey]";
$at_txt		= ApprovalReq($at_data,$pg[ssl]);	// ���� �ʿ� (SSL:SSL�̿�� / NOSSL:SSL�̻���-�����ڵ� 0212�� ��� ���)
$at_return	= return_allat($at_txt);

//--- �ÿ� �α�
$logMsg = chr(9).str_replace(chr(10),chr(10).chr(9), str_replace('=', chr(9).chr(9).'= ', iconv('EUC-KR','UTF-8',($at_txt)))).chr(10);
allat_log_write($logMsg);

$REPLYCD	= $at_return['reply_cd'];		//����ڵ�
$REPLYMSG	= $at_return['reply_msg'];		//����޼���

### �����α� ����
$at_return = array_map("trim",$at_return);
extract($at_return);
/******************************************************************************
reply_cd		= 0000				# ����ڵ�
reply_msg		= �׽�Ʈ����		# ����޼���
order_no		= 1149831153181		# �ֹ���ȣ
amt				= 14600				# ���αݾ�
pay_type		= ISP				# ���Ҽ��� (3D, ISP, NOR, ABANK)
approval_ymdhms	= 20060609150711	# �����Ͻ�
seq_no			= 0000000000		# �ŷ��Ϸù�ȣ
approval_no		= 12345678			# ���ι�ȣ
card_id			= 00				# ī��ID - ī�������ڵ�(��:01,02,�� �� )
card_nm			= �׽�Ʈ			# ī��� - ī��������(��:�Ｚ, ����, �� �� )
sell_mm			= 00				# �Һΰ���
zerofee_yn		= N					# ������(Y),�Ͻú�(N)
cert_yn			= N					# �������� - ����(Y),������(N)
contract_yn		= N					# �����Ϳ��� - 3�ڰ�����(Y),��ǥ������(N)
*******************************************************************************
sfcard_id		= 00				#
sfcard_nm		= �׽�Ʈ			#
bank_id			=					# ����ID
bank_nm			=					# �����
cash_bill_no	=					# ���ݿ������Ϸù�ȣ - ���ݿ����� ��Ͻ�
escrow_yn		=					# ����ũ�ο��� - Y(����ũ��), N(������)
*******************************************************************************/

switch ($pay_type){
	case "3D": case "ISP": case "NOR":
		$settlelogAdd = "
����ī�� : [$card_id] $card_nm
�Һΰ��� : $sell_mm
������   : $zerofee_yn
";
		break;
	case "ABANK":
		$settlelogAdd = "
�������� : [$bank_id] $bank_nm
���ݿ������Ϸù�ȣ : $cash_bill_no
";
		break;
	case "VBANK":
		$settlelogAdd = "
������� : $bank_nm $account_no $account_nm
�Աݰ��¸� : $income_account_nm
�Աݱ����� : $income_limit_ymd
�Աݿ����� : $income_expect_ymd
���ݿ�������û���� : $cash_yn
���ݿ������Ϸù�ȣ : $cash_bill_no
";
		break;
	case "HP":
		$settlelogAdd = "
�̵���Ż籸�� : $hp_id
";
		break;
}

$settlelog = "$ordno (".date('Y:m:d H:i:s').")
----------------------------------------
����ڵ� : $reply_cd
������� : $reply_msg
���αݾ� : $amt
���Ҽ��� : $pay_type
�����Ͻ� : $approval_ymdhms
�ŷ���ȣ : $seq_no
���ι�ȣ : $approval_no
�������� : $cert_yn
����ũ�� : $escrow_yn
----------------------------------------";

if ($settlelogAdd) $settlelog .= $settlelogAdd."----------------------------------------";

### ���ں������� �߱�
@session_start();
if (session_is_registered('eggData') === true && !strcmp($REPLYCD,"0000")){
	if ($_SESSION[eggData][ordno] == $ordno && $_SESSION[eggData][resno1] != '' && $_SESSION[eggData][resno2] != '' && $_SESSION[eggData][agree] == 'Y'){
		include '../../../lib/egg.class.usafe.php';
		$eggData = $_SESSION[eggData];
		switch ($pay_type){
			case "3D": case "ISP": case "NOR":
				$eggData[payInfo1] = $card_nm; # (*) ��������(ī���)
				$eggData[payInfo2] = $approval_no; # (*) ��������(���ι�ȣ)
				break;
			case "ABANK":
				$eggData[payInfo1] = $bank_nm; # (*) ��������(�����)
				$eggData[payInfo2] = $seq_no; # (*) ��������(���ι�ȣ or �ŷ���ȣ)
				break;
			case "VBANK":
				$eggData[payInfo1] = $bank_nm; # (*) ��������(�����)
				$eggData[payInfo2] = $account_no; # (*) ��������(���¹�ȣ)
				break;
		}
		$eggCls = new Egg( 'create', $eggData );
		if ( $eggCls->isErr == true && $pay_type == "VBANK" ){
			$REPLYCD = '';
		}
		else if ( $eggCls->isErr == true && in_array($pay_type, array("3D","ISP","NOR","ABANK")) );
	}
	session_unregister('eggData');
}

### �ŷ��Ϸù�ȣ ����
$query = "update ".GD_ORDER." set cardtno='".$seq_no."' where ordno='".$ordno."'";
$db -> query($query);

### ������� ������ ��� üũ �ܰ� ����
$res_cstock = true;
if($cfg['stepStock'] == '1' && $pay_type=="VBANK") $res_cstock = false;

### item check stock
include "../../../lib/cardCancel.class.php";
include "../../../lib/cardCancel_social.class.php";
$cancel = new cardCancel_social();
if(!$cancel->chk_item_stock($ordno) && $res_cstock){
	$cancel -> cancel_allat_request($ordno);
	exit;
}

$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
if($oData['step'] > 0 || $oData['vAccount'] != ''){		// �ߺ�����

	$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
	go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");

} else if( !strcmp($REPLYCD,"0000") ){		// ���� ����

	$query = "
	select * from
		".GD_ORDER." a
		left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
	where
		a.ordno='$ordno'
	";
	$data = $db->fetch($query);

	include "../../../lib/cart.class.php";

	$cart = new Cart($_COOKIE[gd_isDirect]);
	$cart->chkCoupon();

	$cart->delivery = $data[delivery];
	$cart->dc = $member[dc]."%";
	$cart->calcu();
	$cart -> totalprice += $delivery[price];

	### �ֹ�Ȯ�θ���
	$data[cart] = $cart;
	$data[str_settlekind] = $r_settlekind[$data[settlekind]];
	//sendMailCase($data[email],0,$data);
	// �����̼� �ֹ� sms & ���� �׸��� ���� �߱�
	$todayshop_noti = &load_class('todayshop_noti', 'todayshop_noti');
	$orderinfo = $todayshop_noti->getorderinfo($ordno);
	$todayshop_noti->set($ordno,'order');
	$todayshop_noti->send();

	### ����ũ�� ���� Ȯ��
	$escrowyn = ($escrow_yn=="Y") ? "y" : "n";

	### ���� ���� ����
	$step = 1;
	$qrc1 = "cyn='y', cdt=now(),";
	$qrc2 = "cyn='y',";

	### ������� ������ �������� ����
	if ($pay_type=="VBANK"){
		$vAccount = $bank_nm." ".$account_no." ".$account_nm;
		$step = 0; $qrc1 = $qrc2 = "";
	}

	### ���ݿ����� ����
	if ($cash_bill_no != ''){
		$qrc1 .= "cashreceipt='{$cash_bill_no}',";
	}

	### �ǵ���Ÿ ����
	$db->query("
	update ".GD_ORDER." set $qrc1
		step		= '$step',
		step2		= '',
		escrowyn	= '$escrowyn',
		escrowno	= '$escrowno',
		vAccount	= '$vAccount',
		settlelog	= concat(ifnull(settlelog,''),'$settlelog')
	where ordno='$ordno'"
	);
	$db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$ordno'");

	### �ֹ��α� ����
	orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

	### ��� ó��
	setStock($ordno);

	### ��ǰ���Խ� ������ ��� _ 2007-06-04
	if ($data[m_no] && $data[emoney]){
		setEmoney($data[m_no],-$data[emoney],"��ǰ���Խ� ������ ���� ���",$ordno);
	}

	### SMS ���� ����
	$dataSms = $data;

	if ($pay_type!="VBANK"){

		/*/
		sendMailCase($data['email'],1,$data);			### �Ա�Ȯ�θ���
		sendSmsCase('incash',$data['mobileOrder']);	### �Ա�Ȯ��SMS
		/*/
		// �����̼� �Ա� Ȯ�� sms & ���� �׸��� ���� �߱�
		if ($orderinfo['goodstype'] == 'coupon') { // ������ ���
			if ($orderinfo['processtype'] == 'i') { // ��� �߱� ������ �߱��ϰ� SMS/MAIL
				if (($cp_sno = $todayshop_noti->publishCoupon($ordno)) !== false) {
					$formatter = &load_class('stringFormatter', 'stringFormatter');
					if ($phone = $formatter->get($data['mobileReceiver'],'dial','-')) {
						$db->query("UPDATE ".GD_TODAYSHOP_ORDER_COUPON." SET cp_publish = 1 WHERE cp_sno = '$cp_sno'");	// �߱� ó��
						ctlStep($ordno,4,1);
					}
				}
			}
		}
		else {	
			// ������ �ƴ� �ǹ���ǰ�� ���, �Ǹŷ� ����
			$query = "
				select
				TG.tgsno from ".GD_ORDER_ITEM." AS O
				INNER JOIN ".GD_TODAYSHOP_GOODS." AS TG
				ON O.goodsno = TG.goodsno
				where O.ordno='$ordno'
			";
			$res = $db->query($query);
			while($tmp = $db->fetch($res)) {
	
				$query = "
					SELECT
	
						IFNULL(SUM(OI.ea), 0) AS cnt
	
					FROM ".GD_ORDER." AS O
					INNER JOIN ".GD_ORDER_ITEM." AS OI
						ON O.ordno=OI.ordno
					INNER JOIN ".GD_TODAYSHOP_GOODS_MERGED." AS TG
						ON OI.goodsno = TG.goodsno
	
					WHERE
							O.step > 0
						AND O.step2 < 40
						AND TG.tgsno='".$tmp['tgsno']."'
	
				";
	
				$_res = $db->query($query);
	
				while ($_tmp = $db->fetch($_res)) {
	
					$query = "
					UPDATE
						".GD_TODAYSHOP_GOODS_MERGED."		AS TGM
						INNER JOIN ".GD_TODAYSHOP_GOODS."	AS TG	ON TGM.goodsno = TG.goodsno
					SET
						TGM.buyercnt = ".$_tmp['cnt'].",
						TG.buyercnt = ".$_tmp['cnt']."
					WHERE
						TG.tgsno = ".$tmp['tgsno']."
					";
					$db->query($query);
	
				}
	
			}
		}			
		// eof �����̼� ���� �߱�
		/**/

	}
	/*
	else {
		sendSmsCase('order',$data[mobileOrder]);	### �ֹ�Ȯ��SMS
	}
	*/






	go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");

} else {	// ���� ����

	$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno' and step2=50");
	$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno' and istep=50");
	go("../../order_fail.php?ordno=$ordno","parent");

}

?>