<?
include "../lib/library.php";
@include "../conf/config.pay.php";
include "../conf/config.php";
@include "../conf/auctionos.php";
@include "../conf/fieldset.php";
@include "../conf/coupon.php";

$aboutcoupon = $config->load('aboutcoupon');
$LF = "\r\n";

function check_accept_ip(){
	$out = readurl("http://gongji.godo.co.kr/userinterface/serviceIp/auctionos.php");
	$arr = explode(chr(10),$out);
	$ret = false;
	foreach($arr as $data){
		$data = trim($data);
		if($data&&preg_match('/'.$data.'/',$_SERVER['REMOTE_ADDR']))$ret = true;
	}
	if(preg_match('/admin\/auctionos\/partner.php/',$_SERVER['HTTP_REFERER'])) $ret = true;
	return $ret;
}

if(!check_accept_ip()) exit;

$delimiter = "<!>";
### 옥션 db url  메인 생성 ###
$file	= "../conf/godomall.cfg.php";
$file	= file($file);
$godo	= decode($file[1],1);
if(!$partner['auctionshopid'])$partner['auctionshopid'] = "GODO".$godo[sno];

$tmpdir = explode('/','../data/auctionos/godo/'.$partner['auctionshopid']);
foreach($tmpdir as $k => $data){
	unset($rdir);
	for($i=0;$i <= $k;$i++) $rdir[] = $tmpdir[$i];
	$dir = implode('/',$rdir);
	if(!is_dir($dir)){
		@mkdir($dir);
		@chmod($dir,0707);
	}
}
$fp = fopen($dir."/auctionos2.php","w");
fwrite($fp,'<?'.$LF);
fwrite($fp,'if($_GET[mode] && $_GET[mode] != "new")	include "../../../../conf/engine/auctionos2_".$_GET[mode].".php";'.$LF);
fwrite($fp,'else	include "../../../../partner/auctionos2.php";'.$LF);
fwrite($fp,'?>'.$LF);
fclose($fp);
@chmod($dir."/auctionos2.php",0707);

function eSpecialTag($str){
	$str = strip_tags($str);
	$tmp = "\" ' < > \ |";
	$arr = explode(' ',$tmp);
	$str = str_replace($arr,'',$str);
	return $str;
}

$dir = "../conf/engine";
if (!is_dir($dir)) {
	@mkdir($dir, 0707);
	@chmod($dir, 0707);
}

### 기본 회원 할인율
if($joinset[grp] != ''){
	$memberdc = $db->fetch("select dc,excep,excate from ".GD_MEMBER_GRP." where level='".$joinset[grp]."' limit 1");
}

$querycnt = "select count(1) from ".GD_GOODS."  where runout=0 and open=1";
list($totnum) = $db->fetch($querycnt);
$httphost=preg_replace("/\:[0-9]+/","",$_SERVER['HTTP_HOST']);
$url = "http://".$httphost.$cfg[rootDir];

### 카테고리명 배열
$query = "select * from ".GD_CATEGORY."";
$res = $db->query($query);
while ($data=$db->fetch($res)) $catnm[$data[category]] = $data[catnm];

if($tt != '1'){
	### 일주일간 판매 상품 총금액
	$onemonth = date("Y-m-d h:i:s",(time()-7*24*60*60));
	$query = "select sum(price * ea) from ".GD_ORDER_ITEM." a left join ".GD_ORDER." b on a.ordno=b.ordno where istep < '40' and b.cdt >= '$onemonth'";
	list($tot) = $db->fetch($query);
	if(!$tot)$tot = 1;
}

### 상품 데이타
$query = "
SELECT

	G.goodsno, G.goodsnm, G.goodscd, G.img_m, G.goods_delivery, G.delivery_type, G.maker, G.origin, G.updatedt, G.regdt,
	G.sales_range_start, G.sales_range_end,
	CT.category,
	GO.price,GO.reserve,
	BR.brandnm

FROM ".GD_GOODS." AS G

INNER JOIN ".GD_GOODS_LINK." AS LNK
	ON G.goodsno = LNK.goodsno

INNER JOIN ".GD_CATEGORY." AS CT
	ON LNK.category = CT.category

INNER JOIN ".GD_GOODS_OPTION." AS GO
	ON G.goodsno = GO.goodsno AND GO.link = 1 and go_is_deleted <> '1' and go_is_display = '1'

LEFT JOIN ".GD_GOODS_BRAND." AS BR
	ON G.brandno = BR.sno

WHERE G.open = 1 AND G.runout = 0

GROUP BY G.goodsno

ORDER BY NULL
";
$res = $db->query($query);

for($tt=0;$tt < 2;$tt++){

	switch($tt){
		case "0" : $filename = "auctionos2_all.php";
		break;
		case "1" : $filename = "auctionos2_summary.php";
		break;
	}

	$_filename = '../conf/engine/'.$filename;	// ep 파일

	// 파일 기록 시간을 근거로, 최종 ep 갱신 일을 구함
	if (($last_ep_update_time = @filectime($_filename)) === false) {
		$last_ep_update_time = time();
	}

	$last_ep_update_time = $last_ep_update_time - 43200;	// 추가 갱신 시간

	# 여기에서 일단 기존 파일을 삭제하고 다시 쓴다.
	$fp = fopen($_filename,"w");
	fwrite($fp,'<?'.$LF);
	fwrite($fp,'header("Cache-Control: no-cache, must-revalidate");'.$LF);
	fwrite($fp,'header("Content-Type: text/plain; charset=euc-kr");'.$LF);
	fwrite($fp,'?>'.$LF);
	fclose($fp);


	// 내부 포인터만 이동
	mysql_data_seek($res, 0);

	$fp = fopen($_filename,"a");

	$goodsModel = Clib_Application::getModelClass('goods');

	while ($data=$db->fetch($res)){

		// 판매 중지(기간 외 포함)인 경우 제외
		if (! $goodsModel->setData($data)->canSales()) continue;

		### 상품명에 머릿말 조합
		if($partner['goodshead'])$data[goodsnm] = str_replace(array('{_maker}','{_brand}'),array($data[maker],$data[brandnm]),$partner['goodshead']).$data['goodsnm'];
		$data['goodsnm'] = strip_tags($data['goodsnm']);
		$data['goodsnm'] = strcut(eSpecialTag($data['goodsnm']),255);

		/*
			1. 제외카테고리 상품여부 확인
			2. 특정카테고리 ( 성인상품) 표시
		 */

		list($data[img]) = explode("|",$data[img_m]);

		// 이미지 경로 처리 및 이미지 갱신 태그 설정
		if(preg_match('/http:\/\//',$data[img])) {
			$img_url = $data[img];
			$modimg = ($last_ep_update_time <= strtotime($data['updatedt'])) ? 'Y' : 'N';
		}
		else {
			$img_url = $url.'/data/goods/'.$data[img];
			$modimg = ($last_ep_update_time <= @filectime('../data/goods/'.$data[img])) ? 'Y' : 'N';
		}

		if($tt != '1'){
			###이벤트
			$date = date("Ymd");
			$query = "select z.subject from
									".GD_EVENT." z left join ".GD_GOODS_DISPLAY." a on z.sno=substring(a.mode,2) and substring(a.mode,1,1) = 'e'
									left join ".GD_GOODS." b on a.goodsno=b.goodsno
									left join ".GD_GOODS_OPTION." c on a.goodsno=c.goodsno and go_is_deleted <> '1' and go_is_display = '1'
									where link and a.goodsno='$row[goodsno]' and z.sdate <= '$date' and z.edate >= '$date' limit 1";
			list($event) = $db->fetch($query);

			### 일주일간 이 상품의 판매 금액
			$query = "select sum(a.price * a.ea) from ".GD_ORDER_ITEM." a left join ".GD_ORDER." b on a.ordno=b.ordno where istep < '40' and b.cdt >= '$onemonth' and a.goodsno='".$data['goodsno']."'";
			list($goodstot) = $db->fetch($query);
		}


		### 즉석할인쿠폰
		$coupon = 0;
		list($data[coupon],$data[coupon_emoney]) = getCouponInfo($data[goodsno],$data[price]);
		$data[reserve] += $data[coupon_emoney];
		if($data[coupon])$coupon = getDcprice($data[price],$data[coupon]);

		### 회원할인
		$dcprice = 0;
		if (is_array($memberdc) === true) {
			$mdc_exc = chk_memberdc_exc($memberdc,$data['goodsno']); // 회원할인 제외상품 체크
			if($mdc_exc === false)$dcprice = getDcprice($data['price'],$memberdc['dc'].'%');
		}

		### 어바웃 쿠폰 적용여부 확인
		$about_dc_price = 0;
		if ( $aboutcoupon['use_aboutcoupon'] == 'Y' && $aboutcoupon['use_test']=='N' ) {
			$about_dc_price = getDcprice($data[price], '8%');
		}

		### 쿠폰 회원할인 중복 할인 체크
		if($coupon>0 && $dcprice>0){
			if($cfgCoupon['range'] == 2)$dcprice=0;
			if($cfgCoupon['range'] == 1)$coupon=0;
		}

		### 노출 가격
		$coupon += 0;
		$dcprice += 0;
		$price = $data[price] - $coupon - $dcprice - $about_dc_price;

		### 배송료
		$param = array(
			'mode' => '1',
			'deliPoli' => 0,
			'price' => $price,
			'goodsno' => $data[goodsno],
			'goods_delivery' => $data[goods_delivery],
			'delivery_type' => $data[delivery_type]
		);
		$tmp = getDeliveryMode($param);
		$deli=0;
		if($tmp[type] =="후불" || ($tmp['free'] && $tmp['price'])) $deli = -1;
		else{
			$deli = $tmp['price']+0;
		}

		$EP_DATA = array();
		$EP_DATA[1] = $data['goodsno'];	 	//쇼핑몰 상품ID
		$EP_DATA[2] = ($tt != "1") ? 'C' : 'U';	 	//상품 구분(C/U/D)
		$EP_DATA[3] = $data['goodsnm'];	 	//상품명
		$EP_DATA[4] = $price;	 	//판매가격
		$EP_DATA[5] = $url.'/goods/goods_view.php?inflow=auctionos&goodsno='.$data['goodsno'];	 	//상품의 상세페이지 주소
		$EP_DATA[6] = $img_url;	 	//이미지 URL

		/*
		$EP_DATA[7];	 	//대분류 카테고리 코드
		$EP_DATA[8];	 	//중분류 카테고리 코드
		$EP_DATA[9];	 	//소분류 카테고리 코드
		$EP_DATA[10];	 	//세분류 카테고리 코드

		$EP_DATA[11];	 	//대카테고리명
		$EP_DATA[12];	 	//중카테고리명
		$EP_DATA[13];	 	//소카테고리명
		$EP_DATA[14];	 	//세카테고리명
		*/

		for ($i=1;$i<=4;$i++){
			if ($i*3 <= strlen($data['category'])) {
				$EP_DATA[$i + 6] = substr($data['category'],0,$i *3);	// 카테고리 코드
				$EP_DATA[$i + 10] = eSpecialTag($catnm[substr($data['category'],0,$i*3)]);	// 카테고리 명
			}
			else {
				$EP_DATA[$i + 6] = '';	// 카테고리 코드
				$EP_DATA[$i + 10] = '';	// 카테고리 명
			}
		}

		$EP_DATA[15] = strip_tags($data[goodscd]);	 	//모델명
		$EP_DATA[16] = strip_tags($data[brandnm]);	 	//브랜드
		$EP_DATA[17] = strip_tags($data[maker]);	 	//메이커
		$EP_DATA[18] = strip_tags($data[origin]);	 	//원산지
		$EP_DATA[19] = substr($data[regdt],0,10);	 	//상품등록일자
		$EP_DATA[20] = $deli;	 	//배송비
		$EP_DATA[21] = strip_tags($event);	 	//이벤트
		$EP_DATA[22] = ($coupon+$about_dc_price);	 	//쿠폰금액
		$EP_DATA[23] = trim($partner[nv_pcard]);	 	//무이자
		$EP_DATA[24] = $data[reserve];	 	//적립금
		$EP_DATA[25] = $modimg;	 	//이미지변경여부
		$EP_DATA[26] = '';	 	//물품특성정보
		$EP_DATA[27] = round($goodstot/$tot*100);	 	//상점내 매출비율

		$EP_DATA[28] = date('Y-m-d H:i:s');	 	//상품정보 변경시간*/

		ksort($EP_DATA);
		fwrite($fp, implode($delimiter, $EP_DATA).$LF);

		flush();
		$num++;
		if(!$_GET['gengine']){
			$per = round( $num / ($totnum * 2)  * 100 );
			echo("<script>parent.document.getElementById('progressbar').style.width='".$per."%';</script>\n");
		}

	}
	fclose($fp);
	@chmod('../conf/engine/'.$filename,0707);
}
if(!$_GET['gengine']){
	echo("<script>parent.document.getElementById('progressbar').style.width='100%';</script>\n");
	msg("업데이트 완료!");
}else{
	echo("ok!!");
}
?>
