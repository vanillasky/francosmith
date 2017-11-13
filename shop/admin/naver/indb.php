<?

include "../lib.php";
@include '../../lib/naverPartner.class.php';

if(class_exists('NaverCommonInflowScript', false)===false) include dirname(__FILE__).'/../../lib/naverCommonInflowScript.class.php';
$naverCommonInflowScript = new NaverCommonInflowScript();
if($naverCommonInflowScript->isEnabled===false) exit('
<script type="text/javascript">
alert("���̹� ��������Ű�� �����ϼž� �����ϽǼ� �ֽ��ϴ�.");
</script>
');

require_once("../../lib/qfile.class.php");
$qfile = new qfile();

switch ($_POST[mode]){

	case "naver":

		if($_POST['cpaAgreement']!=='true')
		{
			msg('CPA �ֹ������� �����Ͽ��ֽñ� �ٶ��ϴ�.');
			exit;
		}

		// ��ǰ���� ����
		$_POST['partner']['cpaAgreement'] = $_POST['cpaAgreement'];
		$_POST['partner']['unmemberdc'] = ($_POST['inmemberdc'] == 'Y' ? 'N' : 'Y');
		$_POST['partner']['uncoupon'] = ($_POST['incoupon'] == 'Y' ? 'N' : 'Y');
		$_POST['partner']['naver_version'] = $_POST['naver_version'];
		$_POST['partner']['useYn'] = $_POST['useYn'];
		$_POST['partner']['naver_event_common'] = $_POST['naver_event_common'];
		$_POST['partner']['naver_event_goods'] = $_POST['naver_event_goods'];
		$_POST['partner']['auto_create_use'] = $_POST['auto_create_use'];
		$_POST['partner']['auto_excute_time'] = $_POST['auto_excute_time'];

		// ����
		$partner = array();
		@include "../../conf/partner.php";

		if($_POST['partner']['cpaAgreement']==='true')
		{
			if(isset($partner['cpaAgreement'])===false || strlen($partner['cpaAgreement'])<1) $_POST['partner']['cpaAgreementTime'] = date('Y.m.d h:i');
		}
		else
		{
			if(isset($partner['cpaAgreementTime'])) unset($partner['cpaAgreementTime']);
		}

		$partner = array_map("addslashes",array_map("stripslashes",$partner));
		$partner = array_merge($partner,$_POST[partner]);

		$qfile->open("../../conf/partner.php");
		$qfile->write("<? \n");
		$qfile->write("\$partner = array( \n");
		foreach ($partner as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();

		break;
		
		//���̹� ���� ��ǰ ���� ����
	case 'naverShopingGoods' :
		$message = "������ �����Ͽ����ϴ�. �����Ϳ� �����Ͽ� �ּ���.";
		$naver = new naverPartner();
		if(!is_object($naver)){
			msg($message, -1);
			exit;
		}

		if ($_POST['naver_shopping_yn'] == 1) {	// �˻��� ��ǰ
			$_POST['param']['sword'] = iconv("UTF-8","EUC-KR",$_POST['param']['sword']);
			$goodsHelper = Clib_Application::getHelperClass('admin_goods');
			$goodsList = $goodsHelper->getGoodsCollection($_POST['param']);
			$pg = $goodsList->getPaging();

			foreach ($goodsList as $goods) {
				$naver->shoppingGoodsSetting($goods['goodsno'],$_POST['searched']);
			}

			if ($pg->page['total'] == 1) {
				echo 'end';
				exit;
			}

			echo 'ok';
			exit;
		}
		else {	// üũ�� ��ǰ
			foreach($_POST['chk'] as $goods) {
				$naver->shoppingGoodsSetting($goods,$_POST['checked']);
			}
			echo 'end';
			exit;
		}

		break;

	//���̹� ���� ���̱׷��̼�
	case 'naverShoppingMigration' :
		$message = "���̱׷��̼��� �����Ͽ����ϴ�. �����Ϳ� �����Ͽ� �ּ���.";
		$naver = new naverPartner();
		if(!is_object($naver)){
			msg($message, -1);
			exit;
		}

		if ($_POST['category']) {
			$res = $naver->naverShoppingChkSetting($_POST['category'],$_POST['cnt']);
		}
		else {
			$res = $naver->migration($_POST['cnt']);
		}

		if ($res == false) {
			echo 'end';
			exit;
		}
		else {
			echo 'ok';
			exit;
		}

		break;
}

msg("���������� ����Ǿ����ϴ�.");

?>
				