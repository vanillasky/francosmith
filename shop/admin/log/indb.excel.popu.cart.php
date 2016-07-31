<?
include "../lib.php";

function __utf8($str) {
	return iconv('EUC-KR','UTF-8',$str);
}

### 공백 제거
$_POST[sword] = trim($_POST[sword]);

$year = ($_POST[year]) ? $_POST[year] : date("Y");
$month = ($_POST[month]) ? sprintf("%02d",$_POST[month]) : date("m");

$stype = ($_POST[stype]) ? $_POST[stype] : 'm';
$sdate_s = ($_POST[regdt][0]) ? $_POST[regdt][0] : date('Ymd',strtotime('-7 day'));
$sdate_e = ($_POST[regdt][1]) ? $_POST[regdt][1] : date('Ymd');

$srunout = ($_POST[srunout]) ? $_POST[srunout] : '';
$sbuy = ($_POST[sbuy]) ? $_POST[sbuy] : '';

$date = $year."-".sprintf("%02d",$month);
$last = date("t",strtotime($date."-01"));

if ($srunout == '1') $where[] = "(G.runout = 1 OR (G.usestock = 'o' AND G.usestock IS NOT NULL AND G.totstock < 1))";
elseif ($srunout == '-1') $where[] = "G.runout <> 1 AND (G.usestock <> 'o' OR G.usestock IS NULL OR G.totstock > 0)";

if ($sbuy != '') {
	$where[] = "CT.is_buy = '".($sbuy == '1' ? '1' : '0')."'";
}

if ($_POST[sword]) $where[] = "$_POST[skey] like '%$_POST[sword]%'";

if ($stype == 'm') {
	$where[] = " DATE_FORMAT(CT.regdt, '%Y-%m') = '$date' ";
}
else if ($sdate_s & $sdate_e){
	$where[] = " ( DATE_FORMAT(CT.regdt,'%Y%m%d') >= '".($sdate_s)."' and DATE_FORMAT(CT.regdt,'%Y%m%d') <= '".($sdate_e)."')";
}

$query = "
	SELECT

		G.goodsno, G.goodsnm, G.img_s, G.totstock, G.regdt, G.icon, G.usestock, G.runout,
		O.price,
		COUNT(CT.uid) AS `cart_cnt`, COUNT( IF(CT.m_id != '',1,null) ) AS `cart_mb`

	FROM ".GD_CART." AS CT
	INNER JOIN ".GD_GOODS." AS G
	ON CT.goodsno = G.goodsno
	INNER JOIN ".GD_GOODS_OPTION." AS O
	ON G.goodsno = O.goodsno AND O.link = 1 and go_is_deleted <> '1'
";
$query .= ' WHERE '.implode(' AND ',$where);
$query .= " GROUP BY G.goodsno ";
$query .= " ORDER BY `cart_cnt` DESC ";

$rs = $db->query($query);

//
$file_name = "장바구니분석.xls";

header( "Content-type: application/vnd.ms-excel" );
header( "Content-Disposition: attachment; filename=$file_name" );
header( 'Expires: 0' );
header( 'Cache-Control: must-revalidate, post-check=0,pre-check=0' );
header( 'Pragma: public' );

echo '<?xml version="1.0"?>';
echo '<?mso-application progid="Excel.Sheet"?>';
?>
<Workbook
	xmlns="urn:schemas-microsoft-com:office:spreadsheet"
	xmlns:o="urn:schemas-microsoft-com:office:office"
	xmlns:x="urn:schemas-microsoft-com:office:excel"
	xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
	xmlns:html="http://www.w3.org/TR/REC-html40">
	<DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
		<Author>enamoo</Author>
		<LastAuthor>enamoo</LastAuthor>
		<Created>2011-11-30T23:04:04Z</Created>
		<Company>godomall</Company>
		<Version>11.8036</Version>
	</DocumentProperties>
	<ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
		<WindowHeight>6795</WindowHeight>
		<WindowWidth>8460</WindowWidth>
		<WindowTopX>120</WindowTopX>
		<WindowTopY>15</WindowTopY>
		<ProtectStructure>False</ProtectStructure>
		<ProtectWindows>False</ProtectWindows>
	</ExcelWorkbook>
	<Styles>
		<Style ss:ID="Default" ss:Name="Normal">
		<Alignment ss:Vertical="Bottom" />
		<Borders />
		<Font />
		<Interior />
		<NumberFormat />
		<Protection />
		</Style>
		<Style ss:ID="sBody">
		<Font x:Family="Modern" x:CharSet="129" ss:FontName="Gulim" ss:Bold="0" />
		</Style>
		<Style ss:ID="sHead">
		<Alignment ss:Horizontal="Center" />
		<Font x:Family="Modern" x:CharSet="129" ss:FontName="Gulim" ss:Bold="1"  />
		</Style>
		<Style ss:ID="sNum">
		<Font x:Family="Modern" x:CharSet="129" ss:FontName="Gulim" ss:Bold="0"  />
		<NumberFormat ss:Format="#,##0_ "/>
		</Style>
	</Styles>
	<Worksheet ss:Name="Sheet1">
		<Table ss:ExpandedColumnCount="8" ss:ExpandedRowCount="65535" x:FullColumns="1" x:FullRows="1">
			<Row>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('순위')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('고객수')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('회원')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('비회원')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('상품명')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('등록일')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('가격')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('재고')?></Data></Cell>
			</Row>
		<? $rank = 1; ?>
		<? while($row = $db->fetch($rs,1)) { ?>
			<Row>
				<Cell ss:StyleID="sBody"><Data ss:Type="String"><?=$rank++?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['cart_cnt'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['cart_mb'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['cart_cnt'] - $row['cart_mb'])?></Data></Cell>
				<Cell ss:StyleID="sBody"><Data ss:Type="String"><?=__utf8($row['goodsnm'])?></Data></Cell>
				<Cell ss:StyleID="sBody"><Data ss:Type="String"><?=substr($row['regdt'],0,10)?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['price'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['totstock'])?></Data></Cell>
			</Row>
		<? } ?>
		</Table>
	</Worksheet>
</Workbook>
