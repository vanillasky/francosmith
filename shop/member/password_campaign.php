<?

include "../_header.php";

### 회원인증여부
chkMember();

$tpl->assign('ori_returnUrl', $_GET['ori_returnUrl']);
$tpl->print_('tpl');

?>