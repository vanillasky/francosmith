<? 

header ("Cache-Control: no-cache, must-revalidate"); 
header ("Pragma: no-cache");

include "../lib/library.php";
include "../conf/config.php";

$loccd = $_GET[idx];
$url = "/".$cfg[rootDir]."/data/skin/".$cfg[tplSkin]."/img/banner/";

$query = "
select * from
	".GD_BANNER."
where
	tplSkin = '$cfg[tplSkin]'
	and loccd = '$loccd'
order by sort
";

$res = $db->query($query);

?>

//이동스피드: 1~5 /소숫점 불가능
&goods_speed=2&

//진동크기: 1~5 /소숫점 불가능
&goods_quake=1&

//이동시간간격: 소숫점 가능
&move_time=3&

<? 
$idx = 0;
while ($data=$db->fetch($res)){ $idx++;
	if ($data[linkaddr]=="nolink") $data[linkaddr] = "";
?>
&imagepath_<?=$idx?>=<?=$url.$data[img]?>&<?="\n"?>
&excURL_<?=$idx?>=<?=urlencode($data[linkaddr])?>&<?="\n"?>
<? } ?>