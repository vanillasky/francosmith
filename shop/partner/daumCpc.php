<?
require "../lib/library.php";
require "../conf/config.pay.php";
require "../conf/config.php";
require "../lib/load.class.php";
require "../lib/partner.class.php";
@include "../conf/coupon.php";
@require "../conf/daumCpc.cfg.php";

if($daumCpc['useYN']!= 'Y') exit;

class DaumCpcList Extends LoadClass
{
	function getDeliveryPrice($v){
		global $set;
		$deli1 = "1";
		$deli2 = "유료";
		if($v['delivery_type'] == 0){
			if($set['delivery']['free'] && $set['delivery']['default'] && $set['delivery']['deliveryType'] != "후불"){
				$deli1 = "2";
				$deli2 = $set['delivery']['free']."원이상무료 or ".$set['delivery']['default']."원";
			}
		}else if($v[delivery_type] == 1){
			$deli1 = "0";
			$deli2 = "무료";
		}
		return array($deli1,$deli2);
	}

	function exec(){
		global $db,$daumCpc,$cfg,$cfgCoupon,$set;
		$url = "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir'];
		$this->class_load('Partner','Partner');
		$godo = $this->class['Partner']->getGodoCfg();
		$memberdc = $this->class['Partner']->getBasicDc();
		$catnm = $this->class['Partner']->getCatnm();
		$query = $this->class['Partner']->getGoodsSql($mode);
		$res = $db->query($query);
		$tdate = date('Ymd',time());

		$goodsModel = Clib_Application::getModelClass('goods');

		while ($v = $db->fetch($res)){

			// 판매 중지(기간 외 포함)인 경우 제외
			if (! $goodsModel->setData($v)->canSales()) continue;

			list($v['price'],$v['reserve']) = $this->class['Partner']->getGoodsOption($v['goodsno']);

			### 상품명에 머릿말 조합
			$v['goodsnm'] = $this->class['Partner']->getGoodsnm($daumCpc,$v);

			### 즉석할인쿠폰
			list($v['coupon'],$v['coupon_emoney']) = getCouponInfo($v['goodsno'],$v['price']);
			$v['reserve'] += $v['coupon_emoney'];
			$coupon = 0;
			if($v['coupon'])$coupon = getDcprice($v['price'],$v['coupon']);

			### 회원할인
			$dcprice = 0;
			if (is_array($memberdc) === true) {
				$mdc_exc = chk_memberdc_exc($memberdc,$v['goodsno']); // 회원할인 제외상품 체크
				if($mdc_exc === false)$dcprice = getDcprice($v['price'],$memberdc['dc'].'%');
			}

			### 쿠폰 회원할인 중복 할인 체크
			if($coupon>0 && $dcprice>0){
				if($cfgCoupon['range'] == 2)$dcprice=0;
				if($cfgCoupon['range'] == 1)$coupon=0;
			}

			### 노출 가격
			$coupon += 0;
			$dcprice += 0;
			$price = $v['price'] - $coupon - $dcprice;

			### 배송비
			$deli = $this->getDeliveryPrice($v);
			$img_url = $this->class['Partner']->getGoodsImg($v['img_m'],$url);

			### review
			$review = $this->class['Partner']->getReviewCnt($v['goodsno']);
			
			###업데이트 날짜
			$updateimg=preg_replace("/[^0-9]*/s",'',$v['updatedt']);

			### event
			$event = $this->class['Partner']->getEvent($v['goodsno'],$tdate);
			if($event['sno']) $eventurl = $url . "/goods/goods_event.php?sno=".$event['sno'];
			
			###적립금
			if($v['use_emoney']=='0')
			{
				if( !$set['emoney']['chk_goods_emoney'] ){
					if( $set['emoney']['goods_emoney'] ) {
						$dc=$set['emoney']['goods_emoney']."%";
						$tmp_price = $v['price'];
						if( $set['emoney']['cut'] ) $po = pow(10,$set['emoney']['cut']);
						else $po = 100;
						$tmp_price = (substr($dc,-1)=="%") ? $tmp_price * substr($dc,0,-1) / 100 : $dc;
						$point =  floor($tmp_price / $po) * $po;

					}
				}else{
					$point	= $set['emoney']['goods_emoney'];
				}
			}
			else
			{
				$point=$v['reserve'];
			}
			
			if($catnm[substr($v['category'],0,3)] && $godo['sno'] && $v['goodsno'] && $price != "" && $v['goodsnm']){
				echo('<<<begin>>>'.chr(10));
				echo('<<<pid>>>'.$v['goodsno'].chr(10));
				echo('<<<price>>>'.$price.chr(10));
				echo('<<<pname>>>'.$v['goodsnm'].chr(10));
				echo('<<<pgurl>>>'.$url.'/goods/goods_view.php?inflow=daumCpc&goodsno='.$v['goodsno'].chr(10));
				echo('<<<igurl>>>'.$img_url.chr(10));
				if($v['updatedt'])echo('<<<updateimg>>>'.$updateimg.chr(10));
				for ($i=1;$i<=strlen($v['category'])/3;$i++) echo('<<<cate'.$i.'>>>'.substr($v['category'],0,$i*3).chr(10));
				for ($i=1;$i<=strlen($v['category'])/3;$i++) echo('<<<catename'.$i.'>>>'.$catnm[substr($v['category'],0,$i*3)].chr(10));
				if($v['goodscd'])echo('<<<model>>>'.$v['goodscd'].chr(10));
				if($v['brandnm'])echo('<<<brand>>>'.$v['brandnm'].chr(10));
				if($v['maker'])echo('<<<maker>>>'.$v['maker'].chr(10));
				if($v['launchdt'])echo('<<<pdate>>>'.str_replace('-','',substr($v['launchdt'],0,7)).chr(10));
				if ($coupon) echo('<<<coupon>>>'.$coupon.chr(10));
				if($daumCpc['nv_pcard'])echo('<<<pcard>>>'.$daumCpc['nv_pcard'].chr(10));
				if($point)echo('<<<point>>>'.$point.chr(10));
				echo('<<<deliv>>>'.$deli[0].chr(10));
				echo('<<<deliv2>>>'.$deli[1].chr(10));
				if($review)echo('<<<review>>>'.$review.chr(10));
				if($event['subject'])echo('<<<event>>>'.$event['subject'].chr(10));
				if($eventurl)echo('<<<eventurl>>>'.$eventurl.chr(10));
				if($cfg['shopName'])echo('<<<sellername>>>'.$cfg['shopName'].chr(10));
				echo('<<<end>>>'.chr(10));
			}
			flush();
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
