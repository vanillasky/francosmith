<?php

include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
include dirname(__FILE__)."/../../../../conf/pg.agspay.php";

$UserId = "";	 //ȸ�����̵�
$StoreNm = "";	// ������
$pg_mobile = $pg;

if(!preg_match('/mypage/',$_SERVER[SCRIPT_NAME])){
	$item = $cart -> item;
}

foreach($item as $v){
	$i++;
	if($i == 1) $ordnm = $v[goodsnm];
}
$ordnm = strip_tags($ordnm);
if($i > 1){
	if(strlen($ordnm) > 90 )	$ordnm = substr($ordnm,0,90);
	$ordnm .= " ��".($i-1)."��";
}


switch ($_POST[settlekind]){
	case "c":	// �ſ�ī��
		$settlekind		= "card";
		break;
	case "v":	// �������
		$settlekind		= "virtual";
		break;
	case "h":	// �ڵ���
		$settlekind		= "hp";
		break;
}

//ȸ�����̵�
$UserId = ($sess) ? $sess['m_id']: 'guest';
//������
$StoreNm = ($cfg['compName']) ? $cfg['compName'] : $_SERVER['SERVER_NAME'];
?>

<script language=javascript>

var _ua = window.navigator.userAgent.toLowerCase();

var browser = {
	model: _ua.match(/(samsung-sch-m490|sonyericssonx1i|ipod|iphone)/) ? _ua.match(/(samsung-sch-m490|sonyericssonx1i|ipod|iphone)/)[0] : "",
	skt : /msie/.test( _ua ) && /nate/.test( _ua ),
	lgt : /msie/.test( _ua ) && /([010|011|016|017|018|019]{3}\d{3,4}\d{4}$)/.test( _ua ),
	opera : (/opera/.test( _ua ) && /(ppc|skt)/.test(_ua)) || /opera mobi/.test( _ua ),
	ipod : /webkit/.test( _ua ) && /\(ipod/.test( _ua ) ,
	iphone : /webkit/.test( _ua ) && /\(iphone/.test( _ua ),
	lgtwv : /wv/.test( _ua ) && /lgtelecom/.test( _ua )
};

if(browser.opera) {
	document.write("<meta name=\"viewport\" content=\"user-scalable=no, initial-scale=0.75, maximum-scale=0.75, minimum-scale=0.75\" \/>");
} else if (browser.ipod || browser.iphone) {
	setTimeout(function() { if(window.pageYOffset == 0){ window.scrollTo(0, 1);} }, 100);
}

function Pay(){
	form = document.frmAGS_pay;
	if(Check_Common(form) == true){
		form.submit();
	}
}

function Check_Common(form){
	if(form.StoreId.value == ""){
		alert("�������̵� �Էµ��� �ʾҽ��ϴ�.\n�ٽ� �ŷ��� �õ����ֽñ� �ٶ��ϴ�.");
		return false;
	}
	else if(form.StoreNm.value == ""){
		alert("�������� �Էµ��� �ʾҽ��ϴ�.\n�ٽ� �ŷ��� �õ����ֽñ� �ٶ��ϴ�.");
		return false;
	}
	else if(form.OrdNo.value == ""){
		alert("�ֹ���ȣ�� �Էµ��� �ʾҽ��ϴ�.\n�ٽ� �ŷ��� �õ����ֽñ� �ٶ��ϴ�.");
		return false;
	}
	else if(form.ProdNm.value == ""){
		alert("��ǰ���� �Էµ��� �ʾҽ��ϴ�.\n�ٽ� �ŷ��� �õ����ֽñ� �ٶ��ϴ�.");
		return false;
	}
	else if(form.Amt.value == ""){
		alert("�ݾ��� �Էµ��� �ʾҽ��ϴ�.\n�ٽ� �ŷ��� �õ����ֽñ� �ٶ��ϴ�.");
		return false;
	}
	else if(form.MallUrl.value == ""){
		alert("����URL�� �Էµ��� �ʾҽ��ϴ�.\n�ٽ� �ŷ��� �õ����ֽñ� �ٶ��ϴ�.");
		return false;
	}
	return true;
}
</script>
</head>
<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
<!-- ���ڵ� ����� UTF-8�� �ϴ� ��� action ��� �� http://www.allthegate.com/payment/mobile_utf8/pay_start.jsp -->
<form name="frmAGS_pay" method="post" action="http://www.allthegate.com/payment/mobile/pay_start.jsp">
<!-- �� => �ʼ� -->

<!--//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// [1] �Ϲ�/������ �������θ� �����մϴ�.
//
// �Һ��Ǹ��� ��� �����ڰ� ���ڼ����Ḧ �δ��ϴ� ���� �⺻�Դϴ�. �׷���,
// ������ �ô�����Ʈ���� ���� ����� ���ؼ� �Һ����ڸ� ���������� �δ��� �� �ֽ��ϴ�.
// �̰�� �����ڴ� ������ �Һΰŷ��� �����մϴ�.
//
// ����)
// 	(1) �Ϲݰ����� ����� ���
// 	form.DeviId.value = "9000400001";
//
// 	(2) �����ڰ����� ����� ���
// 	form.DeviId.value = "9000400002";
//
// 	(3) ���� ���� �ݾ��� 100,000�� �̸��� ��� �Ϲ��Һη� 100,000�� �̻��� ��� �������Һη� ����� ���
// 	if(parseInt(form.Amt.value) < 100000)
//		form.DeviId.value = "9000400001";
// 	else
//		form.DeviId.value = "9000400002";
//////////////////////////////////////////////////////////////////////////////////////////////////////////////-->

<!--//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// [2] �Ϲ� �ҺαⰣ�� �����մϴ�.
//
// �Ϲ� �ҺαⰣ�� 2 ~ 12�������� �����մϴ�.
// 0:�Ͻú�, 2:2����, 3:3����, ... , 12:12����
//
// ����)
// 	(1) �ҺαⰣ�� �ϽúҸ� �����ϵ��� ����� ���
// 	form.QuotaInf.value = "0";
//
// 	(2) �ҺαⰣ�� �Ͻú� ~ 12�������� ����� ���
//		form.QuotaInf.value = "0:2:3:4:5:6:7:8:9:10:11:12";
//
// 	(3) �����ݾ��� ���������ȿ� ���� ��쿡�� �Һΰ� �����ϰ� �� ���
// 	if((parseInt(form.Amt.value) >= 100000) || (parseInt(form.Amt.value) <= 200000))
// 		form.QuotaInf.value = "0:2:3:4:5:6:7:8:9:10:11:12";
// 	else
// 		form.QuotaInf.value = "0";
//////////////////////////////////////////////////////////////////////////////////////////////////////////////-->

<!--////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// [3] ������ �ҺαⰣ�� �����մϴ�.
// (�Ϲݰ����� ��쿡�� �� ������ ������� �ʽ��ϴ�.)
//
// ������ �ҺαⰣ�� 2 ~ 12�������� �����ϸ�,
// �ô�����Ʈ���� ������ �Һ� ������������ �����ؾ� �մϴ�.
//
// 100:BC
// 200:����
// 300:��ȯ
// 400:�Ｚ
// 500:����
// 800:����
// 900:�Ե�
//
// ����)
// 	(1) ��� �Һΰŷ��� �����ڷ� �ϰ� ���������� ALL�� ����
// 	form.NointInf.value = "ALL";
//
// 	(2) ����ī�� Ư���������� �����ڸ� �ϰ� ������� ����(2:3:4:5:6����)
// 	form.NointInf.value = "200-2:3:4:5:6";
//
// 	(3) ��ȯī�� Ư���������� �����ڸ� �ϰ� ������� ����(2:3:4:5:6����)
// 	form.NointInf.value = "300-2:3:4:5:6";
//
// 	(4) ����,��ȯī�� Ư���������� �����ڸ� �ϰ� ������� ����(2:3:4:5:6����)
// 	form.NointInf.value = "200-2:3:4:5:6,300-2:3:4:5:6";
//
//	(5) ������ �ҺαⰣ ������ ���� ���� ��쿡�� NONE�� ����
//	form.NointInf.value = "NONE";
//
//	(6) ��ī��� Ư���������� �����ڸ� �ϰ� �������(2:3:6����)
//	form.NointInf.value = "100-2:3:6,200-2:3:6,300-2:3:6,400-2:3:6,500-2:3:6,600-2:3:6,800-2:3:6,900-2:3:6";
//
////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->


<input type=hidden name=DeviId value="<? echo ($pg['zerofee'] != 'yes') ? '9000400001': '9000400002' ;?>">			<!-- �ܸ�����̵� - �Ϲݰ���:9000400001, �����ڰ���:9000400002 -->
<input type=hidden name=QuotaInf value="<?=$pg['quota']?>">			<!-- �Ϲ��Һΰ����������� -->
<input type=hidden name=NointInf value="<?=$pg['zerofee_period']?>">		<!-- �������Һΰ����������� -->


<input type=hidden name=Job value="<?=$settlekind?>">	<!-- �������� card - �ſ�ī�� , virtual - �������, hp - �޴��� -->
<input type=hidden name=StoreId value="<?=$pg['id']?>">	<!-- �ڻ������̵� (20) -->
<input type=hidden name=OrdNo value="<?=$_POST['ordno']?>">	<!-- ���ֹ���ȣ (40) -->
<input type=hidden name=Amt value="<?=$_POST['settleprice']?>">	<!-- �ڱݾ� (12) -->
<input type=hidden name=StoreNm value="<?=addslashes($StoreNm)?>">	<!-- �ڻ����� (50) -->
<input type=hidden name=ProdNm value="<?=addslashes($ordnm)?>">	<!-- �ڻ�ǰ�� (300) -->
<input type=hidden name=MallUrl value="<?='http://'.$_SERVER['SERVER_NAME']?>">	<!-- �ڻ���URL (50) -->
<input type=hidden name=UserEmail value="<?=$_POST['email']?>">	<!-- �ֹ����̸��� (50) -->
<input type=hidden name=UserId value="<?=$UserId?>">	<!-- ȸ�����̵� (20) -->
<input type=hidden name=OrdNm value="<?=$_POST['nameOrder']?>">	<!-- �ֹ��ڸ� (40) -->
<input type=hidden name=OrdPhone value="<?=implode('-',$_POST['mobileOrder'])?>">	<!-- �ֹ��ڿ���ó (21) -->
<input type=hidden name=OrdAddr value="<? echo $_POST['address'].' '.$_POST['address_sub'] ?>">	<!-- �ֹ����ּ� (100) -->
<input type=hidden name=RcpNm value="<?=$_POST['nameReceiver']?>">	<!-- �����ڸ� (40) -->
<input type=hidden name=RcpPhone value="<?=implode('-',$_POST['mobileReceiver'])?>">	<!-- �����ڿ���ó (21) -->
<input type=hidden name=DlvAddr value="<? echo $_POST['address'].' '.$_POST['address_sub'] ?>">	<!-- ������ּ� (100) -->
<input type=hidden name=Remark value="<?=addslashes($_POST['memo'])?>">	<!-- ��Ÿ�䱸���� (350) -->
<input type=hidden name=CardSelect value="">	<!-- ī��缱�� - ��� ����ϰ��� �� ������ �ƹ� ���� �Է����� �ʽ��ϴ�. -->
<input type=hidden name=RtnUrl value="<? echo 'http://'.$_SERVER['SERVER_NAME'].$cfg['rootDir'].'/order/card/agspay/mobile/shopTouch_card_return.php' ?>">	<!-- �ڼ��� URL (150) - ���� URL�� �ݵ�� ������ AGS_pay_ing.php�� ��ü ��η� ���� �ֽñ� �ٶ��ϴ�. ex)http://www.allthegate.com/mall/AGS_pay_ing.php -->
<input type=hidden name=CancelUrl value="<? echo 'http://'.$_SERVER['SERVER_NAME'].'/shopTouch/shopTouch_myp/orderview.php?ordno='.$_POST['ordno'] ?>">	<!-- ����� URL (150) - ���� ��Ҹ� ������ ����� �̵� URL ��η� ��ü ��η� ���� �ֽñ� �Դϴ�. ex)http://www.allthegate.com/mall/AGS_pay_cancel.php -->
<input type=hidden name=Column1 value="">	<!-- �߰�����ʵ�1 (200) -->
<input type=hidden name=Column2 value="">	<!-- �߰�����ʵ�2 (200) -->
<input type=hidden name=Column3 value="">	<!-- �߰�����ʵ�3 (200) -->

<!--  ������� ���� ��� ���� ���� -->
<!-- ������� �������� ��/��� �뺸�� ���� �ʼ� �Է� ���� �Դϴ�. -->
<!-- �������ּҴ� �������ּҸ� ������ '/'���� �ּҸ� �����ֽø� �˴ϴ�. ex)/mall/AGS_VirAcctResult.php -->
<input type=hidden name=MallPage value="/shop/order/card/agspay/mobile/AGS_VirAcctResult.php">	<!-- ���뺸������ (100) -->
<input type=hidden name=VIRTUAL_DEPODT value="">	<!-- �Աݿ����� (8) -->
<!--  ������� ���� ��� ���� �� -->

<!-- �޴��� ���� ��� ���� ���� -->
<input type=hidden name=HP_ID value="">	<!-- CP���̵� (10) - CP���̵� �ڵ��� ���� �ǰŷ� ��ȯ�Ŀ��� �߱޹����� CPID�� �����Ͽ� �ֽñ� �ٶ��ϴ�. -->
<input type=hidden name=HP_PWD value="">	<!-- CP��й�ȣ (10) - CP��й�ȣ�� �ڵ��� ���� �ǰŷ� ��ȯ�Ŀ��� �߱޹����� ��й�ȣ�� �����Ͽ� �ֽñ� �ٶ��ϴ�. -->
<input type=hidden name=HP_SUBID value="<?=$pg['sub_cpid']?>">	<!-- SUB-CP���̵� (10) - SUB-CPID�� �ڵ��� ���� �ǰŷ� ��ȯ�Ŀ� �߱޹����� ������ �Է��Ͽ� �ֽñ� �ٶ��ϴ�. -->
<input type=hidden name=ProdCode value="">	<!-- ��ǰ�ڵ� (10) - ��ǰ�ڵ带 �ڵ��� ���� �ǰŷ� ��ȯ�Ŀ��� �߱޹����� ��ǰ�ڵ�� �����Ͽ� �ֽñ� �ٶ��ϴ�. -->
<!-- ��ǰ������ �ڵ��� ���� �ǰŷ� ��ȯ�Ŀ��� �߱޹����� ��ǰ������ �����Ͽ� �ֽñ� �ٶ��ϴ�. -->
<!-- �Ǹ��ϴ� ��ǰ�� ������(������)�� ��� = 1, �ǹ�(��ǰ)�� ��� = 2 -->
<input type=hidden name= value="">	<!-- ��ǰ���� -->
<!-- �޴��� ���� ��� ���� �� -->

<div style="text-align:center;padding:20px 0;font-size:12px;"><strong><b>����� �ô�����Ʈ Mobile ����ȭ������ �̵��մϴ�.</b></strong></div>

</form>