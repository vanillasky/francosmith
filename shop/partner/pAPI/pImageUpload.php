<?
/*********************************************************
* 파일명     :  pImageUpload.php
* 프로그램명 :	이미지 upload API
* 작성자     :  dn
* 생성일     :  2011.12.06
**********************************************************/
include "../../lib/library.php";
include "../../conf/config.php";
require_once "../../lib/pAPI.class.php";
require_once "../../lib/json.class.php";

$pAPI = new pAPI();
$json = new Services_JSON(16);

### 인증키 Check (실제로는 아이디와 비번 임) 시작 ###
if(!$_POST['authentic']) {
	$res_data['code'] = '302';
	$res_data['msg'] = '인증키가 없습니다.';
	echo ($json->encode($res_data));
	exit;
}

if(!$pAPI->keyCheck($_POST['authentic'])) {
	$res_data['code'] = '302';
	$res_data['msg'] = '인증키가 맞지 않습니다.';
	echo ($json->encode($res_data));
	exit;
}
unset($_POST['authentic']);
### 인증키 Check 끝 ###

if(!empty($_POST)) {
	foreach($_POST as $key => $val) {
		if(strstr($key, 'arr_')) {
			${str_replace('arr_', '', $key)} = explode('|', $val);
		}
		else  {
			${$key} = $val;
		}
	}
}

if($mode == 'goods') {
	
	foreach($images as $image_url) {
		$url_stuff = parse_url($img_url); 
		$port = isset($url_stuff['port']) ? $url_stuff['port'] : 80; 

		$fp = fsockopen($url_stuff['host'], $port); 

		$query  = 'GET ' . $url_stuff['path'] . " HTTP/1.0\n"; 
		$query .= 'Host: ' . $url_stuff['host']; 
		$query .= "\n\n"; 

		@fwrite($fp, $query); 

		while ($tmp = fread($fp, 1024)) 
		{ 
			$buffer .= $tmp; 
		} 
		
		preg_match('/Content-Length: ([0-9]+)/', $buffer, $parts); 

		$img_content = substr($buffer, - $parts[1]); 
		@fclose($fp);

		
		$ext_idx = strrpos($img_url, '.');
		$ext = '';
		if ($ext_idx !== false) $ext = '.'.substr($img_url, $ext_idx + 1);

		### 이미지 확장자가 없을 경우 이미지 타입으로 확장자 입력
		if(strtoLower($ext) != '.gif' || strtoLower($ext) != '.jpg' || strtoLower($ext) != '.jpeg' || strtoLower($ext) != '.png') {
			$arr_img_type = getimagesize($img_url);
			
			switch ($arr_img_type[2]) {
				case 1 :
					$ext = '.gif';
					break;
				case 2 :
					$ext = '.jpg';
					break;
				case 3 :
					$ext = '.png';
					break;
				case 6 :
					$ext = '.bmp';
					break;
				default :
					$ext = '';
					break;
			}
		}
		
		### 이미지 확장자가 없을 경우 이미지 비정상 처리
		if(!$ext) {
			$img_yn='N';
		}

		$img_path = '../../data/goods/'.date('Ym');
		if (!is_dir($img_path)) {
			@mkdir($img_path);
			@chmod($img_path, 0707);
		}
		$arr_goods['img'.$i] = $img_path.'/'.$arr_goods['goods_cd'].'_'.$i.$ext;
		
		$w_fh = @fopen($arr_goods['img'.$i], 'w');
		@fwrite($w_fh, $img_content);
		@chmod($arr_goods['img'.$i], 0707);
		@fclose($w_fh);

		if(!$img_content) {
			$img_yn = 'N';
		}

		$thumb_dir = '../data/goods/'.date('Ym').'/t';
		if (!is_dir($thumb_dir)) {
			@mkdir($thumb_dir);
			@chmod($thumb_dir, 0707);
		}
		
		thumbnailImage($arr_goods['img'.$i], $thumb_dir.'/'.$arr_goods['goods_cd'].'_'.$i.$ext, 50);
		$thumb_dir = null;
		unset($img_content);
		unset($buffer);
		$arr_goods['img'.$i] = str_replace('../', '/', $arr_goods['img'.$i]);
	}
}
else {

}



echo ($json->encode($res_data));

?>