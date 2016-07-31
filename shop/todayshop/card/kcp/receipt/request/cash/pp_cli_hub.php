<?
/* ============================================================================== */
/* =   PAGE : ���/���� ó�� PAGE                                               = */
/* = -------------------------------------------------------------------------- = */
/* =   Copyright (c)  2007   KCP Inc.   All Rights Reserverd.                   = */
/* ============================================================================== */


/* ============================================================================== */
/* = ���̺귯�� �� ����Ʈ ���� include                                          = */
/* = -------------------------------------------------------------------------- = */
if (isset($_GET['crno']) === false)
{
	include dirname(__FILE__).'/../../../../../../lib/library.php';
}
include dirname(__FILE__).'/../../../../../../conf/config.php';
include dirname(__FILE__).'/../../../../../../conf/pg.kcp.php';
require dirname(__FILE__).'/pp_cli_hub_lib.php';
include dirname(__FILE__).'/../configure/site.php';
/* ============================================================================== */

/* ============================================================================== */
/* =   01. ��û ���� ����                                                       = */
/* = -------------------------------------------------------------------------- = */
if ($_POST['req_tx'] == 'pay' && isset($_GET['crno']) === false)
{
	$req_tx = $_POST['req_tx']; // ��û ����
	/* = -------------------------------------------------------------------------- = */
	$ordr_idxx = $_POST['ordr_idxx']; // �ֹ� ��ȣ
	$good_name = $_POST['good_name']; // ��ǰ ����
	$buyr_name = $_POST['buyr_name']; // �ֹ��� �̸�
	$buyr_mail = $_POST['buyr_mail']; // �ֹ��� E-Mail
	$buyr_tel1 = $_POST['buyr_tel1']; // �ֹ��� ��ȭ��ȣ
	$comment = ''; // ���
	/* = -------------------------------------------------------------------------- = */
	$corp_type = '0'; // ����� ����
	//$corp_tax_type = ''; // ����/�鼼 ����
	//$corp_tax_no = ''; // ���� ����� ��ȣ
	//$corp_nm = ''; // ��ȣ
	//$corp_owner_nm = ''; // ��ǥ�ڸ�
	//$corp_addr = ''; // ����� �ּ�
	//$corp_telno = ''; // ����� ��ǥ ����ó
	/* = -------------------------------------------------------------------------- = */
	$trad_time = date('YmdHis'); // ���ŷ� �ð�
	$tr_code = $_POST['tr_code']; // ����뵵
	$id_info = $_POST['id_info']; // �ź�Ȯ�� ID
	$amt_tot = $_POST['amt_tot']; // �ŷ��ݾ� �� ��
	$amt_sup = $_POST['amt_sup']; // ���ް���
	$amt_svc = '0'; // �����
	$amt_tax = $_POST['amt_tax']; // �ΰ���ġ��
	/* = -------------------------------------------------------------------------- = */
	$ordno = $ordr_idxx;

	$data = $db->fetch("select * from gd_order where ordno='{$ordno}'",1);

	// �߱޻���üũ(�����ý��۰��)
	if ($data['cashreceipt'] != '' && file_exists(dirname(__FILE__).'/../../../../../../lib/cashreceipt.class.php') === false) {
		msg('���ݿ����� �����û����!! \\n['.$ordno.'] �ֹ��� �̹� ����Ǿ����ϴ�.');
		exit;
	}

	### ���ݿ�������û���� �߰�
	@include dirname(__FILE__).'/../../../../../../lib/cashreceipt.class.php';
	if (class_exists('cashreceipt'))
	{
		// �߱޻���üũ
		list($crno) = $db->fetch("select crno from gd_cashreceipt where ordno='{$ordno}' and status='ACK' order by crno desc limit 1");
		if ($crno != '') {
			msg('���ݿ����� �����û����!! \\n['.$ordno.'] �ֹ��� �̹� ����Ǿ����ϴ�.');
			exit;
		}

		$indata = array();
		$indata['ordno'] = $ordno;
		$indata['goodsnm'] = $good_name;
		$indata['buyername'] = $buyr_name;
		$indata['buyeremail'] = $buyr_mail;
		$indata['buyerphone'] = $buyr_tel1;
		$indata['useopt'] = $tr_code;
		$indata['certno'] = $id_info;
		$indata['amount'] = $amt_tot;
		$indata['supply'] = $amt_sup;
		$indata['surtax'] = $amt_tax;
		$indata['regdt'] = $trad_time;

		$cashreceipt = new cashreceipt();
		$crno = $cashreceipt->putReceipt($indata);
	}
}
else if ($crdata['req_tx'] == 'pay')
{
	$req_tx = $crdata['req_tx']; // ��û ����
	/* = -------------------------------------------------------------------------- = */
	$ordr_idxx = $crdata['ordno']; // �ֹ� ��ȣ
	$good_name = $crdata['goodsnm']; // ��ǰ ����
	$buyr_name = $crdata['buyername']; // �ֹ��� �̸�
	$buyr_mail = $crdata['buyeremail']; // �ֹ��� E-Mail
	$buyr_tel1 = $crdata['buyerphone']; // �ֹ��� ��ȭ��ȣ
	$comment = ''; // ���
	/* = -------------------------------------------------------------------------- = */
	$corp_type = '0'; // ����� ����
	//$corp_tax_type = ''; // ����/�鼼 ����
	//$corp_tax_no = ''; // ���� ����� ��ȣ
	//$corp_nm = ''; // ��ȣ
	//$corp_owner_nm = ''; // ��ǥ�ڸ�
	//$corp_addr = ''; // ����� �ּ�
	//$corp_telno = ''; // ����� ��ǥ ����ó
	/* = -------------------------------------------------------------------------- = */
	$trad_time = date('YmdHis'); // ���ŷ� �ð�
	$tr_code = $crdata['useopt']; // ����뵵
	$id_info = $crdata['certno']; // �ź�Ȯ�� ID
	$amt_tot = $crdata['amount']; // �ŷ��ݾ� �� ��
	$amt_sup = $crdata['supply']; // ���ް���
	$amt_svc = '0'; // �����
	$amt_tax = $crdata['surtax']; // �ΰ���ġ��
	/* = -------------------------------------------------------------------------- = */
	$ordno = $ordr_idxx;
	$crno = $_GET['crno'];
}
else if ($crdata['req_tx'] == 'mod')
{
	$req_tx = $crdata['req_tx']; // ��û ����
	/* = -------------------------------------------------------------------------- = */
	$mod_type = 'STSC'; // ���� Ÿ��
	$mod_gubn = 'MG01'; // ���� ��û �ŷ���ȣ ����
	$mod_value = $crdata['tid']; // ���� ��û �ŷ���ȣ
	preg_match("/���ι�ȣ : {$crdata['receiptnumber']}.*\n���ŷ��ð� : ([^(\n)]*)\n/s", $crdata['receiptlog'], $log);
	$trad_time = $log[1]; // ���ŷ� �ð�
	//$mod_mny = ''; // ���� ��û �ݾ�
	//$rem_mny = ''; // ����ó�� ���� �ݾ�
	/* = -------------------------------------------------------------------------- = */
	$ordno = $crdata['ordno'];
}
$cust_ip = getenv( 'REMOTE_ADDR' ); // ��û IP
/* ============================================================================== */


/* ============================================================================== */
/* =   02. �ν��Ͻ� ���� �� �ʱ�ȭ                                              = */
/* = -------------------------------------------------------------------------- = */
$c_PayPlus  = new C_PAYPLUS_CLI;
$c_PayPlus->mf_clear();
/* ============================================================================== */


/* ============================================================================== */
/* =   03. ó�� ��û ���� ����, ����                                            = */
/* = -------------------------------------------------------------------------- = */

/* = -------------------------------------------------------------------------- = */
/* =   03-1. ���� ��û                                                          = */
/* = -------------------------------------------------------------------------- = */
// ��ü ȯ�� ����
if ( $req_tx == 'pay' )
{
	$tx_cd = '07010000'; // ���ݿ����� ��� ��û

	// ���ݿ����� ����
	$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'user_type',      $g_conf_user_type );
	$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'trad_time',      $trad_time        );
	$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'tr_code',        $tr_code          );
	$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'id_info',        $id_info          );
	$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'amt_tot',        $amt_tot          );
	$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'amt_sup',        $amt_sup          );
	$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'amt_svc',        $amt_svc          );
	$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'amt_tax',        $amt_tax          );
	$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'pay_type',       'PAXX'            ); // �� ���� ���� ����(PABK - ������ü, PAVC - �������, PAXX - ��Ÿ)
	//$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'pay_trade_no',   $pay_trade_no ); // ���� �ŷ���ȣ(PABK, PAVC�� ��� �ʼ�)
	//$rcpt_data_set .= $c_PayPlus->mf_set_data_us( 'pay_tx_id',      $pay_tx_id    ); // ������� �Ա��뺸 TX_ID(PAVC�� ��� �ʼ�)

	// �ֹ� ����
	$c_PayPlus->mf_set_ordr_data( 'ordr_idxx',  $ordr_idxx );
	$c_PayPlus->mf_set_ordr_data( 'good_name',  preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "",  $good_name) );
	$c_PayPlus->mf_set_ordr_data( 'buyr_name',  $buyr_name );
	$c_PayPlus->mf_set_ordr_data( 'buyr_tel1',  $buyr_tel1 );
	$c_PayPlus->mf_set_ordr_data( 'buyr_mail',  $buyr_mail );
	$c_PayPlus->mf_set_ordr_data( 'comment',    $comment   );

	// ������ ����
	$corp_data_set .= $c_PayPlus->mf_set_data_us( 'corp_type',       $corp_type     );

	if ( $corp_type == '1' ) // �������� ��� �ǸŻ��� DATA ���� ����
	{
		$corp_data_set .= $c_PayPlus->mf_set_data_us( 'corp_tax_type',   $corp_tax_type );
		$corp_data_set .= $c_PayPlus->mf_set_data_us( 'corp_tax_no',     $corp_tax_no   );
		$corp_data_set .= $c_PayPlus->mf_set_data_us( 'corp_sel_tax_no', $corp_tax_no   );
		$corp_data_set .= $c_PayPlus->mf_set_data_us( 'corp_nm',         $corp_nm       );
		$corp_data_set .= $c_PayPlus->mf_set_data_us( 'corp_owner_nm',   $corp_owner_nm );
		$corp_data_set .= $c_PayPlus->mf_set_data_us( 'corp_addr',       $corp_addr     );
		$corp_data_set .= $c_PayPlus->mf_set_data_us( 'corp_telno',      $corp_telno    );
	}

	$c_PayPlus->mf_set_ordr_data( 'rcpt_data', $rcpt_data_set );
	$c_PayPlus->mf_set_ordr_data( 'corp_data', $corp_data_set );
}

/* = -------------------------------------------------------------------------- = */
/* =   03-2. ��� ��û                                                          = */
/* = -------------------------------------------------------------------------- = */
else if ( $req_tx == 'mod' )
{
	if ( $mod_type == 'STSQ' )
	{
		$tx_cd = '07030000'; // ��ȸ ��û
	}
	else
	{
		$tx_cd = '07020000'; // ��� ��û
	}

	$c_PayPlus->mf_set_modx_data( 'mod_type',   $mod_type   );      // ���ŷ� ���� ��û ����
	$c_PayPlus->mf_set_modx_data( 'mod_value',  $mod_value  );
	$c_PayPlus->mf_set_modx_data( 'mod_gubn',   $mod_gubn   );
	$c_PayPlus->mf_set_modx_data( 'trad_time',  $trad_time  );

	if ( $mod_type == 'STPC' ) // �κ����
	{
		$c_PayPlus->mf_set_modx_data( 'mod_mny',  $mod_mny  );
		$c_PayPlus->mf_set_modx_data( 'rem_mny',  $rem_mny  );
	}
}
/* ============================================================================== */


/* ============================================================================== */
/* =   03-3. ����                                                               = */
/* ------------------------------------------------------------------------------ */
if ( strlen($tx_cd) > 0 )
{
	$c_PayPlus->mf_do_tx( '',                $g_conf_home_dir, $g_conf_site_id,
	                      '',                $tx_cd,           '',
	                      $g_conf_pa_url,    $g_conf_pa_port,  'payplus_cli_slib',
	                      $ordr_idxx,        $cust_ip,         $g_conf_log_level,
	                      '',                $g_conf_tx_mode );
}
else
{
	$c_PayPlus->m_res_cd  = '9562';
	$c_PayPlus->m_res_msg = '���� ����';
}
$res_cd  = $c_PayPlus->m_res_cd;                      // ��� �ڵ�
$res_msg = $c_PayPlus->m_res_msg;                     // ��� �޽���
/* ============================================================================== */


/* ============================================================================== */
/* =   04. ���� ��� ó��                                                       = */
/* = -------------------------------------------------------------------------- = */
if ( $req_tx == 'pay' )
{
	if ( $res_cd == '0000' )
	{
		$cash_no    = $c_PayPlus->mf_get_res_data( 'cash_no'    );       // ���ݿ����� �ŷ���ȣ
		$receipt_no = $c_PayPlus->mf_get_res_data( 'receipt_no' );       // ���ݿ����� ���ι�ȣ
		$app_time   = $c_PayPlus->mf_get_res_data( 'app_time'   );       // ���νð�(YYYYMMDDhhmmss)
		$reg_stat   = $c_PayPlus->mf_get_res_data( 'reg_stat'   );       // ��� ���� �ڵ�
		$reg_desc   = $c_PayPlus->mf_get_res_data( 'reg_desc'   );       // ��� ���� ����

		/* = -------------------------------------------------------------------------- = */
		/* =   04-1. ���� ����� ��ü ��ü������ DB ó�� �۾��Ͻô� �κ��Դϴ�.         = */
		/* = -------------------------------------------------------------------------- = */
		/* =         ���� ����� DB �۾� �ϴ� �������� ���������� ���ε� �ǿ� ����      = */
		/* =         DB �۾��� �����Ͽ� DB update �� �Ϸ���� ���� ���, �ڵ�����       = */
		/* =         ���� ��� ��û�� �ϴ� ���μ����� �����Ǿ� �ֽ��ϴ�.                = */
		/* =         DB �۾��� ���� �� ���, bSucc ��� ����(String)�� ���� 'false'     = */
		/* =         �� ������ �ֽñ� �ٶ��ϴ�. (DB �۾� ������ ��쿡�� 'false' �̿��� = */
		/* =         ���� �����Ͻø� �˴ϴ�.)                                           = */
		/* = -------------------------------------------------------------------------- = */
		$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= '���ݿ����� �߱� ����'."\n";
		$settlelog .= '����ڵ� : '.$res_cd."\n";
		$settlelog .= '������� : '.$res_msg."\n";
		$settlelog .= '���ι�ȣ : '.$receipt_no."\n";
		$settlelog .= '�ŷ���ȣ : '.$cash_no."\n";
		$settlelog .= '���νð� : '.$app_time."\n";
		$settlelog .= '��ϻ����ڵ� : '.$reg_stat."\n";
		$settlelog .= '��ϻ��¼��� : '.$reg_desc."\n";
		$settlelog .= '���ŷ��ð� : '.$trad_time."\n";
		$settlelog .= '-----------------------------------'."\n";
		echo nl2br($settlelog);

		if (empty($crno) === true)
		{
			$db->query("update gd_order set cashreceipt='{$cash_no}',settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
		}
		else {
			# ���ݿ�������û���� ����
			$db->query("update gd_cashreceipt set pg='kcp',cashreceipt='{$cash_no}',receiptnumber='{$receipt_no}',tid='{$cash_no}',moddt=now(),status='ACK',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
			$db->query("update gd_order set cashreceipt='{$cash_no}' where ordno='{$ordno}'");
		}

		$bSucc = ''; // DB �۾� ������ ��� 'false' �� ����

		/* = -------------------------------------------------------------------------- = */
		/* =   04-2. DB �۾� ������ ��� �ڵ� ���� ���                                 = */
		/* = -------------------------------------------------------------------------- = */
		if ( $bSucc == 'false' )
		{
			$c_PayPlus->mf_clear();

			$tx_cd = '07020000'; // ��� ��û

			$c_PayPlus->mf_set_modx_data( 'mod_type',  'STSC'     );                    // ���ŷ� ���� ��û ����
			$c_PayPlus->mf_set_modx_data( 'mod_value', $cash_no   );
			$c_PayPlus->mf_set_modx_data( 'mod_gubn',  'MG01'     );
			$c_PayPlus->mf_set_modx_data( 'trad_time', $trad_time );

			$c_PayPlus->mf_do_tx( '',                $g_conf_home_dir, $g_conf_site_id,
			                      '',                $tx_cd,           '',
			                      $g_conf_pa_url,    $g_conf_pa_port,  'payplus_cli_slib',
			                      $ordr_idxx,        $cust_ip,         $g_conf_log_level,
			                      '',                $g_conf_tx_mode );

			$res_cd  = $c_PayPlus->m_res_cd;
			$res_msg = $c_PayPlus->m_res_msg;
		}

		$c_PayPlus->mf_clear(); // �ν��Ͻ� CleanUp
		if (isset($_GET['crno']) === false)
		{
			msg('���ݿ������� ����߱޵Ǿ����ϴ�');
			echo '<script>parent.location.reload();</script>';
		}
		else {
			echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
		}
	}
	else {
		/* = -------------------------------------------------------------------------- = */
		/* =   04-3. ��� ���и� ��ü ��ü������ DB ó�� �۾��Ͻô� �κ��Դϴ�.         = */
		/* = -------------------------------------------------------------------------- = */
		$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= '���ݿ����� �߱� ����'."\n";
		$settlelog .= '����ڵ� : '.$res_cd."\n";
		$settlelog .= '������� : '.$res_msg."\n";
		$settlelog .= '-----------------------------------'."\n";
		echo nl2br($settlelog);

		if (empty($crno) === true)
		{
			$db->query("update gd_order set settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
		}
		else {
			# ���ݿ�������û���� ����
			$db->query("update gd_cashreceipt set pg='kcp',errmsg='{$res_cd}:{$res_msg}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
		}

		$c_PayPlus->mf_clear(); // �ν��Ͻ� CleanUp
		if (isset($_GET['crno']) === false)
		{
			msg($res_msg);
			exit;
		}
		else {
			echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
		}
	}
}
/* ============================================================================== */


/* ============================================================================== */
/* =   05. ���� ��� ó��                                                       = */
/* = -------------------------------------------------------------------------- = */
else if ( $req_tx == 'mod' )
{
	if ( $res_cd == '0000' )
	{
		$cash_no    = $c_PayPlus->mf_get_res_data( 'cash_no'    );       // ���ݿ����� �ŷ���ȣ
		$receipt_no = $c_PayPlus->mf_get_res_data( 'receipt_no' );       // ���ݿ����� ���ι�ȣ
		$app_time   = $c_PayPlus->mf_get_res_data( 'app_time'   );       // ���νð�(YYYYMMDDhhmmss)
		$reg_stat   = $c_PayPlus->mf_get_res_data( 'reg_stat'   );       // ��� ���� �ڵ�
		$reg_desc   = $c_PayPlus->mf_get_res_data( 'reg_desc'   );       // ��� ���� ����

		$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= '���ݿ����� ��� ����'."\n";
		$settlelog .= '����ڵ� : '.$res_cd."\n";
		$settlelog .= '������� : '.$res_msg."\n";
		$settlelog .= '���ι�ȣ : '.$receipt_no."\n";
		$settlelog .= '�ŷ���ȣ : '.$cash_no."\n";
		$settlelog .= '���νð� : '.$app_time."\n";
		$settlelog .= '��ϻ����ڵ� : '.$reg_stat."\n";
		$settlelog .= '��ϻ��¼��� : '.$reg_desc."\n";
		$settlelog .= '-----------------------------------'."\n";
		echo nl2br($settlelog);

		$db->query("update gd_cashreceipt set moddt=now(),status='CCR',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'{$settlelog}') where crno='{$_GET['crno']}'");
		$c_PayPlus->mf_clear(); // �ν��Ͻ� CleanUp
		echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
	}
	else {
		/* = -------------------------------------------------------------------------- = */
		/* =   05-1. ���� ���и� ��ü ��ü������ DB ó�� �۾��Ͻô� �κ��Դϴ�.         = */
		/* = -------------------------------------------------------------------------- = */
		$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= '���ݿ����� ��� ����'."\n";
		$settlelog .= '����ڵ� : '.$res_cd."\n";
		$settlelog .= '������� : '.$res_msg."\n";
		$settlelog .= '-----------------------------------'."\n";
		echo nl2br($settlelog);

		$db->query("update gd_cashreceipt set errmsg='{$res_cd}:{$res_msg}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$_GET['crno']}'");
		$c_PayPlus->mf_clear(); // �ν��Ͻ� CleanUp
		echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
	}
}
/* ============================================================================== */

?>