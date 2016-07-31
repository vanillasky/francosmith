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

// 운송장 번호, 택배사 체크
if (empty($data['deliveryno']) || empty($data['deliverycode'])) {
	msg('운송장 번호나 선택된 택배사가 없습니다. 다시 확인 바랍니다.');
	exit;
}

// 배송비 지급방법 설정
if ($data['delivery'] > 0) {
	$dlvChargeVal	= 'BH';
} else {
	$dlvChargeVal	= 'SH';
}

// 배송등록 확인일시
if (strlen($data['ddt'] > 9)) {
	$dlvInvoiceDay	= $data['ddt'];
} else {
	$dlvInvoiceDay	= date('Y-m-d H:i:s');
}

// 수신자 전화번호
if (empty($data['mobileReceiver']) === false) {
	$recvTel	= $data['mobileReceiver'];
} else {
	$recvTel	= $data['phoneReceiver'];
}

// 택배사 코드 및 택배사 명 설정
$compcode			= array();
$compcode['15']		= array('code'	=> 'DC02', 'name' =>'CJ GLS');
$compcode['13']		= array('code'	=> 'DC09', 'name' =>'현대택배');
$compcode['12']		= array('code'	=> 'DC08', 'name' =>'한진택배');
$compcode['4']		= array('code'	=> 'DC01', 'name' =>'대한통운');
$compcode['1']		= array('code'	=> 'DC10', 'name' =>'KGB택배');
$compcode['5']		= array('code'	=> 'DC05', 'name' =>'로젠택배');
$compcode['9']		= array('code'	=> 'DC07', 'name' =>'우체국택배');
$compcode['100']	= array('code'	=> 'DC07', 'name' =>'우체국택배');
$compcode['8']		= array('code'	=> 'DC04', 'name' =>'옐로우캡');
$compcode['20']		= array('code'	=> 'DC11', 'name' =>'하나로택배');
$compcode['21']		= array('code'	=> 'DC06', 'name' =>'동부택배');	//동부익스프레스??
$compcode['9999']	= array('code'	=> '9999', 'name' =>'기타택배');

if (in_array($data['deliveryno'], array_keys($compcode))) {
	$dlvExArr	= $compcode[$data['deliveryno']];
} else {
	$dlvExArr	= $compcode['9999'];
}
?>
<html>
<head>
<title>이지페이 자체 에스크로(INIescrow)</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<meta http-equiv="Pragma" content="no-cache" />
<script type="text/javascript">
    function f_submit() {
        var frm_mgr = document.frm_mgr;
        
        var bRetVal = false;
        
        /*  변경정보 확인 */
        if( !frm_mgr.org_cno.value ) {
            alert("PG거래번호를 입력하세요!!");
            frm_mgr.org_cno.focus();
            return;
        }
        
        if( !frm_mgr.req_id.value ) {
            alert("요청자ID를 입력하세요!!");
            frm_mgr.req_id.focus();
            return;
        }
        /* 에스크로 변경은 변경세부구분 체크 */
        if ( frm_mgr.mgr_txtype.value != "61" ) { 
            alert("에스크로는 반드시 에스크로 변경으로 처리하시기 바랍니다.");
            frm_mgr.mgr_txtype.focus();
            return;
        }
        /* 각 필드 값을 체크하시기 발랍니다. */
        
        bRetVal = true;
        if ( bRetVal ) frm_mgr.submit();
    }
</script>
</head>

<body onload="f_submit()">
<form name="frm_mgr" method="post" action="./escrow_delivery.php">
<!-- [필수]거래구분(수정불가) -->
<input type="test" name="EP_tr_cd" value="00201000">
<!-- [필수]요청자 IP -->
<input type="hidden" name="req_ip" value="<?=getenv('REMOTE_ADDR')?>">
<input type="hidden" name="mgr_txtype"  value="61" />
<input type="hidden" name="mgr_subtype" value="ES07" selected ><!--배송중--> 
<input type="hidden" name="ordno" value="<?=$ordno?>" />
<input type="hidden" name="org_cno"  value="<?php echo $data['escrowno'];?>" >
<input type="hidden" name="req_id" value="<?=$_SESSION['sess']['m_id']?>" />
<input type="hidden" name="deli_cd"  value="DE01" /> <!--자가-->
<input type="hidden" name="deli_corp_cd" value="<?php echo $dlvExArr['code'];?>"   />
<!-- [옵션]배송중 요청 시 필수항목  운송장 번호 -->
<input type="hidden" name="deli_invoice" value="<?php echo $data['deliverycode'];?>" >
 <!-- [옵션]배송중 요청 시 필수항목  수령인 이름-->
<input type="hidden" name="deli_rcv_nm"  value="<?php echo $data['nameReceiver'];?>" >
  <!-- [옵션]배송중 요청 시 필수항목  수령인 연락처-->
<input type="hidden" name="deli_rcv_tel"  value="<?php echo $recvTel;?>">
</form>

</body>
</html>