<?
include "../lib.php";

$maxMessage = 1000;

function __parse_msg ($stocked_noti_cfg) {

	$msg = $stocked_noti_cfg['msg'];
	$data = array();

	if (($num_args = func_num_args()) > 1) {
		$args = func_get_args();

		for ($i=1;$i<$num_args;$i++) {
			$data = array_merge($data,$args[$i]);
		}
	}

	if (preg_match_all('/\{([a-zA-Z_]{1}[a-zA-Z0-9_]+)\}/',$msg,$replaces)) {

		$from	= $replaces[0];
		$to		= $replaces[1];

		for ($i=0,$max=sizeof($from);$i<$max;$i++) {
			if (isset($data[ $to[$i] ])) {

				$tmp = str_replace($from[$i],$data[ $to[$i] ],$msg);

				$msg = $tmp;
			}
		}

	}
	return $msg;
}

function __cutStrKor($str,$maxlen) {
	if($maxlen<=0) return $str;
	if($maxlen >= strlen($str)) return $str;
	$klen = $maxlen - 1;
	while(ord($str[$klen]) & 0x80) $klen--;

	return substr($str, 0, $maxlen - (($maxlen + $klen + 1) % 2));
}

function __cutSMS($msg, $goodsopt, $goodsnm, $minCut, $charset, $stocked_noti_cfg, $cfg, $row){
	$msgLen = strlen($msg);
	$goodsnmMin = $goodsoptMin = false;

	while($msgLen > 90){
		$i++;
		if(mb_strlen($goodsopt) > $minCut){
			$goodsopt = mb_substr($goodsopt, 0, mb_strlen($goodsopt, $charset) - 1, $charset);
		}else{
			$goodsoptMin = true;
		}
		$row['goodsopt'] = $goodsopt;
		$msg = __parse_msg($stocked_noti_cfg,$cfg,$row);
		if(strlen($msg) > 90){
			if(mb_strlen($goodsnm) > $minCut){
				$goodsnm = mb_substr($goodsnm, 0, mb_strlen($goodsnm, $charset) - 1, $charset);
			}else{
				$goodsnmMin = true;
			}
		}
		if($goodsnmMin && $goodsoptMin){
			$msg = __cutStrKor($msg, 90);
			break;
		}

		$row['goodsnm'] = $goodsnm;
		$msg = __parse_msg($stocked_noti_cfg,$cfg,$row);
		$msgLen = strlen($msg);
	}
	return $msg;
}

function seprateMsg($msg){
	while(strlen($msg) > 0){
		$smsMsg[] = __cutStrKor($msg, 90);
		$msg= str_replace($smsMsg[count($smsMsg)-1], "", $msg);
	}
	return $smsMsg;
}

// 변수 받기
$stocked_noti_cfg['msg'] = isset($_POST['msg']) ? $_POST['msg'] : '';
$goodsno = isset($_POST['goodsno']) ? $_POST['goodsno'] : '';
$optno = isset($_POST['optno']) ? $_POST['optno'] : '';
$opt1 = isset($_POST['opt1']) ? $_POST['opt1'] : '';
$opt2 = isset($_POST['opt2']) ? $_POST['opt2'] : '';
$msgOpt = isset($_POST['msgOpt']) ? $_POST['msgOpt'] : 'fix';
$shortGoodsNm = isset($_POST['shortGoodsNm']) ? $_POST['shortGoodsNm'] : 'fix';
$method = isset($_POST['method']) ? $_POST['method'] : '';
$chks = isset($_POST['chk']) ? $_POST['chk'] : array();
$startMessage = isset($_POST['startMessage']) ? $_POST['startMessage'] : 0;
$totalSMS = isset($_POST['totalSMS']) ? $_POST['totalSMS'] : -1;
$sentSMS = isset($_POST['sentSMS']) ? $_POST['sentSMS'] : 0;
$members = isset($_POST['members']) ? $_POST['members'] : 0;

$sms = Core::loader('sms');
$sms_sendlist = $sms->loadSendlist();
$formatter = Core::loader('stringFormatter');

$query = "
SELECT
	NT.*,
	G.goodsnm
FROM ".GD_GOODS_STOCKED_NOTI." AS NT
INNER JOIN  ".GD_GOODS." AS G
ON NT.goodsno = G.goodsno
WHERE NT.goodsno = $goodsno AND NT.sended = 0 AND NT.optno='".$optno."' AND NT.opt1='".$opt1."' AND NT.opt2='".$opt2."'
";

if ($method != 'all') $query .= " AND NT.sno IN (".implode(',',$chks).')';
$tmpRes = $db->fetch($query);
$totalSMS = $db->affected($tmpRes);

$query .= ' LIMIT '.$maxMessage.' ';
$rs = $db->query($query);

if ($sms->smsPt < mysql_num_rows($rs) && $force != 1) {
	msg('SMS 잔여 포인트가 부족합니다.');
	exit;
}

if($sentSMS > $maxMessage){
?>
	<script type="text/javascript">
		parent.document.getElementById("smsSendPage").style.display="none";
		parent.document.getElementById("smsSendingPage").style.display="block";
		parent.document.getElementById("msgIng").innerText="SMS <?=$sentSMS?>건 전송 완료";
	</script>
<?
}

$total = 0;
while ($row = $db->fetch($rs,1)) {
	if(!empty($row['opt2'])){
		$row['goodsopt'] = $row['opt1']."/".$row['opt2'];
	}else if(!empty($row['opt1'])){
		$row['goodsopt'] = $row['opt1'];
	}else{
		$row['goodsopt'] = "";
	}
	
	if ($row['phone'] && ($sms->smsPt > 0)) {

		$msg = __parse_msg($stocked_noti_cfg,$cfg,$row);
		if($msgOpt == "fix"){
			$i = 0;
			##단문(90바이트)고정 - 90바이트 넘을 경우 상품명 자르기
			if($shortGoodsNm == "y"){
				##상품정보 짧게 표시 - 90바이트 넘을 경우 상품명 1바이트까지 자르기
				$smsMsg[] = __cutSMS($msg, $row['goodsopt'], $row['goodsnm'], 1, "euc-kr", $stocked_noti_cfg, $cfg, $row);
			}else{
				##90바이트 넘을 경우 상품명 10바이트까지 자르기
				$smsMsg[] = __cutSMS($msg, $row['goodsopt'], $row['goodsnm'], 10, "euc-kr", $stocked_noti_cfg, $cfg, $row);
			}
		}else if($msgOpt == "separate"){
			##장문(90바이트 이상)분할전송 - 90바이트 넘을 경우 문자 분할
			$smsMsg = seprateMsg($msg);
		}
		$members ++;
		foreach($smsMsg as $msgContent){
			$type = 20;
			$sms->log($msgContent,$row['name']."(".$row['phone'].")",$type,"1");
			$sms_sendlist->setSimpleInsert($row['phone'], $sms->smsLogInsertId, '');
			if ($sms->send($msgContent,$row['phone'],$cfg['smsRecall'])) {
				$db->query("UPDATE ".GD_GOODS_STOCKED_NOTI." SET sended = 1, sendeddt=NOW() WHERE sno = $row[sno]");
				$sentSMS++;

				$sms->update();
			}
		}
		unset($smsMsg);
	}
}
// 로그
if($totalSMS == 0 || $method != 'all'){
	msg($members."명에게 ".number_format($sentSMS).' 건의 상품 재입고 알림 메시지가 발송되었습니다.');
	?>
	<script>
		parent.location.reload();
	</script>
	<?
}else{
	?>
	<form method="post" name="smsSend" action="<?=$PHP_SELF?>">
	<?
	$_POST['startMessage'] = $startMessage + $maxMessage;
	$_POST['sentSMS'] = $sentSMS;
	$_POST['members'] = $members;
	$_POST['totalSMS'] = $totalSMS;
	foreach($_POST as $k => $v){
		?><input type="hidden" name="<?=$k?>" value="<?=$v?>" />
		<?
	}
	?>
	</form>
	<script>
		document.smsSend.submit();
	</script>
	<?
}
?>
