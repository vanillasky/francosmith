<?php
/* Print Pc080 Icon Function */

function dataIconPhone($icon){
	global $set,$cfg;
	if(!$set['phone']['pc080_id']){
		@include dirname(__FILE__)."/../../conf/phone.php";
	}
	switch($icon){
		case "0" :
				$file = dirname(__FILE__)."/../../data/skin/".$cfg[tplSkin]."/img/banner/banner_phone.gif";				
				$on = "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/data/skin/".$cfg[tplSkin]."/img/banner/banner_phone.gif";
				$off = "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/data/skin/".$cfg[tplSkin]."/img/banner/banner_phone_off.gif";
				
			break;
		case "1" :
				$file = dirname(__FILE__)."/../../data/skin/".$cfg[tplSkin]."/img/banner/banner_phone1.gif";
				$on = "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/data/skin/".$cfg[tplSkin]."/img/banner/banner_phone1.gif";
				$off = "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/data/skin/".$cfg[tplSkin]."/img/banner/banner_phone1_off.gif";
				
			break;
		case "2" :
				$file = dirname(__FILE__)."/../../data/skin/".$cfg[tplSkin]."/img/banner/banner_phone2.gif";
				$on = "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/data/skin/".$cfg[tplSkin]."/img/banner/banner_phone2.gif";
				$off = "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/data/skin/".$cfg[tplSkin]."/img/banner/banner_phone2_off.gif";
				
			break;
	}
	
	if( file_exists($file) ) $size = getimagesize($file);
	if($size){
		$width = $size[0];
		$height = $size[1];
	} 
	
	if($set['phone']['pc080_id']){
		$msg = "<iframe frameborder='0' width='$width' height='$height' scrolling='no' src='http://www.pc080.net/wpresence/wpicon.php?value=".$set['phone']['pc080_id']."|".$set['phone']['coop_id']."|".$on."|".$off."'></iframe>";		
		echo($msg);		
	}	
}
?>