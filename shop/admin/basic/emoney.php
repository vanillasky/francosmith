<?

$location = "�⺻���� > ������ ����";
include "../_header.php";
include "../../conf/config.pay.php";

$set = $set['emoney'];

if(!$set[limit])$set[limit] = 0;

$k_max = (strpos($set['max'],'%')!==false) ? 1 : 0;

$checked['useyn'][$set[useyn]] = "checked";
$checked['k_max'][$k_max] = "checked";
$checked['limit'][$set[limit]] = "checked";
$max[$k_max] = $set['max'];

if(!$set['emoney_delivery']) $set['emoney_delivery'] = 0;
$checked['emoney_delivery'][$set['emoney_delivery']] = "checked";

if(!$set['chk_goods_emoney']) $set['chk_goods_emoney'] = 0;
$checked['chk_goods_emoney'][$set['chk_goods_emoney']] = "checked";

if(!$set['emoney_use_range'])$set['emoney_use_range'] = 0;
$selected['emoney_use_range'][$set['emoney_use_range']] = "selected";

if(!$set['emoney_standard']) $set['emoney_standard'] = 0;
$checked['emoney_standard'][$set['emoney_standard']] = "checked";

if(!$set['useduplicate']) $set['useduplicate'] = 0;
$checked['useduplicate'][$set['useduplicate']] = "checked";

if($set['chk_goods_emoney'] == 0){
	$emoney_standard_display = " style='display:block' ";
}else {
	$emoney_standard_display = " style='display:none' ";
}

if (trim($set['cut']) === '') $set['cut'] = '2';
$selected['cut'][$set['cut']] = 'selected';
?>
<script language=javascript src="/shop/admin/common.js"></script>
<script language="javascript">
function chkGoodsEmoney(){
	var obj = document.getElementsByName('chk_goods_emoney');
	var txt = document.getElementsByName('goods_emoney[]');
	for(var i=0;i<obj.length;i++){
		if(obj[i].checked == true){
			txt[i].style.background = "#ffffff";
			txt[i].readOnly = false;
		}else{
			txt[i].style.background = "#e3e3e3";
			txt[i].readOnly = true;
			txt[i].value = '';
		}
	}
	var es_div = document.getElementById('es_div');
	if(obj[0].checked == true)es_div.style.display = "block";
	if(obj[1].checked == true)es_div.style.display = "none";
}
</script>
<form method=post action="indb.php" onsubmit="return chkForm(this);">
<input type=hidden name=mode value="emoney">

<div class="title title_top">������ ���޿� ���� ��å<span>ȸ������ ���޵Ǵ� �����ݿ� ���� ��å�Դϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=4')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr height=30>
	<td>������ ���޿���</td>
	<td class=noline>
	<input type=radio name=useyn value='y' <?=$checked['useyn']['y']?>> ���
	<input type=radio name=useyn value='n' <?=$checked['useyn']['n']?>> ������
	</td>
</tr>
<tr>
	<td>������ ����<br>��ǰ������ ���޿���</td>
	<td class=noline height=50>
		<div><input type=radio name='limit' value='0' <?=$checked['limit'][0]?>> �������� ����ص� �����Ϸ��� ��ǰ�� �������� �״�� �����մϴ�.</div>
		<div><input type=radio name='limit' value='1' <?=$checked['limit'][1]?>> �������� ����ϸ� �����Ϸ��� ��ǰ�� �������� �������� �ʽ��ϴ�.</div>
		<div class="extext_t">* ȸ���� ���������� �����Ϸ� �� �� ������ ��ǰ�� �����ݵ� ������ �� �������� �����ϴ� �׸��Դϴ�. <br>
���� ���, ������ 10,000���� ��ǰ(�����ϸ� 100�� ����)�� � ȸ���� ������ 5,000���� �̿��ؼ� �� ��ǰ�� �����Ϸ� �Ѵٸ�, �� ȸ������ 100���� ������ ���ٰ��ΰ� ���ϴ� ��å�Դϴ�. <br>
* �����, ȸ���� �������� ���� �Ǵ� ��Ÿ �������� ���� ���ݼ� ����Ʈ�̹Ƿ�, ���ݰ� �����ϰ� ����ϴ� ���� �����ϴ�.
</div>

	</td>
</tr>
</table>
<br>
<br>
<div class="title title_top">��ǰ ������ ���޿� ���� ��å<span>ȸ������ ���޵Ǵ� �����ݿ� ���� ��å�Դϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=4')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr height=30>
	<td>��ǰ������ �⺻����</td>
	<td>
	<div style='height:25;padding-top:2'><input type="radio" name="chk_goods_emoney" value="0" class="null" onclick="chkGoodsEmoney()" <?=$checked[chk_goods_emoney][0]?>>�ֹ� ��ǰ�ݾ��� <input type="text" name="goods_emoney[]" style="text-align:right" value="<?=$set['goods_emoney']?>" size=2 label="��ǰ������" onkeydown="onlynumber()" readonly class=rline> %�� ��ۿϷ� �� �����մϴ�.</div>
	<div style='height:25;padding-top:2'><input type="radio" name="chk_goods_emoney" value="1" class="null" onclick="chkGoodsEmoney()" <?=$checked[chk_goods_emoney][1]?>>�ֹ� ��ǰ �� <input type="text" name="goods_emoney[]" style="text-align:right" value="<?=$set['goods_emoney']?>" size=7 label="��ǰ������" onkeydown="onlynumber()" readonly class=rline> ����  ��ۿϷ� �� �����մϴ�.</div>
	<div style="padding-top:3"><font class=extext>* ��ǰ��Ͻ� ��ǰ���� ���� �������� �������� �Է��� ���� �ֽ��ϴ�.</font>
	</td>
</tr>
</table>
<br>
<div id="es_div" <?=$emoney_standard_display?>>
<table class=tb>
<col class=cellC><col class=cellL>
<tr height=50>
	<td>������ ��������</td>
	<td>
	<div><input type=radio name="emoney_standard" value="0" class=null <?=$checked['emoney_standard'][0]?>> ��ǰ �Ǹűݾ�
	<input type=radio name="emoney_standard" value="1" class=null <?=$checked['emoney_standard'][1]?>> �� �����ݾ�</div>
<div class="extext_t">* ������ ���������� "��ǰ �Ǹűݾ�"���� ���� �� ������/����/��ۺ� ���� �� ��ǰ �Ǹűݾ׸� �������� �������� �����˴ϴ�.<br>
* ������ ���������� "�� �����ݾ�"���� ���� �� ������/����/��ۺ� ���� �� �����ڰ� ���� ������ �� �ݾ��� �������� �������� �����˴ϴ�.</div>
	</td>
</tr>
</table>
</div>
<br>
<div class="title title_top">������ ��뿡 ���� ��å<span>ȸ������ ���޵Ǵ� �����ݿ� ���� ��å�Դϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=4')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr height=30>
	<td>��ǰ �ֹ��հ�� ����</td>
	<td>
	��ǰ �ֹ� �հ���� <input type=text name="totallimit" value="<?=$set['totallimit']?>" size=10 option="regNum" label="��ǰ�ֹ��հ��" onkeydown="onlynumber()" class=rline> �� �̻��϶� ��� �����մϴ�. <span class=extext>(��ǰ �ֹ����� �������� ������ ��� ������ �����մϴ�.)</span></td>
</tr>
<tr height=30>
	<td>��밡���� ������<br />�����ݾ�</td>
	<td>
	���� �������� <input type=text name="hold" value="<?=$set['hold']?>" size=10 option="regNum" label="�����ݻ�밡�ɾ�" onkeydown="onlynumber()" class=rline> �� �̻��϶� ��� �����մϴ�.</div></td>
</tr>
<tr height=50>
	<td>������ �ּ� ���ݾ�</td>
	<td>
	�������� �ּ�  <input type=text name=min value="<?=$set['min']?>" size=10 option="regNum" label="�ּ��ѵ���" onkeydown="onlynumber()" class=rline> �� �̻���� ����� �� �ֽ��ϴ�. <span class=extext>(�����Է�)</span></td>
</tr>

<tr height=50>
	<td>������ �ִ� ���ݾ�</td>
	<td>
	<input type=radio name=k_max value=0 class=null <?=$checked['k_max'][0]?>> ������  �ִ� <input type=text name=max[] size=10 value="<?=$max[0]?>" option="regNum" label="�����ݻ���ѵ�" onkeydown="onlynumber()" class=rline> ������ �������� ����� �� �ֽ��ϴ�.<span class=extext> (�����Է�)</span><br>
	<input type=radio name=k_max value=1 class=null <?=$checked['k_max'][1]?>> ������ �ִ� <select name="emoney_use_range"><option value="0" <?=$selected['emoney_use_range'][0]?>>��  ǰ  ��  ��</option><option value="1" <?=$selected['emoney_use_range'][1]?>>��ǰ�հ�+��ۺ�</option></select>�� <input type=text name=max[] size=3 value="<?=substr($max[1],0,-1)?>" option="regNum" label="�����ݻ���ѵ�" onkeydown="onlynumber()" class=rline> % ���� �������� ����� �� �ֽ��ϴ�. <span class=extext>(�����Է�)</span>
<div class="extext_t">* ������ ������ �ּ� ���ݾװ� �ִ� ���ݾ��� ���մϴ�. ���������� ������ �����ϰ� �Ϸ��� 100%�� �Է��ϼ���. <br>
* �ִ��ѵ����� %�� �� ��� �ּ��ѵ��װ��� ������踦 ����Ͽ� �����ϰ� �����ϼ���. <br>
��) ������ ���� �ּ��ѵ����� 10,000������ �ϰ� �ִ��ѵ����� ��ǰ������ 40%�� �������� ��, ������ ��ǰ�� 20,000���̶�� ���������� ������ �� �ִ� �ִ��ѵ���(40%)�� 8,000���� �˴ϴ�. <br>
�� ��� �ּ��ѵ���(10,000��)���� �ִ��ѵ���(8,000��)�� ���� �ǹǷ� ���� �������� ����� �� ���� ��Ȳ�� �߻��˴ϴ�. <br>
���� ������ ������ ������ ������ �ּ��ѵ��װ� �ִ��ѵ����� ������踦 ����Ͽ� �����Ͻñ� �ٶ��ϴ�. </div>
	</td>
</tr>

<tr height=50>
	<td>������ ������</td>
	<td>
	<div><input type=radio name="emoney_delivery" value="0" class=null <?=$checked['emoney_delivery'][0]?>> ���������� �ֹ��� �����ݾ׿� ������ �ֹ��ݾ� ����</div>
	<div><input type=radio name="emoney_delivery" value="1" class=null <?=$checked['emoney_delivery'][1]?>> ���������� �ֹ��� �����ݾ׿� ������ �ֹ��ݾ� ������</div>
<div class="extext_t">* �����ݻ������� "���������� �ֹ��� �����ݾ׿� ������ �ֹ��ݾ� ������"���� ���� �� ��ۺ� ���� �������� �����ݾ׿� ���Ե��� �ʽ��ϴ�. <br>
��) ��ۺ� ��å�� �����ݾ� 50,000���̻� ����, �̸��� ��쿡�� 2,500���� ��ۺ�ΰ��� �����Ͽ���, �����ݻ������� ���������� �ֹ��� �����ݾ׿� ������ �ֹ��ݾ� ���������� �����Ͽ���, �ֹ� �ѱ��űݾ��� 51,000���̰� ������ 2,000���� ����Ͽ��� ���
�ǰ����ݾ��� 49,000���̸� �����ݾ׿� �������� �����ԵǹǷ� ��ۺ� �ΰ��˴ϴ�.</div>
	</td>
</tr>
</table>

<div class=title>������/���� �ߺ���� ����<span>��ǰ �ֹ��ÿ� �����ݰ� ���� �ߺ���� ���θ� �����մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=4')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>���� �����<br>������ �ߺ� ���</td>
	<td class=noline>
	<div><label><input type="radio" name="useduplicate" value="1" <?=$checked['useduplicate'][1]?>> ���� ����� ������ �ߺ���� ����</label></div>
	<div><label><input type="radio" name="useduplicate" value="0" <?=$checked['useduplicate'][0]?>> ���� ����� ������ ��� �Ұ�</label></div>
	</td>
</tr>
</table>

<div class=title>�ݾ��������<span>�ݾ���������� ������, ���������� �������� �߻��Ǵ� �����ݾ� ���ڸ� ������ �����ϱ� �����Դϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=4')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>������� ����</td>
	<td>
		<select name="cut">
			<option value="0">�������</option>
			<option value="1" <?php echo $selected['cut']['1']; ?>>1�� ���� ����</option>
			<option value="2" <?php echo $selected['cut']['2']; ?>>10�� ���� ����</option>
			<option value="3" <?php echo $selected['cut']['3']; ?>>100�� ���� ����</option>
			<option value="4" <?php echo $selected['cut']['4']; ?>>1000�� ���� ����</option>
		</select>
		<p class="extext">
			�Ǹűݾ��� %������ ������ ������ �߻��ϴ� 1������ �� 10������ �ݾ��� �����Ͽ� �����մϴ�.<br/>
			Ex) �Ǹűݾ� 1,700���� 7% ���� ? ������ 119�� �߻�<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;=> 1�� ���� �����110�� ������ ���� / 10�� ���� ����� 100�� ������ ����<br/>
		</p>
		<p class="extext" style="color: #ff0000;">
			�� ����� �����ݰ� �������� �ݾ��� %�� �����ÿ��� ���� �˴ϴ�.
		</p>
	</td>
</tr>
</table>

<div class=button>
<input type=image src="../img/btn_register.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>


<div id=MSG03>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��������å�� <font class=small_ex_point>��밡���� �ݾ�, ����ѵ�����</font>�� <font class=small_ex_point>'�̿�ȳ�������'</font>�� �����Ͻñ� �ٶ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font class=small_ex_point>�ݾ��������</font>�� ������, ���������� �������� �߻��Ǵ� �����ݾ� ���ڸ� ������ �����ϱ� �����Դϴ�.</td></tr></table>
</div>
<script>cssRound('MSG03');chkGoodsEmoney();</script>


<? include "../_footer.php"; ?>