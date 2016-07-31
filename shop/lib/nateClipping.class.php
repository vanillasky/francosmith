<?php
class NateClipping extends LoadClass
{
	var $nateClipping;

	function NateClipping(){
		$l = dirname(__FILE__)."/../conf/nateClipping.cfg.php";
		if(file_exists($l))@require_once $l;
		if($nateClipping) $this->nateClipping = $nateClipping;
	}

	/* 서비스상태 */
	function open_state(){
		if($this->nateClipping['status'] == 3)return true;
		return false;
	}

	function get_BI($cfg){
		if($this->nateClipping['logo']){
			$tmp[0] = 'http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/data/skin/'.$cfg['tplSkin'].'/img/'.$this->nateClipping['logo'];
			$tmp[1] = 'http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'];
			return $tmp;
		}else{
			$tmp[0] = 'http://gongji.godo.co.kr/userinterface/clipping/images/logo_godo.gif';
			$tmp[1] = 'http://godo.co.kr/';
			return $tmp;
		}
	}

	function get_scrapBt($goodsno,$cfg){
		if($this->nateClipping['scrapBt']) $img="<img src='".$cfg['rootDir']."/data/skin/".$cfg['tplSkin']."/img/".$this->nateClipping['scrapBt']."' border='0'>";
		else $img = "<img src='".$cfg['rootDir']."/admin/img/natescrab_btn.gif' border='0'>";
		return "<a href=\"javascript:open_cyword();\">".$img."</a>";
	}

	function chk_goods($goodsno,$cfg,&$db){
		if(!$this->open_state())return false;
		$this->class[db] = &$db;
		$query = $this->get_xml_data($goodsno);
		$data = $this->class['db']->fetch($query);
		$r_img = explode("|",$data['img_m']);
		list($data['img'],$data['width'],$data['height'])=$this->get_img($r_img[0],$cfg['rootDir'],$cfg['img_m']);

		if(!$data['img']||!$data['height']||!$data['width'])return false;
		if(!$data['goodsnm'])return false;
		if($data['price']==null)return false;
		return true;
	}

	function get_xml_data($goodsno){
		if(!$this->open_state())return false;
		$query = "select * from ".GD_GOODS." a,".GD_GOODS_OPTION." b where a.goodsno=b.goodsno and b.link and go_is_deleted <> '1' and go_is_display = '1' and a.open and a.goodsno='$goodsno' limit 1";
		return $query;
	}

	function get_img($img,$rootDir,$img_m){
		$w = $this->nateClipping['imgWidth'];
		$h = $this->nateClipping['imgHeight'];
		if(!$w || !$h) $w = $h = $img_m;
		if(!preg_match('/http:\/\//',$img)){
			$img1 = "/data/goods/".$img;
			$img2 = dirname(__FILE__)."/../data/goods/".$img;
			if(file_exists($img2)) {
				$size = @getimagesize($img2);
				$w=$size[0];
				$h=$size[1];
				$imgUrl = "http://".$_SERVER['HTTP_HOST'].$rootDir.$img1;
			}
			return array($imgUrl,$w,$h);
		}else{
			return array($img,$w,$h);
		}
	}

	/* 스크랩이미지 업로드 */
	function upload_scrapBt($file,$logo,$width,$height,$proContents,$proContentsLink,$tplSkin){
		$arr = array();
		$target = dirname(__FILE__)."/../data/skin/".$tplSkin."/img/";
		$tmpData = dirname(__FILE__)."/../data/skin/";
		if(!$tplSkin||!$width||!$height)return false;

		$this->class_load('upload','upload_file');
		if($file[tmp_name]){
			$tmp = explode('.',$file[name]);
			$ext = $tmp[count($tmp)-1];
			$filename = "scrapBt.$ext";
			$this->class['upload']->upload_set($file,$target.$filename,'image');
			$this->class['upload']->upload();
			$this->nateClipping['scrapBt'] = $filename;
		}
		if($logo[tmp_name]){
			$tmp = explode('.',$logo[name]);
			$ext = $tmp[count($tmp)-1];
			$filename = "scrapLogo.$ext";
			@unlink($target.$filename);
			$this->class['upload']->upload_set($logo,$tmpData.$filename,'image');
			$this->class['upload']->upload();
			thumbnail($tmpData.$filename,$target.$filename,50,20,4);
			@unlink($tmpData.$filename);
			$this->nateClipping['logo'] = $filename;
		}

		$this->nateClipping['imgWidth'] = $width;
		$this->nateClipping['imgHeight'] = $height;
		$this->nateClipping['proContents'] = $proContents;
		$this->nateClipping['proContentsLink'] = $proContentsLink;

		$this->config_write($arr);
	}

	/* 서비스 설정 저장 */
	function config_write($arr){
		$fn = dirname(__FILE__)."/../conf/nateClipping.cfg.php";
		$this->nateClipping = array_merge($this->nateClipping,$arr);
		$this->nateClipping['updateDt'] = date('Y-m-d H:i:s',time());
		$this->class_load('qfile','qfile');
		$this->class['qfile']->open($fn);
		$this->class['qfile']->write("<? \n");
		$this->class['qfile']->write("\$nateClipping = array( \n");
		foreach ($this->nateClipping as $k=>$v) $this->class['qfile']->write("'$k'=>'$v',\n");
		$this->class['qfile']->write(") \n;");
		$this->class['qfile']->write("?>");
		$this->class['qfile']->close();
		@chmod($fn,0707);
	}

	function get_cyopenscrap($goodsno,$rootDir){
		$tmp = "http://".$_SERVER['HTTP_HOST'].$rootDir."/partner/nateClipping.engine.php?goodsno=$goodsno";
		$xmlurl = urlencode(trim($tmp));
		$url = "http://api.cyworld.com/openscrap/shopping/v1/?xu=".$xmlurl."&sid=".$this->nateClipping['sid'];
		return $url;
	}
}
?>