<?

include "../lib.php";
include_once dirname(__FILE__) . "/../../conf/config.php";
include_once dirname(__FILE__) . "/../lib.skin.php";
require_once("../../lib/qfile.class.php");
require_once("../../lib/todayshop_cache.class.php");
todayshop_cache::truncate();

$qfile = new qfile();

$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

if (!$_POST[returnUrl]) $_POST[returnUrl] = $_SERVER[HTTP_REFERER];


if ( $mode == "register" ){

	$db->query("insert into ".GD_BANNER." set loccd='" . $_POST['loccd'] . "', regdt = now()");
	$_POST['sno'] = $db->lastID();

	{ // 순서 재정렬

		$i = 0;
		$res = $db->query("SELECT sno FROM ".GD_BANNER." WHERE tplSkin = '".$cfg['tplSkinTodayWork']."' and loccd='" . $_POST['loccd'] . "' ORDER BY sort ASC, regdt DESC");

		while ($data=$db->fetch($res)){
			$db->query("UPDATE ".GD_BANNER." SET sort='" . ( ++$i ) . "' WHERE loccd='" . $_POST['loccd'] . "' AND sno='" . $data['sno'] . "'");
		}
	}
}

switch ( $mode ){

	case "delete":

		$infostr = split( ";", $_POST['nolist'] );
		for ( $i = 0; $i < count( $infostr ); $i++ ){

			list ( $img ) = $db->fetch("select img from ".GD_BANNER." where sno='" . $infostr[$i] . "'");
			if ( $img != '' ) @unlink( "../../data/skin_today/" . $cfg['tplSkinTodayWork'] . "/img/banner/" . $img );

			$db->query("delete from ".GD_BANNER." WHERE sno='" . $infostr[$i] . "'");
		}
		setDu('skin'); # 계정용량 계산

		break;

	case "register": case "modify":
		@include_once dirname(__FILE__) . "/webftp/webftp.class_outcall.php";
		outcallUpload( $_FILES, '/img/banner/' );

		### 데이타 수정
		$query = "
		update ".GD_BANNER." set
			loccd		= '$_POST[loccd]',
			linkaddr	= '$_POST[linkaddr]',
			img			= '$_POST[img]',
			target		= '$_POST[target]',
			tplSkin		= '".$cfg['tplSkinTodayWork']."'
		where
			sno = '$_POST[sno]'
		";
		$db->query($query);

		$_POST[returnUrl] = './codi.banner.register.php?mode=modify&sno=' . $_POST['sno'] . '&returnUrl=' . urlencode( $_POST[returnUrl] );

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
			$db->query("UPDATE ".GD_BANNER." SET sort='" . $exp['sort'][$idx] . "' WHERE sno='" . $code . "'");
		}

		break;

	case "sort_up": case "sort_down":

		{ // 변수 초기화

			$BscCode = explode( '|', $_GET['code'] );
			list ( $BscSort ) = $db->fetch("select sort from ".GD_BANNER." where loccd='" . $BscCode[0] . "' AND sno='" . $BscCode[1] . "'");
		}


		// 변경레코드 기본키와 정렬번호 추출
		if ( $mode == 'sort_up' ){
			list ( $sno, $sort ) = $db->fetch("select sno, sort from ".GD_BANNER." where tplSkin = '".$cfg['tplSkinTodayWork']."' and loccd='" . $BscCode[0] . "' and sort < '$BscSort' order by sort desc limit 1");
		}
		else if ( $mode == 'sort_down' ){
			list ( $sno, $sort ) = $db->fetch("select sno, sort from ".GD_BANNER." where tplSkin = '".$cfg['tplSkinTodayWork']."' and loccd='" . $BscCode[0] . "' and sort > '$BscSort' order by sort asc limit 1");
		}


		// 기본레코드와 변경레코드 업데이트
		if ( $sno != '' && $sort != '' ){

			$db->query("update ".GD_BANNER." set sort='$sort' where loccd='" . $BscCode[0] . "' AND sno='" . $BscCode[1] . "'");
			$db->query("update ".GD_BANNER." set sort='$BscSort' where loccd='" . $BscCode[0] . "' AND sno='" . $sno . "'");
		}

		break;

	case "sort_direct":

		{ // 변수 초기화

			$BscCode = explode( '|', $_GET['code'] );
			$ChgSort = $_GET['sort'];
		}


		$db->query("UPDATE ".GD_BANNER." SET sort='$ChgSort' WHERE loccd='" . $BscCode[0] . "' AND sno='" . $BscCode[1] . "'"); // 순서 수정


		{ // 순서 재정렬

			$i = 0;
			$res = $db->query("SELECT sno FROM ".GD_BANNER." WHERE tplSkin = '".$cfg['tplSkinTodayWork']."' and loccd='" . $BscCode[0]  . "' ORDER BY sort ASC, regdt DESC");

			while ($data=$db->fetch($res)){
				$db->query("UPDATE ".GD_BANNER." SET sort='" . ( ++$i ) . "' WHERE loccd='" . $BscCode[0]  . "' AND sno='" . $data['sno'] . "'");
			}
		}

		break;

	case "modify_loccd":

		$path = dirname(__FILE__) . "/../../conf/config.todayshop.banner_".$cfg['tplSkinTodayWork'].".php";
		include_once $path;

		{ // 환경파일 저장

			foreach ( $_POST['loccd'] AS $k => $v ) $b_loccd[$k] = $v;

			$qfile->open( $path);
			$qfile->write("<?\n\n" );
			$qfile->write("\$b_loccd = array(\n" );

			ksort( $b_loccd );
			foreach ( $b_loccd as $k => $v ) $qfile->write("'$k'\t\t=> '$v',\n" );

			$qfile->write(");\n\n" );
			$qfile->write("?>" );
			$qfile->close();
			@chMod( $path, 0757 );
		}

		break;
}

go($_POST[returnUrl]);

?>
