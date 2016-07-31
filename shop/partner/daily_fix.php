<?
if($_GET[yyyymmdd]) include "../lib/library.php";
else include "../_header.php";
include "../conf/merchant.php";
	$regdt = $_POST[regdt];
	$yyyymmdd = $_GET[yyyymmdd];
	if($yyyymmdd){
		 $where = " and date_format( HHMMISS, '%Y%m%d')  = '$yyyymmdd'";
		 $datefield = "date_format(HHMMISS,'%H%i%s') HHMMISS";
	}else{
		$datefield = "HHMMISS";
		echo("
		<html>
		<head>
		<title></title>
		<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
		<link rel='styleSheet' href='/shop/admin/style.css'>
		");
		echo ("<table width=100% cellborder=0 cellspacing=0>");
		echo ("
	<tr bgcolor=#313131 style='color:#ffffff' height=25>
	<th>일시</th>
	<th>머천트코드</th>
	<th>아이디/이름</th>
	<th>주문번호</th>
	<th>상품일련번호</th>
	<th>수량</th>
	<th>금액</th>
	<th>상품명</th>	
</tr>");
	}
	if($regdt[0]){
		$where .= " and date_format( HHMMISS, '%Y%m%d')  >= '$regdt[0]'";
	}
	
	if($regdt[1]){
		$where .= " and date_format( HHMMISS, '%Y%m%d')  <= '$regdt[1]'";
	}
	if($where)$where = "where ".substr($where,4);
	$query = "select {$datefield}, LPINFO, ID,NAME,ORDER_CODE,PRODUCT_CODE,COUNT,PRICE,PRODUCT_NAME  from ".LINKPRICE_ORDER." ".$where." order by sno desc";

	$result = $db->query($query);
	$Total=$db->count_($result);

	while($Total > 0)
	{
		$row = $db->fetch($result);

		$line  = $row[HHMMISS] . "\t";
		$line .= $row[LPINFO] . "\t";
		$line .= $row[ID] . "(" . rawurlencode($row[NAME]) . ")" . "\t";
		$line .= $row[ORDER_CODE] . "\t";
		$line .= $row[PRODUCT_CODE] . "\t";
		$line .= $row[COUNT] . "\t";
		$line .= $row[PRICE] . "\t";
		$line .= $row[CATEGORY_CODE]. "\t" . "\t";
		$line .= rawurlencode($row[PRODUCT_NAME])."\t";
		
		if($yyyymmdd){		
			if($Total != 1)
			{		
				$line .= $row[REMOTE_ADDR]."\n";
				echo "$line";
			}
			## 만약 데이터의 마지막 값이면 줄 바꿈(\n)을 하지 않는다.
			else
			{
				$line .= $row[REMOTE_ADDR];
				echo "$line";
			}
		}else{			
			echo "<tr><td>".$row[HHMMISS]."</td><td>".$row[LPINFO]."</td><td>".$row[ID]." (".$row[NAME].")</td><td>".$row[ORDER_CODE]."</td><td>".$row[PRODUCT_CODE]."</td><td>".$row[COUNT]."</td><td>".$row[PRICE] * $row[COUNT]."</td><td>".$row[PRODUCT_NAME]."</td></tr>";			
		}
		
		$Total= $Total - 1;
		
	}
	if(!$yyyymmdd)	echo ("</table>");
?>
