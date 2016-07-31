<?

include "../lib.php";


$cfgByte	= trim( preg_replace( "'m'si", "", get_cfg_var( 'upload_max_filesize' ) ) ) * ( 1024 * 1024 ); # 업로드최대용량 : mb * ( kb * b )
$fileByte	= filesize( $_FILES['file_excel'][tmp_name] ); # 파일용량


if ( empty( $_FILES['file_excel'][name] ) ) $altMsg = 'CSV파일을 선택하지 않으셨습니다.'; // 화일이 없으면
else if ( !preg_match("/.csv$/i", $_FILES['file_excel'][name] ) ) $altMsg = 'CSV 파일만 업로드 하실 수 있습니다.'; // 확장자 체크
else if ( $fileByte > $cfgByte ) $altMsg = get_cfg_var( 'upload_max_filesize' ) . '이하의 파일만 업로드 하실 수 있습니다.'; // 업로드최대용량 초과
else { // 화일이 있으면

	setlocale(LC_CTYPE, 'ko_KR.eucKR');
	header( 'Content-type: application/vnd.ms-excel' );
	header( 'Content-Disposition: attachment; filename=['. strftime( '%y년%m월%d일' ) .'] 데이타이전 결과.xls' );
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


		{ // 필드 번호 셋팅

			$fields = fgetcsv( $fp, 135000, ',' );
			$fields = fgetcsv( $fp, 135000, ',' );
			$fieldLen = count( $fields );
			$FieldNm = Array();

			for ( $i = 0; $i < $fieldLen; $i++ ){
				if ( $fields[$i] <> '' ) $FieldNm[$fields[$i]] = $i;
			}

			//echo '<tr><td colspan="5">'; echo print_r($FieldNm); echo '</td></tr><tr><td colspan="5">' . str_repeat( '=', 60 ) . '</td></tr>';
		}


		while ( $data = fgetcsv( $fp, 135000, ',' ) ){ // 데이타 저장

			$row++;

			//----------------------------------------------------------------------------------------------//
			$Recode = array();

			if ( strlen( $data[$FieldNm[m_id]] ) > 20 ){ // 기본키 값 체크

				print "<tr><td>line $row:	</td><td>회원아이디</td><td>" . $data[$FieldNm[m_id]] . "</td><td>처리결과</td><td>NOT PROCESS : m_id 20자리이상임.</td></tr>";
				continue;
			}
			else if( $data[$FieldNm[m_id]] == '' ){ // 기본키 값 셋

				print "<tr><td>line $row:	</td><td>회원아이디</td><td>" . $data[$FieldNm[m_id]] . "</td><td>처리결과</td><td>NOT PROCESS : m_id 비어있음.</td></tr>";
				continue;
			}

			foreach ( $FieldNm as $key => $dataIdx ){ // Recode 배열 구성

				$Recode[$key] = addslashes( trim( $data[$dataIdx] ) ); // 제품 필드

				if ( in_array( $key, array('birth_year', 'birth', 'busino', 'emoney', 'marridate', 'cnt_login', 'cnt_sale', 'sum_sale' ) ) ) $Recode[$key] = preg_replace( '/[^0-9]/', '', $Recode[$key] ); // 숫자만 저장

				if ( in_array( $key, array( 'password' ) ) && $_POST[chkpass] ){ // password 저장
					list( $pass ) = $db->fetch( "select PASSWORD( '" . $Recode[$key] . "' ) as pass" );
					$Recode[$key] = $pass;
				}

				if ( $key == 'regdt' && $Recode["regdt"] == '' ) $Recode["regdt"] = date('Y-m-d H:i:s'); // 등록일

				if ( $key == 'status' && in_array( $Recode[$key], array( '1', '승인' ) ) ) $Recode[$key] = '1'; // 승인
				else if ( $key == 'status' ) $Recode[$key] = '0'; // 미승인

				if ( $key == 'sex' && in_array( $Recode[$key], array( 'w', '여자' ) ) ) $Recode[$key] = 'w'; // 여자
				else if ( $key == 'sex' ) $Recode[$key] = 'm'; // 남자

				if ( $key == 'calendar' && in_array( $Recode[$key], array( 'l', '음', '음력' ) ) ) $Recode[$key] = 'l'; // 음
				else if ( $key == 'calendar' ) $Recode[$key] = 's'; // 양

				if ( $key == 'mailling' && in_array( $Recode[$key], array( 'n', '거부' ) ) ) $Recode[$key] = 'n'; // 거부
				else if ( $key == 'mailling' ) $Recode[$key] = 'y'; // 받음

				if ( $key == 'sms' && in_array( $Recode[$key], array( 'n', '거부' ) ) ) $Recode[$key] = 'n'; // 거부
				else if ( $key == 'sms' ) $Recode[$key] = 'y'; // 받음

				if ( $key == 'marriyn' && in_array( $Recode[$key], array( 'y', '기혼' ) ) ) $Recode[$key] = 'y'; // 기혼
				else if ( $key == 'marriyn' ) $Recode[$key] = 'n'; // 미혼

				if ( $key == 'interest' && $Recode["interest"] != '' ){

					$interest = explode( "|", $Recode["interest"] );
					foreach ( $interest as $k => $v ) $interest[$k] = pow( 2, $v );
					$Recode[$key] = @array_sum($interest);
				}
			} // end foreach


			{ // Recode 배열 저장

				if ( count( $Recode ) < 1 ) continue;
				$tmpSQL = array();
				foreach ( $Recode as $key => $value ) $tmpSQL[] = "$key='$value'";

				list( $getScnt ) = $db->fetch( "select count(*) from ".GD_MEMBER." where m_id='" . $Recode['m_id'] . "'" );
				$strSQL = ( $getScnt == 0 ? "insert into " : "update " ) . " ".GD_MEMBER." set " . implode( ", ", $tmpSQL ) . ( $getScnt == 0 ? "" : " where m_id='" . $Recode['m_id'] . "'" );

				$db->query( 'set autocommit=0' );	//자동 commit 기능 방지
				$result1 = $db->query( $strSQL );
				$db->query( 'commit' );	//위에서 실행한 명령을 commit 하여 디비에 반영
			}
			//---------------------------------------------------------------------------------------------- END //


			{ // 결과출력

				print '<tr><td>line ' . $row . ': </td>';
				print '<td>회원아이디</td><td>' . $Recode['m_id'] . '</td>';
				print '<td>처리결과</td><td>' . ( $getScnt == 0 ? 'INSERT' : 'UPDATE' ) . ' (' . ( $result1 ? 'T' : 'F' ) . ')' . '</td>';
				print '</tr>';
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