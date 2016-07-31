<?
include "../lib.php";
include "../../lib/json.class.php";

header('Content-Type: text/html; charset=euc-kr');

if( $_GET['mode'] == "view" ){

	$Query = "
		select 
			*
		from
			gd_diaryContent
		where
			diary_date = '$_GET[date]'
	";
	$row = $db->fetch($Query);

	$Query_sub = "
		select
			alarmtype_popup,
			alarmtype_sms
		from
			gd_diaryAlarm
		where
			sno = '1'
	";
	$_info = $db->fetch($Query_sub);

	$_value_array = array();
	$_value_array[mode] = $_GET['mode'];
	$_value_array[data] = $row;
	$_value_array[info] = $_info;
	$_value_array[ndate] = $_GET['date'];


	$json = new Services_JSON();
	$output = $json->encode($_value_array);

	echo $output;
	exit();
}


//등록!!
if( $_GET['mode'] == "new" ){
	
	if( $_GET['ch_mode'] == "new" ){ //등록

			$Query = "
				insert into
					gd_diaryContent
				set
					diary_date = '$_GET[date]',
					diary_title = '$_GET[diary_title]',
					diary_content = '$_GET[diary_content]',
					diary_alarm = '$_GET[diary_alarm]',
					diary_regdt = now()
			";
			$db->query($Query);

	}else{ //수정
			$Query = "
				update
					gd_diaryContent
				set
					diary_title = '$_GET[diary_title]',
					diary_content = '$_GET[diary_content]',
					diary_alarm = '$_GET[diary_alarm]'
				where
					diary_date = '$_GET[date]'
			";
			$db->query($Query);
	}

		//sms보내기!!
		$sms_sendok = 'ing';
		if( $_GET['diary_alarm'] == "y" || $_GET['diary_alarm'] == "sy" ){
			$sms_fc = alram_Sms($_GET['diary_title'],$_GET['date'],$_GET['ch_mode']);
			$sms_sendok = $sms_fc;
		}
		//sms보내기!! end

	$_value_array = array();
	$_value_array['mode'] = $_GET['mode'];
	$_value_array['ndate'] = $_GET['date'];
	$_value_array['ch_mode'] = $_GET['ch_mode'];
	$_value_array['sms_sendok'] = $sms_sendok;

	$json = new Services_JSON();
	$output = $json->encode($_value_array);

	echo $output;
	exit();
}

//해당 달에 등록된 정보
if( $_GET['mode'] == "month_info" ){

	$DateStr = explode("/", $_GET['date']);
	$mDate = sprintf("%02d", $DateStr[1]+1);
	$getDate = $DateStr[0].$mDate;

	$Query = "
		select 
			right( diary_date, 2 ) as diary_date,
			diary_title,
			diary_content
		from
			gd_diaryContent
		where
			 left(diary_date,6) = '$getDate'
		order by diary_date asc
	";
	$res = $db->query( $Query );

	$_info = array();
	$no = 0;
	while( $row = $db->fetch($res) ){
		$_info[$no] = $row;
		$no++;
	}
	$_value_array = array();
	$_value_array[mode] = $_GET['mode'];
	$_value_array[data] = $_info;
	$_value_array[ndate] = $_GET['date'];

	$json = new Services_JSON();
	$output = $json->encode($_value_array);

	echo $output;
	exit();
}


//삭제
if( $_GET['mode'] == "delete" ){

	$Query = "
		delete from 
			gd_diaryContent
		where
			sno = '$_GET[sno]'
	";
	$db->query($Query);

		//sms삭제!!
			$sms_fc = alram_Sms('',$_GET['date'],'delete');
			$sms_sendok = $sms_fc;
		//sms삭제!! end

	$_value_array = array();
	$_value_array[mode] = $_GET['mode'];
	$_value_array[ndate] = $_GET['date'];

	$json = new Services_JSON();
	$output = $json->encode($_value_array);

	echo $output;
	exit();
}

if( $_GET['mode'] == "alarm" ){

	$phone = $_GET['phone1'].$_GET['phone2'].$_GET['phone3'];

	$Query = "
		update
			gd_diaryAlarm
		set
			alarmtype_popup = '$_GET[alarmtype_popup]',
			dday = '$_GET[dday]',
			alarmtype_sms = '$_GET[alarmtype_sms]',
			dday_sms = '$_GET[dday_sms]',
			dday_smsTime = '$_GET[dday_smsTime]',
			 phone  = '$phone'
		where
			sno = '1'
	";
	$db->query($Query);
	$_info = array();
	$_info['alarmtype_popup'] = $_GET['alarmtype_popup'];
	$_info['dday'] = $_GET['dday'];
	$_info['alarmtype_sms'] = $_GET['alarmtype_sms'];
	$_info['dday_sms'] = $_GET['dday_sms'];
	$_info['dday_smsTime'] = $_GET['dday_smsTime'];
	$_info['phone1'] = $_GET['phone1'];
	$_info['phone2'] = $_GET['phone2'];
	$_info['phone3'] = $_GET['phone3'];

	$_value_array = array();
	$_value_array['mode'] = $_GET['mode'];
	$_value_array['data'] = $_info;

	$json = new Services_JSON();
	$output = $json->encode($_value_array);

	echo $output;
	exit();
}

if( $_GET['mode'] == "alarm_view" ){
	include "../../conf/godomall.cfg.php";

	$Query = "
		select
			*
		from
			gd_diaryAlarm
		where
			sno = '1'
	";
	$row = $db->fetch($Query);
	
	$_value_array = array();
	$_value_array['mode'] = $_GET['mode'];
	$_value_array['data'] = $row;
	$_value_array['godosms'] = getSmsPoint();
	$json = new Services_JSON();
	$output = $json->encode($_value_array);

	echo $output;
	exit();
}

if( $_GET['mode'] == "setCookie" ){
	$nowDay = date("Ymd");
	setCookie("Alarm_popID",$nowDay,time()+86400*1,"/");
	
	if( $_COOKIE['Alarm_popID'] ) echo "ok";
	else echo "err";
}


function alram_Sms($diary_title,$date,$type){

	global $db;

	include "../../lib/sms.class.php";

	$phon_Query = $db->query("select dday_sms,dday_smsTime,phone from gd_diaryAlarm where sno = '1'");
	$phon = $db->fetch($phon_Query);
	
	$sms = new Sms(true);

	$send_datey=substr($_GET['date'],0,4);
	$send_datem=substr($_GET['date'],4,2);
	$send_dated=substr($_GET['date'],6,2);
	
	$alram_Time = mktime($phon['dday_smsTime'], 0, 0, $send_datem, $send_dated, $send_datey);
	if( $phon['dday_sms'] == "2") $alram_Dday = ( $alram_Time ) - ( 86400 * 1 );
	else if( $phon['dday_sms'] == "3") $alram_Dday = ( $alram_Time ) - ( 86400 * 2 );
	else if( $phon['dday_sms'] == "4") $alram_Dday = ( $alram_Time ) - ( 86400 * 3 );
	else $alram_Dday = $alram_Time;
	$alram_Date = date('Y-m-d H:i:s',$alram_Dday);

	$alram_etc = $date;
	if( $alram_Dday > time() ){ 
		$sms->sms_Control($phon['phone'],$diary_title,$alram_Date,$alram_etc,$type);
		$sms_sendok = 'ing';
	}
	else $sms_sendok = "n";

	return $sms_sendok;
}
?>
