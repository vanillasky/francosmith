<?

// Ŭ����

class codi {

	var $codi_path		= "";		# �ڵ���
	var $skin_name		= "";		# ��Ų������
	var $skin_path		= "";		# ��Ų�������

	var $design_dir		= array();	# ���ø� ���丮 ����
	var $design_skin	= array();	# ���ø� ���� ����

	var $dir_display	= array();	# ���丮 ��� ����
	var $notfile		= array( '.', '..', '.bash_logout', '.bash_profile', '.bashrc', '.emacs', '.bash_history', '.bash_logout.rpmnew', '.bash_profile.rpmnew', '.bashrc.rpmnew', 'common.js', 'style.css', 'intro.htm', 'intro_adult.htm', 'intro_member.htm', 'img' ); # ���� ��� ����



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

		array_push($this->notfile, 'intro_adult.htm', 'intro_member.htm', 'intro_adult_login.htm', 'tpl');	// �߰��� ��Ʈ�� (�������� ȸ������) ��Ͽ��� ����, �ű��� ��Ʈ�� ���� ���������� ������ ����� �ϱ� ����. ,�� īƮ�� ���� tpl ���� ����
	}



	/*-------------------------------------
		���丮 ��� ���� üũ
		dirpath : ���丮 ���
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
		���丮 ����
		dirpath : ���丮 ���
	-------------------------------------*/
	function get_dirinfo( $dirpath ){

		if ( $this->skin_path != substr( $dirpath, 0, strlen( $this->skin_path ) ) ) $dirpath = $this->skin_path . $dirpath;

		$tmp = array( 'type' => 'dir', 'name' => basename( $dirpath ), 'size' => filesize( $dirpath ), 'date' => filemtime( $dirpath ) );

		$design = $this->design_dir[ str_replace( $this->skin_path, "", $dirpath ) . '/' ];

		if( is_array( $design ) ) $tmp = array_merge( $tmp, $design );

		return $tmp;
	}



	/*-------------------------------------
		���� ����
		filepath : ���� ���
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
		���丮 ���� ����
		dirpath : ���丮 ���
	-------------------------------------*/
	function get_dirList( $dirpath='' ){

		$arr = array( 'dir' => array(), 'file' => array() );

		if ( $dirpath == '' ) $dirpath = $this->skin_path;
		if ( $this->skin_path != substr( $dirpath, 0, strlen( $this->skin_path ) ) ) $dirpath = $this->skin_path . $dirpath;
		$dir = $dirpath;

		if ( !( $dh = @opendir( $dir ) ) ) return array();

		while ( ( $file = @readdir( $dh ) ) !==  false ) {
			// 2013-08-08 slowj __gd__history, .svn,  __gd__preview ���丮 ����.
			if ( (@filetype( $dir . $file ) == 'dir' && in_array($file, array('__gd__history', '.svn', '__gd__preview'))) ) continue;
			if ( preg_match("/(ico|gif|jpg|bmp)$/i", $file) ) continue;
			if ( in_array( $file, $this->notfile) ) continue;
			if ( @filetype( $dir . $file ) == 'dir' && $this->chkDirDisplay( $dirpath, $file ) !== true ) continue;

			if ( @filetype( $dir . $file ) == 'dir' ) $arr[dir][] = $this->get_dirinfo( $dir . $file );
			else $arr[file][] = $this->get_fileinfo( $dir . $file );
		}

		@closedir( $dh );


		$result = array();
		foreach ( $arr as $b_key => $b_arr ){ // ����

			if ( count( $b_arr ) <= 1 ){
				$result[ $b_key ] = $b_arr;
				continue;
			}

			$tmp = array();
			foreach ( $b_arr as $s_key => $s_arr ) $tmp[ $s_key ] = strtolower( $s_arr[ 'name' ] ); // �ӽ� ������ ����

			asort( $tmp );
			reset( $tmp );

			foreach ( $tmp as $k => $v ) $result[ $b_key ][] = $arr[ $b_key ][ $k ];  // ���� �������� ����Ÿ ����
		}


		$arr = array();
		foreach ( $result as $b_arr ){ // ����
			if ( count( $b_arr ) ) $arr = array_merge( $arr, $b_arr );
		}

		return $arr;
	}
}





// Ʈ��

class codiTree extends codi {

	### ���� ����
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

	### ���� ���
	var $ahead = array(
		array(
			'type' => 'dir',
			'name' => 'bundleHead',
			'text' => '�⺻����',
			'child' => array(
				array(
					'type' => 'dir',
					'name' => 'popup',
					'text' => '�����˾�â',
					),
				array(
					'type' => 'dir',
					'name' => 'bundlePrivate',
					'text' => '����������޹�ħ',
					'child' => array(
						array(
							'type' => 'doc',
							'name' => '../design/iframe.checkprivacy.php',
							'text' => '����������޹�ħ �ȳ� �� ����',
							),
						array(
							'type' => 'doc',
							'name' => 'service/_private.txt',
							'text' => '����������޹�ħ ����',
							),
						array(
							'type' => 'doc',
							'name' => 'service/_private1.txt',
							'text' => '�������� �̿��� ���ǻ���',
							),
						array(
							'type' => 'doc',
							'name' => 'service/_private2.txt',
							'text' => '�������� ��3�� ��������',
							),
						array(
							'type' => 'doc',
							'name' => 'service/_private3.txt',
							'text' => '�������� ��޾��� ��Ź����',
							),
						array(
							'type' => 'doc',
							'name' => 'service/_private_non.txt',
							'text' => '��ȸ�� �������� ��޹�ħ ����',
							),
						),
					),
					array(
					'type' => 'dir',
					'name' => 'bundleIntro',
					'text' => '��Ʈ��/������������',
					'child' => array(
						array(
							'type' => 'doc',
							'name' => 'main/intro.htm',
							'text' => '�Ϲ� ��Ʈ�� ������',
							),
						array(
							'type' => 'doc',
							'name' => 'main/intro_adult.htm',
							'text' => '���� ���� ��Ʈ��',
							),
						array(
							'type' => 'doc',
							'name' => 'main/intro_adult_login.htm',
							'text' => 'ȸ�� �������� ��Ʈ��',
							),
						array(
							'type' => 'doc',
							'name' => 'main/intro_member.htm',
							'text' => 'ȸ�� ���� ��Ʈ��',
							),
						),
					),
				array(
					'type' => 'doc',
					'name' => 'style.css',
					'text' => '��Ÿ�Ͻ�Ʈ',
					),
				array(
					'type' => 'doc',
					'name' => 'common.js',
					'text' => '�ڹٽ�ũ��Ʈ',
					),
				array(
					'type' => 'doc',
					'name' => 'proc/_agreement.txt',
					'text' => '�̿���',
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
		Ʈ�� ������
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
		PATH �� ���丮&����
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
			$list['before'][$key] = array('catnm' => '��ü���̾ƿ� �����ϱ�', 'id' => 'default', 'folder' => 'doc');
		}
		if (is_array($list['before'])) ksort ($list['before']);
		return array_merge((array)$list['before'], (array)$list['after']);
	}



	/*-------------------------------------
		name ������
	-------------------------------------*/
	function resetname ($id, $text, $name){

		if ($id == 'outline/') $text = '��ü���̾ƿ� ������';
		else if ($id == 'main/') $text = '���������� ������';
		return ($text ? $text : $name);
	}



	/*-------------------------------------
		���� ���丮&���� �˻�, ���� ��� name ����
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
		��Ű ������
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
