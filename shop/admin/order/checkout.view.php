<?php
$location = '네이버체크아웃 주문 > 주문상세';
if (isset($_GET['win']))
	include '../_header.popup.php';
else
	include '../_header.php';

include '_form.checkout.php';

if (isset($_GET['win'])) {
	echo '<script>
	linecss();
	table_design_load();
	</script>';
}
else
	include '../_footer.php';

?>