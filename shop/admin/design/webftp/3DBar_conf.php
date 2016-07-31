<?
//==============================================================================//
//		PROGRAM Title : Godo-Shoppingmall Counter
//		Company Name  : (주) 플라이폭스 - 고도몰
//
//		Version       : 2.0 Version
//		Create Date   : 2006.06.01
//		Update Date   : ----,--,--
//		Programer     : 박선희(신동규)
//		Copyright (C)2004 flyfox.co.kr , All rights reserved.
//==============================================================================//

//==============================================================================//
//	DB & Data 설정
//==============================================================================//
	include dirname(__FILE__)."/../../lib.php";

//==============================================================================//
//	통계 구하기
//==============================================================================//
	include "3DBar_calc_conf.php";

//==============================================================================//
//	GD library가 서버에 Install 되있는지를 검사
//==============================================================================//
	$GD_library = "no";
	if ( function_exists( "gd_info" ) ){
		$info = gd_info();
		$keys  = array_keys($info);
		for ( $i = 1; $i < count( $keys ); $i++ ){
			if ( !eregi( "PNG", $keys[$i] ) ) continue;
			$infoCHK = (int)$info[$keys[$i]];
			if ( $infoCHK == "1" ) $GD_library = "yes";
			else $GD_library = "no";
		}
	}

	if($GD_library == "yes"){ // 3D 그래프 모드
		include "3DBar_GD.php";
	}
	else { // 일반 그래프 모드
		include "3DBar_basic.php";
	}
?>