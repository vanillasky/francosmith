<?

include "../_header.php";

if ( $cfg['introUseYN'] != 'Y' ){ // ��Ʈ�� �̻��
	header("location:index.php");
}

$tpl->print_('tpl');
?>