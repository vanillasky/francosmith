<?
    /* ============================================================================== */
    /* =   01. ���� �뺸 ������ ����(�ʵ�!!)                                        = */
    /* = -------------------------------------------------------------------------- = */
    /* =   ���� �뺸 ������������, ������� �Ա� �뺸 �����Ϳ� ����ϾȽɰ���       = */
    /* =   �뺸 ������ ���� KCP �� ���� ������ �뺸 ���� �� �ֽ��ϴ�. �̷��� �뺸   = */
    /* =   �����͸� �ޱ� ���� ���������� ����� ���۹޴� �������� ������ ���ƾ�     = */
    /* =   �մϴ�. ������ �������� ��ü�� �°� �����Ͻ� ��, KCP ������ ��������     = */
    /* =   ����� �ֽñ� �ٶ��ϴ�. ��� ����� ���� �Ŵ����� �����Ͻñ� �ٶ��ϴ�.   = */
    /* ============================================================================== */
	$ip_arr = array('203.238.36.58','203.238.36.160','203.238.36.161','203.238.36.173','203.238.36.178');
	if(!in_array($_SERVER[REMOTE_ADDR],$ip_arr))exit; //������ ����

    /* ============================================================================== */
    /* =   02. ���� �뺸 ������ �ޱ�                                                = */
    /* = -------------------------------------------------------------------------- = */
    $site_cd      = $_POST [ "site_cd"  ];                 // ����Ʈ �ڵ�
    $tno          = $_POST [ "tno"      ];                 // KCP �ŷ���ȣ
    $order_no     = $_POST [ "order_no" ];                 // �ֹ���ȣ
    $tx_cd        = $_POST [ "tx_cd"    ];                 // ����ó�� ���� �ڵ�
    $tx_tm        = $_POST [ "tx_tm"    ];                 // ����ó�� �Ϸ� �ð�
    /* = -------------------------------------------------------------------------- = */
    $ipgm_name    = "";                                    // �ֹ��ڸ�
    $remitter     = "";                                    // �Ա��ڸ�
    $ipgm_mnyx    = "";                                    // �Ա� �ݾ�
    $bank_code    = "";                                    // �����ڵ�
    $account      = "";                                    // ������� �Աݰ��¹�ȣ
    $op_cd        = "";                                    // ó������ �ڵ�
    $noti_id      = "";                                    // �뺸 ���̵�
    /* = -------------------------------------------------------------------------- = */

    /* = -------------------------------------------------------------------------- = */
    /* =   02-1. ������� �Ա� �뺸 ������ �ޱ�                                     = */
    /* = -------------------------------------------------------------------------- = */
    if ( $tx_cd == "TX00" )
    {
        $ipgm_name = $_POST[ "ipgm_name" ];                // �ֹ��ڸ�
        $remitter  = $_POST[ "remitter"  ];                // �Ա��ڸ�
        $ipgm_mnyx = $_POST[ "ipgm_mnyx" ];                // �Ա� �ݾ�
        $bank_code = $_POST[ "bank_code" ];                // �����ڵ�
        $account   = $_POST[ "account"   ];                // ������� �Աݰ��¹�ȣ
        $op_cd     = $_POST[ "op_cd"     ];                // ó������ �ڵ�
        $noti_id   = $_POST[ "noti_id"   ];                // �뺸 ���̵�
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   02-2. ����ϾȽɰ��� �뺸 ������ �ޱ�                                    = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX08" )
    {
        $ipgm_mnyx = $_POST[ "ipgm_mnyx" ];                // �Ա� �ݾ�
        $bank_code = $_POST[ "bank_code" ];                // �����ڵ�
    }
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   03. ���� �뺸 ����� ��ü ��ü������ DB ó�� �۾��Ͻô� �κ��Դϴ�.      = */
    /* = -------------------------------------------------------------------------- = */
    /* =   �뺸 ����� DB �۾� �ϴ� �������� ���������� �뺸�� �ǿ� ���� DB �۾���  = */
    /* =   �����Ͽ� DB update �� �Ϸ���� ���� ���, ����� ���뺸 ���� �� �ִ�     = */
    /* =   ���μ����� �����Ǿ� �ֽ��ϴ�. �ҽ����� result ��� Form ���� ���� �Ͻ�   = */
    /* =   ��, DB �۾��� ���� �� ���, result �� ���� "0000" �� ������ �ֽð�,      = */
    /* =   DB �۾��� ���� �� ���, result �� ���� "0000" �̿��� ������ ������ �ֽ�  = */
    /* =   �� �ٶ��ϴ�. result ���� "0000" �� �ƴ� ��쿡�� ���뺸�� �ް� �˴ϴ�.   = */
    /* = -------------------------------------------------------------------------- = */

    /* = -------------------------------------------------------------------------- = */
    /* =   03-1. ������� �Ա� �뺸 ������ DB ó�� �۾� �κ�                        = */
    /* = -------------------------------------------------------------------------- = */
    if ( $tx_cd == "TX00" )
    {
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   03-2. ����ϾȽɰ��� �뺸 ������ DB ó�� �۾� �κ�                       = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX08" )
    {
    }
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   04. result �� ���� �ϱ�                                                  = */
    /* ============================================================================== */
	include "../../../lib/library.php";
	include "../../../conf/config.php";

	$ordno = $order_no;
	if (!$ordno) exit;

	$settlelog = "
	----------------------------------------";
	if($site_cd)$settlelog		.= " 	����Ʈ �ڵ� : ".$site_cd;
	if($tno)$settlelog			.= " 	KCP �ŷ���ȣ : ".$tno;
	if($tx_cd)$settlelog		.= " 	����ó�� ���� �ڵ� : ".$tx_cd;
	if($tx_tm)$settlelog		.= " 	����ó�� �Ϸ� �ð� : ".$tx_tm;
	if($ipgm_name)$settlelog	.= " 	�ֹ��ڸ� : ".$ipgm_name;
	if($ipgm_mnyx)$settlelog	.= " 	�Ա� �ݾ� : ".$ipgm_mnyx;
	if($bank_code)$settlelog	.= " 	�����ڵ� : ".$bank_code;
	if($account)$settlelog		.= " 	������� �Աݰ��¹�ȣ : ".$account;
	if($op_cd)$settlelog		.= " 	ó������ �ڵ� : ".$op_cd;
	if($noti_id)$settlelog		.= " 	�뺸 ���̵� : ".$noti_id;
	if($bank_code)$settlelog	.= " 	�����ڵ� : ".$bank_code;
	$settlelog	.= "
	----------------------------------------
	";

	### item check stock
	include "../../../lib/cardCancel.class.php";
	include "../../../lib/cardCancel_social.class.php";
	$cancel = new cardCancel_social();
	$step = 1;
	if(!$cancel->chk_item_stock($ordno) && $cfg['stepStock'] == '1')$step = 51;

	$query = "
	select * from
		".GD_ORDER." a
		left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
	where
		a.ordno='$ordno'
	";
	$data = $db->fetch($query);
	if($step==51)$cancel->cancel_db_proc($ordno);
	else{
		### �ǵ���Ÿ ����
		$db->query("
		update ".GD_ORDER." set cyn='y', cdt=now(),
			step		= '$step',
			step2		= '',
			settlelog	= concat(settlelog,'$settlelog'),
			cardtno		= '$tno'
		where ordno='$ordno'"
		);
		$db->query("update ".GD_ORDER_ITEM." set cyn='y', istep='$step' where ordno='$ordno'");

		### �ֹ��α� ����
		orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

		### ��� ó��
		setStock($ordno);
/*
		### �Ա�Ȯ�θ���
		sendMailCase($data[email],1,$data);

		### �Ա�Ȯ��SMS
		$dataSms = $data;
		sendSmsCase('incash',$data[mobileOrder]);
*/
		// ��� �߱� ���� ���� �� ���� ���� (todayshop_noti Ŭ������ todayshop �� ��ӹ޾ұ� ������ ����� ����ص� ��)
		$todayshop_noti = &load_class('todayshop_noti', 'todayshop_noti');
		$orderinfo = $todayshop_noti->getorderinfo($ordno);
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
	}

?>
<html><body><form><input type="hidden" name="result" value="0000"></form></body></html>