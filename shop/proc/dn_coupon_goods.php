<?
include "../lib/library.php";
include "../conf/config.php";

$date = date("Y-m-d H:i:s");

## $_GET[couponcd]가 없을 경우
if(!$_GET[couponcd] && $_GET[goodsno]){
	$query = "select a.couponcd from
				".GD_COUPON." a
				left join ".GD_COUPON_CATEGORY." b on a.couponcd = b.couponcd
				left join ".GD_COUPON_GOODSNO." c on a.couponcd = c.couponcd
			where
				a.coupontype = '1'
				AND c.goodsno = '$_GET[goodsno]' OR goodstype='0'
				AND ((a.sdate <= '$date' AND a.edate >= '$date' AND a.priodtype='0') OR (a.priodtype='1'))
				order by couponcd
				limit 1";
	list($_GET[couponcd]) = $db->fetch($query);
}

## 쿠폰정보 불러오기
$query = "select category,char_length(category) clen from ".GD_GOODS_LINK." where  goodsno='$_GET[goodsno]'";
$res = $db->query($query);
while($tmp = $db->fetch($res)) for($i=3;$i<=$tmp[clen];$i+=3) $arrCategory[] = "'".substr($tmp[category],0,$i)."'";

if($_GET[goodsno]) $where[] = "c.goodsno = '$_GET[goodsno]'";
if($arrCategory) $where[] = "b.category in(".implode(',',$arrCategory).")";
if($where)$strWhere = "OR ((".implode(' OR ', $where).") AND goodstype='1')";

$query = "select a.* from
				".GD_COUPON." a
				left join ".GD_COUPON_CATEGORY." b on a.couponcd = b.couponcd
				left join ".GD_COUPON_GOODSNO." c on a.couponcd = c.couponcd
			where
				a.coupontype = '1'
				AND (goodstype='0' $strWhere)
				AND ((a.sdate <= '".$date."' AND a.edate >= '".$date."' AND a.priodtype='0') OR (a.priodtype='1'))
				AND a.couponcd='$_GET[couponcd]' limit 1";
$data = $db->fetch($query);

if(!$data[couponcd]){
	
	msg("쿠폰발급이 종료되었습니다",0);
	exit;
}

### 쿠폰 발행 권한 체크
if (!$sess){
	msg("회원만 쿠폰이 다운가능합니다",0);
	exit;
}

### 미사용 동일 쿠폰 체크
list($cnt) = $db->fetch("select count(*) from ".GD_COUPON_APPLY." a LEFT JOIN ".GD_COUPON_APPLY."member b ON a.sno=b.applysno where b.m_no='$sess[m_no]' and goodsno='$_GET[goodsno]' and couponcd = '{$_GET[couponcd]}' and status='0'");
if($cnt){
	msg("미사용 동일 쿠폰이 이미 있습니다.",0);
	exit;
}

### 중복다운로드 및 다운로드 총량 체크
list($cnt) = $db->fetch("select count(*) from ".GD_COUPON_APPLY." a LEFT JOIN ".GD_COUPON_APPLY."member b ON a.sno=b.applysno where b.m_no='$sess[m_no]' and a.couponcd = '{$_GET[couponcd]}'");

if( $data[dncnt] && $cnt >= $data[dncnt]){
	msg("쿠폰 다운로드 횟수에 제한되었습니다.",0);
	exit;
}

###  각각 상품의 다운로드 횟수 체크
if($data[duplctl] && $data[edncnt]){
	list($cnt) = $db->fetch("select count(*) from ".GD_COUPON_APPLY." a LEFT JOIN ".GD_COUPON_APPLY."member b ON a.sno=b.applysno where b.m_no='$sess[m_no]' and a.goodsno='$_GET[goodsno]' and a.couponcd = '{$_GET[couponcd]}' ");
	if($cnt >= $data[edncnt]){
		msg("쿠폰 다운로드 횟수에 제한되었습니다.",0);
		exit;
	}
}


### 쿠폰 발행
list($goodsnm) = $db->fetch("select goodsnm from ".GD_GOODS." where goodsno='$_GET[goodsno]'");
$goodsnm = addslashes($goodsnm);
$setquery = "
		couponcd		= '$_GET[couponcd]',
		membertype		= '2',
		member_grp_sno  = '',
		goodsno			='$_GET[goodsno]',
		goodsnm			='$goodsnm',
		regdt			= now(),
		status			= '0'
	";

$query = "insert into ".GD_COUPON_APPLY." set  ".$setquery;
$db->query($query);

$query = "select max(sno) from ".GD_COUPON_APPLY;
list($newapplysno) = $db->fetch($query);

$query = "insert into ".GD_COUPON_APPLY."member set m_no='$sess[m_no]', applysno ='$newapplysno'";
$db->query($query);

msg("쿠폰이 정상적으로 발급되었습니다.\\n마이페이지 에서 확인하세요.");
if($_GET[emailcoupon]) go('http://'.$cfg[shopUrl].'/goods/goods_view.php?goodsno='.$_GET[goodsno]);
?>