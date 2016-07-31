<?

include "../lib.php";
require_once "../../lib/load.class.php";

$Goods = Core::loader('Goods');
$extrainfoHelper = Core::loader('extrainfo');
$goodsSort = Core::loader('GoodsSort');

$cfgByte	= trim( preg_replace( "'m'si", "", get_cfg_var( 'upload_max_filesize' ) ) ) * ( 1024 * 1024 ); # 업로드최대용량 : mb * ( kb * b )
$fileByte	= filesize( $_FILES['file_excel'][tmp_name] ); # 파일용량


if ( empty( $_FILES['file_excel'][name] ) ) $altMsg = 'CSV파일을 선택하지 않으셨습니다.'; // 화일이 없으면
else if ( !preg_match("/.csv$/i", $_FILES['file_excel'][name] ) ) $altMsg = 'CSV 파일만 업로드 하실 수 있습니다.'; // 확장자 체크
else if ( $fileByte > $cfgByte ) $altMsg = get_cfg_var( 'upload_max_filesize' ) . '이하의 파일만 업로드 하실 수 있습니다.'; // 업로드최대용량 초과
else { // 화일이 있으면

	setlocale(LC_ALL, "ko_KR.eucKR"); //fgetcsv 함수 사용시, lang 설정에 따른 오류 방지

	// 처리 결과를 파일에 저장하여 다운로더로 이동함.
	$tmp_nm = tempnam( G_CONST_DOCROOT."/cache", "xls");
	$tmp_fp = fopen($tmp_nm, "w");

	fwrite($tmp_fp, '<html><head><title>list</title><meta http-equiv="Content-Type" content="text/html; charset=euc-kr"><style>.xl31{mso-number-format:"0_\)\;\\\(0\\\)";}</style></head><body><table border="1">'.PHP_EOL);

	{ // CSV 파일의 전체 항목을 읽어 DB에 insert

		$row = 0;
		$fp = fopen( $_FILES['file_excel'][tmp_name], 'r' );
		$etcField = array( 'goodscate', 'opts', 'addopts', 'addoptnm', 'inputable_addopts', 'inputable_addoptnm' ); # 별도처리 필드

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
			//if($data[$FieldNm[goodsno]]){ //널체크 by birdmarine 2007.1.30
				if(!$data[$FieldNm[goodsnm]]) continue;
				$row++;

				//----------------------------------------------------------------------------------------------//
				$etcRecode = $Recode = array();

				if ( strlen( $data[$FieldNm[goodsno]] ) > 10 ){ // 기본키 값 체크

					print "<tr><td>line $row:	</td><td>상품번호</td><td>" . $data[$FieldNm[goodsno]] . "</td><td>처리결과</td><td>NOT PROCESS : goodsno 10자리이상임.</td></tr>";
					continue;
				}
				else if( $data[$FieldNm[goodsno]] == '' ){ // 기본키 값 셋

					list( $data[$FieldNm[goodsno]] ) = $db->fetch("select max(goodsno) + 1 from ".GD_GOODS."");
					if ( $data[$FieldNm[goodsno]] == '' ) $data[$FieldNm[goodsno]] = $row;
				}

				foreach ( $FieldNm as $key => $dataIdx ){ // Recode 배열 구성

					if ( in_array( $key, $etcField ) ){
						$etcRecode[$key] = addslashes( trim( $data[$dataIdx] ) ); // 별도처리 필드
						continue;
					}
					else {
						// 제품 필드
						$_v = (string)trim($data[$dataIdx]);

						// 빈값이면 continue. 'NULL' 이면 빈값으로 갱신
						if ($_v === '') continue;
						elseif (strtoupper($_v) === 'NULL') $_v = '';

						$Recode[$key] = ($key == 'extra_info') ? $_v : addslashes( $_v );
					}

					if ( $key == 'regdt' && $Recode["regdt"] == '' ) $Recode["regdt"] = date('Y-m-d H:i:s'); // 등록일

					if ( $key == 'tax' && in_array( $Recode[$key], array( '0', '비과세' ) ) ) $Recode[$key] = '0'; // 비과세
					else if ( $key == 'tax' ) $Recode[$key] = '1'; // 과세

					if ( $key == 'delivery_type' && in_array( $Recode[$key], array( '1', '무료배송' ) ) ) $Recode[$key] = '1'; // 무료배송
					else if ( $key == 'delivery_type' ) $Recode[$key] = '0'; // 기본배송비 사용

					if ( $key == 'open' && in_array( $Recode[$key], array( '1', '보이기' ) ) ) $Recode[$key] = '1'; // 상품출력여부 - 보이기
					else if ( $key == 'open' ) $Recode[$key] = '0'; // 상품출력여부 - 감추기

					if ( $key == 'runout' && in_array( $Recode[$key], array( '1', '품절' ) ) ) $Recode[$key] = '1'; // 품절상품 - 품절
					else if ( $key == 'runout' ) $Recode[$key] = '0'; // 품절상품 - 판매

					if ( $key == 'usestock' && in_array( $Recode[$key], array( 'o', '재고량빠짐' ) ) ) $Recode[$key] = 'o'; // 재고량연동 - 재고량빠짐
					else if ( $key == 'usestock' ) $Recode[$key] = ''; // 재고량연동 - 무한정판매

					if ( $key == 'opttype' && in_array( $Recode[$key], array( 'double', '분리형' ) ) ) $Recode[$key] = 'double'; // 옵션출력방식 - 분리형
					else if ( $key == 'opttype' ) $Recode[$key] = 'single'; // 옵션출력방식 - 일체형

					if ( $key == 'relationis' && in_array( $Recode[$key], array( '1', '수동' ) ) ) $Recode[$key] = '1'; // 관련상품방식 - 수동
					else if ( $key == 'relationis' ) $Recode[$key] = '0'; // 관련상품방식 - 자동

					if ( $key == 'extra_info' ) $Recode[$key] = addslashes($extrainfoHelper->toJson($Recode[$key]));    // 쿼리 생성시 escape 하는 경우, addslashes 하면 안됨.

				} // end foreach


				{ // Recode 배열 저장

					if ( count( $Recode ) < 1 ) continue;

                    list( $getScnt, $chkInterpark ) = $db->fetch( "select count(*), inpk_prdno from ".GD_GOODS." where goodsno='" . $Recode['goodsno'] . "'" );
                    if ($getScnt && $chkInterpark) unset($Recode['extra_info']);    // 인터파크 연동 상품의 필수 정보 컬럼은 업데이트 하지 않음

					$tmpSQL = array();
					foreach ( $Recode as $key => $value ) $tmpSQL[] = "$key='$value'";

					// 시즌4 신규 상점은 상품 등록일 항목이 삭제 되었으므로, insert 시 등록일을 설정한다.
					if ($getScnt == 0 && !array_key_exists('regdt',$Recode)) {
						$tmpSQL[] = "regdt = NOW()";
					}

					// 옵션 사용 여부 (옵션 등록 처리후 다시 갱신)
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

				foreach ( $etcRecode as $key => $value ){ // 별도처리 필드

					if ( $key == 'goodscate' ){ // 상품분류

						if ( trim( $value ) == '' ){
							$db->query( "delete from ".GD_GOODS_LINK." where goodsno='" . $Recode['goodsno'] . "'" );
							continue;
						}

						// 상품분류 연결방식 전환 여부에 따른 처리
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

						### 이벤트 카테고리 연결
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

					else if ( $key == 'opts' ){ // 가격/재고 옵션목록

						if ( trim( $value ) == '' ){
							$db->query( "update ".GD_GOODS_OPTION." set go_is_deleted = '1' where goodsno='" . $Recode['goodsno'] . "'" );
							// 단일 상품이라도 1개의 옵션은 입력되어야 함
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
							$price		= preg_replace( '/[^0-9]/', '', $price ); // 숫자만 저장
							$consumer	= preg_replace( '/[^0-9]/', '', $consumer ); // 숫자만 저장
							$supply		= preg_replace( '/[^0-9]/', '', $supply ); // 숫자만 저장
							$reserve	= preg_replace( '/[^0-9]/', '', $reserve ); // 숫자만 저장
							$stock		= preg_replace( '/[^0-9]/', '', $stock ); // 숫자만 저장
							$go_sort	= preg_replace( '/[^0-9]/', '', $go_sort ); // 숫자만 저장

							if ($idx == 0) {
								$link = 1;

								// 상품 대표 가격, 적립금, 매입가, 소비자가
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

					else if ( ! $addoption_processed && in_array($key, array('addopts', 'addoptnm', 'inputable_addopts', 'inputable_addoptnm'))) { //추가상품목록

						$db->query("update ".GD_GOODS_ADD." SET stats = 0 WHERE goodsno = '".$Recode['goodsno']."'");

						foreach(array('addopts', 'addoptnm', 'inputable_addopts', 'inputable_addoptnm') as $_key) {
							$_val = str_replace( "\n", "", $etcRecode[$_key] );
							${$_key} = $_val ? explode('|', $_val) : array();
						}

						// 데이터 생성
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

						// 데이터 저장, 상품의 addoptnm 컬럼 갱신, 미사용 추가 옵션 삭제
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


				{ // 결과출력
					### 업데이트 일시
					$Goods -> update_date($Recode['goodsno']);

					fwrite($tmp_fp,  '<tr><td>line ' . $row . ': </td>');
					fwrite($tmp_fp,  '<td>상품번호</td><td>' . $Recode['goodsno'] . '</td>');
					fwrite($tmp_fp,  '<td>처리결과</td><td>' . ( $getScnt == 0 ? 'INSERT' : 'UPDATE' ) . ' (' . ( $result1 ? 'T' : 'F' ) . ')' . '</td>');
					fwrite($tmp_fp,  '</tr>');

					fwrite($tmp_fp,  PHP_EOL);

				}
			//}
		}

		fclose($fp);
	}


	fwrite($tmp_fp,  '</table></body></html>');
	fclose($tmp_fp);

	$name = urlencode('['. strftime( '%y년%m월%d일' ) .'] 데이타이전 결과.xls');
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
