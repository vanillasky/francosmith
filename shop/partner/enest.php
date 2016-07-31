<?php
include "../lib/library.php";

/* @var $config config */
$cfgEnest = $config->load('enest');

if(!$cfgEnest['mid'] || !$_POST['f_name'] || !$_POST['back_url']) exit;

$ftphost="enest.godo.co.kr";
$ftpuser="mmcftp";
$ftppass="mf930tp";

$conn_id = ftp_connect($ftphost,21);
$login_result = ftp_login($conn_id, $ftpuser, $ftppass) or die();
$destination_file =  $_POST['f_name'];
$source_file =  "../conf/enest_data.txt";

$ret = ftp_nb_put($conn_id, $destination_file, $source_file, FTP_BINARY);
while ($ret == FTP_MOREDATA) $ret = ftp_nb_continue($conn_id);
if ($ret != FTP_FINISHED) $res = false;
else $res = true;

ftp_close($conn_id);
if($res):
?>
<form method="post" action="<?=$_POST['back_url']?>">
	<input type="hidden" name="f_name" value="<?=$_POST['f_name']?>">
</form>
<script type="text/javascript">document.forms[0].submit();</script>
<?endif;?>
