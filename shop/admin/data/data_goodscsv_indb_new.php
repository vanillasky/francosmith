<?
include "../lib.php";

set_time_limit(300); // 5��
$Goods = Core::loader('Goods');
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

	// CSV ���� �ҷ�����
	$csv = Core::helper('CSV', $_FILES['file_excel'][tmp_name]);
	$header = $csv->getHeader();

	// ��밡���� �÷�
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

	// �ʼ� �÷� (�ƿ� �����Ǿ����� üũ)
	$req_columns = array();
	foreach(array(
		'goodsnm',
		//'goodscate', // �ʼ� �÷����� ����
		) as $req_name) {
		if ( ($req_columns[] = array_search($req_name, $columns)) === false) {
			msg( $req_name.' �÷��� �ʼ� �Դϴ�.' );
			exit;
		}
	}

	$row_cnt = 0;

	list($goodsno) = $db->fetch("select max(goodsno) from ".GD_GOODS."");

	foreach ($csv as $row) {

		// �ʼ� �Է� �÷� üũ (���̶�� ���� row �� �Ѿ)
		foreach($req_columns as $key)
			if (empty($row[$key])) continue 2;

		++$goodsno;

		// body �� ����
		$_gd_goods = array();
		$_gd_goods_link = array();
		$_gd_goods_option = array();

		foreach($columns as $idx => $col) {

			if (isset($row[$idx])) {

				if (($val = (string)trim($row[$idx])) === '') continue;

				if ($col == 'goodscate') {
					// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
					$_gd_goods_link = getHighCategoryCode($val);
				}
				elseif ($col == 'optnm1' || $col == 'optnm2') {

					$tmp = explode('=', $val);
					$_gd_goods_option[]  =  explode(';',$tmp[1]);

					// �ɼǸ�
					$_gd_goods['optnm'] .= !empty($_gd_goods['optnm']) ? '|'.$tmp[0] : $tmp[0];

				}
				elseif ($col == 'price' || $col == 'stock') {

					${'g_'.$col} = preg_replace('/[^0-9]/','',$val);

				}
				else {

					// ����ȭ (�� �ʵ�� �׳� ����)
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

		// �� ó�� �� db ����
		if (sizeof($_gd_goods) > 0) {

			$_gd_goods['goodsno']	= $goodsno;
			$_gd_goods['regdt']		= Core::helper('Date')->format(G_CONST_NOW);
			$_gd_goods['updatedt']	= Core::helper('Date')->format(G_CONST_NOW);
			$_gd_goods['option_name'] = $_gd_goods['optnm'];
			$_gd_goods['option_value'] = implode(',',$_gd_goods_option[0]).'|'.implode(',',$_gd_goods_option[1]);

			// ��ǰ���� ����
			$rs = $db->procedure( 'save_goods', $_gd_goods );

			// �ɼ� ����
			if ($rs) { // �ɼ��� ��� �⺻ 1���� ��ϵǾ�� ��

				$idx = 0;
				$tot_stock = 0;

				$opt1s = @array_shift($_gd_goods_option);
				$opt2s = @array_shift($_gd_goods_option);

				if (empty($opt1s)) $opt1s = array('');
				if (empty($opt2s)) $opt2s = array('');

				foreach($opt1s as $opt1) {
					foreach($opt2s as $opt2) {

						//����� null �� ��� 0���� �Է�
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

						// ��ǰ ��ǥ ���� (������, ���԰�, �Һ��ڰ��� �ű� ��� �������� ���� �ȵ�)
						if ($idx == 0) {
							$sp_param['link'] = 1;
							$goods_price = $g_price;
						}
						$db->insert(GD_GOODS_OPTION)->set($sp_param)->query();

						$idx++;

					}
				}

				// ��ǰ �� ���, �ɼ� ��뿩�� ����
				$db->update(GD_GOODS)->set(array('totstock'=>$tot_stock, 'use_option'=>($idx > 1 ? 1 : 0), 'goods_price' => $goods_price))->where('goodsno = ?', $goodsno)->query();

			}

			// ī�װ�
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

				### �̺�Ʈ ī�װ� ����
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

		// ó��
		$row_cnt++;

		fwrite($tmp_fp,  '<tr><td>line ' . $row_cnt . ': </td>');
		fwrite($tmp_fp,  '<td>��ǰ��ȣ</td><td>' . $goodsno . '</td>');
		fwrite($tmp_fp,  '<td>ó�����</td><td>INSERT (' . ( $rs ? 'T' : 'F' ) . ')' . '</td>');
		fwrite($tmp_fp,  '</tr>');

		fwrite($tmp_fp,  PHP_EOL);

	}

	fwrite($tmp_fp,  '</table></body></html>');
	fclose($tmp_fp);

	$name = urlencode('['. strftime( '%y��%m��%d��' ) .'] ����Ÿ���� ���.xls');
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