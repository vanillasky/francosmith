<?
include "../lib.php";
$sno = $_REQUEST['sno'];
$qr_type = $_REQUEST['qr_type'];

if($qr_type == "")$qr_type= 'etc';

if($_REQUEST['mode']=="del"){
	$db->query("delete from ".GD_QRCODE." where sno =  ".$sno);	
}else{
	if(!empty($sno)){
		$db->query("update ".GD_QRCODE." set  qr_type='$qr_type' ,qr_string = '".$_POST['contents']."', qr_name = '".$_POST['qr_name']."', qr_size='".$_POST['qr_size']."', qr_version= '".$_POST['qr_version']."', useLogo = '".$_POST['useLogo']."', regdt	= now() where sno =  ".$sno);
	}else{
		$db->query($qry = "insert into ".GD_QRCODE." set  qr_type='$qr_type' ,qr_string = '".$_POST['contents']."', qr_name = '".$_POST['qr_name']."', qr_size='".$_POST['qr_size']."', qr_version= '".$_POST['qr_version']."', useLogo = '".$_POST['useLogo']."', regdt	= now()");
		$sno = $db->lastID();
	}
}

if(empty($_POST['returnUrl'])){
	$returnUrl = "qr_list.php";
}else{
	$returnUrl = $_POST['returnUrl']."?sno=".$sno;
}
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
	parent.location.replace("<?=$returnUrl?>");
//-->
</SCRIPT>


