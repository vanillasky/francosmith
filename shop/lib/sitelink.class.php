<?php
/*
	사이트 경로관리 클래스
*/

class sitelink {
	var $_ssl_rule = array(
		// 모바일
		'/^intro\/intro_adult.php/',
		'/^intro\/intro_member.php/',
		'/^mem\/.+/',
		'/^ord\/.+/',
		'/^myp\/qna/',
		'/^myp\/indb.php/',
		// PC
		'/^main\/intro_adult.php/',
		'/^main\/intro_member.php/',
		'/^member\/.+/',
		'/^order\/.+/',
		'/^service\/_private/',
		'/^proc\/naverNcash_bridge/',
		'/^service\/cooperation.php/',
		'/^mypage\/mypage_qna/',
		'/^mypage\/indb.php/',
		// PC, 모바일 공통
		'/^board\/write/',
	);

	var $_pass_rule = array(
		'/^proc\/popup_zipcode/',
		'/^proc\/popup_address/',
		'/^proc\/popup_coupon/',
		'/^proc\/coupon_list/',
	);

	var $_prefix_dir;
	var $_ssl_domain;
	var $_ssl_port;
	var $_free_ssl_domain;
	var $_free_ssl_port;
	var $_regular_domain;

	function sitelink() {
		$config = Core::loader('config');
		$cfg = $config->load('config');

		$today = (int)date('Ymd');
		$ssl_sdate = (int)$cfg['ssl_sdate'];
		$ssl_edate = (int)$cfg['ssl_edate'];

		### 초기값 세팅
		if($cfg['ssl']== 1 && $cfg['ssl_port'] && !$cfg['ssl_type']) $cfg['ssl_type'] = 'godo';

		if(
			($cfg['ssl_type']=='godo' && $ssl_sdate <= $today && $ssl_edate >= $today && $cfg['ssl_domain'])
			||
			($cfg['ssl_type']=='direct' && $cfg['ssl_domain'])
		) {
			$this->_ssl_domain = $cfg['ssl_domain'];
			$this->_ssl_port = $cfg['ssl_port'];
			$this->_regular_domain = $cfg['ssl_domain'];
		}
		elseif($cfg['ssl_type']=='free' && $cfg['ssl_freedomain']) {
			$this->_free_ssl_domain = $cfg['ssl_freedomain'];
			if($_GET['rd']) {
				$this->_regular_domain = $_GET['rd'];
			}
			else {
				$this->_regular_domain = $_SERVER['SERVER_NAME'];
			}
		}
		else {
			$this->_regular_domain = $_SERVER['SERVER_NAME'];
		}
		$__FILE__ = str_replace(DIRECTORY_SEPARATOR,'/',__FILE__);

		$this->_prefix_dir = substr($__FILE__,strlen($_SERVER['DOCUMENT_ROOT']),-1*(strlen('lib/sitelink.class.php')+1));
		if(substr($this->_prefix_dir,0,1) != '/')$this->_prefix_dir = '/'.$this->_prefix_dir;
	}

	function old_get_type() {
		if($this->_ssl_domain) {
			return 'SSL';
		}
		else {
			return 'NOSSL';
		}
	}

	/*
		현재 페이지를 기준으로 인자로받은 URL의 경로를 알려 줍니다
	*/
	function link($semantic_absolute_url,$force_type='auto') {
		if(!preg_match('/^[a-zA-Z0-9]/',$semantic_absolute_url)) {
			return;
		}
		if($this->_is_pass_url($semantic_absolute_url)) {
			return $this->_prefix_dir.'/'.$semantic_absolute_url;
		}

		if($this->_ssl_domain) {
			if($_SERVER['HTTPS']) {
				if(($this->_is_ssl_url($semantic_absolute_url) && $force_type!='regular') || $force_type=='ssl') {
					return $this->_prefix_dir.'/'.$semantic_absolute_url;
				}
				else {
					return $this->_get_full_url('http',$this->_regular_domain,21,$this->_prefix_dir.'/'.$semantic_absolute_url);
				}
			}
			else {
				if(($this->_is_ssl_url($semantic_absolute_url) && $force_type!='regular') || $force_type=='ssl') {
					return $this->_get_full_url('https',$this->_ssl_domain,$this->_ssl_port,$this->_prefix_dir.'/'.$semantic_absolute_url);
				}
				else {
					return $this->_prefix_dir.'/'.$semantic_absolute_url;
				}
			}
		}
		elseif($this->_free_ssl_domain) {
			if($_SERVER['HTTPS']) {
				if($force_type=='ssl') {
					return $this->_prefix_dir.'/'.$semantic_absolute_url;
				}
				else {
					$tmp=explode('?',$semantic_absolute_url);
					$semantic_absolute_url_file=$tmp[0];
					$semantic_absolute_url_query=$tmp[1];
					parse_str($semantic_absolute_url_query,$ar_parsed);
					unset($ar_parsed['sess_id']);
					unset($ar_parsed['rd']);
					return $this->_get_full_url('http',$this->_regular_domain,21,$this->_prefix_dir.'/'.$semantic_absolute_url_file.'?'.http_build_query($ar_parsed));
				}
			}
			else {
				if($force_type=='ssl') {
					$tmp=explode('?',$semantic_absolute_url);
					$semantic_absolute_url_file=$tmp[0];
					$semantic_absolute_url_query=$tmp[1];

					parse_str($semantic_absolute_url_query,$ar_parsed);
					$ar_parsed['sess_id']=session_id();
					$ar_parsed['rd']=$this->_regular_domain;
					return $this->_get_full_url('https',$this->_free_ssl_domain,$this->_free_ssl_port,$this->_prefix_dir.'/'.$semantic_absolute_url_file.'?'.http_build_query($ar_parsed));
				}
				else {
					return $this->_prefix_dir.'/'.$semantic_absolute_url;
				}
			}
		}
		else {
			return $this->_prefix_dir.'/'.$semantic_absolute_url;
		}
	}

	function _get_full_url($protocol,$domain,$port,$uri) {
		if($protocol=='https') {
			if($port=='' || $port=='443') {
				return 'https://'.$domain.$uri;
			}
			else {
				return 'https://'.$domain.':'.$port.$uri;
			}
		}
		else {
			return 'http://'.$domain.$uri;
		}
	}

	/*
		프로그램 시작 때 실행되는 메소드
	*/
	function ready_refresh() {

		@include dirname(__FILE__) . "/../conf/config.mobileShop.php";

		# 모바일 접속 체크 : Start #
		$arrMobileAgent = array('iPhone','Mobile','UP.Browser','Android','BlackBerry','Windows CE','Nokia','webOS','Opera Mini','SonyEricsson','opera mobi','Windows Phone','IEMobile','POLARIS','lgtelecom','NATEBrowser','AppleWebKit');
		$arrExAgent = array('Macintosh','OpenBSD','SunOS','X11','QNX','BeOS', ' OS\/2','Windows NT','iPad');
		if(preg_match('/('.implode('|',$arrMobileAgent).')/i', $_SERVER['HTTP_USER_AGENT']) && !preg_match('/('.implode('|',$arrExAgent).')/i', $_SERVER['HTTP_USER_AGENT'])){
			$isMobile = true;
			if(preg_match('/(AppleWebKit)/i',$_SERVER['HTTP_USER_AGENT']) && preg_match('/(Windows;)/i',$_SERVER['HTTP_USER_AGENT'])) $isMobile = false;
			if(preg_match('/(Windows CE)/i',$_SERVER['HTTP_USER_AGENT']) && !preg_match('/(compatible;)/i',$_SERVER['HTTP_USER_AGENT']) && !preg_match('/(IEMobile)/i',$_SERVER['HTTP_USER_AGENT'])) $isMobile = false;
			if(preg_match('/(AppleWebKit)/i',$_SERVER['HTTP_USER_AGENT']) && preg_match('/(Linux;)/i',$_SERVER['HTTP_USER_AGENT']) && !strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile')) $isMobile = false;
		}

		if($cfgMobileShop['useMobileShop'] && $isMobile && is_null($_GET['pc'])){
			$tmpReferer = parse_url($_SERVER['HTTP_REFERER']);
			if(is_null($_GET['pc']) && $tmpReferer['host']!=$_SERVER['SERVER_NAME']){
				header("location:http://".$_SERVER['SERVER_NAME'].$cfgMobileShop['mobileShopRootDir']."/mgate.php?refer=".$_SERVER['REQUEST_URI']);exit;
			}
		}
		# 모바일 접속 체크 : End #

		// SSL도메인이 아닌 다른 도메인으로 접근했을 경우 처리
		if($this->_ssl_domain && $_SERVER['SERVER_NAME']!=$this->_ssl_domain && strpos($_SERVER['SERVER_NAME'],'.godo.interpark.com') === false) {
			$refresh_url = $this->_get_full_url('http',$this->_ssl_domain,80,$_SERVER['REQUEST_URI']);
			if(!headers_sent()){
				header("Location:{$refresh_url}");exit;
			}else{
				echo "<script>location.replace('{$refresh_url}');</script>";
			}
			exit;
		}

		// 현재 호출된 페이지가 HTTPS프로토콜을 타야하는데 그렇지 않은 경우 처리
		$semantic_absolute_url=$this->_get_semantic_absolute_url();
		if($this->_is_pass_url($semantic_absolute_url)) {
			return;
		}

		if($this->_ssl_domain && $this->_is_ssl_url($semantic_absolute_url) && !$_SERVER['HTTPS']) {
			$refresh_url = $this->_get_full_url('https',$this->_ssl_domain,$this->_ssl_port,$_SERVER['REQUEST_URI']);
			if(is_array($_POST) && count($_POST)) {
				$serialize_post = array_formpost_serialize($_POST);
				echo "<form name='ssl_forward' action='{$refresh_url}' method='post'>\n";
				foreach($serialize_post as $each_input) {
					echo '<input type="hidden" name="'.$each_input['name'].'" value="'.htmlspecialchars($each_input['value']).'">';
				}
				echo "</form>";
				echo "<script>document.ssl_forward.submit();</script>";

			}
			else {
				echo "<script>location.replace('{$refresh_url}');</script>";
			}
			exit;
		}

		if($_SERVER['HTTPS'] && !$this->_is_ssl_url($semantic_absolute_url)) {
			$refresh_url = $this->_get_full_url('http',$this->_regular_domain,21,$_SERVER['REQUEST_URI']);
			echo "<script>location.replace('{$refresh_url}');</script>";
			exit;
		}
	}

	/*
		인자로 받은 url이 보안서버를 사용해야하는지 체크합니다
	*/
	function _is_ssl_url($semantic_absolute_url) {
		foreach($this->_ssl_rule as $each_rule) {
			if(preg_match($each_rule,$semantic_absolute_url)) {
				if ($each_rule == '/^mypage\/indb.php/') {
					if(preg_match('/\/mypage\/mypage_qna/',$_SERVER['REQUEST_URI']) == 1) {
						return true;
					} else {
						return false;
					}
				}
				return true;
			}
		}
		return false;
	}

	/*
		인자로 받은 url이 pass하는지 체크합니다
	*/
	function _is_pass_url($semantic_absolute_url) {
		foreach($this->_pass_rule as $each_rule) {
			if(preg_match($each_rule,$semantic_absolute_url)) {
				return true;
			}
		}
		return false;
	}

	/*
		현재 페이지의 의미적 절대경로를 알려줍니다
	*/
	function _get_semantic_absolute_url() {
		return substr($_SERVER['SCRIPT_FILENAME'],strlen(SHOPROOT)+1);
	}

	/*
		현재 페이지를 기준으로 인자로받은 URL의 경로를 알려 줍니다
	*/
	function link_mobile($semantic_absolute_url,$force_type='auto') {
		@include dirname(__FILE__) . "/../conf/config.mobileShop.php";
		$this->_prefix_dir = $cfgMobileShop['mobileShopRootDir'];
		if(substr($this->_prefix_dir,0,1) != '/')$this->_prefix_dir = '/'.$this->_prefix_dir;

		if(!preg_match('/^[a-zA-Z0-9]/',$semantic_absolute_url)) {
			return;
		}
		if($this->_is_pass_url($semantic_absolute_url)) {
			return $this->_prefix_dir.'/'.$semantic_absolute_url;
		}

		if($this->_ssl_domain) {
			if($_SERVER['HTTPS']) {
				if(($this->_is_ssl_url($semantic_absolute_url) && $force_type!='regular') || $force_type=='ssl') {
					return $this->_prefix_dir.'/'.$semantic_absolute_url;
				}
				else {
					return $this->_get_full_url('http',$this->_regular_domain,21,$this->_prefix_dir.'/'.$semantic_absolute_url);
				}
			}
			else {
				if(($this->_is_ssl_url($semantic_absolute_url) && $force_type!='regular') || $force_type=='ssl') {
					return $this->_get_full_url('https',$this->_ssl_domain,$this->_ssl_port,$this->_prefix_dir.'/'.$semantic_absolute_url);
				}
				else {
					return $this->_prefix_dir.'/'.$semantic_absolute_url;
				}
			}
		}
		elseif($this->_free_ssl_domain) {
			if($_SERVER['HTTPS']) {
				if($force_type=='ssl') {
					return $this->_prefix_dir.'/'.$semantic_absolute_url;
				}
				else {
					$tmp=explode('?',$semantic_absolute_url);
					$semantic_absolute_url_file=$tmp[0];
					$semantic_absolute_url_query=$tmp[1];
					parse_str($semantic_absolute_url_query,$ar_parsed);
					unset($ar_parsed['sess_id']);
					unset($ar_parsed['rd']);
					return $this->_get_full_url('http',$this->_regular_domain,21,$this->_prefix_dir.'/'.$semantic_absolute_url_file.'?'.http_build_query($ar_parsed));
				}
			}
			else {
				if($force_type=='ssl') {
					$tmp=explode('?',$semantic_absolute_url);
					$semantic_absolute_url_file=$tmp[0];
					$semantic_absolute_url_query=$tmp[1];

					parse_str($semantic_absolute_url_query,$ar_parsed);
					$ar_parsed['sess_id']=session_id();
					$ar_parsed['rd']=$this->_regular_domain;
					return $this->_get_full_url('https',$this->_free_ssl_domain,$this->_free_ssl_port,$this->_prefix_dir.'/'.$semantic_absolute_url_file.'?'.http_build_query($ar_parsed));
				}
				else {
					return $this->_prefix_dir.'/'.$semantic_absolute_url;
				}
			}
		}
		else {
			return $this->_prefix_dir.'/'.$semantic_absolute_url;
		}
	}

	/*
		프로그램 시작 때 실행되는 메소드
	*/
	function ready_refresh_mobile() {

		@include dirname(__FILE__) . "/../conf/config.mobileShop.php";
		$this->_prefix_dir = $cfgMobileShop['mobileShopRootDir'];
		if(substr($this->_prefix_dir,0,1) != '/')$this->_prefix_dir = '/'.$this->_prefix_dir;

		// SSL도메인이 아닌 다른 도메인으로 접근했을 경우 처리
		if($this->_ssl_domain && $_SERVER['SERVER_NAME']!=$this->_ssl_domain && strpos($_SERVER['SERVER_NAME'],'.godo.interpark.com') === false) {
			$refresh_url = $this->_get_full_url('http',$this->_ssl_domain,80,$_SERVER['REQUEST_URI']);
			if(!headers_sent()){
				header("Location:{$refresh_url}");exit;
			}else{
				echo "<script>location.replace('{$refresh_url}');</script>";
			}
			exit;
		}

		// 현재 호출된 페이지가 HTTPS프로토콜을 타야하는데 그렇지 않은 경우 처리
		$semantic_absolute_url=$this->_get_semantic_absolute_url_mobile();
		if($this->_is_pass_url($semantic_absolute_url)) {
			return;
		}

		if($this->_ssl_domain && $this->_is_ssl_url($semantic_absolute_url) && !$_SERVER['HTTPS']) {
			$refresh_url = $this->_get_full_url('https',$this->_ssl_domain,$this->_ssl_port,$_SERVER['REQUEST_URI']);
			if(is_array($_POST) && count($_POST)) {
				$serialize_post = array_formpost_serialize($_POST);
				echo "<form name='ssl_forward' action='{$refresh_url}' method='post'>\n";
				foreach($serialize_post as $each_input) {
					echo '<input type="hidden" name="'.$each_input['name'].'" value="'.htmlspecialchars($each_input['value']).'">';
				}
				echo "</form>";
				echo "<script>document.ssl_forward.submit();</script>";

			}
			else {
				echo "<script>location.replace('{$refresh_url}');</script>";
			}
			exit;
		}

		if($_SERVER['HTTPS'] && !$this->_is_ssl_url($semantic_absolute_url)) {
			$refresh_url = $this->_get_full_url('http',$this->_regular_domain,21,$_SERVER['REQUEST_URI']);
			echo "<script>location.replace('{$refresh_url}');</script>";
			exit;
		}
	}

	/*
		현재 페이지의 의미적 절대경로를 알려줍니다
	*/
	function _get_semantic_absolute_url_mobile() {
		return substr($_SERVER['SCRIPT_FILENAME'],strlen(MOBILEROOT)+1);
	}
}



?>
