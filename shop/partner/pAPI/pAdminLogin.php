<?
/*********************************************************
* ���ϸ�     :  pAdminLogin.php
* ���α׷��� :	pad ������ �α��� API
* �ۼ���     :  dn
* ������     :  2011.09.29
**********************************************************/
include "../../lib/library.php";
include "../../conf/config.php";
require_once "../../lib/pAPI.class.php";
require_once "../../lib/json.class.php";

$pAPI = new pAPI();
$json = new Services_JSON(16);

if(!$_POST['id'] || !$_POST['password']) {
	$ret_arr['code'] = '301';
	$ret_arr['msg'] = '���̵�� ��й�ȣ�� Ȯ���� �ֽñ� �ٶ��ϴ�.';

	echo ($json->encode($ret_arr));
	exit;
}

foreach($_POST as $key => $val) {
	${$key} = $val;
}

$query = 'SELECT m.m_no, m.m_id, m.name, m.nickname, m.email, m.status, m.level, g.dc, g.sno gsno';
$query .= ' FROM '.GD_MEMBER.' AS m LEFT JOIN '.GD_MEMBER_GRP.' AS g ON m.level=g.level';
$query .= ' WHERE m.level=[i] AND m.m_id = [s] AND m.password in (password([s]),old_password([s]),[s])';

$mem_query = $db->_query_print($query, 100, $id, $password, $password, md5($password));
$result = $db->_select($mem_query);
$result = $result[0];

if(!$result['m_no']) {
	$ret_arr['code'] = '301';
	$ret_arr['msg'] = '���̵�� ��й�ȣ�� Ȯ���� �ֽñ� �ٶ��ϴ�.';

	echo ($json->encode($ret_arr));
	exit;
}

$ret_arr['code'] = '000';
$ret_arr['msg'] = '����';

echo ($json->encode($ret_arr));
exit;
?>