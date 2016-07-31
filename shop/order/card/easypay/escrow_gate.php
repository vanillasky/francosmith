<?php
include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.easypay.php";
include "../../../conf/pg.escrow.php";
include "./inc/easypay_config.php";
include "./easypay_client.php";

$ordno = $_GET['ordno'];

$query = "
SELECT
	a.settleprice,a.delivery,a.nameReceiver,a.phoneReceiver,a.mobileReceiver,a.zipcode,a.address,a.escrowno,
	a.deliveryno,a.deliverycode,a.delivery,a.ddt
FROM
	".GD_ORDER." a
WHERE
	a.ordno = '$ordno'
";
$data = $db->fetch($query);

// ����� ��ȣ, �ù�� üũ
if (empty($data['deliveryno']) || empty($data['deliverycode'])) {
	msg('����� ��ȣ�� ���õ� �ù�簡 �����ϴ�. �ٽ� Ȯ�� �ٶ��ϴ�.');
	exit;
}

// ��ۺ� ���޹�� ����
if ($data['delivery'] > 0) {
	$dlvChargeVal	= 'BH';
} else {
	$dlvChargeVal	= 'SH';
}

// ��۵�� Ȯ���Ͻ�
if (strlen($data['ddt'] > 9)) {
	$dlvInvoiceDay	= $data['ddt'];
} else {
	$dlvInvoiceDay	= date('Y-m-d H:i:s');
}

// ������ ��ȭ��ȣ
if (empty($data['mobileReceiver']) === false) {
	$recvTel	= $data['mobileReceiver'];
} else {
	$recvTel	= $data['phoneReceiver'];
}

// �ù�� �ڵ� �� �ù�� �� ����
$compcode			= array();
$compcode['15']		= array('code'	=> 'DC02', 'name' =>'CJ GLS');
$compcode['13']		= array('code'	=> 'DC09', 'name' =>'�����ù�');
$compcode['12']		= array('code'	=> 'DC08', 'name' =>'�����ù�');
$compcode['4']		= array('code'	=> 'DC01', 'name' =>'�������');
$compcode['1']		= array('code'	=> 'DC10', 'name' =>'KGB�ù�');
$compcode['5']		= array('code'	=> 'DC05', 'name' =>'�����ù�');
$compcode['9']		= array('code'	=> 'DC07', 'name' =>'��ü���ù�');
$compcode['100']	= array('code'	=> 'DC07', 'name' =>'��ü���ù�');
$compcode['8']		= array('code'	=> 'DC04', 'name' =>'���ο�ĸ');
$compcode['20']		= array('code'	=> 'DC11', 'name' =>'�ϳ����ù�');
$compcode['21']		= array('code'	=> 'DC06', 'name' =>'�����ù�');	//�����ͽ�������??
$compcode['9999']	= array('code'	=> '9999', 'name' =>'��Ÿ�ù�');

if (in_array($data['deliveryno'], array_keys($compcode))) {
	$dlvExArr	= $compcode[$data['deliveryno']];
} else {
	$dlvExArr	= $compcode['9999'];
}
?>
<html>
<head>
<title>�������� ��ü ����ũ��(INIescrow)</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<meta http-equiv="Pragma" content="no-cache" />
<script type="text/javascript">
    function f_submit() {
        var frm_mgr = document.frm_mgr;
        
        var bRetVal = false;
        
        /*  �������� Ȯ�� */
        if( !frm_mgr.org_cno.value ) {
            alert("PG�ŷ���ȣ�� �Է��ϼ���!!");
            frm_mgr.org_cno.focus();
            return;
        }
        
        if( !frm_mgr.req_id.value ) {
            alert("��û��ID�� �Է��ϼ���!!");
            frm_mgr.req_id.focus();
            return;
        }
        /* ����ũ�� ������ ���漼�α��� üũ */
        if ( frm_mgr.mgr_txtype.value != "61" ) { 
            alert("����ũ�δ� �ݵ�� ����ũ�� �������� ó���Ͻñ� �ٶ��ϴ�.");
            frm_mgr.mgr_txtype.focus();
            return;
        }
        /* �� �ʵ� ���� üũ�Ͻñ� �߶��ϴ�. */
        
        bRetVal = true;
        if ( bRetVal ) frm_mgr.submit();
    }
</script>
</head>

<body onload="f_submit()">
<form name="frm_mgr" method="post" action="./escrow_delivery.php">
<!-- [�ʼ�]�ŷ�����(�����Ұ�) -->
<input type="test" name="EP_tr_cd" value="00201000">
<!-- [�ʼ�]��û�� IP -->
<input type="hidden" name="req_ip" value="<?=getenv('REMOTE_ADDR')?>">
<input type="hidden" name="mgr_txtype"  value="61" />
<input type="hidden" name="mgr_subtype" value="ES07" selected ><!--�����--> 
<input type="hidden" name="ordno" value="<?=$ordno?>" />
<input type="hidden" name="org_cno"  value="<?php echo $data['escrowno'];?>" >
<input type="hidden" name="req_id" value="<?=$_SESSION['sess']['m_id']?>" />
<input type="hidden" name="deli_cd"  value="DE01" /> <!--�ڰ�-->
<input type="hidden" name="deli_corp_cd" value="<?php echo $dlvExArr['code'];?>"   />
<!-- [�ɼ�]����� ��û �� �ʼ��׸�  ����� ��ȣ -->
<input type="hidden" name="deli_invoice" value="<?php echo $data['deliverycode'];?>" >
 <!-- [�ɼ�]����� ��û �� �ʼ��׸�  ������ �̸�-->
<input type="hidden" name="deli_rcv_nm"  value="<?php echo $data['nameReceiver'];?>" >
  <!-- [�ɼ�]����� ��û �� �ʼ��׸�  ������ ����ó-->
<input type="hidden" name="deli_rcv_tel"  value="<?php echo $recvTel;?>">
</form>

</body>
</html>