<?
# 합동택배 http://www.hdexp.co.kr/parcel/order_result_t.asp?stype=1&p_item=

$out = split_betweenStr($out,'<div class="order_box">','<!--//.order_box-->');
$out = '<div class="order_box">' . $out[0];
$out = str_replace('<a href="javascript:printIt(document.getElementById(\'printme\').innerHTML)"><img src="/admin/images/print1.gif" border=0 align=\'absmiddle\'></a>', '', $out);
$out = str_replace('<input type="button" name="" value="이전화면" class="btn_black_type" onclick="javascript:location.href=\'order_status_t.asp\';">', '', $out);
?>
<link rel="stylesheet" href="http://www.hdexp.co.kr/asset/css/style.css" type="text/css" media="screen" />
<div><?=$out;?></div>