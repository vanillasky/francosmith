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
//	��� ���ϱ�
//==============================================================================//
	if ( preg_match( "/^self/i", $godo[ecCode] ) ){ // eNamoo ������
		$size = getDu('root');
	}
	else { // �Ӵ���
		$size = getDu('disk');
	}
	$sizeStr	= byte2str($size);
	$size 		= round($size / 1024);			// K����
	$size 		= round($size / 1024,2);		// M����

	$maxsize = $godo[maxDisk] + $godo[diskGoods]; // M�⺻����
	$maxmark = byte2str(mb2byte($godo[maxDisk] + $godo[diskGoods])) . 'b'; // ǥ��

	if ( !$size ) $size = 1;
	if ( !$maxsize ) $maxsize = 1;
	
	# ������ �׷��� �̹����� ��ü ũ��
	$G_Width = $_GET[G_Width];
	$G_Height = $_GET[G_Height];
	if ( $G_Width == '' ) $G_Width	= 144;
	if ( $G_Height == '' ) $G_Height	= 8;
	
	# �Ϲ� �׷��� ����
	$rate = ( $size / $maxsize );
	$square = floor( ( $G_Width - 4 ) * $rate );
	if ( $square < 1 ) $square = 1;
	elseif ( $square > $G_Width ) $square = $G_Width;
?>