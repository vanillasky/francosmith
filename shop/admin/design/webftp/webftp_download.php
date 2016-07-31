<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005, Flyfox All right reserved.
@파일내용: 파일 다운로드
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/

@include_once dirname( __file__ ) . '/conf.php';


$filename = $webftp->ftp_path . $_GET['filename'];


//--------------------------------------------

Header("Content-type: application/octet-stream");
header("Content-disposition:attachment;filename=" . str_replace( dirname( $filename ) . '/', "", $filename ) );
header("Content-length:" . fileSize( $filename ) );
Header("Content-Transfer-Encoding: binary");
Header("Pragma: no-cache");
Header("Expires: 0");

readFile( $filename );
?>