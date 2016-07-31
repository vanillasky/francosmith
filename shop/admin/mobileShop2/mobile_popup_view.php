<?
include "../_header.popup.php";

$mpopup_no = $_GET['mpopup_no'];

if($mpopup_no) {
	$popup_query = $db->_query_print('SELECT * FROM '.GD_MOBILEV2_POPUP.' WHERE mpopup_no=[i]', $mpopup_no);
	$res_popup = $db->_select($popup_query);
	$popup_data = $res_popup[0];
}
?>
<div style="width:320px;">
<?
if($popup_data['popup_type'] == '0') {
	$src = "../../data/m/upload_img/".$popup_data['popup_img'];
	
	$size	= getimagesize($src);

	if($size[0] > 320)  $width='320';
	else				$width=$size[0];
	
	echo goodsimg($src,$width,'',1);

} else { 

	echo $popup_data['popup_body'];
}
?>
</div>