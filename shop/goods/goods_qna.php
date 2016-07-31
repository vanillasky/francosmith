<?

include "../_header.php";
include "../lib/page.class.php";
include "../lib/goods_qna.lib.php";

if(class_exists('validation') && method_exists('validation','xssCleanArray')){
	$_GET = validation::xssCleanArray($_GET, array(
		validation::DEFAULT_KEY => 'text',
	));
}

### 리스트 템플릿 기본 환경변수
$lstcfg[size]	= 50;
$lstcfg['page_num'] = array(10, 20, 30, 40, 50);
if(!is_numeric($cfg['qnaAllListSet']) || !$cfg['qnaAllListSet']) $cfg['qnaAllListSet'] = $lstcfg['page_num'][0];

### 변수할당
if (!$_GET[page_num]) $_GET[page_num] = $cfg['qnaAllListSet'];
$selected[page_num][$_GET[page_num]] = "selected";
$selected[skey][$_GET[skey]] = "selected";
if ( file_exists( dirname(__FILE__) . '/../data/skin/' . $cfg['tplSkin'] . '/admin.gif' ) ) $adminicon = 'admin.gif';

### 상품 문의
$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "distinct a.sno, a.*, b.m_no, b.m_id, b.level,b.name as m_name";
$db_table = "".GD_GOODS_QNA." a left join ".GD_MEMBER." b on a.m_no=b.m_no";

if ($_GET[cate]){
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];

	if ($category){
		$db_table .= " left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";

		// 상품분류 연결방식 전환 여부에 따른 처리
		$where[]	= getCategoryLinkQuery('c.category', $category, 'where');
	}
}

if ($_GET[skey] && $_GET[sword]){
	if ( $_GET[skey]== 'goodnm' ||  $_GET[skey]== 'all' ){
		$tmp = array();
		$res = $db->query("select goodsno from ".GD_GOODS." where goodsnm like '%$_GET[sword]%'");
		while ($data=$db->fetch($res))$tmp[] = $data[goodsno];
		if ( is_array( $tmp ) && count($tmp) ) $goodnm_where = "a.goodsno in(" . implode( ",", $tmp ) . ")";
		else $goodnm_where = "false";
	}

	if ( $_GET[skey]== 'all' ){
		$where[] = "( concat( subject, contents, ifnull(m_id, ''), ifnull(a.name, '') ) like '%$_GET[sword]%' or $goodnm_where )";
	}
	else if ( $_GET[skey]== 'goodnm' ) $where[] = $goodnm_where;
	else if ( $_GET[skey]== 'm_id' ) $where[] = "concat( ifnull(m_id, ''), ifnull(a.name, '') ) like '%$_GET[sword]%'";
	else $where[] = "$_GET[skey] like '%$_GET[sword]%'";
}

$pg->setQuery($db_table,$where,$sort="notice desc, parent desc, ( case when parent=a.sno then 0 else 1 end ) asc, regdt desc");
$pg->exec();

$res = $db->query($pg->query);
while ($data=$db->fetch($res)){
	if(class_exists('validation') && method_exists('validation','xssCleanArray')){
		$data = validation::xssCleanArray($data, array(
			validation::DEFAULT_KEY => 'text',
			'contents'=>'html',
			'subject'=>'html',
		));
	}

	### 원글 체크
	list($data['parent_m_no'],$data['secret'],$data['type']) = goods_qna_answer($data['sno'],$data['parent'],$data['secret'],$data['notice']);

	### 권한체크
	if(!$cfg['qnaSecret']) $data['secret'] = 0;
	list($data['authmodify'],$data['authdelete'],$data['authview']) = goods_qna_chkAuth($data);

	### 비밀글 아이콘
	$data['secretIcon'] = 0;
	if($data['secret'] == '1') $data['secretIcon'] = 1;

	### 순번처리
	$data['idx'] = $pg->idx--;
	if($data['notice'])	$data['idx'] = "공지";

	### 관리자 아이콘 출력
	list( $level ) = $db->fetch("select level from ".GD_MEMBER." where m_no!='' and m_no='".$data['m_no']."'");
	if ( $level == '100' && $adminicon ) $data['m_id'] = $data['name'] = "<img src='../data/skin/".$cfg['tplSkin']."/".$adminicon."' border=0>";

	### 상품정보
	$query = "select b.goodsnm,b.img_s,c.price
	from
		".GD_GOODS." b
		left join ".GD_GOODS_OPTION." c on b.goodsno=c.goodsno and link and go_is_deleted <> '1' and go_is_display = '1'
	where
		b.goodsno = '" . $data['goodsno'] . "'";
	list( $data['goodsnm'], $data['img_s'], $data['price'] ) = $db->fetch($query);

	$loop[] = $data;
}

$tpl->assign( 'pg', $pg );
$tpl->assign( 'lstcfg', $lstcfg );

### 템플릿 출력
$tpl->print_('tpl');

?>
