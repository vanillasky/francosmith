<?
include "../lib/library.php";
include_once dirname(__FILE__)."/../conf/config.php";
@include dirname(__FILE__) . "/../lib/acecounter.class.php";

### ace카운터
$Acecounter = new Acecounter();

$orderitem_mode = "cart";

if ($_GET['action'] == 'ok') {

	switch($_GET['type']) {
		case 'cart_ea':	// ajax 로 호출됨.
			$cart = Core::loader('cart');
			if (method_exists($cart,'modEA') && isset($_GET['idx']) && isset($_GET['ea'])) {
				// ace 카운터 상품수량수정
				if ($_GET['rn'] >= 1301 && $Acecounter->goods_cart_mod($cart->item, (array)$_GET['idx'], (array)$_GET['ea']) === true) {
					$aceScript = strip_tags($Acecounter->scripts);
				}

				$cart->modEA($_GET['idx'], $_GET['ea']);
				$code = 'ok';
			}
			else {
				$code = 'error';
			}

			if ($_GET['rn'] >= 1301) { //13-01-XX 이후 XML방식 리턴
				header('Content-Type: application/xml;charset=euc-kr');
				echo '<?xml version="1.0" encoding="euc-kr" ?>';
				echo '<result>';
				echo '<code>'.$code.'</code>';
				echo '<aceScript><![CDATA['.$aceScript.']]></aceScript>';
				echo '</result>';
				exit;
			} else { // TEXT방식 리턴
				exit($code);
			}
			break;
		case 'truncate':
			switch($_GET['tab']) {
				case 'cart':
					$cart = Core::loader('cart');

					// ace 카운터 장바구니비우기
					if ($Acecounter->goods_cart_dels($cart->item) === true) {
						$aceScript = $Acecounter->scripts;
					}

					$cart->emptyCart();
					break;
				case 'wishlist':
					$query = "DELETE FROM ".GD_MEMBER_WISHLIST." WHERE m_no = '".$sess['m_no']."'";
					$db->query($query);
					break;
				case 'today':
					setcookie('todayGoodsIdx','',time() - 3600,'/');
					setcookie('todayGoods','',time() - 3600,'/');
					break;
			}
			break;
		case 'delete':
			switch($_GET['tab']) {
				case 'cart':
					$cart = Core::loader('cart');

					// ace 카운터 상품개개삭제
					if ($Acecounter->goods_cart_del($cart->item, (array)$_GET['idx']) === true) {
						$aceScript = $Acecounter->scripts;
					}

					$cart->delCart($_GET['idx']);
					break;
				case 'wishlist':
					$query = "

						SELECT
							WS.sno, G.goodsno
						FROM ".GD_MEMBER_WISHLIST." AS WS
						INNER JOIN ".GD_GOODS." AS G
							ON WS.goodsno = G.goodsno AND G.open = 1

						WHERE
							WS.m_no = '".$sess['m_no']."'

						ORDER BY WS.sno DESC
					";
					$rs = $db->query($query);
					if (mysql_data_seek($rs, $_GET['idx'])) {
						$row = $db->fetch($rs,1);
						$query = "DELETE FROM ".GD_MEMBER_WISHLIST." WHERE sno = '".$row[sno]."' AND m_no = '".$sess['m_no']."'";
						$db->query($query);
					}
					break;
				case 'today':
					$today = unserialize(stripslashes($_COOKIE[todayGoods]));
					$today_idx = explode(',',$_COOKIE[todayGoodsIdx]);

					$r_today = array();
					$r_today_idx = array();

					unset($today[$_GET['idx']], $today_idx[$_GET['idx']]);

					$_keys = array_keys($today_idx);
					for ($i=0,$m=sizeof($_keys);$i<$m;$i++) {
						$k = $_keys[$i];

						if (empty($today_idx[$k])) continue;

						$r_today_idx[] = $today_idx[$k];

						if (sizeof($r_today) < 10) {
							if (isset($today[$k])) {
								$r_today[] = $today[$k];
							}
							else {
								$query = "
								SELECT
									G.goodsno, G.goodsnm, G.img_s AS img, O.price
								FROM ".GD_GOODS." AS G
								INNER JOIN ".GD_GOODS_OPTION." AS O
									ON G.goodsno = O.goodsno AND O.link = 1 and go_is_deleted <> '1' and go_is_display = '1'
								WHERE G.goodsno = ".$today_idx[$k]."
								";
								$r_today[] = $db->fetch($query,1);
							}
						}
					}

					setcookie('todayGoodsIdx',implode(",",$r_today_idx),time()+3600*24,'/');
					setcookie('todayGoods',serialize($r_today),time()+3600*24,'/');

					break;
			}
			break;
		case 'view':
			switch($_GET['tab']) {
				case 'cart':
					go('../goods/goods_cart.php');
					break;
				case 'wishlist':
					go('../mypage/mypage_wishlist.php');
					break;
				case 'today':
					go('../mypage/mypage_today.php');
					break;
			}
			break;
		case 'buy':
			switch($_GET['tab']) {
				case 'cart':
					go('../order/order.php');
					break;
			}
			break;
	}


	$returnUrl = isset($_GET['returnUrl']) ? $_GET['returnUrl'] : '../main/index.php';
	if ($aceScript != '') {
		echo $aceScript;
		exit('<script>window.onload = function() { location.replace("'.$returnUrl.'"); } </script>');
	} else {
		go($returnUrl);
	}
	exit;
}

//

$mode = isset($_POST['tab']) ? $_POST['tab'] : '';

$response = null;

switch($mode) {

	case 'today' :

		if ($_COOKIE['todayGoodsIdx']) {

			$todayGoodsIdx = preg_replace('/,$/','',$_COOKIE['todayGoodsIdx']);
			$todayGoodsIdxKeys = array_values( explode(',',$todayGoodsIdx) );

			$query = "
			SELECT
				G.goodsno, G.goodsnm, G.img_s AS img, O.price
			FROM ".GD_GOODS." AS G
			INNER JOIN ".GD_GOODS_OPTION." AS O
				ON G.goodsno = O.goodsno AND O.link = 1 and go_is_deleted <> '1' and go_is_display = '1'
			WHERE G.goodsno IN ($todayGoodsIdx)
			";
			$rs = $db->query($query);
			while ($row = $db->fetch($rs,1)) {
				$key = array_search($row['goodsno'], $todayGoodsIdxKeys);
				$response[$key] = $row;
			}
			@ksort($response);
		}
		break;

	case 'wishlist' :
		$query = "
		SELECT w.*,a.goodsnm,a.img_s as img,b.price,b.reserve

		FROM ".GD_MEMBER_WISHLIST." as w
		left join ".GD_GOODS." as a on w.goodsno=a.goodsno
		left join ".GD_GOODS_OPTION." as b on w.goodsno=b.goodsno and w.opt1=b.opt1 and w.opt2=b.opt2 and go_is_deleted <> '1' and go_is_display = '1'
		WHERE w.m_no = $sess[m_no] AND a.open
		ORDER BY sno DESC
		";

		$res = $db->query($query);
		while ($data=$db->fetch($res,1)){

			### 필수옵션
			$data[opt]	= array_notnull(array(
						$data[opt1],
						$data[opt2],
						));
			### 선택옵션
			$addopt = array_notnull(explode("|",$data[addopt]));
			if ($addopt){
				$data[r_addopt] = $addopt;
				unset($r_addopt); $addprice = 0;
				foreach ($addopt as $v){
					list ($tmp[sno],$tmp[optnm],$tmp[opt],$tmp[price]) = explode("^",$v);
					$r_addopt[] = $tmp;
					$addprice += $tmp[price];
				}
				$data[addopt] = $r_addopt;
				$data[addprice] = $addprice;
			}
			$response[] = $data;
		}
		break;

	case 'cart' :
		$cart = Core::loader('cart');
		$response = $cart->item;

		break;

	default:
		$response = array();

		// 오늘본 상품 갯수
		$_COOKIE['todayGoodsIdx'] = preg_replace('/,$/','',$_COOKIE['todayGoodsIdx']);
		$response['today'] = $_COOKIE['todayGoodsIdx'] ? sizeof( explode(',',$_COOKIE['todayGoodsIdx']) ) : 0;

		// 위시리스트
		$query = "
		SELECT COUNT(w.goodsno) AS cnt
		FROM ".GD_MEMBER_WISHLIST." as w
		left join ".GD_GOODS." as a on w.goodsno=a.goodsno
		left join ".GD_GOODS_OPTION." as b on w.goodsno=b.goodsno and w.opt1=b.opt1 and w.opt2=b.opt2 and go_is_deleted <> '1' and go_is_display = '1'
		WHERE w.m_no = $sess[m_no] AND a.open
		";
		$rs = $db->fetch($query,1);
		$response['wishlist'] = (int)$rs['cnt'];


		// 장바구니
		$cart = Core::loader('cart');
		$response['cart'] = sizeof($cart->item);
		echo '['.gd_json_encode($response).']';
		exit;
		break;
}
echo gd_json_encode($response);
?>
