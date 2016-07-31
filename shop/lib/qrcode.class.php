<?php
if (class_exists('QRCode', false)) return;
class QRCode {
	var $qr_frame_tag;
	var $qrCfg;
	var $fpath;
	var $logoData;
	var $imgURL;

	function QRCode(){
		GLOBAL $cfg;
		$cfgfile = dirname(__FILE__)."/../conf/config.php";
		if(file_exists($cfgfile)) @require_once $cfgfile;
		if($cfg) $this->cfg = $cfg;
		$this->imgURL = "/shop/data/skin/".$this->cfg['tplSkin']."/img/common";
		GLOBAL $qrCfg;
		$cfgfile = dirname(__FILE__)."/../conf/qr.cfg.php";
		if(file_exists($cfgfile)) @require_once $cfgfile;
		if($qrCfg)	$this->qrCfg = $qrCfg;
	}

	function get_GoodsViewTag($contsNo,$useType, $qrdata=null)
	{
		global $qrCfg;
		$this->fpath = dirname(__FILE__);
		if(!$qrCfg):
			if(file_exists($fpath."../conf/qr.cfg.php"))
				require $fpath."../conf/qr.cfg.php";
		endif;
		$this->qrCfg = $qrCfg;

		if($this->qrCfg['useLogo'] == "y")$this->logoData = "&useLogo=".$this->qrCfg['useLogo']."&logoImg=".$this->qrCfg['logoImg']."&logoLocation=".$this->qrCfg['logoLocation']."&degree=".number_format($this->qrCfg['degree']);

		switch ($useType){
			case "goods_view" :
				return $this->goods_qr_view($contsNo);
			break;
			case "goods_down" :
				return $this->goods_qr_down($contsNo);
			break;
			case "event_view" :
				return $this->event_qr_view($contsNo);
			break;
			case "event_down" :
				return $this->event_qr_down($contsNo);
			break;
			case "etc_view" :
				return $this->etc_qr_view($qrdata);
			break;
			case "etc_down" :
				return $this->etc_qr_down($qrdata);
			break;
		}
	}
	//상품 상세보기 태그
	function goods_qr_view($contsNo)
	{
		if($this->qrCfg['useGoods'] == "y"){
			$qr_data = "http://".$_SERVER["SERVER_NAME"]."/shop/goods/goods_view.php?goodsno=".$contsNo;
			$qr_maker_path = "/shop/lib/qrcodeImgMaker.php?s=2&d=".$qr_data."&o=".$qr_data.$this->logoData;
			$qr_iframe = "<iframe src='".$qr_maker_path."' marginheight='0' marginwidth='0' frameBorder='0' scrolling='no' allowTransparency='true' width='130' height='130'></iframe>";

			$qr_frame_tag .= "<div style='width=200' align='center'><table cellpadding='0' cellspacing='0' border='0'>";
			$qr_frame_tag .= "<tr>";
			$qr_frame_tag .= "	<td>";
			$qr_frame_tag .= "		<table cellpadding='0' cellspacing='0' border='0'>";
			$qr_frame_tag .= "			<tr>";
			$qr_frame_tag .= "				<td>".$qr_iframe."</td>";
			$qr_frame_tag .= "				<td>";

			if($this->qrCfg['qr_style'] == "btn"){
				$qr_frame_tag .= "<div><img src='".$this->imgURL."/page02_btn_qr.gif' onclick='javascript:qr_explain(event)' style='cursor:hand'></div>";
				$qr_frame_tag .= "<div><a href='".$qr_maker_path."&qr_proc=down'><img src='".$this->imgURL."/page02_btn_pc.gif'></a></div>";
			}

			$qr_frame_tag .= "				</td>";
			$qr_frame_tag .= "			</tr>";
			$qr_frame_tag .= "		</table>";
			$qr_frame_tag .= "	</td>";
			$qr_frame_tag .= "</tr>";
			$qr_frame_tag .= "</table></div>";

			return $qr_frame_tag;
		}else{
			return;
		}
	}	
	// 관리자 페이지에서 저장
	function goods_qr_down($contsNo)
	{
		if($this->qrCfg['useGoods'] == "y"){
			$qr_data = "http://".$_SERVER["SERVER_NAME"]."/shop/goods/goods_view.php?goodsno=".$contsNo;
			$qr_maker_path = "/shop/lib/qrcodeImgMaker.php?qr_proc=down&s=3&d=".$qr_data."&o=".$qr_data.$this->logoData;
			$qr_frame_tag = "&nbsp;&nbsp;&nbsp;<a href='".$qr_maker_path."'><img src='".$this->imgURL."/page02_btn_pc.gif' style='vertical-align:bottom'></a>";
			
			return $qr_frame_tag;
		}else{
			return;
		}
	}
	//이벤트 상세보기 태그
	function event_qr_view($contsNo)
	{
		$imgURL= "/shop/skin/".$this->cfg['tplSkin']."/img/common";

		
		if($this->qrCfg['useEvent'] == "y"){
			$qr_data = "http://".$_SERVER["SERVER_NAME"]."/shop/goods/goods_event.php?sno=".$contsNo;
			$qr_maker_path = "/shop/lib/qrcodeImgMaker.php?s=2&d=".$qr_data."&o=".$qr_data.$this->logoData;
			$qr_iframe = "<iframe src='".$qr_maker_path."' marginheight='0' marginwidth='0' frameBorder='0' scrolling='no' allowTransparency='true' width='130' height='130'></iframe>";

			$qr_frame_tag .= "<table cellpadding='0' cellspacing='0' border='0'><tr><td align='center'>";
			$qr_frame_tag .= "<div align='center'><table cellpadding='0' cellspacing='0' border='0'>";
			$qr_frame_tag .= "<tr>";
			$qr_frame_tag .= "	<td width='7' height='6' background='".$this->imgURL."/page02_box_top01.gif'></td>";
			$qr_frame_tag .= "	<td height='6' background='".$this->imgURL."/page02_box_top02.gif'></td>";
			$qr_frame_tag .= "	<td width='6' height='6' background='".$this->imgURL."/page02_box_top03.gif'></td>";
			$qr_frame_tag .= "  </tr>";
			$qr_frame_tag .= "<tr>";
			$qr_frame_tag .= "	<td width='7' background='".$this->imgURL."/page02_box_left_bg.gif'></td>";
			$qr_frame_tag .= "	<td>";
			$qr_frame_tag .= "		<table cellpadding='0' cellspacing='0' border='0'>";
			$qr_frame_tag .= "			<tr>";
			$qr_frame_tag .= "				<td>".$qr_iframe."</td>";
			$qr_frame_tag .= "				<td>";

			if($this->qrCfg['qr_style'] == "btn"){
				$qr_frame_tag .= "<div><img src='".$this->imgURL."/page02_btn_qr.gif' onclick='javascript:qr_explain(event)' style='cursor:hand'></div>";
				$qr_frame_tag .= "<div><a href='".$qr_maker_path."&qr_proc=down'><img src='".$this->imgURL."/page02_btn_pc.gif'></a></div>";
			}

			$qr_frame_tag .= "				</td>";
			$qr_frame_tag .= "			</tr>";
			$qr_frame_tag .= "		</table>";
			$qr_frame_tag .= "	</td>";
			$qr_frame_tag .= "	<td width='6' background='".$this->imgURL."/page02_box_rigth_bg.gif'></td>";
			$qr_frame_tag .= "</tr>";
			$qr_frame_tag .= "<tr>";
			$qr_frame_tag .= "	<td width='7' height='6' background='".$this->imgURL."/page02_box_foot01.gif'></td>";
			$qr_frame_tag .= "	<td height='6' background='".$this->imgURL."/page02_box_foot02.gif'></td>";
			$qr_frame_tag .= "	<td width='6' height='6' background='".$this->imgURL."/page02_box_foot03.gif'></td>";
			$qr_frame_tag .= "  </tr>";
			$qr_frame_tag .= "</div></table>";
			$qr_frame_tag .= "</td></tr></table><div style='height:5px'></div>"; 

			return $qr_frame_tag;
		}else{
			return;
		}
	}
	//관리자 페이지에서 저장
	function event_qr_down($contsNo)
	{
		if($this->qrCfg['useEvent'] == "y"){
			$qr_data = "http://".$_SERVER["SERVER_NAME"]."/shop/goods/goods_event.php?sno=".$contsNo;
			$qr_maker_path = "/shop/lib/qrcodeImgMaker.php?qr_proc=down&s=3&d=".$qr_data."&o=".$qr_data.$this->logoData;
			$qr_frame_tag = "&nbsp;&nbsp;&nbsp;<a href='".$qr_maker_path."'><img src='".$this->imgURL."/page02_btn_pc.gif' style='vertical-align:bottom'></a>";
			
			return $qr_frame_tag;
		}else{
			return;
		}
	}
	//기타 url,명함 만들기
	function etc_qr_view($qrdata)
	{
		$qr_maker_path = "/shop/lib/qrcodeImgMaker.php?".$qrdata.$this->logoData;
		$qr_info_tag = "<a href=''>QR Code 란?</a>";
		$qr_frame_tag .=	 "<div><table><tr><td><div style='text-align:center'>스마트폰으로<br> QR코드를 찍어보세요<br><br>".$qr_info_tag."</div></td><td align='center'><div>";
		$qr_frame_tag .= "<iframe src='".$qr_maker_path."' marginheight='0' marginwidth='0' frameBorder='0' scrolling='no' allowTransparency='true' width='130' height='130'></iframe>";
		$qr_frame_tag .= "</div></td></tr></table></div>";
	}
	//기타 url,명함 저장
	function etc_qr_down($qrdata)
	{
		$qr_maker_path = "/shop/lib/qrcodeImgMaker.php?qr_proc=down&".$qrdata.$this->logoData;
		$qr_frame_tag = "&nbsp;<a href='".$qr_maker_path."'><img src='".$this->imgURL."/page02_btn_pc.gif' style='vertical-align:bottom'></a>";
			
		return $qr_frame_tag;
	}

	/* 서비스 설정 저장 */
	function config_write($arr){
		$fn = dirname(__FILE__)."/../conf/qr.cfg.php";
		foreach ($arr as $k=>$v) {
			$arr[$k] = htmlspecialchars(stripslashes($v));
		}
		$this->qrCfg = array_merge($this->qrCfg, $arr);
		$qfile = new qfile();
		$qfile->open($fn);
		$qfile->write("<? \n");
		$qfile->write("\$qrCfg = array( \n");
		foreach ($this->qrCfg as $k=>$v) {
			$qfile->write("'$k'=>'".addslashes($v)."',\n");
		}
		$qfile->write("); \n");
		$qfile->write("?>");
		$qfile->close();
		@chmod($fn,0707);
	}
}
?>
