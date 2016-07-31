<?
include dirname(__FILE__) . "/../_shopTouch_header.php"; 
@include $shopRootDir . "/lib/page.class.php";

# 압축코드 정의
$summary_search = array();
$summary_search[] = "/__shopname__/is";			# 쇼핑몰이름
$summary_search[] = "/__shopdomain__/is";		# 쇼핑몰주소
$summary_search[] = "/__shopcpaddr__/is";		# 사업장주소
$summary_search[] = "/__shopcoprnum__/is";		# 사업자등록번호
$summary_search[] = "/__shopcpmallceo__/is";	# 쇼핑몰 대표
$summary_search[] = "/__shopcpmanager__/is";	# 개인정보관리자
$summary_search[] = "/__shoptel__/is";			# 쇼핑몰 전화
$summary_search[] = "/__shopfax__/is";			# 쇼핑몰 팩스
$summary_search[] = "/__shopmail__/is";			# 쇼핑몰 이메일

$summary_replace = array();
$summary_replace[] = $cfg["shopName"];			# 쇼핑몰이름
$summary_replace[] = $cfg["shopUrl"];			# 쇼핑몰주소
$summary_replace[] = $cfg["address"];			# 사업장주소
$summary_replace[] = $cfg["compSerial"];		# 사업자등록번호
$summary_replace[] = $cfg["ceoName"];			# 쇼핑몰 대표
$summary_replace[] = $cfg["adminName"];			# 개인정보관리자
$summary_replace[] = $cfg["compPhone"];			# 쇼핑몰 전화
$summary_replace[] = $cfg["compFax"];			# 쇼핑몰 팩스
$summary_replace[] = $cfg["adminEmail"];		# 쇼핑몰 이메일

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

### 템플릿 출력
$tpl->print_('tpl');

?>