<?

$location = '���ݿ����� ���� > ���ݿ����� �߱޼���';
include '../_header.php';
include '../../conf/config.php';
include "../../conf/config.pay.php";
if ($cfg['settlePg'] !== '' && file_exists('../../conf/pg.'. $cfg['settlePg'] .'.php')){
	include '../../conf/pg.'. $cfg['settlePg'] .'.php';
}

$pgs = array('inicis' => 'KG�̴Ͻý�', 'inipay' => 'KG�̴Ͻý�', 'allat' => '�Ｚ�þ�', 'allatbasic' => '�Ｚ�þ�', 'dacom' => 'LG U+', 'lgdacom' => 'LG U+', 'kcp'=>'KCP', 'agspay'=>'�ô�����Ʈ', 'easypay'=>'��������', 'settlebank'=>'��Ʋ��ũ');
$pgCompany = $pgs[ $cfg['settlePg'] ];
if ($pgCompany == '') $pgCompany = strtoupper($cfg['settlePg']);

if ($set['receipt']['publisher'] == '') $set['receipt']['publisher'] = 'buyer';
if ($set['receipt']['order'] == '') $set['receipt']['order'] = 'N';
if ($set['receipt']['auto'] == '') $set['receipt']['auto'] = 'N';
if ($set['receipt']['compType'] == '') $set['receipt']['compType'] = '0';

// �����������̰� �ڵ��߱��ΰ�� �����߱����� ����
if ($set['receipt']['order'] == 'N' && $set['receipt']['auto'] == 'Y') {
	$set['receipt']['auto']	= 'N';
}

$checked['receipt'][$pg['receipt']] = 'checked';
$checked['publisher'][$set['receipt']['publisher']] = 'checked';
$checked['order'][$set['receipt']['order']] = 'checked';
$checked['auto'][$set['receipt']['auto']] = 'checked';
$selected['period'][$set['receipt']['period']] = 'selected';
$checked['compType'][$set['receipt']['compType']] = 'checked';
?>

<form method="post" action="../order/cashreceipt.indb.php">
<input type="hidden" name="mode" value="manage">

<div class="title title_top">���ݿ����� �߱޼��� <span>������ PG���� ���ݿ������� ����ϸ�, ���� ��� �ʿ����</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=11')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>��������(PG)</td>
	<td><?=($pgCompany != '' ? $pgCompany : '<font color="#EA0095"><b>��������(PG)�� ���� ��û/�����ϼ���.</b></font>')?> &nbsp;&nbsp;<font color=0864a5>(�������� �ȳ��� <a href="../basic/pg.intro.php"><u><font color=ff4200><b>����</b></font></u></a>�� Ŭ���ϼ���)</font></td>
</tr>
<tr>
	<td><?=$pgCompany?> ID</td>
	<td><?=$pg['id']?></td>
</tr>
<tr>
	<td>�߱� ��뿩��</td>
	<td>
	<input type="radio" name="pg[receipt]" value="N" class="null" <?=$checked['receipt']['N']?> onclick="setDisabled()"> ������
	<input type="radio" name="pg[receipt]" value="Y" class="null" <?=$checked['receipt']['Y']?> onclick="setDisabled()"> ���
	<span style="padding-left:20px">(�� ������ PG���� ���ݿ������� ����ϸ�, ������ü/������� �̿�� �ڵ� ��û/�߱�)</span>
	</td>
</tr>
<tr>
	<td>��������</td>
	<td style="padding:5px;">
	���ݿ������� 1�� �̻��� ���ݼ��ŷ�(�������Ա�, �ǽð�������ü, ����ũ��)�� ���� �߱��� �˴ϴ�.<br>
	<div class="small4" style="color:#6d6d6d">(����û�� ��å�� ���� ���� �� �� �ֽ��ϴ�.)</div>
	</td>
</tr>
<tr>
	<td>�� �� ��</td>
	<td style="padding:5px;">
	<div>
	<input type="radio" name="set[receipt][publisher]" value="buyer" class="null" <?=$checked['publisher']['buyer']?> onclick="setDisabled()"> ������ �߱�
	<span class="small4" style="color:#6d6d6d">(�����ڰ� �������������� �������� ���� �߱��մϴ�.)</span>
	</div>
	<div>
	<input type="radio" name="set[receipt][publisher]" value="seller" class="null" <?=$checked['publisher']['seller']?> onclick="setDisabled()"> ������ �߱�
	<span class="small4" style="color:#6d6d6d">(�����ڰ� �ֹ���/�������������� ������ �߱��� ��û�ϸ� �����ڰ� �߱��մϴ�.)</span>
	</div>
	</td>
</tr>
</table>
<table class="tb" id="seller_option">
<col class="cellC"><col class="cellL">
<tr>
	<td>��û���</td>
	<td>
	<div>
	<input type="radio" name="set[receipt][order]" value="N" class="null" <?=$checked['order']['N']?> onclick="setAutoCheck();"> ����������
	<span class="small4" style="color:#6d6d6d">(���������������� ���ݿ������� ��û�մϴ�.)</span>
	</div>
	<div>
	<input type="radio" name="set[receipt][order]" value="Y" class="null" <?=$checked['order']['Y']?> onclick="setAutoCheck();"> �ֹ���+����������
	<span class="small4" style="color:#6d6d6d">(�ֹ��� �ۼ��� ��û�ϰų� �������������� ��û�մϴ�.)</span>
	</div>
	</td>
</tr>
<tr>
	<td>�߱޹��</td>
	<td>
	<div>
	<input type="radio" name="set[receipt][auto]" value="Y" class="null" <?=$checked['auto']['Y']?> onclick="setAutoCheck();"> �ڵ��߱�
	<span class="small4" style="color:#6d6d6d">(�����ڰ� ���ݿ������� ��û�ϸ� �Ա�Ȯ��/��ҿϷ� �ܰ迡�� �ڵ����� �߱�/��ҵ˴ϴ�.)</span>
	</div>
	<div>
	<input type="radio" name="set[receipt][auto]" value="N" class="null" <?=$checked['auto']['N']?> onclick="setAutoCheck();"> �����߱�
	<span class="small4" style="color:#6d6d6d">(�����ڰ� �Ǻ��� �߱޹�ư / ��ҹ�ư�� ������ �߱� / ����մϴ�.)</span>
	</div>
	</td>
</tr>
</table>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td><span id="periodStr1">��û</span>�Ⱓ����</td>
	<td style="padding:5px;">
	�� �ֹ��Ϸκ���
	<select name="set[receipt][period]">
	<option value="">���Ѿ���</option>
	<option value="2" <?=$selected['period']['2']?>>2��</option>
	<option value="7" <?=$selected['period']['7']?>>7��</option>
	<option value="15" <?=$selected['period']['15']?>>15��</option>
	<option value="30" <?=$selected['period']['30']?>>30��</option>
	<option value="60" <?=$selected['period']['60']?>>60��</option>
	<option value="90" <?=$selected['period']['90']?>>90��</option>
	</select>�̳� <span id="periodStr2">��û</span> ����
	<div class="small4" style="color:#6d6d6d">(������������ �ֹ������󼼿� ������ ���Ŀ��� ���ݿ����� ��ư�� ǥ�õ��� �ʽ��ϴ�.)</div>
	</td>
</tr>
<tr>
	<td>���������</td>
	<td style="padding:5px;">
	<div>
	<input type="radio" name="set[receipt][compType]" value="0" class="null" <?=$checked['compType']['0']?>> �Ϲ� ���������
	<span class="small4" style="color:#6d6d6d">(�ǸŹ�ǰ�� �ΰ����� ����, ���ݿ������� ���ް���,�ΰ����� �и��Ǿ� ����û�뺸 �˴ϴ�.)</span>
	</div>
	<div>
	<input type="radio" name="set[receipt][compType]" value="1" class="null" <?=$checked['compType']['1']?>> �鼼/���̻����
	<span class="small4" style="color:#6d6d6d">(�ǸŹ�ǰ�� �ΰ����� ����, ���ݿ������� ���ް��� = �հ�ݾ� (�ΰ�������)���� ����û�뺸 �˴ϴ�)</span>
	</div>
	</td>
</tr>
<? if ($cfg['settlePg'] == 'dacom'){ ?>
<tr>
	<td>����ڹ�ȣ</td>
	<td style="padding:5px;">
	&#149; <?=$cfg['compSerial']?>
	<span class="small4" style="color:#6d6d6d">(�������� ���� ���ݿ����� �߱޽� �ʿ��մϴ�. <a href="../basic/default.php">[���θ��⺻����]</a> ���� �����մϴ�.)</span>
	</td>
</tr>
<? } ?>
</table>

<!-- ���� ������ ���� �ȳ� : Start -->
<div style="border:solid 4px #dce1e1; border-collapse:collapse; margin:20px 0px 20px 0px; color:#666666;">
	<ul style="padding:0px 0px 0px 0px;">
		<li style="padding:3px; margin-left:10px; list-style-type:none; color:#ff0000; font-weight:bold;">�� �߱޹���� &quot;�ڵ��߱�&quot;���� ������ �ݵ�� Ȯ�����ּ���!</li>
		<li style="padding:3px; margin-left:30px; list-style-type:disc;">�Ʒ��� ���ǿ����� ���ݿ����� �ڵ��߱��� ����� �� �����Ƿ�, <a href="./cashreceipt.list.php" style="color:#627dce; font-weight:bold; text-decoration:underline;">[���ݿ����� �߱�/��ȸ]</a>�� ���Ͽ� �ݵ�� Ȯ�� �� �����Ͽ� �ֽñ� �ٶ��ϴ�.</li>
		<li style="padding:3px; margin-left:30px; list-style-type:disc;">�����ڰ� �������������� ���ݿ����� �߱� ��û�� �� ���</li>
		<li style="padding:3px; margin-left:30px; list-style-type:disc;">������ �ڵ��Ա� ���񽺷� ���Ͽ� �ֹ����� �ֹ����°� �Ա� Ȯ������ �ڵ� ���� �� ���</li>
	</ul>
</div>
<!-- ���� ������ ���� �ȳ� : End -->

<div class="button">
<input type="image" src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>


<div id="MSG01">
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr>
	<td>
		<img src="../img/icon_list.gif" align="absmiddle">���ݿ����� ������ �Һ��ڿ��Դ� �ҵ������, ���������Դ� ���װ����� ������ �ָ� ������ �Һ�ȭ ������ �������� ������ ���� 2005�� 1�� 1�Ϻ��� ���� �Ǵ� �����Դϴ�.<br />
		&nbsp; &nbsp;(2008�� 7�� 1�Ϻ��� 5,000�� �̸� ���ݰ����� Ȯ��Ǿ� 1�� �̻��̸� �߱޵˴ϴ�.)
	</td>
</tr>
<tr>
	<td>
		<img src="../img/icon_list.gif" align="absmiddle">���ݿ����� ������ ������ �ޱ� ���ؼ� �Һ��ڴ� ����(�������Ա�) ���Ž� �Ǹ���(���ݿ����� ������)���� ���ݿ����� �߱��� ��û�� ��, ����Ȯ���� ������ �޴���ȭ ��ȣ ���� �����ؾ� �մϴ�.
	</td>
</tr>
<tr>
	<td>
		<img src="../img/icon_list.gif" align="absmiddle">���ݿ����� ���� �ŷ������� �ֹ����ڿ� ������� �߱����ڸ� �������� �߱޵Ǹ� ���� ����5�� ���� ����ûȨ������ <a href="http://���ݿ�����.kr" target="_blank"><b>http://���ݿ�����.kr</b></a>�� ���� Ȯ���� �� �ֽ��ϴ�.<br />
		&nbsp; &nbsp;����, ���� �ֹ����ڿ� ���̰� �߻��� �� �����Ƿ� �ΰ��� �Ű� �� ����Ű� �����Ͻñ� �ٶ��ϴ�.
	</td>
</tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ݿ����� ���񽺸� �̿��Ͻ÷��� <b>��������(PG)�� ���� ��û�Ͻ� �� �������� �������������������� ���ݿ����� ���񽺸� ��û�մϴ�.</b></td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<script language="javascript"><!--
function setDisabled()
{
	var fobj = document.getElementsByName('pg[receipt]')[0].form;
	var disabled = fobj['pg[receipt]'][0].checked;

	var len = fobj.elements.length;
	for (i = 0; i < len; i++){
		if (fobj.elements[i].name == 'pg[receipt]' || fobj.elements[i].name == 'mode') continue;
		fobj.elements[i].disabled = disabled;
	}

	if (disabled === false){
		var disabled = fobj['set[receipt][publisher]'][0].checked;
		fobj['set[receipt][order]'][0].disabled = disabled;
		fobj['set[receipt][order]'][1].disabled = disabled;
		fobj['set[receipt][auto]'][0].disabled = disabled;
		fobj['set[receipt][auto]'][1].disabled = disabled;

		if (disabled === true){
			_ID('periodStr1').innerHTML = '����';
			_ID('periodStr2').innerHTML = '����';
			_ID('seller_option').style.display	='none';
		}
		else {
			_ID('periodStr1').innerHTML = '��û';
			_ID('periodStr2').innerHTML = '��û';
			_ID('seller_option').style.display	='block';
		}
	}
}

/**
 * ��û��ο� �߱޹�� üũ
 */
function setAutoCheck()
{
	var fobj = document.getElementsByName('pg[receipt]')[0].form;
	var mypageCheck	= fobj['set[receipt][order]'][0].checked;
	var autoCheck	= fobj['set[receipt][auto]'][0].checked;

	if (mypageCheck === true && autoCheck === true) {
		alert('��û��ΰ� "����������"�� ��쿡�� �ڵ��߱��� �������� �ʽ��ϴ�.');
		fobj['set[receipt][auto]'][1].checked = 'checked';
	}
}

setDisabled();
//--></script>

<? include "../_footer.php"; ?>