<?
	include "../_header.popup.php";
	@include "../../conf/bd_".$_GET['inc'].".php";

	if(!$_GET["inc"]) msg("게시판 정보가 없습니다.", "close");

	$targetData = $db->fetch("SELECT * FROM gd_bd_".$_GET['inc']." WHERE no = ".$_GET['no']); // 원본글 정보

	// 원본글 첨부파일
	if($targetData['old_file']) {
		$ar_tmp = explode("|", $targetData['old_file']);
		for($i = 0, $imax = count($ar_tmp); $i < $imax; $i++) {
			if($attachList) $attachList .= "<span style=\"margin:0px 5px; color:#CCCCCC;\">|</span>";
			$attachList .= "<a href=\"../../board/download.php?id=".$_GET['inc']."&no=".$_GET['no']."&div=".$i."\">".$ar_tmp[$i]."</a>";
		}
	}

	// 답변자 정보
	list($memName) = $db->fetch("SELECT name FROM ".GD_MEMBER." WHERE m_no = '".$sess['m_no']."'");
?>
<script src="../../lib/js/board.js"></script>
<script type="text/javascript">
function add() {
	var table = document.getElementById('table');
	if(table.rows.length > 11) {
		alert("다중 업로드는 최대 12개만 지원합니다");
		return;
	}
	date	= new Date();
	oTr		= table.insertRow( table.rows.length );
	oTr.id	= date.getTime();
	oTr.insertCell(0);
	oTd		= oTr.insertCell(1);
	tmpHTML = "<input type=file name='file[]' style='width:80%' class=line onChange='preview(this.value," + oTr.id +")'> <a href='javascript:del(" + oTr.id + ")'><img src='../img/btn_upload_minus.gif' align=absmiddle></a>";
	oTd.innerHTML = tmpHTML;
	oTd = oTr.insertCell(2);
	oTd.id = "prvImg" + oTr.id;
	calcul();
}

function reloadwindow(value) {
	location.href = "admin_register.php?<?=$_SERVER['QUERY_STRING']?>&inc="+value;
}

function htmlspecialchars (string) {
 return string.replace(/&/g, "&amp;").replace(/\"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function encodeContent(form) {
	form.subject.value = htmlspecialchars(form.subject.value);
	form.contents.value = htmlspecialchars(form.contents.value);
	return chkForm(form);
}

</script>
<style type="text/css">
	#targetContents p { margin:2px 0; } /* 원본글 내용에 적용 */
	.popTb { width:100%; background:#E6E6E6; }
	.popTb .fd { color:#333333; background:#F6F6F6; font:9pt tahoma; text-align:left; font-weight:bold; letter-spacing:-1; }
	.popTb .val { background:#FFFFFF; }
</style>
<form name="form" method="post" action="<?=$sitelink->link('admin/board/list_management_indb.php','ssl');?>" onsubmit="return encodeContent(this)" enctype="multipart/form-data" >
<input type="hidden" name="mode" value="<?=$_GET["mode"]?>">
<input type="hidden" name="id" value="<?=$_GET['inc']?>">
<input type="hidden" name="no" value="<?=$_GET['no']?>">
<input type="hidden" name="params" value="<?=$_SERVER['QUERY_STRING']?>">

<!-- 원본 글 정보 시작 -->
<div class="title title_top"><?=$bdName?></div>
<table cellpadding="5" cellspacing="1" border="0" class="popTb">
<col class="fd"><col class="val"><col class="fd"><col class="val">
<tr>
	<td width="100">제목</td>
	<td width="503"><?=$targetData['subject']?></td>
	<td width="40">이름</td>
	<td width="120"><?=$targetData['name']?></td>
</tr>
<tr>
	<td>작성일</td>
	<td colspan="3"><?=$targetData['regdt']?> <?=$targetData['ip']?></td>
</tr>
<tr>
	<td>내용</td>
	<td colspan="3"><div style="border:1px #CCCCCC solid; padding:5px;"><div id="targetContents" style="width:100%; height:150px; overflow-y:scroll; word-wrap:break-word; word-break:break-all; "><?=$targetData['contents']?></div></div></td>
</tr>
<? if($attachList) { ?>
<tr>
	<td>첨부파일</td>
	<td colspan="3"><?=$attachList?></td>
</tr>
<? } ?>
</table>
<!-- 원본 글 정보 맺음 -->

<!-- 답변 폼 시작 -->
<div class="title title_top">게시글 답변</div>
<table cellpadding="5" cellspacing="1" border="0" class="popTb">
<col class="fd"><col class="val"><col class="fd"><col class="val">
<tr>
	<td width="100">제목</td>
	<td width="503"><input type="text" name="subject" value="<?=$targetData['subject']?>" style="width:90%;" class="line" required fld_esssential></td>
	<td width="40">작성자</td>
	<td width="120">
		<input type="hidden" name="m_no" value="<?=$sess["m_no"]?>">
		<input type="text" name="name" value="<?=$memName?>" style="width:100px;" class="line" required fld_esssential>
	</td>
</tr>
<? if($bdTitleCChk == "on" || $bdTitleSChk == "on" || $bdTitleBChk == "on") { ?>
<tr>
	<td>제목효과</td>
	<td colspan="3">
<?
if(isset($bdTitleCChk) && $bdTitleCChk == "on") {
?>
		<select name="titleStyle[C]" id="titleStyle[C]" class="box">
			<option value="">제목 글자색</option>
			<option value="#000000" style="color:#000000" <?=$selected["titleC"]["#000000"]?>>검정</option>
			<option value="#7F7F7F" style="color:#7F7F7F" <?=$selected["titleC"]["#7F7F7F"]?>>회색</option>
			<option value="#FFA300" style="color:#FFA300" <?=$selected["titleC"]["#FFA300"]?>>노랑</option>
			<option value="#FF600F" style="color:#FF600F" <?=$selected["titleC"]["#FF600F"]?>>주황</option>
			<option value="#ff0000" style="color:#ff0000" <?=$selected["titleC"]["#ff0000"]?>>빨강</option>
			<option value="#A03F00" style="color:#A03F00" <?=$selected["titleC"]["#A03F00"]?>>갈색</option>
			<option value="#FF08A0" style="color:#FF08A0" <?=$selected["titleC"]["#FF08A0"]?>>분홍</option>
			<option value="#5000AF" style="color:#5000AF" <?=$selected["titleC"]["#5000AF"]?>>보라</option>
			<option value="#B0008F" style="color:#B0008F" <?=$selected["titleC"]["#B0008F"]?>>자주</option>
			<option value="#7FC700" style="color:#7FC700" <?=$selected["titleC"]["#7FC700"]?>>연두</option>
			<option value="#009FAF" style="color:#009FAF" <?=$selected["titleC"]["#009FAF"]?>>청녹</option>
			<option value="#0000ff" style="color:#0000ff" <?=$selected["titleC"]["#0000ff"]?>>파랑</option>
		</select>
<?
}
if(isset($bdTitleSChk) && $bdTitleSChk == "on") {
?>
		<select name="titleStyle[S]" id="titleStyle[S]" class="box">
			<option value="">제목 글자크기</option>
			<option value="8px" <?=$selected["titleS"]["8px"]?>>아주작게 [8px]</option>
			<option value="10px" <?=$selected["titleS"]["10px"]?>>작게 [10px]</option>
			<option value="12px" <?=$selected["titleS"]["12px"]?>>보통 [12px]</option>
			<option value="18px" <?=$selected["titleS"]["18px"]?>>크게 [18px]</option>
			<option value="24px" <?=$selected["titleS"]["24px"]?>>아주 크게 [24px]</option>
		</select>
<?
}
if(isset($bdTitleBChk) && $bdTitleBChk == "on") {
?>
		<select name="titleStyle[B]" id="titleStyle[B]" class="box">
			<option value="">제목 글자굵기</option>
			<option value="default" <?=$selected["titleB"]["default"]?>>보통</option>
			<option value="bold" <?=$selected["titleB"]["bold"]?>>굵게</option>
		</select>
<?
}
?>
		</div>
	</td>
</tr>
<?
}
if($bdSecretChk != '2') {
?>
<tr>
	<td>비밀글</td>
	<td colspan="3"><label><input type="checkbox" style="border:0" name="secret" <?=($targetData['secret']) ? "checked" : ""?>>비밀글</label></td>
</tr>
<? } ?>
<tr>
	<td>내용</td>
	<td colspan="3">
		<div style="width:100%; height:<?=($_GET['mode'] == "reply") ? "185" : "355"?>px;position:relative;z-index:99">
		<textarea name="contents" style="width:100%;height:<?=($_GET['mode'] == "reply") ? "180" : "350"?>px" type="editor" fld_esssential label="내용"><?=$data["contents"]?></textarea>
		<script src="../../lib/meditor/mini_editor.js"></script>
		<script>mini_editor("../../lib/meditor/",false)</script>
		</div>
	</td>
</tr>
<? if($bdUseFile == "on") { ?>
<tr>
	<td>업로드</td>
	<td colspan="3">
		<table width="100%" id="table" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:2px">
		<col class="engb" align="center">
		<? if(count(file) < 12) { ?>
		<tr>
			<td width="20" nowrap>1</td>
			<td width="100%">
				<input type=file name="file[]" style="width:80%" class="linebg" onChange="preview(this.value,0)">
				<a href="javascript:add()"><img src="../img/btn_upload_plus.gif" align="absmiddle" /></a>
			</td>
			<td id="prvImg0"></td>
		</tr>
		<? } ?>
		</table>
		<div width="100%" style="padding:5;" class="stxt">
			- 파일은 최대 12개까지 다중업로드가 지원됩니다<br>
			- Source창에서 오른쪽 이미지를 클릭하면 이미지치환코드가 입력됩니다
			<? if($bdMaxSize) { ?><br />- 파일 업로드 최대 사이즈는 <?=byte2str($bdMaxSize) ?>입니다<? } ?>
		</div>
	</td>
</tr>
<? } ?>
</table>
<!-- 답변 폼 맺음 -->

<div class="button_popup"><input type="image" src="../img/btn_confirm_s.gif" align="absmiddle" style="margin-right:3px;" /><a href="javascript:self.close()"><img src="../img/btn_cancel_s.gif" align="absmiddle" /></a></div>

</form>
<script>table_design_load();</script>