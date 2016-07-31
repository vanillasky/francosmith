<?
### CJ GLS + 대한통운
$out = iconv("UTF-8","EUC-KR//TRANSLIT",$out);

$out = split_betweenStr($out,"<!-- contents -->","<!-- //contents -->");
$out[0] = str_replace("/dtd_images/","http://www.doortodoor.co.kr/dtd_images/",$out[0]);
$out[0] = preg_replace('/(<!-- title_area -->.+?)+(<!-- \/\/title_area -->)/is', '', $out[0]);
$out[0] = preg_replace('/(<p class="linemap">.+?)+(<\/p>)/is', '', $out[0]);
$out[0] = preg_replace('/(<a href="javascript:go_logii\(\);.+?)+(<\/a>)/is', '', $out[0]);
$out[0] = preg_replace('/(<a href="javascript:goLink.+?)+(<\/a>)/is', '', $out[0]);

?>
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<link rel="stylesheet" href="http://www.doortodoor.co.kr/dtd_css/common.css" media="all" />
<link rel="stylesheet" href="http://www.doortodoor.co.kr/dtd_css/style.css" media="all" />

<style>
.contents {width:auto;padding-bottom:0px}
.contents .title_area {padding-top:0px; padding-bottom:0px}
.intap {position:static}
</style>

<div style="padding-bottom:10px;">
<?
print $out[0];
?>
</div>