<?
@include "../lib/criteo.class.php";
$mode=$_GET['mode'];

$criteo = new Criteo();

if($mode=='get_list') {
	$arr_goodsno=unserialize(stripslashes($_GET['arr_goodsno']));
	$scripts=$criteo->get_list_scripts($arr_goodsno);
}
else if($mode=='get_detail') {
	$goodsno=$_GET['goodsno'];
	$scripts=$criteo->get_detail_scripts($goodsno);
}
else if($mode=='get_main') {
	$scripts=$criteo->get_main_scripts();
}	
else if($mode=='get_cart') {
	$arr_cart=unserialize(stripslashes($_GET['arr_cart']));
	$scripts=$criteo->get_cart_scripts($arr_cart);
}	
else if($mode=='get_order') {
	$arr_order=unserialize(stripslashes($_GET['arr_order']));
	$scripts=$criteo->get_order_scripts($arr_order);
}
else {
	exit;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title> New Document </title>
<meta name="Generator" content="EditPlus">
<meta name="Author" content="">
<meta name="Keywords" content="">
<meta name="Description" content="">
</head>
<body>
<?=$scripts?>
</body>
</html>