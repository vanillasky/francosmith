<?

include "../lib.php";
include "../../lib/imgHostReplace.class.php";
require_once "../../lib/load.class.php";
$Goods = Core::loader('Goods');

$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

switch ( $mode ){

	case "ftpVerify":

		header("Content-type: text/html; charset=euc-kr");

		# 경고출력방지
		# (register_globals≠enabled 경우 세션 extention은 전역변수를 데이타로 인지않는다)
		$_register_globals = (bool) ini_get('register_globals');
		if (!$_register_globals) {
			if (function_exists('ini_set')) {
				ini_set('session.bug_compat_42', 1);
				ini_set('session.bug_compat_warn', 0);
			}
		}

		$_POST['domain'] = $_POST['userid'] . '.godohosting.com';
		$imgHost = new imgHost($_POST);
		$imgHost->_connector();
		$imgHost->_destruct();

		$ftpConf = array('domain' => $_POST['domain'], 'userid' => $_POST['userid'], 'pass' => $_POST['pass']);
		$ftpConf['pass'] = encode($ftpConf['pass'],1);
		$ftpConf = serialize($ftpConf);
		session_register("ftpConf");
		echo "true";
		exit;

		break;

	case "putReplace":

		header("Content-type: text/html; charset=euc-kr");

		$result = array();
		$imgHost = new imgHost($_SESSION['ftpConf']);
		$goods = explode(",", $_POST['goods']);
		foreach ($goods as $no)
		{
			$data = $db->fetch("select goodsno, longdesc from gd_goods where goodsno='{$no}'");
			if (trim($data['longdesc']) != '')
			{
				$longdesc = addslashes($imgHost->replace($data['longdesc']));
				$res = $db->query("update gd_goods set longdesc='{$longdesc}' where goodsno='{$data['goodsno']}'");
				if ($res === true)
				{
					$cnt = $imgHost->imgStatus($longdesc);
					$result[] = $data['goodsno'] .":". intval($cnt['in']);
					### 업데이트 일시
					$Goods -> update_date($no);
				}
			}
		}
		$imgHost->_destruct();

		include dirname(__FILE__)."/../../lib/json.class.php";
		$json = new Services_JSON();
		$output = $json->encode($result);

		echo $output;
		exit;

		break;

}
?>
