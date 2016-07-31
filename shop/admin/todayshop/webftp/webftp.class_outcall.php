<?
/*---------------------------
 WebFTP 관리 외부에 호출 지원
---------------------------*/
function outcallUpload( $_file, $nowPath, $userori = array() ){ // 이미지

	if ( count( $_file ) ){ // Webftp 클래스 정의

		include dirname(__FILE__) . "/../../../conf/config.php";
		include_once dirname(__FILE__) . "/../../design/webftp/webftp.class.php";

		if($cfg['tplSkinTodayWork'] != ""){
			$tplSkin = $cfg['tplSkinTodayWork'];
		}else{
			$tplSkin = $cfg['tplSkinToday'];
		}

		$webftp = new webftp;
		$webftp->ftp_path = str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] ) . $cfg['rootDir'] . '/data/skin_today/' . $tplSkin; # 스킨경로
		if ( $nowPath == '' ) $nowPath = '/'; # 업로드경로
	}

	if ( $nowPath != '' ){ // 업로드경로 검증&생성

		$tmp = explode( "/", $nowPath );
		$dir = $webftp->ftp_path;

		for ( $i = 0; $i < ( count( $tmp ) - 1 ); $i++ ){

			$dir .= $tmp[ $i ] . '/';
			if ( !@file_exists( $dir ) ) @mkdir( $dir, 0757 );
			@chMod( $dir, 0757 );
		}

		$nowPath = $dir; # 업로드경로 재정의
	}

	foreach ( $_file as $key => $property ){ // 삭제처리

		$del = preg_replace( "'_up$'si", "_del", $key );
		$key = preg_replace( "'_up$'si", "", $key );

		if ( is_array( $property['name'] ) == false ){

			if ( trim( $_POST[ $key ] ) == '' ) continue;

			if ( trim( $_POST[ $del ] ) == 'Y' || trim( $property['name'] ) != '' ){
				@unlink( $nowPath . $_POST[ $key ] );
				$_POST[ $key ] = '';
			}
		}
		else {

			for ( $idx = 0; $idx < count( $property['name'] ); $idx++ ){

				if ( trim( $_POST[ $key ][ $idx ] ) == '' ) continue;

				if ( trim( $_POST[ $del ][ $idx ] ) == 'Y' || trim( $property['name'][ $idx ] ) != '' ){
					@unlink( $nowPath . $_POST[ $key ][ $idx ] );
					$_POST[ $key ][ $idx ] = '';
				}
			}
		}
	}

	foreach ( $_file as $key => $property ){ // 업로드처리

		$key = preg_replace( "'_up$'si", "", $key );

		if ( is_array( $property['name'] ) == false ){

			if ( trim( $property['name'] ) == '' ) continue;

			if ( $userori[ $key ] != '' ) $OriName = $userori[ $key ]; # 사용자 저장파일명
			else $OriName = $property['name'];

			$TmpName = $webftp->validName( $nowPath, $OriName );

			if ( $webftp->chkSheet( $OriName, $webftp->app_ext_str ) == false ){
				$webftp->go_end( "업로드파일중에 확장자가 허용되지않는 파일이 첨부되어져 있습니다.", "history.go( -1 );" );
			}

			@move_uploaded_file ( $property['tmp_name'], $nowPath . $TmpName );
			@chmod( $nowPath . $TmpName, 0707 );

			$_POST[ $key ] = $TmpName;
		}
		else {

			for ( $idx = 0; $idx < count( $property['name'] ); $idx++ ){

				if ( trim( $property['name'][ $idx ] ) == '' ) continue;

				if ( $userori[ $key ][ $idx ] != '' ) $OriName = $userori[ $key ][ $idx ]; # 사용자 저장파일명
				else $OriName = $property['name'][ $idx ];

				$TmpName = $webftp->validName( $nowPath, $OriName );

				if ( $webftp->chkSheet( $OriName, $webftp->app_ext_str ) == false ){
					$webftp->go_end( "업로드파일중에 확장자가 허용되지않는 파일이 첨부되어져 있습니다.", "history.go( -1 );" );
				}

				@move_uploaded_file ( $property['tmp_name'][ $idx ], $nowPath . $TmpName );
				@chmod( $nowPath . $TmpName, 0707 );

				$_POST[ $key ][ $idx ] = $TmpName;
			}
		}
	}

	setDu('skin_today'); # 계정용량 계산
}
?>