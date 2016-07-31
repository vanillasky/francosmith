<?
header( "content-type:text/xml");
include "../lib/library.php";
$file	= dirname(__FILE__)."/../conf/godomall.cfg.php";
$file	= file($file);
$godo	= decode($file[1],1);

$search_nm = $_POST['search_nm'];
$sql = "SELECT * FROM gd_category WHERE catnm LIKE '%".$search_nm."%'";
$rs = $db->query($sql);
$cnt = $db->count_($rs);
/* XML */

/* top */
$xml .= "<?xml version=\"1.0\" encoding=\"euc-kr\"?>\n";
$xml .= "<data>\n";
/* /top */


/* body */
$xml .= "	<cnt>".$cnt."</cnt>\n";
$xml .= "	<category>\n";

for($i = 0; $row = $db->fetch($rs); $i++) {
	$tmp_cd = str_split($row['category'], 3);
	if(count($tmp_cd) > 1) {
		for($j = 0; $j < count($tmp_cd); $j++) {
			$ch_cd .= $tmp_cd[$j];
			$ch_sql = "SELECT catnm, category FROM gd_category WHERE category='".$ch_cd."'";
			$ch_rs = $db->_select($ch_sql);
			$arr_cd[] = $ch_rs[0]['category'];
			$arr_nm[] = $ch_rs[0]['catnm'];
		}

		$xml .= "		<item>\n";
		$xml .= "			<shop_cd><![CDATA[".$godo['sno']."]]></shop_cd>\n";
		$xml .= "			<category_nm><![CDATA[".implode('>', $arr_nm)."]]></category_nm>\n";
		$xml .= "			<category_cd><![CDATA[".implode('>', $arr_cd)."]]></category_cd>\n";
		$xml .= "		</item>\n";
		unset($ch_cd, $ch_sql, $ch_rs, $arr_cd, $arr_nm);
	}
	else {
		$xml .= "		<item>\n";
		$xml .= "			<shop_cd><![CDATA[".$godo['sno']."]]></shop_cd>\n";
		$xml .= "			<category_nm><![CDATA[".$row['catnm']."]]></category_nm>\n";
		$xml .= "			<category_cd><![CDATA[".$row['category']."]]></category_cd>\n";
		$xml .= "		</item>\n";
	}
}
$xml .= "	</category>\n";
/* /body */


/* bottom */
$xml .= "</data>\n";
/* /bottom */

/* /XML */
echo $xml;
?>
