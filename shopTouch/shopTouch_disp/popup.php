<?
include dirname(__FILE__) . "/../_shopTouch_header.php"; 
@include $shopRootDir . "/lib/page.class.php";


### popup
$popup_query = $db->_query_print('SELECT * FROM '.GD_SHOPTOUCH_DISPLAY.' WHERE use_display=[i]', 1);

$res_popup = $db->_select($popup_query);
$row_popup= $res_popup[0];

$popup = Array();

if($row_popup['image_up'] == '1') {
	$popup['img'] = '../../shop/data/shoptouch/popup/'.$row_popup['main_img'];
}
else {
	$popup['img'] = $row['main_img'];
}

if($row_popup['link_type'] == '1') {	//상품리스트
	$popup['link_url'] = 'vumall://vercoop.com/move_menu?menu_idx='.$row_popup['category'];
}
else if($row_popup['link_type'] == '2') {	//상품상세
	$popup['link_url'] = 'vumall://vercoop.com/select_goods?goodsno='.$row_popup['goodsno'];
}
else {	// url
	if(!preg_match("/^http(s)?:\/\//", $row_popup['link_url'])) {
		$row_popup['link_url'] = 'http://'.$row_popup['link_url'];
	}
	
	
	$popup['link_url'] = 'vumall://vercoop.com/popup_page?url='.$row_popup['link_url'].'&mode=browser';
}

$tpl->assign('popup', $popup);

### 템플릿 출력
$tpl->print_('tpl');

?>