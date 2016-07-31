<?

include '../_header.popup.php';
include '../../lib/cashreceipt.class.php';
$cashreceipt = new cashreceipt();

$data = $db->fetch("select * from ".GD_CASHRECEIPT." where crno='{$_GET['crno']}'");

$pgs = array('inicis' => 'KG�̴Ͻý�', 'inipay' => 'KG�̴Ͻý�', 'allat' => '�Ｚ�þ�', 'allatbasic' => '�Ｚ�þ�', 'dacom' => 'LG U+', 'lgdacom' => 'LG U+', 'kcp'=>'KCP', 'agspay'=>'�ô�����Ʈ', 'easypay'=>'��������', 'settlebank'=>'��Ʋ��ũ');
$pgCompany = $pgs[ $data['pg'] ];
if ($pgCompany == '') $pgCompany = strtoupper($data['pg']);

# �ֹ�����
if ($data['singly'] == 'Y')
{
	$step = '�����߱�';
}
else
{
	$order = $db->fetch("select ordno, step, step2 from ".GD_ORDER." where ordno='{$data['ordno']}'");
	if ($order['ordno'] == '') $step = '�����ֹ���';
	else $step = getStepMsg($order['step'],$order['step2'],$order['ordno']);
	if(strlen($step) > 10) $step = substr($step,10);
}

# ó������
$status = $cashreceipt->r_status[ $data['status'] ];
if ($data['errmsg']){
	$status = '<span class="red hand" onclick="alert(\''.$data['errmsg'].'\')">'.$status.' -> �߱޽���</span>';
}

?>

<div class="title title_top">���ݿ����� ��û����</div>

<table class="tb">
<col class="cellC"><col class="cellL" width="150"><col class="cellC"><col class="cellL">
<tr>
	<td>��������(PG)</td>
	<td colspan="3"><?=$pgCompany?></span></td>
</tr>
<tr>
	<td>ó������</td>
	<td><?=$status?></span></td>
	<td>���ι�ȣ</td>
	<td><?=$data['receiptnumber']?></span></td>
</tr>
<tr>
	<td>ó������</td>
	<td><?=$data['moddt']?></span></td>
	<td>�ŷ���ȣ</td>
	<td><?=($data['tid'] ? $data['tid'] : '��')?></span></td>
</tr>
<tr>
	<td>�ֹ�����</td>
	<td><?=$step?></span></td>
	<td>��û����</td>
	<td><?=$data['regdt']?></span></td>
</tr>
<tr>
	<td>�ֹ���ȣ</td>
	<td><?=$data['ordno']?></span></td>
	<td>��û IP</td>
	<td><?=$data['ip']?></span></td>
</tr>
<tr>
	<td>�ֹ��ڸ�</td>
	<td><?=$data['buyername']?></span></td>
	<td>����ó</td>
	<td>
		<? if ($data['buyeremail']){ ?>&#149; �̸��� <font color="white">---</font> <?=$data['buyeremail']?><br><? } ?>
		<? if ($data['buyerphone']){ ?>&#149; ��ȭ��ȣ&nbsp; <?=$data['buyerphone']?><? } ?>
	</td>
</tr>
<tr>
	<td>��ǰ��</td>
	<td colspan="3"><?=$data['goodsnm']?></td>
</tr>
<tr>
	<td>��ǰ����</td>
	<td style="padding:5px;">
	����� : <span style="width:70px;text-align:right;"><?=number_format($data['amount'])?></span><br>
	���޾� : <span style="width:70px;text-align:right;"><?=number_format($data['supply'])?></span><br>
	�ΰ��� : <span style="width:70px;text-align:right;"><?=number_format($data['surtax'])?></span>
	</td>
	<td>����뵵</td>
	<td style="padding:5px;">
		<?=($data['useopt'] == '0' ? '���μҵ������' : '���������������');?>
		<div style="border:solid 1px #dddddd; padding:5px; background-color:#F6F6F6; margin-top:5px;">
			�������� <?=$data['certno'];?><?=($data['certno_encode'] ? '-xxxxxxx' : '')?>
		</div>
	</td>
</tr>
<tr>
	<td>�α�</td>
	<td colspan="3"><textarea style="width:100%;height:160px;overflow:visible;font:9pt ����ü;padding:10px 0 0 8px"><?=trim($data['receiptlog'])?></textarea></td>
</tr>
</table>

<script>table_design_load();</script>