<?

### ������ (XPay)

//include "../conf/pg.lgdacom.php";
require_once "../lib/load.class.php";
@include "../conf/pg.escrow.php";

	// �����̼� ������� ��� PG ���� ��ü
	resetPaymentGateway();

	// ������ ����
	$pg['zerofee']	= ( $pg['zerofee'] == "yes" ? '1' : '0' );			// ������ ���� (Y:1 / N:0)

	// ��ǰ ����
	if(!preg_match('/mypage/',$_SERVER['SCRIPT_NAME'])){
		$item = $cart -> item;
	}
	foreach($item as $v){
		$i++;
		if($i == 1) $ordnm = $v['goodsnm'];
	}
	if($i > 1)$ordnm .= " ��".($i-1)."��";

	/*
	 * 1. �⺻���� ������û ���� ����
	 *
	 * �⺻������ �����Ͽ� �ֽñ� �ٶ��ϴ�.(�Ķ���� ���޽� POST�� ����ϼ���)
	 */
	if(!$pg['serviceType']) $pg['serviceType'] = "service";
	$LGD['PLATFORM']				= $pg['serviceType'];								//LG������ ���� ���� ����(test:�׽�Ʈ, service:����)
	$LGD['CMID']					= $pg['id'];										//�������̵�
	$LGD['MID']						= (("test" == $LGD['PLATFORM'])?"t":"").$pg['id'];	//�������̵�
	$LGD['MERTKEY']					= $pg['mertkey'];									//�����޿��� �߱޹��� Ű��
	$LGD['OID']						= $_POST['ordno'];									//�ֹ���ȣ(�������� ����ũ�� �ֹ���ȣ�� �Է��ϼ���)
	$LGD['AMOUNT']					= $_POST['settleprice'];							//�����ݾ�("," �� ������ �����ݾ��� �Է��ϼ���)
	$LGD['PRODUCTINFO']				= $ordnm;											//��ǰ��
	$LGD['TIMESTAMP']				= date(YmdHms);										//Ÿ�ӽ�����
	$LGD['CUSTOM_SKIN']				= $pg['skin']?$pg['skin']:"blue";					//�������� ����â ��Ų (red, blue, cyan, green, yellow)
	$LGD['CUSTOM_PROCESSTIMEOUT']	= "600";											//������ ���ο�û���� ���� ��� �ð�(�ʴ���), ����Ʈ�� 10min

	$configPath						= $_SERVER['DOCUMENT_ROOT'].$cfg['rootDir']."/conf/lgdacom_today";		//LG�����޿��� ������ ȯ������("/conf/lgdacom.conf") ��ġ ����.

	switch ($_POST[settlekind]){

		case "c":	// �ſ�ī��
			$LGD['USABLEPAY']		= "SC0010";
			break;
		case "o":	// ������ü
			$LGD['USABLEPAY']		= "SC0030";
			break;
		case "v":	// �������
			$LGD['USABLEPAY']		= "SC0040";
			break;
		case "h":	// �ڵ���
			$LGD['USABLEPAY']		= "SC0060";
			break;
	}

	/*
	 * �������(������) ���� ������ �Ͻô� ��� �Ʒ� LGD_CASNOTEURL �� �����Ͽ� �ֽñ� �ٶ��ϴ�.
	 */
	$tmpUrl     = explode(':', $_SERVER['HTTP_HOST']);     // ���ȼ��� ����� ��� ��Ʈ ����
	$LGD['CASNOTEURL']  = "http://".$tmpUrl[0].str_replace(basename($_SERVER['PHP_SELF']),"",$_SERVER['PHP_SELF'])."card/lgdacom/cas_noteurl.php";

	/*
	 *************************************************
	 * 2. MD5 �ؽ���ȣȭ (�������� ������) - BEGIN
	 *
	 * MD5 �ؽ���ȣȭ�� �ŷ� �������� �������� ����Դϴ�.
	 *************************************************
	 *
	 * �ؽ� ��ȣȭ ����( LGD_MID + LGD_OID + LGD_AMOUNT + LGD_TIMESTAMP + LGD_MERTKEY )
	 * LGD_MID			: �������̵�
	 * LGD_OID			: �ֹ���ȣ
	 * LGD_AMOUNT		: �ݾ�
	 * LGD_TIMESTAMP	: Ÿ�ӽ�����
	 * LGD_MERTKEY		: ����MertKey (mertkey�� ���������� -> ������� -> ���������������� Ȯ���ϽǼ� �ֽ��ϴ�)
	 *
	 * MD5 �ؽ������� ��ȣȭ ������ ����
	 * LG�����޿��� �߱��� ����Ű(MertKey)�� ȯ�漳�� ����(lgdacom/conf/mall.conf)�� �ݵ�� �Է��Ͽ� �ֽñ� �ٶ��ϴ�.
	 */
	require_once(dirname(__FILE__)."/XPayClient.php");
	$xpay = &new XPayClient($configPath, $LGD['PLATFORM']);
   	$xpay->Init_TX($LGD['MID']);
	$LGD['HASHDATA'] = md5($LGD['MID'].$LGD['OID'].$LGD['AMOUNT'].$LGD['TIMESTAMP'].$LGD['MERTKEY']);
	$LGD['CUSTOM_PROCESSTYPE'] = "TWOTR";
	/*
	 *************************************************
	 * 2. MD5 �ؽ���ȣȭ (�������� ������) - END
	 *************************************************
	 */
	 $tpl->assign('LGD',$LGD);
?>