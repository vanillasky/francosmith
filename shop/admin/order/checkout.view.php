<?php
$location = '���̹�üũ�ƿ� �ֹ� > �ֹ���';
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