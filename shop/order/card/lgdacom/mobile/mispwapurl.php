<?php
//�ش� �������� ����ڰ� ISP{����/BC) ī�� ������ �����Ͽ��� ��, ����ڿ��� �������� �������Դϴ�.
include "../../../../lib/library.php";
include "../../../../conf/config.mobileShop.php";
include "../../../../conf/config.php";
include "../../../../conf/pg_mobile.lgdacom.php";

$LGD_OID= $HTTP_GET_VARS["LGD_OID"];

$card_nm="ISP";
$sql="select step,step2 from ".GD_ORDER." where ordno='".$LGD_OID."'";
$data = $db->fetch($sql);
if($data[step]==1 && $data[step2]==0){	//�������� step,step2 = 1,0:���� 0,54:����
	$goUrl=$cfgMobileShop['mobileShopRootDir']."/ord/order_end.php?ordno=".$LGD_OID."&card_nm=".$card_nm;
}
else{
	$goUrl=$cfgMobileShop['mobileShopRootDir']."/ord/order_fail.php?ordno=".$LGD_OID;
}
	// ���������ÿ���, ���翡�� ������ �ֹ���ȣ (LGD_OID)�� �ش��������� �����մϴ�.  
	// LGD_KVPMISPNOTEURL ���� ������  �����������  �����Ͽ�  ����ڿ��� ������  �����Ϸ�ȭ���� �����Ͻñ� �ٶ��,
	// ��������� LGD_KVPMISPNOTEURL �� ���� ���۵ǹǷ� �ش���� DB������  ����� �̿��Ͽ� �����ϷῩ�θ� ���̵��� �մϴ�.    
	
	////////////////////////////////////////////////////////////////////////////////////////////////////////
	// ����, ���翡�� 'App To App' ������� ����, BCī��翡�� ���� ���� ������ �ް� ������ ���� �����ϰ��� �Ҷ� 
	// ���� ���� initilize function�� ����޴� Custom URL�� ȣ���ϸ� �˴ϴ�.
	// ex) window.location.href = smartxpay://TID=1234567890&OID=0987654321 
	//
	// window.location.href = "���� �۸�://" �� ȣ���Ͻø� �˴ϴ�. 
	////////////////////////////////////////////////////////////////////////////////////////////////////////
go($goUrl);
?>