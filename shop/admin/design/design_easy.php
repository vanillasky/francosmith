<?

$location = "�⺻���� > �����α⺻����";
include "../_header.php";


### �� HTML ��ũ�ּҸ� ��ũ��Ʈ�Լ��� ����
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

<!-- �� HTML : Start -->
<?=$map_html?>
<!-- �� HTML : Start -->


<? include "../_footer.php"; ?>