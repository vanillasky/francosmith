<?
include "../lib.php";

function __utf8($str) {
	return iconv('EUC-KR','UTF-8',$str);
}

$year = ($_POST[year]) ? $_POST[year] : date("Y");
$month = ($_POST[month]) ? sprintf("%02d",$_POST[month]) : date("m");

$stype = ($_POST[stype]) ? $_POST[stype] : 'm';
$sdate_s = ($_POST[regdt][0]) ? $_POST[regdt][0] : date('Ymd',strtotime('-7 day'));
$sdate_e = ($_POST[regdt][1]) ? $_POST[regdt][1] : date('Ymd');

$srunout = ($_POST[srunout]) ? $_POST[srunout] : '';

$_POST[page_num] = $_POST[page_num] ? $_POST[page_num] : 20;

$date = $year."-".sprintf("%02d",$month);
$last = date("t",strtotime($date."-01"));

if ($_POST[brandno]) $where[] = "brandno='$_POST[brandno]'";
if ($_POST[cate]){
	$category = array_notnull($_POST[cate]);
	$category = $category[count($category)-1];
}

$where[] = "o.istep < 40";
$where[] = "o.istep > 0";
if ($stype == 'm') {
	$where[] = " DATE_FORMAT(o2.cdt,'%Y-%m') = '$date' ";
}
else if ($sdate_s & $sdate_e){
	$where[] = " (DATE_FORMAT(o2.cdt, '%Y%m%d') >= '".$sdate_s."' and DATE_FORMAT(o2.cdt,'%Y%m%d') <= '".($sdate_e)."')";
}

if ($srunout == '1') $where[] = "(g.runout = 1 OR (g.usestock = 'o' AND g.usestock IS NOT NULL AND g.totstock < 1))";
elseif ($srunout == '-1') $where[] = "g.runout <> 1 AND (g.usestock <> 'o' OR g.usestock IS NULL OR g.totstock > 0)";

$query = "
SELECT

o.goodsnm,o.goodsno,count(o.sno) cnt,sum(o.ea) as ea, o.price, sum(o.price * ea) as sales,

g.img_s, g.runout, g.icon

from ".GD_ORDER_ITEM." as o FORCE INDEX (ix_goodsno)
left join ".GD_GOODS." as g
ON o.goodsno = g.goodsno
left join ".GD_ORDER." as o2 on o.ordno = o2.ordno
";

if ($category){
	$query .= " left join ".GD_GOODS_LINK." c on g.goodsno=c.goodsno ";

	// 상품분류 연결방식 전환 여부에 따른 처리
	$where[]	= getCategoryLinkQuery('c.category', $category, 'where');
}
$query .= ' where '.implode(' and ',$where);
$query .= " group by o.goodsno";
if (!$_POST[sort]) $query .= " order by goodsno ";
else {
	$query .= " order by ".$_POST[sort];
}

$rs = $db->query($query);

//
$file_name = "상품판매순위분석.xls";
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
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('상품')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('상품등록일')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('가격')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('재고량')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('구매자수')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('구매수량')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('매출액')?></Data></Cell>
			</Row>
		<? $rank = 1; ?>
		<? while($row = $db->fetch($rs,1)) { ?>
			<Row>
				<Cell ss:StyleID="sBody"><Data ss:Type="String"><?=$rank++?></Data></Cell>
				<Cell ss:StyleID="sBody"><Data ss:Type="String"><?=__utf8($row['goodsnm'])?></Data></Cell>
				<Cell ss:StyleID="sBody"><Data ss:Type="String"><?=$row['regdt']?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['price'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['totstock'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['cnt'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['ea'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['sales'])?></Data></Cell>
			</Row>
		<? } ?>
		</Table>
	</Worksheet>
</Workbook>
