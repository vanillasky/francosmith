<?php
require "../lib/library.php";
header("Content-Type: text/html; charset=UTF-8");

require "../conf/config.php";
require "../lib/load.class.php";
require "../lib/nateClipping.class.php";
@include "../conf/fieldset.php";
$nate = new NateClipping();
$query = $nate->get_xml_data($_GET['goodsno']);

if(!$query){
	echo "서비스 중지!";
	exit;
}
$data = $db->fetch($query);
if(!$nate->nateClipping[proContents]){
	$out = readurl("http://gongji.godo.co.kr/userinterface/clipping/pro_contents.inc.php");
	$tmp = explode(';',$out);
	$nate->nateClipping[proContents]=$tmp[0];
	$nate->nateClipping[proContentsLink]=$tmp[1];
}else if($nate->nateClipping[proContentsLink]){
	$nate->nateClipping[proContentsLink] = 'http://'.$nate->nateClipping[proContentsLink];
}
foreach($nate->nateClipping as $k=>$v)$nate->nateClipping[$k]=trim(mb_convert_encoding($v,"UTF-8","EUC-KR"));

$compName = trim(mb_convert_encoding($cfg['shopName'],"UTF-8","EUC-KR"));
$bi = $nate->get_BI($cfg);
$goodsUrl = 'http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/goods/goods_view.php?inflow=cywordScrap&goodsno=';
if($joinset[grp] != '')	list($mdc) = $db->fetch("select dc from gd_member_grp where level='".$joinset[grp]."' limit 1");

$r_img = explode("|",$data[img_m]);
list($data['img'],$data['width'],$data['height'])=$nate->get_img($r_img[0],$cfg['rootDir'],$cfg['img_m']);
$data['goodsUrl'] = $goodsUrl.$data['goodsno'];

### 금액계산
$coupon = 0;
list($data['coupon'],$data['coupon_emoney']) = getCouponInfo($data['goodsno'],$data['price']);
$data['reserve'] += $data['coupon_emoney'];
if($v['coupon'])$coupon = getDcprice($data['price'],$data['coupon']);
$dcprice = 0;
if($mdc)$dcprice = getDcprice($data['price'],$mdc.'%');
$data['price'] = $data['price'] - $coupon - $dcprice;
if($data['updatedt']<$nate->nateClipping['updateDt'])$data['updatedt']=$nate->nateClipping['updateDt'];
$data['updatedt'] = str_replace(array(':','-',' '),'',$data['updatedt']);
if($data)foreach($data as $k=>$v) $data[$k] = strip_tags(trim(mb_convert_encoding($v,"UTF-8","EUC-KR")));
if(!$data['img']||!$data['height']||!$data['width'])exit;
if(!$data['goodsnm'])exit;
if($data['price']==null)exit;

echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>
<ProductInfo xmlns=\"urn:skcomms:prod\"
xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
xsi:schemaLocation=\"urn:skcomms:prod http://api.cyworld.com/xml/openscrap/shopping/v1/product.xsd\">
<SID>".$nate->nateClipping['sid']."</SID>";
?>
<Product>
<Subject><![CDATA[<?php echo $data['goodsnm'];?>]]></Subject>
<GridImage>
<Url><![CDATA[<?php echo $data['img'];?>]]></Url>
<Height><?php echo $data['height'];?></Height>
<Width><?php echo $data['width'];?></Width>
</GridImage>
<OriginReducedPrice><![CDATA[가격:<?php echo number_format($data['price']);?>원]]></OriginReducedPrice>
<OriginAdd01><![CDATA[적립금:<?php echo number_format($data['reserve']);?>]]></OriginAdd01>
<OriginAdd02><![CDATA[]]></OriginAdd02>
<OriginAdd03><![CDATA[<?php echo $data['shortdesc'];?>]]></OriginAdd03>
<Merchant><![CDATA[<?php echo $compName;?>]]></Merchant>
<OriginSiteBI>
<BIIamge>
<Url><![CDATA[<?php echo $bi[0];?>]]></Url>
<Height>20</Height>
<Width>50</Width>
</BIIamge>
<BIURL><![CDATA[<?php echo $bi[1];?>]]></BIURL>
</OriginSiteBI>
<?php if($nate->nateClipping['proContents']){?>
<OriginPromotion>
<ProContents><![CDATA[<?php echo $nate->nateClipping['proContents'];?>]]></ProContents>
<?php if($nate->nateClipping['proContentsLink']){?>
<ProUrl><![CDATA[<?php echo $nate->nateClipping['proContentsLink'];?>]]></ProUrl>
<?php }?>
</OriginPromotion>
<?php }?>
<Url><![CDATA[<?php echo $data['goodsUrl'];?>]]></Url>
<LastUpdateDate><?php echo $data['updatedt'];?></LastUpdateDate>
</Product>
</ProductInfo>