<?php

include dirname(__FILE__).'/../../../lib/library.php';

// ������ Ȯ��
if ($ici_admin !== true) exit;

// �ֹ���ȣ Ȯ��
if (isset($_GET['ordno']) === false || (int)$_GET['ordno'] < 1) exit;

$cardCancel = Core::loader('cardCancel');
$mobilians = Core::loader('Mobilians');

$ordno = $_GET['ordno'];
$orderData = $db->fetch('SELECT cardtno, settleprice FROM '.GD_ORDER.' WHERE ordno='.$ordno.' LIMIT 1', true);

$gResultcd = $mobilians->paymentCancel($ordno, $orderData['cardtno'], $orderData['settleprice']);

if ($gResultcd === '0000') {
	$cardCancel->cancel_proc($ordno, '['.date('Y-m-d H:i:s').'] ������� : ����');
}
else if ($gResultcd === '0044') {
	$cardCancel->cancel_proc($ordno, '['.date('Y-m-d H:i:s').'] ������� : �̹� ���ó�� �Ǿ� �ֹ� ������Ʈ');
}

/*
========================================================================
����ڵ�              ��� ��û ��� �ش� �ڵ� ����
========================================================================
0000 ����ó��
0014 ����
0020 ���������� ����ġ(SKT,LGT�� ��� ������������濡 ���� ���� �� ��� ����)
0041 �ŷ����� ������
0042 ��ұⰣ���
0044 �ߺ� ��� ��û
0045 ��� ��û �� ��� ���� ����ġ
0097 ��û�ڷ� ����
0098 ��Ż� ��ſ���
0099 ��Ÿ
========================================================================
*/

?>
<html>
	<head>
		<title>mcash �޴������ ���</title>
	</head>
	<body bgcolor="#FFFFFF" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0">
		<script type="text/javascript" charset="<?php echo $_CFG['global']['charset']; ?>">
		var resultCode = "<?php echo $gResultcd; ?>";
		switch (resultCode) {
			case "0000" :
				alert("���������� ��ҵǾ����ϴ�.");
				parent.location.reload();
				break;
			case "0014" :
				alert("������ ������ �Դϴ�.");
				break;
			case "0020" :
				alert("�������� ��Ż� ������ ����Ǿ� ���ó���� �Ұ��� �մϴ�.");
				break;
			case "0041" :
				alert("�ŷ������� �������� �ʽ��ϴ�.");
				break;
			case "0042" :
				alert("��� �Ⱓ�� ����Ͽ� ���ó���� �Ұ��� �մϴ�.\r\n���ó���� ���� ��� ���ϱ����� �����մϴ�.");
				break;
			case "0044" :
				alert("�̹� ���ó�� �Ǿ����ϴ�.\r\n�ֹ����� ���������� ��һ��·� �����մϴ�.");
				parent.location.reload();
				break;
			case "0045" :
				alert("��û�� ��������� ��ġ���� �ʽ��ϴ�.");
				break;
			case "0097" :
				alert("��û�� ������ ������ �ֽ��ϴ�.");
				break;
			case "0098" :
				alert("��Ż�� ��� ������ �߻��Ͽ����ϴ�.\r\n����Ŀ� �ٽ� �õ��Ͽ��ֽñ� �ٶ��ϴ�.");
				break;
			default :
				alert("�˼����� �����Դϴ�.");
				break;
		}
		</script>
	</body>
</html>
