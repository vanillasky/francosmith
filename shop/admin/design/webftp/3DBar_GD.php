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
//	그래프 설정
//==============================================================================//
	// 마진 설정
	$G_Margin0	= 0;
	$G_Margin1	= 1;
	$G_Margin2	= 12;

	$S_Dot_RXX	= $G_Margin0;			// 뒷 배경의 좌측 윗쪽 시작 X 점
	$S_Dot_RYY	= $G_Margin2;			// 뒷 배경의 좌측 윗쪽 시작 Y 점
	$E_Dot_RXX	= $G_Width - $G_Margin1;	// 뒷 배경의 우측 아래쪽 끝 X 점
	$E_Dot_RYY	= $G_Height - $G_Margin1;	// 뒷 배경의 우측 아래쪽 끝 Y 점

	$RXX_Line_Total	= $E_Dot_RXX - $S_Dot_RXX;		// 값 : 280
	$RYY_Line_Total	= $E_Dot_RYY - $S_Dot_RYY;		// 값 : 50

//==============================================================================//
//	그래프 기초 그리기 & 그래프의 천제 색상 설정 (그래프 배경)
//==============================================================================//
	// 그래프 그리기
	Header("Content-type : image/png");
	$ImC = ImageCreate($G_Width, $G_Height);

	// 그래프 출력 색상 지정
	$G_BGColor	= ImageColorAllocate($ImC , 255 , 255 , 255);
	$G_BGLine	= ImageColorAllocate($ImC , 112 , 112 , 112);
	$G_FontColor	= ImageColorAllocate($ImC , 242 , 68 , 0);
	$G_SquareColor	= ImageColorAllocate($ImC , 54 , 54  , 54);

	// 기본 이미지 & 바깥외곽선 그리기
	ImageFilledRectangle($ImC, 0, 0, $G_Width, $G_Height, $G_BGColor);

//==============================================================================//
//	그래프 눈금선 그리기
//==============================================================================//
	ImageString($ImC , 2, $S_Dot_RXX, $S_Dot_RYY - 13, "0Mb", $G_FontColor);
	ImageString($ImC , 2, $RXX_Line_Total - (strlen($maxmark) * 5.5), $S_Dot_RYY - 13, $maxmark, $G_FontColor);

	ImageRectangle($ImC, $S_Dot_RXX, $S_Dot_RYY, $E_Dot_RXX, $E_Dot_RYY, $G_BGLine);		// 사각형 (평면)

	$rate = ( $size / $maxsize );
	$square = floor( ( $E_Dot_RXX-3 ) * $rate );
	if ( $square < 1 ) $square = 1;

	ImageFilledRectangle($ImC, $S_Dot_RXX+2, $S_Dot_RYY+2, $S_Dot_RXX+1+$square, $E_Dot_RYY-2, $G_SquareColor);	// 직선 눈금 색채우기

//==============================================================================//
//	3차원 그래프 출력
//==============================================================================//
	imagepng($ImC);
	ImageDestroy($ImC);
?>