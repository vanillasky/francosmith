<?
$channel = preg_replace('/_form\.([a-z]+)\.php/','$1',basename(__FILE__));
// �ܺ� �ֹ����̹Ƿ� �߰輭���� �����͸� ����ȭ ���� �����ش�.

/*
$integrate_order = Core::loader('integrate_order');
$integrate_order -> doSync();
*/

// �ֹ�����
$orderInfo = $db->fetch("SELECT * FROM ".GD_INTEGRATE_ORDER." WHERE channel = '$channel' AND ordno = '".$_GET['ordno']."'",1);

if (!$orderInfo) {
	msg('�ֹ������� �������� �ʽ��ϴ�.',-1);
	exit;
}

?>
<div class="title title_top">�ֹ��󼼳���</div>
<? @include dirname(__FILE__) . '/../selly/_market_order_form.php';	//�����ֹ�_��Ŭ��� ?>
<div style="height:20px;"></div>
