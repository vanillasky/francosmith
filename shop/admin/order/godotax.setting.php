<?php
$location = "�����ڼ��ݰ�꼭 > �����ڼ��ݰ�꼭 ����";
include "../_header.php";
$config_pay = $config->load('configpay');
$config_tax = $config_pay['tax'];
$config_godotax = $config->load('godotax');

?>
<form method="post" action="../order/godotax.setting.indb.php" target="ifrmHidden" id="frmTax">
<div class="title title_top">���� ��û/����<span>����(���ڼ��ݰ�꼭) ���񽺸� ��û �� �����ϴ� ������ �Դϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=20')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>���� ��뿩��</td>
	<td class="noline">
	<input type="radio" name=useyn value='y' <?=frmChecked($config_tax['useyn'],'y')?>> ���
	<input type="radio" name=useyn value='n' <?=frmChecked($config_tax['useyn'],'n')?>> ������
	</td>
</tr>
<tr>
	<td>���� ��������</td>
	<td class=noline>
	<input type=checkbox name=use_a <?=frmChecked($config_tax['use_a'],'on')?> value="on"> �������Ա�
	<input type=checkbox name=use_c disabled> �ſ�ī��
	<input type=checkbox name=use_o <?=frmChecked($config_tax['use_o'],'on')?> value="on"> ������ü
	<input type=checkbox name=use_v <?=frmChecked($config_tax['use_v'],'on')?> value="on"> �������
	</td>
</tr>
<tr>
	<td>���� ���۴ܰ�</td>
	<td class=noline>
	<input type=radio name=step value='1' <?=frmChecked($config_tax['step'],'1')?>> �Ա�Ȯ��
	<input type=radio name=step value='2' <?=frmChecked($config_tax['step'],'2')?>> ����غ���
	<input type=radio name=step value='3' <?=frmChecked($config_tax['step'],'3')?>> �����
	<input type=radio name=step value='4' <?=frmChecked($config_tax['step'],'4')?>> ��ۿϷ�
	</td>
</tr>
</table>
<br><br>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>���� ȸ�� ID</td>
	<td>
		<input type="text" name="godotax_site_id" value="<?=$config_godotax['site_id']?>" class="line" style="width:170px" maxlength="16">
	</td>
</tr>
<tr>
	<td>���� API_KEY</td>
	<td>
		<input type="text" name="godotax_api_key" value="<?=$config_godotax['api_key']?>" class="line" style="width:300px" maxlength="32"><br>
		<div class="extext" style="padding-top: 3px;">
		 ���� Ȩ���������� �α��� ��, �α��ιڽ��� �ִ� [API KEY] ��ư�� Ŭ���ϸ� Ȯ���� �� �ֽ��ϴ�. <br>
		 API KEY ���� �����Ͽ�, �����Ͻø� �˴ϴ�.
		</div>
	</td>
</tr>

</table>

<div style="position:relative;">
	<div class=button >
	<input type=image src="../img/btn_save.gif">
	</div>
	<a href="http://www.godobill.com" target="_blank" style="display:block;position:absolute;right:10px;top:0px"><img src="../img/btn_godobill_go2.gif"></a>
</div>

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� �������ǿ��� �ſ�ī���� ��쿡�� ���ݰ�꼭 �߱��� �Ұ����մϴ�.
�ſ�ī�� ������ǥ�� ���ݰ�꼭 ������� ����ϸ� �˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� ȸ��ID, ���� API_KEY �� ������ ȸ�������� �ϸ� �߱��� �Ǵ� �����Դϴ�. ���� Ȩ���������� Ȯ�� �� �Է��Ͻø� �˴ϴ�.
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<? include "../_footer.php"; ?>