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
//	통계 구하기
//==============================================================================//
	if ( preg_match( "/^self/i", $godo[ecCode] ) ){ // eNamoo 독립형
		$size = getDu('root');
	}
	else { // 임대형
		$size = getDu('disk');
	}
	$sizeStr	= byte2str($size);
	$size 		= round($size / 1024);			// K단위
	$size 		= round($size / 1024,2);		// M단위

	$maxsize = $godo[maxDisk] + $godo[diskGoods]; // M기본제공
	$maxmark = byte2str(mb2byte($godo[maxDisk] + $godo[diskGoods])) . 'b'; // 표기

	if ( !$size ) $size = 1;
	if ( !$maxsize ) $maxsize = 1;
	
	# 생성될 그래프 이미지의 전체 크기
	$G_Width = $_GET[G_Width];
	$G_Height = $_GET[G_Height];
	if ( $G_Width == '' ) $G_Width	= 144;
	if ( $G_Height == '' ) $G_Height	= 8;
	
	# 일반 그래프 모드용
	$rate = ( $size / $maxsize );
	$square = floor( ( $G_Width - 4 ) * $rate );
	if ( $square < 1 ) $square = 1;
	elseif ( $square > $G_Width ) $square = $G_Width;
?>