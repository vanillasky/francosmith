<?php
/**
 * �̴Ͻý� PG ��� ������
 * �̴Ͻý� PG ���� : INIpayMobile Web (V 2.4 - 20110725)
 */

include dirname(__FILE__)."/../../../../lib/library.php";
include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
 




$shopdir = dirname(__FILE__).'/../../../..';
include($shopdir.'/conf/config.php'); 
include($shopdir.'/conf/pg.easypay.php'); 
require_once($shopdir.'/order/card/easypay/inc/easypay_config.php');
require_once($shopdir.'/order/card/easypay/easypay_client.php');

// PG���� ������ üũ �� ��ȿ�� üũ
if (forge_order_check($_POST["order_no"],$_POST['pay_mny']) === false) {
	msg('�ֹ� ������ ���� ������ ���� �ʽ��ϴ�. �ٽ� ���� �ٶ��ϴ�.',$cfgMobileShop['mobileShopRootDir'].'/ord/order_fail.php?ordno='.$_POST["order_no"],'parent');
	exit();
}

// ���̹� ���ϸ��� ���� ���� API
include dirname(__FILE__)."/../../../../lib/naverNcash.class.php";
$naverNcash = new naverNcash();
if ($naverNcash->useyn == 'Y') {
	if ($_POST['pay_type'] == '22') $ncashResult = $naverNcash->payment_approval($_POST['order_no'], false);
	else $ncashResult = $naverNcash->payment_approval($_POST['order_no'], true);
	if ($ncashResult === false) {
		msg('���̹� ���ϸ��� ��뿡 �����Ͽ����ϴ�.', $cfgMobileShop['mobileShopRootDir'].'/ord/order_fail.php?ordno='.$_POST["order_no"],'parent');
		exit();
	}
}

/* -------------------------------------------------------------------------- */
/* ::: ó������ ����                                                          */
/* -------------------------------------------------------------------------- */
$TRAN_CD_NOR_PAYMENT    = "00101000";   // ����(�Ϲ�, ����ũ��)
$TRAN_CD_NOR_MGR        = "00201000";   // ����(�Ϲ�, ����ũ��)

/* -------------------------------------------------------------------------- */
/* ::: ó�� �������� ����                                                     */
/* -------------------------------------------------------------------------- */
$res_cd           = $_POST["res_cd"];              // [�ʼ�]��û����
$res_msg          = $_POST["res_msg"];             // [�ʼ�]��û����

$tr_cd            = $_POST["tr_cd"];               // [�ʼ�]��û����
$sessionkey       = $_POST["sessionkey"];          // [�ʼ�]��ȣȭŰ
$encrypt_data     = $_POST["encrypt_data"];        // [�ʼ�]��ȣȭ ����Ÿ


$mall_id          = $_POST["mall_id"];             // [�ʼ�]������ ID
$order_no         = $_POST["order_no"];            // [�ʼ�]�ֹ���ȣ

$pay_type         = $_POST["pay_type"];            // [�ʼ�]��������

$client_ip         = $_POST["client_ip"];            // [�ʼ�]��IP


/* -------------------------------------------------------------------------- */
/* ::: ������ �����ʵ� �������� ����                                          */
/* -------------------------------------------------------------------------- */
$mobilereserved1 = $_POST["mobilereserved1"];      // [����]�����ʵ�
$mobilereserved2 = $_POST["mobilereserved2"];      // [����]�����ʵ�
$reserved1       = $_POST["reserved1"];            // [����]�����ʵ�
$reserved2       = $_POST["reserved2"];            // [����]�����ʵ�
$reserved3       = $_POST["reserved3"];            // [����]�����ʵ�
$reserved4       = $_POST["reserved4"];            // [����]�����ʵ�

/* -------------------------------------------------------------------------- */
/* ::: ������� ���� ����                                                     */
/* -------------------------------------------------------------------------- */
$mgr_txtype       = $_POST["mgr_txtype"];          // [�ʼ�]�ŷ�����
$mgr_subtype      = $_POST["mgr_subtype"];         // [����]���漼�α���
$org_cno          = $_POST["org_cno"];             // [�ʼ�]���ŷ�������ȣ
$mgr_amt          = $_POST["mgr_amt"];             // [����]�κ����/ȯ�ҿ�û �ݾ�
$mgr_bank_cd      = $_POST["mgr_bank_cd"];         // [����]ȯ�Ұ��� �����ڵ�
$mgr_account      = $_POST["mgr_account"];         // [����]ȯ�Ұ��� ��ȣ
$mgr_depositor    = $_POST["mgr_depositor"];       // [����]ȯ�Ұ��� �����ָ�
$mgr_socno        = $_POST["mgr_socno"];           // [����]ȯ�Ұ��� �ֹι�ȣ
$mgr_telno        = $_POST["mgr_telno"];           // [����]ȯ�Ұ� ����ó
$deli_cd          = $_POST["deli_cd"];             // [����]��۱���[�ڰ�:DE01,�ù�:DE02]
$deli_corp_cd     = $_POST["deli_corp_cd"];        // [����]�ù���ڵ�
$deli_invoice     = $_POST["deli_invoice"];        // [����]����� ��ȣ
$deli_rcv_nm      = $_POST["deli_rcv_nm"];         // [����]������ �̸�
$deli_rcv_tel     = $_POST["deli_rcv_tel"];        // [����]������ ����ó
$req_id           = $_POST["req_id"];              // [����]������ ������ �α��� ���̵�
$mgr_msg          = $_POST["mgr_msg"];             // [����]���� ����

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

if( $TRAN_CD_NOR_PAYMENT == $tr_cd )
{
	
	/* ---------------------------------------------------------------------- */
    /* ::: ���ο�û(��ȣȭ ���� ����)                                         */
    /* ---------------------------------------------------------------------- */
    /* ---------------------------------------------------------------------- */
    /* ::: ���ο�û �� �����ڵ�, �ֹ���ȣ��, MALL_ID�� �ŷ�Ȯ�� �ʼ�          */
    /* ---------------------------------------------------------------------- */ 
	if( $res_cd == "0000" ) {    
        //$easyPay->set_trace_no($trace_no);
        $easyPay->set_snd_key($sessionkey);
        $easyPay->set_enc_data($encrypt_data);	


    }
	
}
else if( $TRAN_CD_NOR_MGR == $tr_cd )
{
    /* ---------------------------------------------------------------------- */
    /* ::: ������� ��û                                                      */
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
    
    /* PG Client ������ ���ؼ� ���� */
    $res_cd = "0000";
}

/* -------------------------------------------------------------------------- */
/* ::: ����                                                                   */
/* -------------------------------------------------------------------------- */ 
/* ���������� '0000'�� �ƴϸ� ����ó�� */
if ( $res_cd == "0000" ) {    
	$opt = "option value";
    $easyPay->easypay_exec($g_mall_id, $tr_cd, $order_no, $client_ip, $opt);
    
    $res_cd  = $easyPay->_easypay_resdata["res_cd"];    // �����ڵ�
    $res_msg = $easyPay->_easypay_resdata["res_msg"];   // ����޽���
}

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
$r_canc_acq_data   = $easyPay->_easypay_resdata[ "canc_acq_data"   ];    //��������Ͻ�
$r_canc_date       = $easyPay->_easypay_resdata[ "canc_date"       ];    //����Ͻ�
$r_refund_date     = $easyPay->_easypay_resdata[ "refund_date"     ];    //ȯ�ҿ����Ͻ�    

$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$order_no'");
if($oData['step'] > 0 || $oData['vAccount'] != '' ){
	$settlelog = PHP_EOL."$order_no (".date('Y:m:d H:i:s').")
	----------------------------------------
	�ߺ����ο�û
	----------------------------------------
	�����ڵ� : $res_cd
	PG�ŷ���ȣ : $r_cno
	----------------------------------------";
	$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$order_no'");
	go($cfgMobileShop['mobileShopRootDir']."/ord/order_end.php?ordno=$order_no&card_nm=".$r_issuer_nm,"parent");
	exit();
}

/* -------------------------------------------------------------------------- */
/* ::: ������ DB ó��                                                         */
/* -------------------------------------------------------------------------- */
/* �����ڵ�(res_cd)�� "0000" �̸� ������� �Դϴ�.                            */
/* r_amount�� �ֹ�DB�� �ݾװ� �ٸ� �� �ݵ�� ��� ��û�� �Ͻñ� �ٶ��ϴ�.     */
/* DB ó�� ���� �� ��� ó���� ���ֽñ� �ٶ��ϴ�.                             */
/* -------------------------------------------------------------------------- */
if ( $TRAN_CD_NOR_PAYMENT == $tr_cd && $res_cd == "0000" )
{

		### �α�

		switch($pay_type){
			case "11" : // �ſ�ī��
				$settlelogAdd = "
	ī���ȣ : $r_card_no
	�� �� �� : [$r_issuer_cd] $r_issuer_nm
	�� �� �� : [$r_acquirer_cd] $r_acquirer_nm
	�Һΰ��� : $r_install_period
	�����ڿ��� : $r_noint
	";
				break;
			case "21" : // ������ü
			$settlelogAdd = "
	�������� : [$r_bank_cd] $r_bank_nm
	���ݿ����� ��� �ڵ� : $r_cash_res_cd
	���ݿ����� ��� �޽��� : $r_cash_res_msg
	���ݿ����� ���ι�ȣ : $r_cash_auth_no
	";
				break;
			case "22" : // �������Ա�(�������)
			$settlelogAdd = "
	�������� : [$r_bank_cd] $r_bank_nm
	���¹�ȣ : $r_account_no
	���»�� ������ : ".date('Y-m-d G:i:s',strtotime($r_expire_date))."
	";
				break;
			case "31" :	// �޴���
			$settlelogAdd = "
	�޴��� ���� ID : $r_auth_id
	�޴��� ������ȣ : $r_billid
	�޴��� ��ȣ : $r_mobile_no
	";
				break;
		}

	$settlelog = "$order_no (".date('Y:m:d H:i:s').")
	----------------------------------------
	�����ڵ� : $res_cd
	����޽��� : $res_msg
	PG�ŷ���ȣ : $r_cno
	�� �����ݾ� : $r_amount
	���ι�ȣ : $r_auth_no
	�����Ͻ� : $r_tran_date
	����ũ�� ������� : $r_escrow_yn
	----------------------------------------";

	$settlelog .= $settlelogAdd."----------------------------------------";



	$card_nm=$r_issuer_nm;
	### ������� ������ ��� üũ �ܰ� ����
	$res_cstock = true;
	if($cfg['stepStock'] == '1' && $pay_type=="22") $res_cstock = false;

	### item check stock
	include "../../../../lib/cardCancel.class.php";
	$cancel = new cardCancel();
	if(!$cancel->chk_item_stock($order_no) && $res_cstock){
		$step = 51; $qrc1 = $qrc2 = "";
	}

	$query = "
		select * from
			".GD_ORDER." a
			left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
		where
			a.ordno='$order_no'
		";
		$data = $db->fetch($query);

		### ����ũ�� ���� Ȯ��
		$escrowyn = ($r_escrow_yn=="Y") ? "y" : "n";
		if($escrowyn == 'y')$escrowno = $r_cno; // <- Ȯ���غ�����.

		### ���� ���� ����
		$step = 1;
		$qrc1 = "cyn='y', cdt=now(),";
		$qrc2 = "cyn='y',";

		### ������� ������ �������� ����
		if ($pay_type=="22"){
			$vAccount = $r_bank_nm." ".$r_account_no." ".$r_deposit_nm;
			$step = 0; $qrc1 = $qrc2 = "";
		}

		### ���ݿ����� ����
		if ($r_cash_res_cd == "0000"){
			$qrc1 .= "cashreceipt='{$r_cash_auth_no}',";
		}

		### �ǵ���Ÿ ����
		$db->query("
		update ".GD_ORDER." set $qrc1
			step		= '$step',
			step2		= '',
			escrowyn	= '$escrowyn',
			escrowno	= '$escrowno',
			vAccount	= '$vAccount',
			settlelog	= concat(ifnull(settlelog,''),'$settlelog'),
			cardtno		= '".$r_cno."'
		where ordno='$order_no'"
		);
		$res = $db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$order_no'");

		### �ֹ��α� ����
		orderLog($order_no,$r_step2[$data[step2]]." > ".$r_step[$step]);

		### ��� ó��
		setStock($order_no);

		### ��ǰ���Խ� ������ ���
		if ($data[m_no] && $data[emoney]){
			setEmoney($data[m_no],-$data[emoney],"��ǰ���Խ� ������ ���� ���",$order_no);
		}

		### �ֹ�Ȯ�θ���
		if(function_exists('getMailOrderData')){
			sendMailCase($data['email'],0,getMailOrderData($order_no));
		}

		### SMS ���� ����
		$dataSms = $data;

		if ($pay_type!="22"){
			sendMailCase($data[email],1,$data);			### �Ա�Ȯ�θ���
			sendSmsCase('incash',$data[mobileOrder]);	### �Ա�Ȯ��SMS
		} else {
			sendSmsCase('order',$data[mobileOrder]);	### �ֹ�Ȯ��SMS
		}

	if($res && $step != 51) {
		$bDBProc = "true"; // DB �۾� ���� �Ǵ� �ݾ� ����ġ�� ��� "false" �� ����
		$res = true;
	}else{
		$bDBProc = "false";
		$res = false;
	}
    $bDBProc = "true";     // DBó�� ���� �� "true", ���� �� "false"
    if ( $bDBProc != "true" )
    {
        $easyPay->clearup_msg();
    
        $tr_cd = $TRAN_CD_NOR_MGR; 
        $mgr_data = $easyPay->set_easypay_item("mgr_data");
        if ( $r_escrow_yn != "Y" )    
        {
            $easyPay->set_easypay_deli_us( $mgr_data, "mgr_txtype"      , "40"   );
        }
        else
        {
            $easyPay->set_easypay_deli_us( $mgr_data, "mgr_txtype"      , "61"   );
            $easyPay->set_easypay_deli_us( $mgr_data, "mgr_subtype"     , "ES02" );
        }
        $easyPay->set_easypay_deli_us( $mgr_data, "org_cno"         , $r_cno     );
        $easyPay->set_easypay_deli_us( $mgr_data, "req_ip"          , $client_ip );
        $easyPay->set_easypay_deli_us( $mgr_data, "req_id"          , "MALL_R_TRANS" );
        $easyPay->set_easypay_deli_us( $mgr_data, "mgr_msg"         , "DB ó�� ���з� �����"  );
    
        $easyPay->easypay_exec($g_mall_id, $tr_cd, $order_no, $client_ip, $opt);
        $res_cd      = $easyPay->_easypay_resdata["res_cd"     ];    // �����ڵ�
        $res_msg     = $easyPay->_easypay_resdata["res_msg"    ];    // ����޽���
        $r_cno       = $easyPay->_easypay_resdata["cno"        ];    // PG�ŷ���ȣ 
        $r_canc_date = $easyPay->_easypay_resdata["canc_date"  ];    // ����Ͻ�

				$settlelog = "";

				$settlelog = "$order_no (".date('Y:m:d H:i:s').")
	----------------------------------------
	���ο�û����(DBó������)
	----------------------------------------
	�����ڵ� : $res_cd
	����޽��� : $res_msg
	PG�ŷ���ȣ : $r_cno
	����Ͻ� : $r_canc_date
	----------------------------------------";

				$db->query("update ".GD_ORDER." set step2='54', settlelog=concat(ifnull(settlelog,''),'".$settlelog."') where ordno='".$order_no."'");
				$db->query("update ".GD_ORDER_ITEM." set istep='54' where ordno='".$order_no."'");

				// Ncash ���� ���� ��� API ȣ��
				if ($naverNcash->useyn == 'Y') $naverNcash->payment_approval_cancel($order_no);

				go($cfgMobileShop['mobileShopRootDir']."/ord/order_fail.php?ordno=$order_no","parent");
	}else{
		go($cfgMobileShop['mobileShopRootDir']."/ord/order_end.php?ordno=$order_no&card_nm=$card_nm","parent"); 
		} 
    }else{
		if ($step == '51') {
			$cancel->cancel_db_proc($order_no);
		} else {
			$db->query("update ".GD_ORDER." set step2='54', settlelog=concat(ifnull(settlelog,''),'".$settlelog."') where ordno='".$order_no."'");
			$db->query("update ".GD_ORDER_ITEM." set istep='54' where ordno='".$order_no."'");
		}

		// Ncash ���� ���� ��� API ȣ��
		if ($naverNcash->useyn == 'Y') $naverNcash->payment_approval_cancel($order_no);

		go($cfgMobileShop['mobileShopRootDir']."/ord/order_fail.php?ordno=$order_no","parent");
	}

 ?>