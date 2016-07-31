<?
	include "../lib.php";
	require_once("../../lib/qfile.class.php");
	$qfile = new qfile();

	$mode = $_POST[mode];

	$sval = $_POST[sval];
	$i=0;
	@include "../../conf/orderXls.php";


	foreach($sval as $v){
		$tmp = explode('^',$v);
		$arr[$i] = $tmp;
		if(in_array($tmp[1],$_POST[chk])) $arr[$i] = array_merge($tmp,array('3' => 'checked'));
		else $arr[$i] = array_merge($tmp,array('3' => ''));

		$i++;
	}

	$$mode = $arr;
	$qfile->open("../../conf/orderXls.php");

	$content .= "<? \n";
	if($orderXls){
		$content .= "\$orderXls = array( \n";
		foreach ($orderXls as $v) {
			$content .= " array( ";
			foreach ($v as $v2)	$content .= "'$v2',";
			$content = substr($content,0,-1);
			$content .= "), \n";
		}
		$content = substr($content,0,-3);
		$content .= "); \n";
	}
	if($orderGoodsXls){
		$content .= "\$orderGoodsXls = array( \n";
		foreach ($orderGoodsXls as $v) {
			$content .= " array( ";
			foreach ($v as $v2)	$content .= "'$v2',";
			$content = substr($content,0,-1);
			$content .= "), \n";
		}
		$content = substr($content,0,-3);
		$content .= "); \n";
	}
	// Åõµ¥ÀÌ¼¥ ½Ç¹° ¿¢¼¿´Ù¿î Ç×¸ñ
	if($orderTodayGoodsXls){
		$content .= "\$orderTodayGoodsXls = array( \n";
		foreach ($orderTodayGoodsXls as $v) {
			$content .= " array( ";
			foreach ($v as $v2)	$content .= "'$v2',";
			$content = substr($content,0,-1);
			$content .= "), \n";
		}
		$content = substr($content,0,-3);
		$content .= "); \n";
	}
	// Åõµ¥ÀÌ¼¥ ÄíÆù ¿¢¼¿´Ù¿î Ç×¸ñ
	if($orderTodayCouponXls){
		$content .= "\$orderTodayCouponXls = array( \n";
		foreach ($orderTodayCouponXls as $v) {
			$content .= " array( ";
			foreach ($v as $v2)	$content .= "'$v2',";
			$content = substr($content,0,-1);
			$content .= "), \n";
		}
		$content = substr($content,0,-3);
		$content .= "); \n";
	}
	$content .= "?>";

	$qfile->write($content);

	$qfile->close();
	@chmod("../../conf/$mode.php", 0707);
	popupReload();
?>
