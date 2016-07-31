<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

$id = ($_POST['id']) ? $_POST['id'] : $_GET['id'];
$mode = ($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];

switch ($mode){

	case "register": case "modify":

		$_POST['id'] = str_replace(" ","_",$_POST['id']);

		$El		= array(
				"bdName",			// 게시판 이름
				"bdGroup",			// 그룹
				"bdSkin",			// 스킨
				"bdAlign",			// 테이블 정렬
				"bdWidth",			// 테이블 크기
				"bdStrlen",			// 제목 자르기
				"bdPageNum",		// 한 페이지 글수
				"bdNew",			// 새글 지속 시간
				"bdHot",			// 인기글 조회수
				"bdNoticeList",		// 공지글 설정
				"bdLvlL",			// 권한 (리스트)
				"bdLvlR",			// 권한 (읽기)
				"bdLvlC",			// 권한 (코멘트)
				"bdLvlW",			// 권한 (쓰기)
				"bdLvlP",			// 권한 (답글)
				"bdIp",				// 아이피 출력 유무
				"bdIpAsterisk",		// 아이피 별표 유무
				"bdTypeView",		// 상세 보기 타입
				"bdUseLink",		// 링크 사용	 유무
				"bdUseFile",		// 업로드 사용 유무
				"bdmaxsize_select", // 업로드 최대 파일 사이즈 선택박스
				"bdMaxSize",		// 업로드 최대 파일 사이즈
				"bdTypeMail",		// 메일 타입
				"bdHeader",			// 해더
				"bdFooter",			// 푸터
				"bdUseSubSpeech",	// 말머리 사용 유무
				"bdSubSpeechTitle",	// 말머리 타이틀
				"bdSubSpeech",		// 말머리
				"bdUseComment",		// 코멘트 사용 유무
				"bdSearchMode",		// 검색 모드
				"bdField",			// 히든 필드
				"bdImg",			// 스킨 (이미지 폴더)
				"bdColor",			// 스킨 (색상코드),
				"bdPrnType",		// 리스트출력정보
				"bdListImgCntW",	// 리스트이미지갯수
				"bdListImgCntH",	// 리스트이미지갯수
				"bdListImgSizeW",	// 리스트이미지크기
				"bdListImgSizeH",	// 리스트이미지크기
				"bdListImg",		// 리스트이미지링크
				"bdUserDsp",		// 작성자표시
				"bdAdminDsp",		// 관리자표시
				"bdSpamComment",	// 코멘트 스팸방지
				"bdSpamBoard",		// 게시글 스팸방지
				"bdSecretChk",		// 비밀글 설정
				"bdTitleCChk",		// 제목 글자색 사용
				"bdTitleSChk",		// 제목 글자크기 사용
				"bdTitleBChk",		// 제목 글자굵기 사용
				"bdEmailNo",		// 이메일 작성
				"bdEditorChk",		// 에디터이미지업로드 사용
				"bdHomepageNo",		// 홈페이지 작성
				"bdUseXss",	//부정태그 사용방지
				'bdAllowPluginDomain', //iframe,embed 허용도메인
				'bdAllowPluginTag', //iframe,embed 허용 태그
				"bdUseMobile",	// 모바일사용여부
				);

		$bdMaxSize = str_replace(',', '', $_POST['bdMaxSize']);
		if($bdMaxSize  > str_to_byte(ini_get("upload_max_filesize"))){
			msg('최대 업로드 제한은 '.ini_get("upload_max_filesize").'byte 입니다.',-1);
			exit;
		}
		$_POST['bdSubSpeech']	= str_replace("\r\n","|",$_POST['bdSubSpeech']);
		$_POST['bdField']		= @array_sum($_POST['bdField']);
		$_POST['bdSpamComment']	= @array_sum($_POST['bdSpamComment']);
		$_POST['bdSpamBoard']	= @array_sum($_POST['bdSpamBoard']);
		if(!$_POST['bdEditorChk']) $_POST['bdEditorChk'] = 0;

		if($_POST['bdSkin'] == "gallery"){
			$_POST['bdPageNum']	= $_POST['bdListImgCntW'] * $_POST['bdListImgCntH'];
		}

		if(is_array($_POST['bdAllowPluginDomain'])){
			$_POST['bdAllowPluginDomain'] = implode('|',array_filter($_POST['bdAllowPluginDomain']));
		}
		if(is_array($_POST['bdAllowPluginTag'])){
			$_POST['bdAllowPluginTag'] = implode('|',array_filter($_POST['bdAllowPluginTag']));
		}


		$_POST	= array_map("stripslashes",$_POST);
		$_POST	= array_map("addslashes",$_POST);

		$qfile->open("../../conf/bd_".$_POST['id'].".php");
		$qfile->write("<?\n");
		for ($i=0;$i<count($El);$i++) $qfile->write("\$$El[$i]=\"{$_POST[$El[$i]]}\";\n");
		$qfile->write("?>");
		$qfile->close();
		@chmod("../../conf/bd_".$_POST['id'].".php",0707);

		if ($_POST['mode']=="register"){

			$data = $db->fetch("select * from ".GD_BOARD." where id='".$_POST['id']."'");
			if ($data) msg("이미 ".$_POST['id']." 코드의 게시판이 존재합니다",-1);
			$db->query("insert into ".GD_BOARD." set id='".$_POST['id']."'");

			### 게시판 관련 디비 테이블 생성
			$dir = "../../data/board/$id"; mkdir($dir,0707); chmod($dir,0707);
			$dir = "../../data/board/$id/t"; mkdir($dir,0707); chmod($dir,0707);

			// notice 테이블은 삭제 할 수 없으므로, 동일한 스키마로 복제
			$_query = sprintf('create table %s like %s', GD_BD_.$_POST['id'], 'gd_bd_notice');
			$db->query($_query);

			$db->query("insert into ".GD_BD_.$_POST['id']." (main) values (0)");
		}
		else if ($_POST['mode'] == 'modify') {
			// 말머리 필드속성 교정
			if ($_POST['bdUseSubSpeech'] == 'on') {
				$rowNotice = $db->fetch(sprintf("SHOW FULL COLUMNS FROM `%s` LIKE '%s'", 'gd_bd_notice', 'category'), 1);
				$rowTarget = $db->fetch(sprintf("SHOW FULL COLUMNS FROM `%s` LIKE '%s'", GD_BD_.$_POST['id'], 'category'), 1);
				if ($rowNotice['Type'] != $rowTarget['Type'] && $rowNotice['Type'] != '') {
					$sql = sprintf("ALTER TABLE `%s` CHANGE `category` `category` %s %s", GD_BD_.$_POST['id'], $rowNotice['Type'], ($rowNotice['Null'] == 'NO' ? 'NOT NULL' : 'NULL'));
					$db->query($sql);
				}
			}
		}
		//go("list.php");
		break;

	case "inf":

		$res = $db->query("select idx,count(*) as z from ".GD_BD_.$id." where idx!='' group by idx");
		while ($data=$db->fetch($res)){
			list ($chk) = $db->fetch("select * from ".GD_BOARD_INF." where id='".$id."' and idx='".$data['idx']."'");
			if ($chk) $db->query("update ".GD_BOARD_INF." set num='".$data['z']."' where id='".$id."' and idx='".$data['idx']."'");
			else $db->query("insert into ".GD_BOARD_INF." set num='".$data['z']."', id='".$id."', idx='".$data['idx']."'");
		}
		msg("$id 게시판이 정상적으로 정리되었습니다");
		break;

	case "drop":

		if(trim($id) == "notice"){
			msg('공지사항은 삭제하실 수 없습니다.');
			exit;
		}

		$dir	= "../../data/board/$id";
		$dirSub	= "../../data/board/$id/t";

		if (is_dir($dirSub)){
			$od = opendir($dirSub);
			while ($rd=readdir($od)) if ($rd!="." && $rd!="..") @unlink("$dirSub/$rd");
			closedir($od);
			rmdir($dirSub);
		}

		if (is_dir($dir)){
			$od = opendir($dir);
			while ($rd=readdir($od)) if ($rd!="." && $rd!="..") @unlink("$dir/$rd");
			closedir($od);
			rmdir($dir);
		}

		@unlink("../../conf/bd_$id.php");

		$db->query("drop table ".GD_BD_.$id);
		$db->query("delete from ".GD_BOARD." where id='$id'");

		### 계정용량 계산
		setDu('board');

		msg("$id 게시판이 정상적으로 삭제되었습니다");
		echo "<script>parent.location.reload();</script>";
		break;

	case "adminicon":

		$_BGFILES = array( 'icon_up' => $_FILES['icon_up'] );
		$userori = array( 'icon' => 'admin' . strrChr( $_FILES['icon_up']['name'], "." ) );

		@include_once dirname(__FILE__) . "/../design/webftp/webftp.class_outcall.php";
		outcallUpload( $_BGFILES, '/', $userori );

		msg("관리자 아이콘 설정이 정상적으로 처리되었습니다");
		break;

	case "captcha":

		@include ($path = "../../conf/captcha.php");
		$captcha = (array)$captcha;
		$captcha = @array_map("stripslashes",$captcha);
		$captcha = @array_map("addslashes",$captcha);
		$captcha = array_merge($captcha,(array)$_POST[captcha]);

		$qfile->open($path);
		$qfile->write("<? \n");
		$qfile->write("\$captcha = array( \n");
		foreach ($captcha as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();
		chMod( $path, 0757 );
		break;

	case "list_delete":
		if( !$_POST['chk_no']) {
			msg('삭제할 게시글을 선택하세요.');
			break;
		}

		for($i=0; $i<count($_POST['chk_no']); $i++){
			$tmpArr = explode("|^", $_POST['chk_no'][$i]);

			if($tmpArr[0] != '' && $tmpArr[1] != '') {
				$ret = $db->_select("SELECT main, HEX(sub) AS sub FROM gd_bd_".$tmpArr[0]." WHERE no=".$tmpArr[1]);

				if($ret){
					$ret = $ret[0];
					$query = "DELETE FROM gd_bd_".$tmpArr[0]." WHERE main = ".$ret['main']." AND HEX(sub) LIKE '".$ret['sub']."%'";
					$db->query($query);
				}
			}
		}

		msg("삭제되었습니다.");
		break;
}

go($_SERVER[HTTP_REFERER]);

?>
