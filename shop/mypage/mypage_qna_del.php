<?

include "../_header.php"; chkMember();

### 변수할당
$mode		= $_GET[mode];
$sno		= $_GET[sno];

### 1:1 문의
$query = "select a.subject from ".GD_MEMBER_QNA." a where a.sno='$sno'";
$data = $db->fetch($query,1);

### 템플릿 출력
$tpl->print_('tpl');

?>