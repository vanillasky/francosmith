<?
include "../lib.php";
$db -> err_report = 1;


$rs = $db->query("SELECT * FROM ".GD_DOPT_EXTEND." order by sno desc");

$result = array();
while ($row = $db->fetch($rs,1)) {


	if(strlen($row[title]) > 20){
		$row[title] = strcut($row[title],20);
	}

	$row[option] = !empty($row[option]) ? unserialize($row[option]) : $_tmp;
	$row[option] = str_replace("\n","",gd_json_encode($row[option]));	// php4 환경이므로 임시 함수 추가 하였음.

	$_row[title] = $row[title];
	$_row[option] = $row[option];
	$_row[sno] = $row[sno];

	$result[] = $_row;
}

echo gd_json_encode($result);
?>
