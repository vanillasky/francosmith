<?php
/* -------------------------------------------------------------------------- */
/* ::: �������� ��Ƽ����                                                               */
/* -------------------------------------------------------------------------- */
$TEMP_IP	= getenv('REMOTE_ADDR');
$PG_IP		= substr($TEMP_IP, 0, 10);
//--- PG���� ���´��� IP�� üũ
	$result_msg = "";
	$r_res_cd         = $_POST[ "res_cd"         ];  // �����ڵ�
	$r_res_msg        = $_POST[ "res_msg"        ];  // ���� �޽���
	$r_cno            = $_POST[ "cno"            ];  // PG�ŷ���ȣ
	$r_memb_id        = $_POST[ "memb_id"        ];  // ������ ID
	$r_amount         = $_POST[ "amount"         ];  // �� �����ݾ�
	$r_order_no       = $_POST[ "order_no"       ];  // �ֹ���ȣ
	$r_noti_type      = $_POST[ "noti_type"      ];  // ��Ƽ���� ����(20), �Ա�(30), ����ũ�� ����(40)
	$r_auth_no        = $_POST[ "auth_no"        ];  // ���ι�ȣ
	$r_tran_date      = $_POST[ "tran_date"      ];  // �����Ͻ�
	$r_card_no        = $_POST[ "card_no"        ];  // ī���ȣ
	$r_issuer_cd      = $_POST[ "issuer_cd"      ];  // �߱޻��ڵ�
	$r_issuer_nm      = $_POST[ "issuer_nm"      ];  // �߱޻��
	$r_acquirer_cd    = $_POST[ "acquirer_cd"    ];  // ���Ի��ڵ�
	$r_acquirer_nm    = $_POST[ "acquirer_nm"    ];  // ���Ի��
	$r_install_period = $_POST[ "install_period" ];  // �Һΰ���
	$r_noint          = $_POST[ "noint"          ];  // �����ڿ���
	$r_bank_cd        = $_POST[ "bank_cd"        ];  // �����ڵ�
	$r_bank_nm        = $_POST[ "bank_nm"        ];  // �����
	$r_account_no     = $_POST[ "account_no"     ];  // ���¹�ȣ
	$r_deposit_nm     = $_POST[ "deposit_nm"     ];  // �Ա��ڸ�
	$r_expire_date    = $_POST[ "expire_date"    ];  // ���»�븸����
	$r_cash_res_cd    = $_POST[ "cash_res_cd"    ];  // ���ݿ����� ����ڵ�
	$r_cash_res_msg   = $_POST[ "cash_res_msg"   ];  // ���ݿ����� ����޽���
	$r_cash_auth_no   = $_POST[ "cash_auth_no"   ];  // ���ݿ����� ���ι�ȣ
	$r_cash_tran_date = $_POST[ "cash_tran_date" ];  // ���ݿ����� �����Ͻ�
	$r_cp_cd          = $_POST[ "cp_cd"          ];  // ����Ʈ��
	$r_used_pnt       = $_POST[ "used_pnt"       ];  // �������Ʈ
	$r_remain_pnt     = $_POST[ "remain_pnt"     ];  // �ܿ��ѵ�
	$r_pay_pnt        = $_POST[ "pay_pnt"        ];  // ����/�߻�����Ʈ
	$r_accrue_pnt     = $_POST[ "accrue_pnt"     ];  // ��������Ʈ
	$r_escrow_yn      = $_POST[ "escrow_yn"      ];  // ����ũ�� �������
	$r_canc_date      = $_POST[ "canc_date"      ];  // ����Ͻ�
	$r_canc_acq_date  = $_POST[ "canc_acq_date"  ];  // ��������Ͻ�
	$r_refund_date    = $_POST[ "refund_date"    ];  // ȯ�ҿ����Ͻ�
	$r_pay_type       = $_POST[ "pay_type"       ];  // ��������
	$r_auth_cno       = $_POST[ "auth_cno"       ];  // �����ŷ���ȣ

	/* -------------------------------------------------------------------------- */
	/* ::: ��Ƽ���� - ����ũ�� ���º���                                           */
	/* -------------------------------------------------------------------------- */
	$r_escrow_yn      = $_POST[ "escrow_yn "     ];  // ����ũ������
	$r_stat_cd        = $_POST[ "stat_cd "       ];  // ���濡��ũ�λ����ڵ�
	$r_stat_msg       = $_POST[ "stat_msg"       ];  // ���濡��ũ�λ��¸޼���
	/* r_stat_cd ���� �ڵ�ǥ
	ES01	����	ES02	�������	ES03	�Աݴ��
	ES04	�ԱݿϷ�	ES05	ȯ�ҿ�û	ES06	ȯ�ҿϷ�
	ES07	�����	ES08	������ ��ҿ�û	ES09	����� ��ҿϷ�
	ES10	����� ȯ�ҿ�û	ES11	����� ȯ�ҿϷ�	ES12	����Ȯ��
	ES13	���Ű���				
	*/

	//--- �������� ���
$easypayHome	= dirname($_SERVER['SCRIPT_FILENAME']);      // �̴����� Ȩ���͸�
$logfile		= fopen( $easypayHome . '/log/easypay_noti_log_'.date('Ymd').'.log', 'a+' );

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

$ip_arr = array('203.233.72.14','203.233.72.150','203.233.72.151','203.233.72.158');
if(!in_array($_SERVER[REMOTE_ADDR],$ip_arr))exit; //������ ����

		if ( $r_res_cd == "0000" )
		{
		/* ---------------------------------------------------------------------- */
		/* ::: ������ DB ó��                                                     */
		/* ---------------------------------------------------------------------- */
		/* DBó�� ���� �� : res_cd=0000, ���� �� : res_cd=5001                    */
		/* ---------------------------------------------------------------------- */

			include "../../../lib/library.php";
			include "../../../conf/config.php";
			$noti_name = "";
			$ordno = $r_order_no;
			switch($r_noti_type){
				case "20" :
					$noti_name = "���� ����";
					break;
				case "30" :
					$noti_name = "�Ա�";
					break;
				case "40" :
					$noti_name = "����ũ�� ����";
					break;
			}

			$settlelog = "\n----------------------------------------\n";
			$settlelog .= "ó�� �ð� : ".date('Y:m:d H:i:s')."\n";
			if($r_noti_type == '30')$settlelog .="�Ա�Ȯ�� : PG���ڵ��Ա�Ȯ��\n";
			$settlelog		.= "����ũ�� : ".$r_escrow_yn."\n";
			$settlelog		.= "��Ƽ���� : ".$r_noti_type."(".$noti_name.")\n";
			$settlelog		.= "�����ڵ� : ".$r_stat_cd."\n";
			$settlelog		.= "���¸޽��� : ".$r_stat_msg."\n";
			if($r_res_cd)$settlelog		.= "���� �ڵ� : ".$r_res_cd."\n";
			if($r_res_msg)$settlelog			.= "���� �޼��� : ".$r_res_msg."\n";
			if($r_noti_type)$settlelog		.= "���� ���� : ".$r_noti_type."\n";
			if($r_auth_no)$settlelog		.= "���ι�ȣ : ".$r_auth_no."\n";
			if($r_tran_date)$settlelog	.= "�����Ͻ� : ".$r_tran_date."\n";
			if($r_bank_nm || $r_bank_cd)$settlelog	.= "�������� : [".$r_bank_cd."] ".$r_bank_nm."\n";
			if($r_amount)$settlelog	.= "�Աݱݾ� : ".$r_amount."\n";
			if($r_deposit_nm)$settlelog	.= "�Ա��ڸ� : ".$r_deposit_nm."\n";
			if($r_cash_res_cd)$settlelog		.= "���ݿ����� ����ڵ� : ".$r_cash_res_cd."\n";
			if($r_cash_res_msg)$settlelog		.= "���ݿ����� ����޽��� : ".$r_cash_res_msg."\n";
			if($r_cash_auth_no)$settlelog		.= "���ݿ����� ���ι�ȣ : ".$r_cash_auth_no."\n";
			if($r_cash_tran_date)$settlelog		.= "���ݿ����� �����Ͻ� : ".$r_cash_tran_date."\n";
			if($r_auth_cno)$settlelog		.= "�����ŷ���ȣ : ".$r_auth_cno."\n";
			$settlelog	.= "----------------------------------------";

			### item check stock
			include "../../../lib/cardCancel.class.php";
			if($r_noti_type=="40" || $r_noti_type=="30") {		//����ũ�� ���� or �Ա�
					$cancel = new cardCancel();
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
							cardtno		= '$r_auth_no'
						where ordno='$ordno'"
						);
						$db->query("update ".GD_ORDER_ITEM." set cyn='y', istep='$step' where ordno='$ordno'");

						if($r_cash_auth_no) $db-> query("update ".GD_ORDER." set cashreceipt='$r_cash_auth_no' where ordno='$ordno'"); //���ݿ������� �߱޵Ǿ��� ��� ���ݿ����� ó��

						### �ֹ��α� ����
						orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

						### ��� ó��
						setStock($ordno);

						### �Ա�Ȯ�θ���
						sendMailCase($data[email],1,$data);

						### �Ա�Ȯ��SMS
						$dataSms = $data;
						sendSmsCase('incash',$data[mobileOrder]);

						### Ncash �ŷ� Ȯ�� API
						include "../../../lib/naverNcash.class.php";
						$naverNcash = new naverNcash();
						$naverNcash->deal_done($ordno);
					}	
			}
			else{
				$db->query("
				UPDATE ".GD_ORDER." SET
					settlelog		= concat(ifnull(settlelog,''),'$settlelog')
				WHERE ordno='$ordno'");
			}
					

			$result_msg = "res_cd=0000" . chr(31) . "res_msg=SUCCESS";
		}
		else
		{
			$result_msg = "res_cd=5001" . chr(31) . "res_msg=FAIL";
		}
	/* -------------------------------------------------------------------------- */
	/* ::: ��Ƽ ó����� ó��                                                     */
	/* -------------------------------------------------------------------------- */
	echo $result_msg;

?>