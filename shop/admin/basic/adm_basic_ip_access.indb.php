<?php
/*
 * IP ���� ���� ���� (������ IP ���� ����, ���θ� IP ���� ���� ����)
 * @author artherot @ godosoft development team.
 */

include dirname(__FILE__).'/../lib.php';

// IP ���� ���� ����
$saveResult				= $IPAccessRestriction->saveAccessIP();

if ($saveResult === 'NO_DATA') {
	msg('IP�������� ������ �ٽ� Ȯ���� �ּ���.');
}
else if ($saveResult === 'NO_IP') {
	msg('IP�������� ������ ó���� IP �� ����� �ּ���.');
} else if ($saveResult === true) {
	msg('IP�������� ������ �Ϸ� �Ǿ����ϴ�.',$_SERVER['HTTP_REFERER'],'parent');
}
?>