<?

include "../lib.php";

$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

if (!$_POST[returnUrl]) $_POST[returnUrl] = $_SERVER[HTTP_REFERER];


if ( $mode == "register" ){

	$db->query("insert into ".GD_FAQ." set itemcd='" . $_POST['itemcd'] . "', regdt = now()");
	$_POST['sno'] = $db->lastID();

	{ // ���� ������

		$i = 0;
		$res = $db->query("SELECT sno FROM ".GD_FAQ." WHERE itemcd='" . $_POST['itemcd'] . "' ORDER BY sort ASC, regdt DESC");

		while ($data=$db->fetch($res)){
			$db->query("UPDATE ".GD_FAQ." SET sort='" . ( ++$i ) . "' WHERE itemcd='" . $_POST['itemcd'] . "' AND sno='" . $data['sno'] . "'");
		}
	}
}


switch ( $mode ){

	case "delete":

		$infostr = split( ";", $_POST['nolist'] );
		for ( $i = 0; $i < count( $infostr ); $i++ ){
			$db->query("delete from ".GD_FAQ." WHERE sno='" . $infostr[$i] . "'");
		}

		break;

	case "register": case "modify":

		### ����Ÿ ����
		$query = "
		update ".GD_FAQ." set
			itemcd		= '$_POST[itemcd]',
			question	= '$_POST[question]',
			descant		= '$_POST[descant]',
			answer		= '$_POST[answer]',
			best		= '$_POST[best]',
			bestsort	= '$_POST[bestsort]'
		where
			sno = '$_POST[sno]'
		";
		$db->query($query);

		$_POST[returnUrl] = './faq_register.php?mode=modify&sno=' . $_POST['sno'] . '&returnUrl=' . urlencode( $_POST[returnUrl] );

		break;

	case "allmodify":

		$fieldChk = array( '' ); // üũ�ڽ� �ʵ��

		$exp = explode( "||", preg_replace( "/\|\|$/", "", $_POST['allmodify'] ) );

		foreach( $exp as $k => $value ){

			if ( $value == '' ){ unset( $exp[ $k ] ); continue; }

			$tmp = explode( "==", $value );
			$tmp[1] = preg_replace( "/;$/", "", $tmp[1] );

			if( !in_array( $key, $fieldChk ) ) $exp[ $tmp[0] ] = explode( ";", $tmp[1] );
			else $exp[ $tmp[0] ] = explode( ";", str_replace( "true", "Y", str_replace( "false", "N", $tmp[1] ) ) ); // üũ�ڽ� �ʵ���

			unset( $exp[ $k ] );
		}

		foreach( $exp['code'] as $idx => $code ){
			$db->query("UPDATE ".GD_FAQ." SET sort='" . $exp['sort'][$idx] . "', itemcd='" . $exp['itemcd'][$idx] . "', best='" . $exp['best'][$idx] . "', bestsort='" . $exp['bestsort'][$idx] . "' WHERE sno='" . $code . "'");
		}

		break;

	case "sort_up": case "sort_down":

		{ // ���� �ʱ�ȭ

			$BscCode = explode( '|', $_GET['code'] );
			list ( $BscSort ) = $db->fetch("select sort from ".GD_FAQ." where itemcd='" . $BscCode[0] . "' AND sno='" . $BscCode[1] . "'");
		}


		// ���淹�ڵ� �⺻Ű�� ���Ĺ�ȣ ����
		if ( $mode == 'sort_up' ){
			list ( $sno, $sort ) = $db->fetch("select sno, sort from ".GD_FAQ." where itemcd='" . $BscCode[0] . "' and sort < '$BscSort' order by sort desc limit 1");
		}
		else if ( $mode == 'sort_down' ){
			list ( $sno, $sort ) = $db->fetch("select sno, sort from ".GD_FAQ." where itemcd='" . $BscCode[0] . "' and sort > '$BscSort' order by sort asc limit 1");
		}


		// �⺻���ڵ�� ���淹�ڵ� ������Ʈ
		if ( $sno != '' && $sort != '' ){

			$db->query("update ".GD_FAQ." set sort='$sort' where itemcd='" . $BscCode[0] . "' AND sno='" . $BscCode[1] . "'");
			$db->query("update ".GD_FAQ." set sort='$BscSort' where itemcd='" . $BscCode[0] . "' AND sno='" . $sno . "'");
		}

		break;

	case "sort_direct":

		{ // ���� �ʱ�ȭ

			$BscCode = explode( '|', $_GET['code'] );
			$ChgSort = $_GET['sort'];
		}


		$db->query("UPDATE ".GD_FAQ." SET sort='$ChgSort' WHERE itemcd='" . $BscCode[0] . "' AND sno='" . $BscCode[1] . "'"); // ���� ����


		{ // ���� ������

			$i = 0;
			$res = $db->query("SELECT sno FROM ".GD_FAQ." WHERE itemcd='" . $BscCode[0]  . "' ORDER BY sort ASC, regdt DESC");

			while ($data=$db->fetch($res)){
				$db->query("UPDATE ".GD_FAQ." SET sort='" . ( ++$i ) . "' WHERE itemcd='" . $BscCode[0]  . "' AND sno='" . $data['sno'] . "'");
			}
		}

		break;
}

go($_POST[returnUrl]);

?>
