<?
### Amail 발송리스트 리턴
include "../lib/library.php";
include "../lib/amail.class.php";

switch($_GET['mode']){
	case "grouplist" :
		echo "<tr><td>all</td><td>전체</td></tr>\n";
		break;
	case "groupchoice" :
		if(file_exists("../conf/amail.data.php")){
			$tmp = @file("../conf/amail.data.php");
			$cnt = count($tmp);
			echo $cnt;
		}
		break;
	case "groupftp" :
		$amail = new aMail;
		$amail -> setAmail();
		$res = $amail -> setList($_GET['user_id'],$_GET['post_id']);

	case "mailing" :
		$m_id = $_GET['id'];
		$emailDeny = Core::loader('LibEmailDeny');
		$denyLink = $emailDeny->getDenyLink($m_id);
		go($denyLink);
		exit;
		break;
}
?>