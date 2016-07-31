<?

include "../lib.php";


$cfgByte	= trim( preg_replace( "'m'si", "", get_cfg_var( 'upload_max_filesize' ) ) ) * ( 1024 * 1024 ); # ���ε��ִ�뷮 : mb * ( kb * b )
$fileByte	= filesize( $_FILES['file_excel'][tmp_name] ); # ���Ͽ뷮


if ( empty( $_FILES['file_excel'][name] ) ) $altMsg = 'CSV������ �������� �����̽��ϴ�.'; // ȭ���� ������
else if ( !preg_match("/.csv$/i", $_FILES['file_excel'][name] ) ) $altMsg = 'CSV ���ϸ� ���ε� �Ͻ� �� �ֽ��ϴ�.'; // Ȯ���� üũ
else if ( $fileByte > $cfgByte ) $altMsg = get_cfg_var( 'upload_max_filesize' ) . '������ ���ϸ� ���ε� �Ͻ� �� �ֽ��ϴ�.'; // ���ε��ִ�뷮 �ʰ�
else { // ȭ���� ������

	setlocale(LC_CTYPE, 'ko_KR.eucKR');
	header( 'Content-type: application/vnd.ms-excel' );
	header( 'Content-Disposition: attachment; filename=['. strftime( '%y��%m��%d��' ) .'] ����Ÿ���� ���.xls' );
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


	{ // CSV ������ ��ü �׸��� �о� DB�� insert

		$row = 0;
		$fp = fopen( $_FILES['file_excel'][tmp_name], 'r' );


		{ // �ʵ� ��ȣ ����

			$fields = fgetcsv( $fp, 135000, ',' );
			$fields = fgetcsv( $fp, 135000, ',' );
			$fieldLen = count( $fields );
			$FieldNm = Array();

			for ( $i = 0; $i < $fieldLen; $i++ ){
				if ( $fields[$i] <> '' ) $FieldNm[$fields[$i]] = $i;
			}

			//echo '<tr><td colspan="5">'; echo print_r($FieldNm); echo '</td></tr><tr><td colspan="5">' . str_repeat( '=', 60 ) . '</td></tr>';
		}


		while ( $data = fgetcsv( $fp, 135000, ',' ) ){ // ����Ÿ ����

			$row++;

			//----------------------------------------------------------------------------------------------//
			$Recode = array();

			if ( strlen( $data[$FieldNm[m_id]] ) > 20 ){ // �⺻Ű �� üũ

				print "<tr><td>line $row:	</td><td>ȸ�����̵�</td><td>" . $data[$FieldNm[m_id]] . "</td><td>ó�����</td><td>NOT PROCESS : m_id 20�ڸ��̻���.</td></tr>";
				continue;
			}
			else if( $data[$FieldNm[m_id]] == '' ){ // �⺻Ű �� ��

				print "<tr><td>line $row:	</td><td>ȸ�����̵�</td><td>" . $data[$FieldNm[m_id]] . "</td><td>ó�����</td><td>NOT PROCESS : m_id �������.</td></tr>";
				continue;
			}

			foreach ( $FieldNm as $key => $dataIdx ){ // Recode �迭 ����

				$Recode[$key] = addslashes( trim( $data[$dataIdx] ) ); // ��ǰ �ʵ�

				if ( in_array( $key, array('birth_year', 'birth', 'busino', 'emoney', 'marridate', 'cnt_login', 'cnt_sale', 'sum_sale' ) ) ) $Recode[$key] = preg_replace( '/[^0-9]/', '', $Recode[$key] ); // ���ڸ� ����

				if ( in_array( $key, array( 'password' ) ) && $_POST[chkpass] ){ // password ����
					list( $pass ) = $db->fetch( "select PASSWORD( '" . $Recode[$key] . "' ) as pass" );
					$Recode[$key] = $pass;
				}

				if ( $key == 'regdt' && $Recode["regdt"] == '' ) $Recode["regdt"] = date('Y-m-d H:i:s'); // �����

				if ( $key == 'status' && in_array( $Recode[$key], array( '1', '����' ) ) ) $Recode[$key] = '1'; // ����
				else if ( $key == 'status' ) $Recode[$key] = '0'; // �̽���

				if ( $key == 'sex' && in_array( $Recode[$key], array( 'w', '����' ) ) ) $Recode[$key] = 'w'; // ����
				else if ( $key == 'sex' ) $Recode[$key] = 'm'; // ����

				if ( $key == 'calendar' && in_array( $Recode[$key], array( 'l', '��', '����' ) ) ) $Recode[$key] = 'l'; // ��
				else if ( $key == 'calendar' ) $Recode[$key] = 's'; // ��

				if ( $key == 'mailling' && in_array( $Recode[$key], array( 'n', '�ź�' ) ) ) $Recode[$key] = 'n'; // �ź�
				else if ( $key == 'mailling' ) $Recode[$key] = 'y'; // ����

				if ( $key == 'sms' && in_array( $Recode[$key], array( 'n', '�ź�' ) ) ) $Recode[$key] = 'n'; // �ź�
				else if ( $key == 'sms' ) $Recode[$key] = 'y'; // ����

				if ( $key == 'marriyn' && in_array( $Recode[$key], array( 'y', '��ȥ' ) ) ) $Recode[$key] = 'y'; // ��ȥ
				else if ( $key == 'marriyn' ) $Recode[$key] = 'n'; // ��ȥ

				if ( $key == 'interest' && $Recode["interest"] != '' ){

					$interest = explode( "|", $Recode["interest"] );
					foreach ( $interest as $k => $v ) $interest[$k] = pow( 2, $v );
					$Recode[$key] = @array_sum($interest);
				}
			} // end foreach


			{ // Recode �迭 ����

				if ( count( $Recode ) < 1 ) continue;
				$tmpSQL = array();
				foreach ( $Recode as $key => $value ) $tmpSQL[] = "$key='$value'";

				list( $getScnt ) = $db->fetch( "select count(*) from ".GD_MEMBER." where m_id='" . $Recode['m_id'] . "'" );
				$strSQL = ( $getScnt == 0 ? "insert into " : "update " ) . " ".GD_MEMBER." set " . implode( ", ", $tmpSQL ) . ( $getScnt == 0 ? "" : " where m_id='" . $Recode['m_id'] . "'" );

				$db->query( 'set autocommit=0' );	//�ڵ� commit ��� ����
				$result1 = $db->query( $strSQL );
				$db->query( 'commit' );	//������ ������ ����� commit �Ͽ� ��� �ݿ�
			}
			//---------------------------------------------------------------------------------------------- END //


			{ // ������

				print '<tr><td>line ' . $row . ': </td>';
				print '<td>ȸ�����̵�</td><td>' . $Recode['m_id'] . '</td>';
				print '<td>ó�����</td><td>' . ( $getScnt == 0 ? 'INSERT' : 'UPDATE' ) . ' (' . ( $result1 ? 'T' : 'F' ) . ')' . '</td>';
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