<?php

// C1. ���̺귯�� ��Ŭ���
include dirname(__FILE__).'/../lib.php';

// C2. ���� �� ���ε�
@include dirname(__FILE__).'/../../conf/config.mobileShop.php';
$mobilians = Core::loader('Mobilians');
$danal = Core::loader('Danal');
$shopConfig = Core::loader('config')->load('config');
$paymentConfig = Core::loader('config')->load('configpay');

// C3. ��������
$serviceId = $_POST['serviceId'];
$serviceType = $_POST['serviceType'];
$pg_centersetting = $_POST['pg_centersetting'];

// C4. �Ϲݼ��θ� �޴������� ���Ȯ��
if ($paymentConfig['use']['h'] === 'on') {
	exit('
	<script type="text/javascript">
	var isConfirm = confirm("���� ���ڰ��� ������ �޴��� ������ ������� ������ ���ÿ� ����� �� �����ϴ�.\r\n������� ���񽺸� �̿��Ͻ÷��� ���� ���� ���ڰ��� ���� ���������� �޴��� ������ ������� �ʵ��� �����Ͽ��ֽñ� �ٶ��ϴ�.\r\n���� ���ڰ��� ���� �������� �̵� �Ͻðڽ��ϱ�?");
	if (isConfirm) {
		parent.location.replace("'.$shopConfig['rootDir'].'/admin/basic/pg.php");
	}
	</script>
	');
}

// C5. ����ϼ� �޴������� ���Ȯ��
if ($paymentConfig['use_mobile']['h'] === 'on') {
	if ($cfgMobileShop['mobileShopRootDir'] !== '/m2') {
		exit('
		<script type="text/javascript">
		var isConfirm = confirm("����ϼ� ���ڰ��� ������ �޴��� ������ ������� ������ ���ÿ� ����� �� �����ϴ�.\r\n������� ���񽺸� �̿��Ͻ÷��� ���� ����ϼ� ���ڰ��� ���� ���������� �޴��� ������ ������� �ʵ��� �����Ͽ��ֽñ� �ٶ��ϴ�.\r\n����ϼ� ���ڰ��� ���� �������� �̵� �Ͻðڽ��ϱ�?");
		if (isConfirm) {
			parent.location.replace("'.$shopConfig['rootDir'].'/admin/mobileShop/mobile_pg.php");
		}
		</script>
		');
	}
	else {
		exit('
		<script type="text/javascript">
		var isConfirm = confirm("����ϼ� ���ڰ��� ������ �޴��� ������ ������� ������ ���ÿ� ����� �� �����ϴ�.\r\n������� ���񽺸� �̿��Ͻ÷��� ���� ����ϼ� ���ڰ��� ���� ���������� �޴��� ������ ������� �ʵ��� �����Ͽ��ֽñ� �ٶ��ϴ�.\r\n����ϼ� ���ڰ��� ���� �������� �̵� �Ͻðڽ��ϱ�?");
		if (isConfirm) {
			parent.location.replace("'.$shopConfig['rootDir'].'/admin/mobileShop2/mobile_pg.php");
		}
		</script>
		');
	}
}

// C5_1 �޴������� ���Ȯ��
if ($danal->isEnabled() === true) {
	msg('���� �ٳ� �޴��� ���� ���񽺸� ������Դϴ�.\r\n������� �޴��� ���� ���񽺸� ���� �� �� �ٽ� ���� ���ּ���.',-1);
	exit;
}

// C6. ����ID Ȯ��
if (strlen(trim($serviceId)) < 1) {
	msg('����ID�� �Էµ��� �ʾҽ��ϴ�.', -1);
	exit;
}
// C7. ���񽺻��� Ȯ��
else if (strlen(trim($serviceType)) < 1) {
	msg('����ȯ���� ���õ��� �ʾҽ��ϴ�.', -1);
	exit;
}
// C8. ���񽺾��̵� prefix Ȯ��
else if ($mobilians->checkPrefix($serviceId) !== true) {
	msg('Prefix ������ �����Ͽ����ϴ�.', -1);
	exit;
}
// C9. ���� ����
else {
	$mobilians->saveConfig($serviceId, $serviceType, $pg_centersetting);
}

?>
<script type="text/javascript">
alert("���������� ����Ǿ����ϴ�.");
parent.location.reload();
</script>