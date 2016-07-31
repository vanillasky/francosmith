<?
include "../lib.php";
require_once ('./_inc/config.inc.php');

$shople = Core::loader('shople');

$mode		= isset($_POST['mode']) ? $_POST['mode'] : '';

$keyword	= isset($_POST['keyword']) ? iconv('UTF-8','EUC-KR',trim($_POST['keyword'])) : '';	//
$depth		= isset($_POST['depth']) ? $_POST['depth'] : 1;
$dispno		= isset($_POST['dispno']) ? $_POST['dispno'] : '';
$catno		= isset($_POST['catno']) ? $_POST['catno'] : '';
$category	= isset($_POST['category']) ? $_POST['category'] : '';
$samelow	= isset($_POST['samelow']) ? $_POST['samelow'] : '';

$result		= array();

if ($mode == 'search') {

	if ($keyword == '') break;

		$query = "
			SELECT

				SC1.dispno as dp1_dispno,
				SC1.name as dp1_name,
				SC1.depth as dp1_depth,

				SC2.dispno as dp2_dispno,
				SC2.name as dp2_name,
				SC2.depth as dp2_depth,

				SC3.dispno as dp3_dispno,
				SC3.name as dp3_name,
				SC3.depth as dp3_depth,

				SC4.dispno as dp4_dispno,
				SC4.name as dp4_name,
				SC4.depth as dp4_depth

			FROM	 ".GD_SHOPLE_CATEGORY." AS SC1

			LEFT JOIN ".GD_SHOPLE_CATEGORY." AS SC2
			ON SC1.dispno = SC2.p_dispno

			LEFT JOIN ".GD_SHOPLE_CATEGORY." AS SC3
			ON SC2.dispno = SC3.p_dispno

			LEFT JOIN ".GD_SHOPLE_CATEGORY." AS SC4
			ON SC3.dispno = SC4.p_dispno

			WHERE
					SC1.depth = 1 AND (
					SC3.name like '%$keyword%' OR
					SC4.name like '%$keyword%'
					)

			ORDER BY SC1.dispno, SC2.dispno, SC3.dispno, SC4.dispno
		";

		$rs = $db->query($query);

		$_row = array();

		while($row = $db->fetch($rs,1)) {

			$_row['full_name'] = '';
			$_row['full_dispno'] = '';

			for ($i=1;$i<=4;$i++) {

				if (!empty($row['dp'.$i.'_dispno'])) {
					$_row['full_name'] .= ($_row['full_name'] == '') ? $row['dp'.$i.'_name'] : ' > '.$row['dp'.$i.'_name'];
					$_row['full_dispno'] .= ($_row['full_dispno'] == '') ? $row['dp'.$i.'_dispno'] : '|'.$row['dp'.$i.'_dispno'];
				}
			}

			$result[] = $_row;
		}

}
// eof mode == search
else if ($mode == 'get') {

	if ($depth == '') break;

		$query = "
			SELECT
				depth, name, dispno, p_dispno , updated
			FROM ".GD_SHOPLE_CATEGORY."
			WHERE depth = '$depth'
		";

		if ($dispno) $query .= " AND p_dispno = '$dispno'";
		$query .= " ORDER BY dispno ";
		$rs = $db->query($query);

		$result = array();
		while($row = $db->fetch($rs,1)) {
			$result[] = $row;
		}

}
// eof mode == get
elseif ($mode == 'save') {

	if ($category == '') break;

	if ($samelow == 'Y') {
		$WHERE = " WHERE category like '".$category."%' ";
	}
	else {
		$WHERE = " WHERE category = '".$category."'";
	}

	$query = "DELETE FROM ".GD_SHOPLE_CATEGORY_MAP." ".$WHERE;
	$db->query($query);

	$query = "INSERT INTO ".GD_SHOPLE_CATEGORY_MAP." (category, 11st) SELECT category, '".$catno."' FROM ".GD_CATEGORY." ".$WHERE;
	$db->query($query);

}
// eof mode == save


echo $shople->json_encode($result);
?>
