<?php
include "../lib.php";

$preset = Clib_Application::getModelClass('goods_option_preset');

if ($sno = Clib_Application::request()->get('sno')) {
	$preset->load($sno);
}

if (Clib_Application::request()->get('action') == 'save') {

	$names = gd_json_decode(Clib_Application::request()->get('name'), true);
	$values = gd_json_decode(Clib_Application::request()->get('value'), true);

	$preset->setData('title', Clib_Application::request()->get('title'));
	$preset->setData('optnm1', (string)$names[0]);
	$preset->setData('optnm2', (string)$names[1]);
	$preset->setData('opt1', str_replace(',','^',$values[0]));
	$preset->setData('opt2', str_replace(',','^',$values[1]));

	if (!$preset->hasLoaded()) {
		$preset->setData('regdt', Core::helper('date')->now());
	}

	$preset->save();

	echo '
	<script type="text/javascript">
	parent.location.replace("../goods/adm_goods_option_preset_list.php");
	</script>
	';


}
else if (Clib_Application::request()->get('action') == 'delete') {
	$preset->delete();

	echo '
	<script type="text/javascript">
	parent.location.reload();
	</script>
	';

}