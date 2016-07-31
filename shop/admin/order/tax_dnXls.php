<?

set_time_limit(0);

header("Content-Type: application/vnd.ms-excel; charset=euc-kr");
header("Content-Disposition: attachment; filename=Tax_".date("YmdHi").".xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

$query = stripslashes($_POST[query]);

include "../lib.php";

$tax_step = array( '�����û', '�������', '����Ϸ�', '���ڹ���' );
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
	<th colspan=7 style="font-size:14pt;">���ݰ�꼭</th>
	<th colspan=4 style="font-size:14pt;">���ڹ�������</th>
	<th colspan=7 style="font-size:14pt;">���</th>
</tr>
<tr bgcolor=#f7f7f7>
	<th>��ȣ</th>
	<th>ȸ���</th>
	<th>����ڹ�ȣ</th>
	<th>��ǰ��</th>
	<th>�����</th>
	<th>���޾�</th>
	<th>�ΰ���(10%)</th>

	<th>���ݰ�꼭�ĺ���ȣ</th>
	<th>�������</th>
	<th>��������</th>
	<th>����/�ݷ��ð�</th>

	<th>�������</th>
	<th>��û��</th>
	<th>���̵�</th>
	<th>�ֹ���ȣ</th>
	<th>�����ݾ�</th>
	<th>��û��</th>
	<th>��û��</th>
</tr>
<? while ($data=$db->fetch($res)){
	### �ֹ�����Ÿ
	$query = "select settleprice from ".GD_ORDER." where ordno='$data[ordno]'";
	$o_data = $db->fetch($query);


	### ���ڹ����������
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
	<th colspan=8 style="font-size:14pt;">���ݰ�꼭</th>
	<th colspan=8 style="font-size:14pt;">���</th>
</tr>
<tr bgcolor=#f7f7f7>
	<th>��ȣ</th>
	<th>������</th>
	<th>ȸ���</th>
	<th>����ڹ�ȣ</th>
	<th>��ǰ��</th>
	<th>�����</th>
	<th>���޾�</th>
	<th>�ΰ���(10%)</th>

	<th>�������</th>
	<th>��û��</th>
	<th>���̵�</th>
	<th>�ֹ���ȣ</th>
	<th>�����ݾ�</th>
	<th>��û��</th>
	<th>������</th>
	<th>�μ���</th>
</tr>
<? while ($data=$db->fetch($res)){
	### �ֹ�����Ÿ
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