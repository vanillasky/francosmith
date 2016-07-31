<?php
include '../_header.php';
include '../conf/config.php';
include '../conf/config.cart.php';

$cart = Core::loader('Cart');
$estimate = new estimate();

$idxs = $_POST['idxs'];

// 회원정보 가져오기
if ($sess) {
	$memberName = $estimate->getName($sess['m_no']);
}

// 체크 된 상품만 가져오기
$item = $estimate->getGoods($cart->item,$idxs);

// 과세 여부 가져오기
$item = $estimate->getTax($item,$idxs);

// 합계 금액 구하기
$totalPrice = $estimate->totalPrice($item);

// 합계 금액 한글로 변환해서 가져오기
$priceKor = $estimate->transNum($totalPrice);

// 공급가액 계산
$item = $estimate->supplyPrice($item,$idxs);

// 공급가액 합산
$totalSupplyPrice = $estimate->totalSupplyPrice($item);

// 비고 메세지 엔터값
$cartCfg['estimateMessage'] = nl2br($cartCfg['estimateMessage']);

// 상품명 HTML 태그 제거
$item = $estimate->tagStrip($item);

// \제거
$cartCfg['estimateMessage'] = stripslashes($cartCfg['estimateMessage']);

$tpl->assign('item',$item);
$tpl->assign('cfg',$cfg);
$tpl->assign('cartCfg',$cartCfg);
$tpl->assign('memberName',$memberName);
$tpl->assign('totalPrice',$totalPrice);
$tpl->assign('totalSupplyPrice',$totalSupplyPrice);
$tpl->assign('priceKor',$priceKor);
$tpl->print_('tpl');

?>