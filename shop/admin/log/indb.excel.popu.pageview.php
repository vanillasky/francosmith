<?
include "../lib.php";

function __utf8($str) {
	return iconv('EUC-KR','UTF-8',$str);
}

$year = ($_POST[year]) ? $_POST[year] : date("Y");
$month = ($_POST[month]) ? sprintf("%02d",$_POST[month]) : date("m");

$stype = ($_POST[stype]) ? $_POST[stype] : 'm';
$sdate_s = ($_POST[regdt][0]) ? $_POST[regdt][0] : '';
$sdate_e = ($_POST[regdt][1]) ? $_POST[regdt][1] : '';

$srunout = ($_POST[srunout]) ? $_POST[srunout] : '';

$date = $year."-".sprintf("%02d",$month);
$last = date("t",strtotime($date."-01"));

if ($_POST[brandno]) $where[] = "brandno='$_POST[brandno]'";
if ($_POST[cate]){
	$category = array_notnull($_POST[cate]);
	$category = $category[count($category)-1];
}

$where[] = '1=1';

if ($stype == 'm') {
	$where[] = " PGV.date like '$date%' ";
}
else if ($sdate_s & $sdate_e){
	$where[] = " PGV.date >= '".(date('Y-m-d', strtotime($sdate_s)))."' AND PGV.date <= '".(date('Y-m-d',strtotime($sdate_e)))."'";
}

if ($srunout == '1') $where[] = "(G.runout = 1 OR (G.usestock = 'o' AND G.usestock IS NOT NULL AND G.totstock < 1))";
elseif ($srunout == '-1') $where[] = "G.runout <> 1 AND (G.usestock <> 'o' OR G.usestock IS NULL OR G.totstock > 0)";

$query = "
	SELECT

		PGV.goodsno, PGV.date,  SUM(PGV.cnt) AS `cnt`,
		G.goodsno, G.goodsnm, G.regdt, G.img_s, G.totstock, G.runout, G.usestock, G.icon,
		GO.price

	FROM gd_goods_pageview AS PGV
	INNER JOIN gd_goods AS G
	ON PGV.goodsno = G.goodsno
	INNER JOIN gd_goods_option AS GO
	ON G.goodsno = GO.goodsno AND GO.link = 1 and go_is_deleted <> '1'
";

if ($category){
	$query .= " left join ".GD_GOODS_LINK." c on G.goodsno=c.goodsno ";

	// 상품분류 연결방식 전환 여부에 따른 처리
	$where[]	= getCategoryLinkQuery('c.category', $category, 'where');
}

$query .= ' WHERE '.implode(' AND ',$where);
$query .= " GROUP BY PGV.goodsno ";
$query .= " ORDER BY `cnt` DESC ";

$rs = $db->query($query);

// 총 카운트 수 (group by 하지 않은 쿼리 실행후 cnt 필드)
$query = "
	SELECT
		SUM(PGV.cnt) AS cnt
	FROM gd_goods_pageview AS PGV
	INNER JOIN gd_goods AS G
	ON PGV.goodsno = G.goodsno
	INNER JOIN gd_goods_option AS GO
	ON G.goodsno = GO.goodsno AND GO.link = 1 and go_is_deleted <> '1'
";
if ($category){
	$query .= " left join ".GD_GOODS_LINK." c on G.goodsno=c.goodsno ";
}
$query .= ' WHERE '.implode(' AND ',$where);

$tmp = $db->fetch($query,1);
$total_cnt = $tmp['cnt'];

//
$file_name = "페이지뷰분석.xls";
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
		<Style ss:ID="sPercent">
		<Font x:Family="Modern" x:CharSet="129" ss:FontName="Gulim" ss:Bold="0"  />
		<NumberFormat ss:Format="Percent"/>
		</Style>
	</Styles>
	<Worksheet ss:Name="Sheet1">
		<Table ss:ExpandedColumnCount="7" ss:ExpandedRowCount="65535" x:FullColumns="1" x:FullRows="1">
			<Row>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('순위')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('상품')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('상품등록일')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('가격')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('재고량')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('횟수')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('비율')?></Data></Cell>
			</Row>
		<? $rank = 1; ?>
		<? while($row = $db->fetch($rs,1)) { ?>
		<? $row['rate'] = round(($row[cnt] / $total_cnt) * 100 * 100) / 10000; ?>
			<Row>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=$rank++?></Data></Cell>
				<Cell ss:StyleID="sBody"><Data ss:Type="String"><?=__utf8(strip_tags($row['goodsnm']))?></Data></Cell>
				<Cell ss:StyleID="sBody"><Data ss:Type="String"><?=$row['regdt']?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['price'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['totstock'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['cnt'])?></Data></Cell>
				<Cell ss:StyleID="sPercent"><Data ss:Type="Number"><?=($row['rate'])?></Data></Cell>
			</Row>
		<? } ?>
		</Table>
	</Worksheet>
</Workbook>
