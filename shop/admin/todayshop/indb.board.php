<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

$id = ($_POST['id']) ? $_POST['id'] : $_GET['id'];
$mode = ($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];

switch ($mode){

	case "register": case "modify":

		$_POST['id'] = str_replace(" ","_",$_POST['id']);

		$El		= array(
				"bdName",			// �Խ��� �̸�
				"bdGroup",			// �׷�
				"bdSkin",			// ��Ų
				"bdAlign",			// ���̺� ����
				"bdWidth",			// ���̺� ũ��
				"bdStrlen",			// ���� �ڸ���
				"bdPageNum",		// �� ������ �ۼ�
				"bdNew",			// ���� ���� �ð�
				"bdHot",			// �α�� ��ȸ��
				"bdNoticeList",		// ������ ����
				"bdLvlL",			// ���� (����Ʈ)
				"bdLvlR",			// ���� (�б�)
				"bdLvlC",			// ���� (�ڸ�Ʈ)
				"bdLvlW",			// ���� (����)
				"bdIp",				// ������ ��� ����
				"bdIpAsterisk",		// ������ ��ǥ ����
				"bdTypeView",		// �� ���� Ÿ��
				"bdUseLink",		// ��ũ ���	 ����
				"bdUseFile",		// ���ε� ��� ����
				"bdMaxSize",		// ���ε� �ִ� ���� ������
				"bdTypeMail",		// ���� Ÿ��
				"bdHeader",			// �ش�
				"bdFooter",			// Ǫ��
				"bdUseSubSpeech",	// ���Ӹ� ��� ����
				"bdSubSpeechTitle",	// ���Ӹ� Ÿ��Ʋ
				"bdSubSpeech",		// ���Ӹ�
				"bdUseComment",		// �ڸ�Ʈ ��� ����
				"bdSearchMode",		// �˻� ���
				"bdField",			// ���� �ʵ�
				"bdImg",			// ��Ų (�̹��� ����)
				"bdColor",			// ��Ų (�����ڵ�),
				"bdPrnType",		// ����Ʈ�������
				"bdListImgCntW",	// ����Ʈ�̹�������
				"bdListImgCntH",	// ����Ʈ�̹�������
				"bdListImgSizeW",	// ����Ʈ�̹���ũ��
				"bdListImgSizeH",	// ����Ʈ�̹���ũ��
				"bdListImg",		// ����Ʈ�̹�����ũ
				"bdUserDsp",		// �ۼ���ǥ��
				"bdAdminDsp",		// ������ǥ��
				"bdSpamComment",	// �ڸ�Ʈ ���Թ���
				"bdSpamBoard",		// �Խñ� ���Թ���
				"bdSecretChk",		// ��б� ����
				"bdTitleCChk",		// ���� ���ڻ� ���
				"bdTitleSChk",		// ���� ����ũ�� ���
				"bdTitleBChk",		// ���� ���ڱ��� ���
				"bdEmailNo",		// �̸��� �ۼ�
				"bdEditorChk",		// �������̹������ε� ���
				"bdHomepageNo"		// Ȩ������ �ۼ�
				);
		if($_POST['bdMaxSize']  > 2 * 1048576){
			msg('�ִ� ���ε� ������ 2Mbyte �Դϴ�.',-1);
			exit;
		}
		$_POST['bdSubSpeech']	= str_replace("\r\n","|",$_POST['bdSubSpeech']);
		$_POST['bdField']		= @array_sum($_POST['bdField']);
		$_POST['bdSpamComment']	= @array_sum($_POST['bdSpamComment']);
		$_POST['bdSpamBoard']	= @array_sum($_POST['bdSpamBoard']);
		if(!$_POST['bdEditorChk']) $_POST['bdEditorChk'] = 0;

		if($_POST['bdSkin'] == "gallery"){
			$_POST['bdPageNum']	= $_POST['bdListImgCntW'] * $_POST['bdListImgCntH'];
		}

		$_POST	= array_map("stripslashes",$_POST);
		$_POST	= array_map("addslashes",$_POST);

		$qfile->open("../../conf/bd_".$_POST['id'].".php");
		$qfile->write("<?\n");
		for ($i=0;$i<count($El);$i++) $qfile->write("\$$El[$i]=\"{$_POST[$El[$i]]}\";\n");
		$qfile->write("?>");
		$qfile->close();
		@chmod("../../conf/bd_".$_POST['id'].".php",0707);

		if ($_POST['mode']=="register"){

			$data = $db->fetch("select * from ".GD_BOARD." where id='".$_POST['id']."'");
			if ($data) msg("�̹� ".$_POST['id']." �ڵ��� �Խ����� �����մϴ�",-1);
			$db->query("insert into ".GD_BOARD." set id='".$_POST['id']."'");

			### �Խ��� ���� ��� ���̺� ����
			$dir = "../../data/board/$id"; mkdir($dir,0707); chmod($dir,0707);
			$dir = "../../data/board/$id/t"; mkdir($dir,0707); chmod($dir,0707);
			$db->query("create table ".GD_BD_.$_POST['id']."(
						no			int unsigned not null auto_increment primary key,
						idx			char(4) not null,
						main		int not null,
						sub			char(10) binary not null,
						name		varchar(20),
						email		varchar(50),
						homepage	varchar(100),
						titleStyle	varchar(50),
						subject		varchar(100),
						contents	text,
						urlLink		varchar(255),
						old_file	varchar(255),
						new_file	varchar(255),
						password	char(32),
						m_no		int unsigned,
						_pass		char(32),
						_member		int,
						ip			char(15) not null,
						notice		char(1),
						secret		char(1),
						html		tinyint,
						hit			int unsigned not null default 0,
						comment		smallint unsigned not null default 0,
						category	varchar(50),
						regdt		datetime,
						key idx(idx,main,sub),
						key idx2(category,idx,main,sub)
						)");
			$db->query("insert into ".GD_BD_.$_POST['id']." (main) values (0)");
		}
		//go("board_list.php");
		break;

	case "inf":

		$res = $db->query("select idx,count(*) as z from ".GD_BD_.$id." where idx!='' group by idx");
		while ($data=$db->fetch($res)){
			list ($chk) = $db->fetch("select * from ".GD_BOARD_INF." where id='".$id."' and idx='".$data['idx']."'");
			if ($chk) $db->query("update ".GD_BOARD_INF." set num='".$data['z']."' where id='".$id."' and idx='".$data['idx']."'");
			else $db->query("insert into ".GD_BOARD_INF." set num='".$data['z']."', id='".$id."', idx='".$data['idx']."'");
		}
		msg("$id �Խ����� ���������� �����Ǿ����ϴ�");
		break;

	case "drop":
		if(trim($id) == "notice"){
			msg('���������� �����Ͻ� �� �����ϴ�.');
			exit;
		}

		$dir	= "../../data/board/$id";
		$dirSub	= "../../data/board/$id/t";

		if (is_dir($dirSub)){
			$od = opendir($dirSub);
			while ($rd=readdir($od)) if ($rd!="." && $rd!="..") @unlink("$dirSub/$rd");
			closedir($od);
			rmdir($dirSub);
		}

		if (is_dir($dir)){
			$od = opendir($dir);
			while ($rd=readdir($od)) if ($rd!="." && $rd!="..") @unlink("$dir/$rd");
			closedir($od);
			rmdir($dir);
		}

		@unlink("../../conf/bd_$id.php");

		$db->query("drop table ".GD_BD_.$id);
		$db->query("delete from ".GD_BOARD." where id='$id'");

		### �����뷮 ���
		setDu('board');

		msg("$id �Խ����� ���������� �����Ǿ����ϴ�");
		echo "<script>parent.location.reload();</script>";
		break;

	case "adminicon":

		$_BGFILES = array( 'icon_up' => $_FILES['icon_up'] );
		$userori = array( 'icon' => 'admin' . strrChr( $_FILES['icon_up']['name'], "." ) );

		@include_once dirname(__FILE__) . "/webftp/webftp.class_outcall.php";
		outcallUpload( $_BGFILES, '/', $userori );

		msg("������ ������ ������ ���������� ó���Ǿ����ϴ�");
		break;

	case "captcha":

		@include ($path = "../../conf/captcha.php");

		$captcha = array_map("stripslashes",$captcha);
		$captcha = array_map("addslashes",$captcha);
		$captcha = array_merge($captcha,$_POST[captcha]);

		$qfile->open($path);
		$qfile->write("<? \n");
		$qfile->write("\$captcha = array( \n");
		foreach ($captcha as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();
		chMod( $path, 0757 );
		break;
}

go($_SERVER[HTTP_REFERER]);

?>