<?

/**
 * @게시판 환경 설정 변수
 *
 * bdName			게시판 이름
 * bdSkin			스킨
 * bdAlign			테이블 정렬
 * bdWidth			테이블 크기
 * bdUseSubSpeech	말머리 사용 유무
 * bdSubSpeechTitle	말머리 타이틀
 * bdSubSpeech		말머리
 * bdStrlen			제목 자르기
 * bdPageNum		한 페이지 글수
 * bdNew			새글 지속 시간
 * bdHot			인기글 조회수
 * bdNoticeList		공지글 설정
 * bdLvlL			권한 (리스트)
 * bdLvlR			권한 (읽기)
 * bdLvlW			권한 (쓰기)
 * bdIp				아이피 출력 유무
 * bdIpAsterisk		아이피 별표 유무
 * bdTypeView		상세 보기 타입
 * bdUseLink		링크 사용 유무
 * bdUseFile		업로드 사용 유무
 * bdMaxSize		업로드 최대 파일 사이즈
 * bdTypeMail		메일 타입
 * bdHeader			해더
 * bdFooter			푸터
 * bdUseComment		코멘트 사용 유무
 * bdSearchMode		검색 모드
 * bdPrintMode		출력 형식
 * bdField			히든 필드
 * bdImg			스킨 (이미지 폴더)
 * bdColor			스킨 색상코드값
 * bdPrnType		리스트출력정보
 * bdListImgCntW	리스트이미지갯수
 * bdListImgCntH	리스트이미지갯수
 * bdListImgSizeW	리스트이미지크기
 * bdListImgSizeH	리스트이미지크기
 * bdListImg		리스트이미지링크
 * bdUserDsp		작성자표시
 * bdAdminDsp		관리자표시
 * bdSpamComment	코멘트 스팸방지
 * bdSpamBoard		게시글 스팸방지
 * bdSecretChk		비밀글 설정
 * bdTitleCChk		제목 글자색 사용
 * bdTitleSChk		제목 글자크기 사용
 * bdTitleBChk		제목 글자굵기 사용
 * bdEmailNo		이메일 작성
 * bdHomepage		홈페이지 작성
 */

$location = "투데이샵 > 게시판만들기";
include "../_header.php";

if (!$_GET['mode']) $_GET['mode'] = "register";
$returnUrl = ($_GET['returnUrl']) ? $_GET['returnUrl'] : $_SERVER['HTTP_REFERER'];
switch ($_GET['mode']){
	case "register":
		$bdId = "<input type=\"text\" name=\"id\" class=\"line\" required label=\"게시판 ID\" option=\"regAlpha\" />";
		break;
	case "modify":
		include "../../conf/bd_".$_GET['id'].".php";
		$bdId = "<b>$_GET[id]</b><input type=\"hidden\" name=\"id\" value=\"".$_GET['id']."\" />";
		break;
}

if(!$bdPrnType) $bdPrnType = 1;
if(!$bdListImg) $bdListImg = 1;
if(!$bdUserDsp) $bdUserDsp = 0;
if(!$bdAdminDsp) $bdAdminDsp = 0;
if(!$bdSecretChk) $bdSecretChk = 0;
if(!$bdSkin) $bdSkin = "default";
if(!$bdListImgCntW) $bdListImgCntW = 5;
if(!$bdListImgCntH) $bdListImgCntH = 4;
if(!$bdListImgSizeW) $bdListImgSizeW = 100;
if(!$bdListImgSizeH) $bdListImgSizeH = 100;
if( $_GET['mode'] == "register" ){
	if(!$bdSpamComment) $bdSpamComment="3";
	if(!$bdSpamBoard) $bdSpamBoard="3";
}

$selected['bdAlign'][$bdAlign]		= "selected";

$checked['bdPrnType'][$bdPrnType]	= "checked";
$checked['bdListImg'][$bdListImg]	= "checked";
$checked['bdAdminDsp'][$bdAdminDsp]	= "checked";
$checked['bdUserDsp'][$bdUserDsp]	= "checked";
$checked['bdSecretChk'][$bdSecretChk]	= "checked";
if($bdEditorChk!= 0||$bdEditorChk == null) $checked['bdEditorChk']="checked";

$disabled['bdListImg']		= (in_array($bdSkin, array('gallery', 'photo')) ? "" : "disabled");
$disabled['bdIpAsterisk']	= ($bdIp ? "" : "disabled");

if(!$bdWidth) $bdWidth = "95%";
if(!$bdPageNum) $bdPageNum = "20";

$od	= opendir("../../data/skin/".$cfg['tplSkin']."/board");
$i	= 0;
while ($rd=readdir($od)){
	if (!ereg("\.$",$rd))$rdir[]= $rd;
}
asort($rdir);
?>
<script>
var skin = new Array();
<?
$i=0;
foreach($rdir as $v){
	echo "skin[$i] = \"$v\"; \n";
	$i++;
}
?>

function createMenus()
{
	var idx	= 0;
	var tmp	= new Array();
	for (i=0;i<skin.length;i++){
		tmp[i] = "<option value='" + skin[i] + "'>" + skin[i] + "</option>";
		if (skin[i]=="<?=$bdSkin?>") var idx = i;
	}
	SKIN.innerHTML = "<select name=\"bdSkin\" onChange=\"setDisabled(this.value);\">" + tmp.join() + "</select>"; // onChange='setSub(this.value)'
	document.forms[0].bdSkin.options[idx].selected = 1;
	//setSub(document.forms[0].bdSkin.value);
}

function setSub(skin)
{
	exec_script("sub.js.php?time=<?=time()?>&skin=" + skin + "&tplSkin=<?=$cfg[tplSkin]?>&bdImg=<?=$bdImg?>");
}

function setDisabled(skin)
{
	var disabled1	= (inArray(skin, new Array('gallery', 'photo')) ? false : true);
	var disabled2	= (inArray(skin, new Array('gallery')) ? false : true);

	if(disabled1 == true){
		document.getElementById('ListImg').style.display = 'none';
		document.getElementById('ListImgSize').style.display = 'none';
	}else{
		document.getElementById('ListImg').style.display = 'block';
		document.getElementById('ListImgSize').style.display = 'block';
	}
	if(disabled2 == true){
		document.getElementById('ListImgCnt').style.display = 'none';
	}else{
		document.getElementById('ListImgCnt').style.display = 'block';
	}
}

function useSubSpeechChk(){
	if( document.getElementById("UseSubSpeech").checked == true ){
		document.getElementById('subSpeechWrite').style.display = 'block';
	}else{
		document.getElementById('subSpeechWrite').style.display = 'none';
	}
}
</script>

<body onLoad="createMenus();setDisabled('<?=$bdSkin?>');">

<form id="form" method="post" action="indb.board.php" onsubmit="return chkForm(this);">
<input type="hidden" name="mode" value="<?=$_GET['mode']?>" />

<div class="title title_top">기본설정<span>커뮤니티 메뉴에서 서비스하는 게시판을 만듭니다 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=19');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tbody style="height:27">
<tr>
	<td>게시판 ID</td>
	<td><?=$bdId?> <font class="extext">(영문입력 / 다른 게시판 ID와 중복불가)</font></td>
</tr>
<tr>
	<td>게시판 이름</td>
	<td><input type="text" name="bdName" value="<?=$bdName?>" class="line" /> <font class="extext">한글입력</font></td>
</tr>
<tr>
	<td>스킨 선택<br><font class="small" color="6d6d6d">(게시판스타일)</font></td>
	<td>
	<table cellpadding="0" cellspacing="0">
	<tr>
		<td><div style="position:relative;width:80px;" id="SKIN"></div></td>
		<td><div style="position:relative;" id="IMG"></div></td>
		<td style="padding-left:7"><font class="extext">gallery, photo 스킨 사용시 하단에서  '파일업로드' 기능을 꼭 체크하세요</font></td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td>게시판 위치</td>
	<td>
		<select name="bdAlign">
		<option value="center" <?=$selected['bdAlign']['center']?>>가운데정렬
		<option value="left" <?=$selected['bdAlign']['left']?>>왼쪽정렬
		<option value="right" <?=$selected['bdAlign']['right']?>>오른쪽정렬
		</select> <font class="extext">가운데정렬 권장</font>
	</td>
</tr>
<tr>
	<td>게시판 넓이</td>
	<td>
		<input type="text" name="bdWidth" size="6" class="rline" value="<?=$bdWidth?>" /> <font class="extext">% 단위 설정은 꼭 % 를 넣어주세요. 픽셀 단위 설정은 숫자만 입력하세요</font>
	</td>
</tr>
<tr>
	<td>작성자 표시방법</td>
	<td>
		<input type="radio" name="bdUserDsp" value="0" class="null" <?=$checked['bdUserDsp'][0]?> /> 이름표시
		<input type="radio" name="bdUserDsp" value="1" class="null" <?=$checked['bdUserDsp'][1]?> /> 아이디표시
		<input type="radio" name="bdUserDsp" value="2" class="null" <?=$checked['bdUserDsp'][2]?> /> 닉네임표시 <font class="extext">(닉네임이 없는 경우에는 이름이 표시됩니다)</font>
	</td>
</tr>
<tr>
	<td>관리자 표시방법</td>
	<td>
		<input type="radio" name="bdAdminDsp" value="0" class="null" <?=$checked['bdAdminDsp'][0]?> /> 이미지로 표시 <font class="extext">(이미지 등록은 <a href="/shop/admin/board/board_list.php" target="_new"><font class="small1" color="0074ba">게시판리스트</font></a> 에서 등록가능)</font>
		<input type="radio" name="bdAdminDsp" value="1" class="null" <?=$checked['bdAdminDsp'][1]?> /> 위 작성자 표시방법과 동일하게 표시
	</td>
</tr>
<tr>
	<td>말머리 기능</td>
	<td>
		<div class="noline"><input type="checkbox" name="bdUseSubSpeech" id="UseSubSpeech" onclick="useSubSpeechChk();"; <? if ($bdUseSubSpeech=="on") echo"checked" ?> /> 말머리 사용 <font class="extext">(글작성시 제목앞에 특정단어를 넣는 기능입니다)</font></div>
		<div id="subSpeechWrite" style="display:none">
		<table align="left">
		<tr>
			<td>말머리 타이틀</td>
			<td><input type="text" name="bdSubSpeechTitle" size="30" class="line" value="<?=$bdSubSpeechTitle?>" /></td>
			<td></td>
		</tr>
		<tr>
			<td>말머리 입력</td>
			<td><textarea name="bdSubSpeech" style="width:200px" rows=8><?=str_replace("|",chr(10),$bdSubSpeech)?></textarea></td>
			<td>
			<font class="extext">- 여러개의 말머리를 등록할 수 있습니다<br />
			<div style="padding-top:1">- 글작성시 말머리를 선택할 수 있습니다</div>
			<div style="padding-top:1">- 엔터로 구분을 해주세요</div>
			<div style="padding-top:1">- 말머리명을 변경 또는 삭제시 기존게시판에는 적용이 되지 않습니다</font></div>
			</td>
		</tr>
		</table>
		</div>
	</td>
</tr>
</table>

<div class="title">권한설정 및 스팸설정<span>커뮤니티 메뉴에서 서비스하는 게시판을 만듭니다 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=19');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tbody style="height:27">
<tr>
	<td>권한 설정</td>
	<td>
	<table align="left" border=0>
	<tr>
		<td align="center">리스트보기</td>
		<td align="center">글내용보기</td>
		<td align="center">코멘트달기</td>
		<td align="center">글쓰기</td>
	</tr>
	<tr>
		<?
		$r_level = array("L","R","C","W");

		$res2 = $db->query("select * from ".GD_MEMBER_GRP." order by level");
		while ($data=$db->fetch($res2)) $memberGrp[$data['level']] = $data['grpnm'];

		$selected['bdLvlL'][$bdLvlL] = "selected";
		$selected['bdLvlR'][$bdLvlR] = "selected";
		$selected['bdLvlW'][$bdLvlW] = "selected";
		$selected['bdLvlC'][$bdLvlC] = "selected";

		for ($i=0;$i<count($r_level);$i++){
		?>
		<td>
			<select name="bdLvl<?=$r_level[$i]?>">
			<option value=''>제한없음</option>
			<? foreach ($memberGrp as $k => $v){ ?>
			<option value="<?=$k?>" <?=$selected["bdLvl$r_level[$i]"][$k]?> style="background-color:#E9FFE9"><?=$v?> - lv[<?=$k?>]</option>
			<? } ?>
			</select>
		</td>
		<? } ?>
	</tr>
	<tr>
		<td colspan="4">
		<div style="padding:3 0 6 0"><font class=extext><a href="/shop/admin/member/group.php" target="_new"><font class="extext_l">[그룹관리]</font></a> 에서 그룹을 만드세요</div>
	<div>그룹권한시 설정 권한 보다 그룹 레벨이 높은 등급은 전부 권한이 있습니다.</font></div>

		</td>
	</tr>
	</table>




	</td>
</tr>
<tr>
	<td>코멘트 스팸방지</td>
	<td class="noline">
		<input type="checkbox" name="bdSpamComment[]" value="1" <? if ($bdSpamComment&1) echo"checked" ?> /> 외부유입차단 &nbsp; &nbsp; &nbsp;
		<input type="checkbox" name="bdSpamComment[]" value="2" <? if ($bdSpamComment&2) echo"checked" ?> /> 자동등록방지문자

		<table cellpadding="0" cellspacing="0">
		<tr><td style="padding: 5 0 5 3"><font class=extext>이 스팸방지기능은 새로 업그레이드 된 기능입니다. 기능사용 전에 꼭 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=19')"><u>패치안내</u></a>를 읽어보세요</font></td></tr>
		</table>
	</td>
</tr>
<tr>
	<td>게시글 스팸방지</td>
	<td class="noline">
		<input type="checkbox" name="bdSpamBoard[]" value="1" <? if ($bdSpamBoard&1) echo"checked" ?> /> 외부유입차단 &nbsp; &nbsp; &nbsp;
		<input type="checkbox" name="bdSpamBoard[]" value="2" <? if ($bdSpamBoard&2) echo"checked" ?> /> 자동등록방지문자 <font class="extext"><a href="javascript:popupLayer('../board/popup.captcha.php')"><font class="extext_l">[이미지설정]</font></a>

		<table cellpadding="0" cellspacing="0">
		<tr><td style="padding: 5 0 5 3">
		<font class="extext">스팸방지에 대해 자세히 숙지하시려면 <a href="http://www.godo.co.kr/edu/edu_board_list.html?cate=adminen&in_view=y&sno=408#Go_view" target=_blank><font class="extext_l">[교육자료]</font></a> 를 확인하세요</font></font><br>
		<div style="padding-top:3"><font class=extext>이 스팸방지기능은 새로 업그레이드 된 기능입니다. 기능사용 전에 꼭 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=19')"><font class="extext_l">[패치안내]</font></a> 를 읽어보세요</font></div></td></tr>
		</table>
	</td>
</tr>
</table>

<div class="title">리스트화면설정<span>커뮤니티 메뉴에서 서비스하는 게시판을 만듭니다 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=19');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tbody style="height:27">
<tr>
	<td>제목글수 제한</td>
	<td>
		<input type="text" name="bdStrlen" size="6" class="rline" value="<?=$bdStrlen?>" onkeydown="onlynumber();" /> 글자수 제한
		&nbsp;<font class="extext">(미작성시 제한 없음, 제한 글자수 이상의 경우는 이후 글씨는 보이지 않음)</font>
	</td>
</tr>
<tr>
	<td>페이지당 게시물수</td>
	<td>
		<input type="text" name="bdPageNum" size="6" class="rline" value="<?=$bdPageNum?>" onkeydown="onlynumber();" /> 개
		&nbsp;<font class="extext">(기본 20개 출력, gallery 스킨의 경우는 아래 이미지 갯수에서 설정 사용)</font>
	</td>
</tr>
<tr>
	<td>NEW아이콘 효력</td>
	<td>
		<input type="text" name="bdNew" size="6" class="rline" value="<?=$bdNew?>" onkeydown="onlynumber();" /> 시간
		&nbsp;<font class="extext">(미작성시 사용안함)</font>
	</td>
</tr>
<tr>
	<td>HOT아이콘 조건</td>
	<td>
		조회수 <input type="text" name="bdHot" class="rline" size="6" value="<?=$bdHot?>" onkeydown="onlynumber();" /> 회 이상 게시글
		&nbsp;<font class="extext">(미작성시 사용안함)</font>
	</td>
</tr>
<tr>
	<td>공지글 출력 설정</td>
	<td class="noline">
		<input type="radio" name="bdNoticeList" value="" <? if (!$bdNoticeList) echo "checked" ?> /> 1페이지에만 출력
		<input type="radio" name="bdNoticeList" value="o" <? if ($bdNoticeList) echo "checked" ?> /> 모든페이지 출력
	</td>
</tr>
<tr>
	<td>항목감추기</td>
	<td class="noline">

	<input type="checkbox" name="bdField[]" value="1" <? if ($bdField&1) echo"checked" ?> /> 체크
	<input type="checkbox" name="bdField[]" value="2" <? if ($bdField&2) echo"checked" ?> /> 번호
	<input type="checkbox" name="bdField[]" value="4" <? if ($bdField&4) echo"checked" ?> /> 제목
	<input type="checkbox" name="bdField[]" value="8" <? if ($bdField&8) echo"checked" ?> /> 이름
	<input type="checkbox" name="bdField[]" value="16" <? if ($bdField&16) echo"checked" ?> /> 날짜
	<input type="checkbox" name="bdField[]" value="32" <? if ($bdField&32) echo"checked" ?> /> 조회수

	</td>
</tr>
<tr>
	<td>검색 모드</td>
	<td class="noline">
		<input type="radio" name="bdSearchMode" value="0" <? if (!$bdSearchMode) echo "checked" ?> /> 일반 검색 (검색시 풀스캔)
		<input type="radio" name="bdSearchMode" value="1" <? if ($bdSearchMode) echo "checked" ?> /> 권장 검색 (검색시 부하를 줄이기 위해 페이징 제한)
	</td>
</tr>
<tr>
	<td>리스트<br>
	출력정보 선택<br><font class="small" color="6d6d6d">(board_list.php)</font></td>
	<td class="noline">
		<div><input type="radio" name="bdPrnType" value="1" <?=$checked['bdPrnType'][1] ?> /> 기본정보 (제목,작성일,조회수,코멘트수,업로드파일1개)&nbsp;<font class="extext">(default, gallery, photo 스킨은 기본정보로 선택하세요)</font></div>
		<div><input type="radio" name="bdPrnType" value="2" <?=$checked['bdPrnType'][2] ?> /> 상세정보 (기본정보를 포함한 리스트출력에 필요한 모든 데이타)&nbsp;<font class="extext">(webzine 스킨은 상세정보로 선택하세요)</font></div>
	</td>
</tr>
<tr id="ListImgCnt">
	<td>썸네일 이미지 갯수</td>
	<td>
		<input type="text" name="bdListImgCntW" size="6" class="rline" value="<?=$bdListImgCntW?>" onkeydown="onlynumber();" /> X
		<input type="text" name="bdListImgCntH" size="6" class="rline" value="<?=$bdListImgCntH?>" onkeydown="onlynumber();" />
		&nbsp;<font class="extext">(gallery 스킨만 사용. 갤러리타입 게시판 리스트에 나오는 썸네일이미지 갯수)</font>
	</td>
</tr>
<tr id="ListImgSize">
	<td>썸네일 이미지 크기</td>
	<td>
		<input type="text" name="bdListImgSizeW" id="ListImgSizeW" size="6" class="rline" value="<?=$bdListImgSizeW?>" onkeydown="onlynumber();" /> Pixel X
		<input type="text" name="bdListImgSizeH" id="ListImgSizeH" size="6" class="rline" value="<?=$bdListImgSizeH?>" onkeydown="onlynumber();" /> Pixel
		&nbsp;<font class="extext">(gallery, photo 스킨만 사용)</font>
	</td>
</tr>
<tr id="ListImg">
	<td>이미지 클릭설정</td>
	<td class="noline">
		<input type="radio" name="bdListImg" value="1" <?=$checked['bdListImg'][1] ?> <?=$disabled['bdListImg'] ?> /> 이미지 클릭시 팝업창이 뜹니다&nbsp;
		<input type="radio" name="bdListImg" value="2" <?=$checked['bdListImg'][2] ?> <?=$disabled['bdListImg'] ?> /> 이미지 클릭시 글내용으로 이동&nbsp;
		&nbsp;<font class="extext">(gallery, photo 스킨만 사용)</font> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=19');"><u><font class="small1" color="0074ba"><b>[<u>예제화면보기</u>]</b></a>
	</td>
</tr>
</table>

<div class="title">상세화면설정<span>커뮤니티 메뉴에서 서비스하는 게시판을 만듭니다 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=19');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tbody style="height:27">
<tr>
	<td>View 타입</td>
	<td class="noline">
		<?
		$r_tmp = array("내용만","관련글","리스트");
		for ($i=0;$i<count($r_tmp);$i++){
			$chk = ($bdTypeView==$i) ? "checked" : "";
			echo "<input type=\"radio\" name=\"bdTypeView\" value=\"$i\" $chk class=\"noline\" /> $r_tmp[$i] ";
		}
		?>
	</td>
</tr>
<tr>
	<td>IP 출력</td>
	<td class="noline">
		<input type="checkbox" name="bdIp" <? if ($bdIp=="on") echo"checked" ?> onclick="this.form['bdIpAsterisk'].disabled = !this.checked" /> 글쓴이의 IP를 보여줍니다
		<div style="padding: 2px 0 3px 0"><input type="checkbox" name="bdIpAsterisk" <? if ($bdIpAsterisk=="on") echo"checked" ?> <?=$disabled['bdIpAsterisk'] ?> /> IP 끝자리 암호화표기 <font class=extext>예)</font> <font class="ver71" color="#627dce">123.213.139.***</font></div>
	</td>
</tr>
<tr>
	<td>링크/업로드</td>
	<td class="noline">
		<input type="checkbox" name="bdUseLink" <? if ($bdUseLink=="on") echo"checked" ?> /> 링크 &nbsp; &nbsp; &nbsp;
		<input type="checkbox" name="bdUseFile" <? if ($bdUseFile=="on") echo"checked" ?> /> 파일업로드 <font class="extext">(Gallery, Photo 스킨 사용시 꼭 체크하세요!)</font>
	</td>
</tr>
<tr>
	<td>코멘트(댓글)기능</td>
	<td class="noline"><input type="checkbox" name="bdUseComment" <? if ($bdUseComment=="on") echo"checked" ?> /> 사용</td>
</tr>
</table>

<div class="title">작성화면설정<span>커뮤니티 메뉴에서 서비스하는 게시판을 만듭니다 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=19');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tbody style="height:27">
<tr>
	<td>비밀글 설정</td>
	<td>
		<input type="radio" name="bdSecretChk" value="0" class="null" <?=$checked['bdSecretChk'][0]?> /> 작성시 기본 일반글
		<input type="radio" name="bdSecretChk" value="1" class="null" <?=$checked['bdSecretChk'][1]?> /> 작성시 기본 비밀글
		<input type="radio" name="bdSecretChk" value="2" class="null" <?=$checked['bdSecretChk'][2]?> /> 무조건 일반글
		<input type="radio" name="bdSecretChk" value="3" class="null" <?=$checked['bdSecretChk'][3]?> /> 무조건 비밀글
	</td>
</tr>
<tr>
	<td>제목작성 설정</td>
	<td>
		<input type="checkbox" name="bdTitleCChk" class="null" <? if ($bdTitleCChk=="on") echo"checked" ?> /> 글자색 사용
		<input type="checkbox" name="bdTitleSChk" class="null" <? if ($bdTitleSChk=="on") echo"checked" ?> /> 글자크기 사용
		<input type="checkbox" name="bdTitleBChk" class="null" <? if ($bdTitleBChk=="on") echo"checked" ?> /> 글자굵기 사용
	</td>
</tr>
<tr>
	<td>에디터 업로드 기능</td>
	<td><input type="checkbox" name="bdEditorChk" class="null" value="1" <?=$checked['bdEditorChk']?> /> 에디터 업로드 사용</td>
</tr>
<tr>
	<td>업로드파일 Size</td>
	<td>
		<input type="text" name="bdMaxSize" size="6" class="rline" value="<?=$bdMaxSize?>" onkeydown="onlynumber();" /> Byte
		<font class="extext">(파일 업로드시 파일크기를 제한합니다.)</font></font>
	</td>
</tr>
<tr>
	<td>이메일 작성</td>
	<td>
		<input type="checkbox" name="bdEmailNo" class="null" <? if ($bdEmailNo=="on") echo"checked" ?> /> 이메일 작성 미사용
	</td>
</tr>
<tr>
	<td>홈페이지 작성</td>
	<td>
		<input type="checkbox" name="bdHomepageNo" class="null" <? if ($bdHomepageNo=="on") echo"checked" ?> /> 홈페이지 작성 미사용
	</td>
</tr>

<!--
<tr>
	<td>메일환경설정</td>
	<td class="noline">
		<input type="radio" name="bdTypeMail" value="0" <? if (!$bdTypeMail) echo "checked" ?> /> Outlook
		<input type="radio" name="bdTypeMail" value="1" <? if ($bdTypeMail) echo "checked" ?> /> 내장 메일링
	</td>
</tr>
-->

<!--
<tr>
	<td>스킨 색상값</td>
	<td>
		<div style="padding:5">진한색 ↔ 연한색 (줄바꿈으로 구분)</div>
		<textarea name="bdColor" style="width:100%" rows="5"><?=$bdColor?></textarea>
	</td>
</tr>
-->
</table>

<div class="title">HTML설정<span>커뮤니티 메뉴에서 서비스하는 게시판을 만듭니다 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=19');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tbody style="height:27">
<tr>
	<td>상단디자인<br>(Header)</td>
	<td>
		<textarea name="bdHeader" style="width:100%" rows=8 class=tline><?=stripslashes($bdHeader)?></textarea>
	</td>
</tr>
<tr>
	<td>하단디자인<br>(Footer)</td>
	<td>
		<textarea name="bdFooter" style="width:100%" rows=8 class=tline><?=stripslashes($bdFooter)?></textarea>
	</td>
</tr>
</table>


<div style="padding:20px" align="center" class="noline">
<div class="button">
<input type="image" src="../img/btn_<?=$_GET['mode']?>.gif" />
<a href="board_list.php"><img src="../img/btn_list.gif" /></a>
</div><div>



</form>

<script>useSubSpeechChk();</script>

<? include "../_footer.php"; ?>