<?php

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
		if ($start_index===false) continue;
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

			unset($imgurl);
			$imgurl = array();
			if (count($split) > 0) foreach($split as $aval) {
				//$aval = "http://../m/data/editor/cjupload/htmledit/child/03121.jpg";
				if ( !preg_match("/(?!http:\/\/).*\.(jp[e]?g|gif|png)/i", $aval) &&
				     !preg_match("/data\/m/i", $aval) )
				    {
				    	//기존걸 지우는것
				    }


				if ( preg_match("/(?:http:\/\/).*\.(jp[e]?g|gif|png)/i", $aval) )
				{
					//if (!preg_match("/datam\/m/", $aval)) {	--> 모바일용 컨버전한 것은 안할때
					$pos = strrpos($aval, "/");
					if ($pos) {
						$new_filename = "m_".substr($aval, ($pos+1));
						$result = editorimg_convert($aval, $img_editor_fullpath.$new_filename, 300);
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
						$result = editorimg_convert(SHOPROOT."/..".$aval, $img_editor_fullpath.$new_filename, 300);
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
			$arrFinalContent[] = implode('', $imgurl);
		}
	}
	$new_desc = implode('', $arrFinalContent);

	return $new_desc;
}

function img_convert($src,$folder,$sizeX=100,$sizeY=100,$fix=0)
{
	$_dir	= "../../data/goods/";
	if (preg_match('/^http(s)?:\/\//',$src))
		$_src = $src;
	else
		$_src = $_dir.$src;

	thumbnail($_src,$_dir.$folder,$sizeX);
	return 1;
}

function editorimg_convert($src,$folder,$sizeX=100,$sizeY=100,$fix=0)
{
	thumbnail($src,$folder,$sizeX);
	return 1;
}


?>