<?

set_time_limit(0);

header("Content-Type: application/vnd.ms-excel; charset=euc-kr");
header("Content-Disposition: attachment; filename=Tax_".date("YmdHi").".xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

$query = stripslashes($_POST[query]);

include "../lib.php";

$tax_step = array( '발행신청', '발행승인', '발행완료', '전자발행' );
$res = $db->query($query);

?>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<style>td {mso-number-format:"@"}</style>
<? if ($_POST[mode] == 'etax'){

	include_once dirname(__FILE__)."/../../lib/tax.class.php";
	$etax = new eTax();

	include_once dirname(__FILE__)."/../../lib/json.class.php";
	$json = new Services_JSON();
?>

<table border=1>
<tr>
	<th colspan=7 style="font-size:14pt;">세금계산서</th>
	<th colspan=4 style="font-size:14pt;">전자발행정보</th>
	<th colspan=7 style="font-size:14pt;">비고</th>
</tr>
<tr bgcolor=#f7f7f7>
	<th>번호</th>
	<th>회사명</th>
	<th>사업자번호</th>
	<th>상품명</th>
	<th>발행액</th>
	<th>공급액</th>
	<th>부가세(10%)</th>

	<th>세금계산서식별번호</th>
	<th>발행상태</th>
	<th>삭제상태</th>
	<th>승인/반려시간</th>

	<th>발행상태</th>
	<th>신청자</th>
	<th>아이디</th>
	<th>주문번호</th>
	<th>결제금액</th>
	<th>신청일</th>
	<th>요청일</th>
</tr>
<? while ($data=$db->fetch($res)){
	### 주문데이타
	$query = "select settleprice from ".GD_ORDER." where ordno='$data[ordno]'";
	$o_data = $db->fetch($query);


	### 전자발행상태정보
	$out = $etax->getTaxbill( array('doc_number' => $data['doc_number']) );
	if (preg_match("/^false/i",$out[1])) $edata = array();
	else $edata = $json->decode(trim(preg_replace("/^true[ |]*-[ |]*/i", "", $out[1])));
?>
<tr>
	<td><?=++$idx?></td>
	<td><?=$data[company]?></td>
	<td><?=$data[busino]?></td>
	<td><?=$data[goodsnm]?></td>
	<td><?=$data[price]?></td>
	<td><?=$data[supply]?></td>
	<td><?=$data[surtax]?></td>

	<td><?=$edata->mtsid?></td>
	<td><?=$edata->status_txt?></td>
	<td><?=$edata->del_status?></td>
	<td><?=$edata->act_tm?></td>

	<td><?=$tax_step[ $data[step] ]?></td>
	<td><?=$data[m_name]?></td>
	<td><?=$data[m_id]?></td>
	<td><?=$data[ordno]?></td>
	<td><?=$o_data[settleprice]?></td>
	<td><?=$data[regdt]?></td>
	<td><?=$data[agreedt]?></td>
</tr>
<? } ?>
</table>

<? } else { ?>

<table border=1>
<tr>
	<th colspan=8 style="font-size:14pt;">세금계산서</th>
	<th colspan=8 style="font-size:14pt;">비고</th>
</tr>
<tr bgcolor=#f7f7f7>
	<th>번호</th>
	<th>발행일</th>
	<th>회사명</th>
	<th>사업자번호</th>
	<th>상품명</th>
	<th>발행액</th>
	<th>공급액</th>
	<th>부가세(10%)</th>

	<th>발행상태</th>
	<th>신청자</th>
	<th>아이디</th>
	<th>주문번호</th>
	<th>결제금액</th>
	<th>신청일</th>
	<th>승인일</th>
	<th>인쇄일</th>
</tr>
<? while ($data=$db->fetch($res)){
	### 주문데이타
	$query = "select settleprice from ".GD_ORDER." where ordno='$data[ordno]'";
	$o_data = $db->fetch($query);
?>
<tr>
	<td><?=++$idx?></td>
	<td><?=$data[issuedate]?></td>
	<td><?=$data[company]?></td>
	<td><?=$data[busino]?></td>
	<td><?=$data[goodsnm]?></td>
	<td><?=$data[price]?></td>
	<td><?=$data[supply]?></td>
	<td><?=$data[surtax]?></td>

	<td><?=$tax_step[ $data[step] ]?></td>
	<td><?=$data[m_name]?></td>
	<td><?=$data[m_id]?></td>
	<td><?=$data[ordno]?></td>
	<td><?=$o_data[settleprice]?></td>
	<td><?=$data[regdt]?></td>
	<td><?=$data[agreedt]?></td>
	<td><?=$data[printdt]?></td>
</tr>
<? } ?>
</table>

<? } ?>