<?

@include dirname(__FILE__) . "/../lib/library.php";

// DB 장바구니의 사용을 위해 uid는 보존
$_tmp_uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';

session_destroy();
setCookie('Xtime','',0,'/');
setcookie('gd_cart','',time() - 3600,'/');
setcookie('gd_cart_direct','',time() - 3600,'/');

if ($_tmp_uid) {
	session_start();
	$_SESSION['uid'] = $_tmp_uid;
}

go('vumall://vercoop.com/logout_success');

?>