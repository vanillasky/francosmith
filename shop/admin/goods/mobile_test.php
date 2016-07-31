<?php
include "../lib.php";

$img_tmp = "/shop/data/tmp/"; 
$img_list = "/shop/data/goods/"; 
$img_editor_all = "/www/punchto1_godo_co_kr/shop/data/m/editor/"; 
$img_editor = "/shop/data/m/editor/"; 

function img_convert($src,$folder,$sizeX=100,$sizeY=100,$fix=0)
{
	$size	= getimagesize($src);

	switch ($size[2]){
		case 1:	$image	= @ImageCreatefromGif($src); break;
		case 2:	$image	= ImageCreatefromJpeg($src); break;
		case 3:	$image	= ImageCreatefromPng($src);  break;
	}

	if ($fix){
		$gap = abs($size[0]-$size[1]);
		switch ($fix){
			case 1:		# 설정된 크기에 따라 비율을 조정
				$reSize		= ImgSizeSet($src,$sizeX,$sizeY,$size[0],$size[1]);
				$g_width	= 0;
				$g_height	= 0;
				$newSizeX	= $reSize[0];
				$newSizeY	= $reSize[1];
				break;
			case 2:		# 사용되지 않음
				if ($size[0]>$size[1]) $g_width  = $gap / 2;
				else $g_height = $gap / 2;
				$newSizeX	= $sizeX;
				$newSizeY	= $sizeX;
				if ($size[0]>$size[1]) $size[0] = $size[1];
				else $size[1] = $size[0];
				break;
			case 3:		# 사용되지 않음
				if ($size[0]>$size[1]) $g_width  = $gap;
				else $g_height = $gap;
				$newSizeX	= $sizeX;
				$newSizeY	= $sizeX;
				if ($size[0]>$size[1]) $size[0] = $size[1];
				else $size[1] = $size[0];
				break;
			case 4:
				$newSizeX	= $sizeX;
				$newSizeY	= $sizeY;
				break;
		}

		$dst	= ImageCreateTruecolor($newSizeX,$newSizeY);
		Imagecopyresampled($dst,$image,0,0,$g_width,$g_height,$newSizeX,$newSizeY,$size[0],$size[1]);
	} else {
		$width	= $sizeX;
		$height = $size[1] / $size[0] * $sizeX;
		$dst	= ImageCreateTruecolor($width,$height);
		Imagecopyresampled($dst,$image,0,0,0,0,$width,$height,$size[0],$size[1]);
	}
	ImageJpeg($dst,$folder,100);
	ImageDestroy($dst);
	@chmod($folder,0707); // 업로드된 파일 권한 변경
}

## 전달변수의 값은 항상 검증해야 한다. 

if ( preg_match("/[a-z]/i", $_GET['goodsno'] ) ) {
	debug("숫자가 아닌값이 있습니다."); 
	exit; 
}
$goodsno = intval($_GET['goodsno']); 

$goods_data = $db->fetch("select * from ".GD_GOODS." where goodsno=".$goodsno);

debug($goods_data['longdesc']);
$longdesc = $goods_data['longdesc']; 
//$longdesc = "<img id='1' src=\"/../test.jpg\" onerror=\"\" /><Img id='1' src=\"/../test2.jpg\" /><iMg id='1' src=\"/../test3.jpg\" /><IMG id='1' src=\"/../test4.jpg\" /><imG id='1' src=\"/../test5.jpg\" />";
$cnt = preg_match_all("/<img/i", $longdesc, $out, PREG_PATTERN_ORDER); 

$index = 0; 

debug($longdesc);
$new_desc = ""; 
if (count($out) >0 && count($out[0])>0) foreach($out[0] as $v) {
	$index = strpos($longdesc, $v); 
	
	$index = $index + strlen("<img"); 
	$index = strpos($longdesc, "src", $index );
	$index = strpos($longdesc, "=", $index+3 );
	$index = strpos($longdesc, "\"", $index+1 );
	
	$new_desc .= substr($longdesc, 0, $index+1); 
	$longdesc = substr($longdesc, $index+1); 
	
	while (strlen($longdesc) > 0) {
		$index = strpos($longdesc, "\"");
		if ($longdesc[$index-1] != "\\") {
			$img_src = substr($longdesc, 0, $index); 
			## 이미지 변환 처리 후, 새로운 이미지 이름을 만든다.
			$new_img_src = $goods_data['goodsno']."_".time().substr($key,-1,1).".jpg";
			img_convert($img_src, $img_editor_all.$new_img_src, 300);
			##
			$longdesc = substr($longdesc, $index+1); 
			$new_desc .= $img_editor.$new_img_src;
			$new_desc .= "\""; 
			break; 
		}
		else {
			$new_desc .= substr($longdesc, 0, $index+1); 
			$longdesc = substr($longdesc, $index+1); 
		}
	}
}
$new_desc.= $longdesc;
debug($new_desc);
echo $new_desc;

?>

