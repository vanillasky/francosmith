<?
@include "../../conf/sms/birth.php";
@include "../../conf/sms/birth_ok.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

if( $sms_auto['send_c'] == "on" ){
	# ������ ����
	$query = "select m_id,name,mobile,sms,calendar from ".GD_MEMBER." where birth='".date("md")."' AND sms = 'y' AND " . MEMBER_DEFAULT_WHERE;
	$res = $db->query($query);
	$birth['total']['cnt']	= $db->count_($res);
	$birth['sendN']['cnt']	= 0;
	while ($data=$db->fetch($res)){
		# SMS ����, ���, �ڵ�����ȣ�ִ� ȸ��
		if($data['sms'] == "y" && $data['calendar'] == "s" && $data['mobile'] != ""){
			$birth['sendY']['m_id'][]		= $data['m_id'];
			$birth['sendY']['name'][]		= $data['name'];
			$birth['sendY']['mobile'][]		= $data['mobile'];
		# ���� ȸ��
		}else{
			$birth['sendN']['cnt']			= $birth['sendN']['cnt'] + 1;
		}
	}
}
# ���� 09�� ~ ���� 12�� ������ ���� ������ ���Ӱ��� ���� ����
if( $sms_auto['send_c'] == "on" && (date("H") >= "09" && date("H") < "12") ){

	# ���� ���ۿ��� üũ
	if( $sms_auto['send_ok'] != date("Ymd") ){

		# ���ۿ��� ����
		$qfile->open("../../conf/sms/birth_ok.php");
		$qfile->write("<? \n");
		$qfile->write("\$sms_auto['send_ok'] = \"".date("Ymd")."\"; \n");
		$qfile->write("?>");
		$qfile->close();
		@chmod('../../conf/sms/birth_ok.php',0707);

		# ������ SMS ����
		for ($i=0; $i < sizeof($birth['sendY']['m_id']); $i++){
			$smsData['m_id']	= $birth['sendY']['m_id'][$i];
			$smsData['name']	= $birth['sendY']['name'][$i];
			$smsData['mobile']	= $birth['sendY']['mobile'][$i];

			# SMS ����
			$GLOBALS[dataSms] = $smsData;
			sendSmsCase('birth',$smsData['mobile']);
		}
	}
}
?>