<?php
require "../lib.php";
require "../../lib/load.class.php";
require "../../lib/upload.lib.php";
require "../../lib/lib.enc.php";
require "../../lib/qfile.class.php";
require "../../lib/validation.class.php";
require "../../conf/config.php";

class DaumCpc extends LoadClass {
	var $daumCpc;
	var $shopPath;
	function daumCpc(){
		$this->shopPath = dirname(__FILE__)."/../../";
		$this->cfgPath = $this->shopPath."conf/daumCpc.cfg.php";
		if(file_exists($this->cfgPath)){
			require $this->cfgPath;
			$this->daumCpc = $daumCpc;
		}
	}
	function upload_logo_file($file,$mode){
		$uploadPath = $this->shopPath."data/";
		$this->class_load('upload','upload_file');
		$tmp = explode('.',$file['name']);
		$ext = $tmp[count($tmp)-1];
		$filename = "daumShopLogo".$mode.".".$ext;
		@unlink($uploadPath.$filename);
		$this->class['upload']->upload_set($file,$uploadPath."tmp".$filename,'image');
		$this->class['upload']->upload();
		thumbnail($uploadPath."tmp".$filename,$uploadPath.$filename,65,15,4);
		@unlink($uploadPath."tmp".$filename);
		return $filename;
	}
	function regist_logo_file(){
		$file = reverse_file_array($_FILES['file']);
		foreach($file as $v)if(!$v['tmp_name'])return false;
		foreach($file as $k => $v)
			$this->daumCpc['logo'.$k] = $this->upload_logo_file($v,$k);
		$this->configration();
		return true;
	}
	function configration(){
		$this->class_load('qfile','qfile');
		$this->class['qfile']->open($this->cfgPath);
		$this->class['qfile']->write("<? \n");
		$this->class['qfile']->write("\$daumCpc = array( \n");
		foreach ($this->daumCpc as $k=>$v) $this->class['qfile']->write("'$k'=>'$v',\n");
		$this->class['qfile']->write(") \n;");
		$this->class['qfile']->write("?>");
		$this->class['qfile']->close();
		@chmod($this->cfgPath,0707);
	}
	function chk_regist($arr){
		$this->class_load('Validation','Validation');
		if($arr['service_agreYn'] == 'no') return "약관에 동의 하셔야 합니다.";
		if(!$this->class['Validation']->check_require($arr['shop_sno'])) return "접속경로가 올바르지 않습니다.";
		if(!$this->class['Validation']->check_require($arr['mall_id'])) return "접속경로가 올바르지 않습니다.";
		if(!$this->class['Validation']->check_require($arr['shop_sno'])) return "접속경로가 올바르지 않습니다.";
		if(!$this->class['Validation']->check_require($arr['loginid'])) return "다음 통합 광고주 아이디는 필수 입니다.";
		if(!$this->class['Validation']->check_require($arr['shopname'])) return "쇼핑몰명(한글)은 필수 입니다.";
		if(!$this->class['Validation']->check_require($arr['shopengname'])) return "쇼핑몰명(영문)은 필수 입니다.";
		if(!$this->class['Validation']->check_require($arr['categoryid'])) return "대표카테고리는 필수 입니다.";
		if(!$this->class['Validation']->check_require($arr['corppt'])) return "회사소개는 필수 입니다.";
		$tel = @implode('',$arr['tel']);
		if(!$this->class['Validation']->check_require($tel)) return "대표 전화번호는 필수 입니다.";
		if(!$this->class['Validation']->check_digit($tel)) return "대표 전화번호가 올바르지 않습니다.";
		$cstel = @implode('',$arr['cstel']);
		if(!$this->class['Validation']->check_require($cstel)) return "고객센터 전화번호는 필수 입니다.";
		if(!$this->class['Validation']->check_digit($cstel)) return "고객센터 전화번호가 올바르지 않습니다.";
		$csmail = @implode('@',$arr['csmail']);
		if(!$this->class['Validation']->check_require($csmail)) return "고객문의 메일는 필수 입니다.";
		if(!$this->class['Validation']->check_email($csmail)) return "고객문의 메일이 올바르지 않습니다.";
		if(!$this->class['Validation']->check_require($arr['joname'])) return "담당자명은 필수 입니다.";
		$jotel = @implode('',$arr['jotel']);
		if(!$this->class['Validation']->check_require($jotel)) return "담당자 전화번호는 필수 입니다.";
		if(!$this->class['Validation']->check_digit($jotel)) return "담당자 전화번호가 올바르지 않습니다.";
		$johpnum = @implode('',$arr['johpnum']);
		if(!$this->class['Validation']->check_require($johpnum)) return "담당자 핸드폰는 필수 입니다.";
		if(!$this->class['Validation']->check_digit($johpnum)) return "담당자 핸드폰가 올바르지 않습니다.";
		$jomail = @implode('@',$arr['jomail']);
		if(!$this->class['Validation']->check_require($jomail)) return "담당자 이메일는 필수 입니다.";
		if(!$this->class['Validation']->check_email($jomail)) return "담당자 이메일이 올바르지 않습니다.";
		return false;
	}
}

if($_POST['mode'])$mode = $_POST['mode'];
else $mode = $_GET['mode'];
$daumCpc = new DaumCpc;
switch($mode){
	case "regist":
		// 파라미터 검증
		$msg = $daumCpc -> chk_regist($_POST);
		if($msg)msg($msg,0);

		// 로고이미미지 처리
		if(!$daumCpc -> regist_logo_file()) msg("로고 이미지는 필수 입니다.",0);

		// 파라미터 변수 재가공
		foreach($_POST as $k => $v){
			if(is_array($v)) foreach($v as $k1 => $v1){
				$data[$k][$k1] = godoConnEncode($v1);
			}else{
				$data[$k] = godoConnEncode($v);
			}
		}
		$logourl = "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/data/";
		$data['logo0'] = ($daumCpc->daumCpc['logo0'] ? godoConnEncode($logourl.$daumCpc->daumCpc['logo0']) : '');
		$data['logo1'] = ($daumCpc->daumCpc['logo1'] ? godoConnEncode($logourl.$daumCpc->daumCpc['logo1']) : '');
		$data['ip'] = godoConnEncode($_SERVER['REMOTE_ADDR']);

		// 서비스신청
		$out = readpost("http://gongji.godo.co.kr/userinterface/daum_cpc/cpc_indb.php", $data, $port=80);
		if($out == 1){
			echo("<script>parent.location.reload();</script>");
		}
		break;
	case "daumCpc":
		$daumCpc -> daumCpc['useYN'] = $_POST['daumCpc']['useYN'];
		$daumCpc -> daumCpc['nv_pcard'] = $_POST['daumCpc']['nv_pcard'];
		$daumCpc -> daumCpc['goodshead'] = $_POST['daumCpc']['goodshead'];
		$daumCpc -> configration();
		msg("설정 되었습니다.",0);
		break;
}
?>