<?
include "../_header.php";

$tgsno = $_GET['tgsno'];
$page = $_GET['page'];
if (!$tgsno) exit;

$todayShop = Core::loader('todayshop');

// 상품토크
$talk = $todayShop->getTalk($tgsno, $page);
foreach($talk as $val) {
	foreach($val as $key2 => $val2) {
		$tmp[] = $key2.':"'.preg_replace(array('/\r/','/\n/'), '', nl2br($val2)).'"';
	}
	$result[] = '{'.implode(',', $tmp).'}';
	unset($tmp);
}
echo '{talk : ['.implode(',', $result).'],';
unset($talk, $result);

$pager = $todayShop->getTalkPager($tgsno, $page);
foreach($pager as $key => $val) {
	if (is_array($val)) {
		foreach($val as $key2 => $val2) {
			foreach($val2 as $key3 => $val3) {
				$tmp[] = $key3.':"'.$val3.'"';
			}
			$tmp2[] = '{'.implode(',', $tmp).'}';
			unset($tmp);
		}
		$result[] = 'page : ['.implode(',', $tmp2).']';
	}
	else {
		$result[] = $key.':"'.$val.'"';
	}
}
echo 'pager : {'.implode(',', $result).'}};';
unset($pager, $result);
?>
