<?
$db_table = GD_SMS_LOG;
$date = date('Ymd');
if ($_GET['search'] != "yes") if (!$_GET['regdt'][0] && !$_GET['regdt'][1]) $_GET['regdt'][0] = $_GET['regdt'][1] = $date;
if ($_GET['regdt'][0] && $_GET['regdt'][1]) $where[] = "regdt between date_format(".$_GET['regdt'][0].",'%Y-%m-%d 00:00:00') and date_format(".$_GET['regdt'][1].",'%Y-%m-%d 23:59:59')";
if ($_GET['tran_phone']) $where[] = "to_tran like '%".$_GET['tran_phone']."%'";
if ($_GET['status'] == 'send') {
	$where[] = "reservedt <= NOW()";
}
else if ($_GET['status'] == 'res') {
	$where[] = "reservedt > NOW()";
}
else {
	$_GET['status'] = '';
}

$pg = new Page($_GET[page]);
$pg->setQuery($db_table,$where,"-sno");
$pg->exec();

$res = $db->query($pg->query);
while ($data=$db->fetch($res,1)){
	if(!$data['sms_type']){
		$data['sms_type'] = 'sms';
	}
	$loop[] = $data;
}
?>