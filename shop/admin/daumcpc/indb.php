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
		if($arr['service_agreYn'] == 'no') return "����� ���� �ϼž� �մϴ�.";
		if(!$this->class['Validation']->check_require($arr['shop_sno'])) return "���Ӱ�ΰ� �ùٸ��� �ʽ��ϴ�.";
		if(!$this->class['Validation']->check_require($arr['mall_id'])) return "���Ӱ�ΰ� �ùٸ��� �ʽ��ϴ�.";
		if(!$this->class['Validation']->check_require($arr['shop_sno'])) return "���Ӱ�ΰ� �ùٸ��� �ʽ��ϴ�.";
		if(!$this->class['Validation']->check_require($arr['loginid'])) return "���� ���� ������ ���̵�� �ʼ� �Դϴ�.";
		if(!$this->class['Validation']->check_require($arr['shopname'])) return "���θ���(�ѱ�)�� �ʼ� �Դϴ�.";
		if(!$this->class['Validation']->check_require($arr['shopengname'])) return "���θ���(����)�� �ʼ� �Դϴ�.";
		if(!$this->class['Validation']->check_require($arr['categoryid'])) return "��ǥī�װ��� �ʼ� �Դϴ�.";
		if(!$this->class['Validation']->check_require($arr['corppt'])) return "ȸ��Ұ��� �ʼ� �Դϴ�.";
		$tel = @implode('',$arr['tel']);
		if(!$this->class['Validation']->check_require($tel)) return "��ǥ ��ȭ��ȣ�� �ʼ� �Դϴ�.";
		if(!$this->class['Validation']->check_digit($tel)) return "��ǥ ��ȭ��ȣ�� �ùٸ��� �ʽ��ϴ�.";
		$cstel = @implode('',$arr['cstel']);
		if(!$this->class['Validation']->check_require($cstel)) return "������ ��ȭ��ȣ�� �ʼ� �Դϴ�.";
		if(!$this->class['Validation']->check_digit($cstel)) return "������ ��ȭ��ȣ�� �ùٸ��� �ʽ��ϴ�.";
		$csmail = @implode('@',$arr['csmail']);
		if(!$this->class['Validation']->check_require($csmail)) return "������ ���ϴ� �ʼ� �Դϴ�.";
		if(!$this->class['Validation']->check_email($csmail)) return "������ ������ �ùٸ��� �ʽ��ϴ�.";
		if(!$this->class['Validation']->check_require($arr['joname'])) return "����ڸ��� �ʼ� �Դϴ�.";
		$jotel = @implode('',$arr['jotel']);
		if(!$this->class['Validation']->check_require($jotel)) return "����� ��ȭ��ȣ�� �ʼ� �Դϴ�.";
		if(!$this->class['Validation']->check_digit($jotel)) return "����� ��ȭ��ȣ�� �ùٸ��� �ʽ��ϴ�.";
		$johpnum = @implode('',$arr['johpnum']);
		if(!$this->class['Validation']->check_require($johpnum)) return "����� �ڵ����� �ʼ� �Դϴ�.";
		if(!$this->class['Validation']->check_digit($johpnum)) return "����� �ڵ����� �ùٸ��� �ʽ��ϴ�.";
		$jomail = @implode('@',$arr['jomail']);
		if(!$this->class['Validation']->check_require($jomail)) return "����� �̸��ϴ� �ʼ� �Դϴ�.";
		if(!$this->class['Validation']->check_email($jomail)) return "����� �̸����� �ùٸ��� �ʽ��ϴ�.";
		return false;
	}
}

if($_POST['mode'])$mode = $_POST['mode'];
else $mode = $_GET['mode'];
$daumCpc = new DaumCpc;
switch($mode){
	case "regist":
		// �Ķ���� ����
		$msg = $daumCpc -> chk_regist($_POST);
		if($msg)msg($msg,0);

		// �ΰ��̹̹��� ó��
		if(!$daumCpc -> regist_logo_file()) msg("�ΰ� �̹����� �ʼ� �Դϴ�.",0);

		// �Ķ���� ���� �簡��
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

		// ���񽺽�û
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
		msg("���� �Ǿ����ϴ�.",0);
		break;
}
?>