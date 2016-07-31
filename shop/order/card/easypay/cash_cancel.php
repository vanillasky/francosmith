<?php
//--- ���̺귯�� ��Ŭ���
$shopdir=dirname(__FILE__)."/../../../";
include($shopdir.'/conf/config.php');
include($shopdir.'/conf/pg.easypay.php');
require_once($shopdir.'/order/card/easypay/inc/easypay_config.php');
require_once($shopdir.'/order/card/easypay/easypay_client.php');

//--- �ֹ���ȣ ó��
$ordno	= $crdata['ordno'];
/* -------------------------------------------------------------------------- */
/* ::: ó������ ����                                                          */
/* -------------------------------------------------------------------------- */
$ISSUE    = "issue" ;  // ����
$CANCL    = "cancel" ; // ���

$tr_cd            = "00201050";            // [�ʼ�]��û����
$pay_type         = $_POST["EP_pay_type"];         // [�ʼ�]��������??
$req_type         = "cancel";         // [�ʼ�]��ûŸ��

/* -------------------------------------------------------------------------- */
/* ::: ���ݿ����� �������� ����                                               */
/* -------------------------------------------------------------------------- */
$order_no         = $_POST["EP_order_no"];         // [�ʼ�]�ֹ���ȣ
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

/* -------------------------------------------------------------------------- */
/* ::: ���ݿ����� ������� ����                                               */
/* -------------------------------------------------------------------------- */
$mgr_txtype       =  "51";          // [�ʼ�]�ŷ�����
$org_cno          =	 $crdata['tid'];             // [�ʼ�]���ŷ�������ȣ
$req_id           = $_SESSION['sess']['m_id'];              // [�ʼ�]������ ������ �α��� ���̵�
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
    $easyPay->set_easypay_deli_us( $mgr_data, "req_ip"     , $client_ip    );
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

$settlelog	= '';
		$settlelog	.= '===================================================='.chr(10);
		$settlelog	.= '�ֹ���ȣ : '.$ordno.chr(10);
		$settlelog	.= '�ŷ���ȣ : '.$crdata['tid'].chr(10);
		$settlelog	.= '����ڵ� : '.$res_cd.chr(10);
		$settlelog	.= '������� : '.$res_msg.chr(10);
		$settlelog	.= '��ҳ�¥ : '.$r_canc_date.chr(10);

if( $ISSUE == $req_type ){	// ����
	echo "���";
}
else if( $CANCL == $req_type ){ // ���
	if($res_cd == '0000'){	// ����
		//--- �α� ����
		$getPgResult	= true;
			$settlelog	.= '���ݿ����� ��� ���ι�ȣ : '.$r_auth_no.chr(10);
			$settlelog	= '===================================================='.chr(10).'���ݿ����� ��� ���� : ��ҿϷ�ð�('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);


	}else{	// ����
			// PG ���
			$getPgResult	= false;
			$settlelog	= '===================================================='.chr(10).'���ݿ����� ��� ���� : ��ҿ����ð�('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
	}
}
//--- ��� ó��
if( $getPgResult === true )
{
	$db->query("UPDATE ".GD_CASHRECEIPT." SET moddt=now(),status='CCR',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'".$settlelog."') WHERE crno='".$_GET['crno']."'");
}
else
{
	$db->query("UPDATE ".GD_CASHRECEIPT." SET errmsg='".$res_cd.":".$res_msg."',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'".$settlelog."') WHERE crno='".$_GET['crno']."'");
}


?>