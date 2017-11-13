<?
	include "../lib.php";
	require_once("../../lib/qfile.class.php");
	$qfile = new qfile();

	$ddlpath = "../../conf/data_goodsddl.ini";

	if ($_POST['limitmethod'] == 'part' && ( $_POST['limit'][0] == '' || $_POST['limit'][1] == '' ))
		msg( '�κдٿ� �Ͻ� ��쿡�� �ټ��� ��! �Է��մϴ�.', $_SERVER[HTTP_REFERER] );

	if ($_POST['filename'] == '')
		$_POST['filename'] = '[' . strftime( '%y��%m��%d��' ) . '] ��ǰ';

	if ( count( $_POST['field'] ) > 0 ) { // �ʵ� �Ӽ� ����( �ٿ��ʵ� )

		$addFields = array(
		'use_emoney' => array(
			'text' => '��������å',
			'down' => 'N',
			'desc' => '������ ������ ��å ����(0), ������ ���� ����(1) �� ���� �Է�. �⺻�� - ������ ������ ��å ����(0)'
			),
        'extra_info' => array(
            'text' => '��ǰ�ʼ�����',
            'down' => 'N',
            'desc' => '',
            ),
		'naver_event' => array(
			'text' => '�̺�Ʈ����',
			'down' => 'Y',
			'desc' => "'������>���̹�����>���̹� �����̺�Ʈ ���� ����>��ǰ�� ����' ���� �� �Է��� ��ǰ�� ���� �̺�Ʈ ���� �Է� (�ִ� 100�� �̳�)"
			),
		'use_mobile_img' => array(
			'text' => '����� ���� �̹��� ��뿩��',
			'down' => 'Y',
			'desc' => "����ϼ� ���� �̹��� ���(1), PC �̹��� ���(0) �� ���� �Է�, �⺻�� - ����ϼ� ���� �̹��� ���(1)<br><u style='color:#bf0000;'>����ϼ� ���� �̹��� ���(1)���� ���� �� �Ʒ� ����� ����~Ȯ�� �̹����� ����˴ϴ�.<br>PC �̹��� ���(0)���� ���� �ÿ��� �Ʒ� ����� ����~Ȯ�뿡 ���� PC �̹����� ����˴ϴ�.</u>"
			),
		'img_w' => array(
			'text' => '����� ���� �̹���',
			'down' => 'Y',
			'desc' => "����ϼ� ���ο� ����� �̹����� �Է�. <u style='color:#bf0000;'> �� �ش� �̹����� �ϳ��� �̹����� �����ؾ� ���� ����˴ϴ�.</u>"
			),
		'img_x' => array(
			'text' => '����� ����Ʈ �̹���',
			'down' => 'Y',
			'desc' => "����ϼ� ����Ʈ�� ����� �̹����� �Է�. <u style='color:#bf0000;'> �� �ش� �̹����� �ϳ��� �̹����� �����ؾ� ���� ����˴ϴ�.</u>"
			),
		'img_y' => array(
			'text' => '����� �� �̹���',
			'down' => 'Y',
			'desc' => "����ϼ� �󼼿� ����� �̹����� �Է�. �ټ� ��� '|' �� �����ڷ� �Է�. <i>ex) test1.gif|test2.gif</i>"
			),
		'img_z' => array(
			'text' => '����� Ȯ�� �̹���',
			'down' => 'Y',
			'desc' => "����ϼ� Ȯ�뿡 ����� �̹����� �Է�. �ټ� ��� '|' �� �����ڷ� �Է�. <i>ex) test1.gif|test2.gif</i>"
			),
		'img_pc_w' => array(
			'text' => '����� ���ο� ���� PC �̹���',
			'down' => 'Y',
			'desc' => 'PC �̹����� ���� Ÿ��Ʋ�� �Է�. <i>(�����̹���: img_i / ����Ʈ�̹���: img_s / ���̹���: img_m / Ȯ���̹���: img_l �� ��1)</i>'
			),
		'img_pc_x' => array(
			'text' => '����� ����Ʈ ���� PC �̹���',
			'down' => 'Y',
			'desc' => 'PC �̹����� ���� Ÿ��Ʋ�� �Է�. <i>(�����̹���: img_i / ����Ʈ�̹���: img_s / ���̹���: img_m / Ȯ���̹���: img_l �� ��1)</i>'
			),
		'img_pc_y' => array(
			'text' => '����� �� ���� PC �̹���',
			'down' => 'Y',
			'desc' => 'PC �̹����� ���� Ÿ��Ʋ�� �Է�. <i>(�����̹���: img_i / ����Ʈ�̹���: img_s / ���̹���: img_m / Ȯ���̹���: img_l �� ��1)</i>'
			),
		'img_pc_z' => array(
			'text' => '����� Ȯ�� ���� PC �̹���',
			'down' => 'Y',
			'desc' => 'PC �̹����� ���� Ÿ��Ʋ�� �Է�. <i>(�����̹���: img_i / ����Ʈ�̹���: img_s / ���̹���: img_m / Ȯ���̹���: img_l �� ��1)</i>'
			),
		'naver_import_flag' => array(
			'text' => '���� �� ���� ����',
			'down' => 'Y',
			'desc' => "���� : �ؿ�(1), ����(2), �ֹ�����(3) �� �ش� ���� �����Ͽ� �Է�<br>��ǰ�� �ؿܱ��Ŵ����� ��� �ؿ�, ��������� ��� ����, �ֹ������� ��� �ֹ����� �Է�. �ش� ���� ���� ��� ǥ������ ����.<br><u style='color:#bf0000;'>�� ���̹����� 3.0�� �ݿ��Ǵ� ������, �ش� ��ǰ�ӿ��� �ؿܱ��Ŵ��� ���ΰ� �����ϰ� ǥ����� ���� ��� ���� ���� �� �����Ǹ�, Ŭ�����α׷��� ����Ǿ� ����� �϶��� �� �ֽ��ϴ�.</u>"
			),
		'naver_product_flag' => array(
			'text' => '�ǸŹ�� ����',
			'down' => 'Y',
			'desc' => "���� : ����(1), ��Ż(2), �뿩(3), �Һ�(4), �����Ǹ�(5), ���Ŵ���(6) �� �ش� ���� �����Ͽ� �Է�<br>�Ϲ����� �ǸŹ�İ��� �ٸ� ������� �ǸŵǴ� ��ǰ�鿡 ǥ��<br><u style='color:#bf0000;'>�� ���̹����� 3.0�� �ݿ��Ǵ� ������, �ش� ��ǰ�ӿ��� �ǸŹ���� �����ϰ� ǥ����� ���� ��� ���̹����ο��� ��ǰ�� �����Ǹ�, Ŭ�����α׷��� ����Ǿ� ����� �϶��� �� �ֽ��ϴ�.</u>"
			),
		'naver_age_group' => array(
			'text' => '�� �̿� ����',
			'down' => 'Y',
			'desc' => '���� : ����(0), û�ҳ�(1), �Ƶ�(2), ����(3) �� �����Ͽ� �Է�. �⺻�� - ����(0)<br>��ǰ�� �ֿ� ������� �ؽ�Ʈ�� ����. �Է����� �ʴ� ��� �����Ρ����� ó��'
			),
		'naver_gender' => array(
			'text' => '����',
			'down' => 'Y',
			'desc' => '���� : ����(1), ����(2), �������(3) �� �ش� ���� �����Ͽ� �Է�<br>��ǰ�� �ֿ� ���� ���� ������ �Է�'
			),
		'naver_attribute' => array(
			'text' => '��ǰ�Ӽ�',
			'down' => 'Y',
			'desc' => '��ǰ�� �Ӽ� ������ ��^���� �����Ͽ� �Է�, �ִ� 500��<br><i>ex) ����^1��^���Ǻ�^2��^����^��������^��������^��������</i>'
			),
		'naver_search_tag' => array(
			'text' => '�˻��±�',
			'down' => 'Y',
			'desc' => '��ǰ�� �˻��±׿� ���Ͽ� ��|��(Vertical bar)�� �����Ͽ� �Է�. �ִ� 100��<br><i>ex) ��������Ͽ��ǽ�|2016S/S�Ż���ǽ�|��ȥ�ľ�����|��ģ��</i>'
			),
		'naver_category' => array(
			'text' => '���̹� ī�װ�',
			'down' => 'Y',
			'desc' => '���̹� ī�װ��� ID�� �Է�. �ִ� 8��<br>�Է��ϴ� ���, ���̹� ���ο��� �ش� ī�װ��� ��Ī�ϴµ� �ݿ� '
			),
		'naver_product_id' => array(
			'text' => '���ݺ� ������ ID',
			'down' => 'Y',
			'desc' => "���̹� ���ݺ� ������ ID�� �Է��� ��� ���̹� ���ݺ� ��õ�� �ݿ�. �ִ� 50��<br><i>ex) http://shopping.naver.com/detail/detail.nhn?nv_mid=<u style='color:#bf0000;'>8535546055</u>&cat_id=50000151</i>"
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

	if ( $_GET['sample'] != 'Y' ) { // ���� ����

		if ($_POST[cate]) {
			$category = array_notnull($_POST[cate]);
			$category = $category[count($category)-1];
		}

		$db_table = "".GD_GOODS." a ";

		if ($category) {
			$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno ";
			// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
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
										'',	// �Ʒ� �ڵ忡�� �߰��ɼ� �̸����� ��ü��.
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
