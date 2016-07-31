<?

include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.$cfg[settlePg].php";

echo "<SCRIPT language=JavaScript src='http://pgweb.dacom.net/js/DACOMEscrow.js'></SCRIPT>";
echo "<SCRIPT language=JavaScript>var ResultCode = checkDacomESC ('{$pg[id]}', '{$_GET[ordno]}', '');</SCRIPT>";
echo "<SCRIPT language=JavaScript>if( ResultCode != '10002' ) document.location.replace('{$_GET[ret_path]}')</SCRIPT>";
?>