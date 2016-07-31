<?

list( $disk_errno, $disk_msg ) = disk();

if ( $disk_errno == '001' ) $disk_img = "http://www.godo.co.kr/userinterface/img/disk_guide_add.gif";
else if ( $disk_errno == '002' ) $disk_img = "http://www.godo.co.kr/userinterface/img/disk_guide_date.gif";

if ( !empty( $disk_errno ) ){
echo <<<ENDH
<script>var call_file_disabled = true;</script>
<script language="javascript"><!--
function disk_apply(){
	if ( document.location.href.match( /webftp_upload.php|webftp_gdcopy.php/gi ) ){
		opener.location.href="../../basic/disk.pay.php";
		opener.focus();
		window.close();
	}
	else
		document.location.href="../basic/disk.pay.php";
}
--></script>

<div style="margin-bottom:10; text-align:center;"><a href="javascript:disk_apply();"><img src="{$disk_img}" border=0></a></div>
ENDH;
}
?>