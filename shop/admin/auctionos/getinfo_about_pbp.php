<?
include "../../dbconn.php";

error_reporting(0);

$db->silent(true);	// DB 오류 메시지 출력 안함

if ($_GET[type]!='subcall') {
	header ("Content-type: text/xml");
	echo "<?xml version=\"1.0\" encoding=\"euc-kr\"?>";
	echo "<aboutinfo>";

	echo "<shopname>".$cfg[shopName]."</shopname>";
	echo "<shopurl>".$cfg[shopUrl]."</shopurl>";
}

if (!$_GET[mode]) $_GET[mode] = 'last';

if ($_GET[mode] == 'last') {
	$query = " SELECT about.log_aboutcoupon_seq , about.use_aboutcoupon, about.use_test, about.m_id, about.regdt";
	$query.= " FROM gd_log_aboutcoupon about";
	$query.= " JOIN ( SELECT max( log_aboutcoupon_seq ) AS maxseq FROM gd_log_aboutcoupon )X ON about.log_aboutcoupon_seq = X.maxseq ";
	$query.= " LIMIT 1";

	$res_query = $db->query($query);

	if ($res_query) {
		while ($row = $db->fetch($res_query,1))
		{
			$list_row[] = $row;
		}

		if (count($list_row) > 0) {
			if ($_GET[type]!='subcall') {
				foreach($list_row as $v) {
					echo "<last>";
					echo "<user>".$v[m_id]."</user>";
					echo "<use>".$v[use_aboutcoupon]."</use>";
					echo "<test>".$v[use_test]."</test>";
					echo "<regdt>".$v[regdt]."</regdt>";
					echo "</last>";
					echo "<error/>";
				}
			} else {
				foreach($list_row as $v) {
					echo "".$v[m_id]."§".$v[use_aboutcoupon]."§".$v[use_test]."§".substr($v[regdt],0,10)."";
					$db->closeAll();
					exit;
				}
			}
		}
	} else {
	    echo "<error>Could not successfully run query ($query) from DB: " . $db->errorInfo()."</error>";
	    echo "</aboutinfo>";
		$db->closeAll();
	    exit;
	}
}
else {
	## list는 최대 30개까지만 보여준다.
	$query = " SELECT about.log_aboutcoupon_seq , about.use_aboutcoupon, about.use_test, about.m_id, about.regdt";
	$query.= " FROM gd_log_aboutcoupon about";
	$query.= " ORDER BY about.log_aboutcoupon_seq DESC";
	$query.= " LIMIT 30";

	$res_query = $db->query($query);

	if ($res_query) {

		while ($row = $db->fetch($res_query,1))
		{
			echo "<list>";
			echo "<id>".$row[log_aboutcoupon_seq]."</id>";
			echo "<user>".$row[m_id]."</user>";
			echo "<use>".$row[use_aboutcoupon]."</use>";
			echo "<test>".$row[use_test]."</test>";
			echo "<regdt>".$row[regdt]."</regdt>";
			echo "</list>";
		}

	} else {
	    echo "<error>Could not successfully run query ($query) from DB: " . $db->errorInfo()."</error>";
	    echo "</aboutinfo>";
	    $db->closeAll();
	    exit;
	}
}
$db->closeAll();

if ($_GET[type]!='subcall') {
	echo "</aboutinfo>";
}
?>
