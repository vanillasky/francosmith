<?
include "../lib.php";

// 변수 정의
	$idx			= ($_REQUEST['idx'])			? $_REQUEST['idx']			: "";
	$mode			= ($_REQUEST['mode'])			? $_REQUEST['mode']			: "";
	$fa_name		= ($_POST['fa_name'])			? $_POST['fa_name']			: "";
	$fa_email		= ($_POST['fa_email'])			? $_POST['fa_email']		: "";
	$fa_zonecode	= ($_POST['zonecode'])			? $_POST['zonecode']		: "";
	$fa_address		= ($_POST['address'])			? $_POST['address']			: "";
	$fa_road_address= ($_POST['road_address'])		? $_POST['road_address']	: "";
	$fa_address_sub	= ($_POST['address_sub'])		? $_POST['address_sub']		: "";
	$fa_memo		= ($_POST['fa_memo'])			? $_POST['fa_memo']			: "";
	$delList		= ($_POST['delList'])			? $_POST['delList']			: "";

	if($_POST['fa_groupOption'] == "select") $fa_group = $_POST['fa_groupSelect'];
	else if($_POST['fa_groupOption'] == "custom") $fa_group = $_POST['fa_groupCustom'];

	if($_POST['zipcode'][0] && $_POST['zipcode'][1]) $fa_zipcode = $_POST['zipcode'][0]."-".$_POST['zipcode'][1];

	for($i = 0, $imax = count($_POST['fa_phone']); $i < $imax; $i++) {
		if($_POST['fa_phone'][$i]) {
			if($fa_phone) $fa_phone .= "-";
			$fa_phone .= $_POST['fa_phone'][$i];
		}
	}

	for($i = 0, $imax = count($_POST['fa_mobile']); $i < $imax; $i++) {
		if($_POST['fa_mobile'][$i]) {
			if($fa_mobile) $fa_mobile .= "-";
			$fa_mobile .= $_POST['fa_mobile'][$i];
		}
	}

// 처리 분기
	switch($mode) {
		case "faRegist" :
			$query = "INSERT INTO ".GD_FAVORITE_ADDRESS." SET
				fa_group = '$fa_group',
				fa_name = '$fa_name',
				fa_email = '$fa_email',
				fa_zipcode = '$fa_zipcode',
				fa_zonecode = '$fa_zonecode',
				fa_address = '$fa_address',
				fa_road_address = '$fa_road_address',
				fa_address_sub = '$fa_address_sub',
				fa_phone = '$fa_phone',
				fa_mobile = '$fa_mobile',
				fa_memo = '$fa_memo',
				ip = '".$_SERVER['REMOTE_ADDR']."',
				regdt = NOW()
			";
			$db->query($query);
			msg("자주 쓰는 주소가 등록되었습니다.");
			exit("<script>opener.location.reload();window.close();</script>");
			break;

		case "faModify" :
			$query = "UPDATE ".GD_FAVORITE_ADDRESS." SET
				fa_group = '$fa_group',
				fa_name = '$fa_name',
				fa_email = '$fa_email',
				fa_zipcode = '$fa_zipcode',
				fa_zonecode = '$fa_zonecode',
				fa_address = '$fa_address',
				fa_road_address = '$fa_road_address',
				fa_address_sub = '$fa_address_sub',
				fa_phone = '$fa_phone',
				fa_mobile = '$fa_mobile',
				fa_memo = '$fa_memo'
				WHERE fa_no = '$idx'
			";
			$db->query($query);
			msg("자주 쓰는 주소가 수정되었습니다.");
			exit("<script>opener.location.reload();window.close();</script>");
			break;

		case "faDelete" :
			$delList = str_replace(";", ",", $delList);
			$query = "DELETE FROM ".GD_FAVORITE_ADDRESS." WHERE fa_no IN ($delList)";
			$db->query($query);
			msg("해당 주소를 삭제했습니다.", -1);
			break;
	}
?>