<?

include "../lib.php";
$db -> err_report = 1;
$mode = ( $_POST[mode] )?$_POST[mode]:$_GET[mode];
if (!$_POST[returnUrl]) $_POST[returnUrl] = $_SERVER[HTTP_REFERER];

if($mode == "dopt_register" || $mode == "dopt_modify"){
	foreach($_POST[opt1] as $k => $v) $_POST[opt1][$k] = str_replace('^','',$v);
	foreach($_POST[opt2] as $k => $v) $_POST[opt2][$k] = str_replace('^','',$v);
	$opt1 = @implode('^',$_POST[opt1]);
		$opt2 = @implode('^',$_POST[opt2]);
		$setqry = "
		set
		title = '".$_POST[dopt_title]."',
		opttype = '".$_POST[opttype]."',
		optnm1 = '".$_POST[optnm][0]."',
		optnm2 = '".$_POST[optnm][1]."',
		opt1 = '".$opt1."',
		opt2 = '".$opt2."'
		";
}

/**
	2011-01-12 by x-ta-c
	추가 옵션 바구니 저장/수정/삭제 처리
 */
elseif ($mode == "dopt_extend_register" || $mode == "dopt_extend_modify") {

	$dopt_extend = array();

	$addoptnm = isset($_POST[addoptnm]) ? $_POST[addoptnm] : false;
	$addopt_opt = isset($_POST[addopt][opt]) ? $_POST[addopt][opt] : false;
	$addopt_addprice = isset($_POST[addopt][addprice]) ? $_POST[addopt][addprice] : false;
	$addoptreq = isset($_POST[addoptreq]) ? $_POST[addoptreq] : false;
	$doptextendsno = isset($_POST[doptextendsno]) ? $_POST[doptextendsno] : '';
	$dopt_title = isset($_POST[dopt_title]) ? $_POST[dopt_title] : '';

	if (is_array($addoptnm)) {

		foreach ($addoptnm as $key => $name) {

			if (empty($name)) continue;

			$_row['name'] = $name;
			$_row['options'] = array();
			$_row['require'] = (isset($addoptreq[$key]) && $addoptreq[$key] != '') ? true : false;

			foreach($addopt_opt[$key] as $itemkey => $item) {
				if (empty($addopt_opt[$key][$itemkey])) continue;

				$_item['name'] = $addopt_opt[$key][$itemkey];
				$_item['price'] = is_numeric($addopt_addprice[$key][$itemkey]) ? $addopt_addprice[$key][$itemkey] : 0;
				$_row['options'][] = $_item;
			}

			$dopt_extend[] = $_row;
		}
	}


	$dopt_extend = serialize($dopt_extend);

	$setqry = "
	SET
		`title` = '".$dopt_title."' ,
		`option` = '".$dopt_extend."'
	";
}
// 2011-01-12

switch ($mode){
	case "dopt_register":
		$query = "insert into gd_dopt ".$setqry.",regdt=now()";
		$db->query($query);
		echo("<script>alert('저장 완료!');</script>");
	break;
	case "dopt_modify":
		$query = "update ".GD_DOPT.' '. $setqry." where sno='".$_POST['doptsno']."'";
		$db->query($query);
		echo("<script>alert('수정 완료!');parent.location.reload();</script>");
	break;
	case "dopt_del":
		$query = "delete from ".GD_DOPT." where sno='".$_GET['sno']."'";
		$db->query($query);
		echo("<script>alert('삭제 완료!');parent.location.reload();</script>");
	break;
	/**
		2011-01-12 by x-ta-c
		추가 옵션 바구니 저장/수정/삭제 처리
	 */
	case "dopt_extend_register":
		$query = "insert into ".GD_DOPT_EXTEND." ".$setqry.",regdt=now()";
		$db->query($query);
		echo("<script>parent.parent.fnReloadDoptExtendData();</script>");
		echo("<script>alert('저장 완료!');</script>");
	break;
	case "dopt_extend_modify":
		$query = "update ".GD_DOPT_EXTEND.' '. $setqry." where sno='".$doptextendsno."'";
		$db->query($query);
		echo("<script>parent.parent.fnReloadDoptExtendData();</script>");
		echo("<script>alert('수정 완료!');parent.location.reload();</script>");
	break;
	case "dopt_extend_del":
		$query = "delete from ".GD_DOPT_EXTEND." where sno='".$_GET['sno']."'";
		$db->query($query);
		echo("<script>parent.parent.fnReloadDoptExtendData();</script>");
		echo("<script>alert('삭제 완료!');parent.location.reload();</script>");
	break;
	// 2011-01-12

}

go($_POST[returnUrl]);

?>
