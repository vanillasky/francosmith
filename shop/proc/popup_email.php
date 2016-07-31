<?
$noDemoMsg = 1;
include "../_header.php";

// 개인정보수집 및 이용에 대한 안내
$termsPolicyCollection4 = getTermsGuideContents('terms', 'termsPolicyCollection4');
$tpl->assign('termsPolicyCollection4', $termsPolicyCollection4);
$tpl->print_('tpl');

?>