<?
//==============================================================================//
//		PROGRAM Title : Godo-Shoppingmall Counter
//		Company Name  : (��) �ö������� - ����
//
//		Version       : 2.0 Version
//		Create Date   : 2006.06.01
//		Update Date   : ----,--,--
//		Programer     : �ڼ���(�ŵ���)
//		Copyright (C)2004 flyfox.co.kr , All rights reserved.
//==============================================================================//

//==============================================================================//
//	�׷��� ����
//==============================================================================//
	// ���� ����
	$G_Margin0	= 0;
	$G_Margin1	= 1;
	$G_Margin2	= 12;

	$S_Dot_RXX	= $G_Margin0;			// �� ����� ���� ���� ���� X ��
	$S_Dot_RYY	= $G_Margin2;			// �� ����� ���� ���� ���� Y ��
	$E_Dot_RXX	= $G_Width - $G_Margin1;	// �� ����� ���� �Ʒ��� �� X ��
	$E_Dot_RYY	= $G_Height - $G_Margin1;	// �� ����� ���� �Ʒ��� �� Y ��

	$RXX_Line_Total	= $E_Dot_RXX - $S_Dot_RXX;		// �� : 280
	$RYY_Line_Total	= $E_Dot_RYY - $S_Dot_RYY;		// �� : 50

//==============================================================================//
//	�׷��� ���� �׸��� & �׷����� õ�� ���� ���� (�׷��� ���)
//==============================================================================//
	// �׷��� �׸���
	Header("Content-type : image/png");
	$ImC = ImageCreate($G_Width, $G_Height);

	// �׷��� ��� ���� ����
	$G_BGColor	= ImageColorAllocate($ImC , 255 , 255 , 255);
	$G_BGLine	= ImageColorAllocate($ImC , 112 , 112 , 112);
	$G_FontColor	= ImageColorAllocate($ImC , 242 , 68 , 0);
	$G_SquareColor	= ImageColorAllocate($ImC , 54 , 54  , 54);

	// �⺻ �̹��� & �ٱ��ܰ��� �׸���
	ImageFilledRectangle($ImC, 0, 0, $G_Width, $G_Height, $G_BGColor);

//==============================================================================//
//	�׷��� ���ݼ� �׸���
//==============================================================================//
	ImageString($ImC , 2, $S_Dot_RXX, $S_Dot_RYY - 13, "0Mb", $G_FontColor);
	ImageString($ImC , 2, $RXX_Line_Total - (strlen($maxmark) * 5.5), $S_Dot_RYY - 13, $maxmark, $G_FontColor);

	ImageRectangle($ImC, $S_Dot_RXX, $S_Dot_RYY, $E_Dot_RXX, $E_Dot_RYY, $G_BGLine);		// �簢�� (���)

	$rate = ( $size / $maxsize );
	$square = floor( ( $E_Dot_RXX-3 ) * $rate );
	if ( $square < 1 ) $square = 1;

	ImageFilledRectangle($ImC, $S_Dot_RXX+2, $S_Dot_RYY+2, $S_Dot_RXX+1+$square, $E_Dot_RYY-2, $G_SquareColor);	// ���� ���� ��ä���

//==============================================================================//
//	3���� �׷��� ���
//==============================================================================//
	imagepng($ImC);
	ImageDestroy($ImC);
?>