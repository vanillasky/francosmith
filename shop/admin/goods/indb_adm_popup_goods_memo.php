<?php
include "../lib.php";

$goods = Clib_Application::getModelClass('goods');
$goods->load(Clib_Application::request()->get('goodsno'));

if (Clib_Application::request()->get('action') == 'save') {
	$goods->setData('memo', Clib_Application::request()->get('memo'));
	$goods->save();
}

echo '
<script type="text/javascript">
parent.nsAdminForm.dialog.close();
</script>
';
