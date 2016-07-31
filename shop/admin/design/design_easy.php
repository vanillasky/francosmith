<?

$location = "기본관리 > 디자인기본설정";
include "../_header.php";


### 맵 HTML 링크주소를 스크립트함수로 변경
if ( empty( $_GET[map] ) ) $_GET[map] = 'map_main.html';
$tmp = './easy_map/' . $_GET[map];
$file = file( $tmp );
$map_html = implode("",$file);

preg_match_all('/href="([^"]*[gif|jpg])"/', $map_html, $matches);
foreach ( $matches[1] as $k => $v ) $matches[2][$k] = "href=\"javascript:img_replace('{$v}');\"";

$map_html = str_replace( $matches[0], $matches[2], $map_html );
?>

<script language="javascript"><!--
function img_replace( imgpath ){
	popupLayer('../design/popup.easy.php?imgpath=' + imgpath,600,500);
}
--></script>

<!-- 맵 HTML : Start -->
<?=$map_html?>
<!-- 맵 HTML : Start -->


<? include "../_footer.php"; ?>