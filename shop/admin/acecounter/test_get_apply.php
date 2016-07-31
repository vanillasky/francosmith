<?

$shopno = $_GET['shopno']; 
$uid = $_GET['uid']; 

$gcode = "BP4G3518175199"; 
$status_use = "Y"; 
$ver = 'c';
$msg = '신청처리완료'; 
$start = '2010-02-20';
$end = '2010-03-20'; 

echo $uid."|".$gcode."|".$status_use."|".$ver."|".$msg."|".$start."|".$end."|end"; 

?>