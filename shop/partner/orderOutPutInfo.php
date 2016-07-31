<?
	include dirname(__FILE__)."/../lib/library.php";
	$where = array();
	$where[] = "((step in (4) and step2 = '')) and inflow='openstyleOutlink'";
	
	if($_GET['startDt']){
		$chkStartDate = Core::helper('Date')->min($_GET['startDt']);
	}

	if($_GET['endDt']){
		$chkEndDate = Core::helper('Date')->max($_GET['endDt']);
	}
	
	if($_GET['startDt'] && $_GET['endDt']){
		$orddt = "orddt between '$chkStartDate' and '$chkEndDate'";
		$cdt = "cdt between '$chkStartDate' and '$chkEndDate'";
		$ddt = "ddt between '$chkStartDate' and '$chkEndDate'";

	}

	if(!$_GET['startDt'] && !$_GET['endDt']){
		// 서비스 시작일
		$orddt = "orddt >='2009-12-03 00:00:00'";
		$cdt = "cdt >='2009-12-03 00:00:00'";
		$ddt = "ddt >='2009-12-03 00:00:00'";
	}
	
	if ($where) $where_query = " and ".implode(" and ",$where);
	
	///////////////////////////////////////////////////////////////////////////////////////

$r_yoil = array("일","월","화","수","목","금","토");

$year = ($_POST[year]) ? $_POST[year] : date("Y");
$month = ($_POST[month]) ? sprintf("%02d",$_POST[month]) : date("m");
$interestOp = ($_POST['interestOp']) ? $_POST['interestOp'] : 'b';

$selected[year][$year] = "selected";
$selected[month][$month] = "selected";
$selected['interestOp'][$interestOp] = 'selected';

$date = $year."-".sprintf("%02d",$month);
$last = date("t",strtotime($date."-01"));
$query = "
select * from
	".GD_ORDER."
where
	$orddt
	and step2 < 40
	$where_query
";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	$day = str_replace("-","",substr($data[orddt],0,10));
	$cnt[o][$day]++;
	$sum[o][$day] += $data[prn_settleprice];
}

$query = "
select * from
	".GD_ORDER."
where
	$cdt
	and step > 0
	and step2 < 40
";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	list ($supply) = $db->fetch("select sum(supply*ea) from ".GD_ORDER_ITEM." where ordno=$data[ordno] and istep<40");
	$day = str_replace("-","",substr($data[cdt],0,10));
	$cnt[c][$day]++;
	$sum[c][$day] += $data[prn_settleprice];
	$suppsum[c][$day] += $supply;
	$delivery[c][$day] += $data['delivery'];

	if ($interestOp == 'b'){
		$interest['c'][$day] += $data['prn_settleprice'] - $supply - $data['delivery'];
	}
	else {
		$interest['c'][$day] += $data['prn_settleprice'] - $supply;
	}
}

$query = "
select * from
	".GD_ORDER."
where
	$ddt
	and step > 0
	and step2 < 40
";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	$day = str_replace("-","",substr($data[ddt],0,10));
	$cnt[d][$day]++;
	$sum[d][$day] += $data[prn_settleprice];
}

?>
<root>
	<cnto><?=number_format(@array_sum($cnt[o]))?></cnto>
	<sumo><?=number_format(@array_sum($sum[o]))?></sumo>
	<cntc><?=number_format(@array_sum($cnt[c]))?></cntc>
	<sumc><?=number_format(@array_sum($sum[c]))?></sumc>
	<cntd><?=number_format(@array_sum($cnt[d]))?></cntd>
	<sumd><?=number_format(@array_sum($sum[d]))?></sumd>
	<suppsumc><?=number_format(@array_sum($suppsum[c]))?></suppsumc>
	<delivery><?=number_format(@array_sum($delivery[c]))?></delivery>
	<interest><?=number_format(@array_sum($interest[c]))?></interest>
</root>
