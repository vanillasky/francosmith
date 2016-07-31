<?php
/**
 * File 관련 클래스
 */

class L_File {
	// 파일 확장자 구하기
	function getExtension($filename) {
		$f = explode('.', $filename);

		return $f[(count($f) - 1)];
	}

	/* 2012-08-23 by.mimi
	 * 파일의 확장자를 제외한 이름 구하기 */
	function ignorExtensionName ($filename) {
		$f = substr($filename, 0, (strrpos($filename,".")));

		return $f;
	}
	
	/* 썸네일 만들기 - gif, jpg 형식만 지원 
	$org : 파일경로
	$dest : 파일이름
	*/
	function resizeImage($org, $dest, $width, $ext = '') {
		$imgInfo = getimagesize($org);		//이미지 정보를 읽어온다.
		$owidth = $imgInfo[0];
		$oheight = $imgInfo[1];
		$otype = $imgInfo[2];

		if( !($imgInfo[2] == '2' || $imgInfo[2] == '1'|| $imgInfo[2] == '3') && $ext == '' ) die('썸네일은 GIF, JPEG, PNG 형식의 파일만 지원합니다');

		if ( $imgInfo[2] == 1 || $ext == 'gif') {
			$im = imagecreatefromgif($org);
		} else if ( $imgInfo[2] == 3 || $ext == 'png') {
			$im = imagecreatefrompng($org);
		} else {
			$im = imagecreatefromjpeg($org);
		}
		
		$imgw = $width;
		$imgh = $oheight * $imgw / $owidth;
		
		$im1 = imagecreatetruecolor($imgw, $imgh);
		imagecopyresampled($im1, $im, 0, 0, 0, 0, $imgw, $imgh, imagesx($im), imagesy($im));
		if ( $imgInfo[2] == '1' ) {
			imagegif($im1, $dest) or die('imagegif->GIF 썸네일 생성 에러');
		} else if ( $imgInfo[2] == '3' ) {
			imagepng($im1, $dest) or die('imagepng->PNG 썸네일 생성 에러');
		} else {
            imagejpeg($im1, $dest, 100) or die('imagejpeg->JPG 썸네일 생성 에러');
        }
		imagedestroy($im1);
		imagedestroy($im);
	}

	// $watermark = 워터마크 이미지 - 반드시 gif
	// $position = 워터마크 위치 1-왼쪽 상단, 2-오른쪽 상단, 3-중앙, 4-왼쪽 하단, 5-오른쪽 하단
	// $capacity = 투명도 정도
	// $transparentColor = white, black, magenta 중 하나
	// $quality = jpeg 압축 퀄리티
	function resizeImageWatermark($org, $dest, $width, $height, $watermark = '', $position = 5, $capacity = 80, $transparentColor = 'white', $quality = 100) {

		$imgInfo = getimagesize($org);
		$owidth = $imgInfo[0];
		$oheight = $imgInfo[1];
		$otype = $imgInfo[2];
		$waterInfo = getimagesize($watermark);

		if($imgInfo[2] != 2) die('썸네일은 JPEG 형식의 파일만 지원합니다');
		if ( !@is_file($watermark) || $waterInfo[2] != 1 ) die('워터마크 이미지는 GIF 형식의 파일만 지원합니다');

		$im = imagecreatefromjpeg($org);		//jpg파일 만들기
		if ( imagesy($im) < imagesx($im) ) {	//비율축소 넓이 기준
			$imgw = $width;
			$imgh = $oheight * $imgw / $owidth;
		} else {								//높이기준 축소
			$imgh = $height;
			$imgw = $owidth * $imgh / $oheight;
		}

		$im1 = imagecreatetruecolor($imgw, $imgh); 
		imagecopyresampled($im1, $im, 0, 0, 0, 0, $imgw, $imgh, imagesx($im), imagesy($im));

		// for watermark
		// create true color overlay image:
		$overlay_src = imagecreatefromgif($watermark);
		$overlay_w = ImageSX($overlay_src);
		$overlay_h = ImageSY($overlay_src);
		$overlay_img = imagecreatetruecolor($overlay_w, $overlay_h);
		imagecopy($overlay_img, $overlay_src, 0,0,0,0, $overlay_w, $overlay_h);
		imagedestroy($overlay_src);    // no longer needed

		// setup transparent color (pick one):
		$black  = imagecolorallocate($overlay_img, 0x00, 0x00, 0x00);
		$white  = imagecolorallocate($overlay_img, 0xFF, 0xFF, 0xFF);
		$magenta = imagecolorallocate($overlay_img, 0xFF, 0x00, 0xFF);
		// and use it here:
		imagecolortransparent($overlay_img, ${$transparentColor});

		// watermark position
		switch ( $position ) {
			case 1 :
				$pw = 10;
				$ph = 10;
				break;
			case 2 :
				$pw = $imgw - $overlay_w - 10;
				$ph = 10;
				break;
			case 3 :
				$pw = $imgw / 2 + $overlay_w / 2;
				$ph = $imgh / 2 + $overlay_h / 2;
				break;
			case 4 :
				$pw = 10;
				$ph = $imgh - $overlay_h - 10;
				break;
			default :
				$pw = $imgw - $overlay_w - 10;
				$ph = $imgh - $overlay_h - 10;
		}

		// watermark merge
		imagecopymerge($im1, $overlay_img, $pw, $ph, 0, 0, $overlay_w, $overlay_h, $capacity);

		imagejpeg($im1, $dest, $quality);

		imagedestroy($overlay_img);
		imagedestroy($im1);
		imagedestroy($im);
	}

	// 파일 사이즈 리턴
	function byteToKb($size) {
		if($size < 1024) {
			$ret = '1 Kb';
		} else {
			$ret = number_format(floor($size / 1024)) . ' Kb';
		}

		return $ret;
	}

	// enough to delete anything inside a folder and return success or not
	// hope this can save your time :-)
	// author : jackylee
	function deldir($dir) {
		$handle = opendir($dir);
		while (false!==($FolderOrFile = readdir($handle))) {
			if($FolderOrFile != "." && $FolderOrFile != "..") {
				if(is_dir("$dir/$FolderOrFile")) deldir("$dir/$FolderOrFile");  // recursive
				else unlink("$dir/$FolderOrFile");
			}
		}
		closedir($handle);
		if(rmdir($dir)) $success = true;
		return $success;
	}

	//파일 업로드 합니다.
	function fileup($fileup,$filename,$dir){
		//저장경로를 만든다.
		$savefile = $dir.$filename;

		// 서버에 첨부파일을 복사한다.
		if(!move_uploaded_file($fileup, $savefile))  {
			die(" savefile 저장하기 실패! - check permissions");
			exit;
		}

		return $filename;
	}
	
	function copyfileup($source,$filename,$path){
		
		//저장경로를 만든다.
		$savefile = $path."/".$filename;

		if(!copy($source, $savefile)){
			$ret = false;
		}else{
			$ret = true;
		}

		return $ret;
	}

	function FileUpDir( $path, $source, $filename) {
		
		$ret = false;

		// 디렉토리를 만든다.  상위디렉토리부터 모두 만든다.
		$dir=split("/", $path);
		//$tpath =".";
		$ret = true;
		for ($i=1; $i<count($dir); $i++) {
			$tpath .= "/" . $dir[$i];
			if(!@chdir($tpath)){
				@chdir("/");
				//echo $tpath;
				if(!mkdir($tpath,0777)) break;
			} 
		}
		
		if(!copy(  $source,$path . $filename)){
			$ret = false;
		}else{
			$ret = true;
		}
		
		return $ret;
	}
	
	function ftp( $server, $user, $passwd, $path, $source, $filename) {
		$ret = false;
        $conn_id = ftp_connect($server);
        if($conn_id) {
			$login_result = ftp_login($conn_id, $user, $passwd);

			if(!ftp_put($conn_id, $path . $filename, $source, FTP_BINARY)){
				$ret = false;
			}else{
				$ret = true;
			}
            ftp_quit($conn_id);

		}
		return $ret;
	}

	/*function ftp( $server, $user, $passwd, $path, $source, $filename) {
		
		$ret = false;
        $conn_id = ftp_connect($server);
        if($conn_id) {
            $login_result = ftp_login($conn_id, $user, $passwd);

		    // 디렉토리를 만든다.  상위디렉토리부터 모두 만든다.
			$dir=split("/", $path);
			//$tpath =".";
			$ret = true;
			for ($i=1; $i<count($dir); $i++) {
				$tpath .= "/" . $dir[$i];
				if(!@ftp_chdir($conn_id, $tpath)){
					@ftp_chdir($conn_id, "/");
					//echo $tpath;
					if(!ftp_mkdir($conn_id, $tpath)) break;
				} 
			}
			//echo $path.$filename;
			if(!ftp_put($conn_id, $path . $filename, $source, FTP_BINARY)){
				$ret = false;
			}else{
				$ret = true;
			}
            ftp_quit($conn_id);
        }

		return $ret;
	}*/

		
	function fileopen($url, $mode = 'r') {
		$fp = @fopen($url, $mode);
		if ( $fp != null ) {
			while (!feof ($fp)) {
				$result .= fgets($fp, 4096);
			}
			fclose($fp);
		}
		return $result;
	}

	function filewrite($url, $data) {
		
		$fp = fopen($url, 'w');
		fwrite($fp, $data);
		fclose($fp);
	}

	function filecopy($url, $file, $addData = '') {
		$this->filewrite($file, $this->fileopen($url) . $addData);
	}

	function filecache($url, $file, $interval = 1) {
		if ( !is_file($file) ) {
			@touch($file) or die('Permission denied');
		}
		if ( time() - filemtime($file) > $interval * 60 ) {
			$this->filecopy($url, $file, "<!--last modified:" . date('Y-m-d H:i:s') . "-->");
		}

		return $file;
	}




}
?>