<?

include "../lib/library.php";
include_once dirname(__FILE__)."/../conf/config.php";

function chk_referer($url=""){
	if( preg_match('/'.addslashes($_SERVER['HTTP_HOST']).'/',$_SERVER['HTTP_REFERER']) ) $ret = true;
	else $ret = false;

	if( $ret && $url){
		$url = str_replace('/','\/',$url);
		if( preg_match('/'.$url.'/',$_SERVER['HTTP_REFERER']) )	$ret = true;
		else $ret = false;
	}
	return $ret;
}

$mode  = ($_POST[mode]) ? $_POST[mode] : $_GET[mode];

switch ($mode){

	case "sendmail":
		
		if(!chk_referer($cfg[rootDir].'/proc/popup_email.php')){			
			exit;
		}
		
		include "../lib/mail.class.php";

		$_POST[Subject] = stripSlashes($_POST[Subject]);

		$mail = new Mail($params);

		if(!$cfg['adminEmail']){
			msg("관리자 메일 주소가 없습니다.","close");
			exit;
		}

		$headers	= array(
					Name	=> "$_POST[Name]",
					From	=> "$_POST[From]",
					To		=> $cfg['adminEmail'],
					Subject	=> "$_POST[Subject]",
					);
		if ($mail->send($headers, $_POST[Body])) msg("정상적으로 메일이 발송되었습니다","close");
		else msg("메일 전송중 문제가 발생했습니다. 다시 보내주시기 바랍니다",-1);
		exit;
		break;

	case "send_sms":

		if(!chk_referer()){			
			exit;
		}
		
		include_once dirname(__FILE__)."/../lib/sms.class.php";
		$sms = new Sms();
		$sms_sendlist = $sms->loadSendlist();

		if( !trim($cfg['smsAdmin']) ){
			header("Status: 관리자 휴대폰 번호가 없습니다.", true, '400');
			echo ""; # 삭제금지
			exit;
		}
		

		if ($sms->smsPt<=0){
			header("Status: 잔여콜수 부족으로 문자를 발송할 수 없습니다.", true, '400');
			echo ""; # 삭제금지
			exit;
		}

		$sms->log($_GET[msg],$cfg[smsAdmin],'',1);
		$sms_sendlist->setSimpleInsert($cfg[smsAdmin], $sms->smsLogInsertId, '');
		if ($sms->send($_GET[msg],$cfg[smsAdmin],$_GET[callback])){
			$sms->update_ok_eNamoo = true;
			$sms->update();
		}
		else {
			header("Status: 문자 발송이 실패되었습니다.", true, '400');
			echo ""; # 삭제마요
		}

		exit;
		break;

}

?>