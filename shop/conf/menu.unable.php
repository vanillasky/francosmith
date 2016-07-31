<?
# 기본제어(미출력)
$menu_unable = array();
$menu_unable[] = "basic/agspay.php";
$menu_unable[] = "event/orderlist.php";
$menu_unable[] = "order/merchant_list.php";		#링크프라이스주문리스트
$menu_unable[] = "linkprice/merchant.php";		#링크프라이스설정
$menu_unable[] = "open/om_id.php";				# 오픈마켓 계정관리
$menu_unable[] = "open/om_category.php";			# 오픈마켓 분류매칭
$menu_unable[] = "open/om_register.php";			# 오픈마켓 상품등록
$menu_unable[] = "open/om_stock.php";			# 오픈마켓 품절관리

# 무료몰제어(안내팝업호출)
$menu_unfree = array();
$menu_unfree[] = "log/search.php";
$menu_unfree[] = "event/list.php";
$menu_unfree[] = "event/register.php";
$menu_unfree[] = "log/index.php?mode=referer";
$menu_unfree[] = "javascript:if( window.open('../open/om.popup.manager.php','om') );";		# 오픈마켓 매니저
$menu_unfree[] = "order/bankda.php";				# 계좌통합관리
$menu_unfree[] = "order/bankmatch.php";			# 입금조회/수동입금처리
$menu_unfree[] = "goods/price.php";		# 빠른 가격수정
$menu_unfree[] = "goods/reserve.php";		# 빠른 적립금수정
$menu_unfree[] = "goods/link.php";		# 빠른 이동/복사/삭제
$menu_unfree[] = "member/batch.php";
$menu_unfree[] = "member/batch.php?func=emoney";		# 적립금 일괄지급/차감
$menu_unfree[] = "member/batch.php?func=level";		# 회원그룹 일괄변경
$menu_unfree[] = "member/batch.php?func=status";		# 회원승인상태 일괄변경
$menu_unfree[] = "member/batch.php?func=sms";		# SMS 발송하기
$menu_unfree[] = "member/batch.php?func=email";		# 메일 발송하기
$menu_unfree[] = "basic/adminGroup.php";		# 관리자권한설정
$menu_unfree[] = "event/coupon_cfg.php";		# 쿠폰설정
$menu_unfree[] = "event/coupon.php";		# 쿠폰리스트
$menu_unfree[] = "event/coupon_register.php";		# 쿠폰등록

# 임대형제어(미출력)
$menu_unrent = array();

# 독립형제어(미출력)
$menu_unself = array();
$menu_unself[] = "open/om_category.php";			# 오픈마켓 분류매칭
$menu_unself[] = "open/om_register.php";			# 오픈마켓 상품등록
$menu_unself[] = "open/om_stock.php";			# 오픈마켓 품절관리

/*---------------------------------------------------------------*/

include dirname(__FILE__)."/menu.able.php";

function able_unset($var) {
	global $menu_able;
 	return ( !in_array( $var, $menu_able ) );
}

$menu_unable = array_filter($menu_unable, "able_unset");
$menu_unfree = array_filter($menu_unfree, "able_unset");
$menu_unrent = array_filter($menu_unrent, "able_unset");
$menu_unself = array_filter($menu_unself, "able_unset");

if ( preg_match( "/^rental_mx[^free]/i", $godo[ecCode] ) ) // 임대형제어
	$menu_unable = array_merge ($menu_unable, $menu_unrent);
else if( $godo[ecCode]=="self_enamoo_season" ) // 독립형제어
	$menu_unable = array_merge ($menu_unable, $menu_unself);

?>