<?
include dirname(__FILE__).'/adm_data_member_excel_download.php';
exit;
include "../lib.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

$ddlpath = "../../conf/data_memberddl.ini";

if ( $_POST['limitmethod'] == 'part' && ( $_POST['limit'][0] == '' || $_POST['limit'][1] == '' ) ) msg( '부분다운 하실 경우에는 줄수를 꼭! 입력합니다.', $_SERVER[HTTP_REFERER] );
if ( $_POST['filename'] == '' ) $_POST['filename'] = '[' . strftime( '%y년%m월%d일' ) . '] 회원';

if ( count( $_POST['field'] ) > 0 ){ // 필드 속성 저장( 다운필드 )

	$fields = parse_ini_file($ddlpath, true);
	$qfile->open( $ddlpath);

	foreach ( $fields as $key => $arr ){

		if ( array_search( $key, $_POST['field'] ) !== false ) $arr['down'] = 'Y'; else $arr['down'] = 'N';
		$qfile->write("[" . $key . "]" . "\n" );
		$qfile->write("text = \"" . $arr['text'] . "\"" . "\n" );
		$qfile->write("down = \"" . $arr['down'] . "\"" . "\n" );
		$qfile->write("desc = \"" . $arr['desc'] . "\"" . "\n\n" );
	}

	$qfile->close();
	@chMod( $ddlpath, 0707 );
}


if ( $_GET['sample'] != 'Y' ){ // 쿼리 실행

	$db_table = GD_MEMBER;

	if ($_POST['skey'] && $_POST['sword']){
		if ( $_POST['skey']== 'all' ){
			$where[] = "( concat( m_id, name ) like '%".$_POST['sword']."%' or nickname like '%".$_POST['sword']."%' )";
		}
		else $where[] = $_POST['skey'] ." like '%".$_POST['sword']."%'";
	}

	if ($_POST['sstatus']!='') $where[] = "status='".$_POST['sstatus']."'";
	if ($_POST['slevel']!='') $where[] = "level='".$_POST['slevel']."'";

	if ($_POST['ssum_sale'][0] != '' && $_POST['ssum_sale'][1] != '') $where[] = "sum_sale between ".$_POST['ssum_sale'][0]." and ".$_POST['ssum_sale'][1];
	else if ($_POST['ssum_sale'][0] != '' && $_POST['ssum_sale'][1] == '') $where[] = "sum_sale >= ".$_POST['ssum_sale'][0];
	else if ($_POST['ssum_sale'][0] == '' && $_POST['ssum_sale'][1] != '') $where[] = "sum_sale <= ".$_POST['ssum_sale'][1];

	if ($_POST['semoney'][0] != '' && $_POST['semoney'][1] != '') $where[] = "emoney between ".$_POST['semoney'][0]." and ".$_POST['semoney'][1];
	else if ($_POST['semoney'][0] != '' && $_POST['semoney'][1] == '') $where[] = "emoney >= ".$_POST['semoney'][0];
	else if ($_POST['semoney'][0] == '' && $_POST['semoney'][1] != '') $where[] = "emoney <= ".$_POST['semoney'][1];

	if ($_POST['sregdt'][0] && $_POST['sregdt'][1]) $where[] = "regdt between date_format(".$_POST['sregdt'][0].",'%Y-%m-%d 00:00:00') and date_format(".$_POST['sregdt'][1].",'%Y-%m-%d 23:59:59')";
	if ($_POST['slastdt'][0] && $_POST['slastdt'][1]) $where[] = "last_login between date_format(".$_POST['slastdt'][0].",'%Y-%m-%d 00:00:00') and date_format(".$_POST['slastdt'][1].",'%Y-%m-%d 23:59:59')";

	if ($_POST['sex']) $where[] = "sex = '".$_POST['sex']."'";
	if ($_POST['sage']!=''){
		$age[] = date('Y') + 1 - $_POST['sage'];
		$age[] = $age[0] - 9;
		foreach ($age as $k => $v) $age[$k] = substr($v,2,2);
		if ($_POST['sage'] == '60') $where[] = "right(birth_year,2) <= ".$age[1];
		else $where[] = "right(birth_year,2) between ".$age[1]." and ".$age[0];
	}

	if ($_POST['scnt_login'][0] != '' && $_POST['scnt_login'][1] != '') $where[] = "cnt_login between ".$_POST['scnt_login'][0]." and ".$_POST['scnt_login'][1];
	else if ($_POST['scnt_login'][0] != '' && $_POST['scnt_login'][1] == '') $where[] = "cnt_login >= ".$_POST['scnt_login'][0];
	else if ($_POST['scnt_login'][0] == '' && $_POST['scnt_login'][1] != '') $where[] = "cnt_login <= ".$_POST['scnt_login'][1];

	if ($_POST['dormancy']){
		$dormancyDate	= date("Ymd",strtotime("-{$_POST['dormancy']} day"));
		$where[] = " date_format(last_login,'%Y%m%d') <= '".$dormancyDate."'";
	}

	if ($_POST['mailing']) $where[] = "mailling = '".$_POST['mailing']."'";
	if ($_POST['smsyn']) $where[] = "sms = '".$_POST['smsyn']."'";

	if( $_POST['birthtype'] ) $where[] = "calendar = '".$_POST['birthtype']."'";
	if( $_POST['birthdate'][0] ){
		if( $_POST['birthdate'][1] ){
			if(strlen($_POST['birthdate'][0]) > 4 && strlen($_POST['birthdate'][1]) > 4) $where[] = "concat(birth_year, birth) between '".$_POST['birthdate'][0]." and ".$_POST['birthdate'][1]."'";
			else $where[] = "birth between '".$_POST['birthdate'][0]."' and '".$_POST['birthdate'][1]."'";
		}else{
			$where[] = "birth = '".$_POST['birthdate'][0]."'";
		}
	}

	if( $_POST['marriyn'] ) $where[] = "marriyn = '".$_POST['marriyn']."'";
	if( $_POST['marridate'][0] ){
		if( $_POST['marridate'][1] ){
			if(strlen($_POST['marridate'][0]) > 4 && strlen($_POST['marridate'][1]) > 4) $where[] = "marridate between '".$_POST['marridate'][0]."' and '".$_POST['marridate'][1]."'";
			else $where[] = "substring(marridate,5,4) between '".$_POST['marridate'][0]."' and '".$_POST['marridate'][1]."'";
		}else{
			$where[] = "substring(marridate,5,4) = '".$_POST['marridate'][0]."'";
		}
	}

	# 메인에서 생일자 SMS 확인용
	if ($_POST['mobileYN'] == "y") $where[] = "mobile != ''";

	$where[] = "m_id != 'godomall'";

	if ( $_POST['limitmethod'] == 'part' ) $limit = " limit " . ( $_POST['limit'][0] - 1 ) . ", " . ( $_POST['limit'][1] - $_POST['limit'][0] + 1 );
	else $limit = '';

	$sql = "select * from $db_table " . ( count( $where ) ? "where " . implode( " and ", $where ) : "" ) . " order by " . $_POST[sort] . $limit; // echo $sql;

	$res = $db->query( $sql );
}


setlocale(LC_CTYPE, 'ko_KR.eucKR');
header( "Content-type: application/vnd.ms-excel" );
header( "Content-Disposition: attachment; filename=" . $_POST[filename] . ".xls" );
header( 'Expires: 0' );
header( 'Cache-Control: must-revalidate, post-check=0,pre-check=0' );
header( 'Pragma: public' );
header( "Content-Description: PHP4 Generated Data" );

$fields = parse_ini_file($ddlpath, true);
?>
<html>
<head>
<title>list</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<style>.text{mso-number-format:"\@";}</style>
</head>
<body>
<table border="1">
<tr>
<?
foreach ( $fields as $key => $arr ){
	if ( $arr['down'] == 'Y' || $_GET['sample'] == 'Y' ) echo '<th>' . $arr['text'] . '</th>';
}
?>
</tr>
<tr>
<?
foreach ( $fields as $key => $arr ){
	if ( $arr['down'] == 'Y' || $_GET['sample'] == 'Y' ) echo '<th>' . $key . '</th>';
}
?>
</tr>
<?
if ( $_GET['sample'] != 'Y' ){
	while ( $data = $db->fetch($res) ){

		echo '<tr>';

		foreach ( $fields as $key => $arr ){

			if ( $arr['down'] != 'Y' ) continue;

			if ( $key == 'interest' ){
				$tmp = array();
				foreach( codeitem('like') as $k => $v ){
					if ($data['interest']&pow(2,$k)) $tmp[] = $k;
				}
				$data[ $key ] = implode( "|", $tmp );
			}

			if ( in_array( $key, array( 'emoney', 'cnt_login', 'cnt_sale', 'sum_sale' ) ) ) echo '<td>' . $data[ $key ] . '</td>';
			else echo '<td class="text">' . $data[ $key ] . '</td>';
		}

		echo '</tr>';
	}
}
?>
</table>
</body>
</html>