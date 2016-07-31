<?
include "../_header.popup.php";

if($_GET['goodsno']) {
	$query = $db->_query_print('SELECT img_l, img_m, img_s FROM '.GD_GOODS.' WHERE goodsno=[i]', $_GET['goodsno']);
	$data = $db->_select($query);
	$data = $data[0];
}
else if($_GET['img_url']){
	$path = '';
	$img_nm = $_GET['img_url'];
}
else {
	$img_nm = $_GET['img_nm'];	
	if($_GET['mode'] == 'ipad_popup') {
		$path = '../../data/ipad/popup/';
	}
	else {
		$path = '../../data/ipad/main/';
	}
}
?>
<form name=form>
<div class="title title_top">이미지확인</div>
<div style="border:solid 1px;border-color:#cccccc;text-align:center;">
<?
if($_GET['goodsno']) {
	if($data[img_l]) {
		echo goodsimg($data[img_l],"250,250",'',1);
	}
	else {
		echo goodsimg($data[img_m],"250,250",'',1);
	}
}
else {
?>

	<img src="<?=$path?><?=$img_nm?>" width=250 height=250 onerror=this.src='/shop/data/skin/season3/img/common/noimg_300.gif' />
<?
}
?>
</div>
<div class="button_popup">
<a href="javascript:window.close();"><img src="../img/btn_confirm_s.gif"></a>
</div>
</form>
</body>