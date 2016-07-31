<?php
if (class_exists('SNS', false)) return;
class SNS
{
	var $snsCfg;
	var $snsPostCfg;
	var $clogCfg;
	var $mobileSkin = false;
	var $msg_kakao1 = "";
	var $msg_kakao2 = "";
	var $msg_kakao3 = "";

	function SNS(){
		GLOBAL $cfg;
		$cfgfile = dirname(__FILE__)."/../conf/config.php";
		if(file_exists($cfgfile)) @require_once $cfgfile;
		if($cfg) $this->cfg = $cfg;
		GLOBAL $snsCfg;
		$cfgfile = dirname(__FILE__)."/../conf/sns.cfg.php";
		if(file_exists($cfgfile)) @require_once $cfgfile;
		if($snsCfg)	$this->snsCfg = $snsCfg;
		$postcfgfile = dirname(__FILE__)."/../conf/snspost.cfg.php";
		if(file_exists($postcfgfile)) @require $postcfgfile;
		if($snsPostCfg) $this->snsPostCfg = $snsPostCfg;
	}

	/* 서비스 설정 저장 */
	function config_write($arr){
		$fn = dirname(__FILE__)."/../conf/sns.cfg.php";
		foreach ($arr as $k=>$v) {
			$arr[$k] = htmlspecialchars(stripslashes($v));
		}
		$this->snsCfg = array_merge($this->snsCfg, $arr);
		$this->snsCfg['updateDt'] = date('Y-m-d H:i:s',time());
		$qfile = new qfile();
		$qfile->open($fn);
		$qfile->write("<? \n");
		$qfile->write("\$snsCfg = array( \n");
		foreach ($this->snsCfg as $k=>$v) {
			$qfile->write("'$k'=>'".addslashes($v)."',\n");
		}
		$qfile->write("); \n");
		$qfile->write("?>");
		$qfile->close();
		@chmod($fn,0707);
	}

	function get_post_btn($args, $screen=''){
		if ($this->snsCfg['useBtn'] != 'y') return false;
		mb_internal_encoding('EUC-KR'); // 인코딩 EUC-KR 설정.
		global $shopRootDir;

		// 트위터
		$msg = html_entity_decode(preg_replace('/\n/','', $this->snsCfg['msg_twitter']));
		$msg = preg_replace('/{shopnm}/i', $args['shopnm'], $msg);
		$msg = preg_replace('/{goodsurl}/i', $args['goodsurl'], $msg);
		$msg_length = mb_strlen(preg_replace('/{goodsnm}/i', '', $msg));
		$tw_goodsnm = $args['goodsnm'];
		if ($msg_length <= 140) $tw_goodsnm = mb_substr($args['goodsnm'], 0, 140 - $msg_length);
		$msg = preg_replace('/{goodsnm}/i', $tw_goodsnm, $msg);
		$encodedMsg = urlencode(@iconv('EUC-KR', 'UTF-8//IGNORE', $msg));
		$twitterurl = 'http://twitter.com/home?status='.$encodedMsg;

		if($screen = 'm') {
			$twitterurl = 'http://mobile.twitter.com/compose/tweet?status='.$encodedMsg;	//모바일 url은 다름
		}
		// 트위터

		// 페이스북
		$msg = html_entity_decode(preg_replace('/\n/','', $this->snsCfg['msg_facebook']));
		$msg = preg_replace('/{shopnm}/i', $args['shopnm'], $msg);
		$msg = preg_replace('/{goodsnm}/i', $args['goodsnm'], $msg);
		$msg = preg_replace('/{goodsurl}/i', $args['goodsurl'], $msg);
		$encodedMsg = urlencode(@iconv('EUC-KR', 'UTF-8//IGNORE', $msg));
		$facebookurl = 'http://www.facebook.com/sharer.php?u='.urlencode($args['goodsurl'].'&time='.time());
		// 페이스북

		// 페이스북 메타 TAG
		$title_fb = html_entity_decode(preg_replace('/\n/','', $this->snsCfg['msg_facebook']));
		$title_fb = preg_replace('/{shopnm}/i', $args['shopnm'], $title_fb);
		$title_fb = preg_replace('/{goodsnm}/i', $args['goodsnm'], $title_fb);
		$title_fb = preg_replace('/{goodsurl}/i', $args['goodsurl'], $title_fb);
		$title_fb = htmlspecialchars($title_fb);
		$rtn['meta'] = '<meta property="og:title" content="'.$title_fb.'" />';
		$rtn['meta'] .= '<meta property="og:description" content="'.$title_fb.'" />';
		if (preg_match('/^http(s)?:\/\//',$args['img']))
			$rtn['meta'] .= '<meta property="og:image" content="'.$args['img'].'" />';
		else
			$rtn['meta'] .= '<meta property="og:image" content="http://'.$_SERVER['HTTP_HOST'].'/shop/data/goods/'.$args['img'].'" />';
		// 페이스북 메타 TAG

		// 핀터레스트
		if (preg_match('/^http(s)?:\/\//',$args['img']))
			$pinteresturl='https://www.pinterest.com/pin/create/button/?url='.urlencode($args['goodsurl']).'&media='.$args['img'].'&description='.urlencode(iconv('EUC-KR','UTF-8',$args['goodsnm']));
		else
			$pinteresturl='https://www.pinterest.com/pin/create/button/?url='.urlencode($args['goodsurl']).'&media=http://'.$_SERVER['HTTP_HOST'].'/shop/data/goods/'.$args['img'].'&description='.urlencode(iconv('EUC-KR','UTF-8',$args['goodsnm']));
		// 핀터레스트

		// 카카오링크
			// 쇼핑몰 이름
			$this->msg_kakao1 = html_entity_decode($this->snsCfg['msg_kakao_shopnm']);
			$this->msg_kakao1 = preg_replace('/{shopnm}/i', $args['shopnm'], $this->msg_kakao1);
			$this->msg_kakao1 = preg_replace('/{goodsnm}/i', strip_tags($args['goodsnm']), $this->msg_kakao1);
			$this->msg_kakao1 = preg_replace('/{goodsurl}/i', $args['goodsurl'], $this->msg_kakao1);
			// 전송 내용
			$this->msg_kakao2 = html_entity_decode($this->snsCfg['msg_kakao_goodsnm']);
			$this->msg_kakao2 = preg_replace('/{shopnm}/i', $args['shopnm'], $this->msg_kakao2);
			$this->msg_kakao2 = preg_replace('/{goodsnm}/i', strip_tags($args['goodsnm']), $this->msg_kakao2);
			$this->msg_kakao2 = preg_replace('/{goodsurl}/i', $args['goodsurl'], $this->msg_kakao2);
			$this->msg_kakao2 = urlencode(iconv("EUC-KR", "UTF-8", $this->msg_kakao2));
			// 전송 URL
			$this->msg_kakao3 = html_entity_decode($this->snsCfg['msg_kakao_goodsurl']);
			$this->msg_kakao3 = preg_replace('/{shopnm}/i', $args['shopnm'], $this->msg_kakao3);
			$this->msg_kakao3 = preg_replace('/{goodsnm}/i', strip_tags($args['goodsnm']), $this->msg_kakao3);
			$this->msg_kakao3 = preg_replace('/{goodsurl}/i', $args['goodsurl'], $this->msg_kakao3);
			
			if (preg_match('/^http(s)?:\/\//',$args['img'])){
				$this->msg_kakao_imgUrl = $args['img'];
			} else {
				$this->msg_kakao_imgUrl			= 'http://' . $_SERVER['HTTP_HOST'] . '/shop/data/goods/' . $args['img'];
				$this->msg_kakao_AbsoluteImgUrl = $shopRootDir . '/data/goods/' . $args['img'];
			}
		// 카카오링크

		$rtn['btn'] = "";

		// 버튼 TAG
		if($this->mobileSkin == false) {
			if($this->snsCfg['use_twitter'] == 'y') $rtn['btn'] = '<a href="'.$twitterurl.'" target="_blank" style="margin-right:5px;"><img src="../data/skin/'.$this->cfg['tplSkin'].'/img/sns/icon_twitter.png" /></a>';
			if($this->snsCfg['use_facebook'] == 'y') $rtn['btn'] .= '<a href="'.$facebookurl.'" target="_blank" style="margin-right:5px;"><img src="../data/skin/'.$this->cfg['tplSkin'].'/img/sns/icon_facebook.png" /></a>';
			if($this->snsCfg['use_pinterest'] == 'y') $rtn['btn'] .= '<a href="'.$pinteresturl.'" target="_blank" style="margin-right:5px;"><img src="../data/skin/'.$this->cfg['tplSkin'].'/img/sns/icon_pinterest.png" /></a>';
			if($this->snsCfg['use_urlcopy'] == 'y' || !$this->snsCfg['use_urlcopy']){
				$rtn['btn'] .= '<a onclick="goodsCopyUrl();" id="sns_url_copy" style="margin-right:5px; cursor:pointer;display:none;"><img src="../data/skin/'.$this->cfg['tplSkin'].'/img/sns/icon_url.png" /></a>';
				$rtn['btn'] .= '<script type="text/javascript">if (typeof(goodsCopyUrl) != \'undefined\'){ document.getElementById(\'sns_url_copy\').style.display = "inline"; }</script>';
			}
		}
		else {
			if($this->snsCfg['use_kakao'] == 'y') $rtn['btn'] .= '<a href="javascript:;" id="kakaoTalkLink" style="margin-right:5px; display: none;"><img src="../../shop/data/skin_mobile/'.$this->cfg['tplSkinMobile'].'/common/img/sns/kakaotalk.png" width="25" height="25" /></a>';
			if($this->snsCfg['use_kakao'] == 'y') $rtn['btn'] .= '<a href="javascript:;" id="kakao" style="margin-right:5px; display: inline-block;"><img src="../../shop/data/skin_mobile/'.$this->cfg['tplSkinMobile'].'/common/img/sns/kakaotalk.png" width="25" height="25" /></a>';
			if($this->snsCfg['use_twitter'] == 'y') $rtn['btn'] .= '<a href="'.$twitterurl.'" target="_blank" style="margin-right:5px;"><img src="../../shop/data/skin_mobile/'.$this->cfg['tplSkinMobile'].'/common/img/sns/icon_twitter.png" /></a>';
			if($this->snsCfg['use_facebook'] == 'y') $rtn['btn'] .= '<a href="'.$facebookurl.'" target="_blank" style="margin-right:5px;"><img src="../../shop/data/skin_mobile/'.$this->cfg['tplSkinMobile'].'/common/img/sns/icon_facebook.png" /></a>';
			if($this->snsCfg['use_pinterest'] == 'y') $rtn['btn'] .= '<a href="'.$pinteresturl.'" target="_blank" style="margin-right:5px;"><img src="../../shop/data/skin_mobile/'.$this->cfg['tplSkinMobile'].'/common/img/sns/icon_pinterest.png" /></a>';
			if($this->snsCfg['use_urlcopy'] == 'y' || !$this->snsCfg['use_urlcopy']) {
				$rtn['btn'] .= '<a onclick="goodsCopyUrlMobile();" id="sns_url_copy" style="cursor:pointer;display:none;"><img src="../../shop/data/skin_mobile/'.$this->cfg['tplSkinMobile'].'/common/img/sns/icon_url.png" /></a>';
				$rtn['btn'] .= '<script type="text/javascript">if (typeof(goodsCopyUrlMobile) != \'undefined\'){ document.getElementById(\'sns_url_copy\').style.display = "inline"; }</script>';
			}
		}
		return $rtn;
	}

	function get_post_listbox($sno, $previewData=null) {
		// 2014-07-03 이후로 실시간 연동(미투데이) 미지원
		// @todo 무료스킨(side/standard.htm)에서 snsPosts() 함수 호출은 추후 제거
		return '';
	}


	function get_post_btn_mobile($args){
		if ($this->snsCfg['useBtn'] != 'y') return false;
		mb_internal_encoding('EUC-KR'); // 인코딩 EUC-KR 설정.
		global $shopRootDir;

		// 트위터
		$msg = html_entity_decode(preg_replace('/\n/','', $this->snsCfg['msg_twitter']));
		$msg = preg_replace('/{shopnm}/i', $args['shopnm'], $msg);
		$msg = preg_replace('/{goodsurl}/i', $args['goodsurl'], $msg);
		$msg_length = mb_strlen(preg_replace('/{goodsnm}/i', '', $msg));
		$tw_goodsnm = $args['goodsnm'];
		if ($msg_length <= 140) $tw_goodsnm = mb_substr($args['goodsnm'], 0, 140 - $msg_length);
		$msg = preg_replace('/{goodsnm}/i', $tw_goodsnm, $msg);
		$encodedMsg = urlencode(@iconv('EUC-KR', 'UTF-8//IGNORE', $msg));
		$twitterurl = 'http://mobile.twitter.com/compose/tweet?status='.$encodedMsg;	//모바일 url은 다름
		// 트위터

		// 페이스북
		$msg = html_entity_decode(preg_replace('/\n/','', $this->snsCfg['msg_facebook']));
		$msg = preg_replace('/{shopnm}/i', $args['shopnm'], $msg);
		$msg = preg_replace('/{goodsnm}/i', $args['goodsnm'], $msg);
		$msg = preg_replace('/{goodsurl}/i', $args['goodsurl'], $msg);
		$encodedMsg = urlencode(@iconv('EUC-KR', 'UTF-8//IGNORE', $msg));
		$facebookurl = 'http://m.facebook.com/sharer.php?u='.urlencode($args['goodsurl'].'&time='.time());
		// 페이스북

		// 페이스북 메타 TAG
		$title_fb = html_entity_decode(preg_replace('/\n/','', $this->snsCfg['msg_facebook']));
		$title_fb = preg_replace('/{shopnm}/i', $args['shopnm'], $title_fb);
		$title_fb = preg_replace('/{goodsnm}/i', $args['goodsnm'], $title_fb);
		$title_fb = preg_replace('/{goodsurl}/i', $args['goodsurl'], $title_fb);
		$title_fb = htmlspecialchars($title_fb);
		$rtn['meta'] = '<meta property="og:title" content="'.$title_fb.'" />';
		$rtn['meta'] .= '<meta property="og:description" content="'.$title_fb.'" />';
		if (preg_match('/^http(s)?:\/\//',$args['img']))
			$rtn['meta'] .= '<meta property="og:image" content="'.$args['img'].'" />';
		else
			$rtn['meta'] .= '<meta property="og:image" content="http://'.$_SERVER['HTTP_HOST'].'/shop/data/goods/'.$args['img'].'" />';
		// 페이스북 메타 TAG

		// 핀터레스트
		if (preg_match('/^http(s)?:\/\//',$args['img']))
			$pinteresturl='https://www.pinterest.com/pin/create/button/?url='.urlencode($args['goodsurl']).'&media='.$args['img'].'&description='.urlencode(iconv('EUC-KR','UTF-8',$args['goodsnm']));
		else
			$pinteresturl='https://www.pinterest.com/pin/create/button/?url='.urlencode($args['goodsurl']).'&media=http://'.$_SERVER['HTTP_HOST'].'/shop/data/goods/'.$args['img'].'&description='.urlencode(iconv('EUC-KR','UTF-8',$args['goodsnm']));
		// 핀터레스트

		// 카카오링크
			// 쇼핑몰 이름
			$this->msg_kakao1 = html_entity_decode($this->snsCfg['msg_kakao_shopnm']);
			$this->msg_kakao1 = preg_replace('/{shopnm}/i', $args['shopnm'], $this->msg_kakao1);
			$this->msg_kakao1 = preg_replace('/{goodsnm}/i', strip_tags($args['goodsnm']), $this->msg_kakao1);
			$this->msg_kakao1 = preg_replace('/{goodsurl}/i', $args['goodsurl'], $this->msg_kakao1);
			// 전송 내용
			$this->msg_kakao2 = html_entity_decode($this->snsCfg['msg_kakao_goodsnm']);
			$this->msg_kakao2 = preg_replace('/{shopnm}/i', $args['shopnm'], $this->msg_kakao2);
			$this->msg_kakao2 = preg_replace('/{goodsnm}/i', strip_tags($args['goodsnm']), $this->msg_kakao2);
			$this->msg_kakao2 = preg_replace('/{goodsurl}/i', $args['goodsurl'], $this->msg_kakao2);
			$this->msg_kakao2 = urlencode(iconv("EUC-KR", "UTF-8", $this->msg_kakao2));
			// 전송 URL
			$this->msg_kakao3 = html_entity_decode($this->snsCfg['msg_kakao_goodsurl']);
			$this->msg_kakao3 = preg_replace('/{shopnm}/i', $args['shopnm'], $this->msg_kakao3);
			$this->msg_kakao3 = preg_replace('/{goodsnm}/i', strip_tags($args['goodsnm']), $this->msg_kakao3);
			$this->msg_kakao3 = preg_replace('/{goodsurl}/i', $args['goodsurl'], $this->msg_kakao3);
			
			if (preg_match('/^http(s)?:\/\//',$args['img'])){
				$this->msg_kakao_imgUrl = $args['img'];
			} else {
				$this->msg_kakao_imgUrl			= 'http://' . $_SERVER['HTTP_HOST'] . '/shop/data/goods/' . $args['img'];
				$this->msg_kakao_AbsoluteImgUrl = $shopRootDir . '/data/goods/' . $args['img'];
			}
		// 카카오링크

		// 카카오스토리
			// 쇼핑몰명
			$this->msg_kakaoStory_shopnm = html_entity_decode($this->snsCfg['msg_kakaoStory_shopnm']);
			$this->msg_kakaoStory_shopnm = preg_replace('/{shopnm}/i', $args['shopnm'], $this->msg_kakaoStory_shopnm);
			$this->msg_kakaoStory_shopnm = preg_replace('/{goodsnm}/i', strip_tags(addslashes($args['goodsnm'])), $this->msg_kakaoStory_shopnm);
			$this->msg_kakaoStory_shopnm = preg_replace('/{goodsurl}/i', $args['goodsurl'], $this->msg_kakaoStory_shopnm);

			// 상품명
			$this->msg_kakaoStory_goodsnm = html_entity_decode($this->snsCfg['msg_kakaoStory_goodsnm']);
			$this->msg_kakaoStory_goodsnm = preg_replace('/{shopnm}/i', $args['shopnm'], $this->msg_kakaoStory_goodsnm);
			$this->msg_kakaoStory_goodsnm = preg_replace('/{goodsnm}/i', strip_tags(addslashes($args['goodsnm'])), $this->msg_kakaoStory_goodsnm);
			$this->msg_kakaoStory_goodsnm = preg_replace('/{goodsurl}/i', $args['goodsurl'], $this->msg_kakaoStory_goodsnm);

			// 상품url
			$this->msg_kakaoStory_goodsurl = html_entity_decode($this->snsCfg['msg_kakaoStory_goodsurl']);
			$this->msg_kakaoStory_goodsurl = preg_replace('/{shopnm}/i', $args['shopnm'], $this->msg_kakaoStory_goodsurl);
			$this->msg_kakaoStory_goodsurl = preg_replace('/{goodsnm}/i', strip_tags(addslashes($args['goodsnm'])), $this->msg_kakaoStory_goodsurl);
			$this->msg_kakaoStory_goodsurl = preg_replace('/{goodsurl}/i', $args['goodsurl'], $this->msg_kakaoStory_goodsurl);

			//상품이미지
			$msg_kakaoStory_img_l = $args['img_l'];
			if(preg_match('/\|/',$msg_kakaoStory_img_l)) $msg_kakaoStory_img_l = @array_shift(@explode('|',$args['img_l']));

			if (preg_match('/^http(s)?:\/\//',$msg_kakaoStory_img_l)){
				$this->msg_kakaoStory_img_l = $msg_kakaoStory_img_l;
			} else {
				$this->msg_kakaoStory_img_l = "http://" . $_SERVER['HTTP_HOST'] . "/shop/data/goods/" . $msg_kakaoStory_img_l;
			}

		// 카카오스토리

		$rtn['btn'] = "";
		if($this->snsCfg['use_kakao'] == 'y') $rtn['btn'] .= '<a href="javascript:;" id="kakaoTalkLink" style="/*margin-right:5px; */display: none; float: left;"><div class="sns03"><div class="sns03_effect"><div class="sns03_object"></div></div></div></a>';
		if($this->snsCfg['use_kakao'] == 'y') $rtn['btn'] .= '<a href="javascript:;" id="kakao" style="margin-right:5px; display: inline-block; float: left;"><div class="sns03"><div class="sns03_effect"><div class="sns03_object"></div></div></div></a>';
		if($this->snsCfg['use_kakaoStory'] == 'y') $rtn['btn'] .= '<a href="javascript:;" id="kakaoStory"><div class="sns06" style="display: none;"><div class="sns06_effect"><div class="sns06_object"></div></div></div></a>';
		if($this->snsCfg['use_twitter'] == 'y') $rtn['btn'] .= '<a href="'.$twitterurl.'" target="_blank" ><div class="sns01"><div class="sns01_effect"><div class="sns01_object"></div></div></div></a>';
		if($this->snsCfg['use_facebook'] == 'y') $rtn['btn'] .= '<a href="'.$facebookurl.'" target="_blank" ><div class="sns02"><div class="sns02_effect"><div class="sns02_object"></div></div></div></a>';
		if($this->snsCfg['use_pinterest'] == 'y') $rtn['btn'] .= '<a href="'.$pinteresturl.'" target="_blank" ><div class="sns07"><div class="sns07_effect"><div class="sns01_object"></div></div></div></a>';
		if($this->snsCfg['use_urlcopy'] == 'y' || !$this->snsCfg['use_urlcopy']){
			$rtn['btn'] .= '<a onclick="goodsCopyUrl();" id ="sns_url_copy" style="display:none;"><div class="sns08"><div class="sns08_effect"><div class="sns01_object"></div></div></div></a>';
			$rtn['btn'] .= '<script type="text/javascript">if (typeof(goodsCopyUrl) != \'undefined\'){ document.getElementById(\'sns_url_copy\').style.display = "inline"; }</script>';
		}

		return $rtn;
	}
}

?>
