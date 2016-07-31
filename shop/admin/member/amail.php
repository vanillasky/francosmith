<?
/*
amail 로그인 ,발송리스트 생성
*/
include "../_header.popup.php";
include "../../lib/amail.class.php";

function chktel($tel){
	$rfilter = array(',','~');
	foreach($rfilter as $v){
		unset($arr);
		$arr = explode($v,$tel);
		$ret = $arr[0];
	}
	return $ret;
}

$amail = new aMail;
$amail -> setAmail();
if($_POST['type'] == 'query' || $_POST['type'] == 'ts_query') $data = $_POST['query'];
else $data = $_POST['chk'];

$url = 'http://partners.postman.co.kr:90/home/login_partner.jsp';
$r_url = explode(":",$_SERVER["HTTP_HOST"]);
$tmp = str_replace(array('http://','www.'),'',$r_url[0]).$cfg[rootDir];

if(!file_exists('../../conf/amail.set.php')){
	$cell = explode('|',$cfg['smsAddAdmin']);
	$cfg[compPhone] = chktel($cfg[compPhone]);
	$cell[0] = chktel($cell[0]);
	if(!$cell[0]) $cell[0] = $cfg['compPhone'];
	$set['user_name'] = $cfg['ceoName'];
	$set['user_email'] = $cfg['adminEmail'];
	$set['user_tel'] = $cfg['compPhone'];
	$set['user_cell'] = $cell[0];
	$set['user_id'] = $godo['sno'];
}else{
	include "../../conf/amail.set.php";
	$set = $set[amail];
}

### 파라미터 인증
$error = false;
if(!$set['user_id']){
	msg('올바르지 못한 쇼핑몰 입니다.!');
	$error = true;
}
if(!$set['user_name']){
	msg('쇼핑몰기본관리의 대표자명을 입력하여 주십시요!');
	$error = true;
}
if(!$set['user_email']){
	msg('쇼핑몰기본관리의 관리자 Email을 입력하여 주십시요!');
	$error = true;
}
if($error){
	if($_GET['charge'] == 'y') echo("<script>self.close</script>");
	exit;
}

$r_param = array(
	'user_id' => $set['user_id'],
	'user_no' => '*************',
	'user_nm' => $set['user_name'],
	'user_email' => $set['user_email'],
	'user_tel' => $set['user_tel'],
	'user_cell' => $set['user_cell'],
	'user_domain' => $tmp,
	'cooperation_id' => 'GM',
	'charge_yn' => strtoupper($_GET['charge'])
);

if($data){
	$amail -> makeList($_POST['type'],$data, $_POST['receiveRefuseType']);

	?>
	<form name=frmAmail method="post"  action="<?=$url?>">
	<?foreach($r_param as $k=>$v){?>
	<input type=hidden name='<?=$k?>' value='<?=$v?>'>
	<?}?>
	</form>
	<script>document.frmAmail.submit();</script>
<?
}
if($_GET['charge'] == 'y'){
?>
	<form name=frmAmail method="post"  action="<?=$url?>">
	<?foreach($r_param as $k=>$v){?>
	<input type=hidden name='<?=$k?>' value='<?=$v?>'>
	<?}?>
	</form>
	<script>document.frmAmail.submit();</script>
<?
}
?>