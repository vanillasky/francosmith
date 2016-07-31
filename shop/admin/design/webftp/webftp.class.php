<?

// 클래스

class webftp {

	var $ftp_path = ""; # FTP ROOT 경로
	var $ftp_url = ""; # FTP HOME 경로

	var $dir_display = array(); # 디렉토리별 하위디렉토리 출력 제어

	var $notfile =  array( '.', '..', '.bash_logout', '.bash_profile', '.bashrc', '.emacs', '.bash_history', '.bash_logout.rpmnew', '.bash_profile.rpmnew', '.bashrc.rpmnew' ); # 파일명 출력 제어

	var $app_ext = array( 'ai', 'bmp', 'jpg', 'jpeg', 'gif', 'png', 'swf', 'csv', 'doc', 'hwp', 'ppt', 'xls', 'txt', 'zip', 'js', 'ico', 'xml', 'as' ); # 업로드 허용 확장자
	var $app_ext_str = "";

	var $img_ext = array( 'ai', 'bmp', 'jpg', 'jpeg', 'gif', 'png', 'swf' ); # 이미지파일 확장자
	var $img_ext_str = "";

	var $ext_name = array(
		'dir' => '파일 폴더',
		'file' => '파일',
		'ai' => 'AI',
		'bmp' => 'BMP',
		'jpg' => 'JPG',
		'jpeg' => 'JPEG',
		'gif' => 'GIF',
		'png' => 'PNG',
		'swf' => 'SWF',
		'csv' => 'CSV',
		'doc' => '워드',
		'hwp' => '한글',
		'pdf' => 'PDF문서',
		'ppt' => '파워포인트',
		'xls' => '엑셀',
		'txt' => '텍스트',
		'zip' => '압축',
		'js' => 'JS',
	);



	/*-------------------------------------
		Init
	-------------------------------------*/
	function webftp(){

		include dirname(__FILE__) . "/../../../conf/config.php";

		$hostpath	= str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] );

		$this->ftp_path = $hostpath . $cfg['rootDir']; # FTP ROOT 경로
		$this->ftp_url = $cfg['rootDir']; # FTP HOME 경로

		$this->dir_display = array(); # 디렉토리별 하위디렉토리 출력 제어
		$this->dir_display[] = array( 'path' => $this->ftp_path . '/', 'able' => 'data;skin', 'unable' => '' );
		$this->dir_display[] = array( 'path' => $this->ftp_path . '/data/', 'able' => '', 'unable' => 'board' );
		$this->dir_display[] = array( 'path' => $this->ftp_path . '/skin/*/', 'able' => 'board;img', 'unable' => '' );

		$this->app_ext_str = "." . implode( ";.", $this->app_ext ) . ";";
		$this->img_ext_str = "." . implode( ";.", $this->img_ext ) . ";";
	}



	/*-------------------------------------
		확장자 체크
	-------------------------------------*/
	function chkSheet( $fn, $types ){
		$chks = explode( ";", $types );
		$mxs = sizeof( $chks );
		
		$extFn = '.'.strtoLower(pathinfo($fn,PATHINFO_EXTENSION));
		for ( $i = 0; $i < $mxs; $i++ ) if ( trim( $chks[$i] ) && trim( $chks[$i] ) == $extFn ) return true;
		if($extFn == '.') return true;
		
		return false;
	}



	/*-------------------------------------
		스크립트
	-------------------------------------*/
	function go_end( $msg, $cmd="" ){

		$retMsg = "<Script>\n";
		if ( $msg ) $retMsg .= "alert( '" . $msg . "' );\n";
		$retMsg .= $cmd . "\n";
		$retMsg .= "</Script>\n";

		echo $retMsg;
		exit;
	}



	/*-------------------------------------
		디렉토리 출력 제어 체크
		dirpath : 디렉토리 경로
	-------------------------------------*/
	function chkDirDisplay( $dirpath, $file ){

		foreach( $this->dir_display as $tmp ){

			$pattern = str_replace( array( "/", "*" ), array( "\/", "[^\/]+" ), $tmp['path'] );

			if ( !preg_match( "/^{$pattern}$/i", $dirpath ) ) continue;
			if ( $tmp['able'] != '' && !in_array( $file, explode( ";", $tmp['able'] ) ) ) return false;
			if ( $tmp['unable'] != '' && in_array( $file, explode( ";", $tmp['unable'] ) ) ) return false;
		}

		return true;
	}



	/*-------------------------------------
		하위 디렉토리 구조 리턴
		dirpath : 디렉토리 경로
	-------------------------------------*/
	function get_dirStructure( $dirpath ){

		$arr = array();
		$dir = $dirpath;

		if ( !$dh = @opendir( $dir ) ) return $arr;

		while ( ( $file = @readdir( $dh ) ) !==  false ) {

			if ( in_array( $file, array( '.', '..' ) ) ) continue;
			if ( @filetype( $dir . $file ) != 'dir' ) continue;
			if ( $this->chkDirDisplay( $dirpath, $file ) !== true ) continue;

			$arr[ $file ] = array( path => ( $dir . $file ), low_dir => $this->get_dirStructure( $dir . $file . "/" ) );
		}

		@closedir( $dh );

		return $arr;
	}



	/*-------------------------------------
		디렉토리 내용 리턴
		dirpath : 디렉토리 경로
	-------------------------------------*/
	function get_dirList( $dirpath ){

		$arr = array( 'dir' => array(), 'file' => array() );
		$dir = $dirpath;

		if ( $dh = @opendir( $dir ) ) {

			while ( ( $file = @readdir( $dh ) ) !==  false ) {

				if ( in_array( $file, $this->notfile) ) continue;

				if ( @filetype( $dir . $file ) == 'dir' ){

					if ( $this->chkDirDisplay( $dirpath, $file ) !== true ) continue;
					$arr[dir][] = array( 'type' => 'dir', 'name' => $file, 'size' => filesize( $dir . $file ), 'date' => filemtime( $dir . $file ) );
				}
				else {

					if ( $this->chkSheet( $file, $this->app_ext_str ) == false ) continue;
					$arr[file][] = array( 'type' => 'file', 'name' => $file, 'size' => filesize( $dir . $file ), 'date' => filemtime( $dir . $file ) );
				}
			}

			@closedir( $dh );
		}

		return $arr;
	}



	/*-------------------------------------
		디렉토리 쿼리
		dirpath : 디렉토리 경로
	-------------------------------------*/
	function get_dirQuery( $dirpath, $query ){

		$getList = $this->get_dirList( $dirpath );

		$query = preg_replace( "'[[:space:]]{3,}'", " ", $query ); # 공백 2개 이상인 경우 제거


		{ // query 분할

			$exp_query			= preg_split( '/ORDER BY/is', $query, -1 );
			$where_query		= preg_replace( '/WHERE/is', "", $exp_query[0] );
			$orderby_query		= trim( $exp_query[1] );
		}


		if ( $where_query != '' ){ // 검색

			$result = array();

			{ // 검색 쿼리

				$where = explode( "=", $where_query );

				$where[0] = trim( $where[0] );
				$where[1] = trim( $where[1] );

				if ( !in_array( $where[0], array( 'name', 'size', 'date' ) ) ) $where[0] = 'name';
			}


			foreach ( $getList as $b_key => $b_arr ){

				$tmp = array();
				foreach ( $b_arr as $s_key => $s_arr ) $tmp[ $s_key ] = $s_arr[ $where[0] ]; // 임시 공간에 저장

				foreach ( $tmp as $k => $v ){ // 검색실행

					preg_match( "'" . $where[1] . "'is", $v, $matches);
					if ( count( $matches ) > 0 ) $result[ $b_key ][] = $getList[ $b_key ][ $k ];
				}
			}

			$getList = $result;
		}


		if ( $orderby_query != '' ){ // 정렬

			$result = array();

			{ // 정렬 쿼리

				$orderby = explode( " ", strtolower( $orderby_query ) );

				$orderby[0] = trim( $orderby[0] );
				$orderby[1] = trim( $orderby[1] );

				if ( !in_array( $orderby[0], array( 'name', 'size', 'date' ) ) ) $orderby[0] = 'name';
				if ( !in_array( $orderby[1], array( 'asc', 'desc' ) ) ) $orderby[1] = 'asc';
			}


			foreach ( $getList as $b_key => $b_arr ){

				if ( count( $b_arr ) > 1 ){

					$tmp = array();
					foreach ( $b_arr as $s_key => $s_arr ) $tmp[ $s_key ] = strtolower( $s_arr[ $orderby[0] ] ); // 임시 공간에 저장

					if ( $orderby[1] == 'desc' ) arsort( $tmp ); else asort( $tmp ); // 임시 공간 정렬
					reset( $tmp );

					foreach ( $tmp as $k => $v ) $result[ $b_key ][] = $getList[ $b_key ][ $k ];  // 리턴 공간으로 데이타 이전
				}
				else $result[ $b_key ] = $b_arr;
			}

			$getList = $result;
		}


		{ // 병합

			$tmp = array();

			if ( is_array( $getList[dir] ) ) $tmp = array_merge( $tmp, $getList[dir] );
			if ( is_array( $getList[file] ) ) $tmp = array_merge( $tmp, $getList[file] );
			$getList = $tmp;
		}


		return $getList;
	}



	/*-------------------------------------
		폴더 / 파일 삭제
		path : 경로
	-------------------------------------*/
	function delDirFile( $path ){

		if ( !@file_exists( $path ) ) return false;
		else {

			if ( @filetype( $path ) == 'dir' ){
				if ( !@rmdir( $path ) ) return false;
			}
			else {
				if ( !@unlink( $path ) ) return false;
			}
		}

		return true;
	}



	/*-------------------------------------
		이미지 사이즈 체크
		ImgName : 파일 경로
		WSize * HSize : 가로 * 세로 크기
	-------------------------------------*/
	function ImgSizeLode( $ImgName, $WSize="", $HSize="" ){

		if ( $this->chkSheet( $ImgName, $this->img_ext_str ) == true ){

			if ( preg_match( "'\.swf$'is", $ImgName ) ){ // 플래쉬 파일인경우
				$swf = new swfheader(false) ;
				$swf->loadswf( $ImgName ) ;
				$ImgSize = array( $swf->width, $swf->height  );
			}
			else {
				$ImgSize	= @getimagesize( $ImgName ); # 이미지의 크기를 구함
			}

			if ( $WSize && $HSize ){ // 이미지나 플래쉬의 크기를 설정
				$PreWidth	= $WSize;
				$PreHeight	= $HSize;
			}
			else {
				$PreWidth	= $ImgSize[0];
				$PreHeight	= $ImgSize[1];
			}

			if ( !$PreWidth) $PreWidth = 1;
			if ( !$PreHeight ) $PreHeight = 1;

			if ( $ImgSize[0] >= $PreWidth && $ImgSize[1] >= $PreHeight ){ # 이미지나 플래쉬의 크기를 구함

				$height	= $PreWidth * $ImgSize[1] / $ImgSize[0];
				$width	= $PreHeight * $ImgSize[0] / $ImgSize[1];

				if($width >= $PreWidth && $height <= $PreHeight){
					$width		= $PreWidth;
					$height		= $width * $ImgSize[1] / $ImgSize[0];
				}

				if($width <= $PreWidth && $height >= $PreHeight){
					$height		= $PreHeight;
					$width		= $height * $ImgSize[0] / $ImgSize[1];
				}
			}
			else if ( $ImgSize[0] >= $PreWidth || $ImgSize[1] >= $PreHeight ){

				if ( $ImgSize[0] >= $PreWidth ){
					$width		= $PreWidth;
					$height		= $width * $ImgSize[1] / $ImgSize[0];
				}

				if ( $ImgSize[1] >= $PreHeight ){
					$height		= $PreHeight;
					$width		= $height * $ImgSize[0] / $ImgSize[1];
				}
			}
			else{
				$width		= $ImgSize[0];
				$height		= $ImgSize[1];
			}

			if ( !$width || !$height ){
				$width		= $PreWidth;
				$height		= $PreHeight;
			}
		}

		$ReSizeImg	= array("$width","$height"); # 크기를 구한 값을 배열화

		return $ReSizeImg;
	}



	/*-------------------------------------
		이미지 출력
		ImgName : 파일 경로
		WSize * HSize : 가로 * 세로 크기
	-------------------------------------*/
	function confirmImage( $ImgName, $WSize="", $HSize="", $BorderSize="", $IDName="", $vspace="", $hspace="" ){

		$file_path = str_replace( $this->ftp_url, $this->ftp_path, $ImgName );

		if ( $this->chkSheet( $ImgName, $this->img_ext_str ) == false ) return '';

		{ // 이미지 간격 조정

			$spaceStr = '';
			if ( $vspace ) $spaceStr .= ' vspace="' . $vspace . '" ';
			if ( $hspace ) $spaceStr .= ' hspace="' . $hspace . '" ';
		}

		if ( !$BorderSize ) $BorderSize = "0"; # 이미지 보더 크기가 없는 경우 "0"처리

		$ReSizeImg	= $this->ImgSizeLode( $file_path, $WSize, $HSize ); # 이미지나 플래쉬의 설정된 크기를 구함

		if ( preg_match( "'\.swf$'is", $ImgName ) ){ // 플래쉬 파일인경우

			# 플래쉬의 아이디값을 정함
			$randNo		= mt_rand(1,10000);
			$IDNameFlash	= $IDName;
			if ( !$IDName ) $IDNameFlash = "godo" . $randNo;

			$ReturnViewImg = '<script>embed("' . $ImgName . '?pageNum=' . $IDNameFlash.'",' . $ReSizeImg[0] . ',' . $ReSizeImg[1] . ')</script>';
		}
		else{ // 이미지 화일인경우
			$ReturnViewImg = '<img src="'.$ImgName.'" width="'.$ReSizeImg[0].'" height="'.$ReSizeImg[1].'" border="'.$BorderSize.'" style="border:'.$BorderSize.' solid f0f0f0;" align="absmiddle" id="'.$IDName.'" '.$spaceStr.'>';
		}

		return $ReturnViewImg;
	}



	/*-------------------------------------
		GD 복사함수
	-------------------------------------*/
	function create_thumb_wfixed( $file_name_src, $file_name_dest, $weight,$quality=100 ){

		if ( @file_exists( $file_name_src ) && isset( $file_name_dest ) ){

			$est_src = pathinfo( strtolower( $file_name_src ) );
			$est_dest = pathinfo( strtolower( $file_name_dest ) );

			$size = @getimagesize( $file_name_src );
			$w = number_format( $weight, 0, ',', '' );
			$h = number_format( ( $size[1] / $size[0] ) * $weight, 0, ',', '' );

			// IMPOSTAZIONE STREAM DESTINAZIONE
			if ( $est_dest['extension']=="gif" ){
				$dest = imagecreatetruecolor($w, $h);
			}
			elseif ( $est_dest['extension'] == "jpg" ){
			  // $file_name_dest = substr_replace($file_name_dest, 'jpg', -3);
			   $dest = imagecreatetruecolor($w, $h);
			   //imageantialias($dest, TRUE);
			}
			elseif ( $est_dest['extension'] == "png" ){
			   $dest = imagecreatetruecolor($w, $h);
			   //imageantialias($dest, TRUE);
			}
			else{
			   return FALSE;
			}

			// IMPOSTAZIONE STREAM SORGENTE
			switch( $size[2] ){
			case 1:      //GIF
			   $src = imagecreatefromgif( $file_name_src );
			   break;
			case 2:      //JPEG
			   $src = imagecreatefromjpeg( $file_name_src );
			   break;
			case 3:      //PNG
			   $src = imagecreatefrompng( $file_name_src );
			   break;
			default:
			   return FALSE;
			   break;
			}

			imagecopyresampled( $dest, $src, 0, 0, 0, 0, $w, $h, $size[0], $size[1] );

			switch( $size[2] ){
			case 1:
				imagegif( $dest, $file_name_dest );
				break;
			case 2:
				imagejpeg( $dest, $file_name_dest, $quality );
				break;
			case 3:
				imagepng( $dest, $file_name_dest );
				break;
			}
			return TRUE;
		}
		return FALSE;
	}



	/*-------------------------------------
		GD 체크함수
	-------------------------------------*/
	function getSupportedImageTypes(){

		$aSupportedTypes = array();

		$aPossibleImageTypeBits = array( "IMG_GIF", "IMG_JPG", "IMG_PNG", "IMG_WBMP" );

		foreach ( $aPossibleImageTypeBits as $iIndex => $sImageTypeBits ){

			$sEval  = "if ( imagetypes() & " . $sImageTypeBits . " ) { return TRUE; } else { return FALSE; }";
			if ( eval( $sEval ) ) $aSupportedTypes[] = str_replace( "IMG_", "", $sImageTypeBits );
		}

		return $aSupportedTypes;
	}



	/*-------------------------------------
		파일명 유효 체크
		dirpath : 디렉토리 경로
		filename : 파일명
	-------------------------------------*/
	function validName( $dirpath, $filename ){

		if ( $dirpath == '' || $filename == '' ) return '';
		if ( !@file_exists( $dirpath . $filename ) ) return $filename;

		if ( !preg_match( "/^Copy-/", $filename ) ){
			$filename = 'Copy-' . $filename;
			if ( !@file_exists( $dirpath . $filename ) ) return $filename;
		}

		preg_match( "/^(.*)(\.[^.]*$)/", $filename, $str );
		array_shift( $str );

		$getList	= $this->get_dirList( $dirpath );
		$b_arr		= $getList[file];

		$exist_no = array();

		foreach ( $b_arr as $s_arr ){

			$name = $s_arr['name'];

			preg_match( "'^" . $str[0] . ".*" . $str[1] . "$'is", $name, $matches );

			if ( count( $matches ) > 0 ){

				preg_match_all( "/(\()(.*)(\))/", $name, $matches2 );

				if ( count( $matches2 ) > 0 ){

					$num = $matches2[2][0];
					if ( is_numeric( $num ) ) $exist_no[] = $num;
				}
			}
		}

		if ( count( $exist_no ) < 1 ) $no = 2;
		else {

			$max_no = max( $exist_no );

			for ( $i = 2; $i <= ( $max_no + 1 ); $i++ ){

				if ( !in_array( $i, $exist_no ) ){
					$no = $i;
					break;
				}
			}
		}

		$filename = $str[0] . '(' . $no . ')' . $str[1];

		return $filename;
	}
}