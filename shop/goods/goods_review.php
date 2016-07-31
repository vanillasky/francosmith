<?

include "../_header.php";
include "../lib/page.class.php";

if(class_exists('validation') && method_exists('validation','xssCleanArray')){
	$_GET = validation::xssCleanArray($_GET, array(
		validation::DEFAULT_KEY => 'text',
	));
}

### 리스트 템플릿 기본 환경변수
$lstcfg[size]	= 50;
$lstcfg['page_num'] = array(10, 20, 30, 40, 50);
if(!is_numeric($cfg['reviewAllListSet']) || !$cfg['reviewAllListSet']) $cfg['reviewAllListSet'] = $lstcfg['page_num'][0];
$lstcfg[sort] = array( 1 => '최근등록순', 2 => '평점높은순' );

### 변수할당
if (!$_GET[page_num]) $_GET[page_num] = $cfg['reviewAllListSet'];
$selected[page_num][$_GET[page_num]] = "selected";
if (!$_GET[sort]) $_GET[sort] = 1;
$selected[sort][$_GET[sort]] = "selected";
$selected[skey][$_GET[skey]] = "selected";
if ( file_exists( dirname(__FILE__) . '/../data/skin/' . $cfg['tplSkin'] . '/admin.gif' ) ) $adminicon = 'admin.gif';

### 후기 업로드 이미지 갯수 설정
if($cfg['reviewFileNum']){
	$reviewFileNum = $cfg['reviewFileNum'];
} else {
	$reviewFileNum = 1;
}

### 상품 사용기
$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "distinct a.sno, a.goodsno, a.subject, a.contents, a.point, a.regdt, a.emoney, a.name, b.m_no, b.m_id,b.name as m_name,a.parent,a.attach, a.notice";
$db_table = "".GD_GOODS_REVIEW." a left join ".GD_MEMBER." b on a.m_no=b.m_no";

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

switch ($_GET[sort]){

	case "1":
		$sort = "notice desc, parent desc, ( case when parent=a.sno then 0 else 1 end ) asc, regdt desc";
		break;
	case "2":
		$sort = "notice desc, point desc";
		break;
}

$pg->setQuery($db_table,$where,$sort);
$pg->exec();

$res = $db->query($pg->query);
while ($data=$db->fetch($res)){

	if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
		$data = validation::xssCleanArray($data, array(
		    validation::DEFAULT_KEY => 'text',
		    'contents' => array('html', 'ent_noquotes'),
			'subject' => array('html', 'ent_noquotes'),
		));
	}

	$data['idx'] = $pg->idx--;

	$data[type] = ( $data[sno] == $data[parent] ? 'Q' : 'A' );

	$data[authmodify] = $data[authdelete] = $data[authreply] = 'Y'; # 권한초기값

	if ( empty($cfg['reviewWriteAuth']) || isset($sess) || !empty($data[m_no]) ){ // 회원전용 or 회원 or 작성자==회원
		$data[authmodify] = ( isset($sess) && $sess[m_no] == $data[m_no] ? 'Y' : 'N' );
		$data[authdelete] = ( isset($sess) && $sess[m_no] == $data[m_no] ? 'Y' : 'N' );
	}

	list( $data[replecnt] ) = $db->fetch("select count(*) from ".GD_GOODS_REVIEW." where sno != parent and parent='$data[sno]'");
	$data[authdelete] = ( $data[replecnt] > 0 ? 'N' : $data[authdelete] ); # 답글 있는 경우 삭제 불가

	if ( $data[sno] == $data[parent] ){

		if ( empty($cfg['reviewWriteAuth']) ){ // 회원전용
			$data[authreply] = ( isset($sess) ? 'Y' : 'N' );
		}
	}else $data[authreply] = 'N';

	list( $level ) = $db->fetch("select level from ".GD_MEMBER." where m_no!='' and m_no='{$data[m_no]}'");
	if ( $level == '100' && $adminicon ) $data[m_id] = $data[name] = "<img src='../data/skin/{$cfg['tplSkin'] }/{$adminicon}' border=0>";
	if ( empty($data[m_no]) ) $data[m_id] = $data[name]; // 비회원명

	$data[contents] = nl2br(htmlchars_ech($data[contents]));
	if ($data[attach] == 1) {
		$upload_folder = "../data/review/";
		$tmp_img = '';
		for ($i=0; $i<10; $i++){
			if($i == 0){
				$upload_file = 'RV'.sprintf("%010s", $data[sno]);
			} else {
				$upload_file = 'RV'.sprintf("%010s", $data[sno]).'_'.$i;
			}
			if(file_exists($upload_folder.$upload_file)){
				$tmp_img .= "<img src='".$upload_folder.$upload_file."' name='rv_attach_image[]' border='0'><br>";
			}
		}
		if($tmp_img){
			$data[contents] = $tmp_img."<br>".$data[contents];
			$data[subject] = $data[subject].' <img src="../data/skin/'.$cfg[tplSkin].'/img/disk_icon.gif" border=0>';
		}
	}
	$data[point] = sprintf( "%0d", $data[point]);

	$query = "select b.goodsnm,b.img_s,c.price
	from
		".GD_GOODS." b
		left join ".GD_GOODS_OPTION." c on b.goodsno=c.goodsno and link and go_is_deleted <> '1' and go_is_display = '1'
	where
		b.goodsno = '" . $data[goodsno] . "'";
	list( $data[goodsnm], $data[img_s], $data[price] ) = $db->fetch($query);

	$loop[] = $data;
}

$tpl->assign( 'pg', $pg );
$tpl->assign( 'lstcfg', $lstcfg );

### 템플릿 출력
$tpl->print_('tpl');

?>