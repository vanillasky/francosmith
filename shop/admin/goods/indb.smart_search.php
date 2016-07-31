<?
include "../lib.php";
require_once("../../lib/load.class.php");
require_once("../../lib/qfile.class.php");
require_once("../../lib/smartSearch.class.php");

$qfile = new qfile();

if(!$_POST && !$_GET) exit;

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$mode = ($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];

// 색상 쿼리 생성 $val = 색상코드(들) / $codeArray = 색상코드 배열
function setColorQuery($val, $codeArray) {
	if(!$val || !$codeArray || !is_array($codeArray)) return "";

	$rtnQuery = "";

	$arr_val = explode("#", $val);
	for($i = 0, $imax = count($arr_val); $i < $imax; $i++) {
		if($codeArray[strtolower($arr_val[$i])]) {
			if($rtnQuery) $rtnQuery .= ", ";
			$rtnQuery .= "color".$codeArray[strtolower($arr_val[$i])]." = 'y'";
		}
	}

	return $rtnQuery;
}

function getSmartList($field, $category) {
	global $db;

	if($field == 'brandno') {
		$basic_query = "
		SELECT
			d.sno as brandno, d.brandnm,

			IF(d.brandnm REGEXP '^[[:digit:]]', 1,
			IF(d.brandnm REGEXP '^[[:alpha:]]', 3, 2
			)) as sort_flag

		FROM ".GD_CATEGORY." as h
		STRAIGHT_JOIN ".GD_GOODS_LINK." as c
			ON h.category = c.category
		STRAIGHT_JOIN ".GD_GOODS." AS a
			ON a.goodsno = c.goodsno AND a.open = 1
		INNER JOIN ".GD_GOODS_BRAND." as d
			ON a.brandno = d.sno
		WHERE
			h.category LIKE '".$category."%'
		GROUP BY d.sno
		ORDER BY
			sort_flag ASC, d.brandnm ASC
		";
	}
	else {
		$basic_query = "
		SELECT
			a.".$field.",

			IF(a.".$field." REGEXP '^[[:digit:]]', 1,
			IF(a.".$field." REGEXP '^[[:alpha:]]', 3, 2
			)) as sort_flag

		FROM ".GD_CATEGORY." as h
		STRAIGHT_JOIN ".GD_GOODS_LINK." as c
			ON h.category = c.category
		STRAIGHT_JOIN ".GD_GOODS." AS a
			ON a.goodsno = c.goodsno AND a.open = 1
		WHERE
			h.category LIKE '".$category."%'
			AND a.".$field." != ''
		GROUP BY a.".$field."
		ORDER BY
			sort_flag ASC, a.".$field." ASC
		";
	}

	$basic_data = $db->query($basic_query);

	$tmpArray = array();
	while($data = $db->fetch($basic_data)) $tmpArray[] = trim($data[$field]);
	$tmpArray = array_unique($tmpArray);

	return $tmpArray;
}

switch($mode) {
	case 'chkThemeName' :
		$themenm = ($_GET['themenm']) ? $_GET['themenm'] : "";
		list($chk) = $db->fetch("SELECT count(sno) FROM ".GD_GOODS_SMART_SEARCH." WHERE themenm = '$themenm'");
		exit($chk);
		break;

	case 'changeBasic' :
		$db->query("UPDATE ".GD_GOODS_SMART_SEARCH." SET basic = 'n' WHERE basic = 'y'");
		$db->query("UPDATE ".GD_GOODS_SMART_SEARCH." SET basic = 'y', price = 'y' WHERE sno = '".$_GET['basic']."'");
		go('./smart_search.php?'.$_GET['qstr']);
		break;

	case 'setOption' :
		### 설정 저장
		include "../../conf/config.php";

		$cfg = array_map("addslashes",array_map("stripslashes",$cfg));

		$cfg = array_merge($cfg,$_POST);
		unset($cfg['basic']);
		unset($cfg['qstr']);
		unset($cfg['mode']);
		unset($cfg['x']);
		unset($cfg['y']);

		// 기본 테마 설정
		$db->query("UPDATE ".GD_GOODS_SMART_SEARCH." SET basic = 'n' WHERE basic = 'y'");
		$db->query("UPDATE ".GD_GOODS_SMART_SEARCH." SET basic = 'y', price = 'y' WHERE sno = '".$_POST['basic']."'");

		$qfile->open("../../conf/config.php");
		$qfile->write("<? \n");
		$qfile->write("\$cfg = array( \n");
		foreach($cfg as $k => $v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();

		go('./smart_search.php?'.$_POST['qstr']);

		break;

	case 'regTheme' :
	case 'modTheme' :

		$category = '';
		$maker_list = '';
		$origin_list = '';
		$brandno_list = '';
		$ex_list = '';
		$opt_list = '';

		for($i = 0; $i < 4; $i++) if($_POST['cate'][$i]) $category = $_POST['cate'][$i];

		/*-----------------------------------------------------------*/
		/*    - 상품 기본정보 저장                                   */
		/*-----------------------------------------------------------*/

		if($_POST['basic_menu']) {
			$maker = $origin = $brandno = array();

			$_POST['price'] = (in_array('price',$_POST['basic_menu'])) ? 'y' : 'n'; // 가격
			if(in_array('maker',$_POST['basic_menu'])) { // 제조사
				$maker = getSmartList('maker', $category);
				if($maker) $maker_list = smartSearch::getThemeValueString($maker, _OPT_PIPE_);
			}
			if(in_array('origin',$_POST['basic_menu'])) {
				$origin = getSmartList('origin', $category);
				if($origin) $origin_list = smartSearch::getThemeValueString($origin, _OPT_PIPE_);
			}
			if(in_array('brandno',$_POST['basic_menu'])) {
				$brandno = getSmartList('brandno', $category);
				if($brandno) $brandno_list = smartSearch::getThemeValueString($brandno, _OPT_PIPE_, false);	// 브랜드 번호이므로 정렬하지 않음
			}
		}

		/*-----------------------------------------------------------*/
		/*    - 상품 추가정보 저장                                   */
		/*-----------------------------------------------------------*/

		if($_POST['goods_add_menu']) {
			// 추가 정보 저장
			foreach($_POST['goods_add_menu'] as $b_k => $b_v) {

				$ex_array = array();
				$query = sprintf("
				SELECT
					g.ex_title, g.ex1, g.ex2, g.ex3, g.ex4, g.ex5, g.ex6

				FROM ".GD_CATEGORY." as ct
				STRAIGHT_JOIN ".GD_GOODS_LINK." AS gl
					ON ct.category = gl.category

				STRAIGHT_JOIN ".GD_GOODS." AS g
					ON gl.goodsno = g.goodsno AND g.open = 1

				WHERE
					ct.category like '%s%%' AND g.ex_title LIKE '%%%s%%'


				", $db->_escape($category), $db->_escape($b_v));

				$query = $db->query($query);
				while($data = $db->fetch($query)) {
					$data_array = explode('|', $data[0]);
					$data_key = array_search($b_v, $data_array);
					if ($v = trim($data['ex'.($data_key + 1)]))
						$ex_array[] = $v;
				}

				if($ex_list) $ex_list .= PHP_EOL;
				$ex_list .= $b_v._OPT_PIPE_._OPT_PIPE_;
				$ex_list .= smartSearch::getThemeValueString($ex_array, _OPT_PIPE_);

				/* 저장 예제
				추가정보1의이름|^|^추가정보1의값1|^추가정보1의값2|^추가정보1의값3|^추가정보1의값4|^추가정보1의값5...
				추가정보2의이름|^|^추가정보2의값1|^추가정보2의값2|^추가정보2의값3|^추가정보2의값4|^추가정보2의값5...
				추가정보3의이름|^|^추가정보3의값1|^추가정보3의값2|^추가정보3의값3|^추가정보3의값4|^추가정보3의값5...
				*/
			}
		}

		/*-----------------------------------------------------------*/
		/*    - 상품 가격옵션 저장                                   */
		/*-----------------------------------------------------------*/

		if($_POST['goods_option_menu']) {

			foreach($_POST['goods_option_menu'] as $b_k => $b_v) {
				$opt_array = array();

				$query = sprintf("
				SELECT
					g.optnm, go.opt1, go.opt2

				FROM ".GD_CATEGORY." as ct
				STRAIGHT_JOIN ".GD_GOODS_LINK." AS gl
					ON ct.category = gl.category

				STRAIGHT_JOIN ".GD_GOODS." AS g
					ON gl.goodsno = g.goodsno AND g.open = 1

				STRAIGHT_JOIN ".GD_GOODS_OPTION." AS go
					ON gl.goodsno = go.goodsno and go_is_deleted <> '1' and go_is_display = '1'

				WHERE
					ct.category like '%s%%' AND g.optnm LIKE '%%%s%%'

				GROUP BY go.opt1, go.opt2

				", $db->_escape($category), $db->_escape($b_v));

				$query = $db->query($query);
				while($data = $db->fetch($query)) {
					$data_array = explode('|',$data[0]);
					$data_key = array_search($b_v,$data_array);
					if ($v = trim($data['opt'.($data_key+1)]))
						$opt_array[] = $v;
				}

				if($opt_list) $opt_list .= PHP_EOL;
				$opt_list .= $b_v._OPT_PIPE_._OPT_PIPE_;
				$opt_list .= smartSearch::getThemeValueString($opt_array, _OPT_PIPE_);

				/* 저장 예제
				옵션정보1의이름|^|^옵션정보1의값1|^옵션정보1의값2|^옵션정보1의값3|^옵션정보1의값4|^옵션정보1의값5...
				옵션정보2의이름|^|^옵션정보2의값1|^옵션정보2의값2|^옵션정보2의값3|^옵션정보2의값4|^옵션정보2의값5...
				*/
			}
		}

		/*-----------------------------------------------------------*/
		/*  * gd_goods_smart_search 저장(테마생성)                   */
		/*-----------------------------------------------------------*/

		if($_POST['themenm']) {
			$maker_list = $db->_escape($maker_list);
			$origin_list = $db->_escape($origin_list);
			$brandno_list = $db->_escape($brandno_list);
			$ex_list = $db->_escape($ex_list);
			$opt_list = $db->_escape($opt_list);
			$_POST['themenm'] = $db->_escape($_POST['themenm']);

			if($mode == "modTheme" && $_POST['sno']) {
				$sql = "UPDATE ".GD_GOODS_SMART_SEARCH." SET themenm = '".$_POST['themenm']."', category = '$category', price = '".$_POST['price']."', color = '".$_POST['color']."', maker = '$maker_list', origin = '$origin_list', brandno = '$brandno_list', ex = '$ex_list', opt = '$opt_list', ssOrder = '".$_POST['ssOrder']."', updatedt = NOW() WHERE sno = '".$_POST['sno']."'";
				$db->query($sql);
				$cSmart_search = $_POST['sno'];
			}
			else {
				$sql = "INSERT INTO ".GD_GOODS_SMART_SEARCH." SET themenm = '".$_POST['themenm']."', category = '$category', price = '".$_POST['price']."', color = '".$_POST['color']."', maker = '$maker_list', origin = '$origin_list', brandno = '$brandno_list', ex = '$ex_list', opt = '$opt_list', ssOrder = '".$_POST['ssOrder']."', regdt = NOW()";
				$db->query($sql);
				$cSmart_search = $db->lastID();
			}
		}

		if($mode == 'regTheme') {
			echo '
			<script language="javascript">
				if(confirm("등록이 완료 되었습니다.\\n\\n[ 카테고리관리 > SMART검색 테마 등록 ]\\n설정페이지로 이동하시겠습니까?")) {
					location.replace("../goods/category.php?ifrmScroll=1&category='.$category.'");
				}
				else {
					location.href = "../goods/smart_search.php";
				}
			</script>';
		}
		else if($mode == 'modTheme') {
			msg('수정되었습니다.', './smart_search.php?'.$_POST['queryString']);
		}

		break;

	case 'copTheme' :
		$sno = ($_GET['no']) ? $_GET['no'] : "";
		$oData = $db->fetch("SELECT themenm, category, price, color, maker, origin, brandno, ex, opt, ssOrder FROM ".GD_GOODS_SMART_SEARCH." WHERE sno = '$sno'");

		if(is_array($oData)) {
			// 테마복사
				$tmp_rs = $db->query("SELECT themenm FROM ".GD_GOODS_SMART_SEARCH." WHERE themenm LIKE '".$oData['themenm']."-%'");
				while($tmpList = $db->fetch($tmp_rs)) {
					$tmpThemeName = str_replace($oData['themenm']."-", "", $tmpList['themenm']);
					if($tmpThemeName == (int)$tmpThemeName) $tmpArray[] = (int)$tmpThemeName;
				}

				if(count($tmpArray)) rsort($tmpArray);
				$copiedThemeName = $oData['themenm']."-".($tmpArray[0] + 1);

				$oData['maker'] = $db->_escape($oData['maker']);
				$oData['origin'] = $db->_escape($oData['origin']);
				$oData['brandno'] = $db->_escape($oData['brandno']);
				$oData['ex'] = $db->_escape($oData['ex']);
				$oData['opt'] = $db->_escape($oData['opt']);
				$copiedThemeName = $db->_escape($copiedThemeName);

				$db->query("INSERT INTO ".GD_GOODS_SMART_SEARCH." SET themenm = '".$copiedThemeName."', category = '".$oData['category']."', price = '".$oData['price']."', color = '".$oData['color']."', maker = '".$oData['maker']."', origin = '".$oData['origin']."', brandno = '".$oData['brandno']."', ex = '".$oData['ex']."', opt = '".$oData['opt']."', ssOrder = '".$oData['ssOrder']."', regdt = NOW()");

				$newThemeno = $db->_last_insert_id();

			msg('해당 테마를 복사했습니다.', './smart_search.php');
		}
		else msg("이미 지워졌거나 존재하지 않는 테마 입니다.", -1);
		break;

	case 'delTheme' :
		$sno = ($_GET['no']) ? $_GET['no'] : "";
		$db->query("DELETE FROM ".GD_GOODS_SMART_SEARCH." WHERE sno = '$sno'");
		$db->query("UPDATE ".GD_CATEGORY." SET themeno = NULL WHERE themeno = '$sno'");

		msg('테마가 삭제되었습니다.', './smart_search.php');
		break;
}
?>
