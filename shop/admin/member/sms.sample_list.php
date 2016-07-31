<?

include "../_header.popup.php";
include "../../lib/page.class.php";

$db_table = "".GD_SMS_SAMPLE."";

if ($_GET[category]) $where[] = "category='$_GET[category]'";

$pg = new Page($_GET[page],8);
$pg->setQuery($db_table,$where,"sort");
$pg->exec();

$res = $db->query($pg->query);

?>

<style>
body {margin:0}
</style>

<script>
function selectMsg(msg)
{
	parent.document.getElementById("msg").value = msg;
	parent.document.getElementById("msg").focus();
	parent.chkLength(parent.document.getElementById("msg"),'m');
}

function fnSmsSampleDelete(sno) {
if (confirm('문자메세지 예제를 삭제 하시겠습니까?')) {
//
	var f = $('tmpForm');

	if (f == null)
	{
		var f = new Element('form',{'name': 'tmpForm', 'id' : 'tmpForm','method':'post','action':'./indb.php','target':'ifrmHidden'}).insert('<input type="hidden" name="mode" value="sms_sample_del"><input type="hidden" name="sno" value="">');
		document.body.appendChild(f);
	}
	f.sno.value = sno;
	f.submit();
//
}}
</script>

<div style="width:584px">

<table cellpadding=0 cellspacing=0 border=0>
<tr>
	<? $idx=0; while ($data=$db->fetch($res)){ ?>
	<td>
	<table>
	<tr>
		<td>

		<table width=146 cellpadding=0 cellspacing=0 border=0>
		<tr>
			<td height=41 background="../img/sms_top.gif" align=right valign=top style="padding:15px 17px 0 0;font:8pt tahoma">
			<a href="javascript:fnSmsSampleDelete('<?=$data[sno]?>')"><img src="../img/btn_delsmstext.gif"></a>
			<a href="javascript:parent.popupLayer('sms.sample_reg.php?mode=sms_sample_mod&sno=<?=$data[sno]?>')"><img src="../img/btn_editsmstext.gif" boder=0></a>
			</td>
		</tr>
		<tr>
			<td background="../img/sms_bg.gif" align=center height="81"><textarea name=msg style="font:9pt 굴림체;overflow:hidden;border:0;background-color:transparent;width:98px;height:74px;" readonly class=hand onclick="selectMsg(this.value)"><?=$data[msg]?> </textarea></td>
		</tr>
		<tr><td height=28 background="../img/sms_bottom.gif" align=center><?=$data[subject]?></td></tr>
		</table>

		</td>
	</tr>
	</table>

	</td>
	<? if (++$idx%4==0){ ?></tr><tr><? } ?>
	<? } ?>
</tr>
</table>

<div class=pageNavi align=center><font class=ver8><?=$pg->page[navi]?></div>

</div>