<?

include "../_header.popup.php";

### 분류별 수량 체크
$query = "select category,count(*) cnt from ".GD_SMS_SAMPLE." group by category";
$res = $db->query($query);
while ($data=$db->fetch($res)) $cnt[$data[category]] = $data[cnt];

# 회원인경우
if ($_GET['m_id']){
	if ($_GET['m_id']) list ($phone) = $db->fetch("SELECT mobile FROM ".GD_MEMBER." WHERE m_id='".$_GET['m_id']."'");
	if ($_GET['mobile']) $phone = $_GET['mobile'];
}
# SMS 주소록인 경우
if ($_GET['sno']){
	list ($phone) = $db->fetch("SELECT sms_mobile FROM ".GD_SMS_ADDRESS." WHERE sno='".$_GET['sno']."'");
}
# 직접입력
if ($_GET[mobile]) $phone = $_GET[mobile];
if ($phone) $total++;
?>

<script>
function insChr(str)
{
	var fm = document.forms[0];
	fm.msg.value = fm.msg.value + str;
	chkLength(fm.msg);
}
function chkLength(obj){
	str = obj.value;
	document.getElementsByName('vLength')[0].value = chkByte(str);
	if (chkByte(str)>90){
		alert("90byte까지만 입력이 가능합니다");
		obj.value = strCut(str,90);
	}
}

function fnSetSmsMessage() {
	var form = document.frmSmeSelector;
	var msg = "";

	if(form.sms_type[0].checked == true) {
		msg = form.sms_msg.value;
	} else if (form.sms_type[1].checked == true){
		msg = form.lms_msg.value;
	}

	if (msg == '')
	{
		alert('SMS 메시지 내용을 작성해 주세요.');
		return false;
	}

	<? if ($_GET['mode'] == 'popup') { ?>
		opener.document.<?=$_GET['fname']?>.<?=$_GET['fld']?>.value = msg;
		opener.fnToggleSms();
		self.close();
	<? } else if ($_GET['mode'] == 'frame') { ?>
		parent.document.<?=$_GET['fname']?>.<?=$_GET['fld']?>.value = msg;
		opener.fnToggleSms();
		self.close();
	<? } ?>
}

</script>

<div class="title title_top"><font face="굴림" color="black"><b>SMS</b></font> 보내기<span>SMS문자메세지를 이용하여 고객들을 감동시키세요</span></div>

<form name="frmSmeSelector" method="post" action="" target="ifrmHidden" onsubmit="return chkForm(this);">
<input type="hidden" name="mode" value="send_sms">
<input type="hidden" name="type" value="1">

<?
	$is_sms_selector = true;	// 상품 문의 답변시 sms 메시지를 선택하기 위함, 전송용 폼을 공유하므로 해당 값으로 분기.
	include "./_smsForm.php";
?>

</form>