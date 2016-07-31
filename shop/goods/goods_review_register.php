<?

include "../_header.php";

### 접근체크
if ($_GET['mode'] == "add_review" && $cfg['reviewAuth_W'] && $cfg['reviewAuth_W'] > $sess['level']) msg("이용후기 작성 권한이 없습니다", "close");
if ($_GET['mode'] == "reply_review" && $cfg['reviewAuth_P'] && $cfg['reviewAuth_P'] > $sess['level']) msg("이용후기 답변 권한이 없습니다", "close");

### 변수할당
$mode		= $_GET[mode];
$goodsno	= $_GET[goodsno];
$sno		= $_GET[sno];
$referer	= $_GET[referer];

### 후기 업로드 이미지 갯수 설정
if($cfg['reviewFileNum']){
	$reviewFileNum = $cfg['reviewFileNum'];
} else {
	$reviewFileNum = 1;
}
### 상품 데이타
$query = "
select
	goodsnm,img_s,price
from
	".GD_GOODS." a
	left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and go_is_deleted <> '1' and go_is_display = '1'
where
	a.goodsno='$goodsno'
";
$goods = $db->fetch($query,1);

if(class_exists('validation') && method_exists('validation','xssCleanArray')){
	$goods = validation::xssCleanArray($goods, array(
		validation::DEFAULT_KEY => 'html',
		'price'=>'text',
	));
}

### 회원정보
if($mode != 'mod_review' && $sess['m_no']){
	list($data['name'],$data['nickname']) = $db-> fetch("select name,nickname from ".GD_MEMBER." where m_no='".$sess['m_no']."' limit 1");
	if($data['nickname'])$data['name'] = $data['nickname'];
} //end if

### 상품 사용기
if($mode == 'mod_review'){
	$query = "select a.sno, b.m_no, b.m_id, a.subject, a.contents, a.point, a.name, a.attach from ".GD_GOODS_REVIEW." a left join ".GD_MEMBER." b on a.m_no=b.m_no where a.sno='$sno'";
	$data = $db->fetch($query,1);
	
	// @qnibus 2015-06 회원아이디와 게시글 작성자 일치여부 확인
	if($sess['m_no'] && $sess[level] < 80 && $sess['m_no'] != $data['m_no']) {
		msg('본인이 작성한 상품후기만 수정하실 수 있습니다.', 'close');
	}

	$data['point'] = array( $data['point'] => 'checked' );

	$file_arr = '';
	$data[image] = '';
	if ($data[attach] == 1) {
		if($cfg['reviewFileNum'] > 0){
			$upload_folder = "../data/review/";
			// 파일 존재 가능 최대 수 만큼(관리자 최대 10개까지 가능)
			for ($i=0; $i<10; $i++){
				if($i == 0){
					$upload_file = 'RV'.sprintf("%010s", $data[sno]);
				} else {
					$upload_file = 'RV'.sprintf("%010s", $data[sno]).'_'.$i;
				}
				if(file_exists($upload_folder.$upload_file)){
					$file_arr[$i] = "<input type=\"hidden\" name=\"file_ori[]\" value=\"$i\" /><input type=\"checkbox\" name=\"del_file[$i]\" value=\"on\" class=linebg /> 삭제<img src='".$upload_folder.$upload_file."' width='40px' height='40px' align=absmiddle>";
				}
			}
			// 배열의 중간 빈값 채우기
			$max_arr = end(array_keys($file_arr));
			for($mi=0;$mi<=$max_arr;$mi++){
				if(!$file_arr[$mi]){
					$file_arr[$mi] = "";
				}
			}
			// 배열 키 정리
			ksort($file_arr);
		} else {
			$data[image] = '<img src="../data/review/'.'RV'.sprintf("%010s", $data[sno]).'" width="20" style="border:1 solid #cccccc" onclick=popupImg("../data/review/'.'RV'.sprintf("%010s", $data[sno]).'","../") class=hand>';
		}
	}
}
else {
	$data['m_id'] = $sess['m_id'];
}

// 받은 데이터 처리
$data['subject'] = ($_POST['subject']) ? $_POST['subject'] : $data['subject'];
$data['contents'] = ($_POST['contents']) ? $_POST['contents'] : $data['contents'];
if($_POST['point']) $data['point'] = array( $_POST['point'] => 'checked' );

if(class_exists('validation') && method_exists('validation','xssCleanArray')){
	$data = validation::xssCleanArray($data, array(
		validation::DEFAULT_KEY => 'text',
		'subject' => 'html',
		'contents' => 'html',
	));
}

### 템플릿 출력
$tpl->print_('tpl');

?>