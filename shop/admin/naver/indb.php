<?

include "../lib.php";

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
}

msg("���������� ����Ǿ����ϴ�.");

?>
