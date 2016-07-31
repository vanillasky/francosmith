<?
error_reporting(0);

if ($fp = @fopen($_GET['path'], "r")) {

	setlocale(LC_CTYPE, 'ko_KR.eucKR');

	header( 'Content-type: application/vnd.ms-excel' );
	header( 'Content-Disposition: attachment; filename='.$_GET['name'] );
	header( 'Content-Description: PHP5 Generated Data' );

	while (!feof($fp)) {
		echo fread($fp, 8192);
	}
	fclose($fp);

}

@unlink($_GET['path']);
?>