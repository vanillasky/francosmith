<?
include "../lib.php";
if (!$_POST['query']) exit;
if (get_magic_quotes_gpc()) $_POST['query'] = stripslashes($_POST['query']);
$query = base64_decode($_POST['query']);

$query = str_replace('GROUP BY O.settlekind','GROUP BY O.settlekind , `date`',$query);	// 콤마의 위치를 서로 달리해서 간단히 처리하기로함;;
$query = str_replace('O.settlekind,','O.settlekind,DATE_FORMAT(O.cdt,\'%Y-%m-%d\') AS `date`,',$query);
$query .= ' ORDER BY `date`';

$rs = $db->query($query);

function __utf8($str) {
	return iconv('EUC-KR','UTF-8',$str);
}

$arRow = array();
while ($row = $db->fetch($rs,1)) {
	$_key = $row['settlekind'] ? $r_settlekind[$row['settlekind']] : '미지정';
	$arRow[$_key][] = $row;
}
$db->free($rs);

$file_name = "결제수단별매출통계.xls";

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
	<? foreach($arRow as $settlekind => $data) { ?>
	<Worksheet ss:Name="<?=__utf8($settlekind)?>">
		<Table ss:ExpandedColumnCount="12" ss:ExpandedRowCount="65535" x:FullColumns="1" x:FullRows="1">
			<Row>
				<!--Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('날짜')?></Data></Cell-->
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('건수')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('적립금적용')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('회원할인')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('쿠폰할인')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('상품할인')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('에누리')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('주문금액')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('결제금액(배송비포함)')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('매출금액(배송비제외)')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('매입금액')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('판매이익')?></Data></Cell>
			</Row>
		<? for ($i=0,$m=sizeof($data);$i<$m;$i++) { ?>
		<? $row = $data[$i]; ?>
			<Row>
				<!--Cell ss:StyleID="sBody"><Data ss:Type="String"><?=($row['date'])?></Data></Cell-->
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['cnt'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['tot_emoney'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['tot_member_dc'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['tot_coupon_dc'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['tot_goods_dc'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['tot_enuri_dc'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['tot_price'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['tot_settle'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['tot_settle'] - $row['tot_delivery'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['tot_supply'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['tot_settle'] - $row['tot_delivery'] - $row['tot_supply'])?></Data></Cell>
			</Row>
		<? } ?>
		</Table>
	</Worksheet>
	<? } ?>
</Workbook>
