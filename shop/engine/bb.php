<?
include "../lib/library.php";
include "../lib/page.class.php";
include "../conf/config.php";
include "../conf/engine.php";
include "../conf/config.pay.php";
@include "../conf/fieldset.php";

function img_url($url,$src){
	if(preg_match('/http:\/\//',$src))$img_url = $src;
	else $img_url = $url.'/data/goods/'.$src;
	return $img_url;
}

$url = "http://".$_SERVER['HTTP_HOST'].$cfg[rootDir];

if(!$bb[chk]){
	exit;
}

### 기본 회원 할인율
if($joinset[grp] != ''){
	list($mdc) = $db->fetch("select dc from gd_member_grp where level='".$joinset[grp]."' limit 1");
}

### 상품 리스트
$db_table = "
".GD_GOODS_LINK." a
left join ".GD_GOODS." b on a.goodsno=b.goodsno
left join ".GD_GOODS_OPTION." c on a.goodsno=c.goodsno and link and go_is_deleted <> '1' and go_is_display = '1'
left join ".GD_GOODS_BRAND." d on b.brandno=d.sno
";

$where[] = "open";

$pg = new Page($_GET[page],1000);
$pg->cntQuery = "select count(distinct a.goodsno) from ".GD_GOODS_LINK." a,".GD_GOODS." b where a.goodsno=b.goodsno and open";
$pg->field = "
a.goodsno,b.*,
c.price,
c.reserve,
c.opt1,
c.opt2,
c.consumer,
".getCategoryLinkQuery('a.category', null, 'max').",
d.brandnm
";
$pg->setQuery($db_table,$where,$_GET[sort],'group by a.goodsno');
$pg->exec();
?>
<pre>
&lt;&lt;&lt;total&gt;&gt;&gt;<?=chr(10)?>
	<<총상품수>><?=number_format($pg->recode[total])?><?=chr(10)?>
	<<최종갱신일>><?=Core::helper('Date')->format(G_CONST_NOW)?><?=chr(10)?>
	<<수정/추가상품수>><?=number_format($pg->recode[total])?><?=chr(10)?>
&lt;&lt;&lt;/total&gt;&gt;&gt;<?=chr(10)?>
<?
$goodsModel = Clib_Application::getModelClass('goods');

$res = $db->query($pg->query);
while ($data=$db->fetch($res)){

	// 판매 중지(기간 외 포함)인 경우 제외
	if (! $goodsModel->setData($data)->canSales()) continue;

	$query ="select subject
			from ".GD_GOODS_DISPLAY." a
				, ".GD_EVENT." b
			where a.goodsno='$data[goodsno]'
				and substring(a.mode,1,1) = 'e'
				and trim(substring(a.mode,2))=trim(b.sno)
				and b.sdate <= '".date('Ymd',time())."'
				and b.edate >= '".date('Ymd',time())."'";
	$r1 = $db->query($query);

	$event = "";
	while($data1 = $db->fetch($r1)) $event .= ",".$data1[subject];
	if($event)$event = substr($event,1);

	$img_arr = explode("|",$data['img_s']);
	$img_url1 = img_url($url,$img_arr[0]);
	$img_arr = explode("|",$data['img_m']);
	$img_url2 = img_url($url,$img_arr[0]);

	### 배송료
	$param = array(
		'mode' => '1',
		'deliPoli' => 0,
		'price' => $data[price],
		'goodsno' => $data[goodsno],
		'goods_delivery' => $data[goods_delivery],
		'delivery_type' => $data[delivery_type]
	);
	$tmp = getDeliveryMode($param);
	$delivery = $tmp['price']+0;

	### 즉석할인쿠폰 유효성 검사
	list($data[coupon],$data[coupon_emoney]) = getCouponInfo($data[goodsno],$data[price]);
	$data[reserve] += $data[coupon_emoney];

	### 회원할인
	$dcprice = 0;
	if($mdc)$dcprice = getDcprice($data[price],$mdc.'%');

	$t = strlen($data[category]);

	$catenm="";
	if($t){
		for($i=3;$i<=$t;$i+=3) $catenm .= "@".getCatename(substr($data[category],0,$i));
		$catenm = substr($catenm,1);
	}

	$goods_url = "http://{$_SERVER['HTTP_HOST']}{$cfg[rootDir]}/goods/goods_view.php?goodsno=".$data[goodsno]."&category=".$data[category]."&inflow=".$bb[gubun];
	$p = $pg->page['navi'];
	$p = str_replace("<b>","<a href='".$_SERVER[PHP_SELF]."?page=1'>",$p);
	$p = str_replace("class=navi","",$p);
	$p = str_replace("[","",$p);
	$p = str_replace("]","",$p);
	$p = str_replace("</b>","</a>",$p);
	$data[goodsnm] = str_replace(array('※','☆','★','○','●','◎','◇','◆','□','■','△','▲','▽','▼','◁','◀','▷','▶','♤','♠','♡','♥','♧','♣','⊙','◈','▣','◐','◑','◐','◑','▒','▤','▥','▨','▧','▦','▩','♨','☏','☎','☜','☞','¶','†','‡','↕','↗','↙','↖','↘','♭','♩','♪','♬','㉿','＃'),'',$data[goodsnm]);
?>
&lt;&lt;&lt;product&gt;&gt;&gt;<?=chr(10)?>
   <<<상품아이디>>><?=$data[goodsno]?><?=chr(10)?>
   <<<상품명>>><?=$data[goodsnm]?><?=chr(10)?>
   <<<상품분류명>>><?=$catenm?><?=chr(10)?>
   <<<제조사>>><?=$data[maker]?><?=chr(10)?>
   <<<출시일>>><?=substr($data[launchdt],0,7)?><?=chr(10)?>
   <<<브랜드>>><?=$data[brandnm]?><?=chr(10)?>
   <<<원산지>>><?=$data[origin]?><?=chr(10)?>
   <<<상품URL>>><?=$goods_url?><?=chr(10)?>
   <<<상품이미지URL>>><?=$img_url1?><?=chr(10)?>
   <<<상품큰이미지URL>>><?=$img_url2?><?=chr(10)?>
   <<<판매가>>><?=($data[price] - $dcprice)?><?=chr(10)?>
   <<<배송료>>><?=$delivery?><?=chr(10)?>
   <<<배송기간>>><?=chr(10)?>
   <<<할인쿠폰>>><?=$data[coupon]?><?=chr(10)?>
   <<<적립금>>><?=$data[reserve]?><?=chr(10)?>
   <<<무이자할부>>><?=$card[cardfree]?><?=chr(10)?>
   <<<이벤트>>><?=$event?><?=chr(10)?>
&lt;&lt;&lt;/product&gt;&gt;&gt;<?=chr(10)?>
<?
}
?>
<?=$p?>
</pre>
