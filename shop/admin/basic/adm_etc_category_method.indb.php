<?php
include "../lib.php";
include "../../lib/categoryNewMethod.class.php";

// ��ǰ�з� ������ Class
$categoryNewMethod	= Core::loader('categoryNewMethod');

// mode�� ���� ó��
switch ($_GET['mode']){
	case "categoryLink":
		// �Ϸ� �ۼ�Ʈ
		$completPercent	= 80;	// ���⿡���� �ϷḦ 80%�� ����

		// link_chk �ʵ��� ���θ� üũ �� link_chk �ʵ� �߰�
		echo("<script>parent.document.getElementById('informationText').innerHTML='�м���';</script>\n");
		$categoryNewMethod->setLinkCheckFieldAdd();

		// ��ǰ�з� ������ ��ȯ ó��
		$getData	= $categoryNewMethod->setCategoryLinkAdd($_GET['page'], $_GET['completCnt'], $_GET['totalCount']);

		// ���α׷����� ó��
		if ($_GET['totalCount'] > 0) {
			$perBar		= ( ($getData['limitStart'] + $getData['dataCnt']) / $_GET['totalCount']  * $completPercent ); //ó���Ϸ��� ������� ȯ��
		}
		else {
			$perBar		= 80;
		}
		$perNum		= round($perBar,2);
		echo("<script>parent.document.getElementById('progressBar').style.width='".$perBar."%';</script>\n"); //pgogressbar �� ó���Ϸ��� ����
		echo("<script>parent.document.getElementById('informationPercent').innerHTML='".$perNum." %';</script>\n");
		echo("<script>parent.document.getElementById('informationText').innerHTML='ó����..(".number_format($getData['completCnt'])." ��)';</script>\n");

		echo $getData['limitStart']."<br>";
		echo $getData['dataCnt']."<br>";
		echo $_GET['totalCount']."<br>";
		echo $perBar." %<br>";
		echo $perNum." %<br>";
		echo $getData['completCnt']."<br>";

		// �Ϸ��
		if($perBar == $completPercent){
			// ��ǰ�з� ������ ��ȯ ȭ�� ����
			require_once("../../lib/qfile.class.php");
			$qfile = new qfile();
			$qfile->open("../../conf/category_new_method");
			$qfile->write("_CATEGORY_NEW_METHOD_");
			$qfile->close();
			@chmod('../../conf/category_new_method',0707);

			echo("<script>parent.document.getElementById('progressBar').style.width='80%';</script>\n");
			echo("<script>parent.document.getElementById('informationText').innerHTML='�м���. ��ø� ��ٷ��ּ���.';</script>\n");
			echo "<script>location.replace('?mode=duplicationRemove')</script>";
		}
		else{ //100% �̴޼� �� ������������ �̵��ؼ� ��� ó��
			$nextPage	= $_GET['page'] + 1;
			echo "<script>location.replace('?page=".$nextPage."&completCnt=".$getData['completCnt']."&totalCount=".$_GET['totalCount']."&mode=".$_GET['mode']."')</script>";
		}

		exit();
		break;

	case "duplicationRemove":
		// �ߺ� ī�װ� ����
		$removeCnt	= $categoryNewMethod->duplicationRemove();

		// ó�� �Ϸ�
		echo("<script>parent.document.getElementById('informationText').innerHTML='��ȯ�Ϸ�(".number_format($removeCnt).")';</script>\n");
		echo("<script>parent.document.getElementById('informationPercent').innerHTML='100 %';</script>\n");
		echo("<script>parent.document.getElementById('progressBar').style.width='100%';</script>\n");
		echo("<script>alert('��� ��ǰ�� ���� ��ǰ�з� ������ ��ȯ�� �����Ͽ����ϴ�.');parent.parent.location.reload();</script>\n");
		exit();
	break;
}
?>