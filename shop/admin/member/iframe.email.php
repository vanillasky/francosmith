<?

set_time_limit(0);

include "../lib.php";
include "../../lib/mail.class.php";
include "../../conf/config.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

### �߼۰Ǽ� üũ
$tot = getMailCnt();
if($_POST[total] > 3000-$tot && $godo[webCode] != 'webhost_outside'){
	msg('�ܿ� ���� �߼�����Ʈ�� �����մϴ�.\n�Ŀ����ϼ��񽺸� �̿����ּ���',0);
	exit;
}

### �̸��� �߼� �α� ����
$query = "
insert into ".GD_LOG_EMAIL." set
	subject		= '$_POST[subject]',
	body		= '$_POST[body]',
	target		= '$_POST[total]',
	regdt		= now()
";
$db->query($query);

$mail = new Mail($params);

### ���ϴ��
if ($_POST['query'] != '') {
	$query = stripslashes($_POST['query']);
}
else if ($_POST['toEmail'] != '') {
	$query = "select * from ".GD_MEMBER." limit 1";
	$_POST[total] = 1;
}

?>
<style>
body {
margin:5px;
scrollbar-face-color:#FFFFFF;
scrollbar-shadow-color:#AFAFAF;
scrollbar-highlight-color:#AFAFAF;
scrollbar-3dlight-color:#FFFFFF;
scrollbar-darkshadow-color:#FFFFFF;
scrollbar-track-color:#F7F7F7;
scrollbar-arrow-color:#838383;
}
body,table {font:9pt tahoma;}
</style>
<?

echo "<script>parent.document.getElementById('progressBar').style.width = '0';</script>";

$res = $db->query($query);
while ($data=$db->fetch($res)){

	$idx++;

	$subject = str_replace(array('{m_id}','{name}'),array($data['m_id'],$data['name']),$_POST[subject]);
	$body = str_replace(array('{m_id}','{name}'),array($data['m_id'],$data['name']),$_POST[body]);

	// ���ŵ��ǹ���, ���Űź�
	if ($_POST['query'] != '') {
		$bodyReceiveMsg = '';
		if ($_POST['agreeFlag'] == 'Y' && $_POST['agreeMsg'] != '') {
			$bodyReceiveMsg = $bodyReceiveMsg . '<div>'.$_POST['agreeMsg'].'</div>';
		}
		if ($_POST['denyFlag'] == 'Y') {
			$emailDeny = Core::loader('LibEmailDeny');
			$denyLink = $emailDeny->getDenyLink($data['m_id']);
			$bodyReceiveMsg = $bodyReceiveMsg . '
			<div>
				- �̸����� ������ �� �̻� ������ �����ø� <a href="'.$denyLink.'" target="_blank">[���Űź�]</a>�� Ŭ���� �ּ���.<br/>
				- If you don��t want to receive this mail, <a href="'.$denyLink.'" target="_blank">click here</a>.
			</div>';
		}
		if ($bodyReceiveMsg != '') {
			$body = $body . '<div style="padding:5px; background-color:rgb(250, 250, 250); border:solid 1px #cccccc; color:#626b72; color:#626b72; font:8pt Dotum;">'.$bodyReceiveMsg.'</div>';
		}
	}

	$headers['From']    = $cfg[adminEmail];
	$headers['Name']	= $cfg[shopName];
	$headers['Subject'] = $subject;
	$headers['To'] = (!$_POST[toEmail]) ? $data[email] : $_POST[toEmail];

	$resSend = $mail->send($headers, $body);
	if($resSend){
		$resStr = "����";
		$sucess++;
	}else{
		$resStr = "<font color=red>����</font>";
	}

	if (!$_POST[toEmail]) $addStr = "$data[name] $data[m_id]";
	echo "$idx. $addStr {$headers['To']} $resStr<br><script>scroll(0,999999999)</script>";

	$tmp = floor($idx / $_POST[total] * 100);
	if ($perc!=$tmp){
		$perc = $tmp;
		echo "<script>parent.document.getElementById('progressBar').style.width = '{$perc}%';</script>";
	}

	flush();

}


$tot += $sucess;
$arr = array(date('Ym',time()) => $tot);

$tmp =  urlencode(serialize($arr));
$qfile->open("../../conf/mail.cnt.php");
$qfile->write($tmp);
$qfile->close();
@chmod('../../conf/mail.cnt.php',0707);
?>