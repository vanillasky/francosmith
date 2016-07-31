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
 * �����ΰ��� ���÷� ������ xml ����
 *	<item>
 *	<title> ��ǰ�� </title> 
 *	<description> ������, ����Ÿ��Ʋ, ���ܼ���, ���� ���� .. </description> <-- �� �κп��� �������� ���� �����ϳ� , ª�� ���� �����͸� ����.
 *	<link> �������� �̵��Ǵ� url �ּ� </link>
 *	<media:thumbnail width="������̹������λ�����" height="������̹������λ�����" url="������̹����ּ�"/>
 *	</item>
**/

## ����ó��
$exceptions_condition = "";
$exceptions_tmp = "";

### ��ǰ���� ����
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

## ��ǰ���� 
while($data = $db->fetch($result)){
	## ǰ������
	if($data['usestock'] == 'o' && $data['totstock'] == '0') $data[runout] = 1;

	if($data[runout]) continue;

	## ����Ʈ �̹��� ( ����Ͽ� �̹����� �������� ������̹����� , ���ٸ� ����Ʈ �̹����� )
	if($data['img_mobile']) $data['img_s'] = $data['img_mobile'];

	## �̹��� ������ ���ϱ�
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
