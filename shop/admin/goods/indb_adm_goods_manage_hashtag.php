<?php
include '../lib.php';

if (in_array($_POST['hashtagMethod'], array('all_add_goods', 'all_add', 'tag_del')) === false) {
	msg("필수 정보가 누락되었습니다.", -1);
}
if ($_POST['hashtagMethod'] == 'all_add_goods' && !trim($_POST['hashtagName1'])) {
	msg("일괄적으로 등록할 해시태그를 입력해 주세요.", -1);
}
if ($_POST['hashtagMethod'] == 'all_add' && !trim($_POST['hashtagName2'])) {
	msg("일괄적으로 등록할 해시태그를 입력해 주세요.", -1);
}
if ($_POST['hashtagMethod'] == 'tag_del' && !trim($_POST['hashtagName3'])) {
	msg("해시태그를 검색 후 사용하여 주세요.", -1);
}

$hashtag = Core::loader('hashtag');

if(count($_POST['chk']) < 1){
	msg('상품을 선택하여 주세요.', -1);
}

$errorMessage = '';
$errorMessage = $hashtag->indbManageHashtag($_POST, $_POST['chk']);
if($errorMessage !== ''){
	msg($errorMessage, -1);
	break;
}

switch($_POST['hashtagMethod']){
	case 'all_add_goods': case 'all_add':
		$successMessage = '상품에 해시태그가 저장되었습니다.';
		echo '
		<script>
		alert("'.$successMessage.'");
		parent.location.reload();
		</script>
		';
	break;

	case 'tag_del':
		$successMessage = '삭제되었습니다.';
		echo '
		<script>
		alert("'.$successMessage.'");
		parent.location.href = "./adm_goods_manage_hashtag.php";
		</script>
		';
	break;
}
exit;