<?
$location = "��ٿ� > ��ٿ����� ����";
include "../_header.php";
@include "../../conf/auctionos.php";
$aboutcoupon = $config->load('aboutcoupon');

if (!$aboutcoupon['use_aboutcoupon']) $aboutcoupon['use_aboutcoupon'] = 'N';
if (!$aboutcoupon['use_test']) $aboutcoupon['use_test'] = 'Y';

$checked['use_aboutcoupon'][$aboutcoupon['use_aboutcoupon']] = "checked";
$checked['use_test'][$aboutcoupon['use_test']] = "checked";
?>
<div class="title title_top">��ٿ����� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=21')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form name=form method=post action="indb.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="aboutcoupon">

<table class="tb" border="0">
<col class="cellC"><col class="cellL">

<tr class='noline'>
	<td>��ٿ����� ���</td>
	<td>
		<input type='radio' name='use_aboutcoupon' value='Y' <?=$checked['use_aboutcoupon']['Y']?> />about���� ���������
		<input type='radio' name='use_aboutcoupon' value='N' <?=$checked['use_aboutcoupon']['N']?> />about���� ����������
		<P>
		<div style="overflow:inline;"><span class="extext" style="padding-left:5">��ٿ� ���������� ��������� �����Ͻø�, ��ٿ����� ������ ���ؿ� ���� ��ٿ����� ���Ե� ������ �ǴܵǴ� ���, ��� ��ǰ�� ���� ��ǰ���� ������ �ڵ����� ó���˴ϴ�.</span>
		</div>
		<div style="overflow:inline;"><span class="extext" style="padding-left:5">��ٿ� �������� ����� ���� �����Ͻø�, ��� ��ǰ�� ���� �������Ѿ��� ȸ�� �ٿ�ε� ������ �ڵ��߱޵˴ϴ�.</span></div>
		<div style="overflow:inline;"><span class="extext" style="padding-left:5">��ٿ� ������ ������뿩�� ������ ���þ��� ����˴ϴ�.</span></div>
		<div style="overflow:inline;"><span class="extext" style="padding-left:5">��ٿ� ������ �������Ž� ��ǰ������ŭ ����˴ϴ�.</span></div>
		<div style="overflow:inline;"><span class="extext" style="padding-left:5">��ٿ� ������ �����, ���� �ֹ��ÿ��� �ٿ�ε��Ͽ� ��밡���մϴ�.</span></div>
	</td>
</tr>
<tr >
	<td>��������Ⱓ</td>
	<td colspan=3>
	<input type=text name=regdt[] value="<?=$aboutcoupon[startdate]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline" required> -
	<input type=text name=regdt[] value="<?=$aboutcoupon[enddate]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline" required>	
	</td>
</tr>
<tr >
	<td>�������̾� ��ġ</td>
	<td>
		���� ��ġ : <input type='input' name='left_loc' size=10  value='<?=$aboutcoupon[left_loc]?>'required >
		��� ��ǥ : <input type='input' name='top_loc' size=10 value='<?=$aboutcoupon[top_loc]?>' required >
	</td>
</tr>
<tr class='noline'>
	<td>��ٿ����� �׽�Ʈ</td>
	<td>
		<input type='radio' name='use_test' value='Y' <?=$checked['use_test']['Y']?> />about���� ���� �׽�Ʈ����<br>
		<input type='radio' name='use_test' value='N' <?=$checked['use_test']['N']?> />about���� ���� �׽�Ʈ�ƴ�
		<p>
		<div style="overflow:inline;"><span class="extext" style="padding-left:5">��ٿ� �������� �׽�Ʈ������ �����ϸ� ��ٿ��������� ���ÿ��� ������ �α��νÿ��� ��ٿ������� ����ǰ�, ����˴ϴ�.</span></div>
		<div style="overflow:inline;"><span class="extext" style="padding-left:5">�� ����� ��ٿ������� �������� ������ Ȯ���ϱ� ���� �� �Դϴ�.</span></div>
	</td>
</tr>
</table>
<p/>
<div align="center"><input type="image" src="../img/btn_naver_install.gif" align="absmiddle" border="0"></div>
</form>

<p/>

<div id="MSG01">
<table cellpadding="2" cellspacing="0" border=0 class="small_ex">
<tr><td>
<div style="padding-top:6;"></div>
<img src="../img/icon_list.gif" align="absmiddle"><span class="color_ffe">��ٿ� ���������̶�?</span><BR>
&nbsp;&nbsp;��ٿ� �������� ����� ���� �����Ͻø�, ��� ��ǰ�� ���� �������Ѿ��� ȸ�� �ٿ�ε� ������ �ڵ��߱޵˴ϴ�.<br>
&nbsp;&nbsp;��ٿ� ������ ������뿩�� ������ ���þ��� ����˴ϴ�.<br>
&nbsp;&nbsp;��ٿ� ������ �������Ž� ��ǰ������ŭ ����˴ϴ�.<br>
&nbsp;&nbsp;��ٿ� ������ �����, ���� �ֹ��ÿ��� �ٿ�ε��Ͽ� ��밡���մϴ�.<br>
<br>
<br>
<img src="../img/icon_list.gif" align="absmiddle"><span class="color_ffe"> ��ٿ� �������� �׽�Ʈ�̶�?</span><BR>
&nbsp;&nbsp;��ٿ� �������� ����� ���󿩺θ� Ȯ���ϱ� ����, ��ٿ� �������� ����� ���� ���� ��, <br>
&nbsp;&nbsp;��ٿ� �������� �׽�Ʈ������ �����ϸ�, �����ڷ� �α��� �ÿ��� ��ٿ� ������ ����ǰ� ����Ǵ� ����Դϴ�.<br>
<br>
</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>
<? include "../_footer.php"; ?>