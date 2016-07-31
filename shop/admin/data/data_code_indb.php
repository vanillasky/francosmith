<?

include "../lib.php";

$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

if (!$_POST[returnUrl]) $_POST[returnUrl] = $_SERVER[HTTP_REFERER];


if ( $mode == "register" ){

	$db->query("insert into ".GD_CODE." set groupcd	= '" . $_POST['groupcd'] . "', itemcd = '" . $_POST['itemcd'] . "'");
	$_POST['sno'] = $db->lastID();

	{ // 순서 재정렬

		$i = 0;
		$res = $db->query("SELECT sno FROM ".GD_CODE." WHERE groupcd='" . $_POST['groupcd'] . "' ORDER BY sort ASC, itemcd ASC");

		while ($data=$db->fetch($res)){
			$db->query("UPDATE ".GD_CODE." SET sort='" . ( ++$i ) . "' WHERE groupcd='" . $_POST['groupcd'] . "' AND sno='" . $data['sno'] . "'");
		}
	}
}


switch ( $mode ){

	case "delete":

		$infostr = split( ";", $_POST['nolist'] );
		for ( $i = 0; $i < count( $infostr ); $i++ ){
			$db->query("delete from ".GD_CODE." WHERE sno='" . $infostr[$i] . "'");
		}

		break;

	case "register": case "modify":

		### 데이타 수정
		$query = "
		update ".GD_CODE." set
			groupcd		= '$_POST[groupcd]',
			itemcd		= '$_POST[itemcd]',
			itemnm		= '$_POST[itemnm]'
		where
			sno = '$_POST[sno]'
		";
		$db->query($query);
		echo "<script>parent.location.reload();</script>";
		exit;
		break;

	case "allmodify":

		$fieldChk = array( '' ); // 체크박스 필드명

		$exp = explode( "||", preg_replace( "/\|\|$/", "", $_POST['allmodify'] ) );

		foreach( $exp as $k => $value ){

			if ( $value == '' ){ unset( $exp[ $k ] ); continue; }

			$tmp = explode( "==", $value );
			$tmp[1] = preg_replace( "/;$/", "", $tmp[1] );

			if( !in_array( $key, $fieldChk ) ) $exp[ $tmp[0] ] = explode( ";", $tmp[1] );
			else $exp[ $tmp[0] ] = explode( ";", str_replace( "true", "Y", str_replace( "false", "N", $tmp[1] ) ) ); // 체크박스 필드경우

			unset( $exp[ $k ] );
		}

		foreach( $exp['code'] as $idx => $code ){
			$db->query("UPDATE ".GD_CODE." SET sort='" . $exp['sort'][$idx] . "' WHERE sno='" . $code . "'");
		}

		break;

	case "sort_up": case "sort_down":

		{ // 변수 초기화

			$BscCode = explode( '|', $_GET['code'] );
			list ( $BscSort ) = $db->fetch("select sort from ".GD_CODE." where groupcd='" . $BscCode[0] . "' AND sno='" . $BscCode[1] . "'");
		}


		// 변경레코드 기본키와 정렬번호 추출
		if ( $mode == 'sort_up' ){
			list ( $sno, $sort ) = $db->fetch("select sno, sort from ".GD_CODE." where groupcd='" . $BscCode[0] . "' and sort < '$BscSort' order by sort desc limit 1");
		}
		else if ( $mode == 'sort_down' ){
			list ( $sno, $sort ) = $db->fetch("select sno, sort from ".GD_CODE." where groupcd='" . $BscCode[0] . "' and sort > '$BscSort' order by sort asc limit 1");
		}


		// 기본레코드와 변경레코드 업데이트
		if ( $sno != '' && $sort != '' ){

			$db->query("update ".GD_CODE." set sort='$sort' where groupcd='" . $BscCode[0] . "' AND sno='" . $BscCode[1] . "'");
			$db->query("update ".GD_CODE." set sort='$BscSort' where groupcd='" . $BscCode[0] . "' AND sno='" . $sno . "'");
		}

		break;

	case "sort_direct":

		{ // 변수 초기화

			$BscCode = explode( '|', $_GET['code'] );
			$ChgSort = $_GET['sort'];
		}


		$db->query("UPDATE ".GD_CODE." SET sort='$ChgSort' WHERE groupcd='" . $BscCode[0] . "' AND sno='" . $BscCode[1] . "'"); // 순서 수정


		{ // 순서 재정렬

			$i = 0;
			$res = $db->query("SELECT sno FROM ".GD_CODE." WHERE groupcd='" . $BscCode[0]  . "' ORDER BY sort ASC, itemcd ASC");

			while ($data=$db->fetch($res)){
				$db->query("UPDATE ".GD_CODE." SET sort='" . ( ++$i ) . "' WHERE groupcd='" . $BscCode[0]  . "' AND sno='" . $data['sno'] . "'");
			}
		}

		break;
}

go($_POST[returnUrl]);

?>