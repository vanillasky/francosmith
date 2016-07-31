<?php
include("../lib.php");

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$ar_use = (array)$_POST['use'];
$major_font = $_POST['major_font'];
$font_list = $db->_select('select * from gd_webfont');

foreach($font_list as $each_font) {
	
	if(is_array($ar_use[$each_font['font_code']]) && count($ar_use[$each_font['font_code']])) {
		$each_use = $ar_use[$each_font['font_code']];
	}
	else {
		$each_use = array();
	}
	$query = $db->_query_print('update gd_webfont set `use`=[s] where `font_no`=[s]',implode(',',$each_use),$each_font['font_no']);

	
	$db->query($query);
}


$config->save('godofont',array(
	'major_font'=>$major_font
));


?>
<script>
	alert('수정되었습니다');
	parent.location.href=parent.location.href;
</script>
