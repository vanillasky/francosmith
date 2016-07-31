<?
include "../lib.php";
if (!$_POST['query']) exit;
if (get_magic_quotes_gpc()) $_POST['query'] = stripslashes($_POST['query']);
$query = base64_decode($_POST['query']);
$rs = $db->query($query);



$file_name = ".xls";
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
		<Style ss:ID="sBody">
		<Font x:Family="Swiss" x:CharSet="129" ss:FontName="Gulim" ss:Bold="0" />
		</Style>
		<Style ss:ID="sHead">
		<Alignment ss:Horizontal="Center" />
		<Font x:Family="Swiss" x:CharSet="129" ss:FontName="Gulim" ss:Bold="1"  />
		</Style>
		<Style ss:ID="sNum">
		<Alignment ss:Horizontal="Center" />
		<Font x:Family="Swiss" x:CharSet="129" ss:FontName="Gulim" ss:Bold="0"  />
		<NumberFormat ss:Format="#,##0_ "/>
		</Style>
	</Styles>
	<Worksheet ss:Name="Sheet1">
		<Table ss:ExpandedColumnCount="<?=mysql_num_fields($rs)?>" ss:ExpandedRowCount="65535" x:FullColumns="1" x:FullRows="1">
		<? while ($row = $db->fetch($rs,1)) { ?>
			<Row>
				<?
				foreach ($row as $v) {
				?>
				<Cell ss:StyleID="s21">
					<Data ss:Type="<?=is_numeric($v) ? 'Number' : 'String'?>"><?=$v?></Data>
				</Cell>
				<? } ?>
			</Row>
		<? } ?>
		</Table>
	</Worksheet>
</Workbook>