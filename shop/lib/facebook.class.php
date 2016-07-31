<?php
class Facebook
{
	var $scripts;
	var $fbCmtCfg;
	var $pageUseYn;
	var $pageAddr;
	var $pageUrl;
	var $pageWidth;
	var $pageHeight;
	var $pageBordercolor;
	var $pageStreamYn;
	var $pageFacesYn;
	var $cmtUseYn;
	var $cmtCount;
	var $cmtWidth;
	var $mbUseYn;
	var $mbAddr;
	var $facebookBtn;
	var $mbfacebookBtn;

	var $defaultUseYn;
	var $defaultAddr;
	var $defaultWidth;
	var $defaultHeight;
	var $defaultCmtWidth;
	var $defaultCount;
	var $defaultBordercolor;
	var $defaultStreamYn;
	var $defaultFacesYn;
	var $defaultFacebookBtn;
	var $defaultMbfacebookBtn;

	var $faceUrl;
	var $defaultImgDir;
	var $customImgDir;
	function Facebook(){
		GLOBAL $cfg;
		if($cfg) $this->cfg = $cfg;
		
		$this->faceUrl=$this->cfg['rootDir']."/goods/facepage.php";	//페이스북 페이지 주소
		$this->defaultImgDir=$this->cfg['rootDir']."/data/skin/".$this->cfg['tplSkin'];	//디폴트 버튼 이미지경로
		$this->customImgDir=$this->cfg['rootDir']."/data/sns";	//커스터마이징 버튼 이미지경ㄴ로
		//디폴트 설정값
		$this->defaultUseYn="y";
		$this->defaultAddr="godomallnews";
		$this->defaultUrl=urlencode('http://facebook.com/').$this->defaultAddr;
		$this->defaultWidth="690";
		$this->defaultHeight="570";
		$this->defaultCount="2";
		$this->defaultBordercolor="#ffffff";
		$this->defaultStreamYn="true";
		$this->defaultFacesYn="true";
		$this->defaultCmtWidth="470";

		$l = dirname(__FILE__)."/../conf/fbPage.cfg.php";
		if(file_exists($l)){
			require $l;
		}
		else{
			$fbPageCfg = array( 
				'useYn' => $this->defaultUseYn, 
				'addr' => $this->defaultAddr, 
				'url' => $this->defaultUrl , 
				'width' => $this->defaultWidth , 
				'height' => $this->defaultHeight , 
				'bordercolor' => $this->defaultBordercolor, 
				'streamYn' => $this->defaultStreamYn, 
				'facesYn' => $this->defaultFacesYn, 
				'facebookBtn' => "", 
				);
		}
		$this->pageUseYn=$fbPageCfg['useYn'];
		$this->pageAddr=$fbPageCfg['addr'];
		$this->pageUrl=$fbPageCfg['url'];
		$this->pageWidth=$fbPageCfg['width'];
		$this->pageHeight=$fbPageCfg['height'];
		$this->pageBordercolor=$fbPageCfg['bordercolor'];
		$this->pageStreamYn=$fbPageCfg['streamYn'];
		$this->pageFacesYn=$fbPageCfg['facesYn'];
		$this->facebookBtn=$fbPageCfg['facebookBtn'];
		

		$l = dirname(__FILE__)."/../conf/fbCmt.cfg.php";
		if(file_exists($l)){
			require $l;
		}
		else{
			$fbCmtCfg = array( 
				'useYn' => $this->defaultUseYn, 
				'count' => $this->defaultCount, 
				'width' => $this->defaultCmtWidth, 
			);
		}
		$this->cmtUseYn=$fbCmtCfg['useYn'];
		$this->cmtCount=$fbCmtCfg['count'];
		$this->cmtWidth=$fbCmtCfg['width'];

		$l = dirname(__FILE__)."/../conf/mfbPage.cfg.php";
		if(file_exists($l)){
			require $l;
		}
		else{
			$mfbPageCfg = array( 
				'useYn' => $this->defaultUseYn, 
				'addr' => $this->defaultAddr,
				'mbfacebookBtn' => "", 
			);
		}
		$this->mbUseYn=$mfbPageCfg['useYn'];
		$this->mbAddr=$mfbPageCfg['addr'];
		$this->mbfacebookBtn=$mfbPageCfg['mbfacebookBtn'];
	}

	function fbButton(){ 
		if(!$this->facebookBtn) {
			$imgdir= $this->defaultImgDir."/img/common/facebookBtn.jpg";	
		}
		else{
			$imgdir= $this->customImgDir."/".$this->facebookBtn;	
		}
		$scripts="";
		$scripts = "<a href='".$this->faceUrl."'  ><img src='".$imgdir."' /></a>";

		return $scripts;
	}

	function mbfbButton(){ 
		if(!$this->mbfacebookBtn) {
			$imgdir= $this->defaultImgDir."/img/common/mbfacebookBtn.gif";	
		}
		else{
			$imgdir= $this->customImgDir."/".$this->mbfacebookBtn;	
		}
		$scripts = "<a href='http://facebook.com/".$this->mbAddr."'><img src='".$imgdir."' /></a>";

		return $scripts;
	}


	function comment(){ 
		$host_url="http://".$_SERVER['HTTP_HOST']; 
		$scripts="";
		if($this->cmtUseYn=='y'){
			$scripts = "<div id=\"fb-root\"></div>
			<script>(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = \"//connect.facebook.net/ko_KR/all.js#xfbml=1&appId=299676300103998\";
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));</script>
			<div class=\"fb-comments\" data-href=\"".$host_url."\" data-num-posts=\"".$this->cmtCount."\" data-width=\"".$this->cmtWidth."\"></div>";
		}
		return $scripts;
	}

	function likebox(){ 
		if($this->pageUseYn=='y'){
			$scripts="<iframe src=\"//www.facebook.com/plugins/likebox.php?href=".$this->pageUrl."&amp;width=".$this->pageWidth."&amp;height=".$this->pageHeight."&amp;colorscheme=light&amp;show_faces=".$this->pageFacesYn."&amp;border_color=".urlencode($this->pageBordercolor)."&amp;stream=".$this->pageStreamYn."&amp;header=false\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:".$this->pageWidth."px; height:".$this->pageHeight."px;\" allowTransparency=\"true\"></iframe>
			";
		}
		return $scripts;
	} 
}
?>