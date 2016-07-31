<?
// 시작시간
$arrSdt = explode('-', $_POST['startDt']);
$arrStm = explode(':', $_POST['startTm']);

// 종료시간
$arrCdt = explode('-', $_POST['closeDt']);
$arrCtm = explode(':', $_POST['closeTm']);

$startTm = mktime($arrStm[0], $arrStm[1], $arrStm[2], $arrSdt[1], $arrSdt[2], $arrSdt[0]);
$closeTm = mktime($arrCtm[0], $arrCtm[1], $arrCtm[2], $arrCdt[1], $arrCdt[2], $arrCdt[0]);
$curTm = time();
unset($arrSdt, $arrStm, $arrCdt, $arrCtm);

$status = '';
$remainTm = '';
if ($startTm > 0 && $curTm < $startTm) $status = 'before'; // 시작시간이 있고, 시작 전이면 status = 'before'
else if ($closeTm > 0) { // 종료시간이 있으면
	$remainTm = $closeTm - $curTm;
	if ($remainTm <= 0) $status = 'closed'; // 종료 후이면 status = 'closed'
}
else $status = 'noperiod'; // 진행시간이 안정해져있으면 status = 'noperiod'

$status = ($status)? $status : 'ing'; // 현재 판매중이면 status = 'ing'
header("Content-Type: application/json; charset=utf-8");
?>
{status:"<?=$status?>", remainTm:"<?=$remainTm?>"}