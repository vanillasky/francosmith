<?

include "../_header.popup.php";

if($_GET["inc"]) {
	@include "../../conf/bd_".$_GET["inc"].".php";
	$selected["id"][$_GET["inc"]] = "selected='selected'";
}

if ($_GET[mode]=="modify") {
	$data = $db->fetch("select * from gd_bd_".$_GET['inc']." where no='" . $_GET['no'] . "'",1);
	if($data["titleStyle"] && $_GET["mode"] == "modify") {
		$title = explode("|", $data["titleStyle"]);
		foreach($title as $val) {
			$tmp_title	= explode(":", $val);
			switch($tmp_title[0]) {
				case "^C": $selected["titleC"][$tmp_title[1]] = "selected='selected'"; break;
				case "^S": $selected["titleS"][$tmp_title[1]] = "selected='selected'"; break;
				case "^B": $selected["titleB"][$tmp_title[1]] = "selected='selected'"; break;
			}
		}
	}
	if($data["notice"]) $checked["notice"] = "checked='checked'";
	if($data["secret"]) $checked["secret"] = "checked='checked'";
	if($data['old_file']) {
		$div = explode("|",$data['old_file']);
		for ($tmp='',$i=0; $i < count($div); $i++) {
			$tmp .= "
			<tr id=".($i+1).">
				<td valign=\"top\" style=\"padding-top:3\">".($i+1)."</td>
				<td class=\"eng\">
				<input type=\"file\" name=\"file[]\" style=\"width:90%\" class=\"line\" onChange=\"preview(this.value,".($i+1).");\" /><br>
				<input type=\"checkbox\" name=\"del_file[$i]\" /> Delete Uploaded File .. {$div[$i]}
				</td>
				<td id=\"prvImg".($i+1)."\"><a href=\"javascript:input(".($i+1).")\"><img src=\"download.php?id=".$id."&no=".$no."&mode=1&div=".$i."&thumbnail=1\" width=\"50\" onload=\"if(this.height>this.width) {this.height=50}\" onError=\"this.style.display='none'\" /></a></td>
			</tr>
			";
		}
		$data['prvFile'] = $tmp;
	}

	list( $data['m_id'] ) = $db->fetch("select m_id from ".GD_MEMBER." where m_no='" . $data['m_no'] . "'");
	$data['subject'] = htmlspecialchars( $data['subject'] );
	$data['contents'] = htmlspecialchars( $data['contents'] );
	$data['mobile']	= explode("-",$data[mobile]);
}
else {
	$colspan = " colspan=\"3\"";
}
?>
<script src="../../lib/js/board.js"></script>
<script type="text/javascript">
function add() {
	var table = document.getElementById('table');
	if (table.rows.length>11) {
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
	opener.location.reload();
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
<form name=form method=post action="<?=$sitelink->link('admin/board/list_management_indb.php','ssl');?>" onsubmit="return encodeContent(this)" enctype="multipart/form-data" >
<input type=hidden name=mode value="<?= $_GET["mode"]?>">
<input type=hidden name=no value="<?=$_GET[no]?>">
<input type=hidden name=params value="<?=$_SERVER[QUERY_STRING]?>">

<div class="title title_top">게시글 <?=($_GET["mode"] == "register") ? "작성" : "수정"?></div>

<table class=tb>
<? if($_GET["mode"] == "register") { ?>
<col width="120" class=cellC><col width="500" class=cellL><col width="40" class=cellC><col width="120" class=cellL>
<? } else { ?>
<col class=cellC><col class=cellL>
<? } ?>
<tr">
<? if($_GET["mode"] == "register") { ?>
	<td>게시판 선택 </td>
	<? if(!$_GET['inc'] || $_GET['inc'] == "all") { ?>
	<td>
		<select name="id" onchange="reloadwindow(this.value)" required fld_esssential>
		<option value="">  -- 게시판선택 -- </option>
		<?
		$query="select * from ".GD_BOARD." order by sno";
		$result=mysql_query($query);
		while($row=mysql_fetch_array($result)) {
			include "../../conf/bd_".$row['id'].".php";
			?>
		<option value="<?=$row['id']?>" <?= $selected['id'][$key]?>><?=$bdName?></option>
		<? } ?>
		</select>
	</td>
	<? } else { ?>
	<td>
		<?=$bdName?>
		<input type="hidden" name="id" value="<?=$_GET['inc']?>" />
	</td>
	<? } ?>
<? } else { ?>
	<input type="hidden" name="id" value="<?=$_GET['inc']?>" />
<? } ?>
	<td>작성자</td>
	<td>
		<? if($_GET["mode"] == "register") { ?>
		<input type="hidden" name="m_no" value="<?= $sess["m_no"]?>" />
		<input type="text" name="name" value="<?= $_SESSION["member"]["name"]?>" class="line" style="width:100px;" />
		<? } else { ?>
		<input type="hidden" name="m_no" value="<?= $data["m_no"]?>" />
		<input type="hidden" name="name" value="<?= $data["name"]?>" />
		<font class=ver8><b><?= $data["name"]?></b>
		<? } ?>
	</td>
</tr>
<? if($_GET["mode"] == "modify") { ?>
<tr>
	<td>작성일</td>
	<td<?=$colspan?>><?= $data["regdt"]?> (IP: <?= $data["ip"]?>)</td>
</tr>
<? } ?>
<? if($bdTitleCChk == "on" || $bdTitleSChk == "on" || $bdTitleBChk == "on") { ?>
<tr>
	<td>제목효과</td>
	<td<?=$colspan?> style="height:32px">
	<? if(isset($bdTitleCChk) && $bdTitleCChk == "on") { ?>
		<select name="titleStyle[C]" id="titleStyle[C]" class=box>
			<option value="">제목 글자색</option>
			<option value="#000000" style="color:#000000" <?= $selected["titleC"]["#000000"]?>>검정</option>
			<option value="#7F7F7F" style="color:#7F7F7F" <?= $selected["titleC"]["#7F7F7F"]?>>회색</option>
			<option value="#FFA300" style="color:#FFA300" <?= $selected["titleC"]["#FFA300"]?>>노랑</option>
			<option value="#FF600F" style="color:#FF600F" <?= $selected["titleC"]["#FF600F"]?>>주황</option>
			<option value="#ff0000" style="color:#ff0000" <?= $selected["titleC"]["#ff0000"]?>>빨강</option>
			<option value="#A03F00" style="color:#A03F00" <?= $selected["titleC"]["#A03F00"]?>>갈색</option>
			<option value="#FF08A0" style="color:#FF08A0" <?= $selected["titleC"]["#FF08A0"]?>>분홍</option>
			<option value="#5000AF" style="color:#5000AF" <?= $selected["titleC"]["#5000AF"]?>>보라</option>
			<option value="#B0008F" style="color:#B0008F" <?= $selected["titleC"]["#B0008F"]?>>자주</option>
			<option value="#7FC700" style="color:#7FC700" <?= $selected["titleC"]["#7FC700"]?>>연두</option>
			<option value="#009FAF" style="color:#009FAF" <?= $selected["titleC"]["#009FAF"]?>>청녹</option>
			<option value="#0000ff" style="color:#0000ff" <?= $selected["titleC"]["#0000ff"]?>>파랑</option>
		</select>
	<? } ?>
	<? if(isset($bdTitleSChk) && $bdTitleSChk == "on") { ?>
		<select name="titleStyle[S]" id="titleStyle[S]" class=box>
			<option value="">제목 글자크기</option>
			<option value="8px" <?= $selected["titleS"]["8px"]?>>아주작게 [8px]</option>
			<option value="10px" <?= $selected["titleS"]["10px"]?>>작게 [10px]</option>
			<option value="12px" <?= $selected["titleS"]["12px"]?>>보통 [12px]</option>
			<option value="18px" <?= $selected["titleS"]["18px"]?>>크게 [18px]</option>
			<option value="24px" <?= $selected["titleS"]["24px"]?>>아주 크게 [24px]</option>
		</select>
	<? } ?>
	<? if(isset($bdTitleBChk) && $bdTitleBChk == "on") { ?>
		<select name="titleStyle[B]" id="titleStyle[B]" class=box>
			<option value="">제목 글자굵기</option>
			<option value="default" <?= $selected["titleB"]["default"]?>>보통</option>
			<option value="bold" <?= $selected["titleB"]["bold"]?>>굵게</option>
		</select>
	<? } ?>
		</div>
	</td>
</tr>
<? } ?>
<tr>
	<td>제목</td>
	<td<?=$colspan?>><input type="text" name="subject" value="<?=$data['subject']?>" style="width:90%;" class=line required fld_esssential></td>
</tr>
<? if(!$data['sub']) { ?>
<tr>
	<td>공지</td>
	<td<?=$colspan?>><label><input type="checkbox" style="border:0" name="notice" value="o" <?= $checked["notice"] ?>>NOTICE</label></td>
</tr>
<?
}
if($bdName && $bdSecretChk != '2') {
?>
<tr>
	<td>비밀글</td>
	<td<?=$colspan?>><label><input type="checkbox" style="border:0" name="secret" <?= $checked["secret"] ?>>비밀글</label></td>
</tr>
<? } ?>
<tr>
	<td>내용</td>
	<td<?=$colspan?>>
		<div style="height:400px;padding-top:5px;position:relative;z-index:99">
		<textarea name=contents style="width:100%;height:350px" type=editor fld_esssential label="내용"><?=$data["contents"]?></textarea>
		<script src=../../lib/meditor/mini_editor.js></script>
		<script>mini_editor("../../lib/meditor/",false)</script>
		</div>
	</td>
</tr>
<? if($bdUseFile == "on") {?>
<tr>
	<td>업로드</td>
	<td<?=$colspan?>>

	<table width=100% id=table cellpadding=0 cellspacing=0 border=0>
	<col class=engb align=center>
	<?= $data["prvFile"] ?>
	<? if(count(file)<12 ) {?>
	<tr>
		<td width=20 nowrap>1</td>
		<td width=100%>
		<input type=file name="file[]" style="width:80%" class=linebg onChange="preview(this.value,0)">
		<a href="javascript:add()"><img src="../img/btn_upload_plus.gif" align=absmiddle></a>
		</td>
		<td id=prvImg0></td>
	</tr>
	<? } ?>
	</table>

	<table><tr><td height=2></td></tr></table>
	<div width=100% style="padding:5;" class=stxt>
	- 파일은 최대 12개까지 다중업로드가 지원됩니다<br>
	- Source창에서 오른쪽 이미지를 클릭하면 이미지치환코드가 입력됩니다
	<? if($bdMaxSize) { ?><div>- 파일 업로드 최대 사이즈는 <?= byte2str($bdMaxSize) ?>입니다</div><? } ?>
	</div>
	</td>
</tr>
<? } ?>
</table>

<div class="button_popup">
<input type=image src="../img/btn_confirm_s.gif">
<a href="javascript:self.close()"><img src="../img/btn_cancel_s.gif"></a>
</div>

</form>
<script>table_design_load();</script>