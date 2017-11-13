<?
include "../lib.php";

set_time_limit(300); // 5분
$Goods = Core::loader('Goods');
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

	// CSV 파일 불러오기
	$csv = Core::helper('CSV', $_FILES['file_excel'][tmp_name]);
	$header = $csv->getHeader();

	// 사용가능한 컬럼
	$availableColumns = array(
		'goodsnm',
		'goodscate',
		'goodscd',
		'origin',
		'maker',
		'brandno',
		'tax',
		'delivery_type',
		'keyword',
		'strprice',
		'longdesc',
		'img_i',
		'img_s',
		'img_m',
		'img_l',
		'launchdt',
		'open',
		'price',
		'stock',
		'use_emoney',
		'usestock',
		'opttype',
		'optnm1',
		'optnm2',
		'memo',
		'naver_event',
		'use_mobile_img',
		'img_w',
		'img_x',
		'img_y',
		'img_z',
		'img_pc_w',
		'img_pc_x',
		'img_pc_y',
		'img_pc_z',
		'naver_import_flag',
		'naver_product_flag',
		'naver_age_group',
		'naver_gender',
		'naver_attribute',
		'naver_search_tag',
		'naver_category',
		'naver_product_id',
	);

	$columns = array_intersect( array_keys($header), $availableColumns);
	asort($columns);

	// 필수 컬럼 (아예 누락되었는지 체크)
	$req_columns = array();
	foreach(array(
		'goodsnm',
		//'goodscate', // 필수 컬럼에서 제외
		) as $req_name) {
		if ( ($req_columns[] = array_search($req_name, $columns)) === false) {
			msg( $req_name.' 컬럼은 필수 입니다.' );
			exit;
		}
	}

	$row_cnt = 0;

	list($goodsno) = $db->fetch("select max(goodsno) from ".GD_GOODS."");

	foreach ($csv as $row) {

		// 필수 입력 컬럼 체크 (빈값이라면 다음 row 로 넘어감)
		foreach($req_columns as $key)
			if (empty($row[$key])) continue 2;

		++$goodsno;

		// body 를 생성
		$_gd_goods = array();
		$_gd_goods_link = array();
		$_gd_goods_option = array();

		foreach($columns as $idx => $col) {

			if (isset($row[$idx])) {

				if (($val = (string)trim($row[$idx])) === '') continue;

				if ($col == 'goodscate') {
					// 상품분류 연결방식 전환 여부에 따른 처리
					$_gd_goods_link = getHighCategoryCode($val);
				}
				elseif ($col == 'optnm1' || $col == 'optnm2') {

					$tmp = explode('=', $val);
					$_gd_goods_option[]  =  explode(';',$tmp[1]);

					// 옵션명
					$_gd_goods['optnm'] .= !empty($_gd_goods['optnm']) ? '|'.$tmp[0] : $tmp[0];

				}
				elseif ($col == 'price' || $col == 'stock') {

					${'g_'.$col} = preg_replace('/[^0-9]/','',$val);

				}
				else {

					// 정규화 (외 필드는 그냥 삽입)
					switch ($col) {
						case 'tax':
							if (!in_array($val, array('0','1'))) $val = '1';
							break;
						case 'delivery_type':
							if (!in_array($val, array('0','1'))) $val = '0';
							break;
						case 'brandno':
							$val = preg_replace('/[^0-9]/','',$val);
							break;
						case 'open':
							if (!in_array($val, array('0','1'))) $val = '0';
							break;
						case 'use_emoney':
							if (!in_array($val, array('0','1'))) $val = '0';
							break;
						case 'usestock':
							if (!in_array(strtolower($val), array('o',''))) $val = '';
							break;
						case 'opttype':
							if (!in_array(strtolower($val), array('single','double'))) $val = 'single';
							break;
					}


					$_gd_goods[$col] = $val;
				}
			}

		}

		// 후 처리 및 db 저장
		if (sizeof($_gd_goods) > 0) {

			$_gd_goods['goodsno']	= $goodsno;
			$_gd_goods['regdt']		= Core::helper('Date')->format(G_CONST_NOW);
			$_gd_goods['updatedt']	= Core::helper('Date')->format(G_CONST_NOW);
			$_gd_goods['option_name'] = $_gd_goods['optnm'];
			$_gd_goods['option_value'] = implode(',',$_gd_goods_option[0]).'|'.implode(',',$_gd_goods_option[1]);

			// 상품정보 저장
			$rs = $db->procedure( 'save_goods', $_gd_goods );

			// 옵션 저장
			if ($rs) { // 옵션이 없어도 기본 1개는 등록되어야 함

				$idx = 0;
				$tot_stock = 0;

				$opt1s = @array_shift($_gd_goods_option);
				$opt2s = @array_shift($_gd_goods_option);

				if (empty($opt1s)) $opt1s = array('');
				if (empty($opt2s)) $opt2s = array('');

				foreach($opt1s as $opt1) {
					foreach($opt2s as $opt2) {

						//재고량이 null 일 경우 0으로 입력
						if($g_stock === null) $g_stock = 0;

						$tot_stock += $g_stock;

						$sp_param = array(
							'goodsno' => $goodsno,
							'opt1' => $opt1,
							'opt2' => $opt2,

							'price' => (int) $g_price,
							'consumer' => 0,
							'supply' => 0,
							'reserve' => 0,
							'stock' => $g_stock,

							'link' => 0,
							'go_is_display' => 1,
							'go_is_deleted' => 0,

						);

						// 상품 대표 가격 (적립금, 매입가, 소비자가는 신규 등록 폼에서는 지원 안됨)
						if ($idx == 0) {
							$sp_param['link'] = 1;
							$goods_price = $g_price;
						}
						$db->insert(GD_GOODS_OPTION)->set($sp_param)->query();

						$idx++;

					}
				}

				// 상품 총 재고량, 옵션 사용여부 갱신
				$db->update(GD_GOODS)->set(array('totstock'=>$tot_stock, 'use_option'=>($idx > 1 ? 1 : 0), 'goods_price' => $goods_price))->where('goodsno = ?', $goodsno)->query();

			}

			// 카테고리
			if ($rs && sizeof($_gd_goods_link) > 0) {

				$goodsLinkSort = array();
				$maxSortIncrease = array();
				$linkSortIncrease = array();
				$resultSet = $db->select(GD_GOODS_LINK, 'category, sort1, sort2, sort3, sort4')->where('goodsno = ?', $goodsno);
				while ($goodsLink = $resultSet->fetch()) {
					for ($length = 3; $length <= strlen($data['category']); $length+=3) {
						$goodsLinkSort[substr($data['category'], 0, $length)] = $goodsLink['sort'.($length/3)];
					}
				}

				foreach($_gd_goods_link as &$_category) {

					if (($_category = (string)trim($_category)) === '') continue;

					$_category = str_pad($_category, ceil(strlen($_category) / 3 ) * 3, '0', STR_PAD_LEFT);

					list( $cnt ) = $db->select(GD_GOODS_LINK, 'count(*)')->where('goodsno = ?', $goodsno)->where('category = ?', $_category)->fetch();
					if ( $cnt < 1 ){
						list( $sort ) = $db->select(GD_GOODS_LINK, 'max(sno)')->fetch();
						$sp_param = array(
							'goodsno' => $goodsno,
							'category' => $_category,
							//'sort' => G_CONST_NOW * -1, // $sort++ ?
						);
						foreach ($goodsSort->getManualSortInfoHierarchy($_category) as $categorySortSet) {
							if (strlen($_category)/3 >= $categorySortSet['depth']) {
								if ($goodsLinkSort[$categorySortSet['category']]) {
									$sp_param[$categorySortSet['sort_field']] = $goodsLinkSort[$categorySortSet['category']];
								}
								else {
									if ($categorySortSet['manual_sort_on_link_goods_position'] === 'FIRST') {
										if (isset($linkSortIncrease[$categorySortSet['category']]) === false) {
											$goodsSort->increaseCategorySort($categorySortSet['category'], $categorySortSet['sort_field']);
											$linkSortIncrease[$categorySortSet['category']] = true;
										}
										$sp_param[$categorySortSet['sort_field']] = 1;
									}
									else {
										$sp_param[$categorySortSet['sort_field']] = ((int)$categorySortSet['sort_max']+1);
									}
									$maxSortIncrease[$categorySortSet['category']] = true;
								}
							}
						}
						$db->insert(GD_GOODS_LINK)->set($sp_param)->query();

						$last_sno = $db->lastID();
						$goods_link_sort = (G_CONST_NOW * -1) - $last_sno;
						$db->query("update ".GD_GOODS_LINK." SET sort=".$goods_link_sort." where sno = ".$last_sno);
					}

				}
				foreach (array_keys($maxSortIncrease) as $category) $goodsSort->increaseSortMax($category);

				### 이벤트 카테고리 연결
				$stmt = $db->prepare("select b.* from ".GD_GOODS_LINK." a, ".GD_EVENT." b where a.category=b.category and a.goodsno=?");
				$stmt->execute($goodsno);
				$i=0;
				while($tmp = $stmt->fetch()){
					$mode = "e".$tmp['sno'];
					list($cnt) = $db->fetch("select count(*) from ".GD_GOODS_DISPLAY." where mode = '$mode' and goodsno='".$goodsno."'");
					if($cnt == 0){
						list($sort) = $db->fetch("select max(sort) from ".GD_GOODS_DISPLAY." where mode = '$mode'");
						$sort++;
						$db->insert(GD_GOODS_DISPLAY)->set(array(
							'goodsno' => $goodsno,
							'mode' => $mode,
							'sort' => $sort,
						))->query();
					}
				}
			}

		}

		// 처리
		$row_cnt++;

		fwrite($tmp_fp,  '<tr><td>line ' . $row_cnt . ': </td>');
		fwrite($tmp_fp,  '<td>상품번호</td><td>' . $goodsno . '</td>');
		fwrite($tmp_fp,  '<td>처리결과</td><td>INSERT (' . ( $rs ? 'T' : 'F' ) . ')' . '</td>');
		fwrite($tmp_fp,  '</tr>');

		fwrite($tmp_fp,  PHP_EOL);

	}

	fwrite($tmp_fp,  '</table></body></html>');
	fclose($tmp_fp);

	$name = urlencode('['. strftime( '%y년%m월%d일' ) .'] 데이타이전 결과.xls');
	$path = urlencode($tmp_nm);

	echo '
	<script type="text/javascript">
		parent.nsGodoLoadingIndicator.hide();
		self.location.href = "./data_indb_result.php?name='.$name.'&path='.$path.'";
	</script>
	';
	exit;
}

msg( $altMsg, $_SERVER[HTTP_REFERER] );
?>