<?
// 필요파일 로드
	include "../lib/library.php";

// 파라메터 가공 및 정의
	$_GET = $_POST;		// 카테고리

// 클래스 로드
	$smartSearch = Core::loader('smartSearch');

// 해당 카테고리의 코드
	$smartSearch->menuName = iconv("utf-8", "euc-kr", $_POST['menuName']);
	$smartSearch->searchID = iconv("utf-8", "euc-kr", $_POST['searchID']);
	$smartSearch->loadTheme();
	echo $smartSearch->getOption();
?>
