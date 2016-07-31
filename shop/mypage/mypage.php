<?php
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

include "../_header.php";
include "../conf/config.pay.php";
include "../conf/config.display.php";
chkMember();	// 회원인지 체크

// 진행중인 주문 START
$ordering = $db->fetch("
SELECT
	IFNULL(SUM(IF(step=0 AND step2=0, 1, 0)), 0) pendingPayment
	, IFNULL(SUM(IF(step=1 AND step2=0, 1, 0)), 0) confirmPayment
	, IFNULL(SUM(IF(step=2 AND step2=0, 1, 0)), 0) deliveryPrepare
	, IFNULL(SUM(IF(step=3 AND step2=0, 1, 0)), 0) delivering
	, IFNULL(SUM(IF(step=4 AND step2=0, 1, 0)), 0) deliveryComplete
	, IFNULL(SUM(IF((step2=41 OR step2=42 OR step2=44), 1, 0)), 0) cancel
FROM ".GD_ORDER."
WHERE
	m_no='".$sess['m_no']."'
	AND LEFT(orddt, 10) > DATE_SUB(LEFT(NOW(), 10), INTERVAL 30 DAY)
");
// 진행중인 주문 END

// 최근 주문 정보 START
$query = "
SELECT
	orddt
	, ordno
	, settlekind
	, settleprice
	, step
	, step2
	, nameOrder
FROM ".GD_ORDER."
WHERE m_no='".$sess['m_no']."'
ORDER BY orddt DESC
LIMIT 3
";
$res = $db->query($query);
$orderInfo = array(); // 변수 초기화

while ($orderData=$db->fetch($res,1)) {
	$orderData['str_step'] = (!$orderData['step2']) ? $r_step[$orderData['step']] : $r_step2[$orderData['step2']];	// 주문 상태
	$orderData['str_settlekind'] = $r_settlekind[$orderData['settlekind']];	// 결제 방법
	// 최초 결제 금액은 변경하지 않음.
	// 취소 금액은 (최초결제 금액 - 실결제금액)
	$order = Core::loader('order');
	$order->load($orderData['ordno']);
	$orderData['settleprice'] = $order->getRealPrnSettleAmount();
	$orderData['canceled_price'] = $order->getCancelCompletedAmount();
	$orderInfo[] = $orderData;
}
// 최근 주문 정보 END


// 1:1 문의내역 START
$query = "
SELECT
	gmq.ordno
	, gmq.m_no
	, gmq.sno
	, gmq.itemcd
	, gmq.parent
	, gmq.subject
	, gmq.contents
	, gmq.regdt
	, gm.m_id
FROM ".GD_MEMBER_QNA." gmq
LEFT OUTER JOIN ".GD_MEMBER." gm ON gmq.m_no=gm.m_no
WHERE gmq.m_no='".$sess['m_no']."' AND gmq.sno=gmq.parent
ORDER BY gmq.regdt DESC
LIMIT 3
";
$res = $db->query($query);
$qna = array(); // 변수 초기화
$idx = 0; // 글번호
$itemcds = codeitem( 'question' );	// 질문유형

while ($qnaData=$db->fetch($res,1)) {
	$qnaData['itemcd'] = $itemcds[ $qnaData['itemcd'] ];	// 질문유형
	// 답글 출력
	$query = "
SELECT
	gmq.ordno
	, gmq.m_no
	, gmq.sno
	, gmq.itemcd
	, gmq.parent
	, gmq.subject
	, gmq.contents
	, gmq.regdt
	, gm.m_id
FROM ".GD_MEMBER_QNA." gmq
LEFT JOIN ".GD_MEMBER." gm ON gmq.m_no=gm.m_no
WHERE gmq.parent='".$qnaData['sno']."' AND gmq.sno <> gmq.parent
ORDER BY gmq.regdt DESC
";
	$repResult = $db->query($query);
	$qnaData['repleCnt'] = mysql_num_rows($repResult);	// 답글 갯수
	$qnaData['idx'] = ++$idx;	// 글번호
	$qna[] = $qnaData;

	while ($repData=$db->fetch($repResult,1)) {
		$repData['idx'] = ++$idx;
		$qna[] = $repData;
	}
}

// 글번호 정렬
for ($i=0,$j=count($qna)-1; $i<$j; $i++,$j--) {
	$tmp = $qna[$i]['idx'];
	$qna[$i]['idx'] = $qna[$j]['idx'];
	$qna[$j]['idx'] = $tmp;
}
// 1:1 문의내역 END


// 오늘 본 상품 리스트 START
### 임시 테이블 생성
$query = "
CREATE TEMPORARY TABLE gd_today(
	sno INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	goodsno INT
)
";
$db->query($query);

### 오늘본상품 리스트업
$todayGoodsIdx = explode(",",$_COOKIE['todayGoodsIdx']);
foreach ($todayGoodsIdx as $v){
	if ($v) $db->query("INSERT INTO gd_today VALUES ('',$v)");
}

### 상품 리스트
$pg = new Page(1, 20); // 최대 20개
$pg->field = "b.*,c.*";
$db_table = "gd_today a,".GD_GOODS." b,".GD_GOODS_OPTION." c";
$where[] = "a.goodsno=b.goodsno";
$where[] = "a.goodsno=c.goodsno";
$where[] = "link AND go_is_deleted <> '1' AND go_is_display = '1'";
if (!$_GET['sort']) $_GET['sort'] = 'a.sno';
$pg->setQuery($db_table,$where,$_GET['sort']);
$pg->exec();
$todayList = $db->query($pg->query);

while ($data=$db->fetch($todayList)) {
	### 적립금 정책적용
	if(!$data['use_emoney']){
		if( !$set['emoney']['chk_goods_emoney'] ){
			if( $set['emoney']['goods_emoney'] ) $data['reserve'] = getDcprice($data['price'],$set['emoney']['goods_emoney'].'%');
		}else{
			$data['reserve']	= $set['emoney']['goods_emoney'];
		}
	}
	### 아이콘
	$data['icon'] = setIcon($data['icon'],$data['regdt']);
	// 상품할인 가격 표시
	if ($displayCfg['displayType'] === 'discount') {
		$discountModel = '';
		$goodsDiscount = '';
		if ($data['use_goods_discount'] === '1') {
			$discountModel = Clib_Application::getModelClass('Goods_Discount');
			$goodsDiscount = $discountModel->getDiscountAmountSearch($data);
		}
		if ($goodsDiscount) {
			$data['oriPrice'] = $data['price'];
			$data['goodsDiscountPrice'] = $data['price'] - $goodsDiscount;
		}
		else {
			$data['oriPrice'] = '0';
			$data['goodsDiscountPrice'] = $data['price'];
		}
	}


	$loop[] = setGoodsOuputVar($data);
}
// 오늘 본 상품 리스트 END

$tpl->assign(array(
	'ordering'	=> $ordering, // 진행 중인 주문
	'orderInfo'	=> $orderInfo, // 최근 주문 정보
	'qna'	=> $qna, // 1:1문의내역
	'loop'	=> $loop, // 오늘 본 상품
));
$tpl->print_('tpl');
?>