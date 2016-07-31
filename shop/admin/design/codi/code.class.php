<?

// 클래스

class codi {

	var $codi_path		= "";		# 코디경로
	var $skin_name		= "";		# 스킨폴더명
	var $skin_path		= "";		# 스킨폴더경로

	var $design_dir		= array();	# 템플릿 디렉토리 정의
	var $design_skin	= array();	# 템플릿 파일 정의

	var $dir_display	= array();	# 디렉토리 출력 제어
	var $notfile		= array( '.', '..', '.bash_logout', '.bash_profile', '.bashrc', '.emacs', '.bash_history', '.bash_logout.rpmnew', '.bash_profile.rpmnew', '.bashrc.rpmnew', 'common.js', 'style.css', 'intro.htm', 'intro_adult.htm', 'intro_member.htm', 'img' ); # 파일 출력 제어



	/*-------------------------------------
		Init
	-------------------------------------*/
	function codi (){

		@include dirname(__FILE__) . "/../../../conf/config.php";
		@include dirname(__FILE__) . "/../../lib.skin.php";
		$this->codi_path = dirname(__FILE__) . "/../../../data/skin/";
		$this->skin_name = $cfg['tplSkinWork'];
		$this->skin_path = $this->codi_path . $this->skin_name . '/';

		@include dirname(__FILE__) . "/../../../conf/design_dir.php";
		$this->design_dir = &$design_dir;

		@include dirname(__FILE__) . "/../../../conf/design_skin_" . $cfg['tplSkinWork'] . ".php";
		$this->design_skin = &$design_skin;

		$this->dir_display[] = array( 'path' => $this->skin_path, 'able' => '', 'unable' => 'img' );

		array_push($this->notfile, 'intro_adult.htm', 'intro_member.htm', 'intro_adult_login.htm', 'tpl');	// 추가된 인트로 (성인인증 회원전용) 목록에서 제외, ∴기존 인트로 설정 페이지에서 디자인 등등을 하기 때문. ,또 카트탭 내의 tpl 폴더 제외
	}



	/*-------------------------------------
		디렉토리 출력 제어 체크
		dirpath : 디렉토리 경로
	-------------------------------------*/
	function chkDirDisplay( $dirpath, $file ){

		foreach( $this->dir_display as $tmp ){

			if ( $tmp['path'] != $dirpath ) continue;
			if ( $tmp['able'] != '' && !in_array( $file, explode( ";", $tmp['able'] ) ) ) return false;
			if ( $tmp['unable'] != '' && in_array( $file, explode( ";", $tmp['unable'] ) ) ) return false;
		}

		return true;
	}



	/*-------------------------------------
		디렉토리 정보
		dirpath : 디렉토리 경로
	-------------------------------------*/
	function get_dirinfo( $dirpath ){

		if ( $this->skin_path != substr( $dirpath, 0, strlen( $this->skin_path ) ) ) $dirpath = $this->skin_path . $dirpath;

		$tmp = array( 'type' => 'dir', 'name' => basename( $dirpath ), 'size' => filesize( $dirpath ), 'date' => filemtime( $dirpath ) );

		$design = $this->design_dir[ str_replace( $this->skin_path, "", $dirpath ) . '/' ];

		if( is_array( $design ) ) $tmp = array_merge( $tmp, $design );

		return $tmp;
	}



	/*-------------------------------------
		파일 정보
		filepath : 파일 경로
	-------------------------------------*/
	function get_fileinfo( $filepath ){

		if ( $this->skin_path != substr( $filepath, 0, strlen( $this->skin_path ) ) ) $filepath = $this->skin_path . $filepath;

		$tmp = array( 'type' => 'file', 'name' => basename( $filepath ), 'size' => @filesize( $filepath ), 'date' => @filemtime( $filepath ) );

		$design = $this->design_skin[ str_replace( $this->skin_path, "", $filepath ) ];

		if ( $design['text'] == '' || $design['linkurl'] == '' ){

			$fd = @fopen( $filepath, "r" );
			$contents = @fread( $fd, 150 );
			@fclose( $fd );

			preg_match("/\{\*\*\*( .*)\*\*\*\}/i", $contents, $matches);
			$matches = explode( "|", $matches[1] );

			if ( $design['text'] == '' ) $design['text'] = trim( $matches[0] );
			if ( $design['linkurl'] == '' ) $design['linkurl'] = trim( $matches[1] );
		}

		if( is_array( $design ) ) $tmp = array_merge( $tmp, $design );

		return $tmp;
	}



	/*-------------------------------------
		디렉토리 내용 리턴
		dirpath : 디렉토리 경로
	-------------------------------------*/
	function get_dirList( $dirpath='' ){

		$arr = array( 'dir' => array(), 'file' => array() );

		if ( $dirpath == '' ) $dirpath = $this->skin_path;
		if ( $this->skin_path != substr( $dirpath, 0, strlen( $this->skin_path ) ) ) $dirpath = $this->skin_path . $dirpath;
		$dir = $dirpath;

		if ( !( $dh = @opendir( $dir ) ) ) return array();

		while ( ( $file = @readdir( $dh ) ) !==  false ) {
			// 2013-08-08 slowj __gd__history, .svn,  __gd__preview 디렉토리 제거.
			if ( (@filetype( $dir . $file ) == 'dir' && in_array($file, array('__gd__history', '.svn', '__gd__preview'))) ) continue;
			if ( preg_match("/(ico|gif|jpg|bmp)$/i", $file) ) continue;
			if ( in_array( $file, $this->notfile) ) continue;
			if ( @filetype( $dir . $file ) == 'dir' && $this->chkDirDisplay( $dirpath, $file ) !== true ) continue;

			if ( @filetype( $dir . $file ) == 'dir' ) $arr[dir][] = $this->get_dirinfo( $dir . $file );
			else $arr[file][] = $this->get_fileinfo( $dir . $file );
		}

		@closedir( $dh );


		$result = array();
		foreach ( $arr as $b_key => $b_arr ){ // 정렬

			if ( count( $b_arr ) <= 1 ){
				$result[ $b_key ] = $b_arr;
				continue;
			}

			$tmp = array();
			foreach ( $b_arr as $s_key => $s_arr ) $tmp[ $s_key ] = strtolower( $s_arr[ 'name' ] ); // 임시 공간에 저장

			asort( $tmp );
			reset( $tmp );

			foreach ( $tmp as $k => $v ) $result[ $b_key ][] = $arr[ $b_key ][ $k ];  // 리턴 공간으로 데이타 이전
		}


		$arr = array();
		foreach ( $result as $b_arr ){ // 병합
			if ( count( $b_arr ) ) $arr = array_merge( $arr, $b_arr );
		}

		return $arr;
	}
}





// 트리

class codiTree extends codi {

	### 순서 정의
	var $sequence = array(
		'outline/',
		'main/',
		'goods/',
		'order/',
		'member/',
		'mypage/',
		'board/',
		'service/',
		'proc/',
		'default',
		'outline/_header.htm',
		'outline/_footer.htm',
		'outline/header/',
		'outline/footer/',
		'outline/side/',
		);

	### 선행 노드
	var $ahead = array(
		array(
			'type' => 'dir',
			'name' => 'bundleHead',
			'text' => '기본관리',
			'child' => array(
				array(
					'type' => 'dir',
					'name' => 'popup',
					'text' => '메인팝업창',
					),
				array(
					'type' => 'dir',
					'name' => 'bundlePrivate',
					'text' => '개인정보취급방침',
					'child' => array(
						array(
							'type' => 'doc',
							'name' => '../design/iframe.checkprivacy.php',
							'text' => '개인정보취급방침 안내 및 설정',
							),
						array(
							'type' => 'doc',
							'name' => 'service/_private.txt',
							'text' => '개인정보취급방침 내용',
							),
						array(
							'type' => 'doc',
							'name' => 'service/_private1.txt',
							'text' => '개인정보 이용자 동의사항',
							),
						array(
							'type' => 'doc',
							'name' => 'service/_private2.txt',
							'text' => '개인정보 제3자 제공관련',
							),
						array(
							'type' => 'doc',
							'name' => 'service/_private3.txt',
							'text' => '개인정보 취급업무 위탁관련',
							),
						array(
							'type' => 'doc',
							'name' => 'service/_private_non.txt',
							'text' => '비회원 개인정보 취급방침 내용',
							),
						),
					),
					array(
					'type' => 'dir',
					'name' => 'bundleIntro',
					'text' => '인트로/공사중페이지',
					'child' => array(
						array(
							'type' => 'doc',
							'name' => 'main/intro.htm',
							'text' => '일반 인트로 페이지',
							),
						array(
							'type' => 'doc',
							'name' => 'main/intro_adult.htm',
							'text' => '성인 전용 인트로',
							),
						array(
							'type' => 'doc',
							'name' => 'main/intro_adult_login.htm',
							'text' => '회원 성인인증 인트로',
							),
						array(
							'type' => 'doc',
							'name' => 'main/intro_member.htm',
							'text' => '회원 전용 인트로',
							),
						),
					),
				array(
					'type' => 'doc',
					'name' => 'style.css',
					'text' => '스타일시트',
					),
				array(
					'type' => 'doc',
					'name' => 'common.js',
					'text' => '자바스크립트',
					),
				array(
					'type' => 'doc',
					'name' => 'proc/_agreement.txt',
					'text' => '이용약관',
					),
				),
			),
		);
	var $aheadNode = array();
	var $aheadRef = array();



	/*-------------------------------------
		Init
	-------------------------------------*/
	function codiTree (){
		$this->codi();
	}



	/*-------------------------------------
		트리 데이터
	-------------------------------------*/
	function getTree ($dirfiles){

		$this->opened = explode("|", $_COOKIE['opened']);

		foreach ($this->ahead as $ak => $anode){
			array_push($this->aheadNode, $anode['name'] . '/');
			array_push($this->aheadRef, &$this->ahead[$ak]);
			foreach ($anode['child'] as $ck => $cnode){
				if ($cnode['type'] == 'dir'){
					array_push($this->aheadNode, $cnode['name'] . '/');
					array_push($this->aheadRef, &$this->ahead[$ak]['child'][$ck]);
				}
			}
		}

		$this->sequence = array_merge($this->aheadNode, $this->sequence);
		$json_var = $this->getSection($dirfiles);

		include_once dirname(__FILE__)."/../../../lib/json.class.php";
		$json = new Services_JSON();
		$this->output = $json->encode($json_var);
		$this->obOut = ob_get_clean();
	}



	/*-------------------------------------
		PATH 내 디렉토리&파일
	-------------------------------------*/
	function getSection ($path){

		$list = array();
		$dirList = $this->get_dirList($path);
		if ($path == '') $dirList = array_merge($this->ahead, $dirList);

		if ($path != 'popup/') $nodekey = array_search($path, $this->aheadNode);
		else $nodekey = false;
		if ($nodekey !== false){
			$dirList = array_merge($this->aheadRef[$nodekey]['child'], $dirList);
		}

		foreach ($dirList as $arr)
		{
			$id = ($arr['type'] == 'dir' ? $path . $arr['name'] . "/" : $path . $arr['name']);
			if ($nodekey !== false) $id = str_replace($path, '', $id);
			if ($nodekey === false && $this->ahead_search($path . $arr['name'])) continue;

			$key = array_search($id, $this->sequence);
			if ($key === false) $tmp = &$list['after'][];
			else $tmp = &$list['before'][$key];

			$tmp['catnm'] = $this->resetname($id, $arr['text'], $arr['name']);
			$tmp['id'] = $id;
			$tmp['folder'] = ($arr['type'] == 'dir' ? 'folder' : 'doc');
			if (in_array($tmp['id'], $this->opened)) $tmp['childNodes'] = $this->getSection($tmp['id']);
		}
		if ($path == 'outline/'){
			$key = array_search('default', $this->sequence);
			$list['before'][$key] = array('catnm' => '전체레이아웃 설정하기', 'id' => 'default', 'folder' => 'doc');
		}
		if (is_array($list['before'])) ksort ($list['before']);
		return array_merge((array)$list['before'], (array)$list['after']);
	}



	/*-------------------------------------
		name 재정의
	-------------------------------------*/
	function resetname ($id, $text, $name){

		if ($id == 'outline/') $text = '전체레이아웃 디자인';
		else if ($id == 'main/') $text = '메인페이지 디자인';
		return ($text ? $text : $name);
	}



	/*-------------------------------------
		선행 디렉토리&파일 검색, 선행 노드 name 리턴
	-------------------------------------*/
	function ahead_search($child){

		foreach ($this->ahead as $anode)
		{
			foreach ($anode['child'] as $cnode)
			{
				if ($cnode['name'] == $child) return $anode['name'];
			}
		}
	}



	/*-------------------------------------
		쿠키 재정의
	-------------------------------------*/
	function resetCookie($design_file){

		$opened = explode("|", $_COOKIE['opened']);
		$node = $this->ahead_search($design_file);
		if ($node){
			$design_file = $node . '/' . $design_file;
		}
		foreach(explode("/", $design_file) as $k){
			$tmp2 .= $k . '/';
			if (strpos($k, '.') === false) $opened[] = $tmp2;
		}
		$opened = (implode("|", array_unique ($opened)));
		return $opened;
	}

}
?>
