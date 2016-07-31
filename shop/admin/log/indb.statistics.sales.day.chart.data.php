<?
include "../lib.php";

/*
	��� �������� �׷����� open flash chart (http://teethgrinder.co.uk/open-flash-chart )�� �����
	LGPL ���̼�����.
*/
include '../../lib/ofc/php-ofc-library/open-flash-chart.php';

$r_yoil = array("��","��","ȭ","��","��","��","��");
$where = array();

$datatype = $_POST['datatype'];

// �˻� ����
	$sdate_s = ($_POST['regdt'][0]) ? date('Y-m-d',strtotime($_POST['regdt'][0])) : date('Y-m',G_CONST_NOW).'-01';
	$sdate_e = ($_POST['regdt'][1]) ? date('Y-m-d', strtotime('+1 day', strtotime($_POST['regdt'][1]))) : date('Y-m',strtotime('+1 month', strtotime($sdate_s))).'-01';

	$sword = isset($_POST['sword']) ? iconv('utf-8','euc-kr',$_POST['sword']) : '';
	if ($sword) {
		$where[] = $_POST['skey']." like '%$sword%'";
	}

	$brandnm = isset($_POST['brandnm']) ? iconv('utf-8','euc-kr',$_POST['brandnm']) : '';
	if ($brandnm) {
		$where[] = " OI.brandnm = '$brandnm'";
	}

	if (sizeof($_POST['settlekind']) < 1 || $_POST['settlekind']['all']) {
		$_POST['settlekind'] = array();
		$_POST['settlekind']['all'] = 1;
	}
	elseif (sizeof($_POST['settlekind']) === 6) {
		$_POST['settlekind'] = array();
		$_POST['settlekind']['all'] = 1;
	}
	else {
		$_tmp = array();
		foreach($_POST['settlekind'] as $k => $v) {
			if (!$v || $k == 'all') continue;

			if ($k == 'r') {	// ������
				$_tmp[] = " O.emoney > 0 ";
			}
			elseif ($k == 'cp') {	// ����
				$_tmp[] = " O.coupon > 0 ";
			}
			else {
				$_tmp[] = " O.settlekind = '".$k."'";
			}

		}

		if (!empty($_tmp)) $where[] = ' ('.implode(' OR ',$_tmp).') ';
	}


// sql
	if (empty($_POST['dtkind'])) $dtkind = 'orddt';
	else $dtkind = $_POST['dtkind'];


	switch ($datatype) {
		case 'yoil':
			$_date = "DATE_FORMAT(O.$dtkind,'%w') AS `date`,";
			break;
		case 'hour':
			$_date = "DATE_FORMAT(O.$dtkind,'%H') AS `date`,";
			break;
		case 'day':
		default :
			$_date = "DATE_FORMAT(O.$dtkind,'%Y-%m-%d') AS `date`,";
			break;
	}


	$sub_query = "
	SELECT
		$_date
		O.$dtkind AS $dtkind,

		O.emoney,			/* ������ ��� �ݾ� */
		(O.coupon + O.memberdc) AS dc,	/* ����, ȸ������ �ݾ� */
		O.goodsprice,			/* ��ǰ���� */
		O.prn_settleprice,		/* �����ݾ� */
		O.delivery,		/* �����ݾ��� ��ۺ� */

		SUM(OI.supply * OI.ea) AS sub_supply	/* ���Աݾ� */

	FROM ".GD_ORDER." AS O

	INNER JOIN ".GD_ORDER_ITEM." AS OI
	ON O.ordno = OI.ordno
	LEFT JOIN ".GD_GOODS." AS G
	ON OI.goodsno = G.goodsno
	";

	$_param = array(
		$dtkind,
		Core::helper('Date')->min($sdate_s),
		Core::helper('Date')->max($sdate_e)
	);

	$where[] = vsprintf("O.%s between '%s' and '%s'", $_param);
	$where[] = "O.step2 < 40";

	$sub_query .= ' WHERE '.implode(' AND ', $where);
	$sub_query .= ' GROUP BY O.ordno ';
	$sub_query .= ' ORDER BY NULL ';



	$query = "
	SELECT
		SUB.`date`,
		COUNT(SUB.$dtkind) AS cnt,
		SUM(SUB.emoney) AS tot_emoney,			/* ������ ��� �ݾ� */
		SUM(SUB.dc) AS tot_dc,	/* ����, ȸ������ �ݾ� */
		SUM(SUB.goodsprice) AS tot_price,			/* ��ǰ���� */
		SUM(SUB.prn_settleprice) AS tot_settle,		/* �����ݾ� */
		SUM(SUB.delivery) AS tot_delivery,		/* �����ݾ��� ��ۺ� */
		SUM(SUB.sub_supply) AS tot_supply	/* ���Աݾ� */

	FROM
	(
		".$sub_query."

	) AS SUB
	";
	$query .= ' GROUP BY SUB.`date` ';
	$query .= ' ORDER BY SUB.`date` ';

// ����
$rs = $db->query($query);
$rs_max = mysql_num_rows($rs);

$total = $arRow = $chart = array();

$multi = floor($rs_max / 10);

while ($_row = $db->fetch($rs,1)) {

	$row['date']		= $_row['date'];

	$row['payment_cnt']	= $_row['cnt'];
	$row['tot_emoney']	= $_row['tot_emoney'];
	$row['tot_dc']		= $_row['tot_dc'];
	$row['tot_price']	= $_row['tot_price'];
	$row['tot_settle']	= $_row['tot_settle'];
	$row['tot_delivery']		= $_row['tot_delivery'];
	$row['tot_supply']	= $_row['tot_supply'];

	$row['tot_sales']	= $row['tot_settle'] - $row['tot_delivery'];
	$row['tot_earn']	= $row['tot_sales'] - $row['tot_supply'];

	$total = get_total($total, $row);

	$arRow[] = $row;

	// ��Ʈ ������
	$chart['data'][1][] = (int)$row['tot_sales'];
	$chart['data'][2][] = (int)$row['tot_supply'];
	$chart['data'][3][] = (int)$row['tot_earn'];

	$_m = max( array($row['tot_sales'],$row['tot_supply'],$row['tot_earn']));
	$chart['y_max'] = ($chart['y_max'] > $_m) ? (int)$chart['y_max'] : $_m;

	switch ($datatype) {
		case 'yoil':
			$chart['x_label'][] = iconv('EUC-KR','UTF-8',$r_yoil[$row['date']]);
			break;
		case 'hour':
			$chart['x_label'][] = iconv('EUC-KR','UTF-8', $row['date'].'��');
			break;
		case 'day':
		default :
			$chart['x_label'][] = $row['date'];
			break;
	}
}

//  �׷���

	// �Ϻ�
		$step = 1;
		$chart['color'][1] = '#A6A6A6';
		$chart['color'][2] = '#92D050';
		$chart['color'][3] = '#FF0000';

		$chart['Key'][1] = '����ݾ�';
		$chart['Key'][2] = '���Աݾ�';
		$chart['Key'][3] = '�Ǹ�����';

		$ofc = new open_flash_chart();

		$tmp = pow(10,strlen($chart['y_max']) - 1);
		$chart['y_max'] =  ceil($chart['y_max'] / $tmp) * $tmp;

		$y = new y_axis();
		$y->set_range(0, $chart['y_max']);
		$y->set_colours( '#595D63', '#DEDEDE');

		$x = new x_axis();
		$x->set_colours( '#595D63', '#DEDEDE');

		$x_labels = new x_axis_labels();
		$x_labels->set_colour( '#595D63' );

		switch ($datatype) {
			case 'yoil':
				$x_labels->set_steps( 1 );
				break;
			case 'hour':
				$x_labels->set_steps( 1 );
				break;
			case 'day':
			default :
				$x_labels->set_steps( ceil($rs_max / 11) );
				break;
		}

		$x_labels->set_labels( $chart['x_label'] );
		$x_labels->set_size( 12 );

		$x->set_labels( $x_labels );

		$ofc->set_x_axis( $x );
		$ofc->set_y_axis( $y );
		$ofc->set_bg_colour( '#FFFFFF' );

		foreach($chart['data'] as $k => $data) {
			${'data'} = $data;
			${'bar'.$k} = new bar();
			${'bar'.$k}->colour( $chart['color'][$k] );
			${'bar'.$k}->set_values( $data );
			${'bar'.$k}->key( iconv('euc-kr','utf-8',$chart['Key'][$k]) , 12);

			$ofc->add_element( ${'bar'.$k} );
		}

		echo $ofc->toPrettyString();
?>
