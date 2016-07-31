<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
require_once("../../lib/upload.lib.php");
$qfile = new qfile();

$mode = ($_POST[mode]) ? $_POST[mode] : $_GET[mode];
unset($_POST[mode]); unset($_POST[x]); unset($_POST[y]);

switch ($mode){

	case "inipayKeyCopy":
		$inipayKeyName	= $_POST['inicisKeyName'];
		$inipayKeyFiles	= array('keypass.enc','mcert.pem','mpriv.pem');

		$inipayConfFile	= "../../conf/pg.inipay.php";
		$inipayKeyDir	= "../../order/card/inipay/key/".$inipayKeyName;
		$inipayMobileConfFile	= "../../conf/pg_mobile.inipay.php";

		$inicisConfFile	= "../../conf/pg.inicis.php";
		$inicisKeyDir	= "../../order/card/inicis/key/".$inipayKeyName;
		$inicisMobileConfFile	= "../../conf/pg_mobile.inicis.php";

		// TX5�� Ű���� ���� �� Űȭ�� ����
		if ( is_dir($inipayKeyDir) === false || is_file($inipayKeyDir.'/'.$inipayKeyFiles[0]) === false || is_file($inipayConfFile) === false) {
			mkdir($inipayKeyDir,0707);
			@chmod($inipayKeyDir,0707);
			foreach($inipayKeyFiles as $val) {
				copy($inicisKeyDir.'/'.$val, $inipayKeyDir.'/'.$val);
				@chmod($inipayKeyDir.'/'.$val,0707);
			}
		}

		// ���� ȭ�� ���� (������ inipay TX4 ���� �״�� �������)
		if (is_file($inicisConfFile) && is_file($inipayConfFile) === false) {
			copy($inicisConfFile, $inipayConfFile);
			@chmod($inipayConfFile,0707);
		}

		// ����ϼ� ���� ȭ�� ���� (������ inipay TX4 ���� �״�� �������)
		if (is_file($inicisMobileConfFile) && is_file($inipayMobileConfFile) === false) {
			copy($inicisMobileConfFile, $inipayMobileConfFile);
			@chmod($inipayMobileConfFile,0707);
		}

		// ����ũ�� Űȭ�� ����
		include "../../conf/pg.escrow.php";
		if (is_dir($dir.$escrow['id']) === false) {
			$inipayKeyDir	= "../../order/card/inipay/key/".$escrow['id'];
			$inicisKeyDir	= "../../order/card/inicis/key/".$escrow['id'];

			// TX5�� Ű���� ���� �� Űȭ�� ����
			if ( is_dir($inipayKeyDir) === false || is_file($inipayKeyDir.'/'.$inipayKeyFiles[0]) === false) {
				mkdir($inipayKeyDir,0707);
				@chmod($inipayKeyDir,0707);
				foreach($inipayKeyFiles as $val) {
					copy($inicisKeyDir.'/'.$val, $inipayKeyDir.'/'.$val);
					@chmod($inipayKeyDir.'/'.$val,0707);
				}
			}
		}

		exit();
		break;
	case "allatCopy":

		$allatbasicConfFile	= "../../conf/pg.allatbasic.php";

		$allatConfFile	= "../../conf/pg.allat.php";

		// ���� ȭ�� ���� (������ All@Pay��Plus ���� �״�� �������)
		if (is_file($allatConfFile) && is_file($allatbasicConfFile) === false) {
			copy($allatConfFile, $allatbasicConfFile);
			@chmod($allatbasicConfFile,0707);
		}
		exit();
		break;
	case "bank":
		include "../../conf/config.pay.php";
		$set = (array)$set;
		$set = array_map('strip_slashes',$set);
		$set = array_map('add_slashes',$set);

		if($_POST['set']['use']['a'])$set['use']['a'] = $_POST['set']['use']['a'];
		else unset($set['use']['a']);
		$qfile->open("../../conf/config.pay.php");
		$qfile->write("<? \n");
		if ($set) foreach ($set as $k=>$v) foreach ($v as $k2=>$v2)$qfile->write("\$set['$k']['$k2'] = '$v2'; \n");
		$qfile->write("?>");
		$qfile->close();
		break;
	case "config":
		//ȸ��Ұ� ����
		$cfgCompany['compIntroduce'] = $_POST['compIntroduce'];
		$cfgCompany['compMap'] = $_POST['compMap'];

		if (!get_magic_quotes_gpc()) $cfgCompany = array_map('addslashes', (array)$cfgCompany);

		$cfgCompanyPath = '../../conf/config.company.php';
		$qfile->open($cfgCompanyPath);
		$qfile->write("<?php \n");
		$qfile->write("\$cfgCompany = array( \n");
		foreach ($cfgCompany as $key => $value) {
			$qfile->write("'$key' => '$value', \n");
		}
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();
		@chmod($cfgCompanyPath,0707);

		unset($_POST['compIntroduce']);
		unset($_POST['compMap']);
		//ȸ��Ұ� ����

		$_POST['customerHour'] = preg_replace("/\r\n/","<br />",$_POST['customerHour']);

		if ($_POST[zipcode]) $_POST[zipcode] = implode("",$_POST[zipcode]);

		include "../../conf/config.php";
		$cfg = (array)$cfg;
		$cfg = array_map("stripslashes",$cfg);
		$cfg = array_map("addslashes",$cfg);
		$cfg = array_merge($cfg,(array)$_POST);

		$qfile->open("../../conf/config.php");
		$qfile->write("<? \n");
		$qfile->write("\$cfg = array( \n");
		foreach ($cfg as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();

		break;

	case "delivery":

		$dmode = 1;
		include "setAreaName.inc.php";

		### �⺻�����å
		$set2['delivery']['default']	= &$_POST['default'];
		$set2['delivery']['default_msg']	= &$_POST['default_msg'];
		$set2['delivery']['free']	= &$_POST['free'];
		$set2['delivery']['basis']	= &$_POST['basis'];
		$set2['delivery']['deliveryType']	= &$_POST['deliveryType'];
		$set2['delivery']['freeDelivery']	= &$_POST['freeDelivery'];
		$set2['delivery']['goodsDelivery']	= &$_POST['goodsDelivery'];
		$set2['delivery']['deliverynm']	= &$_POST['deliverynm'];
		$set2['delivery']['deliveryOrder']	= &$_POST['deliveryOrder'];

		### ������ ��ۺ�
		$set2['delivery']['area_deli_type']	= $_POST['area_deli_type'];
		$set2['delivery']['overAdd']	= implode("|",$_POST['overAdd']);
		$set2['delivery']['overAddZip']	= implode("|",$_POST['overAddZip']);
		$set2['delivery']['overZipcode'] = implode("|",$overZipcode);
		$set2['delivery']['areaZip1'] = implode("|",$_POST['areaZip1']);
		$set2['delivery']['areaZip2'] = implode("|",$_POST['areaZip2']);

		// ������ �� ������ �߰� ��ۺ�
		if (isset($_POST['add_extra_fee_free']) === true) {
			$set2['delivery']['add_extra_fee_free']				= (int)$_POST['add_extra_fee_free'];			// ������ ��ǰ �ֹ���
		}
		if (isset($_POST['add_extra_fee_basic']) === true) {
			$set2['delivery']['add_extra_fee_basic']			= (int)$_POST['add_extra_fee_basic'];			// �⺻ �����å�� ���� ���Ǻ� ������ ���
		}
		if (isset($_POST['add_extra_fee_memberGroup']) === true) {
			$set2['delivery']['add_extra_fee_memberGroup']		= (int)$_POST['add_extra_fee_memberGroup'];		// ȸ�� �׷� ���ÿ� ���� ��ۺ� ������ ���
		}

		// ������ �߰� ��ۺ� ���� �ΰ� ����
		if (isset($_POST['add_extra_fee_duplicate_each']) === true) {
			$set2['delivery']['add_extra_fee_duplicate_each']		= (int)$_POST['add_extra_fee_duplicate_each'];		// ������ۻ�ǰ �ֹ��� (���̻� ������� ����)
		}
		if (isset($_POST['add_extra_fee_duplicate_free']) === true) {
			$set2['delivery']['add_extra_fee_duplicate_free']		= (int)$_POST['add_extra_fee_duplicate_free'];		// ������ ��ǰ �ֹ���
		}
		if (isset($_POST['add_extra_fee_duplicate_fixEach']) === true) {
			$set2['delivery']['add_extra_fee_duplicate_fixEach']	= (int)$_POST['add_extra_fee_duplicate_fixEach'];	// ���� ��ۺ� ��ǰ �ֹ���
		}

		include "../../conf/config.pay.php";
		$set = (array)$set;
		$set = array_map('strip_slashes',$set);
		$set = array_map('add_slashes',$set);
		$old_set = $set;

		### �߰� �����å
		$tmp = explode('|',$set['r_delivery']);
		foreach($tmp as $v)unset($v);
		unset($set['r_delivery']);
		if($_POST['r_delivery']){
			$set['r_delivery']['title'] = implode('|',$_POST['r_delivery']);
			for($i=0;$i<count($_POST[r_delivery]);$i++){
				$set[$_POST['r_delivery'][$i]]['r_free'] = $_POST['r_free'][$i];
				$set[$_POST['r_delivery'][$i]]['r_deliType'] = $_POST['r_deliType'][$i];
				$set[$_POST['r_delivery'][$i]]['r_default'] = $_POST['r_default'][$i];
				$set[$_POST['r_delivery'][$i]]['r_default_msg'] = $_POST['r_default_msg'][$i];
			}
		}

		$set = array_merge($set,$set2);

		### �⺻ �ù��
		if ($_POST[delivery]){
			$set['delivery']['defaultDelivery'] = $_POST['delivery']['0'];
		}

		$qfile->open("../../conf/config.pay.php");
		$qfile->write("<? \n");
		foreach ($set as $k=>$v){
			foreach ($v as $k2=>$v2){
				$qfile->write("\$set['$k']['$k2'] = '$v2'; \n");
			}
		}
		$qfile->write("?>");
		$qfile->close();

		### �ù��/������� ����
		$db->query("update ".GD_LIST_DELIVERY." set useyn='n'");
		if ($_POST[delivery]) foreach ($_POST[delivery] as $v) $db->query("update ".GD_LIST_DELIVERY." set useyn='y' where deliveryno='$v'");

		### ������ �߰� ��ۺ� ���� ����� ����� ��� �̷±��
		if (($old_set['delivery']['area_deli_type'] != '2' && $set2['delivery']['area_deli_type'] == '2')
			|| ($old_set['delivery']['area_deli_type'] == '2' && $set2['delivery']['area_deli_type'] != '2')) {
			$adcLog = Core::loader('areaDeliveryChangeLog');
			$res = $adcLog->sendChangeLog($set2['delivery']['area_deli_type'], $old_set['delivery']['area_deli_type']);
		}
		break;

	case "emoney":

		$_POST['max'] = $_POST['max'][$_POST[k_max]];
		if ($_POST[k_max]) $_POST['max'] .= "%";
		unset($_POST[k_max]);
		$_POST['goods_emoney'] =  $_POST['goods_emoney'][$_POST['chk_goods_emoney']];
		$tmp['emoney'] = &$_POST;

		include "../../conf/config.pay.php";
		$set = (array)$set;
		$set = array_map('strip_slashes',$set);
		$set = array_map('add_slashes',$set);
		$set = array_merge($set,$tmp);

		$qfile->open("../../conf/config.pay.php");
		$qfile->write("<? \n");
		foreach ($set as $k=>$v) foreach ($v as $k2=>$v2) $qfile->write("\$set['$k']['$k2'] = '$v2'; \n");
		$qfile->write("?>");
		$qfile->close();

		break;

	case "registerDelivery":

		$db->query("insert into ".GD_LIST_DELIVERY." set deliverycomp='$_POST[deliverycomp]', deliveryurl='$_POST[deliveryurl]'");
		popupReload();
		break;

	case "modifyDelivery":

		$db->query("update ".GD_LIST_DELIVERY." set deliverycomp='$_POST[deliverycomp]', deliveryurl='$_POST[deliveryurl]' where deliveryno='$_POST[deliveryno]'");
		popupReload();
		break;

	case "addBank":

		$db->query("insert into ".GD_LIST_BANK." set bank='$_POST[bank]', account='$_POST[account]', name='$_POST[name]'");
		popupReload();
		break;

	case "modBank":

		$db->query("update ".GD_LIST_BANK." set bank='$_POST[bank]', account='$_POST[account]', name='$_POST[name]' where sno='$_POST[sno]'");
		popupReload();
		break;

	case "delBank":

		$db->query("update ".GD_LIST_BANK." set useyn='n' where sno='$_GET[sno]'");
		break;

	case "cfgemoney":

		include "../../conf/config.php";
		$cfg = (array)$cfg;
		$cfg = array_map("stripslashes",$cfg);
		$cfg = array_map("addslashes",$cfg);
		$cfg = array_merge($cfg,(array)$_POST);

		$qfile->open("../../conf/config.php");
		$qfile->write("<? \n");
		$qfile->write("\$cfg = array( \n");
		foreach ($cfg as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write("); \n;");
		$qfile->write("?>");
		$qfile->close();
		popupReload();
		break;

	case "ssl":
		include "../../conf/config.php";
		if($_POST['ssl_type']) $_POST['ssl']=1;
		else $_POST['ssl']=0;

		if($_POST['ssl_type'] == "") { $_POST['ssl_seal'] = "0";  $_POST['free_ssl_seal'] = "0"; }
		if($_POST['ssl_type'] == "free") { $_POST['ssl_seal'] = "0"; }
		if($_POST['ssl_type'] == "godo") { $_POST['free_ssl_seal'] = "0"; }

		$cfg = (array)$cfg;
		$cfg = array_map("stripslashes",$cfg);
		$cfg = array_map("addslashes",$cfg);
		$cfg = array_merge($cfg,(array)$_POST);

		$qfile->open("../../conf/config.php");
		$qfile->write("<? \n");
		$qfile->write("\$cfg = array( \n");
		foreach ($cfg as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write("); \n;");
		$qfile->write("?>");
		$qfile->close();
		break;

	case "admin_ip": //������ IP�������� ����

		if ($_POST['ip_permit'] == "" || ($_POST['ip_permit'] != 0 && $_POST['ip_permit'] != 1) ) { //�������� �� �� Ȥ�� 0,1�� �ƴѰ�� ���â ���
			msg('�߸��� �����Դϴ�.', -1);
		}

		if($_POST['ip_permit'] == "1" && !$_POST['regist_ip']){ // ������IP���������� ��������� �����ϰ� IP�� ������� �ʾ����� ���â ���
			msg('IP�� ����� �ּ���.', -1);
		}

		$ip_permit = $_POST['ip_permit'];

		$set_ip_write = "<? \n";
		$set_ip_write .= "\$set_ip_permit = '$ip_permit'; \n";

		if ($_POST['ip_permit'] == "1") {
			$set_ip = array();

			if (is_array($_POST['regist_ip'])){
				$_POST['regist_ip'] = array_filter($_POST['regist_ip']); //�迭�� �� ����.
				$_POST['regist_ip'] = array_unique($_POST['regist_ip']); //�ߺ�IP����

				$i=0;
				foreach($_POST['regist_ip'] as $k=>$v) {

					if(!$v=trim($v)){
						continue;
					}
					$set_ip[] = $v;
					$i++;
				}
			}

			$set_ip_write .= "\$set_regist_ip = array( \n";

			foreach ($set_ip as $k=>$v) {
				$set_ip_write .= "'$k' => '$v', \n";
			}
			$set_ip_write .= "); \n;";
		}
		$set_ip_write .= "?>";

		$qfile->open("../../conf/config.admin_access_ip.php");
		$qfile->write($set_ip_write);
		$qfile->close();
		@chmod("../../conf/config.admin_access_ip.php",0707);
		break;

	case "memo":
		if($_POST['miniMemo']==null) $_POST['miniMemo'] = ' ';
		$qfile->open("../../conf/mini_memo.php");
		$qfile->write(stripslashes($_POST['miniMemo']));
		$qfile->close();
		@chmod("../../conf/mini_memo.php",0707);

		echo "<script>parent.location.reload();</script>";
		exit;
		break;

	case "orderSet" :
		include "../../conf/config.php";

		$arr = array(
			'stepStock' => $_POST['stepStock'],
			'autoCancel' => $_POST['autoCancel'],
			'autoCancelUnit' => $_POST['autoCancelUnit'],
			'autoCancelFail' => $_POST['autoCancelFail'],
			'RecoverCoupon' => $_POST['RecoverCoupon'],
			'orderPeriod' => $_POST['orderPeriod'],
			'orderPageNum' => $_POST['orderPageNum'],
			'autoCancelRecoverStock' => $_POST['autoCancelRecoverStock'],
			'autoCancelRecoverReserve' => $_POST['autoCancelRecoverReserve'],
			'autoCancelRecoverCoupon' => $_POST['autoCancelRecoverCoupon'],
			'orderDoubleCheck' => $_POST['orderDoubleCheck'],
			'reOrder' => $_POST['reOrder']
		);
		$cfg = (array)$cfg;
		$cfg = array_map("stripslashes",$cfg);
		$cfg = array_map("addslashes",$cfg);
		$cfg = array_merge($cfg,$arr);
		$qfile->open("../../conf/config.php");
		$qfile->write("<? \n");
		$qfile->write("\$cfg = array( \n");
		foreach ($cfg as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();

		unset($set);

		include "../../conf/config.pay.php";
		$set = (array)$set;
		$set = array_map('strip_slashes',$set);
		$set = array_map('add_slashes',$set);
		$set['delivery']['basis']	= &$_POST['basis'];
		$qfile->open("../../conf/config.pay.php");
		$qfile->write("<? \n");
		foreach ($set as $k=>$v) foreach ($v as $k2=>$v2){
			$qfile->write("\$set['$k']['$k2'] = '$v2'; \n");
		}


		$qfile->write("?>");
		$qfile->close();
	break;

	case "cartSet" :
		include '../../conf/config.cart.php';
		
		// �ΰ� ����
		if ($_POST['sealDel'] === 'Y') {
				unlink('../..'.$cartCfg['estimateSeal']);
				$cartCfg['estimateSeal'] = '';
			}

		// �ΰ� �̹���
		if ($_FILES['seal']['name'] && $_FILES['seal']['error'] === UPLOAD_ERR_OK) {	// UPLOAD_ERR_OK = 0
			$file = $_FILES['seal'];
			$_ext = strtolower(array_pop(explode('.',$file['name'])));
			$path = '/data/goods/icon/estimateSeal.'.$_ext;
			if ($file['size'] > 0) {
				move_uploaded_file($file['tmp_name'], '../../'.$path);
			}
		}
		else {
			$path = $cartCfg['estimateSeal'];
		}

		$cartVal = array(
			'keepPeriod' =>($_POST['keepPeriodYn']=='Y' || is_null($_POST['keepPeriodYn']))?"0":$_POST['keepPeriod'],
			'runoutDel' => $_POST['runoutDel'],
			'redirectType' => $_POST['redirectType'],
			'estimateUse' => $_POST['estimateUse'],
			'estimateMessage' => $_POST['estimateMessage'],
			'estimateSeal' => $path
		);

		if (!get_magic_quotes_gpc()) $cartVal['estimateMessage'] = addslashes($cartVal['estimateMessage']);

		$qfile->open("../../conf/config.cart.php");
		$qfile->write("<? \n");
		$qfile->write("\$cartCfg = array( \n");
		foreach ($cartVal as $k=>$v){
			if($v!='')$qfile->write("'$k' => '$v', \n");
		}
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();
		@chmod("../../conf/config.cart.php",0707);

	break;

	// ���/�������� ����
	case "terms" :

		if ($_POST['delConsentSno']){
			$delSnoArray = array_filter(explode(',', $_POST['delConsentSno']));

			foreach($delSnoArray as $sno){
				$query = "DELETE FROM ".GD_CONSENT." WHERE sno='".$sno."'";
				$db->query($query);
			}
		}

		foreach($_POST['consentSno'] as $key => $value){
			if (!get_magic_quotes_gpc()){
				$_POST[title][$key] = addslashes($_POST[title][$key]);
				$_POST[termsContent][$key] = addslashes($_POST[termsContent][$key]);
			}

			if ($value){
				$query = "UPDATE ".GD_CONSENT." SET title='".$_POST[title][$key]."', useyn='".$_POST[useyn][$key]."', requiredyn='".$_POST[requiredyn][$key]."', termsContent='".$_POST[termsContent][$key]."' WHERE sno='".$value."'";
			} else {
				$query = "INSERT INTO ".GD_CONSENT." SET title='".$_POST[title][$key]."', useyn='".$_POST[useyn][$key]."', requiredyn='".$_POST[requiredyn][$key]."', termsContent='".$_POST[termsContent][$key]."', regdt=now()";
			}
			$db->query($query);
		}

		if (get_magic_quotes_gpc()) $_POST = array_map('stripslashes', $_POST);
		$saveFilePath	= dirname(__FILE__) . '/../../conf/terms';
		if(!is_dir($saveFilePath)){
			mkdir($saveFilePath);
			@chmod($saveFilePath,0707);
		}

		//��������
		$termsFileName	= array(
			'termsAgreement'		,
			'termsPolicyCollection1',
			'termsPolicyCollection2',
			'termsPolicyCollection3',
			'termsPolicyCollection4',
			'termsThirdPerson'		,
			'termsEntrust'
		);

		foreach($termsFileName as $key => $fileName){
			if(!$fileName) continue;
			$fullFileName = $saveFilePath . '/' . $fileName . '.txt';

			$qfile->open($fullFileName);
			$qfile->write($_POST[$fileName]);
			$qfile->close();

			@chmod($fullFileName,0707);
		}

		//config ���� ����
		$configSaveFile = dirname(__FILE__) . "/../../conf/config.php";
		@include_once $configSaveFile;

		$cfg['private2YN'] = $_POST['private2YN'];
		$cfg['private3YN'] = $_POST['private3YN'];

		$cfg = array_map("stripslashes",$cfg);
		$cfg = array_map("addslashes",$cfg);

		$qfile->open($configSaveFile);
		$qfile->write("<?\n\n" );
		$qfile->write("\$cfg = array(\n" );
		foreach ( $cfg as $k => $v ) {
			if ( $v === true ) $qfile->write("'$k'\t\t\t=> true,\n" );
			else if ( $v === false ) $qfile->write("'$k'\t\t\t=> false,\n" );
			else $qfile->write("'$k'\t\t\t=> '$v',\n" );
		}
		$qfile->write(");\n\n" );
		$qfile->write("?>" );
		$qfile->close();
		@chMod( $configSaveFile, 0757 );

		msg('���������� ����Ǿ����ϴ�.');
		popupReload();
		exit;
	break;

	// �̿�/Ż��ȳ� ����
	case "guide" :

		if (get_magic_quotes_gpc()) $_POST = array_map('stripslashes', $_POST);
		$saveFilePath	= dirname(__FILE__) . '/../../conf/guide';
		if(!is_dir($saveFilePath)){
			mkdir($saveFilePath);
			@chmod($saveFilePath,0707);
		}
		$guideTypeName	= array('guideOperate', 'guideSecede');

		foreach($guideTypeName as $key => $fileName){
			if(!$fileName) continue;
			$fullFileName = $saveFilePath . '/' . $fileName . '.txt';

			$qfile->open($fullFileName);
			$qfile->write($_POST[$fileName]);
			$qfile->close();

			@chmod($fullFileName,0707);
		}

		msg('���������� ����Ǿ����ϴ�.');
		popupReload();
		exit;
	break;

	//�������� ��ȿ�Ⱓ�� ����
	case 'dormantConfig':
		try{
			$executeResult = false;

			$dormant = Core::loader('dormant');
			if(!$dormant) {
				throw new Exception("dormant class file �� Ȯ�ε��� �ʽ��ϴ�.\n�� �����Ϳ� �����Ͽ� �ּ���.");
			}

			register_shutdown_function(array($dormant, 'shutdownLog'), 'dormantAll');

			//�������� ��ȿ�Ⱓ�� ��� ��� ����
			$executeResult = $dormant->executeDormantAll();
			if($executeResult === false){
				throw new Exception("�������� ��ȿ�Ⱓ�� ��� ��� ������ �����Ͽ����ϴ�.\n��� �� �ٽ� �ѹ� �õ��Ͽ� �ּ���.");
			}

			echo "SUCCESS|���������� �����Ǿ����ϴ�.";
		}
		catch (Exception $e){
			echo 'ERROR|' . $e->getMessage();
		}
		exit;
	break;
}

go($_SERVER[HTTP_REFERER]);

?>