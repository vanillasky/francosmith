<?
	include "../_header.popup.php";
	@include "../../conf/bd_".$_GET['inc'].".php";

	if(!$_GET["inc"]) msg("�Խ��� ������ �����ϴ�.", "close");

	$targetData = $db->fetch("SELECT * FROM gd_bd_".$_GET['inc']." WHERE no = ".$_GET['no']); // ������ ����

	// ������ ÷������
	if($targetData['old_file']) {
		$ar_tmp = explode("|", $targetData['old_file']);
		for($i = 0, $imax = count($ar_tmp); $i < $imax; $i++) {
			if($attachList) $attachList .= "<span style=\"margin:0px 5px; color:#CCCCCC;\">|</span>";
			$attachList .= "<a href=\"../../board/download.php?id=".$_GET['inc']."&no=".$_GET['no']."&div=".$i."\">".$ar_tmp[$i]."</a>";
		}
	}

	// �亯�� ����
	list($memName) = $db->fetch("SELECT name FROM ".GD_MEMBER." WHERE m_no = '".$sess['m_no']."'");
?>
<script src="../../lib/js/board.js"></script>
<script type="text/javascript">
function add() {
	var table = document.getElementById('table');
	if(table.rows.length > 11) {
		alert("���� ���ε�� �ִ� 12���� �����մϴ�");
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
	#targetContents p { margin:2px 0; } /* ������ ���뿡 ���� */
	.popTb { width:100%; background:#E6E6E6; }
	.popTb .fd { color:#333333; background:#F6F6F6; font:9pt tahoma; text-align:left; font-weight:bold; letter-spacing:-1; }
	.popTb .val { background:#FFFFFF; }
</style>
<form name="form" method="post" action="<?=$sitelink->link('admin/board/list_management_indb.php','ssl');?>" onsubmit="return encodeContent(this)" enctype="multipart/form-data" >
<input type="hidden" name="mode" value="<?=$_GET["mode"]?>">
<input type="hidden" name="id" value="<?=$_GET['inc']?>">
<input type="hidden" name="no" value="<?=$_GET['no']?>">
<input type="hidden" name="params" value="<?=$_SERVER['QUERY_STRING']?>">

<!-- ���� �� ���� ���� -->
<div class="title title_top"><?=$bdName?></div>
<table cellpadding="5" cellspacing="1" border="0" class="popTb">
<col class="fd"><col class="val"><col class="fd"><col class="val">
<tr>
	<td width="100">����</td>
	<td width="503"><?=$targetData['subject']?></td>
	<td width="40">�̸�</td>
	<td width="120"><?=$targetData['name']?></td>
</tr>
<tr>
	<td>�ۼ���</td>
	<td colspan="3"><?=$targetData['regdt']?> <?=$targetData['ip']?></td>
</tr>
<tr>
	<td>����</td>
	<td colspan="3"><div style="border:1px #CCCCCC solid; padding:5px;"><div id="targetContents" style="width:100%; height:150px; overflow-y:scroll; word-wrap:break-word; word-break:break-all; "><?=$targetData['contents']?></div></div></td>
</tr>
<? if($attachList) { ?>
<tr>
	<td>÷������</td>
	<td colspan="3"><?=$attachList?></td>
</tr>
<? } ?>
</table>
<!-- ���� �� ���� ���� -->

<!-- �亯 �� ���� -->
<div class="title title_top">�Խñ� �亯</div>
<table cellpadding="5" cellspacing="1" border="0" class="popTb">
<col class="fd"><col class="val"><col class="fd"><col class="val">
<tr>
	<td width="100">����</td>
	<td width="503"><input type="text" name="subject" value="<?=$targetData['subject']?>" style="width:90%;" class="line" required fld_esssential></td>
	<td width="40">�ۼ���</td>
	<td width="120">
		<input type="hidden" name="m_no" value="<?=$sess["m_no"]?>">
		<input type="text" name="name" value="<?=$memName?>" style="width:100px;" class="line" required fld_esssential>
	</td>
</tr>
<? if($bdTitleCChk == "on" || $bdTitleSChk == "on" || $bdTitleBChk == "on") { ?>
<tr>
	<td>����ȿ��</td>
	<td colspan="3">
<?
if(isset($bdTitleCChk) && $bdTitleCChk == "on") {
?>
		<select name="titleStyle[C]" id="titleStyle[C]" class="box">
			<option value="">���� ���ڻ�</option>
			<option value="#000000" style="color:#000000" <?=$selected["titleC"]["#000000"]?>>����</option>
			<option value="#7F7F7F" style="color:#7F7F7F" <?=$selected["titleC"]["#7F7F7F"]?>>ȸ��</option>
			<option value="#FFA300" style="color:#FFA300" <?=$selected["titleC"]["#FFA300"]?>>���</option>
			<option value="#FF600F" style="color:#FF600F" <?=$selected["titleC"]["#FF600F"]?>>��Ȳ</option>
			<option value="#ff0000" style="color:#ff0000" <?=$selected["titleC"]["#ff0000"]?>>����</option>
			<option value="#A03F00" style="color:#A03F00" <?=$selected["titleC"]["#A03F00"]?>>����</option>
			<option value="#FF08A0" style="color:#FF08A0" <?=$selected["titleC"]["#FF08A0"]?>>��ȫ</option>
			<option value="#5000AF" style="color:#5000AF" <?=$selected["titleC"]["#5000AF"]?>>����</option>
			<option value="#B0008F" style="color:#B0008F" <?=$selected["titleC"]["#B0008F"]?>>����</option>
			<option value="#7FC700" style="color:#7FC700" <?=$selected["titleC"]["#7FC700"]?>>����</option>
			<option value="#009FAF" style="color:#009FAF" <?=$selected["titleC"]["#009FAF"]?>>û��</option>
			<option value="#0000ff" style="color:#0000ff" <?=$selected["titleC"]["#0000ff"]?>>�Ķ�</option>
		</select>
<?
}
if(isset($bdTitleSChk) && $bdTitleSChk == "on") {
?>
		<select name="titleStyle[S]" id="titleStyle[S]" class="box">
			<option value="">���� ����ũ��</option>
			<option value="8px" <?=$selected["titleS"]["8px"]?>>�����۰� [8px]</option>
			<option value="10px" <?=$selected["titleS"]["10px"]?>>�۰� [10px]</option>
			<option value="12px" <?=$selected["titleS"]["12px"]?>>���� [12px]</option>
			<option value="18px" <?=$selected["titleS"]["18px"]?>>ũ�� [18px]</option>
			<option value="24px" <?=$selected["titleS"]["24px"]?>>���� ũ�� [24px]</option>
		</select>
<?
}
if(isset($bdTitleBChk) && $bdTitleBChk == "on") {
?>
		<select name="titleStyle[B]" id="titleStyle[B]" class="box">
			<option value="">���� ���ڱ���</option>
			<option value="default" <?=$selected["titleB"]["default"]?>>����</option>
			<option value="bold" <?=$selected["titleB"]["bold"]?>>����</option>
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
	<td>��б�</td>
	<td colspan="3"><label><input type="checkbox" style="border:0" name="secret" <?=($targetData['secret']) ? "checked" : ""?>>��б�</label></td>
</tr>
<? } ?>
<tr>
	<td>����</td>
	<td colspan="3">
		<div style="width:100%; height:<?=($_GET['mode'] == "reply") ? "185" : "355"?>px;position:relative;z-index:99">
		<textarea name="contents" style="width:100%;height:<?=($_GET['mode'] == "reply") ? "180" : "350"?>px" type="editor" fld_esssential label="����"><?=$data["contents"]?></textarea>
		<script src="../../lib/meditor/mini_editor.js"></script>
		<script>mini_editor("../../lib/meditor/",false)</script>
		</div>
	</td>
</tr>
<? if($bdUseFile == "on") { ?>
<tr>
	<td>���ε�</td>
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
			- ������ �ִ� 12������ ���߾��ε尡 �����˴ϴ�<br>
			- Sourceâ���� ������ �̹����� Ŭ���ϸ� �̹���ġȯ�ڵ尡 �Էµ˴ϴ�
			<? if($bdMaxSize) { ?><br />- ���� ���ε� �ִ� ������� <?=byte2str($bdMaxSize) ?>�Դϴ�<? } ?>
		</div>
	</td>
</tr>
<? } ?>
</table>
<!-- �亯 �� ���� -->

<div class="button_popup"><input type="image" src="../img/btn_confirm_s.gif" align="absmiddle" style="margin-right:3px;" /><a href="javascript:self.close()"><img src="../img/btn_cancel_s.gif" align="absmiddle" /></a></div>

</form>
<script>table_design_load();</script>