<?

include "../_header.php";
@include '../conf/config.company.php';

$tpl->assign('compIntroduce', stripslashes($cfgCompany['compIntroduce']));
$tpl->assign('compMap', stripslashes($cfgCompany['compMap']));
$tpl->print_('tpl');

?>