<?php
/**
 * File ���� Ŭ����
 */

class L_File {
	// ���� Ȯ���� ���ϱ�
	function getExtension($filename) {
		$f = explode('.', $filename);

		return $f[(count($f) - 1)];
	}

	/* 2012-08-23 by.mimi
	 * ������ Ȯ���ڸ� ������ �̸� ���ϱ� */
	function ignorExtensionName ($filename) {
		$f = substr($filename, 0, (strrpos($filename,".")));

		return $f;
	}
	
	/* ����� ����� - gif, jpg ���ĸ� ���� 
	$org : ���ϰ��
	$dest : �����̸�
	*/
	function resizeImage($org, $dest, $width, $ext = '') {
		$imgInfo = getimagesize($org);		//�̹��� ������ �о�´�.
		$owidth = $imgInfo[0];
		$oheight = $imgInfo[1];
		$otype = $imgInfo[2];

		if( !($imgInfo[2] == '2' || $imgInfo[2] == '1'|| $imgInfo[2] == '3') && $ext == '' ) die('������� GIF, JPEG, PNG ������ ���ϸ� �����մϴ�');

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
			imagegif($im1, $dest) or die('imagegif->GIF ����� ���� ����');
		} else if ( $imgInfo[2] == '3' ) {
			imagepng($im1, $dest) or die('imagepng->PNG ����� ���� ����');
		} else {
            imagejpeg($im1, $dest, 100) or die('imagejpeg->JPG ����� ���� ����');
        }
		imagedestroy($im1);
		imagedestroy($im);
	}

	// $watermark = ���͸�ũ �̹��� - �ݵ�� gif
	// $position = ���͸�ũ ��ġ 1-���� ���, 2-������ ���, 3-�߾�, 4-���� �ϴ�, 5-������ �ϴ�
	// $capacity = ���� ����
	// $transparentColor = white, black, magenta �� �ϳ�
	// $quality = jpeg ���� ����Ƽ
	function resizeImageWatermark($org, $dest, $width, $height, $watermark = '', $position = 5, $capacity = 80, $transparentColor = 'white', $quality = 100) {

		$imgInfo = getimagesize($org);
		$owidth = $imgInfo[0];
		$oheight = $imgInfo[1];
		$otype = $imgInfo[2];
		$waterInfo = getimagesize($watermark);

		if($imgInfo[2] != 2) die('������� JPEG ������ ���ϸ� �����մϴ�');
		if ( !@is_file($watermark) || $waterInfo[2] != 1 ) die('���͸�ũ �̹����� GIF ������ ���ϸ� �����մϴ�');

		$im = imagecreatefromjpeg($org);		//jpg���� �����
		if ( imagesy($im) < imagesx($im) ) {	//������� ���� ����
			$imgw = $width;
			$imgh = $oheight * $imgw / $owidth;
		} else {								//���̱��� ���
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

	// ���� ������ ����
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

	//���� ���ε� �մϴ�.
	function fileup($fileup,$filename,$dir){
		//�����θ� �����.
		$savefile = $dir.$filename;

		// ������ ÷�������� �����Ѵ�.
		if(!move_uploaded_file($fileup, $savefile))  {
			die(" savefile �����ϱ� ����! - check permissions");
			exit;
		}

		return $filename;
	}
	
	function copyfileup($source,$filename,$path){
		
		//�����θ� �����.
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

		// ���丮�� �����.  �������丮���� ��� �����.
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

		    // ���丮�� �����.  �������丮���� ��� �����.
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