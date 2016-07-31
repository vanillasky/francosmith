<?
$noDemoMsg = $indexLog = 1;
include "../_header.php";
include "../lib/page.class.php";

### 변수할당
$goodsno = $_GET[goodsno];
if ( file_exists( dirname(__FILE__) . '/../data/skin/' . $cfg['tplSkin'] . '/admin.gif' ) ) $adminicon = 'admin.gif';
if(!$cfg['reviewWriteAuth']) $cfg['reviewWriteAuth'] = (isset($cfg['reviewAuth_W']) && !$cfg['reviewAuth_W']) ? "free" : ""; // 글쓰기 권한

### 후기 업로드 이미지 갯수 설정
if($cfg['reviewFileNum']){
	$reviewFileNum = $cfg['reviewFileNum'];
} else {
	$reviewFileNum = 1;
}
### 페이지 설정
if(!$cfg['reviewListCnt']) $cfg['reviewListCnt'] = 5;

### 상품 사용기
$pg = new Page($_GET[page],$cfg['reviewListCnt']);
$pg->field = "b.m_no, b.m_id, b.level, a.sno, a.goodsno, a.subject, a.contents, a.point, a.regdt, a.emoney, a.name, b.name as m_name,a.parent,a.attach";
$pg->setQuery($db_table="".GD_GOODS_REVIEW." a left join ".GD_MEMBER." b on a.m_no=b.m_no",$where=array("goodsno='$goodsno'"),$sort="parent desc, ( case when parent=a.sno then 0 else 1 end ) asc,regdt desc");
$pg->exec();
$totcnt = $pg -> recode[total]; //전체 글수

//DB Cache 사용 141030
$usedCache= true;
if ($_GET[page] != null && $_GET[page] >= 2) { //1page만 캐시 사용
	$usedCache= false;
} else {
	$dbCache = Core::loader('dbcache')->setLocation('goodsview_review');
	$loop = $dbCache->getCache($pg->query);
	if (!$loop) $usedCache= false;
}

if ($usedCache === false) {
	$res = $db->query($pg->query);
	while ($data=$db->fetch($res,1)) {

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

		// 삭제 : list( $level ) = $db->fetch("select level from ".GD_MEMBER." where m_no!='' and m_no='{$data[m_no]}'");
		if ( $data[level] == '100' && $adminicon ) $data[m_id] = $data[name] = "<img src='../data/skin/{$cfg['tplSkin'] }/{$adminicon}' border=0>";
		if ( empty($data[m_no]) ) $data[m_id] = $data[name]; // 비회원명

		$data[contents] = nl2br(htmlchars_ech($data[contents]));

		if ($data[attach] == 1) {
			$data_path = "../data/review";
			for($ii=0;$ii<10;$ii++){
				if($ii == 0){
					$name = 'RV'.sprintf("%010s", $data[sno]);
				} else {
					$name = 'RV'.sprintf("%010s", $data[sno]).'_'.$ii;
				}
				if(file_exists($data_path.'/'.$name)){
					$data[image] .= "<img src='".$data_path."/".$name."'><br>";
				}
			}
		}
		else $data[image] = '';


		$data[point] = sprintf( "%0d", $data[point]);

		$loop[] = $data;
	}
	//1page만 캐싱
	if ((!$_GET[page] || $_GET[page] == "1") && $dbCache) {
		$dbCache->setCache($pg->query, $loop);
	}
}

$tpl->assign( 'pg', $pg );
$tpl->assign( 'review_count', $totcnt );
### 템플릿 출력
$tpl->print_('tpl');
?>
