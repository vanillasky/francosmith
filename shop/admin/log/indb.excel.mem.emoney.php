<?
include "../lib.php";

function __utf8($str) {
	return iconv('EUC-KR','UTF-8',$str);
}

$where = array();

//debug($_POST);
// 검색 조건

	$_POST['stype'] = isset($_POST['stype']) ? $_POST['stype'] : 'by_m_no';

	$sdate_s = ($_POST['regdt'][0]) ? date('Y-m-d',strtotime($_POST['regdt'][0])) : date('Y-m',G_CONST_NOW).'-01';
	$sdate_e = ($_POST['regdt'][1]) ? date('Y-m-d', strtotime('+1 day', strtotime($_POST['regdt'][1]))) : date('Y-m',strtotime('+1 month', strtotime($sdate_s))).'-01';

	$_POST['regdt'][0] = $_POST['regdt'][0] ? $_POST['regdt'][0] : date('Ym', G_CONST_NOW).'01';
	$_POST['regdt'][1] = $_POST['regdt'][1] ? $_POST['regdt'][1] : date('Ymt',G_CONST_NOW);

	if (!isset($_POST['syear']) && !isset($_POST['smonth'])) {

		$_POST['syear'][0] = date('Y',G_CONST_NOW);
		$_POST['syear'][1] = date('Y',G_CONST_NOW);

		$_POST['smonth'][0] = date('n',G_CONST_NOW);
		$_POST['smonth'][1] = date('n',G_CONST_NOW);
	}

	switch($_POST['stype']) {
		case 'by_m_no' :
			/* 회원별 -------- */

			$sword = isset($_POST['sword']) ? $_POST['sword'] : '';
			if ($sword) {
				if ($_POST['skey'] == 'all') {
					$where[] = "( CONCAT( MB.m_id, MB.name, MB.nickname, MB.email, MB.phone, MB.mobile, MB.recommid, MB.company ) like '%".$_POST['sword']."%' or MB.nickname like '%".$_POST['sword']."%' )";
				}
				else {
					$where[] = 'MB.'.$_POST['skey']." like '%$sword%'";
				}
			}

			$slevel = isset($_POST['slevel']) ? $_POST['slevel'] : '';
			if ($slevel) {
				$where[] = "MB.level='".$slevel."'";
			}

			$group_field = 'M.m_no';
			$group_order_field = 'O.m_no';
			$join_field = 'XO.m_no';
			$extra_fields = 'MB.m_id, MB.name,';
			break;


		case 'by_month' :
			/* 월별 ---------- */

			$sdate_s = date('Y-m-d',strtotime($_POST['syear'][0].'-'.$_POST['smonth'][0].'-1'));
			$sdate_e = date('Y-m-d', strtotime('+1 month', strtotime($_POST['syear'][1].'-'.$_POST['smonth'][1].'-1')));

			$group_field = "DATE_FORMAT(M.regdt, '%Y-%m')";
			$group_order_field = "DATE_FORMAT(O.ddt, '%Y-%m')";
			$group_order_allias = " as ddt";

			$join_field = "XO.ddt";
			$extra_fields = '';
			break;


		case 'by_day' :
			/* 일별 ---------- */

			$group_field = "DATE_FORMAT(M.regdt, '%Y-%m-%d')";
			$group_order_field = "DATE_FORMAT(O.ddt, '%Y-%m-%d')";
			$group_order_allias = " as ddt";
			$join_field = "XO.ddt";
			$extra_fields = '';
			break;
	}

	$where[] = "M.regdt >= '$sdate_s'";
	$where[] = "M.regdt < '$sdate_e'";


// sql
	$query = "
	SELECT
		$group_field AS `group_field`,

		$extra_fields

		SUM( IF(M.emoney > 0, M.emoney, 0) ) AS plus,
		COUNT( IF(M.emoney > 0, 1, null) ) AS plus_cnt,
		SUM( IF(M.emoney < 0, M.emoney, 0) ) AS minus,
		COUNT( IF(M.emoney < 0, 1, null) ) AS minus_cnt,
		XO.reserve, XO.reserve_cnt
	FROM
	".GD_LOG_EMONEY." AS M
	INNER JOIN ".GD_MEMBER." AS MB
	ON M.m_no = MB.m_no
	LEFT JOIN 
	(
	SELECT ".$group_order_field.$group_order_allias.",
		SUM( IF ('".$set['emoney']['limit']."' = '1' && O.emoney > 0 , null, O.reserve)	) AS reserve,
		COUNT( IF ('".$set['emoney']['limit']."' = '1' && O.emoney > 0 , null, O.reserve) ) AS reserve_cnt	
	FROM
	".GD_ORDER." AS O
	WHERE 
	O.m_no AND O.step = 3 AND O.step2 < 40
	AND (O.ncash_save_yn is null OR ncash_save_yn='n' OR ncash_save_yn='b')
	GROUP BY $group_order_field
	) XO
	ON ".$group_field." = ".$join_field."
	WHERE ".implode(' AND ', $where)."
	GROUP BY `group_field`
	";

// 쿼리
$rs = $db->query($query);

//
$file_name = "회원적립금분석.xls";

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
		<Table ss:ExpandedColumnCount="<?=($_POST['stype'] == 'by_m_no') ? 9 : 8?>" ss:ExpandedRowCount="65535" x:FullColumns="1" x:FullRows="1">
			<Row>
				<? if ($_POST['stype'] == 'by_m_no') { ?>
				<Cell ss:MergeAcross="1" ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('회원')?></Data></Cell>
				<? } else { ?>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('날짜')?></Data></Cell>
				<? } ?>
				<Cell ss:MergeAcross="1" ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('지급적립금')?></Data></Cell>
				<Cell ss:MergeAcross="1" ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('사용적립금')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('잔여적립금')?></Data></Cell>
				<Cell ss:MergeAcross="1" ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('잠정적립금(배송완료 전)')?></Data></Cell>
			</Row>
			<Row>
				<? if ($_POST['stype'] == 'by_m_no') { ?>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('이름')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('아이디')?></Data></Cell>
				<? } else if ($_POST['stype'] == 'by_month') { ?>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('년/월')?></Data></Cell>
				<? } else { ?>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('년/월/일')?></Data></Cell>
				<? } ?>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('건수')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('금액')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('건수')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('금액')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('금액')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('건수')?></Data></Cell>
				<Cell ss:StyleID="sHead"><Data ss:Type="String"><?=__utf8('금액')?></Data></Cell>
			</Row>
		<? while($row = $db->fetch($rs,1)) { ?>
			<Row>
				<? if ($_POST['stype'] == 'by_m_no') { ?>
				<Cell ss:StyleID="sBody"><Data ss:Type="String"><?=__utf8($row['name'])?></Data></Cell>
				<Cell ss:StyleID="sBody"><Data ss:Type="String"><?=__utf8($row['m_id'])?></Data></Cell>
				<? } else { ?>
				<Cell ss:StyleID="sBody"><Data ss:Type="String"><?=__utf8($row['group_field'])?></Data></Cell>
				<? } ?>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['plus_cnt'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['plus'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['minus_cnt'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['minus'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['plus'] + $row['minus'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['reserve_cnt'])?></Data></Cell>
				<Cell ss:StyleID="sNum"><Data ss:Type="Number"><?=number_format($row['reserve'])?></Data></Cell>
			</Row>
		<? } ?>
		</Table>
	</Worksheet>
</Workbook>
