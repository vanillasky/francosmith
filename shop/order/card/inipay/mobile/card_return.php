<?php
/**
 * �̴Ͻý� PG ��� ������
 * �̴Ͻý� PG ���� : INIpayMobile Web (V 2.4 - 20110725)
 */

include dirname(__FILE__)."/../../../../lib/library.php";
include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
include dirname(__FILE__)."/../../../../conf/pg.inipay.php";

parse_str($_POST['P_NOTI'],$P_NOTI);
// PG���� ������ üũ �� ��ȿ�� üũ
if (forge_order_check($_GET['ordno'],$P_NOTI['P_AMT']) === false) {
	msg('�ֹ� ������ ���� ������ ���� �ʽ��ϴ�. �ٽ� ���� �ٶ��ϴ�.',$cfgMobileShop['mobileShopRootDir']."/ord/order_fail.php?ordno=".$_GET['ordno'],'parent');
	exit();
}

// ���̹� ���ϸ��� ���� ���� API
include dirname(__FILE__).'/../../../../lib/naverNcash.class.php';
$naverNcash = new naverNcash(true);
if ($naverNcash->useyn == 'Y') {
	if ($_GET['settlekind'] == 'v') $ncashResult = $naverNcash->payment_approval($_GET['ordno'], false);
	else $ncashResult = $naverNcash->payment_approval($_GET['ordno'], true);
	if ($ncashResult === false) {
		msg('���̹� ���ϸ��� ��뿡 �����Ͽ����ϴ�.', $cfgMobileShop['mobileShopRootDir'].'/ord/order_fail.php?ordno='.$_GET['ordno'], 'parent');
		exit();
	}
}

// ����� ������ ó��
$pg_mobile	= $pg;

// ���� �α� ���� (�̴Ͻý� �α׷� ���Ϸ� ���� �̴Ͻý��� ��� ���� ����)
$logfile		= fopen( dirname(__FILE__) . '/../log/INI_Mobile_auth_'.date('Ymd').'.log', 'a+' );
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

//--- ������� ���ϰ� �Ľ� �Լ�
function stringUnserialize($string){
	$string = trim($string);
	$arr = explode("&",$string);
	$result = array();
	foreach($arr as $v){
		$div = explode("=",$v);
		$result[$div[0]] = $div[1];
	}
	return $result;
}

//--- ���� ���
$pgPayMethod	= array(
		'CARD'			=> '�ſ�ī��',
		'BANK'			=> '�ǽð�������ü',
		'MOBILE'		=> '�ڵ���',
		'VBANK'			=> '�������Ա�(�������)',
);

//--- ī��� �ڵ�
$pgCards	= array(
		'01'	=> '��ȯī��',
		'03'	=> '�Ե�ī��',
		'04'	=> '����ī��',
		'06'	=> '����ī��',
		'11'	=> 'BCī��',
		'12'	=> '�Ｚī��',
		'13'	=> '(��)LGī��',
		'14'	=> '����ī��',
		'15'	=> '�ѹ�ī��',
		'16'	=> 'NHī��',
		'17'	=> '�ϳ�SKī��',
		'21'	=> '�ؿܺ���ī��',
		'22'	=> '�ؿܸ�����ī��',
		'23'	=> '�ؿ�JCBī��',
		'24'	=> '�ؿܾƸ߽�ī��',
		'25'	=> '�ؿܴ��̳ʽ�ī��',
);

//--- ���� �ڵ�
$pgBanks	= array(
		'02'	=> '�ѱ��������',
		'03'	=> '�������',
		'04'	=> '��������',
		'05'	=> '��ȯ����',
		'07'	=> '�����߾�ȸ',
		'11'	=> '�����߾�ȸ',
		'12'	=> '��������',
		'16'	=> '�����߾�ȸ',
		'20'	=> '�츮����',
		'21'	=> '��������',
		'23'	=> '��������',
		'25'	=> '�ϳ�����',
		'26'	=> '��������',
		'27'	=> '�ѱ���Ƽ����',
		'31'	=> '�뱸����',
		'32'	=> '�λ�����',
		'34'	=> '��������',
		'35'	=> '��������',
		'37'	=> '��������',
		'38'	=> '��������',
		'39'	=> '�泲����',
		'41'	=> '��ī��',
		'53'	=> '��Ƽ����',
		'54'	=> 'ȫ�����������',
		'71'	=> '��ü��',
		'81'	=> '�ϳ�����',
		'83'	=> '��ȭ����',
		'87'	=> '�ż���',
		'88'	=> '��������',
);

//--- ���� ����
if($_POST['P_STATUS'] === '00')
{
	//--- �̴Ͻý��� ���� ��û�� ���� ����
	$reqData	= array(
		'P_TID'	=> $_POST['P_TID'],
		'P_MID'	=> $pg_mobile['id'],
	);

	//--- ���� ���� ��û
	$res	= readpost($_POST['P_REQ_URL'],$reqData);

	//--- ���� ��� ���ϰ� �Ľ�
	$resData	= stringUnserialize($res);

	//--- ��� �α� ���� (�̴Ͻý� �α׷� ���Ϸ� ���� �̴Ͻý��� ��� ���� ����)
	$logfile		= fopen( dirname(__FILE__) . '/../log/INI_Mobile_result_'.date('Ymd').'.log', 'a+' );
	$logInfo	 = '------------------------------------------------------------------------------'.chr(10);
	$logInfo	.= 'INFO	['.date('Y-m-d H:i:s').']	START Order log'.chr(10);
	foreach ($resData as $key => $val) {
		$logInfo	.= 'DEBUG	['.date('Y-m-d H:i:s').']	'.$key.'	: '.$val.chr(10);
	}
	$logInfo	.= 'DEBUG	['.date('Y-m-d H:i:s').']	IP	: '.$_SERVER['REMOTE_ADDR'].chr(10);
	$logInfo	.= 'INFO	['.date('Y-m-d H:i:s').']	END Order log'.chr(10);
	$logInfo	.= '------------------------------------------------------------------------------'.chr(10).chr(10);
	fwrite( $logfile, $logInfo);
	fclose( $logfile );

	//--- �ֹ� ��ȣ
	$ordno	= $resData['P_OID'];

	//--- ��� �޽���
	$resData['P_RMESG1']	= strip_tags($resData['P_RMESG1']);

	//--- �α� ����
	$settlelog	= '';
	$settlelog	.= '===================================================='.chr(10);
	$settlelog	.= 'PG�� : �̴Ͻý� - INIpay Mobile'.chr(10);
	$settlelog	.= '�ֹ���ȣ : '.$ordno.chr(10);
	$settlelog	.= '�ŷ���ȣ : '.$resData['P_TID'].chr(10);
	$settlelog	.= '����ڵ� : '.$resData['P_STATUS'].chr(10);
	$settlelog	.= '������� : '.$resData['P_RMESG1'].chr(10);
	$settlelog	.= '���ҹ�� : '.$resData['P_TYPE'].' - '.$pgPayMethod[$resData['P_TYPE']].chr(10);
	$settlelog	.= '���αݾ� : '.$resData['P_AMT'].chr(10);
	$settlelog	.= '�������� : '.$resData['P_AUTH_DT'].chr(10);
	$settlelog	.= '���ι�ȣ : '.$resData['P_AUTH_NO'].chr(10);
	$settlelog	.= ' --------------------------------------------------'.chr(10);

	//--- ���ο��� / ���� ����� ���� ó�� ����
	if($resData['P_STATUS'] === "00"){

		// PG ���
		$getPgResult	= true;
		$pgResultMsg	= '�����ڵ�Ȯ�� : ����Ȯ�νð�';

		switch ($resData['P_TYPE']){
			case "CARD":
				$card_nm	= $pgCards[$resData['P_FN_CD1']];

				$settlelog	.= 'ī���ҺαⰣ : '.$resData['P_RMESG2'].chr(10);
				$settlelog	.= 'ī��� �ڵ� : '.$resData['P_FN_CD1'].' - '.$card_nm.chr(10);
				$settlelog	.= 'ī�� �߱޻� : '.$resData['P_CARD_ISSUER_CODE'].' - '.$pgBanks[$resData['P_CARD_ISSUER_CODE']].chr(10);
				break;

			case 'BANK':

			break;

			case "VBANK":
				$bank_nm	= $pgBanks[$resData['P_VACT_BANK_CODE']];

				$settlelog	.= ' *** ���� ������ �Ϸ� �Ȱ��� �ƴ� ��û �Ϸ��� ***'.chr(10);
				$settlelog	.= '�Աݰ��¹�ȣ : '.$resData['P_VACT_NUM'].chr(10);
				$settlelog	.= '�Ա������ڵ� : '.$resData['P_VACT_BANK_CODE'].' - '.$bank_nm.chr(10);
				$settlelog	.= '�����ָ� : '.$resData['P_VACT_NAME'].chr(10);
				$settlelog	.= '�۱����� : '.$resData['P_VACT_DATE'].chr(10);
				$settlelog	.= '�۱ݽð� : '.$resData['P_VACT_TIME'].chr(10);

				$pgResultMsg	= '�����Ҵ�Ϸ� : ��ûȮ�νð�';
				break;

			case "MOBILE":
				$settlelog	.= '�޴�����Ż� : '.$resData['P_HPP_CORP'].chr(10);
				break;
		}

		$settlelog	= '===================================================='.chr(10).$pgResultMsg.'('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);

		if (forge_order_check($ordno, $resData['P_AMT']) === false) {
			include SHOPROOT.'/conf/pg.inipay.php';
			include SHOPROOT.'/order/card/inipay/libs/INILib.php';
			$inipay	= new INIpay50;
			$inipay->SetField('inipayhome',	SHOPROOT.'/order/card/inipay');	// �̴����� Ȩ���͸�
			$inipay->SetField('type', 'cancel');	// ���� (���� ���� �Ұ�)
			$inipay->SetField('debug', 'true');	// �α׸��('true'�� �����ϸ� �󼼷αװ� ������.)
			$inipay->SetField('mid', $pg['id']);	// �������̵�
			$inipay->SetField('admin', '1111');	// ���Ī ���Ű Ű�н�����
			$inipay->SetField('tid', $resData['P_TID']);	// ����� �ŷ��� �ŷ����̵�
			$inipay->SetField('cancelmsg', '�ŷ��ݾ� ������ ������ ���� �ڵ����');	// ��һ���
			$inipay->startAction();
			$getPgResult = false;
			$settlelog = '----------------------------------------'.
				PHP_EOL.'������� : �ŷ��ݾ� ������ ������ ���� �ڵ����'.
				PHP_EOL.'----------------------------------------'.
				PHP_EOL.$settlelog;
		}
	} else {
		// PG ���
		$getPgResult	= false;

		$settlelog	= '===================================================='.chr(10).'��������Ȯ�� : ����Ȯ�νð�('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
	}

	//--- �ߺ� ���� üũ
	$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
	if($oData['step'] > 0 || $oData['vAccount'] != '' || !strcmp($resData['P_STATUS'],"1179")){		// �ߺ�����

		// �α� ����
		$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		go($cfgMobileShop['mobileShopRootDir']."/ord/order_end.php?ordno=$ordno&card_nm=$card_nm","parent");
		exit();

	}

	//--- ���� ������ ��� ó��
	if( $getPgResult === true ){

		$query = "
		SELECT * FROM
			".GD_ORDER." a
			LEFT JOIN ".GD_LIST_BANK." b on a.bankAccount = b.sno
		WHERE
			a.ordno='$ordno'
		";
		$data = $db->fetch($query);

		// ���� ���� ����
		$step = 1;
		$qrc1 = "cyn='y', cdt=now(),";
		$qrc2 = "cyn='y',";

		// ������� ������ �������� ����
		if ($resData['P_TYPE']=="VBANK"){
			$vAccount = $bank_nm." ".$resData['P_VACT_NUM']." ".$resData['P_VACT_NAME'];
			$step = 0; $qrc1 = $qrc2 = "";
		}

		// �ǵ���Ÿ ����
		$db->query("
		UPDATE ".GD_ORDER." set $qrc1
			step		= '$step',
			step2		= '',
			escrowyn	= '$escrowyn',
			escrowno	= '$escrowno',
			vAccount	= '$vAccount',
			settlelog	= concat(ifnull(settlelog,''),'$settlelog'),
			cardtno		= '".$resData['P_TID']."'
		WHERE ordno='$ordno'"
		);
		$db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$ordno'");

		// �ֹ��α� ����
		orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

		// ��� ó��
		setStock($ordno);

		// ��ǰ���Խ� ������ ���
		if ($sess[m_no] && $data[emoney]){
			setEmoney($sess[m_no],-$data[emoney],"��ǰ���Խ� ������ ���� ���",$ordno);
		}

		### �ֹ�Ȯ�θ���
		if(function_exists('getMailOrderData')){
			sendMailCase($data['email'],0,getMailOrderData($ordno));
		}

		// SMS ���� ����
		$dataSms = $data;

		if ($resData['P_TYPE']!="VBANK"){
			sendMailCase($data[email],1,$data);			### �Ա�Ȯ�θ���
			sendSmsCase('incash',$data[mobileOrder]);	### �Ա�Ȯ��SMS
		} else {
			sendSmsCase('order',$data[mobileOrder]);	### �ֹ�Ȯ��SMS
		}

		go($cfgMobileShop['mobileShopRootDir']."/ord/order_end.php?ordno=$ordno&card_nm=$card_nm","parent");

	} else {		// ī����� ����
		$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog'),cardtno='".$resData['P_TID']."' where ordno='$ordno'");
		$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno'");

		// ���̹� ���ϸ��� ���� ���� ��� API ȣ��
		if ($naverNcash->useyn == 'Y') $naverNcash->payment_approval_cancel($ordno);

		go($cfgMobileShop['mobileShopRootDir']."/ord/order_fail.php?ordno=$ordno","parent");
	}
}
else
{
	$ordno = $_GET['ordno'];

	// ���̹� ���ϸ��� ���� ���� ��� API ȣ��
	if ($naverNcash->useyn == 'Y') $naverNcash->payment_approval_cancel($ordno);

	msg($_POST['P_RMESG1']);
	go($cfgMobileShop['mobileShopRootDir']."/ord/order_fail.php?ordno=$ordno","parent");
}
?>