<?php
    // ȸ���� ������ �°� ��������
    // input parameter 
    // $P_STATUS;      // �ŷ����� : 0021(����), 0031(����), 0051(�Աݴ����)
    // $P_TR_NO;       // �ŷ���ȣ
    // $P_AUTH_DT;     // ���νð� 
    // $P_AUTH_NO;     // ���ι�ȣ
    // $P_TYPE;        // �ŷ����� (CARD, BANK)
    // $P_MID;         // ȸ������̵�
    // $P_OID;         // �ֹ���ȣ
    // $P_FN_CD1;      // �������ڵ� (�����ڵ�, ī���ڵ�)
    // $P_FN_CD2;      // �������ڵ� (�����ڵ�, ī���ڵ�)
    // $P_FN_NM;       // ������� (�����, ī����)
    // $P_UNAME;       // �ֹ��ڸ�
    // $P_AMT;         // �ŷ��ݾ�
    // $P_NOTI;        // �ֹ�����
    // $P_RMSG1;       // �޽���1
    // $P_RMSG2;       // �޽���2

    //	return value
	//  true  : ����
	//  false : ����
	//���� ���� (������� ����)
	function noti_success($noti){
		
		$db = $GLOBALS['db'];
		$member = $GLOBALS['member'];
		$ordno = $noti['P_OID'];
		$resp = true;
		
		//���ݿ����� ��ȸ
		$Cashreceipt = cash_receipt($noti);
		$noti['CASHRECEIPT'] = $Cashreceipt;

		// Ncash ���� ���� API ���̹� ���ϸ���
		include "../../../lib/naverNcash.class.php";
		$naverNcash = new naverNcash(true);
		if($naverNcash->useyn=='Y')
		{
			$ncashResult = $naverNcash->payment_approval($ordno, true);

			if($ncashResult===false)
			{
				$noti['LOGSTATUS'] = "���̹� ���ϸ��� ��뿡 �����Ͽ����ϴ�.";
				$settlelog = basics_log($noti,'autoCancel');
				### �α� ����
				proc_fail_db($noti, $settlelog);
				return false;
			}
		}

		### ���ں������� �߱�
		@session_start();
		if (session_is_registered('eggData') === true && $noti['P_STATUS'] == "0021" ){
			if ($_SESSION[eggData][ordno] == $noti['P_OID'] && $_SESSION[eggData][resno1] != '' && $_SESSION[eggData][resno2] != '' && $_SESSION[eggData][agree] == 'Y'){
				include '../../../lib/egg.class.usafe.php';
				$eggData = $_SESSION[eggData];
				switch ($noti['P_TYPE']){
					case "CARD":
						$eggData[payInfo1] = $noti['P_FN_NM']; # (*) ��������(ī���)
						$eggData[payInfo2] = $noti['P_AUTH_NO']; # (*) ��������(���ι�ȣ)
						break;
					case "BANK":
						$eggData[payInfo1] = $noti['P_FN_NM']; # (*) ��������(�����)
						$eggData[payInfo2] = $noti['P_TR_NO']; # (*) ��������(���ι�ȣ or �ŷ���ȣ)
						break;
				}
				//$eggCls = new Egg( 'create', $eggData );
				//if ( $eggCls->isErr == true && $noti['P_TYPE'] == "HPP_" ){
					//$noti['P_STATUS'] = '';
				//}
				//else if ( $eggCls->isErr == true && in_array($xpay->Response("LGD_PAYTYPE",0), array("SC0010","SC0030")) );
			}
			session_unregister('eggData');
		}
		
		### item check stock
		$res_cstock = true;
		include "../../../lib/cardCancel.class.php";
		$cancel = new cardCancel();
		if(!$cancel->chk_item_stock($ordno) && $res_cstock){
			$step = 51;
		}
		
		// DB ó��
		$oData = $db->fetch("select cardtno, cyn, step, vAccount from ".GD_ORDER." where ordno='".$ordno."'");
		if($oData['cyn'] == 'y' && $oData['step'] > 0) { // �ߺ�����
				//���û���� Ȯ���Ѵ�.
				if (trim($noti['P_TR_NO']) == trim($oData['cardtno'])) {
					$noti['LOGSTATUS'] = "������";
					$settlelog = basics_log($noti);
					$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'".$settlelog."') where ordno='".$ordno."' ");
					$resp = true;
				} else { 
					$noti['LOGSTATUS'] = "�ߺ�����";
					$settlelog = basics_log($noti,'autoCancel');
					### �������
					proc_fail_db($noti, $settlelog);
					$resp = false;
				}

		// ��������
		} else if( $noti['P_STATUS'] == "0021" && $step != 51 ) {
			
			$query = "
			select * from
				".GD_ORDER." a
				left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
			where
				a.ordno='$ordno'
			";
			$data = $db->fetch($query);

			### ���� ���� ����
			$step = 1;
			
			$qrc1 = "cyn='y', cdt=now(), pgAppNo='".$noti['P_AUTH_NO']."', pgAppDt='".$noti['P_AUTH_DT']."', cardtno='".$noti['P_TR_NO']."',";
			$qrc2 = "cyn='y',";

			### ���ݿ����� ����
			if ($Cashreceipt[10]){ //���ݿ�������ȣ
				$qrc1 .= "cashreceipt='".$Cashreceipt[10]."',";
			}

			### �ǵ���Ÿ ����
			$db->query("update ".GD_ORDER." set ".$qrc1." step = '".$step."', step2 = '', escrowyn = '', escrowno = '' where ordno='".$ordno."'");
			$db->query("update ".GD_ORDER_ITEM." set ".$qrc2." istep='".$step."' where ordno='".$ordno."'");

			### �ֹ��α� ����
			if($data[step2] == "") $data[step2]= "0"; 
			if($r_step[$step] == "") $r_step[$step]= "0"; 
			orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

			### ��� ó��
			setStock($ordno);

			### ��ǰ���Խ� ������ ���
			if ($data[m_no] && $data[emoney]){
				setEmoney($data[m_no],-$data[emoney],"��ǰ���Խ� ������ ���� ���",$ordno);
			}

			// �α� ���� 
			$noti['LOGSTATUS'] = "���� ����";
			$settlelog = basics_log($noti);
			$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");

			$resp = true;

		} else {
			
			//���� �α� ���� 
			$noti['LOGSTATUS'] = "���� ����";
			$settlelog = basics_log($noti);

			if ($step == '51') {
				$cancel->cancel_db_proc($ordno);
			} else {
				$db->query("update ".GD_ORDER." set step2='54', settlelog=concat(ifnull(settlelog,''),'".$settlelog."') where ordno='".$ordno."'");
				$db->query("update ".GD_ORDER_ITEM." set istep='54' where ordno='".$ordno."'");
			}
			
			// Ncash ���� ���� ��� API ȣ��
			if($naverNcash->useyn=='Y') $naverNcash->payment_approval_cancel($ordno);

			$resp = false;
		}

	    noti_write("../../../log/settlebank/noti_success.".date('Ymd').".log", $noti);
	    return $resp;
	}

	//�Աݴ����
	function noti_waiting_pay($noti) {

		$db = $GLOBALS['db'];
		$member = $GLOBALS['member'];
		$ordno = $noti['P_OID'];
		$resp = true;
		
		//���ݿ����� ��ȸ
		$Cashreceipt = cash_receipt($noti);
		$noti['CASHRECEIPT'] = $Cashreceipt;

		//��������ϰ�� ������¿� �Աݱ����� ���� �Ѿ�� ��)P_VACCT_NO=1234567|P_EXP_DT=20101025
		$exVal = P_rmesg1_explode($noti['P_RMESG1']);
		$pvacctno = $exVal[0];
		$pexpdt = $exVal[1];

		// Ncash ���� ���� API ���̹� ���ϸ���
		include "../../../lib/naverNcash.class.php";
		$naverNcash = new naverNcash(true);
		if($naverNcash->useyn=='Y')
		{
			if(trim($noti['P_TYPE'])=='VBANK') $ncashResult = $naverNcash->payment_approval($ordno, false);
			else $ncashResult = $naverNcash->payment_approval($ordno, true);
			if($ncashResult===false)
			{
				$noti['LOGSTATUS'] = "���̹� ���ϸ��� ��뿡 �����Ͽ����ϴ�.";
				$settlelog = basics_log($noti);
				return true;
			}
		}

		### ������� ������ ��� üũ �ܰ� ����
		$res_cstock = true;
		if($cfg['stepStock'] == '1') $res_cstock = false;

		### item check stock
		include "../../../lib/cardCancel.class.php";
		$cancel = new cardCancel();
		if(!$cancel->chk_item_stock($ordno) && $res_cstock){
			$step = 51;
		}
		
		// DB ó��
		$oData = $db->fetch("select cyn, step, vAccount from ".GD_ORDER." where ordno='$ordno'");
		if($oData['cyn'] == 'y' && $oData['step'] > 0) { // �ߺ�����
					//�⺻ �α� 
			$noti['LOGSTATUS'] = "������� �ߺ� ����";
			$settlelog = basics_log($noti);
			### �α� ����
			$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
			$resp = false;
		
		//���°� �Աݴ���̰� ������ 51�� �ƴ϶�� 
		} else if( $noti['P_STATUS'] == "0051" && $step != 51 ) {	// �Աݴ��
			
			$query = "
			select * from
				".GD_ORDER." a
				left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
			where
				a.ordno='$ordno'
			";
			$data = $db->fetch($query);

			### ���� ���� ����
			$qrc1 = "cyn='y', cdt=now(),";
			$qrc2 = "cyn='y',";

			### ������� ������ �������� ����
			$vAccount = $noti['P_FN_NM']." ".$pvacctno." ".$noti['P_UNAME'];
			error_log("$vAccount :".$vAccount."\n", 3, "/www/s4qa/shop/log/sms_log.txt");
			$step = 0; $qrc1 = $qrc2 = "";

			### ���ݿ����� ����
			if ($Cashreceipt[10]){ //���ݿ�������ȣ
				$qrc1 .= "cashreceipt='".$Cashreceipt[10]."',";
			}

			### �ǵ���Ÿ ����
//				escrowyn	= '$escrowyn',
//				escrowno	= '$escrowno',
			$db->query("update ".GD_ORDER." set ".$qrc1." step = '".$step."' , step2 = '', vAccount = '".$vAccount."' where ordno='".$ordno."'");
			$db->query("update ".GD_ORDER_ITEM." set ".$qrc2." istep='".$step."' where ordno='".$ordno."'");

			### �ֹ��α� ����
			orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

			### ��� ó��
			setStock($ordno);

			### ��ǰ���Խ� ������ ���
			if ($data[m_no] && $data[emoney]){
				setEmoney($data[m_no],-$data[emoney],"��ǰ���Խ� ������ ���� ���",$ordno);
			}

			$noti['LOGSTATUS'] = "�Ա� ���";
			
			$settlelog = basics_log($noti);
			$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");

			$resp = true;

		} else {
			
			//���� �α� ���� 
			$noti['LOGSTATUS'] = "������� ���� ����";
			$settlelog = basics_log($noti);

			if ($step == '51') {
				$cancel->cancel_db_proc($ordno);
			} else {
				$db->query("update ".GD_ORDER." set step2='54', settlelog=concat(ifnull(settlelog,''),'".$settlelog."') where ordno='".$ordno."'");
				$db->query("update ".GD_ORDER_ITEM." set istep='54' where ordno='".$ordno."'");
			}

			// Ncash ���� ���� ��� API ȣ��
			if($naverNcash->useyn=='Y') $naverNcash->payment_approval_cancel($ordno);
		}

	    noti_write("../../../log/settlebank/noti_success.".date('Ymd').".log", $noti);
	    return $resp;
    }
	

	//������� �Ա�Ȯ��
	function noti_vbanksuccess($noti)
	{
		$db = $GLOBALS['db'];
		$member = $GLOBALS['member'];
		$ordno = $noti['P_OID'];
		$resp = true;
		
		//��������ϰ�� ������¹�ȣ�� �Աݱ����� ���� �Ѿ�� ��)P_VACCT_NO=1234567|P_EXP_DT=20101025
		$exVal = P_rmesg1_explode($noti['P_RMESG1']);
		$pvacctno = $exVal[0];
		$pexpdt = $exVal[1];

		### ���ں������� �߱�
		@session_start();
		if (session_is_registered('eggData') === true && $noti['P_STATUS'] == "0021" ){
			if ($_SESSION[eggData][ordno] == $noti['P_OID'] && $_SESSION[eggData][resno1] != '' && $_SESSION[eggData][resno2] != '' && $_SESSION[eggData][agree] == 'Y'){
				include '../../../lib/egg.class.usafe.php';
				$eggData = $_SESSION[eggData];

				$eggData[payInfo1] = $noti['P_FN_NM']; # (*) ��������(�����)
				$eggData[payInfo2] = $pvacctno; # (*) ��������(���¹�ȣ)
			}
			session_unregister('eggData');
		}

		$query = "
		select * from
			".GD_ORDER." a
			left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
		where
			a.ordno='$ordno'
		";
		$data = $db->fetch($query);

		### ���� ���� ����
		$step = 1;
		$qrc1 = "pgAppNo='".$noti['P_AUTH_NO']."', pgAppDt='".$noti['P_AUTH_DT']."', cardtno='".$noti['P_TR_NO']."',";
		$qrc2 = "cyn='y',";

		### �ǵ���Ÿ ����
		$db->query("update ".GD_ORDER." set ".$qrc1." step = '".$step."' , step2 = '', escrowyn = '', escrowno = '' where ordno='".$ordno."'");
		$db->query("update ".GD_ORDER_ITEM." set ".$qrc2." istep='".$step."' where ordno='".$ordno."'");
		
		### �ֹ��α� ����
		if($data[step2] == "") $data[step2]= "0"; 
		if($r_step[$step] == "") $r_step[$step]= "0"; 
		orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);


		### ��� ó��
		setStock($ordno);
		
		$noti['LOGSTATUS'] = "������� �Ա� �Ϸ�";
		
		$settlelog = basics_log($noti);
		$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");

		$resp = true;

	    noti_write("../../../log/settlebank/noti_success.".date('Ymd').".log", $noti);
	    return $resp;
	}

	//���� ����
	function noti_failure($noti){

		//�⺻ �α� 
		$noti['LOGSTATUS'] = "���� ����(PG��)";
		$settlelog = basics_log($noti);

		proc_fail_db($noti, $settlelog);
	    noti_write("../../../log/settlebank/noti_failure.".date('Ymd').".log", $noti);
	    return false;
	}

	//hash ������
	function noti_hash_err($noti) {

		$noti['LOGSTATUS'] = "�����Ͱ� ���������� �ʽ��ϴ�.";
		$settlelog = basics_log($noti);

		proc_fail_db($noti, $settlelog);
	    noti_write("../../../log/settlebank/noti_hash_err.".date('Ymd').".log", $noti);
		return false;
    }

	function noti_write($file, $noti) {
		$fp = fopen($file, "a+");
		ob_start();
		print_r($noti);
		$msg = ob_get_contents();
		ob_end_clean();
		fwrite($fp, $msg);
		fclose($fp);
	}
      
    function get_param($name){
		global $HTTP_POST_VARS, $HTTP_GET_VARS;
		if (!isset($HTTP_POST_VARS[$name]) || $HTTP_POST_VARS[$name] == "") {
			if (!isset($HTTP_GET_VARS[$name]) || $HTTP_GET_VARS[$name] == "") {
				return false;
			}
			else {
			 return $HTTP_GET_VARS[$name];
			}
		}
		return $HTTP_POST_VARS[$name];
	}

	//�α׳����� �����Ѵ�.
	function basics_log($logVal, $mode = null){
		$failReason = '';
		if($mode == 'autoCancel') {
			$failReason = '->�ڵ� �������(15���̳� �� �ڵ����� ���ó���� �Ϸ�˴ϴ�.)';
		}

		$tmp_log[] = "��Ʋ��ũ ������û�� ���� ���";
		$tmp_log[] = "������� : ".$logVal['LOGSTATUS'].$failReason;
		$tmp_log[] = "������� : ".$logVal['P_TYPE'];		// (ī��(CARD), ������ü(BANK), �������(VBANK), �ڵ���(HPP_))";
		$tmp_log[] = "����ڵ� : ".$logVal['P_STATUS']."(".P_status($logVal['P_STATUS']).")";		// (0021(����), 0031(����), 0051(�Աݴ����))";
		$tmp_log[] = "�����ݾ� : ".$logVal['P_AMT'];
		$tmp_log[] = "�������̵� : ".$logVal['P_MID'];
		$tmp_log[] = "�ֹ���ȣ : ".$logVal['P_OID'];
		$tmp_log[] = "�����Ͻ� : ".$logVal['P_AUTH_DT'];
		$tmp_log[] = "�ŷ���ȣ : ".$logVal['P_TR_NO'];
		$tmp_log[] = "��������� : ".$logVal['P_FN_NM'];
		$tmp_log[] = "��������ڵ� : ".$logVal['P_FN_CD1'];
		$tmp_log[] = "��������ڵ念�� : ".$logVal['P_FN_CD2'];

		if($logVal['P_TYPE'] == 'CARD') {
			$tmp_log[] = "���������� : ".$logVal['P_UNAME'];
			$tmp_log[] = "����������ι�ȣ : ".$logVal['P_AUTH_NO'];

		} else if ($logVal['P_TYPE'] == 'BANK'){
			$tmp_log[] = "���ݿ��������ι�ȣ : ".$logVal['CASHRECEIPT'][10];
			$tmp_log[] = "���ݿ��������� : ".$logVal['CASHRECEIPT'][12];
			$tmp_log[] = "���¼������̸� : ".$logVal['P_UNAME'];
		} else if ($logVal['P_TYPE'] == 'VBANK'){
			$tmp_log[] = "���ݿ��������ι�ȣ : ".$logVal['CASHRECEIPT'][10];
			$tmp_log[] = "���ݿ��������� : ".$logVal['CASHRECEIPT'][12];

			$exVal = P_rmesg1_explode($logVal['P_RMESG1']);
			$tmp_log[] = "������¹߱޹�ȣ : ".$exVal[0];
			$tmp_log[] = "������� �Ա��ѵ���¥ : ".$exVal[1];
			$tmp_log[] = "��������Ա��ڸ� : ".$logVal['P_UNAME'];
			//$tmp_log[] = "�Աݴ����ݾ� : ".$xpay->Response("LGD_CASTAMOUNT",0);
			//$tmp_log[] = "�ŷ����� : ".$xpay->Response("LGD_CASFLAG",0)." (R:�Ҵ�,I:�Ա�,C:���)";
			
		} else if ($logVal['P_TYPE'] == 'HPP_'){
		
		}

		$tmp_log[] = "P_RMESG ���� : ".$logVal['P_RMESG1'];
		$tmp_log[] = "��Ʋ���� HASH : ".$logVal['P_HASH'];
		$tmp_log[] = "������ HASH: ".$logVal['HashData'];
		$tmp_log[] = "����ڿ�û���� : ".$logVal['P_NOTI'];

		$settlelog = "{$logVal['P_OID']} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . implode( "\n", $tmp_log ) . "\n-----------------------------------\n";

		return $settlelog;
	}

	//���ݿ����� �߱� ��ȸ
	function cash_receipt($logVal){
		$dt = substr($logVal['P_AUTH_DT'],0,8);
		$url="http://www.settlebank.co.kr/pgtrans/CashReceiptMultiAction.do?_method=getReceipt&mid=".$logVal['P_MID']."&trDt1=".$dt."&trDt2=".$dt."&trNo=".$logVal['P_TR_NO'];
				
		$ret = settle_Url_Reader($url);

		if( $ret != false ){
			$ret = preg_replace('/\<\!--.*--\>/','',$ret);
			$ret = str_replace('</br>','',$ret);
			$ret = str_replace('&nbsp;',' ',$ret);
			$receipt = explode('|',trim(iconv('UTF-8','EUC-KR',$ret)));
			if(!$receipt[10]) $receipt = false;
			
		}else{
			$receipt = false;
		}
		
		return $receipt;
	}
	
	//URl ��ȸ���� �����մϴ�.
	function settle_Url_Reader($url,$post_data='')
	{
		$ret = "true";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER,  0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);

		
		if($post_data) {
			curl_setopt($ch, CURLOPT_POST,1); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
		}
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		//���� ��ȸ
		$ret = curl_exec($ch);
		
		//���� ó��
		if( curl_error($ch) || $ret == false){
			//$ret = false;
		}
		
		//curl ���Ǵݱ�
		curl_close($ch);
		
		return $ret;
	}

	function P_rmesg1_explode($p_rmesg1) {
		$prmesg1 = explode('|',$p_rmesg1);
		$VbankNo = explode('=',$prmesg1[0]);
		$VbankDt = explode('=',$prmesg1[1]);

		$val[0] = $VbankNo[1];
		$val[1] = $VbankDt[1];
		
		return $val;
	}


	//������ü�� �ڵ�
	function VP_fn_cd1 ($code){
		$Vpfncd1 = array(
			'39' => '�泲',
			'34' => '����',
			'04' => '����',
			'03' => '���',
			'11' => '����',
			'31' => '�뱸',
			'32' => '�λ�',
			'45' => '������',
			'07' => '����',
			'88' => '����',
			'05' => '��ȯ',
			'20' => '�츮',
			'71' => '��ü��',
			'37' => '����',
			'23' => 'SC����',
			'35' => '����',
			'21' => '����',
			'81' => '�ϳ�',
			'27' => '��Ƽ',
			'48' => '����'
		);
		
		return $Vpfncd1[$code];
	}

	//������ü ������ �ڵ�
	function P_fn_cd2 ($code){
		$pfncd2 = array(
			'knb' => '�泲',
			'kibank' => '����',
			'kb' => '����',
			'ibk' => '���',
			'nacf' => '����',
			'daegubank' => '�뱸',
			'psb' => '�λ�',
			'kfcc' => '������',
			'suhyup' => '����',
			'shb' => '����',
			'keb' => '��ȯ',
			'woori' => '�츮',
			'post' => '��ü��',
			'jbbank' => '����',
			'kfb' => 'SC����',
			'cjb' => '����',
			'chb' => '����',
			'hnb' => '�ϳ�',
			'citi' => '��Ƽ',
			'cu' => '����'
		);
	
		return $pfncd2[$code];
	}

	//�ſ�ī�� �ڵ尪
	function CP_fn_cd1 ($code){
		$Cpfncd1 = array(
			'01' => '��',
			'02' => '����',
			'03' => '��ȯ',
			'04' => '�Ｚ',
			'05' => '����',
			'07' => 'JCB',
			'08' => '����',
			'09' => '�Ե�(�� �Ƹ߽�)',
			'10' => '����',
			'11' => '�ѹ�',
			'12' => '����',
			'13' => '�ѹ̽ż���',
			'14' => '�츮',
			'15' => '����',
			'16' => '����',
			'17' => '����',
			'18' => '����',
			'20' => '�Ե�',
			'24' => '�ϳ�',
			'25' => '�ؿ�',
			'26' => '��Ƽ',
			'74' => '�������',
			'75' => '�ϳ�����',
			'77' => '�Ѿ�',
			'79' => '�ż���',
			'80' => '����',
		);
		
		return $Cpfncd1[$code];
	}

	//�ſ�ī�� �ڵ尪
	function P_status($code){
		$P_status = array(
			'0021' => '����',
			'0031' => '����',
			'0051' => '�Աݴ����',
		);
		
		return $P_status[$code];
	}

	 function proc_fail_db($noti, $settleLog) {
		$db = $GLOBALS['db'];

		$db->query("update ".GD_ORDER." set cardtno='".$noti['P_TR_NO']."', step2='54', settlelog=concat(ifnull(settlelog,''),'".$settleLog."') where ordno='".$noti['P_OID']."' ");
		$db->query("update ".GD_ORDER_ITEM." set istep='54' where ordno=".$noti['P_OID']." ");

		return true;
	}
?>