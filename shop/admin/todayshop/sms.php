<?
include "../_header.popup.php";

/*
������..
    [mode] =>
    [query] => select  SC.*, MB.name from
	gd_todayshop_subscribe AS SC
	LEFT JOIN gd_member AS MB
	ON SC.m_id = MB.m_id

    [chk] => Array
        (
            [0] => 1
            [1] => 2
        )

    [type] => ts_select



��ü..
Array
(
    [mode] =>
    [query] => select  SC.*, MB.name from
	gd_todayshop_subscribe AS SC
	LEFT JOIN gd_member AS MB
	ON SC.m_id = MB.m_id

    [type] => ts_query
)
*/


$_smsReceiverChk = '';

$now = time();

if ($_POST['type'] == 'ts_query') {	// �˻� ��� ��ü

	$_POST['query'] = get_magic_quotes_gpc() ? stripslashes($_POST['query']) : $_POST['query'];

	// ī���� ���� �����
	$cnt_query = "select count(*) ".substr($_POST['query'],strpos($_POST['query'], 'from'),strlen($_POST['query']) );
	list($total) = $db->fetch($cnt_query);

}
else {								// ����
	$total = sizeof($_POST['chk']);
}
?>

<script language="JavaScript" type="text/JavaScript">
function fnChkForm(f) {
	f.msg.value = f.msg.value.replace(/\u00A0/g, ' ');
	return chkForm(f);
}

function fnSMSReserve(v) {

	if (v == 1) {
		$('reserve_date_wrap').setStyle({display:'inline'});
		//$('reserve_date').trigger('click');
	}
	else {
		$('reserve_date_wrap').setStyle({display:'none'});

	}

}


</script>

<div class="title title_top"><font face="����" color="black">SMS(���ⱸ��) �ۼ� <span>���ⱸ�� ��û�ڵ鿡�� �߼��� SMS�� �ۼ��մϴ�</span></div>

<form method="post" action="indb.sms.php" target="ifrmHidden" onsubmit="return fnChkForm(this);">
<input type="hidden" name="type" value="<?=$_POST['type']?>">
<input type="hidden" name="query" value="<?=$_POST['query']?>">
<? if (isset($_POST['chk'])) { foreach ($_POST['chk'] as $v) {?>
<input type="hidden" name="chk[]" value="<?=$v?>">
<? }} ?>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>���Ŵ��</td><td>�߼��ο� �� <?=number_format($total)?>��</td>
</tr>
<tr>
	<td>�߼ۼ���</td>
	<td>
	<label class="noline"><input type="radio" name="reserve" value="0" onClick="fnSMSReserve(0);" checked>��ù߼�</label>
	<label class="noline"><input type="radio" name="reserve" value="1" onClick="fnSMSReserve(1);" >����߼�</label>

	<div id="reserve_date_wrap" style="display:none;">
	<input class="line" type="text" name="reserve_date" id="reserve_date" value="<?=date('Ymd',$now)?>" onclick="calendar(event)" onkeydown="onlynumber()" >

	<select name="reserve_hour">
		<? $h = date('H',$now + 3600); ?>
		<? for ($i=1;$i<=24;$i++) { ?>
		<option value="<?=$i?>" <?=($i == $h ? 'selected' : '')?>><?=$i?>��</option>
		<? } ?>
	</select>

	<select name="reserve_minute">
		<? for ($i=0;$i<=60;$i = $i + 10) { ?>
		<option value="<?=$i?>"><?=$i?>��</option>
		<? } ?>
	</select>
	</div>
	</td>
</tr>
<tr>
	<td>�߼���Ȳ</td>
	<td>
	<div style="background:#D7D7D7;border:0 solid #C5C5C5;width:600px;height:10px;font:0;">
	<div id="sms_bar" style="width:0;height:10px;font:0;background:#ff0000;"></div>
	</div>
	</td>
</tr>
</table>

<div style="padding-top:10px"></div>

<!-- SMS������ : Start -->
<? include "../member/_smsForm.php"; ?>
<!-- SMS������ : End -->

</form>
