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
$serviceType = $_POST['serviceType'];

// C2. ��� ������ �������� üũ
$danalCfg = $danal->getConfig();
if ($danalCfg['S_CPID'] == '') {
	msg('�ٳ� ���񽺸� ��û�� �ֽñ� �ٶ��ϴ�.',-1);
}

// C4. �Ϲݼ��θ� �޴������� ���Ȯ��
if ($paymentConfig['use']['h'] === 'on') {
	exit('
	<script type="text/javascript">
	var isConfirm = confirm("���� ���ڰ��� ������ �޴��� ������ �ٳ� ������ ���ÿ� ����� �� �����ϴ�.\r\n�ٳ� ���񽺸� �̿��Ͻ÷��� ���� ���� ���ڰ��� ���� ���������� �޴��� ������ ������� �ʵ��� �����Ͽ��ֽñ� �ٶ��ϴ�.\r\n���� ���ڰ��� ���� �������� �̵� �Ͻðڽ��ϱ�?");
	if (isConfirm) {
		parent.location.replace("'.$shopConfig['rootDir'].'/admin/basic/pg.php");
	}
	else {
		history.back();
	}
	</script>
	');
}

// C5. ����ϼ� �޴������� ���Ȯ��
if ($paymentConfig['use_mobile']['h'] === 'on') {
	if ($cfgMobileShop['mobileShopRootDir'] !== '/m2') {
		exit('
		<script type="text/javascript">
		var isConfirm = confirm("����ϼ� ���ڰ��� ������ �޴��� ������ �ٳ� ������ ���ÿ� ����� �� �����ϴ�.\r\n�ٳ� ���񽺸� �̿��Ͻ÷��� ���� ����ϼ� ���ڰ��� ���� ���������� �޴��� ������ ������� �ʵ��� �����Ͽ��ֽñ� �ٶ��ϴ�.\r\n����ϼ� ���ڰ��� ���� �������� �̵� �Ͻðڽ��ϱ�?");
		if (isConfirm) {
			parent.location.replace("'.$shopConfig['rootDir'].'/admin/mobileShop/mobile_pg.php");
		}
		else {
			history.back();
		}
		</script>
		');
	}
	else {
		exit('
		<script type="text/javascript">
		var isConfirm = confirm("����ϼ� ���ڰ��� ������ �޴��� ������ �ٳ� ������ ���ÿ� ����� �� �����ϴ�.\r\n�ٳ� ���񽺸� �̿��Ͻ÷��� ���� ����ϼ� ���ڰ��� ���� ���������� �޴��� ������ ������� �ʵ��� �����Ͽ��ֽñ� �ٶ��ϴ�.\r\n����ϼ� ���ڰ��� ���� �������� �̵� �Ͻðڽ��ϱ�?");
		if (isConfirm) {
			parent.location.replace("'.$shopConfig['rootDir'].'/admin/mobileShop2/mobile_pg.php");
		}
		else {
			history.back();
		}
		</script>
		');
	}
}

// C6 �޴������� ���Ȯ��
if ($mobilians->isEnabled() === true) {
	msg('���� ������� �޴��� ���� ���񽺸� ������Դϴ�.\r\n������� �޴��� ���� ���񽺸� ���� �� �� �ٽ� ���� ���ּ���.',-1);
}

// C7. ���� ����
else {
	$danal->saveConfig('','','','',$serviceType,'');
}

?>
<script type="text/javascript">
alert("���������� ����Ǿ����ϴ�.");
parent.location.reload();
</script>