<?

$location = "���ڼ��ݰ�꼭 ���� > ���ڼ��ݰ�꼭 ����";
include "../_header.php";
include "../../conf/config.pay.php";

$set = $set['tax'];

$checked['useyn'][$set[useyn]] = "checked";
$checked['step'][$set[step]] = "checked";

$checked['use_a'][$set[use_a]] = "checked";
$checked['use_c'][$set[use_c]] = "checked";
$checked['use_o'][$set[use_o]] = "checked";
$checked['use_v'][$set[use_v]] = "checked";

?>

<form method=post action="../order/tax_indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="tax">

<div class="title title_top">���ڼ��ݰ�꼭����<span>ȸ������ ����Ǵ� ���ڼ��ݰ�꼭�� ���� ��å�Դϴ�</span></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>���� ��뿩��</td>
	<td class=noline>
	<input type=radio name=useyn value='y' <?=$checked['useyn']['y']?>> ���
	<input type=radio name=useyn value='n' <?=$checked['useyn']['n']?>> ������
	</td>
</tr>
<tr>
	<td>���� ��������</td>
	<td class=noline>
	<input type=checkbox name=use_a <?=$checked['use_a']['on']?>> �������Ա�
	<input type=checkbox name=use_c <?=$checked['use_c']['on']?> disabled> �ſ�ī��
	<input type=checkbox name=use_o <?=$checked['use_o']['on']?>> ������ü
	<input type=checkbox name=use_v <?=$checked['use_v']['on']?>> �������
	</td>
</tr>
<tr>
	<td>���� ���۴ܰ�</td>
	<td class=noline>
	<input type=radio name=step value='1' <?=$checked['step']['1']?>> �Ա�Ȯ��
	<input type=radio name=step value='2' <?=$checked['step']['2']?>> ����غ���
	<input type=radio name=step value='3' <?=$checked['step']['3']?>> �����
	<input type=radio name=step value='4' <?=$checked['step']['4']?>> ��ۿϷ�
	</td>
</tr>
<tr height=30>
	<td class=ver81>���� �ܿ� ����Ʈ</td>
	<td class=noline style="padding-left:15">
	<font size=4><b style="color:#FF6600"><?=number_format($godo[tax])?></b></font> point
	<a href="../order/etax.pay.php"><img src="../img/btn_addsms.gif" border=0 align=absmiddle hspace=2></a>
	<font class=extext>(���ڼ��ݰ�꼭�� �����Ϸ��� ����Ʈ ���� �� ����ؾ� �մϴ�)</font>
	</td>
</tr>
</table>

<div class=button>
<input type=image src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>


<div id=MSG01>
<table cellpadding=0 cellspacing=0 border=0 class=small_ex style="line-height:14px;">
<tr><td>
<dl style="margin:0;">
<dt><img src="../img/icon_list.gif" align="absmiddle">�ſ�ī�� �����ֹ��� ���ݰ�꼭�� �������� �ʽ��ϴ�.</dt>
<dd style="margin-left:8px;">2004�� ������ �ΰ���ġ������ ���ϸ�, 2004.7.1 ���� �ſ�ī��� ������ �ǿ� ���ؼ��� ���� ��꼭 ������ �Ұ��ϸ� �ſ�ī�� ������ǥ�� �ΰ���ġ�� �Ű� �ϼž� �մϴ�.<br>
[ �ΰ���ġ���� ����� 57�� ���ù��� ���� ]</dd>
</dl>
</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ڼ��ݰ�꼭 ������ �ȳ�</font>
<ol type="a" style="margin:0px 0px 0px 40px;">
<li>������ ���� ���ݰ�꼭�� ���ڹ���ȭ�Ͽ� ���ͳ��� ���� ������ ��Ȯ�ϰ� �����ϰ� ������ �� �ְ� �ϴ� ���� �����Դϴ�.</li>
<li>�ΰ���ġ����, ����û ��ÿ� �ǰ��Ͽ� ������������ ������� �������� ���ڼ����� �� ������ ���� ���·� ���ͳ��� ���� ó���մϴ�.<br>
[ �ΰ���ġ���� ����� 53���� 79��, ����û ��� ��2001-4ȣ ���� ]</li>
<li>���ݰ�꼭 ���� �� ���� ������ �ҿ�Ǵ� �ð��� ����� ���� ������Ű�� ����(5�Ⱓ)�� �����Ű� ������ ������ �����մϴ�.</li>
<li>������ ���Ͽ� ���ڼ��ݰ�꼭�� �����ϸ� ���� ������������ �������� �ʾƵ� �˴ϴ�. ���������� ���� ������������ �����մϴ�.</li>
<li>���ڼ��ݰ�꼭 ���༭�񽺴� ����Ʈ���������� ����Ʈ�� �־�߸� ������ �����մϴ�.</li>
<li>����Ʈ�� �����Ͻ÷��� ���ڼ��ݰ�꼭�� �����ϴ� LG������ ���ý�21�� ���� �����ϼž� �մϴ�.</li>
</ol>
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>