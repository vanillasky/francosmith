<?php
include "../lib.php";
include "../../lib/categoryNewMethod.class.php";

// 상품분류 연결방식 Class
$categoryNewMethod	= Core::loader('categoryNewMethod');

// mode에 따른 처리
switch ($_GET['mode']){
	case "categoryLink":
		// 완료 퍼센트
		$completPercent	= 80;	// 여기에서는 완료를 80%로 지정

		// link_chk 필드의 여부를 체크 및 link_chk 필드 추가
		echo("<script>parent.document.getElementById('informationText').innerHTML='분석중';</script>\n");
		$categoryNewMethod->setLinkCheckFieldAdd();

		// 상품분류 연결방식 전환 처리
		$getData	= $categoryNewMethod->setCategoryLinkAdd($_GET['page'], $_GET['completCnt'], $_GET['totalCount']);

		// 프로그레스바 처리
		if ($_GET['totalCount'] > 0) {
			$perBar		= ( ($getData['limitStart'] + $getData['dataCnt']) / $_GET['totalCount']  * $completPercent ); //처리완료율 백분율로 환산
		}
		else {
			$perBar		= 80;
		}
		$perNum		= round($perBar,2);
		echo("<script>parent.document.getElementById('progressBar').style.width='".$perBar."%';</script>\n"); //pgogressbar 에 처리완료율 노출
		echo("<script>parent.document.getElementById('informationPercent').innerHTML='".$perNum." %';</script>\n");
		echo("<script>parent.document.getElementById('informationText').innerHTML='처리중..(".number_format($getData['completCnt'])." 개)';</script>\n");

		echo $getData['limitStart']."<br>";
		echo $getData['dataCnt']."<br>";
		echo $_GET['totalCount']."<br>";
		echo $perBar." %<br>";
		echo $perNum." %<br>";
		echo $getData['completCnt']."<br>";

		// 완료시
		if($perBar == $completPercent){
			// 상품분류 연결방식 전환 화일 저장
			require_once("../../lib/qfile.class.php");
			$qfile = new qfile();
			$qfile->open("../../conf/category_new_method");
			$qfile->write("_CATEGORY_NEW_METHOD_");
			$qfile->close();
			@chmod('../../conf/category_new_method',0707);

			echo("<script>parent.document.getElementById('progressBar').style.width='80%';</script>\n");
			echo("<script>parent.document.getElementById('informationText').innerHTML='분석중. 잠시만 기다려주세요.';</script>\n");
			echo "<script>location.replace('?mode=duplicationRemove')</script>";
		}
		else{ //100% 미달성 시 다음페이지로 이동해서 계속 처리
			$nextPage	= $_GET['page'] + 1;
			echo "<script>location.replace('?page=".$nextPage."&completCnt=".$getData['completCnt']."&totalCount=".$_GET['totalCount']."&mode=".$_GET['mode']."')</script>";
		}

		exit();
		break;

	case "duplicationRemove":
		// 중복 카테고리 제거
		$removeCnt	= $categoryNewMethod->duplicationRemove();

		// 처리 완료
		echo("<script>parent.document.getElementById('informationText').innerHTML='전환완료(".number_format($removeCnt).")';</script>\n");
		echo("<script>parent.document.getElementById('informationPercent').innerHTML='100 %';</script>\n");
		echo("<script>parent.document.getElementById('progressBar').style.width='100%';</script>\n");
		echo("<script>alert('모든 상품에 대한 상품분류 연결방식 전환에 성공하였습니다.');parent.parent.location.reload();</script>\n");
		exit();
	break;
}
?>