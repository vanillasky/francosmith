<?
require_once('../lib/todayshop_cache.class.php');
$cache = new todayshop_cache();

include "../lib/library.php";
//include "../_header.php";

$todayShop = Core::loader('todayshop');

switch ($_GET['mode']) {
	case 'todaygoods': {
		// 상품페이지 상품요약정보
		$tgsno = $_GET['tgsno'];
		if (!$tgsno) exit;

		$data = $todayShop->getGoodsSummary($tgsno);
		if (is_array($data) && empty($data) === false) {
			foreach($data as $val) {
				foreach($val as $key2 => $val2) {
					$tmp[] = $key2.':"'.preg_replace(array('/\r/','/\n/'), '', nl2br($val2)).'"';
				}
				$result[] = '{'.implode(',', $tmp).'}';
				unset($tmp);
			}
			unset($data);

			$sms = Core::loader('sms');
			$smsCnt = preg_replace('/[^0-9-]*/', '', $sms->smsPt);
			unset($sms);

			$jsonData = 'data:['.implode(',', $result).']';
			$jsonData .= ',smsCnt:"'.$smsCnt.'",useSMS:"'.$todayShop->cfg['useSMS'].'",useEncor:"'.$todayShop->cfg['useEncor'].'",useGoodsTalk:"'.$todayShop->cfg['useGoodsTalk'].'",';
		}
		else {
			$jsonData = 'data:[{fakestock:"0",buyercnt:"0",optno:"0",opt1:"",opt2:"",price:"0",stock:"0",consumer:"0",runout:"1"}],smsCnt:"0",useEncor:"n",useGoodsTalk:"n",';
		}
		break;
	}
	case 'todaylist': {
		// 상품리스트 상품요약정보
		$category = $_GET['category'];
		$year = $_GET['year'];
		$month = $_GET['month'];
		$day = $_GET['day'];
		if (!$category || !$year || !$month || !$day) exit;

		$data = $todayShop->getListSummary($year.'-'.$month.'-'.$day, $category);
		if (is_array($data) && empty($data) === false) {
			foreach($data as $val) {
				foreach($val as $key2 => $val2) {
					$tmp[] = $key2.':"'.preg_replace(array('/\r/','/\n/'), '', nl2br($val2)).'"';
				}
				$result[] = '{'.implode(',', $tmp).'}';
				unset($tmp);
			}
			unset($data);

			$sms = Core::loader('sms');
			$smsCnt = preg_replace('/[^0-9-]*/', '', $sms->smsPt);
			unset($sms);

			$jsonData = 'data:['.implode(',', $result).'],smsCnt:"'.$smsCnt.'",useSMS:"'.$todayShop->cfg['useSMS'].'",';
		}
		break;
	}
	case 'calendar': {
		// 엥콜요청 가능여부
		$jsonData = 'useEncor:"'.$todayShop->cfg['useEncor'].'",';
		break;
	}
}

$member = 'n';
if ($sess){
	$query = "
	SELECT * FROM
		".GD_MEMBER." a
		LEFT JOIN ".GD_MEMBER_GRP." b ON a.level=b.level
	WHERE
		m_no='$sess[m_no]'
	";
	$res = $db->fetch($query,1);
	if ($res['m_no']) $member = 'y';
	unset($res);
}

ob_start();
	echo '{'.$jsonData.'member:"'.$member.'"};';
	$_html = ob_get_contents();
ob_end_clean();

$cache->setCache($_html);
?>
