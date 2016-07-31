<?php

	include "../../../lib/library.php";
	include "../../../conf/config.php";
	include "../../../conf/pg.easypay.php";
    include "./inc/easypay_config.php";
    include "./easypay_client.php";

 
	// PG���� ������ üũ �� ��ȿ�� üũ
	if (forge_order_check($_POST['EP_order_no'],$_POST['EP_product_amt']) === false) {
		msg('�ֹ� ������ ���� ������ ���� �ʽ��ϴ�. �ٽ� ���� �ٶ��ϴ�.','../../order_fail.php?ordno='.$_POST['EP_order_no'],'parent');
		exit();
	}

	// Ncash ���� ���� API
	include "../../../lib/naverNcash.class.php";
	$naverNcash = new naverNcash();
	if($naverNcash->useyn=='Y')
	{
		if($_POST["EP_ret_pay_type"]=="22") $ncashResult = $naverNcash->payment_approval($_POST['EP_order_no'], false);
		else $ncashResult = $naverNcash->payment_approval($_POST['EP_order_no'], true);
		if($ncashResult===false)
		{
			msg('���̹� ���ϸ��� ��뿡 �����Ͽ����ϴ�.', '../../order_fail.php?ordno='.$_POST['EP_order_no'],'parent');
			exit();
		}
	}

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
$order_no         = $_POST["EP_order_no"];         // [�ʼ�]�ֹ���ȣ
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

//--- �������� ���
$easypayHome	= dirname($_SERVER['SCRIPT_FILENAME']);      // �̴����� Ȩ���͸�
$logfile		= fopen( $easypayHome . '/log/easypay_log_'.date('Ymd').'.log', 'a+' );

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

/* -------------------------------------------------------------------------- */
/* ::: ������ DB ó��                                                         */
/* -------------------------------------------------------------------------- */
/* �����ڵ�(res_cd)�� "0000" �̸� ������� �Դϴ�.                            */
/* r_amount�� �ֹ�DB�� �ݾװ� �ٸ� �� �ݵ�� ��� ��û�� �Ͻñ� �ٶ��ϴ�.     */
/* DB ó�� ���� �� ��� ó���� ���ֽñ� �ٶ��ϴ�.                             */
/* -------------------------------------------------------------------------- */


//////////////////////////// ���� Start ////////////////////////////

if( $TRAN_CD_NOR_PAYMENT == $tr_cd )
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

$settlelog = "
*********************�ֹ�ó��********************
�ֹ���ȣ : $order_no
ó������ : ".date('Y:m:d H:i:s')."
�����ڵ� : $res_cd
����޽��� : $res_msg
PG�ŷ���ȣ : $r_cno
�� �����ݾ� : $r_amount
���ι�ȣ : $r_auth_no
�����Ͻ� : $r_tran_date
����ũ�� ������� : $r_escrow_yn
--------------------------------------------------";
$settlelog .= $settlelogAdd."
**************************************************";
 
	### ���ں������� �߱�
	@session_start();
	if (session_is_registered('eggData') === true && !strcmp($res_cd,"0000")){
		if ($_SESSION[eggData][ordno] == $order_no && $_SESSION[eggData][resno1] != '' && $_SESSION[eggData][resno2] != '' && $_SESSION[eggData][agree] == 'Y'){
			include '../../../lib/egg.class.usafe.php';
			$eggData = $_SESSION[eggData];
			switch ($pay_type){
				case "11":
					$eggData[payInfo1] = $r_issuer_nm; # (*) ��������(ī���)
					$eggData[payInfo2] = $r_auth_no; # (*) ��������(���ι�ȣ)
					break;
				case "21":
					$eggData[payInfo1] = $r_bank_nm; # (*) ��������(�����)
					$eggData[payInfo2] = $r_cno; # (*) ��������(���ι�ȣ or �ŷ���ȣ)
					break;
				case "22":
					$eggData[payInfo1] = $r_bank_nm; # (*) ��������(�����)
					$eggData[payInfo2] = $r_account_no; # (*) ��������(���¹�ȣ)
					break;
			}
			$eggCls = new Egg( 'create', $eggData );
			if ( $eggCls->isErr == true && $pay_type == "22" ){
				$res_cd = '';
			}
			else if ( $eggCls->isErr == true && in_array($pay_type, array("11","21","22")) );
		}
		session_unregister('eggData');
	}

	### ������� ������ ��� üũ �ܰ� ����
	$res_cstock = true;
	if($cfg['stepStock'] == '1' && $pay_type=="22") $res_cstock = false;

	### item check stock
	include "../../../lib/cardCancel.class.php";
	$cancel = new cardCancel();
	if(!$cancel->chk_item_stock($order_no) && $res_cstock){
		$step = 51; $qrc1 = $qrc2 = "";
	}

	### DB(����&����) ó��
	$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='".$order_no."'");
	if ($oData['step'] > 0 || $oData['vAccount'] != '') { // �ߺ�����

		### �α� ����
		$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'".$settlelog."') where ordno='".$order_no."'");
		go('../../order_end.php?ordno='.$order_no.'&card_nm='.$r_bank_nm,'parent');

	}else if ( $res_cd == "0000" )	// ���� ����
	{
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

		//$bDBProc = "false";     // DBó�� ���� �� "true", ���� �� "false"

		if($res && $step != 51) {
			$bDBProc = "true"; // DB �۾� ���� �Ǵ� �ݾ� ����ġ�� ��� "false" �� ����
			$res = true;
			
			// �����ݾ� ������ üũ
			if (forge_order_check($_POST['EP_order_no'],$easyPay->_easypay_resdata['amount']) === false) {
				$bDBProc = "false";
			}
		}else{
			$bDBProc = "false";
			$res = false;
		}

		if ( $bDBProc != "true" )
		{
			// ���ο�û�� ���� �� �Ʒ� ����
			if( $TRAN_CD_NOR_PAYMENT == $tr_cd )
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
				if($naverNcash->useyn=='Y') $naverNcash->payment_approval_cancel($order_no);

				go("../../order_fail.php?ordno=$order_no","parent");
			}
		}else{
			 
			 
			go("../../order_end.php?ordno=$order_no&card_nm=$r_issuer_nm","parent");
		}
	}else{	// ���� ����

		if ($step == '51') {
			$cancel->cancel_db_proc($order_no);
		} else {
			$db->query("update ".GD_ORDER." set step2='54', settlelog=concat(ifnull(settlelog,''),'".$settlelog."') where ordno='".$order_no."'");
			$db->query("update ".GD_ORDER_ITEM." set istep='54' where ordno='".$order_no."'");
		}
	 
		// Ncash ���� ���� ��� API ȣ��
		if($naverNcash->useyn=='Y') $naverNcash->payment_approval_cancel($order_no);

		go("../../order_fail.php?ordno=$order_no","parent");
	}
}
//////////////////////////// ���� End ////////////////////////////

?>