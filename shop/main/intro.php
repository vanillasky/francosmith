<?

include "../_header.php";

if ( $cfg['introUseYN'] != 'Y' ){ // 인트로 미사용
	header("location:index.php");
}

$tpl->print_('tpl');
?>