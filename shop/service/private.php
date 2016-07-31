<?

include "../_header.php";

$termsPolicyCollection1 = getTermsGuideContents('terms', 'termsPolicyCollection1');

$tpl->assign('termsPolicyCollection1', $termsPolicyCollection1);
$tpl->print_('tpl');

?>