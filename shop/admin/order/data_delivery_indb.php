<?
include "../lib.php";
@include "../../conf/config.pay.php";
@include "../../conf/orderXls.php";

$integrate_order = Core::loader('integrate_order');
register_shutdown_function(array(&$integrate_order, 'reserveSync'));

$cfgByte	= trim( preg_replace( "'m'si", "", get_cfg_var( 'upload_max_filesize' ) ) ) * ( 1024 * 1024 ); # 업로드최대용량 : mb * ( kb * b )
$fileByte	= filesize( $_FILES['file_excel'][tmp_name] ); # 파일용량


if ( empty( $_FILES['file_excel'][name] ) ) $altMsg = 'CSV파일을 선택하지 않으셨습니다.'; // 화일이 없으면
else if ( !preg_match("/.csv$/i", $_FILES['file_excel'][name] ) ) $altMsg = 'CSV 파일만 업로드 하실 수 있습니다.'; // 확장자 체크
else if ( $fileByte > $cfgByte ) $altMsg = get_cfg_var( 'upload_max_filesize' ) . '이하의 파일만 업로드 하실 수 있습니다.'; // 업로드최대용량 초과
else { // 화일이 있으면

	setlocale(LC_CTYPE, 'ko_KR.eucKR');
	header( 'Content-type: application/vnd.ms-excel' );
	header( 'Content-Disposition: attachment; filename=['. strftime( '%y년%m월%d일' ) .'] 송장업데이트결과.xls' );
	header( 'Expires: 0' );
	header( 'Cache-Control: must-revalidate, post-check=0,pre-check=0' );
	header( 'Pragma: public' );
	header( 'Content-Description: PHP4 Generated Data' );

	echo '
		<html>
		<head>
		<title>list</title>
		<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
		<style>.xl31{mso-number-format:"0_\)\;\\\(0\\\)";}</style>
		</head>
		<body>
		<table border="1">
		';


	{ // CSV 파일의 전체 항목을 읽어 DB에 insert

		$row = 0;
		$fp = fopen( $_FILES['file_excel'][tmp_name], 'r' );
		$fields = fgetcsv( $fp, 135000, ',' );
		$fieldLen = count( $fields );
		$FieldNm = Array();

		if(!$set[delivery][basis])$tval = "orderXls";
		else $tval = "orderGoodsXls";

		if(!${$tval})${$tval} = $default[$tval];
		else ${$tval} = getdefault($tval);

		foreach($$tval as $k => $v)	if($v[3]=='checked')$arr_col[] = $v[1];

		$accept_arr = array('ordno', 'nameOrder', 'email', 'phoneOrder', 'mobileOrder', 'nameReceiver', 'phoneReceiver',
			'mobileReceiver','zipcode', 'zonecode', 'address', 'road_address', 'memo');

		while ( $data = fgetcsv( $fp, 135000, ',' ) ){ // 데이타 저장

			//----------------------------------------------------------------------------------------------//
			$Recode = array();

			foreach ( $arr_col as $key  => $dataIdx ) $Recode[$dataIdx] = addslashes( trim( $data[$key] ) ); // 제품 필드

			{ // Recode 배열 저장
				$tmpSQL = array();

				foreach ( $Recode as $key => $value ){
					if(in_array($key,$accept_arr))	$tmpSQL[] = "$key='$value'";
				}

				list( $getScnt ) = $db->fetch( "select count(*) from ".GD_ORDER." where ordno='" . $Recode['ordno'] . "'" );
				if($getScnt != 1){
					print "<tr><td>line $row:	</td><td>주문번호</td><td>" . $Recode['ordno'] . "</td><td>처리결과</td><td>NOT PROCESS : 주문번호 오류.</td></tr>";
					continue;
				}
				$strSQL = ( $getScnt == 0 ? "insert into " : "update " ) . " ".GD_ORDER." set " . implode( ", ", $tmpSQL ) . ( $getScnt == 0 ? "" : " where ordno='" . $Recode['ordno'] . "'" );

				$db->query( 'set autocommit=0' );	//자동 commit 기능 방지
				$result1 = $db->query( $strSQL );
				$db->query( 'commit' );	//위에서 실행한 명령을 commit 하여 디비에 반영

				$_STEP = 0;

				### 송장번호 입력
				if($Recode[deliveryno] && $Recode[deliverycode] && $Recode['ordno']){
					$Recode['deliverycode'] = str_replace('-','',$Recode['deliverycode']); // 송장번호에 있는 - 문자 제거
					$query = "update ".GD_ORDER." set deliverycode='".$Recode['deliverycode']."',deliveryno='".$Recode['deliveryno']."',ddt='".$Recode['ddt']."'  where ordno='".$Recode['ordno']."'";
					$db->query( $query );

					if($set[delivery][basis] && $Recode['sno']){
						$query = "update ".GD_ORDER_ITEM." set dvcode='".$Recode['deliverycode']."',dvno='".$Recode['deliveryno']."' where ordno='".$Recode['ordno']."' and sno='".$Recode['sno']."'";
						$db->query($query);
					}

					$_STEP = 3;
				}

				### 배송완료 처리 및 입력
				if (preg_match('/[0-9]{4}.[0-9]{2}.[0-9]{2} [0-9]{2}.[0-9]{2}.[0-9]{2}/',$Recode['confirmdt'])) {
					$query = "update ".GD_ORDER." set confirmdt='".$Recode['confirmdt']."' where ordno='".$Recode['ordno']."'";
					if ($db->query( $query )) {
						$_STEP = 4;
					}
				}

				if ($_STEP == 4) {
					ctlStep($Recode['ordno'],$_STEP,'stock');
					setStock($Recode['ordno']);
					set_prn_settleprice($Recode['ordno']);
				}

				$row++;
				{ // 결과출력
					print '<tr><td>line ' . $row . ': </td>';
					print '<td>주문번호</td><td class=xl31>' . $Recode['ordno'] . '</td>';
					print '<td>처리결과</td><td>' . ( $getScnt == 0 ? 'INSERT' : 'UPDATE' ) . ' (' . ( $result1 ? 'T' : 'F' ) . ')' . '</td>';
					print '</tr>';
				}
			}
		}
		fclose($fp);
	}


	echo '
		</table>
		</body>
		</html>
		';

	exit;
}


msg( $altMsg, $_SERVER[HTTP_REFERER] );

?>