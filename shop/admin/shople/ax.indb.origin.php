<?
include "../lib.php";
require_once ('./_inc/config.inc.php');
$shople = Core::loader('shople');

$depth = isset($_POST['depth']) ? $_POST['depth'] : 1;
$value = isset($_POST['value']) ? iconv('UTF-8','EUC-KR',trim($_POST['value'])) : '';

if ($depth == 1) {
	$rs = $db->query("SELECT DISTINCT `area` as `name`, `area` as `value` FROM ".GD_SHOPLE_ORIGIN_CODE." WHERE `country` = '".$_spt_ar_country[ $value ]."'");
}
else {
	$rs = $db->query("SELECT `name`, `value` FROM ".GD_SHOPLE_ORIGIN_CODE." WHERE `area` = '$value'");

}

$result = array();
while ($row = $db->fetch($rs,1)) {
	$result[] = $row;
}

echo $shople->json_encode($result);
?>
