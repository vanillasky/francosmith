<?
    /* ============================================================================== */
    /* =   PAGE : ���� ��û �� ��� ó�� PAGE                                       = */
    /* = -------------------------------------------------------------------------- = */
    /* =   ������ ������ �߻��ϴ� ��� �Ʒ��� �ּҷ� �����ϼż� Ȯ���Ͻñ� �ٶ��ϴ�.= */
    /* =   ���� �ּ� : http://testpay.kcp.co.kr/pgsample/FAQ/search_error.jsp       = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2010.05   KCP Inc.   All Rights Reserved.                 = */
    /* ============================================================================== */

	include_once "../../../../lib/library.php";
	include_once "../../../../conf/config.php";
	include_once "../../../../conf/config.mobileShop.php";
	@include_once "../../../../conf/pg.kcp.php";

	$page_type = $_GET['page_type'];

	if($page_type=='mobile') {
		$order_end_page = $cfgMobileShop['mobileShopRootDir'].'/ord/order_end.php';
		$order_fail_page = $cfgMobileShop['mobileShopRootDir'].'/ord/order_fail.php';
	}
	else {
		$order_end_page = $cfg['rootDir'].'/order/order_end.php';
		$order_fail_page = $cfg['rootDir'].'/order/order_fail.php';
	}

	// PG���� ������ üũ �� ��ȿ�� üũ
	if (forge_order_check($_POST['ordr_idxx'],$_POST['good_mny']) === false && $_POST['req_tx'] == 'pay') {
		msg('�ֹ� ������ ���� ������ ���� �ʽ��ϴ�. �ٽ� ���� �ٶ��ϴ�.',$order_fail_page.'?ordno='.$_POST['ordr_idxx'],'parent');
		exit();
	}

	// ���̹� ���ϸ��� ���� ���� API
	include dirname(__FILE__).'/../../../../lib/naverNcash.class.php';
	$naverNcash = new naverNcash(true);
	if ($naverNcash->useyn == 'Y' && $_POST['req_tx'] != 'mod_escrow') {
		if ($_POST['use_pay_method'] == '001000000000') $ncashResult = $naverNcash->payment_approval($_POST['ordr_idxx'], false);
		else $ncashResult = $naverNcash->payment_approval($_POST['ordr_idxx'], true);
		if ($ncashResult === false) {
			msg('���̹� ���ϸ��� ��뿡 �����Ͽ����ϴ�.', $order_fail_page.'?ordno='.$_POST['ordr_idxx'],'parent');
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

		$settlelog = "{$data[ordr_idxx]} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . implode( "\n", $tmp_log ) . "\n-----------------------------------\n";
		return $settlelog;
	}

    /* ============================================================================== */
    /* =   ȯ�� ���� ���� Include                                                   = */
    /* = -------------------------------------------------------------------------- = */
    /* =   �� �ʼ�                                                                  = */
    /* =   �׽�Ʈ �� �ǰ��� ������ site_conf_inc.php������ �����Ͻñ� �ٶ��ϴ�.     = */
    /* = -------------------------------------------------------------------------- = */

	$g_conf_home_dir  = $_SERVER['DOCUMENT_ROOT'].$cfg[rootDir]."/order/card/kcp/mobile/receipt";     // BIN ������ �Է� (bin������)
	//$g_conf_gw_url    = "testpaygw.kcp.co.kr";
    //$g_conf_site_cd   = "T0000";
	//$g_conf_site_key  = "3grptw1.zW0GSo4PQdaGvsF__";
	$g_conf_site_name = "KCP SHOP";
	$g_conf_gw_url    = "paygw.kcp.co.kr";	// �ǰ����� paygw.kcp.co.kr
	$g_conf_site_cd   = $pg[id];				// �ǰ����� ���� ID
    $g_conf_site_key  = $pg[key];				// �ǰ����� ���� key
	$g_conf_gw_port   = "8090";        // ��Ʈ��ȣ(����Ұ�)
	$g_conf_log_level = "3";			// ���� �α� ���� (����Ұ�)
	$g_conf_module_type = "01";			// ���� ��� Ÿ�� ���� (����Ұ�)

    require "pp_ax_hub_lib.php";              // library [�����Ұ�]

	$module_type      = "01";          // ����Ұ�
	/* ============================================================================== */
    /* = ����Ʈ�� SOAP ��� ����                                                     = */
    /* =----------------------------------------------------------------------------= */
    /* = �׽�Ʈ �� : KCPPaymentService.wsdl                                         = */
    /* = �ǰ��� �� : real_KCPPaymentService.wsdl                                    = */
    /* ============================================================================== */
    $g_wsdl           = "real_KCPPaymentService.wsdl";

    /* = -------------------------------------------------------------------------- = */
    /* =   ȯ�� ���� ���� Include END                                               = */
    /* ============================================================================== */
?>

<?
    /* ============================================================================== */
    /* =   01. ���� ��û ���� ����                                                  = */
    /* = -------------------------------------------------------------------------- = */
	$req_tx         = $_POST[ "req_tx"         ]; // ��û ����
	$tran_cd        = $_POST[ "tran_cd"        ]; // ó�� ����
	/* = -------------------------------------------------------------------------- = */
	$cust_ip        = getenv( "REMOTE_ADDR"    ); // ��û IP
	$ordr_idxx      = $_POST[ "ordr_idxx"      ]; // ���θ� �ֹ���ȣ
	$good_name      = $_POST[ "good_name"      ]; // ��ǰ��
	$good_mny       = $_POST[ "good_mny"       ]; // ���� �ѱݾ�
	/* = -------------------------------------------------------------------------- = */
    $res_cd         = "";                         // �����ڵ�
    $res_msg        = "";                         // ����޽���
    $tno            = $_POST[ "tno"            ]; // KCP �ŷ� ���� ��ȣ
    /* = -------------------------------------------------------------------------- = */
    $buyr_name      = $_POST[ "buyr_name"      ]; // �ֹ��ڸ�
    $buyr_tel1      = $_POST[ "buyr_tel1"      ]; // �ֹ��� ��ȭ��ȣ
    $buyr_tel2      = $_POST[ "buyr_tel2"      ]; // �ֹ��� �ڵ��� ��ȣ
    $buyr_mail      = $_POST[ "buyr_mail"      ]; // �ֹ��� E-mail �ּ�
    /* = -------------------------------------------------------------------------- = */
    $mod_type       = $_POST[ "mod_type"       ]; // ����TYPE VALUE ������ҽ� �ʿ�
    $mod_desc       = $_POST[ "mod_desc"       ]; // �������
    /* = -------------------------------------------------------------------------- = */
    $use_pay_method = $_POST[ "use_pay_method" ]; // ���� ���
    $bSucc          = "";                         // ��ü DB ó�� ���� ����
    /* = -------------------------------------------------------------------------- = */
	$app_time       = "";                         // ���νð� (��� ���� ���� ����)
	$amount         = "";                         // KCP ���� �ŷ� �ݾ�
	$total_amount   = 0;                          // ���հ����� �� �ŷ��ݾ�
    /* = -------------------------------------------------------------------------- = */
    $card_cd        = "";                         // �ſ�ī�� �ڵ�
    $card_name      = "";                         // �ſ�ī�� ��
    $app_no         = "";                         // �ſ�ī�� ���ι�ȣ
    $noinf          = "";                         // �ſ�ī�� ������ ����
    $quota          = "";                         // �ſ�ī�� �Һΰ���
	/* = -------------------------------------------------------------------------- = */
    $bankname       = "";                         // �Ա��� �����
    $depositor      = "";                         // �Ա��� ���� ������ ����
    $account        = "";                         // �Ա��� ���� ��ȣ
    /* = -------------------------------------------------------------------------- = */
	$pnt_issue      = "";                         // ���� ����Ʈ�� �ڵ�
	$pt_idno        = "";                         // ���� �� ���� ���̵�
	$pnt_amount     = "";                         // �����ݾ� or ���ݾ�
	$pnt_app_time   = "";                         // ���νð�
	$pnt_app_no     = "";                         // ���ι�ȣ
    $add_pnt        = "";                         // �߻� ����Ʈ
	$use_pnt        = "";                         // ��밡�� ����Ʈ
	$rsv_pnt        = "";                         // ���� ����Ʈ
    /* = -------------------------------------------------------------------------- = */
	$commid         = "";                         // ��Ż� �ڵ�
	$mobile_no      = "";                         // �޴��� ��ȣ
	/* = -------------------------------------------------------------------------- = */
	$tk_van_code    = "";                         // �߱޻� �ڵ�
	$tk_app_no      = "";                         // ��ǰ�� ���� ��ȣ
	/* = -------------------------------------------------------------------------- = */
    $cash_yn        = $_POST[ "cash_yn"        ]; // ���ݿ����� ��� ����
    $cash_authno    = "";                         // ���� ������ ���� ��ȣ
    $cash_tr_code   = $_POST[ "cash_tr_code"   ]; // ���� ������ ���� ����
    $cash_id_info   = $_POST[ "cash_id_info"   ]; // ���� ������ ��� ��ȣ
    /* ============================================================================== */

    /* ============================================================================== */
    /* =   02. �ν��Ͻ� ���� �� �ʱ�ȭ                                              = */
    /* = -------------------------------------------------------------------------- = */
    /* =       ������ �ʿ��� �ν��Ͻ��� �����ϰ� �ʱ�ȭ �մϴ�.                     = */
    /* = -------------------------------------------------------------------------- = */
    $c_PayPlus = new C_PP_CLI;

    $c_PayPlus->mf_clear();
    /* ------------------------------------------------------------------------------ */
	/* =   02. �ν��Ͻ� ���� �� �ʱ�ȭ END											= */
	/* ============================================================================== */


    /* ============================================================================== */
    /* =   03. ó�� ��û ���� ����                                                  = */
    /* = -------------------------------------------------------------------------- = */

    /* = -------------------------------------------------------------------------- = */
    /* =   03-1. ���� ��û                                                          = */
    /* = -------------------------------------------------------------------------- = */
    if ( $req_tx == "pay" )
    {
            $c_PayPlus->mf_set_encx_data( $_POST[ "enc_data" ], $_POST[ "enc_info" ] );
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   03-2. ���/���� ��û                                                     = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $req_tx == "mod" )
    {
        $tran_cd = "00200000";

        $c_PayPlus->mf_set_modx_data( "tno",      $tno      ); // KCP ���ŷ� �ŷ���ȣ
        $c_PayPlus->mf_set_modx_data( "mod_type", $mod_type ); // ���ŷ� ���� ��û ����
        $c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip  ); // ���� ��û�� IP
        $c_PayPlus->mf_set_modx_data( "mod_desc", $mod_desc ); // ���� ����
    }
	/* ------------------------------------------------------------------------------ */
	/* =   03.  ó�� ��û ���� ���� END  											= */
	/* ============================================================================== */



    /* ============================================================================== */
    /* =   04. ����                                                                 = */
    /* = -------------------------------------------------------------------------- = */
    if ( $tran_cd != "" )
    {
        $c_PayPlus->mf_do_tx( $trace_no, $g_conf_home_dir, $g_conf_site_cd, $g_conf_site_key, $tran_cd, "",
                              $g_conf_gw_url, $g_conf_gw_port, "payplus_cli_slib", $ordr_idxx,
                              $cust_ip, $g_conf_log_level, 0, 0 ); // ���� ���� ó��

		$res_cd  = $c_PayPlus->m_res_cd;  // ��� �ڵ�
		$res_msg = $c_PayPlus->m_res_msg; // ��� �޽���
    }
    else
    {
        $c_PayPlus->m_res_cd  = "9562";
        $c_PayPlus->m_res_msg = "���� ����|tran_cd���� �������� �ʾҽ��ϴ�.";
    }


    /* = -------------------------------------------------------------------------- = */
    /* =   04. ���� END                                                             = */
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   05. ���� ��� �� ����                                                    = */
    /* = -------------------------------------------------------------------------- = */
    if ( $req_tx == "pay" )
    {
        if( $res_cd == "0000" )
        {
            $tno       = $c_PayPlus->mf_get_res_data( "tno"       ); // KCP �ŷ� ���� ��ȣ
            $amount    = $c_PayPlus->mf_get_res_data( "amount"    ); // KCP ���� �ŷ� �ݾ�
			$pnt_issue = $c_PayPlus->mf_get_res_data( "pnt_issue" ); // ���� ����Ʈ�� �ڵ�

    /* = -------------------------------------------------------------------------- = */
    /* =   05-1. �ſ�ī�� ���� ��� ó��                                            = */
    /* = -------------------------------------------------------------------------- = */
            if ( $use_pay_method == "100000000000" )
            {
                $card_cd   = $c_PayPlus->mf_get_res_data( "card_cd"   ); // ī��� �ڵ�
                $card_name = $c_PayPlus->mf_get_res_data( "card_name" ); // ī�� ����
                $app_time  = $c_PayPlus->mf_get_res_data( "app_time"  ); // ���� �ð�
                $app_no    = $c_PayPlus->mf_get_res_data( "app_no"    ); // ���� ��ȣ
                $noinf     = $c_PayPlus->mf_get_res_data( "noinf"     ); // ������ ���� ( 'Y' : ������ )
                $quota     = $c_PayPlus->mf_get_res_data( "quota"     ); // �Һ� ���� ��
            }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-2. ������� ���� ��� ó��                                            = */
    /* = -------------------------------------------------------------------------- = */
            if ( $use_pay_method == "001000000000" )
            {
                $bankname  = $c_PayPlus->mf_get_res_data( "bankname"  ); // �Ա��� ���� �̸�
                $depositor = $c_PayPlus->mf_get_res_data( "depositor" ); // �Ա��� ���� ������
                $account   = $c_PayPlus->mf_get_res_data( "account"   ); // �Ա��� ���� ��ȣ
				$app_time  = $c_PayPlus->mf_get_res_data( "app_time"  ); // ���� �ð�
            }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-3. ����Ʈ ���� ��� ó��                                               = */
    /* = -------------------------------------------------------------------------- = */
            if ( $use_pay_method == "000100000000" )
            {
				$pt_idno      = $c_PayPlus->mf_get_res_data( "pt_idno"      ); // ���� �� ���� ���̵�
                $pnt_amount   = $c_PayPlus->mf_get_res_data( "pnt_amount"   ); // �����ݾ� or ���ݾ�
	            $pnt_app_time = $c_PayPlus->mf_get_res_data( "pnt_app_time" ); // ���νð�
	            $pnt_app_no   = $c_PayPlus->mf_get_res_data( "pnt_app_no"   ); // ���ι�ȣ
	            $add_pnt      = $c_PayPlus->mf_get_res_data( "add_pnt"      ); // �߻� ����Ʈ
                $use_pnt      = $c_PayPlus->mf_get_res_data( "use_pnt"      ); // ��밡�� ����Ʈ
                $rsv_pnt      = $c_PayPlus->mf_get_res_data( "rsv_pnt"      ); // ���� ����Ʈ
            }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-4. �޴��� ���� ��� ó��                                              = */
    /* = -------------------------------------------------------------------------- = */
            if ( $use_pay_method == "000010000000" )
            {
				$app_time  = $c_PayPlus->mf_get_res_data( "hp_app_time"  ); // ���� �ð�
				$commid    = $c_PayPlus->mf_get_res_data( "commid"	     ); // ��Ż� �ڵ�
				$mobile_no = $c_PayPlus->mf_get_res_data( "mobile_no"	 ); // �޴��� ��ȣ
            }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-5. ��ǰ�� ���� ��� ó��                                              = */
    /* = -------------------------------------------------------------------------- = */
            if ( $use_pay_method == "000000001000" )
            {
				$app_time    = $c_PayPlus->mf_get_res_data( "tk_app_time"  ); // ���� �ð�
				$tk_van_code = $c_PayPlus->mf_get_res_data( "tk_van_code"  ); // �߱޻� �ڵ�
				$tk_app_no   = $c_PayPlus->mf_get_res_data( "tk_app_no"    ); // ���� ��ȣ
            }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-6. ���ݿ����� ��� ó��                                               = */
    /* = -------------------------------------------------------------------------- = */
			$cash_yn = $c_PayPlus->mf_get_res_data("cash_yn");	// ���ݿ����� ��Ͽ���
            $cash_authno  = $c_PayPlus->mf_get_res_data( "cash_authno"  ); // ���� ������ ���� ��ȣ
			$cash_id_info = $c_PayPlus->mf_get_res_data("cash_id_info");	// ���ݿ����� ��Ϲ�ȣ

		}
	}
	/* = -------------------------------------------------------------------------- = */
    /* =   05. ���� ��� ó�� END                                                   = */
    /* ============================================================================== */

	/* ============================================================================== */
    /* =   06. ���� �� ���� ��� DBó��                                             = */
    /* = -------------------------------------------------------------------------- = */
	/* =       ����� ��ü ��ü������ DBó�� �۾��Ͻô� �κ��Դϴ�.                 = */
    /* = -------------------------------------------------------------------------- = */

  if( $res_cd == "" ) //������ҽ� (KCP���������� ����â�� �����)
  {
	  msg('������ ����Ͽ����ϴ�');
	  go($cfg['rootDir'].'/order/order.php');
  }
  
	if ( $req_tx == "pay" )
    {
		$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordr_idxx'");
		if( ($oData['step'] > 0 || $oData['vAccount'] != '' || $res_cd=='8128') && $_POST[pay_method] != "SAVE") // �ߺ�����
		{
			### �α� ����
			$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordr_idxx'");
			$res = true;
		}
		else if( $res_cd == "0000" )
        {
			$tno	= $arr[tno] = $c_PayPlus->mf_get_res_data( "tno"	); // KCP �ŷ� ���� ��ȣ

			$amount = $arr[amount] =  $c_PayPlus->mf_get_res_data( "amount" ); // KCP ���� �ŷ� �ݾ�

			// 06-1-1. �ſ�ī��
			if ( $use_pay_method == "100000000000" )
            {
				$card_cd = $arr[card_cd]  = $c_PayPlus->mf_get_res_data( "card_cd"   ); // ī�� �ڵ�
				$card_name = $arr[card_name] = $c_PayPlus->mf_get_res_data( "card_name" ); // ī�� ����
				$app_time = $arr[app_time]  = $c_PayPlus->mf_get_res_data( "app_time"  ); // ���� �ð�
				$app_no = $arr[app_no]   = $c_PayPlus->mf_get_res_data( "app_no"	); // ���� ��ȣ
				$noinf = $arr[noinf]	= $c_PayPlus->mf_get_res_data( "noinf"	 ); // ������ ���� ( 'Y' : ������ )
				$quota = $arr[quota]	= $c_PayPlus->mf_get_res_data( "quota"	 ); // �Һ� ����
			}
			// 06-1-2. �������
			if ( $use_pay_method == "001000000000" )
            {
				$bankname = $arr[bankname]  = $c_PayPlus->mf_get_res_data( "bankname"  ); // �Ա��� ���� �̸�
				$depositor = $arr[depositor] = $c_PayPlus->mf_get_res_data( "depositor" ); // �Ա��� ���� ������
				$account = $arr[account]  = $c_PayPlus->mf_get_res_data( "account"   ); // �Ա��� ���� ��ȣ
				$app_time = $arr[app_time]  = $c_PayPlus->mf_get_res_data( "app_time"  ); // ���� �ð�
			}
			// 06-1-3. ����Ʈ
			if ( $use_pay_method == "000100000000" )
            {
				$pnt_amount = $arr[pnt_amount]  = $c_PayPlus->mf_get_res_data( "pnt_amount"   );
				$pnt_app_time = $arr[pnt_app_time] = $c_PayPlus->mf_get_res_data( "pnt_app_time" );
				$pnt_app_no = $arr[pnt_app_no]   = $c_PayPlus->mf_get_res_data( "pnt_app_no"   );
				$add_pnt  = $arr[add_pnt]	= $c_PayPlus->mf_get_res_data( "add_pnt"	  );
				$use_pnt  = $arr[use_pnt]	= $c_PayPlus->mf_get_res_data( "use_pnt"	  );
				$rsv_pnt   = $arr[rsv_pnt]   = $c_PayPlus->mf_get_res_data( "rsv_pnt"	  );
			}
			// 06-1-4. �޴���
			if ( $use_pay_method == "000010000000" )
            {
				$app_time = $arr[app_time] = $c_PayPlus->mf_get_res_data( "hp_app_time"  ); // ���� �ð�
			}
			// 06-1-5. ��ǰ��
			 if ( $use_pay_method == "000000001000" )
            {
				 $app_time = $arr[app_time] = $c_PayPlus->mf_get_res_data( "tk_app_time"  ); // ���� �ð�
			}

			// ���ݿ�����
			//$cash_yn = $arr[cash_yn] = $c_PayPlus->mf_get_res_data("cash_yn");	// ���ݿ����� ��Ͽ���
			//$cash_authno = $arr[cash_authno] = $c_PayPlus->mf_get_res_data("cash_authno");	// ���ݿ����� ���ι�ȣ
			//$cash_id_info = $arr[cash_id_info] = $c_PayPlus->mf_get_res_data("cash_id_info");	// ���ݿ����� ��Ϲ�ȣ

			$query = "
				select * from
					".GD_ORDER." a
					left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
				where
					a.ordno='$ordr_idxx'
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
					if ($_SESSION[eggData][ordno] == $ordr_idxx && $_SESSION[eggData][resno1] != '' && $_SESSION[eggData][resno2] != '' && $_SESSION[eggData][agree] == 'Y'){
						include '../../../../lib/egg.class.usafe.php';
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
							//$inipay->m_resultCode = '';
						}
						else if ( $eggCls->isErr == true && in_array($use_pay_method, array("100000000000","010000000000")) );
					}
					session_unregister('eggData');
				}

				### ������� ������ ��� üũ �ܰ� ����
				$res_cstock = true;
				if($cfg['stepStock'] == '1' && $use_pay_method=="001000000000") $res_cstock = false;

				### item check stock
				include "../../../../lib/cardCancel.class.php";
				$cancel = new cardCancel();
				if(!$cancel->chk_item_stock($ordr_idxx) && $res_cstock){
					$step = 51; $qrc1 = $qrc2 = "";
				}

				if($step == 51) $cancel->cancel_db_proc($ordr_idxx,$tno);
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
					where ordno='$ordr_idxx'"
					);
					$res = $db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$ordr_idxx'");

					### �ֹ��α� ����
					orderLog($ordr_idxx,$r_step2[$data[step2]]." > ".$r_step[$step]);

					### ��� ó��
					setStock($ordr_idxx);

					### ��ǰ���Խ� ������ ���
					if ($sess[m_no] && $data[emoney]){
						setEmoney($sess[m_no],-$data[emoney],"��ǰ���Խ� ������ ���� ���",$ordr_idxx);
					}

					### �ֹ�Ȯ�θ���
					if(function_exists('getMailOrderData')){
						sendMailCase($data['email'],0,getMailOrderData($ordr_idxx));
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

				if($cash_authno) $db-> query("update ".GD_ORDER." set cashreceipt='$cash_authno' where ordno='$ordr_idxx'"); //���ݿ������� �߱޵Ǿ��� ��� ���ݿ����� ó��
				
				go($order_end_page."?ordno=$ordr_idxx&card_nm=$card_name","parent");
		}

	/* = -------------------------------------------------------------------------- = */
    /* =   06. ���� �� ���� ��� DBó��                                             = */
    /* ============================================================================== */
		else if ( $req_cd != "0000" )
		{
			$res_cd = $arr[res_cd]  = $c_PayPlus->mf_get_res_data( "res_cd"   ); // ��� �ڵ�
			$card_cd = $arr[card_cd]  = $c_PayPlus->mf_get_res_data( "card_cd"   ); // ī�� �ڵ�
			$res_msg = $arr[res_msg] = $c_PayPlus->m_res_msg;

			$arr = array_merge($_POST,$arr);
			$settlelog = settlelog($arr);

			if($_POST[pay_method] == "SAVE" ){
				msg('OKĳ���� �������еǾ����ϴ�.',0);
			}else{
				$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog'),cardtno='".$tno."' where ordno='$ordr_idxx'");
				$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordr_idxx'");
			}

			// Ncash ���� ���� ��� API ȣ��
			if ($naverNcash->useyn == 'Y') $naverNcash->payment_approval_cancel($ordr_idxx);

			$res = false;

			go($order_fail_page."?ordno=$ordr_idxx&card_nm=$card_name","parent");
		}
	}

	/* ============================================================================== */
    /* =   07. ���� ��� DBó�� ���н� : �ڵ����                                   = */
    /* = -------------------------------------------------------------------------- = */
    /* =         ���� ����� DB �۾� �ϴ� �������� ���������� ���ε� �ǿ� ����      = */
    /* =         DB �۾��� �����Ͽ� DB update �� �Ϸ���� ���� ���, �ڵ�����       = */
    /* =         ���� ��� ��û�� �ϴ� ���μ����� �����Ǿ� �ֽ��ϴ�.                = */
	/* =                                                                            = */
    /* =         DB �۾��� ���� �� ���, bSucc ��� ����(String)�� ���� "false"     = */
    /* =         �� ������ �ֽñ� �ٶ��ϴ�. (DB �۾� ������ ��쿡�� "false" �̿��� = */
    /* =         ���� �����Ͻø� �˴ϴ�.)                                           = */
    /* = -------------------------------------------------------------------------- = */

	$bSucc = ""; // DB �۾� ���� �Ǵ� �ݾ� ����ġ�� ��� "false" �� ����

    /* = -------------------------------------------------------------------------- = */
    /* =   07-1. DB �۾� ������ ��� �ڵ� ���� ���                                 = */
    /* = -------------------------------------------------------------------------- = */
    if ( $req_tx == "pay" )
    {
		if( $res_cd == "0000" )
        {
			if ( $bSucc == "false" )
            {
                $c_PayPlus->mf_clear();

                $tran_cd = "00200000";

                $c_PayPlus->mf_set_modx_data( "tno",      $tno                         );  // KCP ���ŷ� �ŷ���ȣ
                $c_PayPlus->mf_set_modx_data( "mod_type", "STSC"                       );  // ���ŷ� ���� ��û ����
                $c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip                     );  // ���� ��û�� IP
                $c_PayPlus->mf_set_modx_data( "mod_desc", "��� ó�� ���� - �ڵ� ���" );  // ���� ����

                $c_PayPlus->mf_do_tx( "",  $g_conf_home_dir, $g_conf_site_cd,
                                      $g_conf_site_key,  $tran_cd,    "",
                                      $g_conf_gw_url,  $g_conf_gw_port,  "payplus_cli_slib",
                                      $ordr_idxx, $cust_ip,    $g_conf_log_level,
                                      0, 0 );

                $res_cd  = $c_PayPlus->m_res_cd;
                $res_msg = $c_PayPlus->m_res_msg;
            }
        }
	} // End of [res_cd = "0000"]

?>