<?php

include "../lib/library.php";
@include "../conf/config.pay.php";
include "../conf/config.php";

$shoppingApp = $config->load('shoppingApp');
if($shoppingApp){
    $shoppingApp['e_exceptions']=unserialize($shoppingApp['e_exceptions']);
}

if ($shoppingApp['useApp'] != 'Y') exit;

/**
 * 디자인공장 어플로 보내는 xml 형식
 *	<item>
 *	<title> 상품명 </title> 
 *	<description> 부제목, 서브타이틀, 간단설명, 한줄 설명 .. </description> <-- 이 부분에는 여러가지 값이 가능하나 , 짧은 설명 데이터를 보냄.
 *	<link> 눌렀을때 이동되는 url 주소 </link>
 *	<media:thumbnail width="썸네일이미지가로사이즈" height="썸네일이미지세로사이즈" url="썸네일이미지주소"/>
 *	</item>
**/

## 예외처리
$exceptions_condition = "";
$exceptions_tmp = "";

### 상품진열 여부
if (count($shoppingApp['e_exceptions'])>0) {
	foreach($shoppingApp['e_exceptions'] as $v) {
		if (strlen($exceptions_tmp)>0) 	$exceptions_tmp .= "','";
		$exceptions_tmp .= $v ;
	}	
	$arWhere[] = " goodsno in ('".$exceptions_tmp."') ";
}else{
	$arWhere[] = " 0 ";
}

if($arWhere) $strWhere = implode(' and ',$arWhere);

if(!empty($load_config_shoppingApp['orderby']) && !$_GET['sort']){
	$orderby = "order by a.".$shoppingApp['orderby']." desc";
}

$query = "
	select
		distinct goodsno,goodsnm,shortdesc,img_mobile,img_s,totstock,usestock,runout
	from
		".GD_GOODS."
	where
		open=1 and {$strWhere}
	{$orderby}
";

$result = $db->query($query);

$shopDomain = 'http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'];

header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: application/xml; charset=EUC-KR");

echo "<?xml version=\"1.0\" encoding=\"EUC-KR\" ?>"."\n";

echo "<?xml-stylesheet title=\"XSL_formatting\" type=\"text/xsl\" href=\"/shared/bsp/xsl/rss/nolsol.xsl\"?><rss version=\"2.0\" xmlns:media=\"http://www.qng.co.kr\"><channel><ttl>100</ttl><summery>&amp;</summery>"."\n";

## 상품정보 
while($data = $db->fetch($result)){
	## 품절여부
	if($data['usestock'] == 'o' && $data['totstock'] == '0') $data[runout] = 1;

	if($data[runout]) continue;

	## 리스트 이미지 ( 모바일용 이미지가 있을때는 모바일이미지로 , 없다면 리스트 이미지로 )
	if($data['img_mobile']) $data['img_s'] = $data['img_mobile'];

	## 이미지 사이즈 구하기
	if($data['img_s']) $size = @getimagesize($shopDomain."/data/goods/".$data['img_s']);

	$width = ($size[0]) ? $size[0] : "66";
	$height = ($size[1]) ? $size[1] : "49";

	echo "<item>"."\n";
	echo "<title>".htmlspecialchars($data['goodsnm'])."</title>"."\n";
	echo "<description>".htmlspecialchars($data['shortdesc'])."</description>"."\n";
	echo "<link>".$shopDomain."/goods/goods_view.php?goodsno=".$data['goodsno']."</link>"."\n";
	echo "<media:thumbnail width=\"".$width."\" height=\"".$height."\" url=\"".$shopDomain."/data/goods/".$data['img_s']."\"/>"."\n";
	echo "</item>"."\n";
	
}

echo "</channel>"."\n";
echo "</rss>";

?>
