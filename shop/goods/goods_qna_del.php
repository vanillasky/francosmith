<?

include "../_header.php";

### 변수할당
$mode		= $_GET[mode];
$sno		= $_GET[sno];

### 상품 질문과답변
$query = "select a.m_no, a.subject from ".GD_GOODS_QNA." a where a.sno='$sno'";
$data = $db->fetch($query,1);

### 템플릿 출력
$tpl->print_('tpl');

if(isset($sess) && $data[m_no] == $sess[m_no]){
	echo("<script>document.forms[0].password.value='1';document.forms[0].submit();</script>");
}
?>