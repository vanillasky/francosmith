<?
include dirname(__FILE__) . "/../_shopTouch_header.php"; 
@include $shopRootDir . "/lib/page.class.php";

# �����ڵ� ����
$summary_search = array();
$summary_search[] = "/__shopname__/is";			# ���θ��̸�
$summary_search[] = "/__shopdomain__/is";		# ���θ��ּ�
$summary_search[] = "/__shopcpaddr__/is";		# ������ּ�
$summary_search[] = "/__shopcoprnum__/is";		# ����ڵ�Ϲ�ȣ
$summary_search[] = "/__shopcpmallceo__/is";	# ���θ� ��ǥ
$summary_search[] = "/__shopcpmanager__/is";	# ��������������
$summary_search[] = "/__shoptel__/is";			# ���θ� ��ȭ
$summary_search[] = "/__shopfax__/is";			# ���θ� �ѽ�
$summary_search[] = "/__shopmail__/is";			# ���θ� �̸���

$summary_replace = array();
$summary_replace[] = $cfg["shopName"];			# ���θ��̸�
$summary_replace[] = $cfg["shopUrl"];			# ���θ��ּ�
$summary_replace[] = $cfg["address"];			# ������ּ�
$summary_replace[] = $cfg["compSerial"];		# ����ڵ�Ϲ�ȣ
$summary_replace[] = $cfg["ceoName"];			# ���θ� ��ǥ
$summary_replace[] = $cfg["adminName"];			# ��������������
$summary_replace[] = $cfg["compPhone"];			# ���θ� ��ȭ
$summary_replace[] = $cfg["compFax"];			# ���θ� �ѽ�
$summary_replace[] = $cfg["adminEmail"];		# ���θ� �̸���

$company_info = Array();
$company_info['comp_name'] = $cfg['compName'];
$company_info['comp_addr'] = $cfg['address'];
$company_info['comp_serial'] = $cfg['compSerial'];
$company_info['comp_orderserial'] = $cfg['orderSerial'];
$company_info['comp_ceo'] = $cfg['ceoName'];
$company_info['comp_admin'] = $cfg['adminName'];
$company_info['comp_tel'] = $cfg['compPhone'];
$company_info['comp_fax'] = $cfg['compFax'];

$skin_dir = $cfg['tplSkin'];

$agreement_path = '../..'.$cfg['rootDir'].'/data/skin/'.$cfg['tplSkin'].'/proc/_agreement.txt';

$fh = @fopen($agreement_path, 'r');
$agreement_txt = fread($fh, @filesize($agreement_path));
@fclose($fh);

$agreement_txt = str_replace("{_cfg['compName']}", $cfg['compName'], str_replace("{_cfg['shopName']}", $cfg['shopName'], str_replace("\n", "<br>", $agreement_txt)));

$tpl->assign('comp_info', $company_info);
$tpl->assign('agreement', $agreement_txt);

### ���ø� ���
$tpl->print_('tpl');

?>