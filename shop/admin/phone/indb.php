<?
include "../lib.php";
@include "../../conf/config.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

$mode = ($_POST[mode]) ? $_POST[mode] : $_GET[mode];

switch ($mode){
	case "register" :
			$coop_id = "godo1";
			$url = "https://www.pc080.net/cooperation/".$coop_id."/register_member.php";

			$tmp = array('user_id','email','pwd','user_name','tel');
			$arr['coop_id'] = $coop_id;
			$arr['user_id'] = 'godo'.$godo[sno];

			foreach($_POST as $k => $v) if(in_array($k,$tmp)) $arr[$k] = $v;

			$out = readpost($url, $arr);
			//$out = "result=0000&user_id=godo11343&email=birdmarine@naver.com&pc080_id=birdmarine@naver.com&user_tel=01075305656";
			if($out) @parse_str($out);
			unset($tmp);
			if($result == '0000' || $result == "Exist_member"){ //성공

				if(!$pc080_id)$pc080_id = $email;
				if(!$user_tel)$user_tel = $arr['tel'];

				$tmp = array(
					'coop_id' => $arr['coop_id'],
					'user_id'=> $arr['user_id'],
					'email' => $email,
					'user_name' => $arr['user_name'],
					'pc080_id' => $pc080_id,
					'pwd' => $_POST['pwd'],
					'user_tel' => $user_tel
				);

				$qfile->open("../../conf/phone.php");
				$qfile->write("<? \n");
				$qfile->write("\$set['phone'] = array( \n");
				if ($tmp) foreach ($tmp as $k=>$v) $qfile->write("'$k' => '$v', \n");
				$qfile->write(") \n;");
				$qfile->write("?>");
				$qfile->close();
				@chmod("../../conf/phone.php",0707);

				### xxx.txt파일 생성
				$tmp = 'Windows Registry Editor Version 5.00


						[HKEY_CURRENT_USER\Software\YouWin\pc080\Biz]
						"BizName"="eNamooPhone"
						"BizInfoUrl"="http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/partner/pc080_info.php"
						"BizInfoWidth"="300"
						"BizInfoHeight"="200"
						"MainWidth"="330"
						"MainHeight"="450"
						"GWNesUrl"="http://gongji.godo.co.kr/userinterface/pc080News.php"';

				$arr = explode("\n",$tmp);

				$qfile->open("../../conf/pc080.txt");
				$cnt = count($arr);
				foreach($arr as $k => $v){
					if($k != $cnt-1) $v = trim($v)."\n";
					else $v = trim($v);
					$qfile->write($v);
				}
				$qfile->close();
				@chmod("../../conf/pc080.txt",0707);

				msg('신청완료!!');
			}else{
				msg($result.' Error');
			}
		break;
	case "setting":
			@include "../../conf/phone.php";

			$tmp = array(
				'coop_id' => $set['phone']['coop_id'],
				'user_id'=> $set['phone']['user_id'],
				'email' => $_POST['email'],
				'user_name' => $_POST['user_name'],
				'pc080_id' => $_POST['email'],
				'pwd' => $_POST['pwd'],
				'user_tel' => $_POST['tel'],
				'icon' => $_POST['icon']
			);

			$qfile->open("../../conf/phone.php");
			$qfile->write("<? \n");
			$qfile->write("\$set['phone'] = array( \n");
			if ($tmp){
				foreach ($tmp as $k=>$v) $qfile->write("'$k' => '$v', \n");
			}
			$qfile->write(") \n;");
			$qfile->write("?>");
			$qfile->close();
			@chmod("../../conf/phone.php",0707);
			msg('세팅완료!!');

		break;

}
if($returl) go($returl);
else go($_SERVER[HTTP_REFERER]);
?>