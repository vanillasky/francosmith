<?

$location = "회원관리 > 회원리스트";
include "../_header.php";

$returnUrl = ($_GET['returnUrl']) ? $_GET['returnUrl'] : $_SERVER['HTTP_REFERER'];

$parseUrl	= parse_url( $returnUrl );
$listUrl	= ( $returnUrl ? $parseUrl['query'] : $_SERVER['QUERY_STRING'] );
$listUrl	= 'list.php?' . preg_replace( "'(mode|m_id)=[^&]*(&|)'is", '', $listUrl );

include "info_inc.php";

include "../_footer.php";
?>