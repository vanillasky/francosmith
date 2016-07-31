<?
include "../lib/library.php";
include "../lib/partner.class.php";
include "../conf/daumCpc.cfg.php";

if ($daumCpc['useYN']!= 'Y') exit;

// �г��� �ڸ���
function sub_string($string,$length) {
	$string = mb_substr($string,0,$length,'euc-kr').'**';
	return $string;
}

// ��ǰ��EP ù ������
if ($daumCpc['try'] != 'Y') {
	$daumCpc['try'] = 'Y';
	$qfile = Core::loader('qfile');
	$qfile->open('../conf/daumCpc.cfg.tmp');
	$qfile->write('<?php'.PHP_EOL);
	$qfile->write('$daumCpc = array('.PHP_EOL);
	foreach ($daumCpc as $name => $value) {
		$qfile->write("'".$name."' => '".$value."',".PHP_EOL);
	}
	$qfile->write(');'.PHP_EOL);
	$qfile->write('?>');
	$qfile->close();

	// �ӽ������� �������Ϸ� ����
	$copyResult = @copy('../conf/daumCpc.cfg.tmp', '../conf/daumCpc.cfg.php');

	// ���翡 ���������� �ӽ����� �����ϰ� ���Ѽ���
	if ($copyResult === true) {
		@unlink('../conf/daumCpc.cfg.tmp');
		@chmod('../conf/daumCpc.cfg.php', 0707);
	}
}

$partner = new Partner();
global $db;
$godo = $partner->getGodoCfg();
$query = $partner->getGoodsSql();
$res = $db->query($query);

$ep = array();
$delEp = array();
$tocnt = 0;	// ��ǰ�� �� ����

// ��ǰ��EP ù ������ ��� ��ǰ�� ���� �ƴϸ� ������Ʈ �� �� ����
if ($daumCpc['try'] === 'Y') {
	$where = 'sno=parent and regdt>curdate()-1 and';
}
else {
	$where = 'sno=parent and';
}

while ($data = $db->fetch($res,1)) {

	$query = "select goodsno,sno,subject,contents,point,date_format(regdt,'%Y%m%d%H%i%s') regdt,name from gd_goods_review where ".$where." goodsno='".$data['goodsno']."'";
	$review = $db->query($query);
	$tocnt += mysql_num_rows($review);

	while ($reviewData = $db->fetch($review,1)) {
		$ep[] = $reviewData;
	}
}

// ù �����ÿ��� ������ ��ǰ���� ������ �ʿ䰡 ����
if ($daumCpc['try'] === 'Y') {
	daum_goods_review_check();

	$query = "select sno,goodsno,subject,contents,point,date_format(regdt,'%Y%m%d%H%i%s') regdt,name from ".GD_GOODS_UPDATE_REVIEW_DAUM;
	$deleteReview = $db->query($query);
	$tocnt += mysql_num_rows($deleteReview);

	while ($deleteData = $db->fetch($deleteReview,1)) {
		$deleteData['status'] = 'D';
		$ep[] = $deleteData;
	}
}

header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: text/plain; charset=euc-kr");
if ($tocnt > 0) {
	echo('<<<tocnt>>>'.$tocnt.chr(10));
	for ($i=0; $i<count($ep); $i++) {
		echo('<<<begin>>>'.chr(10));
		echo('<<<mapid>>>'.$ep[$i]['goodsno'].chr(10));
		echo('<<<reviewid>>>'.$godo['sno'].$ep[$i]['goodsno'].$ep[$i]['sno'].chr(10));
		if ($ep[$i]['status'] === 'D') { echo('<<<status>>>D'.chr(10)); } else { echo('<<<status>>>S'.chr(10)); }
		echo('<<<title>>>'.strip_tags($ep[$i]['subject']).chr(10));
		echo('<<<contents>>>'.strip_tags($ep[$i]['contents']).chr(10));
		if (mb_strlen($ep[$i]['name'],'euc-kr') === 2) {
			echo('<<<writer>>>'.mb_substr($ep[$i]['name'],0,mb_strlen($ep[$i]['name'],'euc-kr')-1,'euc-kr').'*'.chr(10));
		}
		else if (mb_strlen($ep[$i]['name'],'euc-kr') === 1) {
			echo('<<<writer>>>'.$ep[$i]['name'].chr(10));
		}
		else {
			echo('<<<writer>>>'.mb_substr($ep[$i]['name'],0,mb_strlen($ep[$i]['name'],'euc-kr')-2,'euc-kr').'**'.chr(10));
		}
		echo('<<<cdate>>>'.$ep[$i]['regdt'].chr(10));
		echo('<<<rating>>>'.$ep[$i]['point'].'/5'.chr(10));
		echo('<<<ftend>>>'.chr(10));
	}
}
?>