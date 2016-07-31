<?

include "../_header.php";
include "../lib/page.class.php";

$codeitem = codeitem('faq');

# 압축코드 정의
$summary_search = array();
$summary_search[] = "/__shopname__/is";			# 쇼핑몰이름
$summary_search[] = "/__shopdomain__/is";		# 쇼핑몰주소
$summary_search[] = "/__shopcpaddr__/is";		# 사업장주소
$summary_search[] = "/__shopcoprnum__/is";		# 사업자등록번호
$summary_search[] = "/__shopcpmallceo__/is";	# 쇼핑몰 대표
$summary_search[] = "/__shopcpmanager__/is";	# 개인정보관리자
$summary_search[] = "/__shoptel__/is";			# 쇼핑몰 전화
$summary_search[] = "/__shopfax__/is";			# 쇼핑몰 팩스
$summary_search[] = "/__shopmail__/is";			# 쇼핑몰 이메일

$summary_replace = array();
$summary_replace[] = $cfg["shopName"];			# 쇼핑몰이름
$summary_replace[] = $cfg["shopUrl"];			# 쇼핑몰주소
$summary_replace[] = $cfg["address"];			# 사업장주소
$summary_replace[] = $cfg["compSerial"];		# 사업자등록번호
$summary_replace[] = $cfg["ceoName"];			# 쇼핑몰 대표
$summary_replace[] = $cfg["adminName"];			# 개인정보관리자
$summary_replace[] = $cfg["compPhone"];			# 쇼핑몰 전화
$summary_replace[] = $cfg["compFax"];			# 쇼핑몰 팩스
$summary_replace[] = $cfg["adminEmail"];		# 쇼핑몰 이메일

if ( isset( $_GET[ssno] ) == false && isset( $_GET[sitemcd] ) == false && isset( $_GET[faq_sword] ) == false ){
	$_GET['sitemcd'] = array_shift( array_keys( $codeitem ) ); // 분류 기본값
}

### FAQ
$pg = new Page($_GET[page],1000);
$pg->field = "sno, itemcd, question, descant, answer";
$db_table = "".GD_FAQ."";

if ($_GET[ssno]){
	$where[] = "sno='$_GET[ssno]'";
}

if ($_GET[sitemcd]){
	$where[] = "itemcd='$_GET[sitemcd]'";
}

if ($_GET[faq_sword]){
	$where[] = "concat(question, descant, answer) like '%$_GET[faq_sword]%'";
}

$pg->setQuery($db_table,$where,$sort='sort');
$pg->exec();

$res = $db->query($pg->query);
while ($data=$db->fetch($res)){

	$data['idx'] = $pg->idx--;

	$data['itemcd'] = $codeitem[ $data['itemcd'] ];

	if ( blocktag_exists( $data[descant] ) == false ){
		$data[descant] = nl2br($data[descant]);
	}

	$data[descant] = preg_replace( $summary_search, $summary_replace, $data[descant] );

	if ( blocktag_exists( $data[answer] ) == false ){
		$data[answer] = nl2br($data[answer]);
	}

	$data[answer] = preg_replace( $summary_search, $summary_replace, $data[answer] );

	$loop[] = $data;
}

$tpl->assign( 'pg', $pg );

### 템플릿 출력
$tpl->print_('tpl');

?>