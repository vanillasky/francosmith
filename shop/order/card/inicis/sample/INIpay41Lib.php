<?php


/****************************************************************************************
 **** ���Ҽ��ܺ��� PGID�� �ٸ��� ǥ���Ѵ� (2003.12.19 �븮 ������) ****
 ****************************************************************************************
 *** �ϴ��� PGID �κ��� ���Ҽ��ܺ��� TID�� ������ ǥ���ϵ��� �ϸ�,  ***
 *** ���Ƿ� �����ϴ� ��� ���� ���а� �߻� �ɼ� �����Ƿ� ����� ����  ***
 *** ���� �ʵ��� �Ͻñ� �ٶ��ϴ�.     *********************************************
 *** ���Ƿ� �����Ͽ� �߻��� ������ ���ؼ��� (��)�̴Ͻý��� å����    *****
 *** ������ ���� �Ͻñ� �ٶ��ϴ�.      ********************************************
 ***************************************************************************************/
extract($_POST);
extract($_GET);
switch($paymethod){

	case(Card): // �ſ�ī��
		$pgid = "CARD";
		break;
	case(Account): // ���� ���� ��ü
		$pgid = "ACCT";
		break;
	case(DirectBank): // �ǽð� ���� ��ü
		$pgid = "DBNK";
		break;
	case(OCBPoint): // OCB
		$pgid = "OCBP";
		break;
	case(VCard): // ISP ����
		$pgid = "ISP_";
		break;
	case(HPP): // �޴��� ����
		$pgid = "HPP_";
		break;
	case(ArsBill): // 700 ��ȭ����
		$pgid = "ARSB";
		break;
	case(PhoneBill): // PhoneBill ����(�޴� ��ȭ)
		$pgid = "PHNB";
		break;
	case(Ars1588Bill): // 1588 ��ȭ����
		$pgid = "1588";
		break;
	case(VBank):  // ������� ��ü
		$pgid = "VBNK";
		break;
	case(Culture):  // ��ȭ��ǰ�� ����
		$pgid = "CULT";
		break;
	case(CMS): // CMS ����
		$pgid = "CMS_";
		break;
	case(AUTH): // �ſ�ī�� ��ȿ�� �˻�
		$pgid = "AUTH";
		break;
	case(INIcard): // ��Ƽ�Ӵ� ����
		$pgid = "INIC";
		break;
	case(MDX):  // �󵦽�ī��
		$pgid = "MDX_";
		break;
	default:        // ��� ���Ҽ��� �� �߰��Ǵ� ���Ҽ����� ��� �⺻���� paymethod�� 4�ڸ��� �Ѿ�´�.
		$pgid = $paymethod;
}

/*************************************************************************************
 *************************************************************************************
   ********************        ���κ� ���� ���� �Ұ�      ************************
 *************************************************************************************
 *************************************************************************************/

/*----------------------------------------------------------*
 *������ �Һΰŷ��� ��� �Һΰ����� �ڿ� �������Һ����� ǥ��*
 *----------------------------------------------------------*/

if($quotainterest == "1")
{
	$interest = "(�������Һ�)";
}

/*----------------------------------------------------------*/


class INIpay41
{
	var $fd;
	var $m_inipayHome; 		//�̴����� Ȩ���͸�
	var $m_test; 			// "true"�� 17������ ������
	var $m_debug; 			// "true"�� ���� �α׸� �����
	var $m_type; 			// �ŷ� ����
	var $m_pgId; 			// PGID
	var $m_keyPw; 			// keypass.enc�� pass phrase
	var $m_subPgIp; 		// 3��° ���� PG IP Addr
	var $m_mid; 			// ���� ���̵�
	var $m_language; 		// �����
	var $m_oldTid; 			// �κ����(�����) ���� ���ŷ����̵�
	var $m_tid; 			// �ŷ����̵�
	var $m_goodName; 		// ��ǰ��
	var $m_currency; 		// ȭ����� (WON, USD)
	var $m_price; 			// �ݾ�
	var $m_confirm_price;		// ����� ��û �ݾ�
	var $m_buyerName; 		// ������ ����
	var $m_buyerTel; 		// ������ ��ȭ��ȣ (SMS ���� �ݵ�� �̵���ȭ...)
	var $m_buyerEmail; 		// ������ �̸���
	var $m_recvName; 		// ������ ����
	var $m_recvTel; 		// ������ ����ó
	var $m_recvAddr; 		// ������ �ּ�
	var $m_recvPostNum; 		// ������ �����ȣ
	var $m_recvMsg; 		// �����ο��� ������ �޽���
	var $m_companyNumber; 		// ����� ��Ϲ�ȣ(10�ڸ� ����)
	var $m_cardCode; 		// ī��� �ڵ�
	var $m_cardIssuerCode; 		// ī�� �߱޻�(����) �ڵ�
	var $m_payMethod; 		// ���ҹ��
	var $m_merchantReserved1; 	// �����ʵ� (����)
	var $m_merchantReserved2; 	// �����ʵ� (����)
	var $m_merchantReserved3; 	// �����ʵ� (����)
	var $m_uip; 			// ������ PC IP Addr
	var $m_url; 			// ���� ���� URL
	var $m_billingPeriod; 		// Billing �Ⱓ (2002/07 ���� ������)
	var $m_payOption;
	var $m_encrypted; 		// ��ȣ�� (��ĪŰ�� ��ȣȭ�� PLAIN TEXT)
	var $m_sessionKey; 		// ��ȣ�� (����Ű�� ��ȣȭ�� ��ĪŰ)
	var $m_uid; 			// INIpay User ID (2002/07 ���� ������)
	var $m_quotaInterest; 		// �������Һ� FLAG
	var $m_cardNumber;  		// �ſ�ī�� ��ȣ
	var $m_price1; 			// OK Cashbag, Netimoney ���� ����ϴ� �߰� �ݾ�����
	var $m_price2; 			// OK Cashbag, Netimoney ���� ����ϴ� �߰� �ݾ�����
	var $m_cardQuota; 		// �ҺαⰣ
	var $m_bankCode; 		// �����ڵ�
	var $m_ocbNumber; 		// OK Cashbag ī�� ��ȣ
	var $m_ocbPasswd; 		// OK Cashbag ī�� ��й�ȣ
	var $m_authentification; 	// �������� FLAG
	var $m_authField1; 		// ���������� �ʿ��� �ֹι�ȣ �� 7�ڸ�
	var $m_authField2; 		// ���������� �ʿ��� ī�� ��й�ȣ �� 2�ڸ�
	var $m_authField3; 		// ���������� �ʿ��� �����ʵ�
	var $m_passwd; 			// (����) ��й�ȣ
	var $m_cardExpy; 		// �ſ�ī�� ��ȿ�Ⱓ-�� (YY)
	var $m_cardExpm; 		// �ſ�ī�� ��ȿ�Ⱓ-�� (MM)
	var $m_cardExpire; 		// �ſ�ī�� ��ȿ�Ⱓ (YYMM)
	var $m_ocbCardType; 		// OK Cashbag ī�� ���� (�ڻ�ī��...)
	var $m_merchantReserved; 	// �����ʵ� (������)
	var $m_cancelMsg; 		// ��� ����
	var $m_resultCode; 		// ��� �ڵ� (2 digit)
	var $m_resultMsg; 		// ��� ����
	var $m_authCode; 		// �ſ�ī�� ���ι�ȣ
	var $m_ocbResultPoint; 		// OK Cashbag Point ��ȸ�� ��������Ʈ
	var $m_authCertain; 		// PG���� ���������� �����Ͽ������� ��Ÿ���� FLAG
	var $m_ocbSaveAuthCode; 	// OK Cashbag ���� ���ι�ȣ
	var $m_ocbUseAuthCode; 		// OK Cashbag ��� ���ι�ȣ
	var $m_ocbAuthDate; 		// OK Cashbag ���� ��¥
	var $m_pgAuthDate; 		// PG ���� ��¥
	var $m_pgAuthTime; 		// PG ���� �ð�
	var $m_pgCancelDate; 		// PG ��� ��¥
	var $m_pgCancelTime; 		// PG ��� �ð�
	var $m_requestMsg; 		// ���� �޽���
	var $m_responseMsg; 		// ���� �޽���
	var $m_resulterrcode; 		// ����޼��� �����ڵ�
	var $m_resultprice; 		// ���� �Ϸ� �ݾ�

/* == ƾĳ�� �߰� �ʵ� (2005.02.01 �븮 ������) == */
	var $m_remain_price;		// ƾĳ�� �ܾ�

/* == CMS������ü �ʵ� �߰� (2004. 11. 15 �븮 ������) == */
	var $m_bankAccount; 		// ���� ���¹�ȣ
	var $m_regNumber; 		// �ֹε�Ϲ�ȣ (�ǽð� ������ �ֹε�� ��ȣ 13�ڸ�)
	var $m_CMSBankCode;		// �����Ϸ��� ���� �����ڵ�
	var $m_price_org;		// ����ѱݾ�
	var $m_cmsday;			// ��ݿ�����
	var $m_cmsdatefrom;		// ��ݽ��ۿ�
	var $m_cmsdatero;		// ��������
	var $m_cmstype;			// 1-CMS �ڵ�(����)��ü, 2-CMS���µ��

/* == �κ����(�����) �߰� �ʵ� (2004.11.05 �븮 ������) == */
	var $m_tid_org;		// ���ŷ� TID
	var $m_remains = "";		// �������� �ݾ�
	var $m_flg_partcancel = "";	// �κ����, ����� ���а�
	var $m_cnt_partcancel = ""; 	// �κ����(�����) ��ûȽ��

/* == �ʵ��߰� (2004.06.23 �븮 ������) == */
	var $m_moid; 		// ��ǰ�ֹ���ȣ
	var $m_codegw; 		// ��ȭ���� ����� �ڵ�
	var $m_ParentEmail; 	// ��ȣ�� �̸��� �ּ�
	var $m_ocbcardnumber; 	// OK CASH BAG ���� , ������ ��� OK CASH BAG ī�� ��ȣ
	var $m_cultureid;	// ���� ���� ID
	var $m_directbankacc;	// ���� ������ü ������ ��� ���� ���� ��ȣ
	var $m_directbankcode;	// ���� ������ü ������ ��� ���� �ڵ� ��ȣ
	var $m_billKey;		// �ǽð� ���� ��Ű
	var $m_cardPass;	// �ǽð� ������ �ſ�ī�� ��й�ȣ �� 2�ڸ�
	var $m_billtype;	// ����Ÿ�� (�ſ�ī�� - card, �޴��� - hpp)


/* ==  ������¸� ���� �߰� (2003.07.07 �븮 ������)  == */
	var $m_perno; 		// ������� ���� ������ �ֹι�ȣ
	var $m_oid; 		// �ֹ���ȣ(�������� ���޵Ǵ� ��)
	var $m_vacct; 		// ������� ��ȣ
	var $m_vcdbank; 	// ä���� ���� �����ڵ�
	var $m_dtinput; 	// �Ա� ������
	var $m_nminput; 	// �۱��� ��
	var $m_nmvacct; 	// ������ ��
	var $m_rvacct;		// ȯ�Ұ��� ��ȣ
	var $m_rvcdbank;	// ȯ�Ұ��� �����ڵ�
	var $m_rnminput;	// ȯ�Ұ��� �����ָ�

/* == ���� ������ ���� �ʵ� �߰� (2003.12.08 �븮 ������) == */
	var $m_cr_price;	// �� ���ݰ��� �ݾ�
	var $m_sup_price;	// ���ް�
	var $m_tax;		// �ΰ���
	var $m_srvc_price;	// �����
	var $m_usepot;		// ������ ���뵵
	var $m_ocbprice;	// OCB ������û�ݾ�

/* ==  ������¸� ���� �߰� (2006.10.18 ����)  == */
	var $m_tminput; 	// �Ա� ���� �ð�

/* ==  �������� ������ ���� (2006.12.27 �̽±�)  == */
	var $m_enc_arr = array();
	var $m_enctype;
	var $m_checkopt;
	var $m_rn;
	var $m_ini_rn;
	var $m_ini_encfield;
	var $m_ini_certid;

/* ==  ������û������ ����Ÿ ��ȣȭ(2007.01.10 �̽±�)  == */
	var $m_enc_src;

/* ==  ����� KVP�÷����� ó��(2007.01.25 �̽±�) == */
	var $m_kvp_card_prefix;
	var $m_kvp_noint;
	var $m_kvp_quota;

	var $m_pgn;
/* ==  ���¸��� ���ݿ�����(2007.06.28 �̽±�) == */
	var $m_OMFlag;
	var $m_SubCrCnt;
	var $m_om = array();

	/* exec ���� ���� �Լ� */
	function _exec($command, $output = null, $return_var = null) {
		/*
			escape �� string �� �̴Ͻý��� php ���̳ʸ����� �ؼ��� ���ϴ� �����, ` (backtick operator)�� escape �ϵ��� ����
		*/
		//$command = escapeshellcmd($command);
		$command = str_replace('`','\`',$command);
		return exec($command,$output,$return_var);
	}

	function startAction()
	{
		switch($this->m_type)
		{
			case("securepay") :
				$this->m_requestMsg =
					"inipayhome=" . $this->m_inipayHome . "\x0B" .
					"rn=" . $this->m_rn . "\x0B" .
					"encfield=" . $this->m_ini_encfield . "\x0B" .
					"pgid=" . $this->m_pgId . "\x0B" .
					"spgip=" . $this->m_subPgIp . "\x0B" .
					"admin=" . $this->m_keyPw . "\x0B" .
					"debug=" . $this->m_debug . "\x0B" .
	        "oid=" . $this->m_oid . "\x0B" .
					"test=" . $this->m_test . "\x0B" .
					"mid=" . $this->m_mid . "\x0B" .
					"uid=" . $this->m_uid . "\x0B" .
					"url=" . $this->m_url . "\x0B" .
					"uip=" . $this->m_uip . "\x0B" .
					"paymethod=" . $this->m_payMethod . "\x0B" .
					"goodname=" . $this->m_goodName . "\x0B" .
					"currency=" . $this->m_currency . "\x0B" .
					"price=" . $this->m_price . "\x0B" .
					"buyername=" . $this->m_buyerName . "\x0B" .
					"buyertel=" . $this->m_buyerTel . "\x0B" .
					"buyeremail=" . $this->m_buyerEmail . "\x0B" .
					"parentemail=" . $this->m_ParentEmail . "\x0B" .
					"recvname=" . $this->m_recvName . "\x0B" .
					"recvtel=" . $this->m_recvTel . "\x0B" .
					"recvaddr=" . $this->m_recvAddr . "\x0B" .
					"recvpostnum=" . $this->m_recvPostNum . "\x0B" .
					"recvmsg=" . $this->m_recvMsg . "\x0B" .
					"sessionkey=" . $this->m_sessionKey . "\x0B" .
					"encrypted=" . $this->m_encrypted . "\x0B" .
					"pgn=" . $this->m_pgn . "\x0B" .
					"enctype=" . $this->m_enctype . "\x0B" .
					"merchantreserved1=" . $this->m_merchantReserved1 . "\x0B" .
					"merchantreserved2=" . $this->m_merchantReserved2 . "\x0B" .
					"merchantreserved3=" . $this->m_merchantReserved3;
				$exec_str = $this->m_inipayHome . "/phpexec/INIsecurepay.phpexec \"" . $this->m_requestMsg . "\"";
				$this->m_responseMsg = $this->_exec($exec_str);
				if(strlen($this->m_responseMsg) <= 1)
					$this->m_responseMsg = "ResultCode=01&ResultMsg=[9199]INVOKE ERR : " . $this->m_inipayHome . "/phpexec/INIsecurepay.phpexec";
				break;

			case("cancel") :
				$this->m_requestMsg =
					"inipayhome=" . $this->m_inipayHome . "\x0B" .
					"pgid=" . $this->m_pgId . "\x0B" .
					"spgip=" . $this->m_subPgIp . "\x0B" .
					"admin=" . $this->m_keyPw . "\x0B" .
					"debug=" . $this->m_debug . "\x0B" .
					"test=" . $this->m_test . "\x0B" .
					"mid=" . $this->m_mid . "\x0B" .
					"tid=" . $this->m_tid . "\x0B" .
					"msg=" . $this->m_cancelMsg . "\x0B" .
					"uip=" . $this->m_uip . "\x0B" .
					"merchantreserved=" . $this->m_merchantReserved;
				$this->m_responseMsg = $this->_exec($this->m_inipayHome . "/phpexec/INIcancel.phpexec \"" . $this->m_requestMsg . "\"");
				if(strlen($this->m_responseMsg) <= 1)
					$this->m_responseMsg = "ResultCode=01&ResultMsg=[9199]INVOKE ERR : " . $this->m_inipayHome . "/phpexec/INIcancel.phpexec";
				break;

			case("confirm") :
				$this->m_requestMsg =
					"inipayhome=" . $this->m_inipayHome . "\x0B" .
					"test=" . $this->m_test . "\x0B" .
					"pgid=" . $this->m_pgId . "\x0B" .
					"spgip=" . $this->m_subPgIp . "\x0B" .
					"admin=" . $this->m_keyPw . "\x0B" .
					"mid=" . $this->m_mid . "\x0B" .
					"tid=" . $this->m_tid . "\x0B" .
					"debug=" . $this->m_debug . "\x0B" .
					"merchantreserved=" . $this->m_merchantReserved;
				$this->m_responseMsg = $this->_exec($this->m_inipayHome . "/phpexec/INIconfirm.phpexec \"" . $this->m_requestMsg . "\"");
				if(strlen($this->m_responseMsg) <= 1)
					$this->m_responseMsg = "ResultCode=01&ResultMsg=[9199]INVOKE ERR : " . $this->m_inipayHome . "/phpexec/INIconfirm.phpexec";
				break;

			case("capture") :
				$this->m_requestMsg =
					"inipayhome=" . $this->m_inipayHome . "\x0B" .
					"test=" . $this->m_test . "\x0B" .
					"pgid=" . $this->m_pgId . "\x0B" .
					"spgip=" . $this->m_subPgIp . "\x0B" .
					"admin=" . $this->m_keyPw . "\x0B" .
					"mid=" . $this->m_mid . "\x0B" .
					"tid=" . $this->m_tid . "\x0B" .
					"debug=" . $this->m_debug . "\x0B" .
					"merchantreserved=" . $this->m_merchantReserved;
				$this->m_responseMsg = $this->_exec($this->m_inipayHome . "/phpexec/INIcapture.phpexec \"" . $this->m_requestMsg . "\"");
				if(strlen($this->m_responseMsg) <= 1)
					$this->m_responseMsg = "ResultCode=01&ResultMsg=[9199]INVOKE ERR : " . $this->m_inipayHome . "/phpexec/INIcapture.phpexec";
				break;

			case("formpay") :
				$this->m_requestMsg =
					"inipayhome=" . $this->m_inipayHome . "\x0B" .
					"pgid=" . $this->m_pgId . "\x0B" .
					"spgip=" . $this->m_subPgIp . "\x0B" .
					"admin=" . $this->m_keyPw . "\x0B" .
					"debug=" . $this->m_debug . "\x0B" .
					"test=" . $this->m_test . "\x0B" .
					"mid=" . $this->m_mid . "\x0B" .
					"uid=" . $this->m_uid . "\x0B" .
					"url=" . $this->m_url . "\x0B" .
					"uip=" . $this->m_uip . "\x0B" .
					"paymethod=" . $this->m_payMethod . "\x0B" .
					"goodname=" . $this->m_goodName . "\x0B" .
					"currency=" . $this->m_currency . "\x0B" .
					"price=" . $this->m_price . "\x0B" .
					"buyername=" . $this->m_buyerName . "\x0B" .
					"buyertel=" . $this->m_buyerTel . "\x0B" .
					"buyeremail=" . $this->m_buyerEmail . "\x0B" .
					"recvname=" . $this->m_recvName . "\x0B" .
					"recvtel=" . $this->m_recvTel . "\x0B" .
					"recvaddr=" . $this->m_recvAddr . "\x0B" .
					"recvpostnum=" . $this->m_recvPostNum . "\x0B" .
					"recvmsg=" . $this->m_recvMsg . "\x0B" .
					"cardnumber=" . $this->m_cardNumber . "\x0B" .
					"cardquota=" . $this->m_cardQuota . "\x0B" .
					"cardexpy=" . $this->m_cardExpy . "\x0B" .
					"cardexpm=" . $this->m_cardExpm . "\x0B" .
					"quotainterest=" . $this->m_quotaInterest . "\x0B" .
					"authentification=" . $this->m_authentification . "\x0B" .
					"authfield1=" . $this->m_authfield1 . "\x0B" .
					"authfield2=" . $this->m_authfield2 . "\x0B" .
					"price1=" . $this->m_price1 . "\x0B" .
					"price2=" . $this->m_price2 . "\x0B" .
					"bankcode=" . $this->m_bankCode . "\x0B" .
					"bankaccount=" . $this->m_bankAccount . "\x0B" .
					"regnumber=" . $this->m_regNumber . "\x0B" .
					"price_org=" . $this->m_price_org . "\x0B" .
					"cmsday=" . $this->m_cmsday .  "\x0B" .
					"cmsdatefrom=" . $this->m_cmsdatefrom . "\x0B" .
					"cmsdateto=" . $this->m_cmsdateto . "\x0B" .
					"cmstype=" . $this->m_cmstype . "\x0B" .
					"ocbnumber=" . $this->m_ocbNumber . "\x0B" .
					"ocbpasswd=" . $this->m_ocbPasswd . "\x0B" .
					"passwd=" . $this->m_passwd . "\x0B" .
					"perno=" . $this->m_perno . "\x0B" .
	                "oid=" . $this->m_oid . "\x0B" .
	                "vacct=" . $this->m_vacct . "\x0B" .
	                "vcdbank=" . $this->m_vcdbank . "\x0B" .
	                "dtinput=" . $this->m_dtinput . "\x0B" .
	                "nminput=" . $this->m_nminput . "\x0B" .
					"companynumber=" . $this->m_companyNumber . "\x0B" .
					"merchantreserved1=" . $this->m_merchantReserved1 . "\x0B" .
					"merchantreserved2=" . $this->m_merchantReserved2 . "\x0B" .
					"merchantreserved3=" . $this->m_merchantReserved3;

				$this->m_responseMsg = $this->_exec($this->m_inipayHome . "/phpexec/INIformpay.phpexec \"" . $this->m_requestMsg . "\"");
				if(strlen($this->m_responseMsg) <= 1)
					$this->m_responseMsg = "ResultCode=01&ResultMsg=[9199]INVOKE ERR : " . $this->m_inipayHome . "/phpexec/INIformpay.phpexec";


				break;

			case("repay") :
				$this->m_requestMsg =
					"inipayhome=" . $this->m_inipayHome . "\x0B" .
					"pgid=" . $this->m_pgId . "\x0B" .
					"spgip=" . $this->m_subPgIp . "\x0B" .
					"admin=" . $this->m_keyPw . "\x0B" .
					"debug=" . $this->m_debug . "\x0B" .
					"test=" . $this->m_test . "\x0B" .
					"mid=" . $this->m_mid . "\x0B" .
					"oldtid=" . $this->m_oldTid . "\x0B" .
					"url=" . $this->m_url . "\x0B" .
					"uip=" . $this->m_uip . "\x0B" .
					"goodname=" . $this->m_goodName . "\x0B" .
					"currency=" . $this->m_currency . "\x0B" .
					"price=" . $this->m_price . "\x0B" .
					"confirm_price=" . $this->m_confirm_price . "\x0B" .
					"buyername=" . $this->m_buyerName . "\x0B" .
					"buyertel=" . $this->m_buyerTel . "\x0B" .
					"buyeremail=" . $this->m_buyerEmail . "\x0B" .
					"cardquota=" . $this->m_cardQuota . "\x0B" .
					"quotainterest=" . $this->m_quotaInterest . "\x0B" .
					"merchantreserved1=" . $this->m_merchantReserved1 . "\x0B" .
					"merchantreserved2=" . $this->m_merchantReserved2 . "\x0B" .
					"merchantreserved3=" . $this->m_merchantReserved3;
				$this->m_responseMsg = $this->_exec($this->m_inipayHome . "/phpexec/INIrepay.phpexec \"" . $this->m_requestMsg . "\"");
				if(strlen($this->m_responseMsg) <= 1)
					$this->m_responseMsg = "ResultCode=01&ResultMsg=[9199]INVOKE ERR : " . $this->m_inipayHome . "/phpexec/INIrepay.phpexec";
				break;

			case("ocbquery") :
				$this->m_requestMsg =
					"inipayhome=" . $this->m_inipayHome . "\x0B" .
					"mid=" . $this->m_mid . "\x0B" .
					"ocbnumber=" . $this->m_ocbNumber;
				$this->m_responseMsg = $this->_exec($this->m_inipayHome . "/phpexec/INIocbquery.phpexec \"" . $this->m_requestMsg . "\"");
				if(strlen($this->m_responseMsg) <= 1)
					$this->m_responseMsg = "ResultCode=01&ResultMsg=[9199]INVOKE ERR : " . $this->m_inipayHome . "/phpexec/INIocbquery.phpexec";
				break;

			case("auth_bill") :
				$this->m_requestMsg =
					"inipayhome=" . $this->m_inipayHome . "\x0B" .
					"paymethod=" . $this->m_payMethod . "\x0B" .
					"pgid=" . $this->m_pgId . "\x0B" .
					"spgip=" . $this->m_subPgIp . "\x0B" .
					"admin=" . $this->m_keyPw . "\x0B" .
					"debug=" . $this->m_debug . "\x0B" .
					"billtype=" . $this->m_billtype . "\x0B" .
					"mid=" . $this->m_mid . "\x0B" .
					"test=" . $this->m_test . "\x0B" .
					"uip=" . $this->m_uip . "\x0B" .
					"goodname=" . $this->m_goodName . "\x0B" .
					"uid=" . $this->m_uid . "\x0B" .
					"url=" . $this->m_url . "\x0B" .
					"buyername=" . $this->m_buyerName . "\x0B" .
					"encrypted=" . $this->m_encrypted . "\x0B" .
					"sessionkey=" . $this->m_sessionKey . "\x0B" .
					"merchantReserved3=" . $this->m_merchantReserved3;

				$this->m_responseMsg = $this->_exec($this->m_inipayHome . "/phpexec/INIauth_bill.phpexec \"" . $this->m_requestMsg . "\"");
				if(strlen($this->m_responseMsg) <= 1)
					$this->m_responseMsg = "ResultCode=01&ResultMsg=[9199]INVOKE ERR : " . $this->m_inipayHome . "/phpexec/INIauth_bill.phpexec";

				break;

			case("auth") :
				$this->m_requestMsg =
					"inipayhome=" . $this->m_inipayHome . "\x0B" .
					"paymethod=" . $this->m_payMethod . "\x0B" .
					"pgid=" . $this->m_pgId . "\x0B" .
					"spgip=" . $this->m_subPgIp . "\x0B" .
					"admin=" . $this->m_keyPw . "\x0B" .
					"debug=" . $this->m_debug . "\x0B" .
					"billtype=" . $this->m_billtype . "\x0B" .
					"mid=" . $this->m_mid . "\x0B" .
					"test=" . $this->m_test . "\x0B" .
					"uip=" . $this->m_uip . "\x0B" .
					"uid=" . $this->m_uid . "\x0B" .
					"url=" . $this->m_url . "\x0B" .
					"buyername=" . $this->m_buyerName . "\x0B" .
					"encrypted=" . $this->m_encrypted . "\x0B" .
					"sessionkey=" . $this->m_sessionKey . "\x0B" .
					"merchantReserved3=" . $this->m_merchantReserved3;

				$this->m_responseMsg = $this->_exec($this->m_inipayHome . "/phpexec/INIauth.phpexec \"" . $this->m_requestMsg . "\"");
				if(strlen($this->m_responseMsg) <= 1)
					$this->m_responseMsg = "ResultCode=01&ResultMsg=[9199]INVOKE ERR : " . $this->m_inipayHome . "/phpexec/INIauth.phpexec";

				break;

			case("formauth") :
				$this->m_requestMsg =
					"inipayhome=" . $this->m_inipayHome . "\x0B" .
					"paymethod=" . $this->m_payMethod . "\x0B" .
					"pgid=" . $this->m_pgId . "\x0B" .
					"spgip=" . $this->m_subPgIp . "\x0B" .
					"admin=" . $this->m_keyPw . "\x0B" .
					"debug=" . $this->m_debug . "\x0B" .
					"billtype=" . $this->m_billtype . "\x0B" .
					"mid=" . $this->m_mid . "\x0B" .
					"test=" . $this->m_test . "\x0B" .
					"uip=" . $this->m_uip . "\x0B" .
					"cardnumber=" . $this->m_cardNumber . "\x0B" .
					"cardexpy=" . $this->m_cardExpy . "\x0B" .
					"cardexpm=" . $this->m_cardExpm . "\x0B" .
					"authfield1=" . $this->m_authfield1 . "\x0B" .
					"authfield2=" . $this->m_authfield2 . "\x0B" .
					"goodname=" . $this->m_goodName . "\x0B" .
					"uid=" . $this->m_uid . "\x0B" .
					"url=" . $this->m_url . "\x0B" .
					"buyername=" . $this->m_buyerName . "\x0B" .
					"merchantReserved3=" . $this->m_merchantReserved3;

				$this->m_responseMsg = $this->_exec($this->m_inipayHome . "/phpexec/INIformauth.phpexec \"" . $this->m_requestMsg . "\"");
				if(strlen($this->m_responseMsg) <= 1)
					$this->m_responseMsg = "ResultCode=01&ResultMsg=[9199]INVOKE ERR : " . $this->m_inipayHome . "/phpexec/INIformauth.phpexec";

				break;

			case("reqrealbill") :
				$this->m_requestMsg =
					"inipayhome=" . $this->m_inipayHome . "\x0B" .
					"pgid=" . $this->m_pgId . "\x0B" .
					"spgip=" . $this->m_subPgIp . "\x0B" .
					"admin=" . $this->m_keyPw . "\x0B" .
					"debug=" . $this->m_debug . "\x0B" .
					"mid=" . $this->m_mid . "\x0B" .
					"uip=" . $this->m_uip . "\x0B" .
					"paymethod=" . $this->m_payMethod . "\x0B" .
					"url=" . $this->m_url . "\x0B" .
					"test=" . $this->m_test . "\x0B" .
					"goodname=" . $this->m_goodName . "\x0B" .
					"currency=" . $this->m_currency . "\x0B" .
					"price=" . $this->m_price . "\x0B" .
					"billkey=" . $this->m_billKey . "\x0B" .
					"billtype=" . $this->m_billtype . "\x0B" .
					"cardpass=" . $this->m_cardPass . "\x0B" .
					"regnumber=" . $this->m_regNumber . "\x0B" .
					"cardquota=" . $this->m_cardQuota . "\x0B" .
					"authentification=" . $this->m_authentification . "\x0B" .
					"quotainterest=" . $this->m_quotaInterest . "\x0B" .
					"buyername=" . $this->m_buyerName . "\x0B" .
					"buyertel=" . $this->m_buyerTel . "\x0B" .
					"buyeremail=" . $this->m_buyerEmail . "\x0B" .
					"merchantreserved3=" . $this->m_merchantReserved3;

				$this->m_responseMsg = $this->_exec($this->m_inipayHome . "/phpexec/INIreqrealbill.phpexec \"" . $this->m_requestMsg . "\"");
				if(strlen($this->m_responseMsg) <= 1)
					$this->m_responseMsg = "ResultCode=01&ResultMsg=[9199]INVOKE ERR : " . $this->m_inipayHome . "/phpexec/INIreqrealbill.phpexec";

				break;

			case("receipt") :
				$this->m_requestMsg =
					"inipayhome=" . $this->m_inipayHome . "\x0B" .
					"pgid=" . $this->m_pgId . "\x0B" .
					"spgip=" . $this->m_subPgIp . "\x0B" .
					"admin=" . $this->m_keyPw . "\x0B" .
					"debug=" . $this->m_debug . "\x0B" .
					"test=" . $this->m_test . "\x0B" .
					"mid=" . $this->m_mid . "\x0B" .
					"uip=" . $this->m_uip . "\x0B" .
					"paymethod=" . $this->m_payMethod . "\x0B" .
					"goodname=" . $this->m_goodName . "\x0B" .
					"currency=" . $this->m_currency . "\x0B" .
					"cr_price=" . $this->m_cr_price . "\x0B" .
					"sup_price=" . $this->m_sup_price . "\x0B" .
					"tax=" . $this->m_tax . "\x0B" .
					"srvc_price=" . $this->m_srvc_price . "\x0B" .
					"buyername=" . $this->m_buyerName . "\x0B" .
					"buyertel=" . $this->m_buyerTel . "\x0B" .
					"buyeremail=" . $this->m_buyerEmail . "\x0B" .
					"ocbnumber=" . $this->m_ocbnumber . "\x0B" .
					"ocbprice=" . $this->m_ocbprice . "\x0B" .
					"reg_num=" . $this->m_reg_num . "\x0B" .
					"useopt=" . $this->m_useopt . "\x0B" .
					"companynumber=" . $this->m_companyNumber. "\x0B";
				//���¸��� ���ݿ�����
				if( $this->m_OMFlag == "2" && $this->m_SubCrCnt > 0 )
				{
					$this->m_requestMsg .= "OMFlag=".$this->m_OMFlag."\x0B";
					$this->m_requestMsg .= "SubCrCnt=".$this->m_SubCrCnt."\x0B";
					for( $i=1; $i <= $this->m_SubCrCnt ; $i++ )
					{
						$arr["SubNmComp$i"] .= $this->m_om["Submall_NmComp$i"]	. "\x0C";
						$arr["SubNmComp$i"] .= $this->m_om["Submall_NoComp$i"]	. "\x0C";
						$arr["SubNmComp$i"] .= $this->m_om["Submall_NoCEO$i"]		. "\x0C";
						$arr["SubNmComp$i"] .= $this->m_om["Submall_ID$i"]			. "\x0C";
						$arr["SubNmComp$i"] .= $this->m_om["Submall_price$i"]		. "\x0C";
						$arr["SubNmComp$i"] .= $this->m_om["Submall_srvprice$i"];
						$this->m_requestMsg .= "SubNmComp$i=".$arr["SubNmComp$i"]."\x0B";
					}
				}
				$this->m_responseMsg = $this->_exec($this->m_inipayHome . "/phpexec/INIreceipt.phpexec \"" . $this->m_requestMsg . "\"");
				if(strlen($this->m_responseMsg) <= 1)
					$this->m_responseMsg = "ResultCode=01&ResultMsg=[9199]INVOKE ERR : " . $this->m_inipayHome . "/phpexec/INIreceipt.phpexec";
				break;

			case("chkfake") :
				//�迭üũ
				if( !is_array( $this->m_enc_arr ) )
				{
					echo "�ּ� 4���� �׸� ���ؼ� ��ȣȭ�ϼž� �մϴ�.";
					return;
				}
				//set parameter
				$this->m_requestMsg =
					"inipayhome=" . $this->m_inipayHome . "\x0B" .
					"debug=" . $this->m_debug. "\x0B".
					"enctype=" . $this->m_enctype. "\x0B".
					"admin=" . $this->m_keyPw . "\x0B" .
					"checkopt=" . $this->m_checkopt . "\x0B";

				foreach ($this->m_enc_arr as $key => $val)
				{
					$this->m_requestMsg = $this->m_requestMsg . $key . "=" . $val . "\x0B";
				}
				$this->m_requestMsg = substr( $this->m_requestMsg, 0, -1 ); //trim end \x0B

				$exec_str = $this->m_inipayHome . "/phpexec/INIchkfake.phpexec \"" . $this->m_requestMsg . "\"";
				$this->_exec( $exec_str, &$output );
				if( is_array( $output ) )
				{
					foreach( $output as $out_str )
					{
						$resData.= $out_str."\n";
					}
						$resData = substr($resData, 0, -1); // Eliminate unnecessary \n
				}
				else
				{
					$resData .= $out_str;
				}
				$this->m_responseMsg = $resData;
				if(strlen($this->m_responseMsg) <= 1)
					$this->m_responseMsg = "ResultCode=01&ResultMsg=[9199]INVOKE ERR : " . $this->m_inipayHome . "/phpexec/INIchkfake.phpexec";
				break;

			case("makeenc") :
				$this->m_requestMsg =
					"inipayhome=" . $this->m_inipayHome . "\x0B" .
					"debug=" . $this->m_debug . "\x0B" .
					"mid=" . $this->m_mid . "\x0B" .
					"encsrc=" . $this->m_enc_src;
				$exec_str = $this->m_inipayHome . "/phpexec/INImakeenc.phpexec \"" . $this->m_requestMsg . "\"";
				$this->_exec( $exec_str, &$output );
				if( is_array( $output ) )
				{
					foreach( $output as $out_str )
					{
						$resData.= $out_str."\n";
					}
					$resData = substr($resData, 0, -1); // Eliminate unnecessary \n
				}
				else
				{
				  $resData .= $out_str;
				}
				$this->m_responseMsg = $resData;
				if(strlen($this->m_responseMsg) <= 1)
					$this->m_responseMsg = "ResultCode=01&ResultMsg=[9199]INVOKE ERR : " . $this->m_inipayHome . "/phpexec/INImakeenc.phpexec";
				break;

			case("KVPSafeKeyIN") :
				$this->m_requestMsg =
					"inipayhome=" . $this->m_inipayHome . "\x0B" .
					"pgid=" . $this->m_pgId . "\x0B" .
					"spgip=" . $this->m_subPgIp . "\x0B" .
					"admin=" . $this->m_keyPw . "\x0B" .
					"debug=" . $this->m_debug . "\x0B" .
					"test=" . $this->m_test . "\x0B" .
					"mid=" . $this->m_mid . "\x0B" .
					"uid=" . $this->m_uid . "\x0B" .
					"url=" . $this->m_url . "\x0B" .
					"uip=" . $this->m_uip . "\x0B" .
					"paymethod=" . $this->m_payMethod . "\x0B" .
					"goodname=" . $this->m_goodName . "\x0B" .
					"currency=" . $this->m_currency . "\x0B" .
					"cardcode=" . $this->m_cardCode . "\x0B" .
					"price=" . $this->m_price . "\x0B" .
					"buyername=" . $this->m_buyerName . "\x0B" .
					"buyertel=" . $this->m_buyerTel . "\x0B" .
					"buyeremail=" . $this->m_buyerEmail . "\x0B" .
					"sessionkey=" . $this->m_sessionKey . "\x0B" .
					"encrypted=" . $this->m_encrypted . "\x0B" .
					"kvp_card_prefix=" . $this->m_kvp_card_prefix . "\x0B" .
					"kvp_noint=" . $this->m_kvp_noint . "\x0B" .
					"kvp_quota=" . $this->m_kvp_quota;
				$exec_str = $this->m_inipayHome . "/phpexec/INIKVPSafeKeyIN.phpexec \"" . $this->m_requestMsg . "\"";
				$this->m_responseMsg = $this->_exec($exec_str);
				if(strlen($this->m_responseMsg) <= 1)
					$this->m_responseMsg = "ResultCode=01&ResultMsg=[9199]INVOKE ERR : " . $this->m_inipayHome . "/phpexec/INIKVPSafeKeyIN.phpexec";
				break;

			default :
				$this->m_responseMsg = "ResultCode=01&ResultMsg=ó���� �� ���� �ŷ������Դϴ� : " . $this->m_type;
		}

		parse_str($this->m_responseMsg);
		$this->m_resultCode = $ResultCode;
		$this->m_resultMsg = $ResultMsg;
		$this->m_payMethod = $PayMethod;
		$this->m_authCode = $CardAuthCode;
		$this->m_cardCode = $CardResultCode;
		$this->m_cardIssuerCode = $Detailcode;
		$this->m_tid = $Tid;
		$this->m_price1 = $Price1;
		$this->m_price2 = $Price2;
		$this->m_cardQuota = $CardResultQuota;
		$this->m_quotaInterest = $QuotaInterest;
		$this->m_authCertain = $AuthCertain;
		$this->m_pgAuthDate = $PGauthdate;
		$this->m_pgAuthTime = $PGauthtime;
		$this->m_ocbSaveAuthCode = $OCBauthcode1;
		$this->m_ocbUseAuthCode = $OCBauthcode2;
		$this->m_ocbAuthDate = $OCBauthdate;
		$this->m_ocbResultPoint = $ResultPoint;
		$this->m_cardNumber = $CardResultNumber;
		$this->m_cardExpire = $CardResultExpire;
		$this->m_cardQuota = $CardResultQuota;
		$this->m_perno = $perno;
		$this->m_void = $void;
		$this->m_vacct = $vacct;
		$this->m_vcdbank = $vcdbank;
		$this->m_dtinput = $dtinput;
/* == ������� ���� �߰� 2006.10.18 rywkim == */
		$this->m_tminput = $tminput;
		$this->m_nminput = $nminput;
		$this->m_nmvacct = $nmvacct;
		$this->m_rvacct = $rvacct;
		$this->m_rvcdbank = $vcdbank;
		$this->m_rnminput = $nminput;
		$this->m_eventFlag = $EventFlag;
		$this->m_nohpp = $nohpp;
		$this->m_noars = $noars;
		$this->m_resultprice = $Price;
		$this->m_pgCancelDate = $PGcanceldate;
		$this->m_pgCancelTime = $PGcanceltime;
		$this->m_authCertain = $Authentification;

/* == �޷����� ȯ������ == */
		$this->m_ReqCurrency = $ReqCurrency;		// �ش� ��ȭ �ڵ�
		$this->m_RateExchange = $RateExchange;		// ȯ��

/* == �ǽð� ���� �ʵ� == */
		$this->m_billKey = $BillKey;
    $this->m_cardPass = $CardPass;
    $this->m_cardKind = $CardKind;

/* == ƾĳ�� �߰� �ʵ�(2005.02.01 �븮 ������) == */
		$this->m_remain_price = $remain_price;		// ƾĳ�� �ܾ�

/* == ���ݿ����� ���� ���� �ʵ� == */
		$this->m_rcr_price = $RCR_Price;		// �����ݰ��� �ݾ�
		$this->m_rsup_price = $RSup_Price;		// ���ް�
		$this->m_rtax = $RTax;				// �ΰ���
		$this->m_rsrvc_price = $RSrvc_Price;		// �����
		$this->m_ruseopt = $RUseOpt;			// ���ݿ����� �뵵 ����
		$this->m_rcash_noappl = $Rcash_noappl;		// ���ݿ����� ���� ���ι�ȣ
		$this->m_rcash_rslt = $Rcash_rslt;		// ���ݿ����� �߱��ڵ� (4�ڸ�)

/* == ���ݿ����� ��� ���� ��ȣ ���� == */
		$this->m_rcash_cancel_noappl = $Rcash_cancel_noappl;


/* == CMS ������ü ���� �ʵ� (2004. 11. 15 �븮 ������) == */
		$this->m_cmsbankcode = $CMSBankCode;		// �����ڵ�


/* == �κ����(�����) �߰� �ʵ� (2004.11.05 �븮 ������) == */
		$this->m_tid_org = $TID_org;			// ���ŷ� TID
		$this->m_remains = $PR_remains;			// �������� �ݾ�
		$this->m_flg_partcancel = $flg_partcancel;	// �κ����, ����� ���а�
		$this->m_cnt_partcancel = $cnt_partcancel; 	// �κ����(�����) ��ûȽ��

/* == �߰� �ʵ� (2004.6.23 �븮 ������) == */
		$this->m_moid = $MOID;				// �����ֹ���ȣ
		$this->m_codegw = $CodeGW;			// ��ȭ���� ����� �ڵ�
		$this->m_ocbcardnumber = $OCBcardnumber; 	// OCB ī���ȣ
		$this->m_cultureid = $CultureID;		// ��ó���� ID, ƾĳ�� ID
		$this->m_directbankcode = $DirectBankCode;	// �ǽð� ���������ü �����ڵ�


/* == ����޼��� ($m_resultMsg)���� �����ڵ� ���� == */
		$str = $ResultMsg ;
		$arr = split("\]+", $str);
		$this->m_resulterrcode = substr($arr[0],1);	// []���� �ڵ常 ǥ��


/* == ������ ��ȭ ��ǰ�� ī�� �� (SKT ��ǰ�� �������� ���)== */
		$this->m_dgcl_cardcount = $dgcl_cardcount;


/* == SKT ��ǰ�� �ʵ� (���� ��� ����) == */
		$this->m_sktg_method = $sktg_method;

/* ==  �������� ������ ���� (2006.12.27 �̽±�)  == */
		$this->m_ini_rn = $rn;
		if($this->m_enctype == "asym")
		{
			$this->m_ini_encfield = str_replace(" ", "+", $encfield);
			$this->m_ini_encfield .= "&src=";
			$this->m_ini_encfield .= str_replace(" ", "+", $src);
		}
		else
		{
			$this->m_ini_encfield = str_replace(" ", "+", $encfield); //����ȸ�� because parse_str�� "+"->" "
		}
		$this->m_ini_certid = str_replace(" ", "+", $certid);
	}
}

?>
