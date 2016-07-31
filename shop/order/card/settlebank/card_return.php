<?php
//�� �������� �������� ���ʽÿ�. ������ html�±׳� �ڹٽ�ũ��Ʈ�� ���� ��� ������ ������ �� �����ϴ�

//hash����Ÿ���� �´� �� Ȯ�� �ϴ� ��ƾ�� ��Ʋ��ũ���� ���� ����Ÿ�� �´��� Ȯ���ϴ� ���̹Ƿ� �� ����ϼž� �մϴ�
//�������� ���� ���ӿ��� �ұ��ϰ� ��Ƽ ������(card_return)�� ������ ��Ʈ�� ���� ������ ���� hash ���� ������ �߻��� ���� �ֽ��ϴ�.
//�׷��Ƿ� hash �����ǿ� ���ؼ��� ���� �߻��� ������ �ľ��Ͽ� ��� ���� �� ��ó�� �ּž� �մϴ�.
//�׸��� ���������� data�� ó���� ��쿡�� ��Ʋ��ũ���� ������ ���� ���� ���� ��������� �ߺ��ؼ� ���� �� �����Ƿ� ������ ó���� ����Ǿ�� �մϴ�. 
//(PTrno �� PAuthDt�� ����(8�ڸ�)�� ���� unique �ϴ� PTrno�� üũ ���ּ���) 

	// ȸ���� callback function page
	include "../../../lib/library.php";
	include "../../../conf/config.php";
	include "../../../conf/pg.settlebank.php";
	include "./callback.php";

	//��Ʋ��ũ noti server���� ���� value
	$P_STATUS;	  // �ŷ����� : 0021(����), 0031(����), 0051(�Աݴ����)
	$P_TR_NO;     // �ŷ���ȣ
	$P_AUTH_DT;   // ���νð�
	$P_AUTH_NO;   // ���ι�ȣ
	$P_TYPE;      // �ŷ����� (CARD, BANK)
	$P_MID;       // ȸ������̵�
	$P_OID;       // �ֹ���ȣ
	$P_FN_CD1;    // �������ڵ�1 (�����ڵ�, ī���ڵ�)
	$P_FN_CD2;    // �������ڵ�2 (�����ڵ�, ī���ڵ�)
	$P_FN_NM;     // ������� (�����, ī����)
	$P_UNAME;     // �ֹ��ڸ�
	$P_AMT;       // �ŷ��ݾ�
	$P_NOTI;      // �ֹ�����
	$P_RMESG1;    // �޽���1
	$P_RMESG2;    // �޽���2
	$P_HASH;      // NOTI HASH �ڵ尪
	
	$resp = false;

	$P_STATUS = get_param(PStateCd);
	$P_TR_NO = get_param(PTrno);
	$P_AUTH_DT = get_param(PAuthDt);
	$P_AUTH_NO = get_param(PAuthNo);
	$P_TYPE = get_param(PType);
	$P_MID = get_param(PMid);
	$P_OID = get_param(POid);
	$P_FN_CD1 = get_param(PFnCd1);
	$P_FN_CD2 = get_param(PFnCd2);
	$P_FN_NM = get_param(PFnNm);
	$P_UNAME = get_param(PUname);
	$P_AMT = get_param(PAmt);
	$P_NOTI = get_param(PNoti);
	$P_RMESG1 = get_param(PRmesg1);
	$P_RMESG2 = get_param(PRmesg2);
	$P_HASH = get_param(PHash);

	/* mid�� mid_test�� ��쿡 ����մϴ�
	   �ش� ȸ���� id�� �ϳ��� auth_key�� �߱޵˴ϴ�
	   �߱޹����� auth_key�� �����ϼž� �մϴ� */
	$PG_AUTH_KEY = $pg['key'];    

	$md5_hash = md5($P_STATUS.$P_TR_NO.$P_AUTH_DT.$P_TYPE.$P_MID.$P_OID.$P_AMT.$PG_AUTH_KEY); 

	$value = array("P_STATUS"  => $P_STATUS,
                   "P_TR_NO"   => $P_TR_NO,  
                   "P_AUTH_DT" => $P_AUTH_DT,      
                   "P_TYPE"    => $P_TYPE,     
                   "P_MID"     => $P_MID,  
                   "P_OID"     => $P_OID,  
                   "P_FN_CD1"  => $P_FN_CD1,
                   "P_FN_CD2"  => $P_FN_CD2,
                   "P_FN_NM"   => $P_FN_NM,  
                   "P_UNAME"   => $P_UNAME,  
                   "P_AMT"     => $P_AMT,  
                   "P_NOTI"    => $P_NOTI,  
                   "P_RMESG1"  => $P_RMESG1,  
                   "P_RMESG2"  => $P_RMESG2,
                   "P_AUTH_NO" => $P_AUTH_NO,
                   "P_HASH"    => $P_HASH,
                   "HashData"  => $md5_hash );

	//���� dbó���� callback.asp�� noti_success(),noti_failure(),noti_hash_err()���� ���� ��ƾ�� �߰��Ͻø� �˴ϴ�
	//�� �Լ� ȣ��� ���� �迭�� ���޵ǵ��� �Ǿ� �����Ƿ� ó���� �����Ͻñ� �ٶ��ϴ�.
	//���� �� �Լ����� ���� ������ ���� log����� �˴ϴ�. ȸ���缭������ ����� �����η� �°� �����Ͽ� �ּ���

	if ($md5_hash == $P_HASH) {
		
		if(forge_order_check($P_OID, $P_AMT) === false){
			$resp = false;
		}else if($P_TYPE != 'VBANK' && $P_STATUS == "0021"){
			$resp = noti_success($value);
		}else if($P_TYPE == 'VBANK' && $P_STATUS == "0021"){
			$resp = noti_vbanksuccess($value);
		}else if($P_TYPE == 'VBANK' && $P_STATUS == "0051"){
			$resp = noti_waiting_pay($value);
		}else if($P_STATUS == "0031"){
			$resp = noti_failure($value);
		}else{
			$resp = false;
		}

	}
	else {
			$resp = noti_hash_err($value);
	}
   
        //��Ʋ��ũ�� ���۵Ǿ�� �ϴ� ���̹Ƿ� �������� ������.
   	if($resp === true){
   		echo "OK";
   	}else if($resp === false){
   		echo "CANC";
   	}
?>
