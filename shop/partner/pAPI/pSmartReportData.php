<?
/*********************************************************
* ���ϸ�     :  pSmartReportData.php
* ���α׷��� :	pad ����Ʈ����Ʈ DATA API
* �ۼ���     :  dn
* ������     :  2011.10.06
**********************************************************/
include "../../lib/library.php";
include "../../conf/config.php";
require_once "../../lib/pAPI.class.php";
require_once "../../lib/json.class.php";

$pAPI = new pAPI();
$json = new Services_JSON(16);

### ����Ű Check (�����δ� ���̵�� ��� ��) ���� ###
if(!$_POST['authentic']) {
	$res_data['code'] = '302';
	$res_data['msg'] = '����Ű�� �����ϴ�.';
	echo ($json->encode($res_data));
	exit;
}

if(!$pAPI->keyCheck($_POST['authentic'])) {
	$res_data['code'] = '302';
	$res_data['msg'] = '����Ű�� ���� �ʽ��ϴ�.';
	echo ($json->encode($res_data));
	exit;
}
unset($_POST['authentic']);
### ����Ű Check �� ###

### �ֹ� DATA START ###
$sterm = 1;
$eterm = 0;

$sdate = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m'), date('d') - $sterm, date('Y')));
$edate = date('Y-m-d 23:59:59', mktime(0, 0, 0, date('m'), date('d') - $eterm, date('Y')));

$ord_data = Array();
$c_data = Array();
$d_data = Array();

for($i = $sterm; $i >= $eterm; $i--) {
	$key_date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - $i, date('Y')));
	$ord_data[$key_date] = 0;
	$c_data[$key_date] = 0;
	$d_data[$key_date] = 0;
}

### �ֹ����� ###
$ord_query = $db->_query_print('SELECT count(*) as cnt_ord, SUBSTRING(orddt, 1, 10) as ord_date FROM '.GD_ORDER.' WHERE orddt > [s] AND orddt < [s] AND step2 < [i] GROUP BY SUBSTRING(orddt, 1, 10)', $sdate, $edate, 40);
$res_ord = $db->_select($ord_query);

if(!empty($res_ord) && is_Array($res_ord)) {
	foreach($res_ord as $row_ord) {
		$ord_data[$row_ord['ord_date']] = $row_ord['cnt_ord'];
	}
}

### �Ա�Ȯ�� ###
$c_query = $db->_query_print('SELECT count(*) as cnt_c, SUBSTRING(cdt, 1, 10) as c_date FROM '.GD_ORDER.' WHERE cdt > [s] AND cdt < [s] AND step > [i] AND step2 < [i] GROUP BY SUBSTRING(cdt, 1, 10)', $sdate, $edate, 0, 40);
$res_c = $db->_select($c_query);

if(!empty($res_c) && is_Array($res_c)) {
	foreach($res_c as $row_c) {
		$c_data[$row_c['c_date']] = $row_c['cnt_c'];
	}
}

### ��ۿϷ� ###
$d_query = $db->_query_print('SELECT count(*) as cnt_d, SUBSTRING(ddt, 1, 10) as d_date FROM '.GD_ORDER.' WHERE ddt > [s] AND ddt < [s] AND step > [i] AND step2 < [i] GROUP BY SUBSTRING(ddt, 1, 10)', $sdate, $edate, 0, 40);
$res_d = $db->_select($d_query);

if(!empty($res_d) && is_Array($res_d)) {
	foreach($res_d as $row_d) {
		$d_data[$row_d['d_date']] = $row_c['cnt_d'];
	}
}

### �ְ� ���� ���� ###
$sterm = 6;
$eterm = 0;

$sdate = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m'), date('d') - $sterm, date('Y')));
$edate = date('Y-m-d 23:59:59', mktime(0, 0, 0, date('m'), date('d') - $eterm, date('Y')));

$arr_week = Array('(��)', '(��)', '(ȭ)', '(��)', '(��)', '(��)', '(��)');

$sale_query = $db->_query_print('SELECT SUM(prn_settleprice) as sum_sale, SUBSTRING(cdt, 1, 10) as c_date, ordno FROM '.GD_ORDER.' WHERE cdt > [s] AND cdt < [s] AND step > [i] AND step < [i] GROUP BY SUBSTRING(cdt, 1, 10)', $sdate, $edate, 0, 40);

$res_sale = $db->_select($sale_query);

$d_data = Array();

$sale_data = Array();

for($i = $sterm; $i >= $eterm; $i--) {
	$key_date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - $i, date('Y')));
	$tmp_week = date('w',mktime(0,0,0, substr($key_date, 5, 2), substr($key_date, 8, 2), substr($key_date, 0, 4)));
	$tmp_date = date('m/d',mktime(0,0,0, substr($key_date, 5, 2), substr($key_date, 8, 2), substr($key_date, 0, 4)));
	$sale_data[$key_date]['str_date'] = $tmp_date.$arr_week[$tmp_week];
	$sale_data[$key_date]['sum_sale'] = 0;
}

if(!empty($res_sale) && is_Array($res_sale)) {
	foreach($res_sale as $row_sale) {
		$sale_data[$row_sale['c_date']]['sum_sale'] = $row_sale['sum_sale'];
	}
}

### �ֱ� ���� ���� ###
$sale_trends = Array();

$tmp_sdate = date('Y-m-d');
$tmp_edate = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));

$sale_trends[$tmp_edate] = Array();
$sale_trends[$tmp_sdate] = Array();

$sale_trends[$tmp_sdate]['str_date'] = '����';
$sale_trends[$tmp_sdate]['sum_sale'] = $sale_data[$tmp_sdate]['sum_sale'];


$sale_trends[$tmp_edate]['str_date'] = '����';
$sale_trends[$tmp_edate]['sum_sale'] = $sale_data[$tmp_edate]['sum_sale'];


if($sale_trends[$tmp_sdate]['sum_sale'] == 0) {
	$tmp_percent = 0;
}
else if($sale_trends[$tmp_edate]['sum_sale'] == 0) {
	$tmp_percent = 100;
}
else {
	$tmp_percent = round((($sale_trends[$tmp_sdate]['sum_sale'] / $sale_trends[$tmp_edate]['sum_sale'])) * 100);
}

$sale_trends[$tmp_sdate]['percent'] = $tmp_percent;
$sale_trends[$tmp_edate]['percent'] = 0;

### ���� ���� ���� ###
$sterm = 6;
$eterm = 0;

$sdate = date('Y-m-01 00:00:00', mktime(0, 0, 0, date('m') - $sterm, date('d'), date('Y')));
$edate = date('Y-m-31 23:59:59', mktime(0, 0, 0, date('m') - $eterm, date('d'), date('Y')));

$msale_query = $db->_query_print('SELECT SUM(prn_settleprice) as sum_sale, SUBSTRING(cdt, 1, 7) as c_date, ordno FROM '.GD_ORDER.' WHERE cdt > [s] AND cdt < [s] AND step > [i] AND step < [i] GROUP BY SUBSTRING(cdt, 1, 7)', $sdate, $edate, 0, 40);

$res_msale = $db->_select($msale_query);

for($i = $sterm; $i >= $eterm; $i--) {
	$key_date = date('Y-m', mktime(0, 0, 0, date('m') - $i, 1, date('Y')));
	$tmp_month = date('n',mktime(0,0,0, substr($key_date, 5, 2), 1, substr($key_date, 0, 4)));
	$msale_data[$key_date]['str_date'] = $tmp_month.'��';
	$msale_data[$key_date]['sum_sale'] = 0;
}

if(!empty($res_msale) && is_Array($res_msale)) {
	foreach($res_msale as $row_msale) {
		$msale_data[$row_msale['c_date']]['sum_sale'] = $row_msale['sum_sale'];
	}
}

### �湮 ###
$sterm = 1;
$eterm = 0;

$sdate = date('Ymd', mktime(0,0,0, date('m'), date('d') - $sterm, date('Y')));
$edate = date('Ymd', mktime(0,0,0, date('m'), date('d') - $eterm, date('Y')));

$visit_query = $db->_query_print('SELECT day, uniques, pageviews FROM '.MINI_COUNTER.'  WHERE day >= [i] AND day <=[i]', $sdate, $edate);

$res_visit = $db->_select($visit_query);

$visit_data = Array();

for($i = $sterm; $i >= $eterm; $i--) {
	$key_date = date('Ymd', mktime(0, 0, 0, date('m'), date('d') - $i, date('Y')));
	$visit_data[$key_date]['uniques'] = 0;
	$visit_data[$key_date]['pageviews'] = 0;
}

if(!empty($res_visit) && is_Array($res_visit)) {
	foreach($res_visit as $row_visit){ 

		$visit_data[$row_visit['day']]['uniques'] = $row_visit['uniques'];
		$visit_data[$row_visit['day']]['pageviews'] = $row_visit['pageviews'];
	}
}

### ȸ�� ###
$mem_total_query = $db->_query_print('SELECT count(*) as cnt_mem FROM '.GD_MEMBER.' WHERE 1=1');
$res_mem_total = $db->_select($mem_total_query);
$row_mem_total = $res_mem_total[0];

$tdate = date('Y-m-d');
$mem_today_query = $db->_query_print('SELECT count(*) as cnt_mem FROM '.GD_MEMBER.' WHERE SUBSTRING(regdt, 1, 10)=[s]', $tdate);
$res_mem_today = $db->_select($mem_today_query);
$row_mem_today = $res_mem_today[0];

$member_data['��ȸ��'] = number_format($row_mem_total['cnt_mem']).'��';
$member_data['��  ��'] = number_format($row_mem_today['cnt_mem']).'��';

### ���� ###
$qna_query = $db->_query_print('SELECT count(sno) as cnt_qna FROM '.GD_GOODS_QNA.' WHERE sno=parent');
$res_qna = $db->_select($qna_query);
$row_qna = $res_qna[0];

$one_query = $db->_query_print('SELECT count(sno) as cnt_one FROM '.GD_MEMBER_QNA.' WHERE sno=parent');
$res_one = $db->_select($one_query);
$row_one = $res_one[0];

$noreply_one_query = $db->_query_print('SELECT count(a.parent) as cnt_noreply_one FROM (SELECT count(sno) cnt_sno, parent FROM '.GD_MEMBER_QNA.' WHERE 1=1 GROUP BY parent) a WHERE a.cnt_sno > [i]', 1);

$res_noreply_one = $db->_select($noreply_one_query);
$row_noreply_one = $res_noreply_one[0];

$qna_data['�� �� ��'] = number_format($row_qna['cnt_qna']).'��';
$qna_data['1:1����'] = number_format($row_one['cnt_one']).'��';
$qna_data['��ǰ����'] = number_format($row_noreply_one['cnt_noreply_one']).'��';

### ret ���� ###

for($i = $sterm; $i >= $eterm; $i--) {	//�ֹ�
	if($i==1) {
		$tmp_key = '����';
	} else if($i==0) {
		$tmp_key = '����';
	}
	
	$key_date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - $i, date('Y')));

	$ret_data['order'][$tmp_key]['�ֹ�����'] = number_format($ord_data[$key_date]).'��';
	$ret_data['order'][$tmp_key]['�Ա�Ȯ��'] = number_format($c_data[$key_date]).'��';
	$ret_data['order'][$tmp_key]['��ۿϷ�'] = number_format($d_data[$key_date]).'��';
}

$ret_data['sale_trend'] = $sale_trends;	//�ֱ� ���� ����

for($i = $sterm; $i >= $eterm; $i--) {	//�湮
	if($i==1) {
		$tmp_key = '����';
	} else if($i==0) {
		$tmp_key = '����';
	}
	
	$key_date = date('Ymd', mktime(0, 0, 0, date('m'), date('d') - $i, date('Y')));

	$ret_data['visit_data'][$tmp_key]['�� �� ��'] = number_format($visit_data[$key_date]['uniques']).'��';
	$ret_data['visit_data'][$tmp_key]['��������'] = number_format($visit_data[$key_date]['pageviews']).'��';
}

$ret_data['sale_week'] = $sale_data;	//�ְ� ���� ����
$ret_data['member_data'] = $member_data;	//ȸ��
$ret_data['qna_data'] = $qna_data;	//����
$ret_data['sale_month'] = $msale_data;	//���� ���� ����
$ret_data['hot_issue'] = $issue_data; //�� ���̽�
echo ($json->encode($ret_data));

exit;
?>