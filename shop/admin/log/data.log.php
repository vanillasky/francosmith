<?

include "../_header.popup.php";
$db->silent(true);	// DB ���� �޽��� ��� ����
echo "<div style='padding-left:10px;'>������踦 ���� ���Դϴ�...<br>���ʿ��� ������ �ҿ� �� �� �ֽ��ϴ�.<br><b>�ڵ����� â�� ���������� ��â�� ���� �����ֽʽÿ�!!</b></div>";

$query = "select idx_date from ".MINI_IP." where idx_date < '".date('Ymd')."' group by idx_date order by idx_date";
$res1 = $db->query($query);
while($row = $db->fetch($res1)){

	$date = $row[0];

	$query = "select * from ".MINI_IP." where idx_date='$date'";
	$result = $db->query($query);

	$page_num = 50;
	$tot = $db->count_($result);

	$dir = "../../log/".MINI_COUNTER."";
	if (!is_dir($dir)) {
		@mkdir($dir, 0707);
		@chmod($dir, 0707);
	}

	$filename = $dir."/".$date.".log";

	$fp = fopen($filename,"w");
	$i=0;
	while ($data=$db->fetch($result)){
		$i++;
		 fwrite($fp, ceil($i / $page_num) ."\t". ($tot - $i + 1) ."\t".	$data[ip] . "\t" . 	$data[os] . "\t" . $data[browser] . "\t" .  date("H�� i�� s��",$data[reg_date]) . "\t" . $data[referer]."\n");
	}
	fclose($fp);

	$query = "select count(*)  from ".MINI_IP_OS." where idx_date='$date'";
	$result_os = $db->query($query);
	list($ocnt) = $db->fetch($result_os);

	if(file_exists($filename)){
		if($ocnt == 0){
			$query = "select os,count(*) as cnt from ".MINI_IP." where idx_date='$date' group by os order by cnt desc";
			$res = $db->query($query);

			while ($data=$db->fetch($res)){
				$query = "insert into ".MINI_IP_OS." (os,cnt,idx_date) values('$data[os]','$data[cnt]','$date')";
				$db->query($query);
			}

			$query = "select browser,count(*) as cnt from ".MINI_IP." where idx_date='$date' group by browser order by cnt desc";
			$res = $db->query($query);
			while ($data=$db->fetch($res)){
					$query = "insert into ".MINI_IP_BROWSER." (browser,cnt,idx_date) values('$data[browser]','$data[cnt]','$date')";
					$db->query($query);
			}
		}

		$query = "delete from ".MINI_IP." where  idx_date='$date'";
		$db->query($query);
	}

	flush();

}

popupReload();
?>
