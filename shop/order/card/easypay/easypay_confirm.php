<?php
include "../../../lib/library.php";
include "../../../conf/config.php";

/* -------------------------------------------------------------------------- */
/* ::: ��Ƽ����                                                               */
/* -------------------------------------------------------------------------- */
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
/*ES01	����	ES02	�������	ES03	�Աݴ��
ES04	�ԱݿϷ�	ES05	ȯ�ҿ�û	ES06	ȯ�ҿϷ�
ES07	�����	ES08	������ ��ҿ�û	ES09	����� ��ҿϷ�
ES10	����� ȯ�ҿ�û	ES11	����� ȯ�ҿϷ�	ES12	����Ȯ��
ES13	���Ű���				
*/
 
if ( $r_res_cd == "0000" )    
{
	
	if($r_noti_type=='30') {		//�Ա�
			//gd_order update step=1 
	
			//--- �α� ����
			$settlelog	= '===================================================='.chr(10);
			$settlelog	.= '������� �Ա� �ڵ� Ȯ�� : ���� ('.date('Y-m-d H:i:s').')'.chr(10);
			$settlelog	.= '===================================================='.chr(10);
			$settlelog	.= '�ֹ���ȣ : '.$r_order_no.chr(10);
			$settlelog	.= '�ŷ���ȣ : '.$r_cno.chr(10); 
			$settlelog	.= '�Աݱݾ� : '.number_format($r_amount).chr(10);
			
				// ���ݿ����� ��� ����
			if (empty($r_cash_auth_no) === false ) {
				$settlelog	.= '������� : ������� �ڵ��Ա� Ȯ�ο� ���� ó��'.chr(10);
				$settlelog	.= '���ݿ����� �߱޹�ȣ : '.$r_cash_auth_no.chr(10);
				$settlelog	.= '���ݿ����� �����Ͻ� : '.$cash_tran_date.chr(10);

				$qrc1	= "cashreceipt='".$r_cash_auth_no."',";
			}
			$settlelog	.= '===================================================='.chr(10);

			// �ֹ� ��ȣ
			$ordno	= $r_order_no;

			// �ֹ� ����
			$query = "
			SELECT * FROM
				".GD_ORDER." a
				LEFT JOIN ".GD_LIST_BANK." b on a.bankAccount = b.sno
			WHERE
				a.ordno='$ordno'
			";
			$data = $db->fetch($query);
			$sql="UPDATE ".GD_ORDER." SET ".$qrc1." cyn='y', cdt=now(),
				step		= '1',
				step2		= '',
				cardtno		= '$r_cno',
				settlelog	= concat(settlelog,'".$settlelog."')
			WHERE ordno='$ordno'";

			### �ǵ���Ÿ ����
			$db->query("
			UPDATE ".GD_ORDER." SET ".$qrc1." cyn='y', cdt=now(),
				step		= '1',
				step2		= '',
				cardtno		= '$r_cno',
				settlelog	= concat(settlelog,'".$settlelog."')
			WHERE ordno='$ordno'"
			);
			echo $sql;
			exit;
			$db->query("update ".GD_ORDER_ITEM." set cyn='y', istep='1' where ordno='$ordno'");

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




	

/* ---------------------------------------------------------------------- */
/* ::: ������ DB ó��                                                     */
/* ---------------------------------------------------------------------- */
/* DBó�� ���� �� : res_cd=0000, ���� �� : res_cd=5001                    */
/* ---------------------------------------------------------------------- */
	$result_msg = "res_cd=0000" . chr(31). "res_msg=SUCCESS";
	 	
}	
else
{	
	$result_msg = "res_cd=5001". chr(31) . "res_msg=FAIL";
}

/* -------------------------------------------------------------------------- */
/* ::: ��Ƽ ó����� ó��                                                     */
/* -------------------------------------------------------------------------- */
echo $result_msg;

?>