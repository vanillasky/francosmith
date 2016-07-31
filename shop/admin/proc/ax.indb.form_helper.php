<?
include "../lib.php";

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
}

$mode = $_POST['mode'];
$name = $_POST['name'];
$value= $_POST['value'];
$key= $sess['m_id'].preg_replace('/[^a-zA-Z0-9]/','_',$_POST['key']);

list($_data) = $db->fetch("SELECT `value` FROM gd_env WHERE `category` = 'form_helper' AND `name` = '$name'");
$data = $_data ? unserialize($_data) : array();

switch ($mode) {

	case 'get':
		echo $data[$key];
		exit;
		break;
	case 'set':
		// 저장에서 제외할 필드 처리
		
		if ($name == 'searchCondition') {
			$_tmp = array();
			parse_str($value, $_tmp);		
			unset($_tmp['sword'],$_tmp['sgword']);
			$_data = http_build_query($_tmp);		
		}
		else {
			$_data = $value;			
		}
		
		$data[$key] = $_data;

		$_data = serialize($data);

		$query = "INSERT INTO gd_env SET `category` = 'form_helper', `name` = '$name', `value` = '".mysql_real_escape_string($_data)."' ON DUPLICATE KEY UPDATE `value` = '".mysql_real_escape_string($_data)."'";
		$db->query($query);
		exit('ok');
		break;
}
?>