<?
include "../lib/library.php";
include "../lib/page.class.php";
include "../conf/config.php";
include "../conf/engine.php";
include "../conf/config.pay.php";
if(!$enuri[chk])exit;

function img_url($url,$src){
	if(preg_match('/http:\/\//',$src))$img_url = $src;
	else $img_url = $url.'/data/goods/'.$src;
	return $img_url;
}

$url = "http://".$_SERVER['HTTP_HOST'].$cfg[rootDir];

header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: text/plain; charset=euc-kr");
?>
<html>
<head>
<title>엔진페이지</title>
</head>
<style>
body {font-size:9pt; font-family:"굴림"; text-decoration: none; line-height: 13pt; color:	#333333}
td {font-size:9pt; font-family:"굴림"; text-decoration: none; line-height: 13pt; color:	#333333}
</style>
</head>
<body topmargin="0" leftmargin="10">
<?
if($_GET[type]){
	switch ($_GET[type]){
		case "category" :
			$query = "select * from ".GD_CATEGORY." where CHAR_LENGTH(category) = 3 and hidden = '0' order by sort";
			$res = $db->query($query);
			while($row = $db->fetch($res)){
				$prn_tags .= "<tr bgcolor='white'><td align=center><a href='$PHP_SELF?type=goods&category=$row[category]'>{$row[catnm]}</a></td>";
				$query = "select * from ".GD_CATEGORY." where CHAR_LENGTH(category)=6 and hidden = '0' and category like '$row[category]%' order by sort";
				$res2 = $db->query($query);
				$tmp = "";
				while($row2 = $db->fetch($res2)){
					$tmp .= "<a href='$PHP_SELF?type=goods&category=$row2[category]'>$row2[catnm]</a> | ";
				}
				if($tmp) $tmp = substr($tmp,0,-3);
				$prn_tags .= "<td>$tmp</td></tr>";
			}
			echo "<table border=\"0\" cellspacing=\"1\" cellpadding=\"10\" bgcolor=\"white\" width=\"90%\" align='center'>
			<tr><td>▒ <b>{$cfg[shopName]} (Category 분류)</b></td></tr>
			<table>
			<table border=\"0\" cellspacing=\"1\" cellpadding=\"5\" bgcolor=\"black\" width=\"91%\" align='center'>
				<tr bgcolor=\"#ededed\">
					<th width=60 align=center>대분류</th>
					<th>중분류</th>
				</tr>
				{$prn_tags}
			</table>";
		break;
		case "goods" :
			if($_GET[category]){
				if(strtoupper($pg[receipt]) == 'Y') $pg[receipt] = strtoupper($pg[receipt]);
				else $pg[receipt] = "N";

				// 상품분류 연결방식 전환 여부에 따른 처리
				$whereArr	= getCategoryLinkQuery('a.category', $_GET['category'], null, 'a.goodsno');

				$pg = new Page($_GET[page],1000);
				$pg->cntQuery = "select count(".$whereArr['distinct']." a.goodsno) from ".GD_GOODS_LINK." a,".GD_GOODS." b where a.goodsno=b.goodsno  and ".$whereArr['where']." and open";
				$pg->field = "a.goodsno,b.*,c.price,c.reserve,c.opt1,c.opt2,c.consumer,d.brandnm,b.delivery_type,b.goods_delivery";
				$db_table = "
				".GD_GOODS_LINK." a
				left join ".GD_GOODS." b on a.goodsno=b.goodsno
				left join ".GD_GOODS_OPTION." c on a.goodsno=c.goodsno and link and go_is_deleted <> '1' and go_is_display = '1'
				left join ".GD_GOODS_BRAND." d on b.brandno=d.sno
				";
				$where[] = $whereArr['where'];
				$where[] = "open";
				$pg->setQuery($db_table,$where,$_GET[sort],$whereArr['group']);
				$pg->exec();
				$m = $pg->recode[start];
				$res = $db->query($pg->query);

				echo "
					<center>상품수 : {$pg->recode['total']} 개</center>
					<table border=\"0\" cellspacing=\"1\" cellpadding=\"1\" bgcolor=\"black\" width=\"100%\" align='center'>
						  <tr align=\"center\" bgcolor=\"EDEDED\">
							<td height=\"24\" align=\"center\">번호</td>
							<td height=\"24\" align=\"center\">제품명</td>
							<td height=\"24\" align=\"center\">가격</td>
							<td height=\"24\" align=\"center\">재고<br>유무</td>
							<td height=\"24\" align=\"center\">배송</td>
							<td height=\"24\" align=\"center\">웹상품이미지</td>
							<td height=\"24\" align=\"center\">할인<br>쿠폰 <br></td>
							<td height=\"24\" align=\"center\">계산<br>서</td>
							<td height=\"24\" align=\"center\">제조사</td>
							<td height=\"24\" align=\"center\">상품<br>코드</td>
							<td height=\"24\" align=\"center\">무이자<br>할부</td>
							<td height=\"24\" align=\"center\">카드할인가</td>
							<td height=\"24\" align=\"center\">모바일가격</td>
							<td height=\"24\" align=\"center\">차등배송비</td>
							<td height=\"24\" align=\"center\">설치비</td>
						 </tr>";

				$goodsModel = Clib_Application::getModelClass('goods');

				while ($data=$db->fetch($res)){

					// 판매 중지(기간 외 포함)인 경우 제외
					if (! $goodsModel->setData($data)->canSales()) continue;

					$data[stock] = $data['totstock'];

					### 실재고에 따른 자동 품절 처리
					if (($data[usestock] && $data[stock]==0) || $data['runout']) $data[runout] = "재고<br>없음";
					else $data[runout] = "재고<br>있음";

					$img_arr = explode("|",$data['img_m']);
					$img_url = img_url($url,$img_arr[0]);

					### 즉석할인쿠폰 유효성 검사
					list($data[coupon],$data[coupon_emoney]) = getCouponInfo($data[goodsno],$data[price]);
					$data[reserve] += $data[coupon_emoney];

					if($data['coupon'] > 0){
						$data['price'] = $data['price'] - $data['coupon'];
					}

					$goods_url = "http://{$_SERVER['HTTP_HOST']}{$cfg[rootDir]}/goods/goods_view.php?goodsno=".$data[goodsno]."&category=".$_GET[category]."&inflow=".$enuri[gubun];

					### 배송료
					$param = array(
						'mode' => 1,
						'deliPoli' => 0,
						'price' => $data['price'],
						'goodsno' => $data[goodsno],
						'goods_delivery' => $data[goods_delivery],
						'delivery_type' => $data[delivery_type]
					);

					$tmp = getDeliveryMode($param);
					$delivery = 0;
					if($tmp['free'] && $tmp['price']) $delivery = "유료".$tmp['price']."원 ".$tmp['free']."이상 무료 ";
					else if($tmp['msg']=='개별 착불 배송비' && $data['goods_delivery']) $delivery = "유료".$data[goods_delivery]."원";
					else if($tmp['price']) $delivery = "유료".$tmp['price']."원";
					else if($tmp['type']=='선불' && !$tmp['price']) $delivery = "무료";

					$extra_info = gd_json_decode(stripslashes($data['extra_info']));
					$settingPrice = '';
					$addPrice = '';
					$isAddPrice = '';
					if(is_array($extra_info)){
						foreach($extra_info as $key=>$val) {
							if($val['title'] == '배송 · 설치비용'){
								$settingPrice = $val['desc'];
							}
							if($val['title'] == '추가설치비용'){
								$addPrice = $val['desc'];
							}
						}
					}
					if($addPrice) {
						$isAddPrice = 'Y';
					}

					$m++;
					echo "
					<tr align='center' bgcolor='#FFFFFF'>
						<td height='24'>{$m}</td>
						<td height='24' style='padding-top:3px;padding-bottom:3px'>
							<a href='$goods_url'>{$data[goodsnm]}</a></td>
						<td height='24'>{$data[price]}</td>
						<td height='24'>{$data[runout]}</td>
						<td height='24'>{$delivery}</td>
						<td height='24'>$img_url</td>
						<td height='24'>{$data[coupon]}</td>
						<td height='24'>{$str_receipt}</td>
						<td height='24'>{$data[maker]}</td>
						<td height='24'>{$data[goodsno]}</td>
						<td height='24'>{$card[cardfree]}</td>
						<td height='24'></td>
						<td height='24'>{$data[price]}</td>
						<td height='24'>{$settingPrice}</td>
						<td height='24'>{$isAddPrice}</td>
					</tr>";
					flush();
				}


				echo "<tr><td colspan=11 align=center bgcolor='#FFFFFF'>".$pg->page['navi']."</td></tr>	 ";
			}
		break;
	}
}
?>
</body>
</html>
