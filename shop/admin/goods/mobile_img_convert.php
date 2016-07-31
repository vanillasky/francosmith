<?php
// @deleted
include "../lib.php";
include SHOPROOT."/conf/config.php";

$img_list_org = SHOPROOT."/data/goods/"; 
$img_list_fullpath = SHOPROOT."/data/m/goods/"; 
$img_list = "/shop/data/m/goods/"; 
$img_editor_fullpath = SHOPROOT."/data/m/editor/"; 
$img_editor = "/shop/data/m/editor/"; 

## <------------- 함수 정의 

function longdesc_img_convert($p_longdesc) 
{
	global  $img_editor_fullpath, $img_editor, $img_count, $img_fail_count; 
	
	$val_longdesc = ""; 
	$val_longdesc.= $p_longdesc; 
	
	$cnt = preg_match_all("/<img/i", $val_longdesc, $out, PREG_PATTERN_ORDER); 
	if ($cnt == 0) return $val_longdesc; 
	
	$index = 0; 
	
	$arrContent = Array(); 
	if (count($out) >0 && count($out[0])>0) foreach($out[0] as $v) {
		##
		$start_index = strpos($val_longdesc, $v); 
		if (!$start_index) 	continue; 
		##
		$end_index = strpos($val_longdesc, ">", $start_index+1); 
		if (!$end_index) {
			$arrContent[] = array('type'=>'NO', 'content'=> substr($val_longdesc, 0, $start_index)); 
			$arrContent[] = array('type'=>'IMG', 'content'=> substr($val_longdesc, $start_index));
			$val_longdesc = "";
		}
		else {
			$arrContent[] = array('type'=>'NO', 'content'=> substr($val_longdesc, 0, $start_index)); 
			$arrContent[] = array('type'=>'IMG', 'content'=> substr($val_longdesc, $start_index, ($end_index-$start_index)+1));
			$val_longdesc = substr($val_longdesc, ($end_index+1)); 
		}
	}
	debug($arrContent);
	
	$arrFinalContent = array(); 
	if (count($arrContent)>0) foreach($arrContent as $value) {
		if ($value['type'] == 'NO') {
			$arrFinalContent[] = $value['content']; 
		}
		else {
			$Ext = 'gif|jpg|jpeg|png';
			$Ext = '(?<=src\=")(?:[^"])*[^"]+\.(?:'. $Ext .')(?=")'.
				"|(?<=src\=')(?:[^'])*[^']+\.(?:". $Ext .")(?=')".
				'|(?<=src\=\\\\")(?:[^"])*[^"]+\.(?:'. $Ext .')(?=\\\\")'.
				"|(?<=src\=\\\\')(?:[^'])*[^']+\.(?:". $Ext .")(?=\\\\')";
			$pattern = '@('. $Ext .')@ix';
			
			unset($split);
			$split = preg_split($pattern, $value['content'], -1, PREG_SPLIT_DELIM_CAPTURE);
			debug($split);
			
			unset($imgurl);
			$imgurl = array(); 
			if (count($split) > 0) foreach($split as $aval) {
				//$aval = "http://../m/data/editor/cjupload/htmledit/child/03121.jpg";
				if ( preg_match("/(?:http:\/\/).*\.(jp[e]?g|gif|png)/i", $aval) ) 
				{
					//if (!preg_match("/datam\/m/", $aval)) {	--> 모바일용 컨버전한 것은 안할때
					$pos = strrpos($aval, "/"); 
					debug($aval."==>".$pos);
					if ($pos) {	
						$new_filename = "m_".substr($aval, ($pos+1)); 
						debug("new_filename ==> ".$new_filename);
						$result = img_convert($aval, $img_editor_fullpath.$new_filename, 300);
						debug("result ==> ".$result);
						if ($result == 1) {
							$img_count++; 
							$imgurl[] = $img_editor.$new_filename; 
						} else {
							$img_fail_count++; 
							$imgurl[] = $aval; 
						}
						
						
					} else {
						## 위치를 못찾는다는 것은 파일이름에 디렉토리 정보가 없다는 뜻.
						$imgurl[] = $aval; 
					}
				}
				else if ( preg_match("/\/.*(jp[e]?g|gif|png)/i", $aval) ) 
				{
					$pos = strrpos($aval,"/"); 
					if (!$pos) {
						## 위치를 못찾는다는 것은 파일이름에 디렉토리 정보가 없다는 뜻.
						$imgurl[] = $aval; 
					} else {				
						$new_filename = "m_".substr($aval, ($pos+1)); 
						$result = img_convert(SHOPROOT."/..".$aval, $img_editor_fullpath.$new_filename, 300);
						if ($result == 1) {
							$img_count++; 
							$imgurl[] = $img_editor.$new_filename; 
						} else {
							$img_fail_count++; 
							$imgurl[] = $aval; 
						}
					}
				}
				else 
				{
					$prop_height = '/height\s*=\s*[\'\"]?[0-9]+(px)?[\'\"]?/';
					$aval = preg_replace($prop_height,"",$aval);
					
					$prop_width = '/width\s*=\s*[\'\"]?[0-9]+(px)?[\'\"]?/';
					$aval = preg_replace($prop_width,"",$aval);

					$imgurl[] = $aval; 
				}
			}
			debug("imgurl = " . implode('', $imgurl));
			$arrFinalContent[] = implode('', $imgurl); 
		}
	}
	$new_desc = implode('', $arrFinalContent);

	return $new_desc; 
}

function img_convert($src,$folder,$sizeX=100,$sizeY=100,$fix=0)
{
	$size	= @getimagesize($src);
	debug($size);
	if (!$size) return 0; 

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
	return 1; 
}


## --------------------> 함수 정의 

debug("start : ".date("Y-m-d h:i:s"));

$all_count =0; 
$img_count =0; 
$goods_res = $db->query("select * from ".GD_GOODS." where goodsno=12 limit 10");

$err_list_img = 0; 
$err_editor_img = 0; 
while ($goods_data = $db->fetch($goods_res)) 
{
	$img_l = $goods_data['img_l']; 
	$longdesc = $goods_data['longdesc']; 

	## LIST 이미지 변경 
	$mimg = $goods_data['goodsno']."_".time().substr($key,-1,1).".jpg";
	$result = img_convert($img_list_org.$img_l, $img_editor_fullpath.$mimg, 50);
	if ($result == 0) 
		$err_list_img = 1; 
		
	$mlongdesc = longdesc_img_convert($longdesc) ;
debug($mlongdesc);
	$all_count ++; 
	echo "<div>".$goods_data['goodsno']."</div>";
	echo "<div style='border:1;'>".$mlongdesc."</div>";
}

debug("start : ".date("Y-m-d h:i:s"));
debug("총 ".$all_count." 개 상품 이미지 처리 완료! --> 이미지변화수:".$img_count);


?>
