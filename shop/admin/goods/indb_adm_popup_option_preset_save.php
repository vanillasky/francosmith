<?php
include "../lib.php";

$names = gd_json_decode(Clib_Application::request()->get('name'), true);
$values = gd_json_decode(Clib_Application::request()->get('value'), true);

$preset = Clib_Application::getModelClass('goods_option_preset');
$preset->setData('title', Clib_Application::request()->get('title'));
$preset->setData('optnm1', (string)$names[0]);
$preset->setData('optnm2', (string)$names[1]);
$preset->setData('opt1', str_replace(',','^',$values[0]));
$preset->setData('opt2', str_replace(',','^',$values[1]));
$preset->setData('regdt', Core::helper('date')->now());
$preset->save();

echo '
<script type="text/javascript">
parent.nsAdminForm.dialog.close();
</script>
';
