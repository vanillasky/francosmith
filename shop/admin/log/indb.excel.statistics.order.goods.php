<?
include "../lib.php";
if (!$_POST['query']) exit;
if (get_magic_quotes_gpc()) $_POST['query'] = stripslashes($_POST['query']);
$_query = base64_decode($_POST['query']);

$tmp = explode('limit' , $_query);
array_pop($tmp);
$query = implode('limit',$tmp);

$rs = $db->query($query);

function __utf8($str) {
	return iconv('EUC-KR','UTF-8',$str);

}

$file_name = "상품별주문통계.xls";

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
		<Table ss:ExpandedColumnCount="10" ss:ExpandedRowCount="65535" x:FullColumns="1" x:FullRows="1">
			<Row>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('번호')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('상품명')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('가격')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('총주문건')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('주문접수')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('입금확인')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('배송준비')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('배송중')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('배송완료')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('주문취소')?></Data></Cell>
			</Row>
		<? $idx = $db->count_($rs); ?>
		<? while ($row = $db->fetch($rs,1)) { ?>
			<Row>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($idx)?></Data></Cell>
				<Cell ss:StyleID="sBody"><Data ss:Type="String"><?=__utf8($row['goodsnm'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['price'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['cnt_step_0'] + $row['cnt_step_1'] + $row['cnt_step_2'] + $row['cnt_step_3'] + $row['cnt_step_4'] + $row['cnt_step_cancel'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['cnt_step_0'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['cnt_step_1'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['cnt_step_2'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['cnt_step_3'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['cnt_step_4'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['cnt_step_cancel'])?></Data></Cell>
			</Row>
		<? $idx--; ?>
		<? } ?>
		</Table>
	</Worksheet>
</Workbook>