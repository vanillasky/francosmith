<?php
if (isset($_GET['crno']) === false)
{
	include dirname(__FILE__).'/../../../lib/library.php';
	include dirname(__FILE__).'/../../../conf/config.pay.php';
	@include_once(dirname(__FILE__).'/../../../lib/cashreceipt.class.php');
	extract($_POST);

	if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
		$_POST = validation::xssCleanArray($_POST, array(
			validation::DEFAULT_KEY	=> 'text'
		));
	}

	if(!is_object($cashreceipt) && class_exists('cashreceipt')) $cashreceipt = new cashreceipt();

	### �ݾ� ����Ÿ ��ȿ�� üũ
	$data = $cashreceipt->getCashReceiptCalCulate($ordno);
	if ($data['supply']!=$_POST['sup_price'] || $data['vat']!=$_POST['tax']) msg('�ݾ��� ��ġ���� �ʽ��ϴ�',-1);

	// �߱޻���üũ(�����ý��۰��)
	if ($data['cashreceipt'] != '' && file_exists(dirname(__FILE__).'/../../../../lib/cashreceipt.class.php') === false) {
		msg('���ݿ����� �����û����!! \\n['.$ordno.'] �ֹ��� �̹� ����Ǿ����ϴ�.');
		exit;
	}

	### ���ݿ�������û���� �߰�
	if (is_object($cashreceipt))
	{
		// �߱޻���üũ
		list($crno) = $db->fetch("select crno from gd_cashreceipt where ordno='{$ordno}' and status='ACK' order by crno desc limit 1");
		if ($crno != '') {
			msg('���ݿ����� �����û����!! \\n['.$ordno.'] �ֹ��� �̹� ����Ǿ����ϴ�.');
			exit;
		}
		//�߰�
		if($_POST['EP_issue_type']=='01')
			$useopt="0";
		else
			$useopt="1";


		$indata = array();
		$indata['ordno'] = $_POST['ordno'];
		$indata['goodsnm'] = $_POST['goodname'];
		$indata['buyername'] = $_POST['buyername'];
		$indata['useopt'] = $useopt;
		$indata['certno'] = $_POST['EP_auth_value'];
		$indata['amount'] = $_POST['cr_price'];
		$indata['supply'] = $_POST['sup_price'];
		$indata['surtax'] = $_POST['tax'];

		$crno = $cashreceipt->putReceipt($indata);
	}
}
else {
	$ordno = $crdata['ordno'];
	$goodname = $crdata['goodsnm'];
	$cr_price = $crdata['amount'];
	$sup_price = $crdata['supply'];
	$tax = $crdata['surtax'];
	$srvc_price = 0;
	$buyername = $crdata['buyername'];
	$buyeremail = $crdata['buyeremail'];
	$buyertel = $crdata['buyerphone'];
	$reg_num = $crdata['certno'];
	$useopt = $crdata['useopt'];
	$crno = $_GET['crno'];
}

// ���ݿ����� ���� ������
	$shopdir = dirname(__FILE__).'/../../..';
	include($shopdir.'/conf/config.php');
	include($shopdir.'/conf/pg.'.$cfg[settlePg].'.php');
	require_once($shopdir.'/order/card/easypay/inc/easypay_config.php');
    require_once($shopdir.'/order/card/easypay/easypay_client.php');

/* -------------------------------------------------------------------------- */
/* ::: ó������ ����                                                          */
/* -------------------------------------------------------------------------- */
$ISSUE    = "issue" ;  // ����
$CANCL    = "cancel" ; // ���


$tr_cd            = $_POST["EP_tr_cd"];            // [�ʼ�]��û����
$pay_type         = $_POST["EP_pay_type"];         // [�ʼ�]��������
$req_type         = $_POST["EP_req_type"];         // [�ʼ�]��ûŸ��

if(is_null($tr_cd))
	$tr_cd=$crdata['EP_tr_cd'];
if(is_null($pay_type))
	$pay_type=$crdata['EP_pay_type'];
if(is_null($req_type))
	$req_type=$crdata['EP_req_type'];




/* -------------------------------------------------------------------------- */
/* ::: ���ݿ����� �������� ����                                               */
/* -------------------------------------------------------------------------- */

$user_id          = $_POST["EP_user_id"];          // [����]�� ID
$user_nm          = $_POST["EP_user_nm"];          // [����]����
$issue_type       = $_POST["EP_issue_type"];       // [�ʼ�]���ݿ���������뵵
$auth_type        = $_POST["EP_auth_type"];        // [�ʼ�]��������
$auth_value       = $_POST["EP_auth_value"];       // [�ʼ�]������ȣ
$sub_mall_yn      = $_POST["EP_sub_mall_yn"];      // [�ʼ�]������������뿩��
$sub_mall_buss    = $_POST["EP_sub_mall_buss"];    // [����]��������������ڹ�ȣ
$tot_amt          = $_POST["EP_tot_amt"];          // [�ʼ�]�Ѱŷ��ݾ�
$service_amt      = $_POST["EP_service_amt"];      // [�ʼ�]�����
$vat              = $_POST["EP_vat"];              // [�ʼ�]�ΰ���

//--- �������� ���
$easypayHome	= dirname($_SERVER['SCRIPT_FILENAME']);      // �̴����� Ȩ���͸�
$logfile		= fopen( $easypayHome . '/log/easypay_receipt_log_'.date('Ymd').'.log', 'a+' );

// �α� ����
$logInfo	 = '------------------------------------------------------------------------------'.chr(10);
$logInfo	.= 'INFO	['.date('Y-m-d H:i:s').']	START Order log'.chr(10);
foreach ($_POST as $key => $val) {
	$logInfo	.= 'DEBUG	['.date('Y-m-d H:i:s').']	'.$key.'	: '.$val.chr(10);
}
$logInfo	.= 'DEBUG	['.date('Y-m-d H:i:s').']	IP	: '.$_SERVER['REMOTE_ADDR'].chr(10);
$logInfo	.= 'INFO	['.date('Y-m-d H:i:s').']	END Order log'.chr(10);
$logInfo	.= '------------------------------------------------------------------------------'.chr(10).chr(10);
fwrite( $logfile, $logInfo);
fclose( $logfile );



if(is_null($order_no))
	$order_no=$ordno;         // [�ʼ�]�ֹ���ȣ
else
	$order_no         = $_POST["EP_order_no"];

if(is_null($issue_type)) {
	if($useopt=="0")
		$issue_type="01";
	else
		$issue_type="02";
}



if(is_null($auth_value)) {
	$auth_value=$reg_num;
}

if(is_null($auth_type)) {
	$auth_type="02";
	if(strlen($auth_value)==13) {
		$auth_type="02";//�ֹι�ȣ
	}
	else if(substr($auth_value, 0, 2)=="01" && (strlen($auth_value)==10 || strlen($auth_value)==11)) {
		$auth_type="03";//�޴���ȭ
	}
	else{
		$auth_type="04";//����ڵ�Ϲ�ȣ
	}

}


if(is_null($sub_mall_yn)) {
	$sub_mall_yn="0";
}

if(is_null($tot_amt)) {
	$tot_amt=$cr_price;
}

if(is_null($service_amt)) {
	$service_amt="0";
}

if(is_null($vat)) {
	$vat=$tax;
}
/* -------------------------------------------------------------------------- */
/* ::: ���ݿ����� ������� ����                                               */
/* -------------------------------------------------------------------------- */
$mgr_txtype       = $_POST["mgr_txtype"];          // [�ʼ�]�ŷ�����
$org_cno          = $_POST["org_cno"];             // [�ʼ�]���ŷ�������ȣ
$req_id           = $_SESSION['sess']['m_id'];//$_POST["req_id"];              // [�ʼ�]������ ������ �α��� ���̵�
$mgr_msg          = $_POST["mgr_msg"];             // [����]���� ����

/* -------------------------------------------------------------------------- */
/* ::: IP ���� ����                                                           */
/* -------------------------------------------------------------------------- */
$client_ip         = $_SERVER['REMOTE_ADDR'];      // [�ʼ�]������ IP

/* -------------------------------------------------------------------------- */
/* ::: ���� ���                                                              */
/* -------------------------------------------------------------------------- */
$res_cd     = "";
$res_msg    = "";

/* -------------------------------------------------------------------------- */
/* ::: EasyPayClient �ν��Ͻ� ���� [����Ұ� !!].                             */
/* -------------------------------------------------------------------------- */
$easyPay = new EasyPay_Client;         // ����ó���� Class (library���� ���ǵ�)
$easyPay->clearup_msg();

$easyPay->set_home_dir($g_home_dir);
$easyPay->set_gw_url($g_gw_url);
$easyPay->set_gw_port($g_gw_port);
$easyPay->set_log_dir($g_log_dir);
$easyPay->set_log_level($g_log_level);
$easyPay->set_cert_file($g_cert_file);

if( $ISSUE == $req_type )
{
	/* ---------------------------------------------------------------------- */
    /* ::: ������û ���� ����                                                 */
    /* ---------------------------------------------------------------------- */
    // ���� �ֹ� ����
    $cash_data = $easyPay->set_easypay_item("cash_data");
    $easyPay->set_easypay_deli_us( $cash_data, "order_no"      , $order_no     );
    $easyPay->set_easypay_deli_us( $cash_data, "user_id"       , $user_id      );
    $easyPay->set_easypay_deli_us( $cash_data, "user_nm"       , $user_nm      );
    $easyPay->set_easypay_deli_us( $cash_data, "issue_type"    , $issue_type   );
    $easyPay->set_easypay_deli_us( $cash_data, "auth_type"     , $auth_type    );
    $easyPay->set_easypay_deli_us( $cash_data, "auth_value"    , $auth_value   );
    $easyPay->set_easypay_deli_us( $cash_data, "sub_mall_yn"   , $sub_mall_yn  );
    if( $sub_mall_yn =="1" )
        $easyPay->set_easypay_deli_us( $cash_data, "sub_mall_buss"   , $sub_mall_buss   );

    $easyPay->set_easypay_deli_us( $cash_data, "tot_amt"      , $tot_amt      );
    $easyPay->set_easypay_deli_us( $cash_data, "service_amt"  , $service_amt  );
    $easyPay->set_easypay_deli_us( $cash_data, "vat"          , $vat          );
}
else if( $CANCL == $req_type )
{
    $mgr_data = $easyPay->set_easypay_item("mgr_data");
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_txtype"    , $mgr_txtype   );
    $easyPay->set_easypay_deli_us( $mgr_data, "org_cno"       , $org_cno      );
    $easyPay->set_easypay_deli_us( $mgr_data, "client_ip"     , $client_ip    );
    $easyPay->set_easypay_deli_us( $mgr_data, "req_id"        , $req_id       );
    $easyPay->set_easypay_deli_us( $cash_data, "mgr_msg"      , $mgr_msg      );
}
/* -------------------------------------------------------------------------- */
/* ::: ����                                                                   */
/* -------------------------------------------------------------------------- */
$opt = "option value";
$easyPay->easypay_exec($g_mall_id, $tr_cd, $order_no, $client_ip, $opt);
$res_cd  = $easyPay->_easypay_resdata["res_cd"];    // �����ڵ�
$res_msg = $easyPay->_easypay_resdata["res_msg"];   // ����޽���

/* -------------------------------------------------------------------------- */
/* ::: ��� ó��                                                              */
/* -------------------------------------------------------------------------- */
$r_cno             = $easyPay->_easypay_resdata[ "cno"             ];    // PG�ŷ���ȣ
$r_amount          = $easyPay->_easypay_resdata[ "amount"          ];    //�� �����ݾ�
$r_auth_no         = $easyPay->_easypay_resdata[ "auth_no"         ];    //���ι�ȣ
$r_tran_date       = $easyPay->_easypay_resdata[ "tran_date"       ];    //�����Ͻ�
$r_pnt_auth_no     = $easyPay->_easypay_resdata[ "pnt_auth_no"     ];    //����Ʈ���ι�ȣ
$r_pnt_tran_date   = $easyPay->_easypay_resdata[ "pnt_tran_date"   ];    //����Ʈ�����Ͻ�
$r_cpon_auth_no    = $easyPay->_easypay_resdata[ "cpon_auth_no"    ];    //�������ι�ȣ
$r_cpon_tran_date  = $easyPay->_easypay_resdata[ "cpon_tran_date"  ];    //���������Ͻ�
$r_card_no         = $easyPay->_easypay_resdata[ "card_no"         ];    //ī���ȣ
$r_issuer_cd       = $easyPay->_easypay_resdata[ "issuer_cd"       ];    //�߱޻��ڵ�
$r_issuer_nm       = $easyPay->_easypay_resdata[ "issuer_nm"       ];    //�߱޻��
$r_acquirer_cd     = $easyPay->_easypay_resdata[ "acquirer_cd"     ];    //���Ի��ڵ�
$r_acquirer_nm     = $easyPay->_easypay_resdata[ "acquirer_nm"     ];    //���Ի��
$r_install_period  = $easyPay->_easypay_resdata[ "install_period"  ];    //�Һΰ���
$r_noint           = $easyPay->_easypay_resdata[ "noint"           ];    //�����ڿ���
$r_bank_cd         = $easyPay->_easypay_resdata[ "bank_cd"         ];    //�����ڵ�
$r_bank_nm         = $easyPay->_easypay_resdata[ "bank_nm"         ];    //�����
$r_account_no      = $easyPay->_easypay_resdata[ "account_no"      ];    //���¹�ȣ
$r_deposit_nm      = $easyPay->_easypay_resdata[ "deposit_nm"      ];    //�Ա��ڸ�
$r_expire_date     = $easyPay->_easypay_resdata[ "expire_date"     ];    //���»�븸����
$r_cash_res_cd     = $easyPay->_easypay_resdata[ "cash_res_cd"     ];    //���ݿ����� ����ڵ�
$r_cash_res_msg    = $easyPay->_easypay_resdata[ "cash_res_msg"    ];    //���ݿ����� ����޼���
$r_cash_auth_no    = $easyPay->_easypay_resdata[ "cash_auth_no"    ];    //���ݿ����� ���ι�ȣ
$r_cash_tran_date  = $easyPay->_easypay_resdata[ "cash_tran_date"  ];    //���ݿ����� �����Ͻ�
$r_auth_id         = $easyPay->_easypay_resdata[ "auth_id"         ];    //PhoneID
$r_billid          = $easyPay->_easypay_resdata[ "billid"          ];    //������ȣ
$r_mobile_no       = $easyPay->_easypay_resdata[ "mobile_no"       ];    //�޴�����ȣ
$r_ars_no          = $easyPay->_easypay_resdata[ "ars_no"          ];    //��ȭ��ȣ
$r_cp_cd           = $easyPay->_easypay_resdata[ "cp_cd"           ];    //����Ʈ��/������
$r_used_pnt        = $easyPay->_easypay_resdata[ "used_pnt"        ];    //�������Ʈ
$r_remain_pnt      = $easyPay->_easypay_resdata[ "remain_pnt"      ];    //�ܿ��ѵ�
$r_pay_pnt         = $easyPay->_easypay_resdata[ "pay_pnt"         ];    //����/�߻�����Ʈ
$r_accrue_pnt      = $easyPay->_easypay_resdata[ "accrue_pnt"      ];    //��������Ʈ
$r_remain_cpon     = $easyPay->_easypay_resdata[ "remain_cpon"     ];    //�����ܾ�
$r_used_cpon       = $easyPay->_easypay_resdata[ "used_cpon"       ];    //���� ���ݾ�
$r_mall_nm         = $easyPay->_easypay_resdata[ "mall_nm"         ];    //���޻��Ī
$r_escrow_yn       = $easyPay->_easypay_resdata[ "escrow_yn"       ];    //����ũ�� �������
$r_complex_yn      = $easyPay->_easypay_resdata[ "complex_yn"      ];    //���հ��� ����
$r_canc_acq_date   = $easyPay->_easypay_resdata[ "canc_acq_date"   ];    //��������Ͻ�
$r_canc_date       = $easyPay->_easypay_resdata[ "canc_date"       ];    //����Ͻ�
$r_refund_date     = $easyPay->_easypay_resdata[ "refund_date"     ];    //ȯ�ҿ����Ͻ�


if( $ISSUE == $req_type ){	// ����
	if($res_cd == '0000'){	// ����
		$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= '���ݿ����� �߱� ����'."\n";
		$settlelog .= '����ڵ� : '.$res_cd."\n";
		$settlelog .= '����޼��� : '.$res_msg."\n";
		$settlelog .= '���ι�ȣ : '.$r_auth_no."\n";
		$settlelog .= '�����Ͻ� : '.$r_tran_date."\n";
		$settlelog .= 'PG�ŷ���ȣ : '.$r_cno."\n";
		$settlelog .= '-----------------------------------'."\n";


		if (empty($crno) === true)
		{
			$db->query("update gd_order set cashreceipt='$r_cno',settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
		}
		else {
			# ���ݿ�������û���� ����
			$db->query("update gd_cashreceipt set pg='easypay',cashreceipt='$r_cno',receiptnumber='$r_cash_auth_no',tid='$r_cno',moddt=now(),status='ACK',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
			$db->query("update gd_order set cashreceipt='$r_cno' where ordno='{$ordno}'");

		}

		if (isset($_GET['crno']) === false)
		{
			msg('���ݿ������� ����߱޵Ǿ����ϴ�');
			echo '<script>parent.location.reload();</script>';
		}



	}else{	// ����
				$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
				$settlelog .= '-----------------------------------'."\n";
				$settlelog .= '���ݿ����� �߱� ����'."\n";
				$settlelog .= '����ڵ� : '.$res_cd."\n";
				$settlelog .= '������� : '.$res_msg."\n";
				$settlelog .= '-----------------------------------'."\n";

				if (empty($crno) === true)
				{
					$db->query("update gd_order set settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
				}
				else {
					# ���ݿ�������û���� ����
					$db->query("update gd_cashreceipt set pg='easypay',errmsg='{$res_cd}:{$res_msg}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
				}

				if (isset($_GET['crno']) === false)
				{
					msg($res_msg);
					exit;
				}
	}
}
exit;
?>