<?
	include "../lib.php";
	require_once("../../lib/qfile.class.php");
	$qfile = new qfile();

	$ddlpath = "../../conf/data_goodsddl.ini";

	if ($_POST['limitmethod'] == 'part' && ( $_POST['limit'][0] == '' || $_POST['limit'][1] == '' ))
		msg( '부분다운 하실 경우에는 줄수를 꼭! 입력합니다.', $_SERVER[HTTP_REFERER] );

	if ($_POST['filename'] == '')
		$_POST['filename'] = '[' . strftime( '%y년%m월%d일' ) . '] 상품';

	if ( count( $_POST['field'] ) > 0 ) { // 필드 속성 저장( 다운필드 )

		$addFields = array(
		'use_emoney' => array(
			'text' => '적립금정책',
			'down' => 'N',
			'desc' => '적립금 설정의 정책 적용(0), 적립금 개별 설정(1) 중 택일 입력. 기본값 - 적립금 설정의 정책 적용(0)'
			),
        'extra_info' => array(
            'text' => '상품필수정보',
            'down' => 'N',
            'desc' => '',
            ),
		'naver_event' => array(
			'text' => '이벤트문구',
			'down' => 'Y',
			'desc' => "'마케팅>네이버쇼핑>네이버 쇼핑이벤트 문구 설정>상품별 문구' 선택 후 입력할 상품별 개별 이벤트 문구 입력 (최대 100자 이내)"
			),
		'use_mobile_img' => array(
			'text' => '모바일 전용 이미지 사용여부',
			'down' => 'Y',
			'desc' => "모바일샵 전용 이미지 사용(1), PC 이미지 사용(0) 중 택일 입력, 기본값 - 모바일샵 전용 이미지 사용(1)<br><u style='color:#bf0000;'>모바일샵 전용 이미지 사용(1)으로 설정 시 아래 모바일 메인~확대 이미지가 적용됩니다.<br>PC 이미지 사용(0)으로 설정 시에는 아래 모바일 메인~확대에 사용될 PC 이미지가 적용됩니다.</u>"
			),
		'img_w' => array(
			'text' => '모바일 메인 이미지',
			'down' => 'Y',
			'desc' => "모바일샵 메인에 출력할 이미지명 입력. <u style='color:#bf0000;'> ※ 해당 이미지는 하나의 이미지만 적용해야 정상 적용됩니다.</u>"
			),
		'img_x' => array(
			'text' => '모바일 리스트 이미지',
			'down' => 'Y',
			'desc' => "모바일샵 리스트에 출력할 이미지명 입력. <u style='color:#bf0000;'> ※ 해당 이미지는 하나의 이미지만 적용해야 정상 적용됩니다.</u>"
			),
		'img_y' => array(
			'text' => '모바일 상세 이미지',
			'down' => 'Y',
			'desc' => "모바일샵 상세에 출력할 이미지명 입력. 다수 경우 '|' 를 구분자로 입력. <i>ex) test1.gif|test2.gif</i>"
			),
		'img_z' => array(
			'text' => '모바일 확대 이미지',
			'down' => 'Y',
			'desc' => "모바일샵 확대에 출력할 이미지명 입력. 다수 경우 '|' 를 구분자로 입력. <i>ex) test1.gif|test2.gif</i>"
			),
		'img_pc_w' => array(
			'text' => '모바일 메인에 사용될 PC 이미지',
			'down' => 'Y',
			'desc' => 'PC 이미지의 영문 타이틀명 입력. <i>(메인이미지: img_i / 리스트이미지: img_s / 상세이미지: img_m / 확대이미지: img_l 중 택1)</i>'
			),
		'img_pc_x' => array(
			'text' => '모바일 리스트 사용될 PC 이미지',
			'down' => 'Y',
			'desc' => 'PC 이미지의 영문 타이틀명 입력. <i>(메인이미지: img_i / 리스트이미지: img_s / 상세이미지: img_m / 확대이미지: img_l 중 택1)</i>'
			),
		'img_pc_y' => array(
			'text' => '모바일 상세 사용될 PC 이미지',
			'down' => 'Y',
			'desc' => 'PC 이미지의 영문 타이틀명 입력. <i>(메인이미지: img_i / 리스트이미지: img_s / 상세이미지: img_m / 확대이미지: img_l 중 택1)</i>'
			),
		'img_pc_z' => array(
			'text' => '모바일 확대 사용될 PC 이미지',
			'down' => 'Y',
			'desc' => 'PC 이미지의 영문 타이틀명 입력. <i>(메인이미지: img_i / 리스트이미지: img_s / 상세이미지: img_m / 확대이미지: img_l 중 택1)</i>'
			),
		'naver_import_flag' => array(
			'text' => '수입 및 제작 여부',
			'down' => 'Y',
			'desc' => "유형 : 해외(1), 병행(2), 주문제작(3) 중 해당 사항 택일하여 입력<br>상품이 해외구매대행인 경우 해외, 병행수입인 경우 병행, 주문제작인 경우 주문제작 입력. 해당 사항 없는 경우 표기하지 않음.<br><u style='color:#bf0000;'>※ 네이버쇼핑 3.0에 반영되는 정보로, 해당 상품임에도 해외구매대행 여부가 적절하게 표기되지 않은 경우 노출 중지 및 삭제되며, 클린프로그램이 적용되어 등급이 하락될 수 있습니다.</u>"
			),
		'naver_product_flag' => array(
			'text' => '판매방식 구분',
			'down' => 'Y',
			'desc' => "유형 : 도매(1), 렌탈(2), 대여(3), 할부(4), 예약판매(5), 구매대행(6) 중 해당 사항 택일하여 입력<br>일반적인 판매방식과는 다른 방식으로 판매되는 상품들에 표기<br><u style='color:#bf0000;'>※ 네이버쇼핑 3.0에 반영되는 정보로, 해당 상품임에도 판매방식이 적절하게 표기되지 않은 경우 네이버쇼핑에서 상품이 삭제되며, 클린프로그램이 적용되어 등급이 하락될 수 있습니다.</u>"
			),
		'naver_age_group' => array(
			'text' => '주 이용 고객층',
			'down' => 'Y',
			'desc' => '유형 : 성인(0), 청소년(1), 아동(2), 유아(3) 중 택일하여 입력. 기본값 - 성인(0)<br>상품의 주요 사용층을 텍스트로 기입. 입력하지 않는 경우 ‘성인’으로 처리'
			),
		'naver_gender' => array(
			'text' => '성별',
			'down' => 'Y',
			'desc' => '유형 : 남성(1), 여성(2), 남녀공용(3) 중 해당 사항 택일하여 입력<br>상품의 주요 구매 고객의 성별을 입력'
			),
		'naver_attribute' => array(
			'text' => '상품속성',
			'down' => 'Y',
			'desc' => '상품의 속성 정보를 ‘^’로 구분하여 입력, 최대 500자<br><i>ex) 서울^1개^오션뷰^2명^주중^조식포함^무료주차^와이파이</i>'
			),
		'naver_search_tag' => array(
			'text' => '검색태그',
			'down' => 'Y',
			'desc' => '상품의 검색태그에 대하여 ‘|’(Vertical bar)로 구분하여 입력. 최대 100자<br><i>ex) 물방울패턴원피스|2016S/S신상원피스|결혼식아이템|여친룩</i>'
			),
		'naver_category' => array(
			'text' => '네이버 카테고리',
			'down' => 'Y',
			'desc' => '네이버 카테고리의 ID를 입력. 최대 8자<br>입력하는 경우, 네이버 쇼핑에서 해당 카테고리에 매칭하는데 반영 '
			),
		'naver_product_id' => array(
			'text' => '가격비교 페이지 ID',
			'down' => 'Y',
			'desc' => "네이버 가격비교 페이지 ID를 입력할 경우 네이버 가격비교 추천에 반영. 최대 50자<br><i>ex) http://shopping.naver.com/detail/detail.nhn?nv_mid=<u style='color:#bf0000;'>8535546055</u>&cat_id=50000151</i>"
			),
		);

		$fields = parse_ini_file($ddlpath, true);

		foreach($addFields as $k => $v) {
			if(!$fields[$k]) $fields[$k] = $v;
		}

		$qfile->open( $ddlpath);

		foreach ( $fields as $key => $arr ){

			if ( array_search( $key, $_POST['field'] ) !== false )
				$arr['down'] = 'Y'; else $arr['down'] = 'N';
			$qfile->write("[" . $key . "]" . "\n" );
			$qfile->write("text = \"" . $arr['text'] . "\"" . "\n" );
			$qfile->write("down = \"" . $arr['down'] . "\"" . "\n" );
			$qfile->write("desc = \"" . $arr['desc'] . "\"" . "\n\n" );
		}

		$qfile->close();
		@chMod( $ddlpath, 0707 );
	}

	if ( $_GET['sample'] != 'Y' ) { // 쿼리 실행

		if ($_POST[cate]) {
			$category = array_notnull($_POST[cate]);
			$category = $category[count($category)-1];
		}

		$db_table = "".GD_GOODS." a ";

		if ($category) {
			$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno ";
			// 상품분류 연결방식 전환 여부에 따른 처리
			$where[]	= getCategoryLinkQuery('c.category', $category, 'where');
		}
		if ($_POST[sword])
			$where[] = "$_POST[skey] like '%$_POST[sword]%'";

		if ($_POST[price][0] && $_POST[price][1]) {
			if (preg_match("/".GD_GOODS_OPTION."/", $db_table) == 0) $db_table .= "left join ".GD_GOODS_OPTION." ee on a.goodsno=ee.goodsno and link ";
			$where[] = "ee.price between {$_POST[price][0]} and {$_POST[price][1]}";
		}

		if ($_POST[regdt][0] && $_POST[regdt][1])
			$where[] = "regdt between date_format({$_POST[regdt][0]},'%Y-%m-%d 00:00:00') and date_format({$_POST[regdt][1]},'%Y-%m-%d 23:59:59')";

		if (is_numeric($_POST[open]))
			$where[] = "open=$_POST[open]";

		if ( $_POST['limitmethod'] == 'part' )
			$limit = " limit " . ( $_POST['limit'][0] - 1 ) . ", " . ( $_POST['limit'][1] - $_POST['limit'][0] + 1 );
		else
			$limit = '';

		/*=====================================2007-07-26 add============*/
		if ($_POST[sort] == "price desc" || $_POST[sort] == "price asc") {
			if (preg_match("/".GD_GOODS_OPTION."/", $db_table) == 0) $db_table .= "left join ".GD_GOODS_OPTION." ee on a.goodsno=ee.goodsno and link ";
			$_sort = "ee.".$_POST[sort];
		} else {
			$_sort = $_POST[sort];
		}
		/*=====================================2007-07-26 add============*/

		$sql = "select * from $db_table " . ( count( $where ) ? "where " . implode( " and ", $where ) : "" ) . " order by " . $_sort . $limit; // echo $sql;
		$res = $db->query( $sql );
	}

	setlocale(LC_CTYPE, 'ko_KR.eucKR');
	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-Disposition: attachment; filename=" . $_POST[filename] . ".xls" );
	header( "Content-Description: PHP4 Generated Data" );

	$fields = parse_ini_file($ddlpath, true);

	$extrainfoHelper = Core::loader('extrainfo');
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
				if ( $_GET['sample'] != 'Y' ) {

					while ( $data = $db->fetch($res) ) {

						$addoption_processed = false;
						echo '<tr>';

						foreach ( $fields as $key => $arr ) {

							if ( $arr['down'] != 'Y' ) continue;

							if ( $key == 'goodscate' ) {

								$tmp = array();
								$t_res = $db->query( "select category from ".GD_GOODS_LINK." where goodsno = '" . $data['goodsno'] . "'" );
								while ( $t_row = $db->fetch( $t_res ) ) $tmp[] = $t_row['category'];
								$data[ $key ] = implode( "|", $tmp );

							} else if ( $key == 'opts' ) {

								$tmp = array();
								$t_res = $db->query( "select opt1, opt2, price, consumer, supply, reserve, stock, go_sort from ".GD_GOODS_OPTION." where goodsno = '" . $data['goodsno'] . "' AND go_is_deleted <> '1' order by link desc, sno" );
								while ( $t_row = $db->fetch( $t_res, "MYSQL_ASSOC" ) ) $tmp[] = implode( "^", $t_row );
								$data[ $key ] = htmlspecialchars(implode( "|", $tmp ));

							} else if ( ! $addoption_processed && in_array($key, array('addoptnm','addopts','inputable_addoptnm','inputable_addopts')) ) {

								$addoptnm = array();
								$addopts = array();

								$inputable_addoptnm = array();
								$inputable_addopts = array();

								$_addoptnm = explode( "|", $data['addoptnm'] );
								foreach ( $_addoptnm as $step => $recode ){
									$_tmp = explode('^', $recode);
									if ($_tmp[2] == 'I') {
										$inputable_addoptnm[] = $_tmp[0] . '^' . $_tmp[1];
									}
									else {
										$addoptnm[] = $_tmp[0] . '^' . $_tmp[1];
									}
								}

								$t_res = $db->query("select step, opt, addprice, type from ".GD_GOODS_ADD." where goodsno = '" . $data['goodsno'] . "' order by type, step, sno");
								$_offset = 0;
								while ($t_row = $db->fetch($t_res, 1)) {

									$_addopt = array(
										'',	// 아래 코드에서 추가옵션 이름으로 교체함.
										$t_row['opt'],
										$t_row['addprice'],
									);

									if ($t_row['type'] == 'I') {
										$_addopt[0] = array_shift(explode('^',$inputable_addoptnm[$t_row['step']]));
										$inputable_addopts[] = implode('^',$_addopt);
									}
									else {
										$_addopt[0] = array_shift(explode('^',$addoptnm[$t_row['step']]));
										$addopts[] = implode('^',$_addopt);
									}

								}

								foreach(array('addoptnm','addopts','inputable_addoptnm','inputable_addopts') as $_key) {
									if (array_key_exists($_key, $fields)) {
										$data[ $_key ] = htmlspecialchars(implode( "|", ${$_key} ));
									}
								}

								$addoption_processed = true;

							} else if ( $key == 'longdesc'  || $key == 'goodsnm' || $key == 'goodscd' || $key == 'origin' || $key == 'maker' || $key == 'keyword' || $key == 'strprice' || $key == 'shortdesc' || $key == 'memo' || $key == 'addoptnm' || $key == 'ex_title' || $key == 'ex1' || $key == 'ex2' || $key == 'ex3' || $key == 'ex4' || $key == 'ex5' || $key == 'ex6' || $key == 'naver_event') {
								$data[ $key ] = htmlspecialchars( $data[ $key ] );
							} else if ( $key == 'extra_info' ) {
							    $data[ $key ] = htmlspecialchars($extrainfoHelper->toStr( $data[ $key ] ));
							}

							if ( in_array( $key, array( 'shortdesc', 'longdesc', 'extra_info' ) ) ) echo '<td>' . $data[ $key ] . '</td>';
							else echo '<td class="text">' . $data[ $key ] . '</td>';
						}

						echo '</tr>';
					}
				}
			?>
		</table>
	</body>
</html>
