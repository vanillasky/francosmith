<?
if (!class_exists(callNumber)) include_once dirname(__FILE__)."/../../lib/callNumber.class.php";

$defaultReserveTime = strtotime("+1 hours +30 minute");
$defaultReserveDate = date("Ymd", $defaultReserveTime);
$defaultReserveHour = date("H", $defaultReserveTime);
$defaultReserveMinute = substr(date("i", $defaultReserveTime), 0, 1) . '0';

// �ٸ� ���������� �ҷ����⵵ �ϹǷ� ġȯ�Ѵ�.
$type = isset($_POST['type']) ? $_POST['type'] : 1;

### �з��� ���� üũ
$query = "SELECT category,count(*) cnt FROM ".GD_SMS_SAMPLE." GROUP BY category";
$res = $db->query($query);
while ($data=$db->fetch($res)) $cnt[$data[category]] = $data[cnt];

$callNumber = new callNumber;
$callbackData = $callNumber->getCallNumberData('callback');
?>

<script language="JavaScript" type="text/JavaScript">
function clearBg(obj){
	var backimg = '';
	if(obj.name == 'sms_msg'){
		backimg = "../img/long_message02_none.gif";
	} else {
		backimg = "../img/long_message01_none.gif";
	}
	obj.style.backgroundImage = "url('"+backimg+"')";
}
function insChr(str)
{
	var msg = 	document.getElementById("msg");
	msg.value = msg.value + str;
	clearBg(msg);
	chkLength(msg,'m');
}
function chkLength(obj,tcode){
	str = obj.value;
	if(tcode == 's'){
		if(document.getElementsByName('sms_type')[1].checked == true){
			var specialChars = /[^\u3131-\u314e\uac00-\ud7a3a-zA-Z0-9]/g;
			if(str.match(specialChars)){
				alert("Ư�� ���ڴ� ��� �� �� �����ϴ�.");
				obj.value = str.split(specialChars).join("");
				str = obj.value;
				document.getElementsByName('vLength')[0].value = parseInt(chkByte(str),10) + parseInt(chkByte(document.getElementById("msg").value),10);
				return;
			}
			var strByte = parseInt(chkByte(str),10) + parseInt(chkByte(document.getElementById("msg").value),10);
			if (strByte>2000){
				alert("����� ������ �޽����� 2000bytes�� ���� �� �����ϴ�.");
				var cutByte = 2000 - parseInt(chkByte(document.getElementById("msg").value),10);
				obj.value = strCut(str,cutByte);
				str = obj.value;
				document.getElementsByName('vLength')[0].value = parseInt(chkByte(str),10) + parseInt(chkByte(document.getElementById("msg").value),10);
			}
			if (chkByte(str)>40){
				alert("������ 40bytes���� �Դϴ�.");
				obj.value = strCut(str,40);
				str = obj.value;
				document.getElementsByName('vLength')[0].value = parseInt(chkByte(str),10) + parseInt(chkByte(document.getElementById("msg").value),10);
				return;
			} else {
				document.getElementsByName('vLength')[0].value = parseInt(chkByte(str),10) + parseInt(chkByte(document.getElementById("msg").value),10);
			}
		}
	} else if(tcode == 'm'){
		if(document.getElementsByName('sms_type')[0].checked == true){
			document.getElementsByName('vLength')[0].value = chkByte(str);
			if (chkByte(str)>90){
				document.getElementsByName('sms_type')[1].checked = true;
				document.getElementById("img_sms_title").src = "../img/btn_sms_off.gif";
				document.getElementById("img_lms_title").src = "../img/btn_lms_on.gif";
				document.getElementById("sms_top").style.backgroundImage = "url('../img/lms_top.gif')";
				document.getElementById("tr_lms_subject").style.display = "";
				document.getElementById("td_msg").className = "td_lms_msg";
				document.getElementById("msg").className = "area_lms_msg";
				document.getElementById("msg").setAttribute("name","lms_msg");
				clearBg(document.getElementById("msg"));
				document.getElementById("tr_lms_alert").style.display = "";
				document.getElementById("lms_subject").disabled = false;
				document.getElementsByName('vLength')[0].style.color = "#f00";
				document.getElementById("byte_limit").innerHTML = "2000 Byte";
				document.getElementsByName('vLength')[0].value = chkByte(str);
				document.getElementById("td_point").innerHTML = "LMS - 3����Ʈ ����";
			}
		} else {
			if (chkByte(str)<=90){
				document.getElementsByName('sms_type')[0].checked = true;
				document.getElementById("img_sms_title").src = "../img/btn_sms_on.gif";
				document.getElementById("img_lms_title").src = "../img/btn_lms_off.gif";
				document.getElementById("sms_top").style.backgroundImage = "url('../img/sms_top.gif')";
				document.getElementById("td_msg").className = "td_sms_msg";
				document.getElementById("msg").className = "area_sms_msg";
				document.getElementById("msg").setAttribute("name","sms_msg");
				clearBg(document.getElementById("msg"));
				document.getElementsByName('vLength')[0].style.color = "#000";
				document.getElementById("byte_limit").innerHTML = "90 Byte";
				document.getElementsByName('vLength')[0].value = chkByte(str);
				document.getElementById("td_point").innerHTML = "SMS - 1����Ʈ ����";
				document.getElementById("lms_subject").value = '';
				document.getElementById("tr_lms_subject").style.display = "none";
				document.getElementById("tr_lms_alert").style.display = "none";
				document.getElementById("lms_subject").disabled = true;
			} else {
				var strByte = parseInt(chkByte(str),10) + parseInt(chkByte(document.getElementById("lms_subject").value),10);
				if (strByte>2000){
					alert("�޽����� 2000bytes�� �ʰ��� �� �����ϴ�.");
					var cutByte = 2000 - parseInt(chkByte(document.getElementById("lms_subject").value),10);
					obj.value = strCut(str,cutByte);
					str = obj.value;
				}
				//LMS �� ����� �޼��� ���� 2000byte
				document.getElementsByName('vLength')[0].value = parseInt(chkByte(str),10) + parseInt(chkByte(document.getElementById("lms_subject").value),10);
			}
		}
	}
}

function fnSMSReserve(v) {

	if (v == 1) {
		$('reserve_date_wrap').setStyle({display:'inline'});
	}
	else {
		$('reserve_date_wrap').setStyle({display:'none'});

	}

}

function smsSample()
{
	var smsSample = document.getElementById("sms_sample");
	var smsSampleDisplay = document.getElementById("sms_sample_display");
	if(smsSample.style.display == 'none') {
		smsSample.style.display = '';
		smsSampleDisplay.src = '../img/btn_sms_close.gif';
	}
	else {
		smsSampleDisplay.src = '../img/btn_sms_open.gif';
		smsSample.style.display = 'none';
	}
}

function sendfailList() {
	var f = parent.document.failListForm;
	f.target = 'faillist';
	f.action = './popup.sms.failList.php';
	window.open('about:blank', "faillist", "width=720, height=600");
	f.submit();
}
</script>
<style>
#sms_top { width:146px; height:56px; background:url('../img/sms_top.gif') no-repeat top left; text-align:right; }
#img_special { margin-right:15px; margin-bottom:5px; }
#td_lms_subject { background:url(../img/sms_subject_bg.gif) repeat-y; width:146px; height:38px; text-align:center; }
#lms_subject { font:9pt ����ü; overflow:hidden; border:0; width:98px; height:31px; background:url(../img/long_message01.gif) repeat-y; }
.td_sms_msg { background:url(../img/sms_bg.gif) repeat-y; padding-top:8px; width:146px; height:81px; text-align:center; }
.td_lms_msg { background:url(../img/sms_long_bg.gif) repeat-y; padding-top:8px; width:146px; height:170px; text-align:center; }
.area_sms_msg { font:9pt ����ü; overflow:hidden; border:0; width:98px; height:74px; background:url(../img/short_message01.gif) repeat-y; }
.area_lms_msg { font:9pt ����ü; overflow:hidden; border:0; width:98px; height:150px; background:url(../img/long_message02.gif) repeat-y; }
#msg_byte { width:26px; text-align:right; border:0; font-size:8pt; font-style:verdana; }
#td_point { text-align:center; font-size:8pt; font-style:verdana; }
</style>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td valign="top" width="150">

		<table>
		<tr>
			<td>
				<table width="146px" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td height="30px">
						<table width="146px" cellpadding="0" cellspacing="0" border="0">
						<tr class="noline">
							<td width="73px" align="right"><input type="radio" name="sms_type" value="sms" checked="checked" style="visibility: hidden;"><img id="img_sms_title" src="../img/btn_sms_on.gif" /></td>
							<td width="73px" align="left"><input type="radio" name="sms_type" value="lms" style="visibility: hidden;"><img id="img_lms_title" src="../img/btn_lms_off.gif" /></td>
						</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td id="sms_top"><a href="javascript:openLayer('special');" onfocus="blur();"><img src="../img/btn_smstext.gif" id="img_special" /></a></td>
				</tr>
				<tr id="tr_lms_subject" style="display:none;">
					<td id="td_lms_subject"><textarea name="lms_subject" id="lms_subject" onkeydown="chkLength(this,'s');" onkeyup="chkLength(this,'s');" onchange="chkLength(this,'s');" onFocus="clearBg(this);" disabled><?php echo $reSubject; ?></textarea></td>
				</tr>
				<tr>
					<td id="td_msg" class="td_sms_msg"><textarea name="sms_msg" id="msg" class="area_sms_msg" onkeydown="chkLength(this,'m');" onkeyup="chkLength(this,'m');" onchange="chkLength(this,'m');" onFocus="clearBg(this);" required msgR="�޼����� �Է����ּ���"><?php echo $reMsg; ?></textarea></td>
				</tr>
				<tr><td height="31px" background="../img/sms_bottom.gif" align="center"><input name="vLength" type="text" id="msg_byte" value="0">/<font class="ver8" color="#262626" id="byte_limit">90 Bytes</font></td></tr>
				<tr>
					<td id="td_point">SMS - 1����Ʈ ����</td>
				</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td align="center">
			<? if ($is_sms_selector === true)  { ?>
			<input type="image" onClick="fnSetSmsMessage();return false;" src="../img/btn_smsreg.gif" class="null" />

			<? } else { ?>
			<input type="image" src="../img/btn_smssend.gif" class="null" />
			<? } ?>

			</td>
		</tr>
		<tr id="tr_lms_alert" style="display:none;">
			<td style="padding: 5px 10px;width:126px; height:100px;" class="extext">�� Ư�������� ��� ���񿡴� �Է��� �� ������, ���뿡 �Է��ϴ� ��� ��Ż� ��å�� ���� �߼��� ������ �� �ֽ��ϴ�.</td>
		</tr>
		</table>

	</td>
	<td valign="top">

		<br>

		<!-- Ư�� ���� ǥ -->
		<table id="special" style="position:absolute;border:1px solid #cccccc;background:#f7f7f7;padding:5px;display:none;">
		<tr>
			<? for ($i=0;$i<count($r_sms_chr);$i++){ ?>
			<td style="border:1px solid #dddddd;width:20px;height:20px;background:#ffffff" align="center" onClick="insChr(this.innerHTML);" class="hand" onmouseover="this.style.background='#FFC0FF'" onmouseout="this.style.background=''"><?=$r_sms_chr[$i]?></td>
			<? if ($i%15==14){ ?></tr><tr><? } ?>
			<? } ?>
		</tr>
		</table>
		<!-- Ư�� ���� ǥ -->

		<!-- ������� �ؼ����� �ȳ� -->
		<div style="text-align:right;margin-bottom:3px;"><a href="http://www.godo.co.kr/echost/better_godomall.gd?code=enamoo_knowhow&page=1&postNo=23" target="_blank"><img src="../img/bn_ads_send_raw_conduct.gif"/></a></div>
		<!-- ������� �ؼ����� �ȳ� -->

		<table class=tb>
		<col class=cellC><col class=cellL>
		<tr>
			<td>���� SMS ����Ʈ</td>
			<td><span id="span_sms" style="font-weight:bold"><font class="ver9" color="0074ba"><b><?=number_format(getSmsPoint())?></b></font></span><font color="262626">��</font>
			<?php if(preg_match('/member\/popup\.sms\.php/', $_SERVER['SCRIPT_NAME'])){ ?>
				<a href="javascript:" onclick="javascript:window.opener.location.href='../member/sms.pay.php';">
			<?php } else { ?>
				<a href="javascript:location.href='../member/sms.pay.php';">
			<?php } ?>
			<img src="../img/btn_smspoint.gif" align="absmiddle"></a>

			</td>
		</tr>
		<tr>
			<td>�߽Ź�ȣ</td>
			<td>
				<input type="text" name="callback" value="<?=str_replace("-","",$cfg[smsRecall])?>" size="12"  class="line" readonly="readonly" />
				<a onclick="popup_return('../member/popup.callNumber.php?target=callback&changeColor=Y','callNumber',450,250,0,0,'yes')" class="hand"><img src="../img/call_number_btn.gif" align="absmiddle"></a><br>
				<span id="smsRecallText" class="red"></span>
			</td>
		</tr>

		<tr>
			<td>�޴»��</td>
			<td>
				<? if ($type == 1) { ?>
					<? if ($total > 1) { ?>
					�� �߼� �ο� <span id="sms_send_num"><?=number_format($total)?></span> ��
					<textarea name="phone" style="display:none;"><?=$phone?></textarea>
					<? } else { ?>
					<input type="text" name="phone" value="<?php echo $phone; ?>" size="12"> (<?=number_format($total)?> ��)
					<? } ?>
				<? } else { ?>
				<?=$to_tran?> (<?=number_format($total)?> ��)
				<? } ?>

				<span id="smsReceiveRefuse" style="display: none;">&nbsp;&nbsp;(���Űź� <span id="smsReceiveRefuseMsg"><?php echo number_format($smsReceiveRefuseCount); ?></span> �� ����)</span>

				<span id="smsFailListInfo1" style="display: none; vertical-align: bottom;">
					<script type="text/javascript" src="../godo_ui.js"></script>
					<style>div.tooltip {width:260px;padding:0;margin:0;}</style>
					<span style="color: red; vertical-align: bottom;">
						<span id="smsFailListInfo2" style="display: none;">SMS �߼۽��й�ȣ<img src="../img/icons/icon_qmark.gif" style="vertical-align:bottom; cursor:pointer; border: 0px;" class="godo-tooltip" tooltip="<span style=&quot;color: red;&quot;>SMS �߼۽��й�ȣ</span>�� &quot;�߸��� ��ȭ��ȣ&quot; ���� ������ SMS �߼۽��� �̷��� �ִ� ��ȣ�Դϴ�.">&nbsp;(<span id="smsFailListInfoErrorType"><?php echo $errorType; ?></span>)</span>

						<span id="smsFailListInfo3" style="display: none; vertical-align: bottom;">
							SMS �߼۽��й�ȣ <img src="../img/icons/icon_qmark.gif" style="vertical-align:middle; cursor:pointer; border: 0px;" class="godo-tooltip" tooltip="<span style=&quot;color: red;&quot;>SMS �߼۽��й�ȣ</span>��&quot; �߸��� ��ȭ��ȣ&quot; ���� ������ SMS �߼۽��� �̷��� �ִ� ��ȣ�Դϴ�."> (<span id="smsFailListInfoCnt"><?php echo number_format($smsFailCnt); ?></span>��)
							&nbsp;
							(<input type="radio" name="includeFail" id="includeFail1" value="N" style="vertical-align: middle;" checked="checked" style="border: 0px;" disabled /> ����
							<input type="radio" name="includeFail" id="includeFail2" value="Y" style="vertical-align: middle;" style="border: 0px;" disabled /> ����)
							&nbsp;<img src="../img/btn_sms_list.gif" style="vertical-align: middle; cursor:pointer; border: 0px;" onclick="javascript:sendfailList();" />
						</span>
					</span>
				</span>
			</td>
		</tr>

		<tr>
			<td>�߼ۼ���</td>
			<td>
			<label class="noline"><input type="radio" name="reserve" value="0" onClick="fnSMSReserve(0);" checked>��ù߼�</label>
			<label class="noline"><input type="radio" name="reserve" value="1" onClick="fnSMSReserve(1);" >����߼�</label>

			<div id="reserve_date_wrap" style="display:none;">
			<input class="line" type="text" name="reserve_date" id="reserve_date" value="<?php echo $defaultReserveDate; ?>" onclick="calendar(event)" onkeydown="onlynumber()" >

			<select name="reserve_hour">
				<? for ($i=1;$i<=24;$i++) { ?>
				<option value="<?=$i?>" <?php echo ($i == $defaultReserveHour ? 'selected' : ''); ?>><?=$i?>��</option>
				<? } ?>
			</select>

			<select name="reserve_minute">
				<? for ($i=0;$i<=60;$i = $i + 10) { ?>
				<option value="<?=$i?>" <?php echo ($i == $defaultReserveMinute ? 'selected' : ''); ?>><?=$i?>��</option>
				<? } ?>
			</select>
			<div style="margin: 5px 0px 3px 5px; color: #0074ba;">����߼��� ����ð����κ��� 10�� ���ķθ� ������ �����մϴ�.</div>
			<div style="margin: 5px 0px 3px 5px; color: #0074ba;">����߼��� ������� �� ���Ź�ȣ ������ �޽��� �߼� 1�ð� ���� �����մϴ�.</div>
			<div style="margin: 5px 0px 3px 5px; color: red;">�� �߰��ð�(����9�ú��� �� ������ ����8��) ���� ���� ���ۿ� ���� ������ ���Ǹ� ���� �ʰ� ���� SMS �߼� �� ���·ᰡ �ΰ��� �� �ֽ��ϴ�.</div>
			</div>
			</td>
		</tr>
		<tr>
			<td>�߼���Ȳ</td>
			<td>
				<div style="background:#D7D7D7;border:0 solid #C5C5C5;width:100%;height:10px;font-size:0;margin-bottom:10px;">
				<div id="sms_bar" style="width:0;height:10px;font-size:0;background:#ff0000;"></div>
				</div>
			</td>
		</tr>
		</table>

		<br>
		<img src="../img/btn_sms_open.gif" style="border: 0px; cursor: pointer;" id="sms_sample_display" onclick="javascript:smsSample();" />
		<div id="sms_sample" style="display: none;">
		<span class="small"><font class="small" color="444444">�޼����� Ŭ���ϸ� �޼���â�� �ٷ� �Է��� �˴ϴ�</font></span>&nbsp;&nbsp;
		<a href="javascript:popupLayer('../member/sms.sample_reg.php?mode=sms_sample_reg');"><img src="../img/btn_smsadd.gif" align="absmiddle" /></a>

		<div style="height:5;font-size:0"></div>
		<table border="1" bordercolor="#dddddd" style="border-collapse:collapse;">
		<col align="center" span="10">
		<tr>
			<td width="100"><a href="sms.sample_list.php?ifrmScroll=1" target="ifrmSms"><font class="small1" color="161616">��ü����</font></a></td>
			<? $idx=1; foreach($r_sms_category as $v){ ?>
			<td width="100" height="25"><a href="sms.sample_list.php?ifrmScroll=1&category=<?=$v?>" target="ifrmSms"><font class="small" color="161616"><?=$v?></a> (<font color="0074ba"><b><?=number_format($cnt[$v])?></b></font>)</td>
			<? if (++$idx%6==0){ ?></tr><tr><? } ?>
			<? } ?>
		</tr>
		</table>

		<iframe id="ifrmSms" name="ifrmSms" src="../member/sms.sample_list.php?ifrmScroll=1" style="width:100%;height:350px;" frameborder="0" scrolling="no"></iframe>
		</div>
	</td>
</tr>
</table>
</div>

<script>
var msgReload	= '<?php echo $_POST[msgReload]; ?>';
var reMsgType = '<?php echo $reSmsType; ?>';

if(msgReload == 'y'){
	if(reMsgType == 'lms'){
		var lms_subject = document.getElementById("lms_subject");
		chkLength(lms_subject, 's');
		clearBg(lms_subject);
	}

	var msg = document.getElementById("msg");
	chkLength(msg, 'm');
	clearBg(msg);
}

table_design_load();
smsRecallColor('callback','<?echo str_replace("-","",$cfg[smsRecall])?>','<?echo @implode($callbackData, ",")?>');
</script>