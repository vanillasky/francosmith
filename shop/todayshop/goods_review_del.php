<?

include "../_header.php";

### 변수할당
$mode		= $_GET[mode];
$sno		= $_GET[sno];

### 상품 사용기
$query = "select a.m_no, a.subject from ".GD_TODAYSHOP_GOODS_REVIEW." a where a.sno='$sno'";
$data = $db->fetch($query,1);

### 템플릿 출력
$tpl->print_('tpl');

?>