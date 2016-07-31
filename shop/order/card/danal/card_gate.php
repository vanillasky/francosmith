<?php
	// �ٳ� ���� ����
	header( "Pragma: No-Cache" );
	include( "./inc/function.php" );
	/********************************************************************************
	 *
	 * �ٳ� �޴��� ����
	 *
	 * - ���� ��û ������
	 *      CP���� �� ���� ���� ����
	 *
	 * ���� �ý��� ������ ���� ���ǻ����� �����ø� ���񽺰��������� ���� �ֽʽÿ�.
	 * DANAL Commerce Division Technique supporting Team
	 * EMail : tech@danal.co.kr
	 *
	 ********************************************************************************/

	$goodsNm = $danal->makeGoodsName($cart->item);					// ��ǰ�� (80����Ʈ �̳�)
	$domain = array_shift(explode(':', $_SERVER['HTTP_HOST']));		// ���� ������
	$address = (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$domain.($_SERVER['SERVER_PORT']!=='80'?':'.$_SERVER['SERVER_PORT']:'');		// ���� full URL
	$email = $_POST['email'];				// �̸���
	$ordno = $_POST['ordno'];				// �ֹ���ȣ
	$price = $_POST['settleprice'];			// �ֹ��ݾ�
	$isMobile = $_GET['isMobile'];			// ����� ���� Ȯ��
	$isPc = $_GET['pc'];					// n��ũ�� ���� Ȯ��
	
	// ȸ���̸� ���̵�,�ƴϸ� �̸���, �̸��ϵ� ������ guest
	if ($sess['m_id']) {
		$userid = $sess['m_id'];
	}
	else if ($email) {
		$userid = $email;
	}
	else {
		$userid = 'guest';
	}
	/********************************************************************************
	 *
	 * [ ���� ��û ������ ] *********************************************************
	 *
	 ********************************************************************************/

	/***[ �ʼ� ������ ]************************************/
	$TransR = array();

	/******************************************************
	 ** �Ʒ��� �����ʹ� �������Դϴ�.( �������� ������ )
	 * Command      : ITEMSEND2
	 * SERVICE      : TELEDIT
	 * ItemType     : Amount
	 * ItemCount    : 1
	 * OUTPUTOPTION : DEFAULT 
	 ******************************************************/
	$TransR["Command"] = "ITEMSEND2";
	$TransR["SERVICE"] = "TELEDIT";
	$TransR["ItemType"] = "Amount";
	$TransR["ItemCount"] = "1";
	$TransR["OUTPUTOPTION"] = "DEFAULT";

	/******************************************************
	 *  ID          : �ٳ����� ������ �帰 ID( function ���� ���� )
	 *  PWD         : �ٳ����� ������ �帰 PWD( function ���� ���� )
	 *  CPNAME      : CP ��
	 ******************************************************/
	$TransR["ID"] = $ID;
	$TransR["PWD"] = $PWD;
	$CPName = $shopConfig['shopName'];

	/******************************************************
	 * ItemAmt      : ���� �ݾ�( function ���� ���� )
	 *      - ���� ��ǰ�ݾ� ó���ÿ��� Session �Ǵ� DB�� �̿��Ͽ� ó���� �ֽʽÿ�.
	 *      - �ݾ� ó�� �� �ݾ׺����� ������ �ֽ��ϴ�.
	 * ItemName     : ��ǰ��
	 * ItemCode     : �ٳ����� ������ �帰 ItemCode
	 ******************************************************/
	$ItemAmt = $price;
	$ItemName = $goodsNm;
	$ItemCode = $danalCfg['serviceItemCode'];
	$ItemInfo = MakeItemInfo( $ItemAmt,$ItemCode,$ItemName );

	$TransR["ItemInfo"] = $ItemInfo;

	/***[ ���� ���� ]**************************************/
	/******************************************************
	 * SUBCP        : �ٳ����� �����ص帰 SUBCP ID
	 * USERID       : ����� ID
	 * ORDERID      : CP �ֹ���ȣ
	 * IsPreOtbill  : �ڵ����� ����(Y/N) AuthKey ���� ���� (�ڵ������� ���� AuthKey ������ �ʿ��� ��� : Y)
	 * IsSubscript	: �� ���� ���� ����(Y/N) (�� ���� ������ ���� ù ������ ��� : Y)
	 ******************************************************/
	$TransR["SUBCP"] = $danalCfg['S_CPID'];
	$TransR["USERID"] = $userid;
	$TransR["ORDERID"] = $ordno;
	$TransR["IsPreOtbill"] = "N";
	$TransR["IsSubscript"] = "N";

	/********************************************************************************
	 *
	 * [ CPCGI�� HTTP POST�� ���޵Ǵ� ������ ] **************************************
	 *
	 ********************************************************************************/

	/***[ �ʼ� ������ ]************************************/
	$ByPassValue = array();

	/******************************************************
	 * BgColor      : ���� ������ Background Color ����
	 * TargetURL    : ���� ���� ��û �� CP�� CPCGI FULL URL
	 * BackURL      : ���� �߻� �� ��� �� �̵� �� �������� FULL URL
	 * IsUseCI      : CP�� CI ��� ����( Y or N )
	 * CIURL        : CP�� CI FULL URL
	 ******************************************************/
	$ByPassValue["BgColor"] = "00";
	$ByPassValue["TargetURL"] = $address.$shopConfig['rootDir'].'/order/card/danal/card_return.php';

	// PC, ����� ��ü ��� url �б�
	if (isset($isMobile)) {
		$ByPassValue["BackURL"] = $address.$shopConfig['rootDir'].'/order/card/danal/card_back.php?ordno='.$ordno.'&isMobile=true&isPc='.$isPc;
	}
	else{
		$ByPassValue["BackURL"] = $address.$shopConfig['rootDir'].'/order/card/danal/card_back.php?ordno='.$ordno;
	}
	$ByPassValue["IsUseCI"] = "N";
	$ByPassValue["CIURL"] = $address.$shopConfig['rootDir'].'/order/card/danal/images/ci.gif';

	/***[ ���� ���� ]**************************************/

	/******************************************************
	 * Email	: ����� E-mail �ּ� - ���� ȭ�鿡 ǥ��
	 * IsCharSet	: CP�� Webserver Character set
	 ******************************************************/
	$ByPassValue["Email"] = $email;
	$ByPassValue["IsCharSet"] = "";

	/******************************************************
	 ** CPCGI�� POST DATA�� ���� �˴ϴ�.
	 **
	 ******************************************************/
	$ByPassValue['ordno'] = $ordno;

	// PC, ����� url �б�
	if (isset($isMobile)) {
		$startUrl = 'https://ui.teledit.com/Danal/Teledit/FlexMobile/Start.php';
		$ByPassValue['isMobile'] = $isMobile;	// card_check�� ����� ���� ����
		$ByPassValue['isPc'] = $isPc;			// card_check�� n��ũ�� ���� ����
	}
	else {
		$startUrl = 'https://ui.teledit.com/Danal/Teledit/Web/Start.php';
	}

	// �ٳ� ��� ���� �α� ���
	$danal->writeLog(
		'Paygate open start'.PHP_EOL.
		'File : '.__FILE__.PHP_EOL.
		'Transaction ID : '.$ordno.PHP_EOL.
		'Send data : '.http_build_query($TransR)
	);

	$Res = CallTeledit( $TransR,false );

	if( $Res["Result"] == "0" ) {
?>
<html>
<head>
<title>�ٳ� �޴��� ����</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
</head>
<body>
<form name="Ready" action="<?php echo $startUrl; ?>" method="post">
<?php
	MakeFormInput($Res,array("Result","ErrMsg"));
	MakeFormInput($ByPassValue);
?>
<input type="hidden" name="CPName"      value="<?=$CPName?>">
<input type="hidden" name="ItemName"    value="<?=$ItemName?>">
<input type="hidden" name="ItemAmt"     value="<?=$ItemAmt?>">
<input type="hidden" name="IsPreOtbill" value="<?=$TransR['IsPreOtbill']?>">
<input type="hidden" name="IsSubscript" value="<?=$TransR['IsSubscript']?>">
</form>
<script Language="JavaScript">
	var isMobile = "<?php echo $isMobile; ?>";

	if (isMobile) {
		document.Ready.target="_parent";
	}
	else{
		danalWin = window.open("","danalWin","width=500,height=680,toolbar=no,menubar=no,scrollbars=no,resizable=yes");
		danalWin.focus();
		Ready.target="danalWin";
	}

	document.Ready.submit();
</script>
</body>
</html>
<?php
	} else {
		/**************************************************************************
		 *
		 * ���� ���п� ���� �۾�
		 *
		 **************************************************************************/

		$Result		= $Res["Result"];
		$ErrMsg		= $Res["ErrMsg"];
		$AbleBack	= false;
		$BackURL	= $ByPassValue["BackURL"];
		$IsUseCI	= $ByPassValue["IsUseCI"];
		$CIURL		= $ByPassValue["CIURL"];
		$BgColor	= $ByPassValue["BgColor"];
			
		// ���� �α� �ۼ�
		$danal->failLog($ordno, $Result, $ErrMsg);

		if ($Result == '51') {
			msg('�ٳ� ���� �ݾ��� 300���� �ʰ��ؾ� �մϴ�.');
		}
		else {
			msg($ErrMsg);
		}
	}
?>
