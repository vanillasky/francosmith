<?
/*------------------------------------------------------------------------------
�� Copyright 2005, Flyfox All right reserved.
@���ϳ���: �������ڵ���
@��������/������/������:
------------------------------------------------------------------------------*/

@include_once dirname(__FILE__) . "/../../lib.php";
@include_once dirname(__FILE__) . "/../../../conf/config.php";
@include_once dirname(__FILE__) . "/../../lib.skin.php";
@include_once dirname(__FILE__) . "/code.class.php";
$codi = new codi;

if ( $_GET['design_file'] != '' ) { // �� ��ɺ� ���̾ƿ�

	if(empty($cfg['tplSkinMobileWork']) === true) $cfg['tplSkinMobileWork'] = $cfg['tplSkinMobile'];

	$data_dir		= $codi->get_dirinfo( $dirpath = dirname( $_GET['design_file'] ) );	# Directory Data
	$data_file		= $codi->get_fileinfo( $_GET['design_file'] );						# File Data
	$data_default = $codi->get_fileinfo('default');
	//debug( $data_dir ); debug( $data_file );


	### �Է���Ÿ�� ����
	$form_type	= $data_dir['inc'];
	if ( $form_type == '' ) $form_type = 'file';

	if ($_GET['design_file'] == 'default'){ # ��ü���̾ƿ� ������
		$form_type = 'default';
	}
	else if ( in_array( $_GET['design_file'], array( 'outline/_header.htm', 'outline/_footer.htm' ) ) ){ # �ܰ����̾ƿ�
		$form_type = 'outline';
	}
	else if ( in_array( dirname( $_GET['design_file'] ), array( 'outline/header', 'outline/footer', 'outline/side' ) ) ){ # �ܰ��κе�����
		$form_type = 'outSection';
	}
	else if (file_exists(dirname(__FILE__) . "/../../../" . str_replace(".htm", ".php", $_GET['design_file'])) === false){ # ��ũ���
		$form_type = 'inc';
	}
	if ($form_type == 'file' || $form_type == 'inc'){
	
		if ( file_exists($tmp = dirname(__FILE__) . "/../../../data/skin_mobileV2/" . $cfg['tplSkinMobileWork'] . "/" . $_GET['design_file']) ){
			$file = @file( $tmp );
			$source = implode("",$file);
			if (preg_match("/\{ *# *header *\}/is", $source)){ $form_type = 'file'; }
			else { $form_type = 'inc'; }
		}
		
	}


	### ���/�ϴ�/��������� ���ϸ������
	if ($form_type == 'outSection'){
		unset($data_file['outline_header']);
		unset($data_file['outline_side']);
		unset($data_file['outline_footer']);
	}
	
	if ($form_type != 'inc')
	{
		$layout = array('header' => array(), 'side' => array(), 'footer' => array());
		$hidenm = array('header' => '��ܰ���', 'side' => '���鰨��', 'footer' => '�ϴܰ���');
		foreach ($layout as $k => $v)
		{
			$sFile = $data_file['outline_' . $k];
			if ($form_type == 'file' || $form_type == 'outSection'){
				$sDefault = $data_default['outline_' . $k];
			}

			$opt = &$layout[$k];
			if ($form_type == 'default' || $form_type == 'file')
			{
				$opt[0] = array(
					'text' => $hidenm[$k],
					'value' => 'noprint',
					'selected' => ($sFile == 'noprint' ? 'selected' : ''),
					);
				if ($form_type == 'file'){
					if ($sDefault == 'noprint'){
						$opt[0]['text'] .= ' ��';
						$opt[0]['value'] = 'default';
						$opt[0]['selected'] = ('' == $sFile ? 'selected' : $opt[0]['selected']);
					}
				}
			}

			$dirpath = "outline/{$k}/";
			$ls = $codi->get_dirList($dirpath);
			foreach( $ls as $file )
			{
				$tmp = array(
					'text' => ($file['text'] . ' - ' . $dirpath . $file['name']),
					'value' => ($dirpath . $file['name']),
					'selected' => ($sFile == $dirpath . $file['name'] ? 'selected' : ''),
					'path' => ($dirpath . $file['name']),
					);
				if ($form_type == 'file' || $form_type == 'outSection'){
					if ($tmp['value'] == $sDefault){
						$tmp['text'] .= ' ��';
						$tmp['value'] = 'default';
						$tmp['selected'] = ('' == $sFile ? 'selected' : $tmp['selected']);
					}
				}
				if ($form_type == 'outSection'){
					if (strpos($_GET['design_file'], $dirpath) !== false){
						$tmp['selected'] = ($_GET['design_file'] == $dirpath . $file['name'] ? 'selected' : '');
					}
				}
				$opt[] = $tmp;
			}
			unset($opt);
		}

	}

	### �Է���Ÿ�� ��� (default-��ü���̾ƿ�, outline-�ܰ����̾ƿ�, outSection-�ܰ��κе�����, inc-��ũ���, file-����)
	include_once dirname(__FILE__) . "/_lay_" . $form_type . ".php";
	if ($form_type != 'default') @include_once dirname(__FILE__) . "/_codi_multiply.php";
}

if ($_GET[design_file] && $_GET[design_file] != 'default'){ echo "<div id=\"codi_replacecode\"><script>DCRM.write('{$_GET[design_file]}');</script></div>"; }

?>