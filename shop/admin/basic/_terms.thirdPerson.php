<!-- �������� ��3�� ���� ���� -->
<table cellpadding="0" cellspacing="0" width="1000" border="0">
<col width="100" />
<col width="900" />
<tr>
	<td class="termsTableBorder termsBorderBottomZero termsBorderRightZero termsPadding termsTdWidth100">��� ����</td>
	<td class="termsTableBorder termsBorderBottomZero termsPadding">
		<input type="radio" name="private2YN" value="Y" style="border:0px;" <?=$checked['private2YN']['Y']?>  /> �����&nbsp;&nbsp;
		<input type="radio" name="private2YN" value="N" style="border:0px;" <?=$checked['private2YN']['N']?>  /> ������
	</td>
</tr>
<tr>
	<td colspan='2'><textarea name="termsThirdPerson"><?php include $termsFilePath . 'termsThirdPerson.txt'; ?></textarea></td>
</tr>
</table>

<div style="padding:5px 0 30px 5px;">
	<span class="small" style="line-height: 150%;"> 
		- ���θ��̸��� ġȯ�ڵ�{_cfg[��shopName']}�� �����Ǿ� �⺻���� ������ ��ϵ� �����θ��̸����� �ڵ����� ǥ�õ˴ϴ�.<br />
		- <span class="termsFontWeightBold">����� ������ [ȸ������ > �������� ��3�� ���� �׸�]</span>�� ǥ�õ˴ϴ�.
	</span>
</div>

<table cellpadding="0" cellspacing="0" width="100%" border="0">
<tr>
	<td class="termsPadding">
		<span class="termsFontWeightBold termsFontColorRed">�� 2014�� 07�� 31�� ���� ���� ���� ��Ų</span>�� ����Ͻô� ��� <span class="termsFontWeightBold termsTextUnderline">�ݵ�� ��Ų��ġ�� ����</span>�ؾ� ��� ����� �����մϴ�. <a href="http://www.godo.co.kr/customer_center/patch.php?sno=2064" target="_blank" class="termsFontColorSky termsTextUnderline termsFontWeightBold">[��ġ �ٷΰ���]</a>
	</td>
</tr>
<tr>
	<td class="termsPadding">
	- ��Ų��ġ �Ŀ��� �����ΰ��� ���������� �����ϴ� ���/�������� ���� �ؽ�Ʈ(TXT)������ �� �̻� ������� �����Ƿ� ���θ� ��å�� ���� ��� �� ����������� ���� ������<br /> 
	���� �� �Է� �׸� �Է� �Ǵ� �����Ͽ� �ϼ��� �ֽñ� �ٶ��ϴ�.
	</td>
</tr>
</table>

<div class="button"><?php echo $termsButtons; ?></div>

<div id="MSG06">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr>
	<td>
		<strong>�� �������� ��3�� ���� ����</strong><br />
		<span class="termsPaddingLeft">- ���������� ��3�ڿ��� ���� ���� �ʴ� ��� �������ԡ����� üũ�� �Ͻø� �˴ϴ�.</span><br />
		<span class="termsPaddingLeft">- �������� ��3�� ������ �Էµ� ���� ������ �����Ͽ� ���� ���θ� ��� ������ �������� �����Ͽ� ����մϴ�.</span><br />
		<span class="termsPaddingLeft">- �������� �������̿뿡 ���� ���ǿʹ� ������ '����������޹�ħ ����'���� ���������� �����޴� ��,���������� �����޴� ���� �������� �̿����,</span><br />
		<span class="termsPaddingLeft"> �����ϴ� ���������� �׸�,���������� �����޴� ���� �������� ���� �� �̿�Ⱓ�� �����ϰ� ���Ǹ� �޾ƾ� �մϴ�.</span><br />
		<span class="termsPaddingLeft">- ȸ������ �������� ������, �̿��ڰ� ���Ǹ� ���� �ʾƵ� ������ �� �� �ֽ��ϴ�. ��, ���Ǹ� ���� �ʴ� ��� �̿� ���õ� ������ �̿��� �Ұ��� �ϴٴ� ������ ��õǾ�� �մϴ�. </span>
	</td>
</tr>
</table>
</div>