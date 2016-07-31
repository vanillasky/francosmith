<?
include_once "../lib/library.php";
@include_once "../conf/config.pay.php";
include_once "../conf/config.php";
include_once "../lib/upload.lib.php";

$shoppingApp = $config->load('shoppingApp');

if($shoppingApp){
    $shoppingApp['app_premium2']=unserialize($shoppingApp['app_premium2']);
}

$shopDomain = 'http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'];

header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: application/xml; charset=UTF-8");

echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>"."\n";

echo "<?xml-stylesheet title=\"XSL_formatting\" type=\"text/xsl\" href=\"/shared/bsp/xsl/rss/nolsol.xsl\"?><rss version=\"2.0\" xmlns:media=\"http://www.qng.co.kr\"><channel><ttl>20</ttl>"."\n";

foreach($shoppingApp['app_premium2'] as $data){

	echo "<item>"."\n";
	echo "<title>".htmlspecialchars(iconv("euc-kr","utf-8",$data['title']))."</title>"."\n";
	echo "<description>".htmlspecialchars(iconv("euc-kr","utf-8",$data['description']))."</description>"."\n";
	echo "<link>".htmlspecialchars(iconv("euc-kr","utf-8",$data['link']))."</link>"."\n";
	echo "<media:thumbnail width=\"50\" height=\"50\" url=\"".iconv("euc-kr","utf-8",$shopDomain)."/data/m/app2/".iconv("euc-kr","utf-8",$data['thumbnail'])."\"/>"."\n";
	echo "</item>"."\n";

}

echo "</channel>"."\n";
echo "</rss>";
?>
