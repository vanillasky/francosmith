<?
include "../_header.php";

### 변수할당
$tgsno = $_POST['tgsno'];

### 회원정보 가져오기
if ($sess){
	$query = "
	SELECT * FROM
		".GD_MEMBER." a
		LEFT JOIN ".GD_MEMBER_GRP." b ON a.level=b.level
	WHERE
		m_no='$sess[m_no]'
	";
	$member = $db->fetch($query,1);
}
else {
	msg('로그인한 회원만 사용가능합니다.');
	exit;
}

// TodayShop class
$todayShop = Core::loader('todayshop');

### 상품 데이타
if ($tgsno) { // 지정상품 가져오기
	$data = $todayShop->getGoods($tgsno);
}
if (!is_array($data) || empty($data)) {
	msg('잘못된 상품번호입니다.');
	exit;
}
if (!trim($data['sms'])) {
	msg('SMS 서비스를 지원하지 않는 상품입니다.');
	exit;
}

$rcvphone = $_POST['rcvphone0'].'-'.$_POST['rcvphone1'].'-'.$_POST['rcvphone2'];
$callback = $_POST['callback0'].'-'.$_POST['callback1'].'-'.$_POST['callback2'];
$msg = $todayShop->sendSms($tgsno, $rcvphone, $callback);
msg($msg);
?>
