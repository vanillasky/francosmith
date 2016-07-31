<?
header( "content-type:text/xml");
	include "../lib/library.php";
	$file	= dirname(__FILE__)."/../conf/godomall.cfg.php";
	$file	= file($file);
	$godo	= decode($file[1],1);

	$sql = "SELECT * FROM gd_category WHERE Char_Length(category) = 3";
	$rs = $db->query($sql);


/* XML */

	/* top */
	$xml .= "<?xml version=\"1.0\" encoding=\"euc-kr\"?>\n";
	$xml .= "<data>\n";
	/* /top */


	/* body */
	for($i = 1; $i <= 4; $i++) {
		$sql = "SELECT * FROM gd_category WHERE Char_Length(category) = ".($i * 3)." ORDER BY sort ASC";
		$rs = $db->query($sql);

		if(!$db->count_($rs)) continue;

		$xml .= "	<category".$i.">\n";

		for($j = 0; $row = $db->fetch($rs); $j++) {
			$xml .= "		<item>\n";
			$xml .= "			<shop_cd><![CDATA[".$godo['sno']."]]></shop_cd>\n";
			for($k = 1; $k <= $i; $k++) $xml .= "			<category_cd".$k."><![CDATA[".substr($row['category'], 0, ($k * 3))."]]></category_cd".$k.">\n";
			$xml .= "			<category_nm><![CDATA[".$row['catnm']."]]></category_nm>\n";
			$xml .= "			<category_cd><![CDATA[".$row['category']."]]></category_cd>\n";
			$xml .= "			<sort>".$row['sort']."</sort>\n";
			$xml .= "		</item>\n";
		}
		$xml .= "	</category".$i.">\n";
	}
	/* /body */


	/* bottom */
	$xml .= "</data>\n";
	/* /bottom */

/* /XML */

//	echo iconv("euc-kr", "utf-8", $xml);
	echo $xml;
?>
