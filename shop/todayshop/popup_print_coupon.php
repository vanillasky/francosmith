<?
include "../_header.php";



// 변수 받고
	$ordno = isset($_REQUEST['ordno']) ? $_REQUEST['ordno'] : '';

	$action = isset($_POST['action']) ? $_POST['action'] : '';
	$message = isset($_POST['message']) ? $_POST['message'] : '';

	$rcvphone0 = isset($_POST['rcvphone0']) ? $_POST['rcvphone0'] : '';
	$rcvphone1 = isset($_POST['rcvphone1']) ? $_POST['rcvphone1'] : '';
	$rcvphone2 = isset($_POST['rcvphone2']) ? $_POST['rcvphone2'] : '';




// 주문번호를 이용, 쿠폰 정보를 가져옴
$query = "
	SELECT
		A.ordno, A.m_no, A.nameOrder, A.nameReceiver,A.mobileReceiver, A.prn_settleprice,

		C.goodsnm,C.img_s,

		D.cp_sno,
		D.cp_num,
		D.cp_publish,
		D.cp_sms_cnt,
		D.cp_ea

	FROM ".GD_ORDER." AS A

	INNER JOIN ".GD_ORDER_ITEM." AS B
	ON A.ordno = B.ordno

	INNER JOIN ".GD_GOODS." AS C
	ON B.goodsno = C.goodsno

	INNER JOIN ".GD_TODAYSHOP_ORDER_COUPON." AS D
	ON A.ordno = D.ordno

	WHERE A.ordno = '$ordno'
";
$data = $db->fetch($query,1);

// 데이터 없으믄 쿠폰구매 아닌거임.
if (!$data) msg("해당 주문이 존재하지 않습니다",'close');


### 권한 체크
if ($sess[m_no]){
	if ($data[m_no]!=$sess[m_no]) msg("접근권한이 없습니다",'close');
} else {
	if ($data[nameOrder]!=$_COOKIE[guest_nameOrder] || $data[m_no]) msg("접근권한이 없습니다",'close');
}


// 템플릿 데이터

$tpl->assign('couponImage',goodsimg($data['img_s']));
$tpl->assign('couponName',$data['goodsnm']);
$tpl->assign('couponPrice',$data['prn_settleprice']);
$tpl->assign('couponEA',$data['cp_ea']);
$tpl->assign('couponNumber',$data['cp_num']);
$tpl->print_('tpl');
?>