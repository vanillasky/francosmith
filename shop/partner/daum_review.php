<?
set_time_limit(0);
include "../lib/library.php";
include "../lib/partner.class.php";
include "../conf/daumCpc.cfg.php";
include "../lib/qfile.class.php";

if ($daumCpc['useYN']!= 'Y') exit;

function reviewWrite($reviewData,$handle,$status) {
	fwrite($handle,'<<<begin>>>'.chr(10));
	fwrite($handle,'<<<mapid>>>'.$reviewData['goodsno'].chr(10));
	fwrite($handle,'<<<reviewid>>>'.$reviewData['goodsno'].$reviewData['sno'].chr(10));
	fwrite($handle,'<<<status>>>'.$status.chr(10));
	fwrite($handle,'<<<title>>>'.strip_tags($reviewData['subject']).chr(10));
	fwrite($handle,'<<<content>>>'.strip_tags($reviewData['contents']).chr(10));
	if (mb_strlen($reviewData['name'],'euc-kr') < 3) {
		fwrite($handle,'<<<writer>>>'.$reviewData['name'].'**'.chr(10));
	}
	else {
		fwrite($handle,'<<<writer>>>'.mb_substr($reviewData['name'],0,mb_strlen($reviewData['name'],'euc-kr')-2,'euc-kr').'**'.chr(10));
	}
	fwrite($handle,'<<<cdate>>>'.$reviewData['regdt'].chr(10));
	fwrite($handle,'<<<rating>>>'.$reviewData['point'].'/5'.chr(10));
	fwrite($handle,'<<<ftend>>>'.chr(10));
}

global $db;
$total = $_GET['total'];
$partner = new Partner();
$temp = array();
$tocnt = 0;
$arrCnt = 0;
$interval = 200000;

$goodsno = array( 'a.goodsno' );
$query = $partner->getGoodsSqlNew($goodsno);

// 전체 수집, 요약 수집 분기
if ($total != 'y') {
	$where = 'sno=parent and regdt>curdate()-1';
}
else {
	$where = 'sno=parent';
}

$tmpFile = dirname(__FILE__).'/../conf/reviewTemp.php';
if (file_exists($tmpFile)) unlink($tmpFile);
$handle = fopen($tmpFile,'a');

$res = $db->query($query);
while ($data = $db->fetch($res,1)) {
	if ($arrCnt > $interval) {
		$interval += 200000;
		$temp = array();
	}

	if ($arrCnt <= $interval) $temp[] = $data['goodsno'];

	if ($arrCnt == $interval) {
		$query = "select sno,goodsno,subject,contents,point,date_format(regdt,'%Y%m%d%H%i%s') regdt,name from gd_goods_review where ".$where." and goodsno in (".implode(',',$temp).")";
		$review = $db->query($query);
		while ($reviewData = $db->fetch($review,1)) {
			reviewWrite($reviewData,$handle,'S');
			$tocnt++;
		}
	}
	$arrCnt++;
}

if ($arrCnt < $interval) {
	$query = "select sno,goodsno,subject,contents,point,date_format(regdt,'%Y%m%d%H%i%s') regdt,name from gd_goods_review where ".$where." and goodsno in (".implode(',',$temp).")";
	$review = $db->query($query);
	while ($reviewData = $db->fetch($review,1)) {
		reviewWrite($reviewData,$handle,'S');
		$tocnt++;
	}
}
unset($temp);

// 전체 수집시에는 삭제된 상품평을 수집할 필요가 없음
if ($total != 'y') {
	daum_goods_review_check();

	$query = "select sno,goodsno,subject,contents,point,date_format(regdt,'%Y%m%d%H%i%s') regdt,name from ".GD_GOODS_UPDATE_REVIEW_DAUM;
	$deleteReview = $db->query($query);

	while ($deleteData = $db->fetch($deleteReview,1)) {
		reviewWrite($deleteData,$handle,'D');
		$tocnt++;
	}
}

fclose($handle);

$handle = fopen($tmpFile,'r');

header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: text/plain; charset=euc-kr");

echo '<<<tocnt>>>'.$tocnt.chr(10);
while (!feof($handle)) {
	echo fgets($handle, 16384);
}

fclose($handle);
unlink($tmpFile);
?>