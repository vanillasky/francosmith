<?
$location = '���ž��� ���� > ���θ� �������� ����';
include '../_header.php';
include '../../lib/lib.func.egg.php';

$egg = getEggConf();
$checked['displayEgg'][$cfg['displayEgg']+0] = 'checked';
if ($egg['use'] != 'Y') $disabled['displayEgg'] = 'disabled';
if ($egg['use'] != 'Y' || $egg['scope'] != 'P') $disabled['min'] = 'disabled';
?>

<div class="title title_top">���θ� �������� ���� <span>���θ� �������� ��û �� �����Ȳ�� Ȯ���մϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=12')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

<script id=script name=script src="http://www.godo.co.kr/userinterface/_usafe/progress.js.php?godosno=<?=$godo[sno]?>&hashdata=<?=md5($godo[sno])?>&u_id=<?=md5($egg[usafeid])?>"></script>

<div id="request" style="display:none;"><img src="../img/btn_usafe.gif" onclick="popup('http://www.godo.co.kr/service/surety_insurance_regist.php?mode=remoteGodoPage&godosno=<?=$godo[sno]?>&hashdata=<?=md5($godo[sno])?>',770,800);" border="0" style="cursor:pointer;"></div>
<script language="javascript"><!--
if (typeof(usafeStep) != "undefined"){
	if (usafeStep == '' || (usafeStep != '0' && usafeStep != '1' && usafeStep != '3' && usafeStep != '4')){
		document.getElementById('request').style.display = 'block';
		document.getElementById('request').style.margin = '20px 20px 0 320px';
	}
}
--></script>


<!-- ���θ� �������� ���� : Start -->
<div style="padding-top:20px"></div>

<div class="title title_top">���θ� �������� ���� &nbsp; <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=12',890,800)"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<form method=post action="egg.indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="displayEgg">
<input type="hidden" name="min" value="<?=$egg['min']?>" <?=$disabled['min']?>>


<table class=tb>
<col class=cellC><col class=cellL>
<tr height=30>
	<td>���� ���� ǥ�� ����</td>
	<td class=noline>
	<input type=radio name=cfg[displayEgg] value=0 <?=$checked['displayEgg'][0]?> <?=$disabled['displayEgg']?>> �����ϴܰ� �������� �������������� ǥ��
	<input type=radio name=cfg[displayEgg] value=1 <?=$checked['displayEgg'][1]?> <?=$disabled['displayEgg']?>> ��ü�������� ǥ��
	<input type=radio name=cfg[displayEgg] value=2 <?=$checked['displayEgg'][2]?> <?=$disabled['displayEgg']?>> ǥ������ ����
	</td>
</tr>
</table>
<div class=button>
<input type=image src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>
</form>
<!-- ���θ� �������� ���� : End -->


<!-- ���ž��� ���� ǥ�� ���� ��� �ȳ� : Start -->
<div style="padding-top:20px"></div>

<div class="title title_top">���ž��� ���� ǥ�� ���� ��� �ȳ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=12')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<tr><td>
<table cellpadding=15 cellspacing=0 border=0 bgcolor=white width=100%>
<tr><td>
<div style="padding:0 0 5 0">* ���ž������� ǥ�� ������ (�Һ������غ����� ���� ������ ���ž���ǥ�ø� üũ�ϰ�, �Ʒ� ǥ������ ���� �ݿ��ϼ���)</font></div>
<table width=100% height=100 class=tb style='border:1px solid #cccccc;' bgcolor=white>
<tr>
<td width=30% style='border:1px solid #cccccc;padding-left:20'>�� [���������� �ϴ�] ǥ����</td>
<td align=center rowspan=2 style='border:1px solid #cccccc;padding:0 10 0 10'><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=10')"><img src="../img/icon_sample.gif" align=absmiddle></a></td>
<td width=70% style='border:1px solid #cccccc;padding-left:40'><font class=extext><a href='../design/codi.php?design_file=outline/footer/standard.htm' target=_blank><font class=extext><b>[�����ΰ��� > ��ü���̾ƿ� ������ > �ϴܵ����� > html�ҽ� ��������]</b></font></a> �� ����<br> ġȯ�ڵ� <font class=ver8 color=000000><b>{=displayEggBanner()}</b></font> �� �����ϼ���. <a href='../design/codi.php?design_file=outline/footer/standard.htm' target=_blank><font class=extext_l>[�ٷΰ���]</font></a></font></td>
</tr>
<tr>
<td width=30% style='border:1px solid #cccccc;padding-left:20'>�� [�������� ����������] ǥ����</td>
<td width=70% style='border:1px solid #cccccc;padding-left:40'>
<a href='../design/codi.php?design_file=order/order.htm' target=_blank><font class=extext><font class=extext_l>[�����ΰ��� > ��Ÿ������ ������ > �ֹ��ϱ� > order.htm]</font></a> �� ����<br> ġȯ�ڵ� <font class=ver8 color=000000><b>{=displayEggBanner(1)}</b></font> �� �����ϼ���. <a href='../design/codi.php?design_file=order/order.htm' target=_blank><font class=extext_l>[�ٷΰ���]</font></a></font></td>
</tr>
</table>
</td></tr>
</table>

<div style="padding-top:15"></div>

<table cellpadding=1 cellspacing=0 border=0 class=small_tip>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font class=extext><b>���ž������� ���� ǥ�� �ǹ�ȭ �ȳ� (2007�� 9�� 1�� ����)</b></font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class=extext>�� ǥ�á����� �Ǵ� ������ ��ġ�� ���̹��� �ʱ�ȭ��� �Һ����� �������� ����ȭ�� �� ������ ����.</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>- ���̹��� �ʱ�ȭ�� ��� ��10����1���� ������� �ſ� �� ǥ����� ����κ��� �ٷ� ���� �Ǵ� ������ ���ž������� ���� ������ ǥ���ϵ��� ��.</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>- �Һ��ڰ� ��Ȯ�� ���ظ� �������� ���ž������� �̿��� ������ �� �ֵ���, �������� ���úκ��� �ٷ� ���� ���ž������� ���û����� �˱� ���� �����Ͽ���  ��.</font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class=extext>�� ǥ�á����� �Ǵ� ���� �������� ������ �� ������ ������.</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>- ���� ������ ���ں��� �ּ� �ݾ� �̻� ������ �Һ��ڰ� ���ž��������� �̿��� ������ �� �ִٴ� ����</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>- ����Ǹž��� �ڽ��� ������ ���ž��������� ��������ڸ� �Ǵ� ��ȣ</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>- �Һ��ڰ� ���ž������� ���Ի���� ������ Ȯ�� �Ǵ� ��ȸ�� �� �ִٴ� ����</font></td></tr>
<tr><td height=10></td></tr>
</table>

<table cellpadding=1 cellspacing=0 border=0 class=small_tip>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font class=extext><b>���ž������� �ǹ� ���� Ȯ�� (2013�� 11�� 29�� ����)</b></font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class=extext>�� ���� ����</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>5���� ���� �ŷ��� ���ؼ��� �Һ����� ������ ��ȣ�ϱ� ���Ͽ� ���ž������� �ǹ� ���� ��� Ȯ�� <br/>1ȸ ���� ����, 5���� �̻� �� 5���� ������ �Ҿ� �ŷ�(��� �ݾ�)
</font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class=extext>�� ���� ����</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>���ڻ�ŷ� ����� �Һ��ں�ȣ�� ���� ���� <br/>[ ���� ��11841ȣ, ������: 2013.5.28, �Ϻ� ���� ]</font></td></tr>
<tr><td height=10></td></tr>
</table>
</td></tr></table>
<!-- ���ž��� ���� ǥ�� ���� ��� �ȳ� : End -->


<!-- ��3�� �������� ���� : Start -->
<div style="padding-top:20px"></div>

<div class="title title_top">��3�� �������� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=36')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<div style="padding:0 10px 20px 10px;">
	2012�� 8�� 18�Ϻη� ����Ǵ� ������Ÿ��� ��23�� 2�� "�ֹε�Ϲ�ȣ �������� �� ��������"�� ���� �� �� �������� ������������<br>
	�߱޽� ���Ǵ� '�ֹε�Ϲ�ȣ'�� �������� '�������'�� '����'�� ��ü�˴ϴ�.<br>
	���� ���ں����� ��û�ϼ̰ų� ��û�� �ֽô� ��ڲ����� �ݵ�� ����� ���θ��� "�������� ����/���� �� ��3�� �������� ����"<br>
	�׸��� �����Ͽ� �̿������ �Ͽ��� '������'�� �޾ƾ� �մϴ�.
</div>
<!-- ��3�� �������� ���� : End -->


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>
<dl style="margin:0;">
<dt><img src="../img/icon_list.gif" align="absmiddle">�������� ID :  ���� ������ ���� �������� ���� ID�Դϴ�. ���� ������ �ڵ����� ����˴ϴ�.</dt>
</dl>
<dl style="margin:5px 0 0 0;">
<dt><img src="../img/icon_list.gif" align="absmiddle">�������� :</dt>
<dd style="margin-left:8px;">
	<ol style="list-style-type:none; margin:0; padding:0;">
	<li>�� ��ü����(������ �߱�) - ��������(ī��, ����) �� �ݾ� ������� ������ ������ ����(�Һ��� ���þ���)</li>
	<li>�� �κк���(�� �ǹ�ȭ ����) - ���ں��� �ּ� �ݾ� �̻����� ���� �ֹ��� ��� �Һ��� ���� ������ ����</li>
	</ul>
</dd>
</dl>
<dl style="margin:5px 0 0 0;">
<dt><img src="../img/icon_list.gif" align="absmiddle">�������� �߱��� ���� ������ ������ �ȳ�</dt>
<dd style="margin-left:8px;">
	<ol style="list-style-type:none; margin:0; padding:0;">
	<li>�� ������ �Ǹ��� �δ� - �� �����ݾ��� 0.535%�� �Ǹ��ڰ� �δ��ϰ� �˴ϴ�.</li>
	<li>�� ������ �Һ��� �δ� - �� �����ݾ��� 0.535%�� �����ڰ� �δ��ϰ� �˴ϴ�.</li>
	</ul>
</dd>
</dl>
<dl style="margin:5px 0 0 0;">
<dt><img src="../img/icon_list.gif" align="absmiddle">����ó�� �ȳ�</dt>
<dd style="margin-left:8px;">
	<ol style="list-style-type:none; margin:0; padding:0;">
	<li>�� �������߱�
		<ul>
		<li>�������Ա�, �ſ�ī��, ������ü, ������� ��� �߱� �����մϴ�. (�ڵ������� ����)</li>
		<li>PG�� ����(�ſ�ī��/������ü/�������) ��쿡�� ���������� ���εǸ� �߱޵˴ϴ�.</li>
		<li>PG�� ������ �����ε�, �������߱��� �������� ��쿡�� '�ֹ������ȸ'���� ��߱� ��û�� �� �ֽ��ϴ�.</li>
		</ul>
	</li>
	<li>�� ���������/�Ա�Ȯ�� - �������� ����Ʈ�� �����մϴ�.</li>
	</ol>
</dd>
</dl>
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>