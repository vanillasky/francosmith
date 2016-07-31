<?
@include "../../conf/sms/birth.php";
@include "../../conf/sms/birth_ok.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

if( $sms_auto['send_c'] == "on" ){
	# 생일자 추출
	$query = "select m_id,name,mobile,sms,calendar from ".GD_MEMBER." where birth='".date("md")."' AND sms = 'y' AND " . MEMBER_DEFAULT_WHERE;
	$res = $db->query($query);
	$birth['total']['cnt']	= $db->count_($res);
	$birth['sendN']['cnt']	= 0;
	while ($data=$db->fetch($res)){
		# SMS 수신, 양력, 핸드폰번호있는 회원
		if($data['sms'] == "y" && $data['calendar'] == "s" && $data['mobile'] != ""){
			$birth['sendY']['m_id'][]		= $data['m_id'];
			$birth['sendY']['name'][]		= $data['name'];
			$birth['sendY']['mobile'][]		= $data['mobile'];
		# 실패 회원
		}else{
			$birth['sendN']['cnt']			= $birth['sendN']['cnt'] + 1;
		}
	}
}
# 오전 09시 ~ 오전 12시 전까지 최초 관리자 접속고객에 한해 전송
if( $sms_auto['send_c'] == "on" && (date("H") >= "09" && date("H") < "12") ){

	# 기존 전송여부 체크
	if( $sms_auto['send_ok'] != date("Ymd") ){

		# 전송여부 저장
		$qfile->open("../../conf/sms/birth_ok.php");
		$qfile->write("<? \n");
		$qfile->write("\$sms_auto['send_ok'] = \"".date("Ymd")."\"; \n");
		$qfile->write("?>");
		$qfile->close();
		@chmod('../../conf/sms/birth_ok.php',0707);

		# 생일자 SMS 전송
		for ($i=0; $i < sizeof($birth['sendY']['m_id']); $i++){
			$smsData['m_id']	= $birth['sendY']['m_id'][$i];
			$smsData['name']	= $birth['sendY']['name'][$i];
			$smsData['mobile']	= $birth['sendY']['mobile'][$i];

			# SMS 전송
			$GLOBALS[dataSms] = $smsData;
			sendSmsCase('birth',$smsData['mobile']);
		}
	}
}
?>