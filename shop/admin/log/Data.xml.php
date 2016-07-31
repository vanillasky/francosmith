<? 

header ("Cache-Control: no-cache, must-revalidate"); 
header ("Pragma: no-cache");

include "../../lib/library.php";

//$db->connect("cody");

$day = ($_GET[day]) ? $_GET[day] : date("Ymd");
switch ($_GET[mode]){
	case 1: 
		$query = "select * from ".MINI_COUNTER." where day='$day'"; break;
	case 2:
		$query = "select right(day,2)+0 k,uniques v from ".MINI_COUNTER." where day like '".substr($day,0,6)."%'"; break;
	case 3:
		$query = "select right(day,2)+0 k,pageviews v from ".MINI_COUNTER." where day like '".substr($day,0,6)."%'"; break;
	case 4:
		$query	= "select * from ".MINI_COUNTER." where day=0"; break;
}

$res = $db->query($query);
while ($data=$db->fetch($res,1)) $loop[] = $data;

switch ($_GET[mode]){
	case 1: case 4: 
		$ret = array_slice($loop[0],3,24); break;
	case 2: case 3:
		for ($i=1;$i<=date("t",strtotime($day));$i++) $ret[$i] = 0;
		foreach ($loop as $v) $ret[$v[k]] = $v[v];
		break;
}

$max = 0;
foreach ($ret as $k=>$v) $max = ($max<=$v) ? $v : $max;

$total = array_sum($ret);

?>
<graph>
<?
foreach ($ret as $k=>$v){ 
	$color = ($max!=$v) ? "#F56E00" : "#CC371E";
	if ($_GET[mode]==1 || $_GET[mode]==4) $k = substr($k,1);
	if ($_GET[mode]==4) $v = round($v * 100 / $total,1)."%";
?>
<set name='<?=$k?>' value='<?=$v?>' color='<?=$color?>' />
<? } ?>
</graph>