<div style="position:relative;z-index:5;">
<div style="display:none;position:absolute;top:0;left:180;" id="malldiary_formID">

<table width="310" border="0" cellpadding="0" cellspacing="2" bgcolor='9F9F9F'>
<form name="malldiary_Fm" method="get" action="">
<input type="hidden" name="ch_mode" value="">
<input type="hidden" name="date" value="">
<input type="hidden" name="sno" value="">
	<tr bgcolor='#ffffff'>
		<td>
		<table width="310" border="0" cellpadding="0" cellspacing="0" bgcolor='#FFFFFF'>
			<tr>
				<td style="font-size:8pt;font-family:����;color=636363" align="center" height='27' bgcolor="#F9F9F9"><b><span id='ndateID'></span>&nbsp;����</b></td>
			</tr>
			<!--<tr><td bgcolor='585858' height='1'></td></tr>-->
			<tr>
				<td>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="15%" align=right style="font-size:8pt;font-family:����;color=636363;padding-right:10px" height='25'>����</td>
						<td width="85%" style="font-size:8pt;font-family:����;color=636363"><input type="text" name="diary_title" value="" style="font-size:8pt;font-family:����;color=636363" size='39' maxlength="20"></td>
					</tr>
					<!--<tr><td colspan="2" bgcolor='#CACACA' height='1'></td></tr>-->
					<tr>
						<td align=right style="font-size:8pt;font-family:����;color=636363;padding-right:10px;" height='25'>����</td>
						<td>
							<textarea name="diary_content" style="width:244; height:100;font-size:8pt;font-family:����;color:7F7F7F" onKeyUp="CheckLen(this.form)" onfocus="contents_close();"></textarea>
						</td>
					</tr>
					<!--<tr><td colspan="2" bgcolor='#CACACA' height='1'></td></tr>-->
					<tr>
						<td align=right style="font-size:8pt;font-family:����;color=636363;padding-right:10;padding-top:4;letter-spacing:-1px;" height='25'>�˶�</td>
						<td class="noline" style="font-size:8pt;font-family:����;color=7F7F7F;letter-spacing:-1px;"><input type="radio" name="diary_alarm" value="y" checked>�ѱ� ( <span id='alarmMsgID'></span> )&nbsp;<input type="radio" name="diary_alarm" value="n">����
					</td>
					</tr>
					<tr><td colspan="2" bgcolor='#F9F9F9' height='25' align="right" style="padding-right:20;padding-top:4"><a href="javascript:form_check();"><img src="../img/btn_daily_regist.gif" border=0></a>&nbsp;<a href="javascript:del();"><img src="../img/btn_daily_del.gif" border=0></a>&nbsp;<a href="javascript:div_close('malldiary_formID');"><img src="../img/btn_daily_close.gif" border=0></a></td></tr>
				</table>
				</td>
			</tr>
		</table>
		</td>
<!--	<td width="180" style="font-size:8pt;font-family:����;color=7F7F7F;padding-left:5px;padding-top:5px" valign='top'>
		* <b>�˶������� �켱</b><br>�˶��⺻�������� ������<br>�α��ν� �Ǵ� sms �˶�������<br> �ϼž� �մϴ�.<br>
		* <b>sms �˶����� ��</b><br>������ ��������� �˶����������� sms����߼� �Ǹ� ������ ������ ������ ���� ��� �߼۵˴ϴ�.<br>
		* <b>����������</b>���� ��ϵ� sms������ �������� �ʽ��ϴ�.
		</td>
-->
	</tr>
</form>
</table>
</div>
</div>


<div style="position:relative;">
<div style="display:none;position:absolute;top:1;left:98;" id="alarm_formID">
<table width="365" border="0" cellpadding="0" cellspacing="0" bgcolor='#FFFFFF' style='border:2 solid #9F9F9F'>
<form name="alarm_Fm" method="get" action="">
	<tr>
		<td colspan='2' style="font-size:8pt;font-family:����;color=636363" align="center" height='35' bgcolor="#F9F9F9"><b>�˶�����</b></td>
	</tr>
	<tr>
		<td class="noline" width='130' height='25' style="font-size:8pt;font-family:����;color=636363;letter-spacing:-1px;padding-left:20"><input type="checkbox" name="alarmtype_popup" value="y" checked> <font color=#0074BA>������ �α��ν�</td>
		<td width="270" style="font-size:8pt;font-family:����;color=636363">
			<select name="dday" style="font-family:����, ����; width:100">
				<option value="1">���� ����</option>
				<option value="2">���� 1����</option>
				<option value="3">���� 2����</option>
				<option value="4">���� 3����</option>
			</select> <font color=#0074BA>�˾�â�� ���ϴ�.
		</td>
	</tr>
	<tr><td colspan='2' bgcolor='#E4E4E4' height='0'></td></tr>
	<tr>
		<td class="noline" height='25' colspan='2' style="font-size:8pt;font-family:����;color=636363;letter-spacing:-1px;padding-left:20"><input type="checkbox" name="alarmtype_sms" value="y"><font color=#0074BA> SMS �˸� ��ɻ��</font> &nbsp;(SMS ����Ʈ������ �Ǿ��־�� �����մϴ�)</td>
	</tr>
	<tr>
		<td colspan='2' style="padding-left:22px;">
			<select name="dday_sms" style="font-family:����, ����; width:100">
				<option value="1">���� ����</option>
				<option value="2">���� 1����</option>
				<option value="3">���� 2����</option>
				<option value="4">���� 3����</option>
			</select>&nbsp;
			<select name="dday_smsTime" style="font-family:����, ����; width:50">
			<?for($i=1; $i < 25; $i++){?>
				<option value="<?=$i?>"><?=$i?>��</option>���̿� �߼�
			<?}?>
			</select>&nbsp;
			<select name="phone1" style="font-family:����, ����; width:50">
				<option value="010">010</option>
				<option value="011">011</option>
				<option value="016">016</option>
				<option value="017">017</option>
				<option value="018">018</option>
				<option value="019">019</option>
			</select> -
			<input type="text" name="phone2" value="" style='font-family:����, ����; width:30'> -
			<input type="text" name="phone3" value="" style='font-family:����, ����; width:30'>
		</td>
	</tr>
	<tr><td colspan='2' height='5'></td></tr>
	<tr><td colspan='2' bgcolor='#E4E4E4' height='1'></td></tr>
	<tr><td colspan='2' height='5'></td></tr>
	<tr><td colspan='2' align="center" height='20'><b><a href="javascript:diary_Request('','alarm')"><img src="../img/btn_daily_save.gif" border=0></a></b>&nbsp;<a href="javascript:div_close('alarm_formID');"><img src="../img/btn_daily_close.gif" border=0></a></td></tr>
	<tr><td colspan='2' height='5'></td></tr>
</form>
</table>
</div>
</div>

<?
//�˶����� �˾�����!!

$alram_popQuery = "
	select
		alarmtype_popup,
		dday
	from
		gd_diaryAlarm
	where
		sno = '1'
";
$_popinfo = $db->fetch($alram_popQuery);

if( $_popinfo['alarmtype_popup'] == "y" ){

	if( $_popinfo['dday'] == "2") $pop_Dday = ( time() ) + ( 86400 * 1 );
	else if( $_popinfo['dday'] == "3") $pop_Dday = ( time() ) + ( 86400 * 2 );
	else if( $_popinfo['dday'] == "4") $pop_Dday = ( time() ) + ( 86400 * 3 );
	else $pop_Dday = time();
	$pop_rDay = date('Ymd' , $pop_Dday);

	$diary_popQuery = "
		select
			*
		from
			gd_diaryContent
		where
			diary_date = '$pop_rDay'
	";
	$diary_row = $db->fetch($diary_popQuery);
	if( $diary_row['0'] ){
		//�˶� �˾�â ȣ��!!
		$nowDay = date("Ymd");
		if( $_COOKIE['Alarm_popID'] != $nowDay ) echo "<script>alarm_pop('".$pop_rDay."');</script>";
	}
}
?>