<?

include "../_header.popup.php";
include "../../lib/page.class.php";

if( !$_GET['page']) $_GET['page'] = 1;
if( !$_GET['page_num']) $_GET['page_num'] = "10";
if( !$_GET['sort']) $_GET['sort'] = "regdt desc";

if(!$_GET['type']) msg("���־��� �亯�� �Ҽ��� �������� �ʾҽ��ϴ�.", "close");
$db_where[] = "customerType = '".$_GET['type']."'";

$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->vars['page']= getVars('page,log,x,y');
$pg->field = " * ";
$pg->setQuery(GD_GOODS_FAVORITE_REPLY,$db_where,$_GET['sort']);
$pg->exec();

$res = $db->query($pg->query);

$qstr .= "type=".$_GET['type'];
$qstr .= "&page=".$_GET['page'];
$qstr .= "&selType=".$_GET['selType'];
?>
<script src="../../lib/js/board.js"></script>
<script type="text/javascript">
// ���־��� �亯 ���� - ����Ʈ �ڽ� ����
function selectReply(val) {
	selObj = opener.document.getElementById('<?=$_GET['type']?>FavoriteReplyNo');
	rdoObj = opener.document.getElementById('<?=$_GET['type']?>FavoriteReplyUse_y');

	for(i = 0; selObj.options.length > i; i++) {
		if(selObj.options[i].value == val) {
			selObj.options[i].selected = true;
			rdoObj.checked = true;
			opener.checkReplyUse('y');
			window.close();
			break;
		}
	}
}

// �亯���� - ���� �ڵ����� ���־��� �亯 �Է�
function applyForm(val) {
	location.href = "customer_indb.php?type=<?=$_GET['type']?>&mode=selectReply&rno=" + val;
}

// ���Ÿ�� �˻�
function chkDelete(idNum) {
	location.href = "customer_indb.php?id=" + idNum + "&mode=replyDelete&<?=$qstr?>";
}

// ���־��� �亯 ���/���� �� ����
function chkForm(f) {
	if(!f.subject.value) {
		alert("������ �Է��� �ּ���");
		f.subject.focus();
		return false;
	}

	return true;
}
</script>

<div class="title title_top">���־��� �亯</div>

<table width="100%" cellpadding="0" cellspacing="0">
<col width="40" align="center">
<col align="left">
<col width="60" align="center">
<col width="60" align="center">
<tr><td class="rnd" colspan="4"></td></tr>
<tr class="rndbg">
	<th>��ȣ</th>
	<th>����</th>
	<th>����</th>
	<th>����</th>
</tr>
<tr><td class="rnd" colspan="4"></td></tr>
<tr><td height="3" colspan="4"></td></tr>
<?
$i = 0;
while($data=$db->fetch($res)){
?>
<tr height="30">
	<td><?=$pg->idx--?></td>
	<td><a href="javascript:<?=($_GET['selType'] == "rForm") ? "applyForm" : "selectReply"?>('<?=$data['sno']?>')"><?=$data['subject']?></a></td>
	<td><a href="../board/customer_reply.php?id=<?=$data['sno']?>&mode=replyModify&<?=$qstr?>"><img src="../img/i_edit.gif"></a></td>
	<td><a href="javascript:chkDelete(<?=$data['sno']?>)"><img src="../img/i_del.gif"></a></td>
</tr>
<tr><td height=4 colspan=9></td></tr>
<tr><td colspan=9 class=rndline></td></tr>
<? } ?>
</table>

<table style="width:100%">
<tr>
	<td style="width:50%"></td>
	<td style="text-align:right">
		<a href="../board/customer_reply.php?mode=replyRegist&<?=$qstr?>"><img src="../img/btn_select_add.gif" align="absmiddle" /></a>
	</td>
</tr>
</table>


<div align="center" class="pageNavi"><font class="ver9"><?=$pg->page['navi']?></font></div>


<?
	if($_GET['mode'] == "replyRegist" || $_GET['mode'] == "replyModify") {
		if($_GET['id']) {
			$query = "SELECT * FROM ".GD_GOODS_FAVORITE_REPLY." WHERE sno = '".$_GET['id']."'";
			$view = $db->fetch($query);
		}
?>
<form name="form" method="post" action="customer_indb.php" onsubmit="return chkForm(this)">
<input type="hidden" name="type" value="<?=$_GET['type']?>">
<input type="hidden" name="mode" value="<?=$_GET['mode']?>">
<input type="hidden" name="id" value="<?=$_GET['id']?>">
<input type="hidden" name="qstr" value="<?=$qstr?>">

<div class="title title_top">���־��� �亯 <?=($_GET['mode'] == "replyRegist") ? "���" : "����"?></div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>����</td>
	<td><input type="text" name="subject" value="<?=$view['subject']?>" style="width:90%;" class="line" required></td>
</tr>
<tr>
	<td>����</td>
	<td>
		<div style="height:105px;padding-top:5px;position:relative;z-index:99">
<? if($_GET['type'] == 'qna') { ?>
		<textarea name="contents" style="width:100%;height:100px" type="editor" required label="����"><?=$view["contents"]?></textarea>
		<script src="../../lib/meditor/mini_editor.js"></script>
		<script>mini_editor("../../lib/meditor/",false)</script>
<? } else { ?>
		<textarea name="contents" style="width:100%;height:100px" required label="����"><?=$view["contents"]?></textarea>
<? } ?>
		</div>
	</td>
</tr>
</table>
<? } ?>

<div class="button_popup" align="center">
<? if($_GET['mode'] == "replyRegist" || $_GET['mode'] == "replyModify") { ?><input type="image" src="../img/btn_confirm_s.gif" align="absmiddle" /><? } ?>
 <a href="javascript:window.close();"><img src="../img/btn_close_s.gif" align="absmiddle" /></a>
</div>

</form>
<script>table_design_load();</script> 