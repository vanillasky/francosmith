<?

include "../lib.php";
require_once "../../lib/load.class.php";

$Goods = Core::loader('Goods');
$extrainfoHelper = Core::loader('extrainfo');
$goodsSort = Core::loader('GoodsSort');

$cfgByte	= trim( preg_replace( "'m'si", "", get_cfg_var( 'upload_max_filesize' ) ) ) * ( 1024 * 1024 ); # ���ε��ִ�뷮 : mb * ( kb * b )
$fileByte	= filesize( $_FILES['file_excel'][tmp_name] ); # ���Ͽ뷮


if ( empty( $_FILES['file_excel'][name] ) ) $altMsg = 'CSV������ �������� �����̽��ϴ�.'; // ȭ���� ������
else if ( !preg_match("/.csv$/i", $_FILES['file_excel'][name] ) ) $altMsg = 'CSV ���ϸ� ���ε� �Ͻ� �� �ֽ��ϴ�.'; // Ȯ���� üũ
else if ( $fileByte > $cfgByte ) $altMsg = get_cfg_var( 'upload_max_filesize' ) . '������ ���ϸ� ���ε� �Ͻ� �� �ֽ��ϴ�.'; // ���ε��ִ�뷮 �ʰ�
else { // ȭ���� ������

	setlocale(LC_ALL, "ko_KR.eucKR"); //fgetcsv �Լ� ����, lang ������ ���� ���� ����

	// ó�� ����� ���Ͽ� �����Ͽ� �ٿ�δ��� �̵���.
	$tmp_nm = tempnam( G_CONST_DOCROOT."/cache", "xls");
	$tmp_fp = fopen($tmp_nm, "w");

	fwrite($tmp_fp, '<html><head><title>list</title><meta http-equiv="Content-Type" content="text/html; charset=euc-kr"><style>.xl31{mso-number-format:"0_\)\;\\\(0\\\)";}</style></head><body><table border="1">'.PHP_EOL);

	{ // CSV ������ ��ü �׸��� �о� DB�� insert

		$row = 0;
		$fp = fopen( $_FILES['file_excel'][tmp_name], 'r' );
		$etcField = array( 'goodscate', 'opts', 'addopts', 'addoptnm', 'inputable_addopts', 'inputable_addoptnm' ); # ����ó�� �ʵ�

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
			//if($data[$FieldNm[goodsno]]){ //��üũ by birdmarine 2007.1.30
				if(!$data[$FieldNm[goodsnm]]) continue;
				$row++;

				//----------------------------------------------------------------------------------------------//
				$etcRecode = $Recode = array();

				if ( strlen( $data[$FieldNm[goodsno]] ) > 10 ){ // �⺻Ű �� üũ

					print "<tr><td>line $row:	</td><td>��ǰ��ȣ</td><td>" . $data[$FieldNm[goodsno]] . "</td><td>ó�����</td><td>NOT PROCESS : goodsno 10�ڸ��̻���.</td></tr>";
					continue;
				}
				else if( $data[$FieldNm[goodsno]] == '' ){ // �⺻Ű �� ��

					list( $data[$FieldNm[goodsno]] ) = $db->fetch("select max(goodsno) + 1 from ".GD_GOODS."");
					if ( $data[$FieldNm[goodsno]] == '' ) $data[$FieldNm[goodsno]] = $row;
				}

				foreach ( $FieldNm as $key => $dataIdx ){ // Recode �迭 ����

					if ( in_array( $key, $etcField ) ){
						$etcRecode[$key] = addslashes( trim( $data[$dataIdx] ) ); // ����ó�� �ʵ�
						continue;
					}
					else {
						// ��ǰ �ʵ�
						$_v = (string)trim($data[$dataIdx]);

						// ���̸� continue. 'NULL' �̸� ������ ����
						if ($_v === '') continue;
						elseif (strtoupper($_v) === 'NULL') $_v = '';

						$Recode[$key] = ($key == 'extra_info') ? $_v : addslashes( $_v );
					}

					if ( $key == 'regdt' && $Recode["regdt"] == '' ) $Recode["regdt"] = date('Y-m-d H:i:s'); // �����

					if ( $key == 'tax' && in_array( $Recode[$key], array( '0', '�����' ) ) ) $Recode[$key] = '0'; // �����
					else if ( $key == 'tax' ) $Recode[$key] = '1'; // ����

					if ( $key == 'delivery_type' && in_array( $Recode[$key], array( '1', '������' ) ) ) $Recode[$key] = '1'; // ������
					else if ( $key == 'delivery_type' ) $Recode[$key] = '0'; // �⺻��ۺ� ���

					if ( $key == 'open' && in_array( $Recode[$key], array( '1', '���̱�' ) ) ) $Recode[$key] = '1'; // ��ǰ��¿��� - ���̱�
					else if ( $key == 'open' ) $Recode[$key] = '0'; // ��ǰ��¿��� - ���߱�

					if ( $key == 'runout' && in_array( $Recode[$key], array( '1', 'ǰ��' ) ) ) $Recode[$key] = '1'; // ǰ����ǰ - ǰ��
					else if ( $key == 'runout' ) $Recode[$key] = '0'; // ǰ����ǰ - �Ǹ�

					if ( $key == 'usestock' && in_array( $Recode[$key], array( 'o', '�������' ) ) ) $Recode[$key] = 'o'; // ������� - �������
					else if ( $key == 'usestock' ) $Recode[$key] = ''; // ������� - �������Ǹ�

					if ( $key == 'opttype' && in_array( $Recode[$key], array( 'double', '�и���' ) ) ) $Recode[$key] = 'double'; // �ɼ���¹�� - �и���
					else if ( $key == 'opttype' ) $Recode[$key] = 'single'; // �ɼ���¹�� - ��ü��

					if ( $key == 'relationis' && in_array( $Recode[$key], array( '1', '����' ) ) ) $Recode[$key] = '1'; // ���û�ǰ��� - ����
					else if ( $key == 'relationis' ) $Recode[$key] = '0'; // ���û�ǰ��� - �ڵ�

					if ( $key == 'extra_info' ) $Recode[$key] = addslashes($extrainfoHelper->toJson($Recode[$key]));    // ���� ������ escape �ϴ� ���, addslashes �ϸ� �ȵ�.

				} // end foreach


				{ // Recode �迭 ����

					if ( count( $Recode ) < 1 ) continue;

                    list( $getScnt, $chkInterpark ) = $db->fetch( "select count(*), inpk_prdno from ".GD_GOODS." where goodsno='" . $Recode['goodsno'] . "'" );
                    if ($getScnt && $chkInterpark) unset($Recode['extra_info']);    // ������ũ ���� ��ǰ�� �ʼ� ���� �÷��� ������Ʈ ���� ����

					$tmpSQL = array();
					foreach ( $Recode as $key => $value ) $tmpSQL[] = "$key='$value'";

					// ����4 �ű� ������ ��ǰ ����� �׸��� ���� �Ǿ����Ƿ�, insert �� ������� �����Ѵ�.
					if ($getScnt == 0 && !array_key_exists('regdt',$Recode)) {
						$tmpSQL[] = "regdt = NOW()";
					}

					// �ɼ� ��� ���� (�ɼ� ��� ó���� �ٽ� ����)
					if ($getScnt == 0 || ($getScnt != 0 && array_key_exists('opts',$Recode))) {
      					$tmpSQL[] = "use_option='0'";
     				}

					$strSQL = ( $getScnt == 0 ? "insert into " : "update " ) . " ".GD_GOODS." set " . implode( ", ", $tmpSQL ) . ( $getScnt == 0 ? "" : " where goodsno='" . $Recode['goodsno'] . "'" );

					$result1 = $db->query( $strSQL );

					if( $getScnt  == 0  ) $Recode['goodsno'] = $db->lastID();
				}

				$addoption_processed = false;

				if (!$getScnt && !array_key_exists('opts', $etcRecode)) {
					$etcRecode['opts'] = '';
				}

				foreach ( $etcRecode as $key => $value ){ // ����ó�� �ʵ�

					if ( $key == 'goodscate' ){ // ��ǰ�з�

						if ( trim( $value ) == '' ){
							$db->query( "delete from ".GD_GOODS_LINK." where goodsno='" . $Recode['goodsno'] . "'" );
							continue;
						}

						// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
						$tmp = getHighCategoryCode($value);

						$db->query( "delete from ".GD_GOODS_LINK." where goodsno='" . $Recode['goodsno'] . "' and category not in ('" . implode( "','", $tmp ) . "')" );

						$goodsLinkSort = array();
						$maxSortIncrease = array();
						$linkSortIncrease = array();
						$lookupGoodsLink = $db->query('SELECT category, sort1, sort2, sort3, sort4 FROM '.GD_GOODS_LINK.' WHERE goodsno='.$Recode['goodsno']);
						while ($goodsLink = $db->fetch($lookupGoodsLink)) {
							for ($length = 3; $length <= strlen($goodsLink['category']); $length+=3) {
								$goodsLinkSort[substr($goodsLink['category'], 0, $length)] = $goodsLink['sort'.($length/3)];
							}
						}

						foreach ( $tmp as $category ){

							if ( trim( $category ) == '' ) continue;
							list( $cnt ) = $db->fetch( "select count(*) from ".GD_GOODS_LINK." where goodsno='" . $Recode['goodsno'] . "' and category='" . $category . "'" );
							if ( $cnt < 1 ){
								list( $sort ) = $db->fetch( "select max(sno) from ".GD_GOODS_LINK."" );
								$sortList = array();
								foreach ($goodsSort->getManualSortInfoHierarchy($category) as $categorySortSet) {
									if (strlen($category)/3 >= $categorySortSet['depth']) {
										if ($goodsLinkSort[$categorySortSet['category']]) {
											$sortList[] = $categorySortSet['sort_field'].'='.$goodsLinkSort[$categorySortSet['category']];
										}
										else {
											if ($categorySortSet['manual_sort_on_link_goods_position'] === 'FIRST') {
												if (isset($linkSortIncrease[$categorySortSet['category']]) === false) {
													$goodsSort->increaseCategorySort($categorySortSet['category'], $categorySortSet['sort_field']);
													$linkSortIncrease[$categorySortSet['category']] = true;
												}
												$sortList[] = $categorySortSet['sort_field'].'=1';
											}
											else {
												$sortList[] = $categorySortSet['sort_field'].'='.((int)$categorySortSet['sort_max']+1);
											}
											$maxSortIncrease[$categorySortSet['category']] = true;
										}
									}
								}
								$db->query("
									insert into ".GD_GOODS_LINK."
									set goodsno='" . $Recode['goodsno'] . "', category='" . $category . "'".(count($sortList) ? ', '.implode(', ', $sortList) : ''));
								$last_sno = $db->lastID();
								$goods_link_sort = "-unix_timestamp()-".$last_sno;

								$db->query("update ".GD_GOODS_LINK." SET sort=".$goods_link_sort." where sno = ".$last_sno);
							}
						}
						foreach (array_keys($maxSortIncrease) as $category) $goodsSort->increaseSortMax($category);

						### �̺�Ʈ ī�װ� ����
						$res = $db->query("select b.* from ".GD_GOODS_LINK." a, ".GD_EVENT." b where a.category=b.category and a.goodsno='".$Recode['goodsno']."'");
						$i=0;
						while($tmp = $db->fetch($res)){
							$mode = "e".$tmp['sno'];
							list($cnt) = $db->fetch("select count(*) from ".GD_GOODS_DISPLAY." where mode = '$mode' and goodsno='".$Recode['goodsno']."'");
							if($cnt == 0){
								list($sort) = $db->fetch("select max(sort) from ".GD_GOODS_DISPLAY." where mode = '$mode'");
								$sort++;
								$query = "insert into ".GD_GOODS_DISPLAY." set goodsno = '".$Recode['goodsno']."',mode = '$mode', sort = '$sort'";
								$db->query($query);
							}
						}
					}

					else if ( $key == 'opts' ){ // ����/��� �ɼǸ��

						if ( trim( $value ) == '' ){
							$db->query( "update ".GD_GOODS_OPTION." set go_is_deleted = '1' where goodsno='" . $Recode['goodsno'] . "'" );
							// ���� ��ǰ�̶� 1���� �ɼ��� �ԷµǾ�� ��
							$db->query( "insert into ".GD_GOODS_OPTION." set goodsno='" . $Recode['goodsno'] . "', opt1='', opt2='',  link='1', go_is_deleted = '0', go_is_display = '1'" );
							continue;
						}

						$value = str_replace( "\n", "", $value );
						$value = explode( "|", $value );

						$idx = 0;
						$totstock = 0;
						$option_value = array();
						$tmp = array();
						foreach ( $value as $recode ){

							if ( trim( $recode ) == '' ) continue;
							list( $opt1, $opt2, $price, $consumer, $supply, $reserve, $stock, $go_sort ) = explode( "^", $recode );

							$opt1		= trim( $opt1 );
							$opt2		= trim( $opt2 );
							$price		= preg_replace( '/[^0-9]/', '', $price ); // ���ڸ� ����
							$consumer	= preg_replace( '/[^0-9]/', '', $consumer ); // ���ڸ� ����
							$supply		= preg_replace( '/[^0-9]/', '', $supply ); // ���ڸ� ����
							$reserve	= preg_replace( '/[^0-9]/', '', $reserve ); // ���ڸ� ����
							$stock		= preg_replace( '/[^0-9]/', '', $stock ); // ���ڸ� ����
							$go_sort	= preg_replace( '/[^0-9]/', '', $go_sort ); // ���ڸ� ����

							if ($idx == 0) {
								$link = 1;

								// ��ǰ ��ǥ ����, ������, ���԰�, �Һ��ڰ�
								$goods_price = $price;
								$goods_reserve = $reserve;
								$goods_supply = $supply;
								$goods_consumer = $consumer;
							}
							else {
								$link = 0;
							}

							$link		= ( $idx == 0 ? '1' : '0' );
							$totstock = $totstock + $stock;
							$idx++;

							list( $cnt ) = $db->fetch( "select count(*) from ".GD_GOODS_OPTION." where goodsno='" . $Recode['goodsno'] . "' and opt1='" . $opt1 . "' and opt2='" . $opt2 . "' and go_is_deleted <> '1'" );
							if ( $cnt < 1 ) $db->query( "insert into ".GD_GOODS_OPTION." set goodsno='" . $Recode['goodsno'] . "', opt1='" . $opt1 . "', opt2='" . $opt2 . "', price='" . $price . "', consumer='" . $consumer . "', supply='" . $supply . "', reserve='" . $reserve . "', stock='" . $stock . "', go_sort='" . $go_sort . "', link='" . $link . "', go_is_deleted = '0', go_is_display = '1'" );
							else if ( $cnt == 1 ) $db->query( "update ".GD_GOODS_OPTION." set price='" . $price . "', consumer='" . $consumer . "', supply='" . $supply . "', reserve='" . $reserve . "', stock='" . $stock . "', go_sort='" . $go_sort . "', link='" . $link . "', go_is_deleted = '0', go_is_display = '1' where goodsno='" . $Recode['goodsno'] . "' and opt1='" . $opt1 . "' and opt2='" . $opt2 . "' and go_is_deleted <> '1'" );

							$tmp[] = $opt1 . '^' . $opt2;
							$option_value[0][] = $opt1;
							$option_value[1][] = $opt2;
						}

						$option_value = implode(',',array_notnull($option_value[0])).'|'.implode(',',array_notnull($option_value[1]));

						$db->query( "update ".GD_GOODS." set totstock='". $totstock ."', use_option = '".($idx > 1 ? 1 : 0)."', option_name = optnm, option_value = '".$option_value."', goods_price = '".$goods_price."', goods_reserve = '".$goods_reserve."', goods_supply = '".$goods_supply."', goods_consumer = '".$goods_consumer."' where goodsno='". $Recode['goodsno'] ."'");
						$db->query( "update ".GD_GOODS_OPTION." set go_is_deleted = '1' where goodsno='" . $Recode['goodsno'] . "' and concat( opt1, '^', opt2 ) not in ('" . implode( "','", $tmp ) . "') and go_is_deleted <> '1'" );

					}

					else if ( ! $addoption_processed && in_array($key, array('addopts', 'addoptnm', 'inputable_addopts', 'inputable_addoptnm'))) { //�߰���ǰ���

						$db->query("update ".GD_GOODS_ADD." SET stats = 0 WHERE goodsno = '".$Recode['goodsno']."'");

						foreach(array('addopts', 'addoptnm', 'inputable_addopts', 'inputable_addoptnm') as $_key) {
							$_val = str_replace( "\n", "", $etcRecode[$_key] );
							${$_key} = $_val ? explode('|', $_val) : array();
						}

						// ������ ����
						$_addopts = array();
						$_addoptnm = array();
						foreach($addoptnm as $step => $name) {

							$_name = array_pad(explode('^', $name), 3, '');
							$_name[2] = 'S';

							foreach($addopts as $opt) {
								$_opt = explode('^', $opt);

								if ($_name[0] == $_opt[0]) {
									$_addopts[] = array(
										'step' => $step,
										'opt' => trim($_opt[1]),
										'addprice' => preg_replace('/[^0-9]/','',$_opt[2]),
										'stats' => 1,
										'type' => $_name[2],
									);
								}
							}

							$_addoptnm[] = implode('^', $_name);
						}

						foreach($inputable_addoptnm as $step => $name) {

							$_name = array_pad(explode('^', $name), 3, '');
							$_name[2] = 'I';

							foreach($inputable_addopts as $opt) {
								$_opt = explode('^', $opt);

								if ($_name[0] == $_opt[0]) {
									$_addopts[] = array(
										'step' => $step,
										'opt' => preg_replace('/[^0-9]/','',$_opt[1]),
										'addprice' => preg_replace('/[^0-9]/','',$_opt[2]),
										'stats' => 1,
										'type' => $_name[2],
									);
								}
							}

							$_addoptnm[] = implode('^', $_name);
						}

						// ������ ����, ��ǰ�� addoptnm �÷� ����, �̻�� �߰� �ɼ� ����
						$db->query("UPDATE ".GD_GOODS." SET addoptnm = '".implode('|',$_addoptnm)."' WHERE goodsno = '".$Recode['goodsno']."'");
						foreach($_addopts as $_addopt) {
							list( $cnt ) = $db->fetch( "select count(*) from ".GD_GOODS_ADD." where goodsno='" . $Recode['goodsno'] . "' and step='" . $_addopt['step'] . "' and opt='" . $_addopt['opt'] . "' and type = '".$_addopt['type']."'" );

							if ( $cnt < 1 ) $db->query("insert into ".GD_GOODS_ADD." set stats = 1, goodsno='" . $Recode['goodsno'] . "', step='" . $_addopt['step'] . "', opt='" . $_addopt['opt'] . "', addprice='" . $_addopt['addprice'] . "', type = '" . $_addopt['type'] . "'");
							else if ( $cnt == 1 ) $db->query("update ".GD_GOODS_ADD." set stats = 1, addprice='" . $_addopt['addprice'] . "' where goodsno='" . $Recode['goodsno'] . "' and step='" . $_addopt['step'] . "' and opt='" . $_addopt['opt'] . "' and type = '".$_addopt['type']."'");
						}

						$db->query("DELETE FROM ".GD_GOODS_ADD." WHERE goodsno = '".$Recode['goodsno']."' AND stats = 0");

						$addoption_processed = true;

					}
				}
				//---------------------------------------------------------------------------------------------- END //


				{ // ������
					### ������Ʈ �Ͻ�
					$Goods -> update_date($Recode['goodsno']);

					fwrite($tmp_fp,  '<tr><td>line ' . $row . ': </td>');
					fwrite($tmp_fp,  '<td>��ǰ��ȣ</td><td>' . $Recode['goodsno'] . '</td>');
					fwrite($tmp_fp,  '<td>ó�����</td><td>' . ( $getScnt == 0 ? 'INSERT' : 'UPDATE' ) . ' (' . ( $result1 ? 'T' : 'F' ) . ')' . '</td>');
					fwrite($tmp_fp,  '</tr>');

					fwrite($tmp_fp,  PHP_EOL);

				}
			//}
		}

		fclose($fp);
	}


	fwrite($tmp_fp,  '</table></body></html>');
	fclose($tmp_fp);

	$name = urlencode('['. strftime( '%y��%m��%d��' ) .'] ����Ÿ���� ���.xls');
	$path = urlencode($tmp_nm);

	echo '
	<script type="text/javascript">
		parent.nsGodoLoadingIndicator.hide();
		self.location.href = "data_indb_result.php?name='.$name.'&path='.$path.'";
	</script>
	';
	exit;

}


msg( $altMsg, $_SERVER[HTTP_REFERER] );

?>
