<?
set_time_limit(0);

@include "../lib/library.php";
@include "../conf/config.pay.php";
@include "../conf/config.php";
@include "../lib/partner.class.php";
@include "../conf/coupon.php";
@include "../conf/daumCpc.cfg.php";

if($daumCpc['useYN']!= 'Y') exit;

class DaumCpcList
{
	function exec(){
		global $db,$daumCpc,$cfg,$cfgCoupon,$set;
		$url = "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir'];
		$partner = new Partner();
		$columns = $partner->checkColumn();			// EP 생성에 필요한 컬럼 확인
		$couponData = $partner->getCouponInfo();	// 쿠폰
		$memberdc = $partner->getBasicDc();			// 회원할인
		$catnm = $partner->getCatnm();				// 카테고리명
		$brandnm = $partner->getBrand();			// 브랜드명
		$discountData = $partner->getDiscount();	// 상품할인
		$review = $partner->getReview();			// 리뷰 개수
		$query = $partner->getGoodsSqlNew($columns);	// 상품 출력
		$res = $db->query($query);
		$tocnt = mysql_num_rows($res);	// 전체 상품 개수

		echo('<<<tocnt>>>'.$tocnt.chr(10));
		while ($v = $db->fetch($res,1)){

			// 상품별 할인
			$goodsDiscount = 0;
			if ($v['use_goods_discount'] == '1') {
				$goodsDiscount = $partner->getDiscountPrice($discountData,$v['goodsno'],$v['goods_price']);
			}

			// 할인 쿠폰
			$coupon = 0;	// 쿠폰 할인 금액
			$mobileCoupon = 0;	// 모바일 쿠폰 할인 금액
			$coupo = '';	// 쿠폰
			$mcoupon = '';	// 모바일 쿠폰
			$couponReserve = 0;	// 적립 쿠폰
			if ($cfgCoupon['use_yn'] == 1) {
				list($coupon,$mobileCoupon,$couponReserve,$coupo,$mcoupon) = $partner->getCouponPrice($couponData, $v['category'], $v['goodsno'], $v['goods_price'], $v['open_mobile']);
			}

			// 회원할인
			$dcprice = 0;
			$memberdc = '';
			if (is_array($memberdc) === true) {
				$mdc_exc = chk_memberdc_exc($memberdc,$v['goodsno']); // 회원할인 제외상품 체크
				if($mdc_exc === false)$dcprice = getDcprice($v['goods_price'],$memberdc['dc'].'%');
			}

			// 쿠폰 회원할인 중복 할인 체크
			if($coupon>0 && $dcprice>0){
				if($cfgCoupon['range'] == 2)$dcprice=0;
				if($cfgCoupon['range'] == 1){
					$coupon=$mobileCoupon=0;
				}
			}

			// 상품별 할인과 쿠폰 할인에 대한 가격 계산
			$price = 0;
			$mobilePrice = 0;
			
			if ($v['goods_price'] > $coupon + $dcprice + $goodsDiscount) $price = $v['goods_price'] - $coupon - $dcprice - $goodsDiscount;
			else $price = 0;
			
			if ($couponVersion === true && $coupon > $v['goods_price'] - $dcprice - $goodsDiscount) {
				$coupon = $v['goods_price'] - $dcprice - $goodsDiscount;
				$coupo = $coupon.'원';
			}
			
			// 모바일 쿠폰 가격 계산
			if ($v['goods_price'] > $mobileCoupon + $dcprice + $goodsDiscount && $mobileCoupon) $mobilePrice = $v['goods_price'] - $mobileCoupon - $dcprice - $goodsDiscount;
			else $mobilePrice = 0;
			
			if ($couponVersion === true && $mobileCoupon > $v['goods_price'] - $dcprice - $goodsDiscount) {
				$mcoupon = $v['goods_price'] - $dcprice - $goodsDiscount;
				$mcoupon = $mcoupon.'원';
			}
			
			// 배송비
			$deliv = $partner->getDeliveryPrice($v,$price);
			
			// 이미지
			$img_url = '';
			$img_url = $partner->getGoodsImg($v['img_m'],$url);
			
			// 적립금
			$point = 0;
			if($v['use_emoney']=='0')
			{
				if( !$set['emoney']['chk_goods_emoney'] ){
					if( $set['emoney']['goods_emoney'] ) {
						$dc=$set['emoney']['goods_emoney']."%";
						$tmp_price = $v['goods_price'];
						if( $set['emoney']['cut'] ) $po = pow(10,$set['emoney']['cut']);
						else $po = 100;
						$tmp_price = (substr($dc,-1)=="%") ? $tmp_price * substr($dc,0,-1) / 100 : $dc;
						$point =  floor($tmp_price / $po) * $po;

					}
				}else{
					$point = $set['emoney']['goods_emoney'];
				}
			}
			else
			{
				$point = $v['goods_reserve'];
			}
			$point += $couponReserve;

			// 브랜드명 가져오기
			$v['brandnm'] = $brandnm[$v['brandno']];

			// 상품명에 머릿말 조합
			$v['goodsnm'] = $partner->getGoodsnm($daumCpc,$v);

			echo('<<<begin>>>'.chr(10));
			echo('<<<mapid>>>'.$v['goodsno'].chr(10));
			if($v['price'] != $price)echo('<<<lprice>>>'.$v['goods_price'].chr(10));
			echo('<<<price>>>'.$price.chr(10));
			if($mobilePrice > 0 && $v['open_mobile'] == '1')echo('<<<mpric>>>'.$mobilePrice.chr(10));
			echo('<<<pname>>>'.$v['goodsnm'].chr(10));
			echo('<<<pgurl>>>'.$url.'/goods/goods_view.php?inflow=daumCpc&goodsno='.$v['goodsno'].chr(10));
			echo('<<<igurl>>>'.$img_url.chr(10));
			for ($i=1;$i<=strlen($v['category'])/3;$i++) echo('<<<cate'.$i.'>>>'.$catnm[substr($v['category'],0,$i*3)].chr(10));
			for ($i=1;$i<=strlen($v['category'])/3;$i++) echo('<<<caid'.$i.'>>>'.substr($v['category'],0,$i*3).chr(10));
			if($v['model_name'])echo('<<<model>>>'.$v['model_name'].chr(10));
			if($v['brandnm'])echo('<<<brand>>>'.$v['brandnm'].chr(10));
			if($v['maker'])echo('<<<maker>>>'.$v['maker'].chr(10));
			if ($coupon)echo('<<<coupo>>>'.$coupo.chr(10));
			if ($mobileCoupon)echo('<<<mcoupon>>>'.$mcoupon.chr(10));
			if($daumCpc['nv_pcard'])echo('<<<pcard>>>'.$daumCpc['nv_pcard'].chr(10));
			if($point)echo('<<<point>>>'.$point.chr(10));
			echo('<<<deliv>>>'.$deliv.chr(10));
			if($review[$v['goodsno']])echo('<<<revct>>>'.$review[$v['goodsno']].chr(10));
			if($v['naver_event'])echo('<<<event>>>'.$v['naver_event'].chr(10));
			if($v['use_only_adult'] === '1')echo('<<<adult>>>Y'.chr(10));
			echo('<<<ftend>>>'.chr(10));

			flush();
			unset($v);
		}
	}

	function check_accept_ip(){
		$out = readurl("http://gongji.godo.co.kr/userinterface/serviceIp/daumCpc.php");
		$arr = explode(chr(10),$out);
		$ret = false;
		foreach($arr as $v){
			$v = trim($v);
			if($v&&preg_match('/'.$v.'/',$_SERVER['REMOTE_ADDR']))$ret = true;
		}
		if(preg_match('/admin\/daumcpc\/partner.php/',$_SERVER['HTTP_REFERER'])) $ret = true;
		return $ret;
	}
}

$ds = new DaumCpcList;
if(!$ds->check_accept_ip()) exit;

header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: text/plain; charset=euc-kr");

$ds -> exec();
?>
