<?php
include "../lib.php";

foreach(Clib_Application::request()->get('sort') as $sno => $sort) {
	$query = "
	update gd_goods_option set go_sort = $sort where sno = $sno
	";
	$db->query($query);
}

echo '
<script type="text/javascript">
parent.nsAdminForm.dialog.close();
</script>
';
