<?
// ���۽ð�
$arrSdt = explode('-', $_POST['startDt']);
$arrStm = explode(':', $_POST['startTm']);

// ����ð�
$arrCdt = explode('-', $_POST['closeDt']);
$arrCtm = explode(':', $_POST['closeTm']);

$startTm = mktime($arrStm[0], $arrStm[1], $arrStm[2], $arrSdt[1], $arrSdt[2], $arrSdt[0]);
$closeTm = mktime($arrCtm[0], $arrCtm[1], $arrCtm[2], $arrCdt[1], $arrCdt[2], $arrCdt[0]);
$curTm = time();
unset($arrSdt, $arrStm, $arrCdt, $arrCtm);

$status = '';
$remainTm = '';
if ($startTm > 0 && $curTm < $startTm) $status = 'before'; // ���۽ð��� �ְ�, ���� ���̸� status = 'before'
else if ($closeTm > 0) { // ����ð��� ������
	$remainTm = $closeTm - $curTm;
	if ($remainTm <= 0) $status = 'closed'; // ���� ���̸� status = 'closed'
}
else $status = 'noperiod'; // ����ð��� �������������� status = 'noperiod'

$status = ($status)? $status : 'ing'; // ���� �Ǹ����̸� status = 'ing'
header("Content-Type: application/json; charset=utf-8");
?>
{status:"<?=$status?>", remainTm:"<?=$remainTm?>"}