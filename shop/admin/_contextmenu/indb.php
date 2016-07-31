<?
include "../lib.php";
// ajax 호출 페이지 utf-8 인코딩 必
$_REQUEST = iconv_recursive('UTF-8','EUC-KR',$_REQUEST);

$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';
$name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
$url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';
$target = isset($_REQUEST['target']) ? $_REQUEST['target'] : '_self';
$sno = isset($_REQUEST['sno']) ? $_REQUEST['sno'] : '';

switch ($mode) {

	case 'del' :
		if ($sno == '') exit;

		$query = " DELETE FROM ".GD_CONTEXTMENU." WHERE sno = '".$sno."' AND m_no = '".$sess['m_no']."' ";
		$rs = $db->query($query);
		break;
	case 'mod':

		if ($sno == '' || $name == '' || $url == '') exit;
		$query = "
		UPDATE ".GD_CONTEXTMENU." SET
			name = '".mysql_real_escape_string($name)."',
			url = '".mysql_real_escape_string($url)."',
			target = '".mysql_real_escape_string($target)."'
		WHERE sno = '".$sno."' AND m_no = '".$sess['m_no']."'
		";
		$rs = $db->query($query);

		break;
	case 'add':
	default :
		if ($name == '' || $url == '') exit;

		$query = "
		INSERT INTO ".GD_CONTEXTMENU." SET
			m_no = '".$sess['m_no']."',
			name = '".mysql_real_escape_string($name)."',
			url = '".mysql_real_escape_string($url)."',
			target = '".mysql_real_escape_string($target)."'
		";
		$rs = $db->query($query);
		break;
}
if (!$rs) exit;

$query = "SELECT name, url, target FROM ".GD_CONTEXTMENU." WHERE m_no = '".$sess['m_no']."'";
$rs = $db->query($query);
$menu = array();
while ($row = $db->fetch($rs,1)) {
	$menu[] = $row;
}

echo gd_json_encode($menu);
?>
