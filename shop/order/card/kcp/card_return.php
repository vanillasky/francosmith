<?
	/* ============================================================================== */
	/* =   PAGE : ���� ��û �� ��� ó�� PAGE									   = */
	/* = -------------------------------------------------------------------------- = */
	/* =   Copyright (c)  2006   KCP Inc.   All Rights Reserverd.				   = */
	/* ============================================================================== */

	include "../../../lib/library.php";
	include "../../../conf/config.php";
	@include "../../../conf/pg.kcp.php";

	// PG���� ������ üũ �� ��ȿ�� üũ
	if (forge_order_check($_POST['ordr_idxx'],$_POST['good_mny']) === false && $_POST['req_tx'] == 'pay') {
		msg('�ֹ� ������ ���� ������ ���� �ʽ��ϴ�. �ٽ� ���� �ٶ��ϴ�.','../../order_fail.php?ordno='.$_POST['ordr_idxx'],'parent');
		exit();
	}

	// Ncash ���� ���� API
	include "../../../lib/naverNcash.class.php";
	$naverNcash = new naverNcash();
	if($naverNcash->useyn=='Y' && $_POST['req_tx']!='mod_escrow')
	{
		if($_POST['use_pay_method']=='001000000000') $ncashResult = $naverNcash->payment_approval($_POST['ordr_idxx'], false);
		else $ncashResult = $naverNcash->payment_approval($_POST['ordr_idxx'], true);
		if($ncashResult===false)
		{
			msg('���̹� ���ϸ��� ��뿡 �����Ͽ����ϴ�.', '../../order_fail.php?ordno='.$_POST['ordr_idxx'],'parent');
			exit();
		}
	}

	function settlelog($data){
		$tmp_log = array();

		if($data[req_tx])$tmp_log[] = "��û ���� : ".$data[req_tx];
		if($data[use_pay_method])$tmp_log[] = "����� ���� ���� : ".$data[use_pay_method];

		if($data[res_cd])$tmp_log[] = "����ڵ� : ".$data[res_cd];
		if($data[res_msg])$tmp_log[] = "������� : ".$data[res_msg];
		if($data[ordr_idxx])$tmp_log[] = "�ֹ���ȣ : ".$data[ordr_idxx];
		if($data[tno])$tmp_log[] = "KCP �ŷ���ȣ : ".$data[tno];
		if($data[good_mny])$tmp_log[] = "�����ݾ� : ".$data[good_mny];
		if($data[good_name])$tmp_log[] = "��ǰ�� : ".$data[good_name];
		if($data[buyr_name])$tmp_log[] = "�ֹ��ڸ� : ".$data[buyr_name];
		if($data[buyr_tel1])$tmp_log[] = "�ֹ��� ��ȭ��ȣ : ".$data[buyr_tel1];
		if($data[buyr_tel2])$tmp_log[] = "�ֹ��� �޴�����ȣ : ".$data[buyr_tel2];
		if($data[buyr_mail])$tmp_log[] = "�ֹ��� E-mail : ".$data[buyr_mail];

		if($data[card_cd])$tmp_log[] = "ī���ڵ� : ".$data[card_cd];
		if($data[card_name])$tmp_log[] = "ī��� : ".$data[card_name];
		if($data[app_time])$tmp_log[] = "���νð� : ".$data[app_time];
		if($data[app_no])$tmp_log[] = "���ι�ȣ : ".$data[app_no];
		if($data[quota])$tmp_log[] = "�Һΰ��� : ".$data[quota];

		if($data[epnt_issu])$tmp_log[] = "����Ʈ ���񽺻� : ".$data[epnt_issu];
		if($data[pnt_amount])$tmp_log[] = "����Ʈ �����ݾ�(���ݾ�) : ".$data[pnt_amount];
		if($data[pnt_app_time])$tmp_log[] = "����Ʈ���νð� : ".$data[pnt_app_time];
		if($data[pnt_app_no])$tmp_log[] = "����Ʈ���ι�ȣ : ".$data[pnt_app_no];
		if($data[add_pnt])$tmp_log[] = "�߻� ����Ʈ : ".$data[add_pnt];
		if($data[use_pnt])$tmp_log[] = "��밡�� ����Ʈ : ".$data[use_pnt];
		if($data[rsv_pnt])$tmp_log[] = "���� ����Ʈ : ".$data[rsv_pnt];

		if($data[bank_name])$tmp_log[] = "����� : ".$data[bank_name];
		if($data[bank_code])$tmp_log[] = "�����ڵ� : ".$data[bank_code];

		if($data[bankname])$tmp_log[] = "�Ա� ���� : ".$data[bankname];
		if($data[depositor])$tmp_log[] = "�Աݰ��� ������ : ".$data[depositor];
		if($data[account])$tmp_log[] = "�Աݰ��� ��ȣ : ".$data[account];

		if($data[cash_yn])$tmp_log[] = "���ݿ����� ��� ���� : ".$data[cash_yn];
		if($data[cash_authno])$tmp_log[] = "���� ������ ���� ��ȣ : ".$data[cash_authno];
		if($data[cash_tr_code])$tmp_log[] = "���� ������ ���� ���� : ".$data[cash_tr_code];
		if($data[cash_id_info])$tmp_log[] = "���� ������ ��� ��ȣ : ".$data[cash_id_info];

		$settlelog = "{$ordno} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . implode( "\n", $tmp_log ) . "\n-----------------------------------\n";
		return $settlelog;
	}

	/* ============================================================================== */
	/* =   01. ���� ������ �¾� (��ü�� �°� ����)								  = */
	/* = -------------------------------------------------------------------------- = */
	function get_base_dir( $fname ) {
		$tmp = explode( "/", realpath( $fname ) );
		array_pop( $tmp );
		return implode( "/", $tmp );
	}
	$SERVER_DIR = get_base_dir(__FILE__);

	$g_conf_home_dir  = $SERVER_DIR."/payplus"; // BIN ������ �Է�
	$g_conf_log_level = "3";					  // ����Ұ�
	$g_conf_pa_url	= "paygw.kcp.co.kr";	// real url : paygw.kcp.co.kr , test url : testpaygw.kcp.co.kr
	$g_conf_pa_port   = "8090";				   // ��Ʈ��ȣ , ����Ұ�
	$g_conf_mode	  = 0;						// ����Ұ�

	require "pp_ax_hub_lib.php";				  // library [�����Ұ�]
	/* ============================================================================== */


	/* ============================================================================== */
	/* =   02. ���� ��û ���� ����												  = */
	/* = -------------------------------------------------------------------------- = */
	$site_cd		= $_POST[ "site_cd"		]; // ����Ʈ �ڵ�
	$site_key	   = $_POST[ "site_key"	   ]; // ����Ʈ Ű
	$req_tx		 = $_POST[ "req_tx"		 ]; // ��û ����
	$cust_ip		= getenv( "REMOTE_ADDR"	); // ��û IP
	$ordr_idxx	  = $_POST[ "ordr_idxx"	  ]; // ���θ� �ֹ���ȣ
	$good_name	  = $_POST[ "good_name"	  ]; // ��ǰ��
	/* = -------------------------------------------------------------------------- = */
	$good_mny	   = $_POST[ "good_mny"	   ]; // ���� �ѱݾ�
	$tran_cd		= $_POST[ "tran_cd"		]; // ó�� ����
	/* = -------------------------------------------------------------------------- = */
	$res_cd		 = "";						 // �����ڵ�
	$res_msg		= "";						 // ����޽���
	$tno			= $_POST[ "tno"			]; // KCP �ŷ� ���� ��ȣ
	/* = -------------------------------------------------------------------------- = */
	$buyr_name	  = $_POST[ "buyr_name"	  ]; // �ֹ��ڸ�
	$buyr_tel1	  = $_POST[ "buyr_tel1"	  ]; // �ֹ��� ��ȭ��ȣ
	$buyr_tel2	  = $_POST[ "buyr_tel2"	  ]; // �ֹ��� �ڵ��� ��ȣ
	$buyr_mail	  = $_POST[ "buyr_mail"	  ]; // �ֹ��� E-mail �ּ�
	/* = -------------------------------------------------------------------------- = */
	$bank_name	  = "";						 // �����
	$bank_code	  = "";						 // �����ڵ�
	$bank_issu	  = $_POST[ "bank_issu"	  ]; // ������ü ���񽺻�
	/* = -------------------------------------------------------------------------- = */
	$mod_type	   = $_POST[ "mod_type"	   ]; // ����TYPE VALUE ������ҽ� �ʿ�
	$mod_desc	   = $_POST[ "mod_desc"	   ]; // �������
	/* = -------------------------------------------------------------------------- = */
	$use_pay_method = $_POST[ "use_pay_method" ]; // ���� ���
	$epnt_issu	  = $_POST[ "epnt_issu"	  ]; //����Ʈ(OKĳ����,��������Ʈ)
	$bSucc		  = "";						 // ��ü DB ó�� ���� ����
	$acnt_yn		= $_POST[  "acnt_yn"	   ]; // ���º���� ������ü, ������� ����
	$escw_used	  = $_POST[  "escw_used"	 ]; // ����ũ�� ��� ����
	$pay_mod		= $_POST[  "pay_mod"	   ]; // ����ũ�� ����ó�� ���
	$deli_term	  = $_POST[  "deli_term"	 ]; // ��� �ҿ���
	$bask_cntx	  = $_POST[  "bask_cntx"	 ]; // ��ٱ��� ��ǰ ����
	$good_info	  = $_POST[  "good_info"	 ]; // ��ٱ��� ��ǰ �� ����
	$rcvr_name	  = $_POST[  "rcvr_name"	 ]; // ������ �̸�
	$rcvr_tel1	  = $_POST[  "rcvr_tel1"	 ]; // ������ ��ȭ��ȣ
	$rcvr_tel2	  = $_POST[  "rcvr_tel2"	 ]; // ������ �޴�����ȣ
	$rcvr_mail	  = $_POST[  "rcvr_mail"	 ]; // ������ E-Mail
	$rcvr_zipx	  = $_POST[  "rcvr_zipx"	 ]; // ������ �����ȣ
	$rcvr_add1	  = $_POST[  "rcvr_add1"	 ]; // ������ �ּ�
	$rcvr_add2	  = $_POST[  "rcvr_add2"	 ]; // ������ ���ּ�

	/* = -------------------------------------------------------------------------- = */
	$card_cd		= "";						 // �ſ�ī�� �ڵ�
	$card_name	  = "";						 // �ſ�ī�� ��
	$app_time	   = "";						 // ���νð� (��� ���� ���� ����)
	$app_no		 = "";						 // �ſ�ī�� ���ι�ȣ
	$noinf		  = "";						 // �ſ�ī�� ������ ����
	$quota		  = "";						 // �ſ�ī�� �Һΰ���
	$bankname	   = "";						 // �����
	$depositor	  = "";						 // �Ա� ���� ������ ����
	$account		= "";						 // �Ա� ���� ��ȣ
	/* = -------------------------------------------------------------------------- = */
	$amount		 = "";						 // KCP ���� �ŷ� �ݾ�
	/* = -------------------------------------------------------------------------- = */
	$add_pnt		= "";						 // �߻� ����Ʈ
	$use_pnt		= "";						 // ��밡�� ����Ʈ
	$rsv_pnt		= "";						 // ���� ����Ʈ
	$pnt_app_time   = "";						 // ���νð�
	$pnt_app_no	 = "";						 // ���ι�ȣ
	$pnt_amount	 = "";						 // �����ݾ� or ���ݾ�
	/* ============================================================================== */
	$cash_yn		= $_POST[ "cash_yn"		]; // ���ݿ����� ��� ����
	$cash_authno	= "";						 // ���� ������ ���� ��ȣ
	$cash_tr_code   = $_POST[ "cash_tr_code"   ]; // ���� ������ ���� ����
	$cash_id_info   = $_POST[ "cash_id_info"   ]; // ���� ������ ��� ��ȣ

	$ordno = $ordr_idxx;


	/* ============================================================================== */
	/* =   03. �ν��Ͻ� ���� �� �ʱ�ȭ											  = */
	/* = -------------------------------------------------------------------------- = */
	/* =	   ������ �ʿ��� �ν��Ͻ��� �����ϰ� �ʱ�ȭ �մϴ�.					 = */
	/* = -------------------------------------------------------------------------- = */
	$c_PayPlus = new C_PP_CLI;

	$c_PayPlus->mf_clear();
	/* ============================================================================== */


	/* ============================================================================== */
	/* =   04. ó�� ��û ���� ����, ����											= */
	/* = -------------------------------------------------------------------------- = */

	/* = -------------------------------------------------------------------------- = */
	/* =   04-1. ���� ��û														  = */
	/* = -------------------------------------------------------------------------- = */
	if ( $req_tx == "pay" )
	{
		$c_PayPlus->mf_set_ordr_data( 'ordr_mony',  $_POST['good_mny'] );
		$c_PayPlus->mf_set_encx_data( $_POST[ "enc_data" ], $_POST[ "enc_info" ] );
	}

	/* = -------------------------------------------------------------------------- = */
	/* =   04-2. ���/���� ��û													 = */
	/* = -------------------------------------------------------------------------- = */
	else if ( $req_tx == "mod" )
	{
		$tran_cd = "00200000";

		$c_PayPlus->mf_set_modx_data( "tno",	  $tno	  ); // KCP ���ŷ� �ŷ���ȣ
		$c_PayPlus->mf_set_modx_data( "mod_type", $mod_type ); // ���ŷ� ���� ��û ����
		$c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip  ); // ���� ��û�� IP
		$c_PayPlus->mf_set_modx_data( "mod_desc", $mod_desc ); // ���� ����
	}

	/* = -------------------------------------------------------------------------- = */
	/* =   04-3. ����ũ�� ���º��� ��û											  = */
	/* = -------------------------------------------------------------------------- = */
	else if ( $req_tx == "mod_escrow" )
	{
		$tran_cd = "00200000";

		$c_PayPlus->mf_set_modx_data( "tno",		$tno			);		  // KCP ���ŷ� �ŷ���ȣ
		$c_PayPlus->mf_set_modx_data( "mod_type",   $mod_type	   );		  // ���ŷ� ���� ��û ����
		$c_PayPlus->mf_set_modx_data( "mod_ip",	 $cust_ip		);		  // ���� ��û�� IP
		$c_PayPlus->mf_set_modx_data( "mod_desc",   $mod_desc	   );		  // ���� ����
		if ($mod_type == "STE1")												// ���º��� Ÿ���� [��ۿ�û]�� ���
		{
			$c_PayPlus->mf_set_modx_data( "deli_numb",   $_POST[ "deli_numb" ] );		  // ����� ��ȣ
			$c_PayPlus->mf_set_modx_data( "deli_corp",   $_POST[ "deli_corp" ] );		  // �ù� ��ü��
		}
		else if ($mod_type == "STE2" || $mod_type == "STE4")					// ���º��� Ÿ���� [������] �Ǵ� [���]�� ������ü, ��������� ���
		{
			if ($acnt_yn == "Y")
			{
				$c_PayPlus->mf_set_modx_data( "refund_account",   $_POST[ "refund_account" ] );	  // ȯ�Ҽ�����¹�ȣ
				$c_PayPlus->mf_set_modx_data( "refund_nm",		$_POST[ "refund_nm"	  ] );	  // ȯ�Ҽ�������ָ�
				$c_PayPlus->mf_set_modx_data( "bank_code",		$_POST[ "bank_code"	  ] );	  // ȯ�Ҽ��������ڵ�
			}
		}
	}

	/* = -------------------------------------------------------------------------- = */
	/* =   04-3. ����															   = */
	/* = -------------------------------------------------------------------------- = */
	if ( $tran_cd != "" )
	{
		$c_PayPlus->mf_do_tx( $trace_no, $g_conf_home_dir, $site_cd, $site_key, $tran_cd, "",
							  $g_conf_pa_url, $g_conf_pa_port, "payplus_cli_slib", $ordr_idxx,
							  $cust_ip, $g_conf_log_level, 0, $g_conf_mode );
	}
	else
	{
		$c_PayPlus->m_res_cd  = "9562";
		$c_PayPlus->m_res_msg = "���� ���� TRAN_CD[" . $tran_cd . "]";
	}

	$res_cd = $arr[res_cd] = $c_PayPlus->m_res_cd;  // ��� �ڵ�
	$res_msg = $arr[res_msg] = $c_PayPlus->m_res_msg; // ��� �޽���
	/* ============================================================================== */
	// ����ũ�� ��۵�� �ΰ��
	if ( $req_tx == "mod_escrow" ){
		$escrowLog = '';
		$escrowLog .= '=========================================='.chr(10);
		$escrowLog .= '�ֹ���ȣ : '.$_POST['ordno'].chr(10);
		$escrowLog .= '�ŷ���ȣ : '.$tno.chr(10);
		$escrowLog .= '����ڵ� : '.$res_cd.chr(10);
		$escrowLog .= '������� : '.$res_msg.chr(10);
		$escrowLog = '=========================================='.chr(10).'����ũ�� ��۵�� : ('.date('Y-m-d H:i:s').')'.chr(10).$escrowLog.'=========================================='.chr(10);

		if( $res_cd == '0000' ){
			$db->query("update ".GD_ORDER." set escrowconfirm=1, settlelog=concat(ifnull(settlelog,''),'$escrowLog') where ordno='$_POST[ordno]'");
		} else {
			$db->query("update ".GD_ORDER." set escrowconfirm=0, settlelog=concat(ifnull(settlelog,''),'$escrowLog') where ordno='$_POST[ordno]'");
		}
	}


	/* ============================================================================== */
	/* =   05. ���� ��� ó��													   = */
	/* = -------------------------------------------------------------------------- = */
	if ( $req_tx == "pay" )
	{
		$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
		if( ($oData['step'] > 0 || $oData['vAccount'] != '' || $res_cd=='8128') && $_POST[pay_method] != "SAVE") // �ߺ�����
		{
			### �α� ����
			$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
			$res = true;
		}
		else if( $res_cd == "0000" )
		{
			$tno	= $arr[tno] = $c_PayPlus->mf_get_res_data( "tno"	); // KCP �ŷ� ���� ��ȣ

			$amount = $arr[amount] =  $c_PayPlus->mf_get_res_data( "amount" ); // KCP ���� �ŷ� �ݾ�

	/* = -------------------------------------------------------------------------- = */
	/* =   05-1. �ſ�ī�� ���� ��� ó��											= */
	/* = -------------------------------------------------------------------------- = */
			if ( $use_pay_method == "100000000000" )
			{
				$card_cd = $arr[card_cd]  = $c_PayPlus->mf_get_res_data( "card_cd"   ); // ī�� �ڵ�
				$card_name = $arr[card_name] = $c_PayPlus->mf_get_res_data( "card_name" ); // ī�� ����
				$app_time = $arr[app_time]  = $c_PayPlus->mf_get_res_data( "app_time"  ); // ���� �ð�
				$app_no = $arr[app_no]   = $c_PayPlus->mf_get_res_data( "app_no"	); // ���� ��ȣ
				$noinf = $arr[noinf]	= $c_PayPlus->mf_get_res_data( "noinf"	 ); // ������ ���� ( 'Y' : ������ )
				$quota = $arr[quota]	= $c_PayPlus->mf_get_res_data( "quota"	 ); // �Һ� ����

				/* = -------------------------------------------------------------- = */
				/* =   05-1.1. ���հ���(����Ʈ+�ſ�ī��) ���� ��� ó��			   = */
				/* = -------------------------------------------------------------- = */
				if ( $epnt_issu == "SCSK" || $epnt_issu == "SCWB" )
				{
					$pnt_amount  = $arr[pnt_amount]   = $c_PayPlus->mf_get_res_data ( "pnt_amount"   );
					$pnt_app_time  = $arr[pnt_app_time] = $c_PayPlus->mf_get_res_data ( "pnt_app_time" );
					$pnt_app_no  = $arr[pnt_app_no]   = $c_PayPlus->mf_get_res_data ( "pnt_app_no"   );
					$add_pnt  = $arr[add_pnt]	 = $c_PayPlus->mf_get_res_data ( "add_pnt"	  );
					$use_pnt	= $arr[use_pnt]   = $c_PayPlus->mf_get_res_data ( "use_pnt"	  );
					$rsv_pnt	 = $arr[rsv_pnt]  = $c_PayPlus->mf_get_res_data ( "rsv_pnt"	  );
				}
			}

	/* = -------------------------------------------------------------------------- = */
	/* =   05-2. ������ü ���� ��� ó��											= */
	/* = -------------------------------------------------------------------------- = */
			if ( $use_pay_method == "010000000000" )
			{
				$bank_name = $arr[bank_name] = $c_PayPlus->mf_get_res_data( "bank_name"  );  // �����
				$bank_code = $arr[bank_code] = $c_PayPlus->mf_get_res_data( "bank_code"  );  // �����ڵ�
			}

	/* = -------------------------------------------------------------------------- = */
	/* =   05-3. ������� ���� ��� ó��											= */
	/* = -------------------------------------------------------------------------- = */
			if ( $use_pay_method == "001000000000" )
			{
				$bankname = $arr[bankname]  = $c_PayPlus->mf_get_res_data( "bankname"  ); // �Ա��� ���� �̸�
				$depositor = $arr[depositor] = $c_PayPlus->mf_get_res_data( "depositor" ); // �Ա��� ���� ������
				$account = $arr[account]  = $c_PayPlus->mf_get_res_data( "account"   ); // �Ա��� ���� ��ȣ
			}

	/* = -------------------------------------------------------------------------- = */
	/* =   05-4. ����Ʈ ���� ��� ó��											   = */
	/* = -------------------------------------------------------------------------- = */
			if ( $use_pay_method == "000100000000" )
			{
				$pnt_amount = $arr[pnt_amount]  = $c_PayPlus->mf_get_res_data( "pnt_amount"   );
				$pnt_app_time = $arr[pnt_app_time] = $c_PayPlus->mf_get_res_data( "pnt_app_time" );
				$pnt_app_no = $arr[pnt_app_no]   = $c_PayPlus->mf_get_res_data( "pnt_app_no"   );
				$add_pnt  = $arr[add_pnt]	= $c_PayPlus->mf_get_res_data( "add_pnt"	  );
				$use_pnt  = $arr[use_pnt]	= $c_PayPlus->mf_get_res_data( "use_pnt"	  );
				$rsv_pnt   = $arr[rsv_pnt]   = $c_PayPlus->mf_get_res_data( "rsv_pnt"	  );
			}

	/* = -------------------------------------------------------------------------- = */
	/* =   05-5. �޴��� ���� ��� ó��											  = */
	/* = -------------------------------------------------------------------------- = */
			if ( $use_pay_method == "000010000000" )
			{
				$app_time = $arr[app_time] = $c_PayPlus->mf_get_res_data( "hp_app_time"  ); // ���� �ð�
			}

	/* = -------------------------------------------------------------------------- = */
	/* =   05-6. ��ǰ�� ���� ��� ó��											  = */
	/* = -------------------------------------------------------------------------- = */
			if ( $use_pay_method == "000000001000" )
			{
				$app_time = $arr[app_time] = $c_PayPlus->mf_get_res_data( "tk_app_time"  ); // ���� �ð�
			}

	/* = -------------------------------------------------------------------------- = */
	/* =   05-7. Ƽ�Ӵ� ���� ��� ó��											  = */
	/* = -------------------------------------------------------------------------- = */
			if ( $use_pay_method == "000000000100" )
			{
				$app_time = $arr[app_time] = $c_PayPlus->mf_get_res_data("app_time"	  ); // ���νð�
			}

	/* = -------------------------------------------------------------------------- = */
	/* =   05-8. ARS ���� ��� ó��												 = */
	/* = -------------------------------------------------------------------------- = */
			if ( $use_pay_method == "000000000010" )
			{
				$app_time = $arr[app_time] = $c_PayPlus->mf_get_res_data( "ars_app_time" ); // ���� �ð�
			}

	/* = -------------------------------------------------------------------------- = */
	/* =   05-9. ���ݿ����� ��� ó��											   = */
	/* = -------------------------------------------------------------------------- = */
			if ( $cash_yn == "Y" )
			{
				$cash_authno = $arr[cash_authno]  = $c_PayPlus->mf_get_res_data( "cash_authno"  ); // ���� ������ ���� ��ȣ
			}
	/* = -------------------------------------------------------------------------- = */
	/* =   05-10. ���� ����� ��ü ��ü������ DB ó�� �۾��Ͻô� �κ��Դϴ�.		 = */
	/* = -------------------------------------------------------------------------- = */
	/* =		 ���� ����� DB �۾� �ϴ� �������� ���������� ���ε� �ǿ� ����	  = */
	/* =		 DB �۾��� �����Ͽ� DB update �� �Ϸ���� ���� ���, �ڵ�����	   = */
	/* =		 ���� ��� ��û�� �ϴ� ���μ����� �����Ǿ� �ֽ��ϴ�.				= */
	/* =		 DB �۾��� ���� �� ���, bSucc ��� ����(String)�� ���� "false"	 = */
	/* =		 �� ������ �ֽñ� �ٶ��ϴ�. (DB �۾� ������ ��쿡�� "false" �̿��� = */
	/* =		 ���� �����Ͻø� �˴ϴ�.)										   = */
	/* =		 amount(KCP���� �ŷ��ݾ�)�� ��ü�� DB ó���Ͻ� �ݾ��� �ٸ� �����   = */
	/* =		 �� ��ƾ�� �߰� �ϼż� �ٸ� ��� ���������� "false"�� �����Ͽ�	= */
	/* =		 �ֽñ� �ٶ��ϴ�.												   = */
	/* = -------------------------------------------------------------------------- = */
			if( $_POST[pay_method] == "SAVE" && $res_cd == "0000" ){

				$add_pnt = $r_cashbag['add_pnt'] = $c_PayPlus->mf_get_res_data("add_pnt");
				$use_pnt = $r_cashbag['use_pnt'] =  $c_PayPlus->mf_get_res_data("use_pnt");
				$rsv_pnt = $r_cashbag['rsv_pnt'] = $c_PayPlus->mf_get_res_data("rsv_pnt");
				$pnt_app_time = $r_cashbag['pnt_app_time'] = $c_PayPlus->mf_get_res_data("pnt_app_time");
				$pnt_amount = $r_cashbag['pnt_amount'] = $c_PayPlus->mf_get_res_data("pnt_amount");

				### �ֹ����� ĳ���� ���� ���� ������Ʈ
				$query = "update ".GD_ORDER." set cbyn='Y' where ordno = '$ordno' and cbyn='N' and step='4' and step2 = '0'";
				$db -> query($query);

				### okĳ���� �����α�
				$query = "insert into ".GD_ORDER_OKCASHBAG." set ordno='$ordno', tno = '$tno', add_pnt='$add_pnt', use_pnt='$use_pnt', rsv_pnt='$rsv_pnt', pnt_app_time='$pnt_app_time', pnt_amount='$pnt_amount'";
				$db -> query($query);

				msg('OKĳ���� �������� �����Ǿ����ϴ�.',0);
				echo("<script>parent.location.reload();</script>");
				exit;
			}



			$query = "
			select * from
				".GD_ORDER." a
				left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
			where
				a.ordno='$ordno'
			";
			$data = $db->fetch($query);

			### ����ũ�� ���� Ȯ��
			$escrowyn = ($_POST['escw_used']=="Y") ? "y" : "n";
			if($escrowyn == 'y')$escrowno = $tno;

			$arr = array_merge($_POST,$arr);
			$settlelog = settlelog($arr);

			### ���� ���� ����
			$step = 1;
			$qrc1 = "cyn='y', cdt=now(),";
			$qrc2 = "cyn='y',";


			switch ($use_pay_method) {
				case "010000000000" : //������ü
					 // ����� $bank_name �����ڵ� $bank_code

				break;
				case "001000000000" : //�������
					// �Ա��� ���� �̸� $bankname �Ա��� ���� ������ $depositor �Ա��� ���� ��ȣ $account
					$vAccount = $bankname." ".$account." ".$depositor;
					$step = 0; $qrc1 = $qrc2 = "";
				break;
			}

			### ���ں������� �߱�
			@session_start();
			if (session_is_registered('eggData') === true && $res_cd == "0000"){
				if ($_SESSION[eggData][ordno] == $ordno && $_SESSION[eggData][resno1] != '' && $_SESSION[eggData][resno2] != '' && $_SESSION[eggData][agree] == 'Y'){
					include '../../../lib/egg.class.usafe.php';
					$eggData = $_SESSION[eggData];
					switch ($use_pay_method){
						case "100000000000":
							$eggData[payInfo1] = $card_name; # (*) ��������(ī���)
							$eggData[payInfo2] = $app_no; # (*) ��������(���ι�ȣ)
							break;
						case "010000000000":
							$eggData[payInfo1] = $bank_name; # (*) ��������(�����)
							$eggData[payInfo2] = $tno; # (*) ��������(���ι�ȣ or �ŷ���ȣ)
							break;
						case "001000000000":
							$eggData[payInfo1] = $bank_name; # (*) ��������(�����)
							$eggData[payInfo2] = $account; # (*) ��������(���¹�ȣ)
							break;
					}
					$eggCls = new Egg( 'create', $eggData );
					if ( $eggCls->isErr == true && $use_pay_method == "001000000000" ){
						$res_cd = ''; $step = 51;
					}
					else if ( $eggCls->isErr == true && in_array($use_pay_method, array("100000000000","010000000000")) );
				}
				session_unregister('eggData');
			}

			### ������� ������ ��� üũ �ܰ� ����
			$res_cstock = true;
			if($cfg['stepStock'] == '1' && $use_pay_method=="001000000000") $res_cstock = false;

			### item check stock
			include "../../../lib/cardCancel.class.php";
			$cancel = new cardCancel();
			if(!$cancel->chk_item_stock($ordno) && $res_cstock){
				$step = 51; $qrc1 = $qrc2 = "";
			}

			if($step == 51) $cancel->cancel_db_proc($ordno,$tno);
			else {
				### �ǵ���Ÿ ����
				$db->query("
				update ".GD_ORDER." set $qrc1
					step		= '$step',
					step2		= '',
					escrowyn	= '$escrowyn',
					escrowno	= '$escrowno',
					vAccount	= '$vAccount',
					cardtno		= '".$tno."',
					settlelog	= concat(ifnull(settlelog,''),'$settlelog')
				where ordno='$ordno'"
				);
				$res = $db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$ordno'");

				### �ֹ��α� ����
				orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

				### ��� ó��
				setStock($ordno);

				### ��ǰ���Խ� ������ ���
				if ($data[m_no] && $data[emoney]){
					setEmoney($data[m_no],-$data[emoney],"��ǰ���Խ� ������ ���� ���",$ordno);
				}

				### �ֹ�Ȯ�θ���
				if(function_exists('getMailOrderData')){
					sendMailCase($data['email'],0,getMailOrderData($ordno));
				}

				### SMS ���� ����
				$dataSms = $data;

				if ($use_pay_method != "001000000000"){ //������°� �ƴ� ���
					sendMailCase($data[email],1,$data);			### �Ա�Ȯ�θ���
					sendSmsCase('incash',$data[mobileOrder]);	### �Ա�Ȯ��SMS
				} else {
					sendSmsCase('order',$data[mobileOrder]);	### �ֹ�Ȯ��SMS
				}
			}


			if($res && $step != 51) {
				$bSucc = "true"; // DB �۾� ���� �Ǵ� �ݾ� ����ġ�� ��� "false" �� ����
				$res = true;
			}else{
				$bSucc = "false";
				$res = false;
			}

	/* = -------------------------------------------------------------------------- = */
	/* =   05-11. DB �۾� ������ ��� �ڵ� ���� ���								 = */
	/* = -------------------------------------------------------------------------- = */
			if ( $bSucc == "false" )
			{
				$c_PayPlus->mf_clear();

				$tran_cd = "00200000";

				$c_PayPlus->mf_set_modx_data( "tno",	  $tno						 );  // KCP ���ŷ� �ŷ���ȣ
				$c_PayPlus->mf_set_modx_data( "mod_type", "STSC"					   );  // ���ŷ� ���� ��û ����
				$c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip					 );  // ���� ��û�� IP
				$c_PayPlus->mf_set_modx_data( "mod_desc", "��� ó�� ���� - �ڵ� ���" );  // ���� ����

				$c_PayPlus->mf_do_tx( $tno,  $g_conf_home_dir, $site_cd,
									  $site_key,  $tran_cd,	"",
									  $g_conf_pa_url,  $g_conf_pa_port,  "payplus_cli_slib",
									  $ordr_idxx, $cust_ip,	$g_conf_log_level,
									  0,	$g_conf_mode );

				$res_cd = $arr[res_cd]  = $c_PayPlus->m_res_cd;
				$res_msg = $arr[res_msg] = $c_PayPlus->m_res_msg;
			}

		} // End of [res_cd = "0000"]

	/* = -------------------------------------------------------------------------- = */
	/* =   05-12. ���� ���и� ��ü ��ü������ DB ó�� �۾��Ͻô� �κ��Դϴ�.		 = */
	/* = -------------------------------------------------------------------------- = */
		else
		{

			$arr = array_merge($_POST,$arr);
			$settlelog = settlelog($arr);

			if($_POST[pay_method] == "SAVE" ){
				msg('OKĳ���� �������еǾ����ϴ�.',0);
			}else{
				$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog'),cardtno='".$tno."' where ordno='$ordno'");
				$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno'");
			}

			$res = false;

		}
	}
	/* ============================================================================== */


	/* ============================================================================== */
	/* =   06. ���/���� ��� ó��												  = */
	/* = -------------------------------------------------------------------------- = */
	else if ( $req_tx == "mod" )
	{
	}
	if($cash_authno) $db-> query("update ".GD_ORDER." set cashreceipt='$cash_authno' where ordno='$ordno'"); //���ݿ������� �߱޵Ǿ��� ��� ���ݿ����� ó��
	?>
	<script>
	var openwin = window.open( 'proc_win.html', 'proc_win', '' );
	openwin.close();
	</script>
	<?

	if($res && $req_tx == "pay")go("../../order_end.php?ordno=$ordno&card_nm=$card_name","parent");
	else if($req_tx == "pay" && !$res) {
		// Ncash ���� ���� ��� API ȣ��
		if($naverNcash->useyn=='Y') $naverNcash->payment_approval_cancel($ordno);

		go("../../order_fail.php?ordno=$ordno","parent");
	}
	else if ( $req_tx == "mod" ){
		### ĳ���� �������
		$settlelog = chr(10). $tno. " ���� ��� ".date('Y-m-d h:i:s',time());
		$query = "select ordno from  ".GD_ORDER_OKCASHBAG." where tno='$tno' limit 1";
		list($ordno) = $db -> fetch($query);
		$db->query("update ".GD_ORDER." set cbyn='N',settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		$query = "delete from  ".GD_ORDER_OKCASHBAG." where tno='$tno'";
		$db -> query($query);

		echo("<script>alert('ĳ���� ������ ��� �Ǿ����ϴ�.');opener.location.reload();self.close();</script>");
		exit;
	}
	?>
