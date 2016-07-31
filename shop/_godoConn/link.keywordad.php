<?
include "../lib/library.php";

$keywordad = array();
$ad_value = array('Y','N');	// 데이터타입
$ad_type = array('naverad','overture');	// 키워드 종류

$keywordad = $_POST;

### 유효성체크
if(!in_array($keywordad['naverad'],$ad_value)) exit;
if(!in_array($keywordad['overture'],$ad_value)) exit;

foreach($keywordad as $k => $v){

	if(!in_array($k,$ad_type)) continue;	// 키워드 종류 확인

	list($oldValue) = $db->fetch("SELECT value FROM gd_env WHERE category = 'keywordad' AND name='$k'"); // 기존 데이터 체크
	if(!$oldValue) $db->query("INSERT INTO gd_env SET category = 'keywordad', name='$k', value='$v'");
	else {
		$db->query("UPDATE gd_env SET value='$v' WHERE category = 'keywordad' AND name='$k'");
	}

	if(mysql_error()){ echo "ERR"; exit; }
}

echo "OK";

?>