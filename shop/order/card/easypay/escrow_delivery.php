<?php

	include "../../../lib/library.php";
	include "../../../conf/config.php";
	include "../../../conf/pg.easypay.php";
    include "./inc/easypay_config.php";
    include "./easypay_client.php";


/* -------------------------------------------------------------------------- */
/* ::: ó������ ����                                                          */
/* -------------------------------------------------------------------------- */
$TRAN_CD_NOR_PAYMENT    = "00101000";   // ����(�Ϲ�, ����ũ��)
$TRAN_CD_NOR_MGR        = "00201000";   // ����(�Ϲ�, ����ũ��)

/* -------------------------------------------------------------------------- */
/* ::: �÷����� �������� ����                                                 */
/* -------------------------------------------------------------------------- */
$tr_cd            = $_POST["EP_tr_cd"];            // [�ʼ�]��û����
$trace_no         = $_POST["EP_trace_no"];         // [�ʼ�]����������ȣ
$sessionkey       = $_POST["EP_sessionkey"];       // [�ʼ�]��ȣȭŰ
$encrypt_data     = $_POST["EP_encrypt_data"];     // [�ʼ�]��ȣȭ ����Ÿ

$pay_type         = $_POST["EP_ret_pay_type"];     // ��������
$complex_yn       = $_POST["EP_ret_complex_yn"];   // ���հ�������
$card_code        = $_POST["EP_card_code"];        // ī���ڵ�

/* -------------------------------------------------------------------------- */
/* ::: ���� �ֹ� ���� ����                                                    */
/* -------------------------------------------------------------------------- */
$order_no         = $_POST["ordno"];				// [�ʼ�]�ֹ���ȣ
$user_type        = $_POST["EP_user_type"];        // [����]����ڱ��б���[1:�Ϲ�,2:ȸ��]
$memb_user_no     = $_POST["EP_memb_user_no"];     // [����]������ ���Ϸù�ȣ
$user_id          = $_POST["EP_user_id"];          // [����]�� ID
$user_nm          = $_POST["EP_user_nm"];          // [�ʼ�]����
$user_mail        = $_POST["EP_user_mail"];        // [�ʼ�]�� E-mail
$user_phone1      = $_POST["EP_user_phone1"];      // [����]������ �� ��ȭ��ȣ
$user_phone2      = $_POST["EP_user_phone2"];      // [����]������ �� �޴���
$user_addr        = $_POST["EP_user_addr"];        // [����]������ �� �ּ�
$product_type     = $_POST["EP_product_type"];     // [����]��ǰ��������[0:�ǹ�,1:������]
$product_nm       = $_POST["EP_product_nm"];       // [�ʼ�]��ǰ��
$product_amt      = $_POST["EP_product_amt"];      // [�ʼ�]��ǰ�ݾ�

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
$req_ip           = $_POST["req_ip"];              // [�ʼ�]��û�� IP
$req_id           = $_POST["req_id"];              // [����]������ ������ �α��� ���̵�
$mgr_msg          = $_POST["mgr_msg"];             // [����]���� ����
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

/* -------------------------------------------------------------------------- */
/* ::: IP ���� ����                                                           */
/* -------------------------------------------------------------------------- */
$client_ip = $easyPay->get_remote_addr();    // [�ʼ�]������ IP


if( $TRAN_CD_NOR_PAYMENT == $tr_cd )
{

	/* ---------------------------------------------------------------------- */
    /* ::: ���ο�û(�÷����� ��ȣȭ ���� ����)                                */
    /* ---------------------------------------------------------------------- */
    $easyPay->set_trace_no($trace_no);
    $easyPay->set_snd_key($sessionkey);
    $easyPay->set_enc_data($encrypt_data);

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
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_cd"    , $deli_cd     );		
    $easyPay->set_easypay_deli_us( $mgr_data, "req_ip"          , $client_ip        );
    $easyPay->set_easypay_deli_us( $mgr_data, "req_id"          , $req_id           );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_msg"         , $mgr_msg          );
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

/* -------------------------------------------------------------------------- */
/* ::: ������ DB ó��                                                         */
/* -------------------------------------------------------------------------- */
/* �����ڵ�(res_cd)�� "0000" �̸� ������� �Դϴ�.                            */
/* r_amount�� �ֹ�DB�� �ݾװ� �ٸ� �� �ݵ�� ��� ��û�� �Ͻñ� �ٶ��ϴ�.     */
/* DB ó�� ���� �� ��� ó���� ���ֽñ� �ٶ��ϴ�.                             */
/* -------------------------------------------------------------------------- */


//////////////////////////// ���� Start ////////////////////////////
//--- �α� ����
$settlelog	= '';
$settlelog	.= '===================================================='.chr(10);
$settlelog	.= '�ֹ���ȣ : '.$order_no.chr(10);
$settlelog	.= '�ŷ���ȣ : '.$r_cno.chr(10)  ;
$settlelog	.= '����ڵ� : '.$res_cd.chr(10);
$settlelog	.= '������� : '.$res_msg.chr(10);
$settlelog	.= 'ó����¥ : '.$r_tran_date.chr(10);
$settlelog	.= 'ó����IP : '.$_SERVER['REMOTE_ADDR'].chr(10);
 
	 
if ( $res_cd == "0000" )	// ���� ����
{
	$getPgResult		= true;
	$settlelog	= '===================================================='.chr(10).'����ũ�� ��۵�� : ó���Ϸ�ð�('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
}else{	// ���� ����

	$settlelog	= '===================================================='.chr(10).'����ũ�� ��۵�� : ����Ȯ�νð�('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);

	// PG ���
	$getPgResult		= false;
}

 
//////////////////////////// ���� End ////////////////////////////


//--- ������ ��� ó��
if( $getPgResult === true ){
	// �ǵ���Ÿ ����
	$db->query("
	UPDATE ".GD_ORDER." SET
		escrowconfirm	= 1,
		settlelog		= concat(ifnull(settlelog,''),'$settlelog')
	WHERE ordno='$order_no'"
	);
} else {
	// �ǵ���Ÿ ����
	$db->query("
	UPDATE ".GD_ORDER." SET
		settlelog		= concat(ifnull(settlelog,''),'$settlelog')
	WHERE ordno='$order_no'"
	);
}
msg($res_msg);
exit;
?>