<?
$location = "��ũ�����̽� ���� > ��õƮ����Ʈ����";
@include "../_header.php";
@include "../../conf/merchant.php";
?>
<div class="title title_top">��õƮ����Ʈ ����</div>
<form name=form method=post action="indb.php">
<input type=hidden name=mode value="merchant">
<table class=tb>
	<tr class=rndbg>
		<td align=center><b>��뿩��</b></td>
		<td align=center><b>��õƮ����Ʈ</b></td>
		<td align=center><b>����</b></td>
	</tr>

	<tr class=cellL>

		<td align=center><input type=checkbox class=null name=linkprice[chk] value='1' <?=($linkprice[chk])?"checked":""?>></td>
		<td style="padding-left:10" align=center><a href="http://www.linkprice.com/home/linkpricehome.htm" target="_blank"><font color=444444>��ũ�����̽�</a></td>
		<td align=left style=padding-left:5>
			<table>
				<tr>
					<td align=center>Sid</td>
					<td><input  type=text name="linkprice[sid]" size=50  value='<?=$linkprice['sid']?>'></td>
				</tr>
				<tr>
					<td align=center>Code</td>
					<td><input  type=text name="linkprice[code]" size=50  value='<?=$linkprice['code']?>'></td>
				</tr>
				<tr>
					<td align=center>Pad</td>
					<td><input  type=text name="linkprice[pad]" size=50  value='<?=$linkprice['pad']?>'></td>
				</tr>
				<tr>
					<td align=center>��ũ���</td>
					<td style="padding-left:5">http://<?=$_SERVER[HTTP_HOST]?><?=$cfg[rootDir]?>/partner/lpfront.php</td>
				</tr>
				<input type=hidden name="linkprice[joburl]" size=50  value='http://service.linkprice.com/lppurchase.php' readonly></td>
				<input type=hidden name="linkprice[accurl]" size=50  value='/shop/partner/daily_fix.php' readonly>
				</tr>
			</table>
		</td>
	</tr>
</table><p>

<div class="button" align=center>
<input type=image src="../img/btn_register.gif">
</div>
</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_tip>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font color=0074BA>��ũ�����̽����� �ο�����  �������̵�� �ڵ� ��� ���� �����Ͻð� ��뿩�θ� üũ���ּ���.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font color=0074BA>���� ǥ�õ� ��ũ ��δ� ��ũ�����̽����� ���θ��� �Ѿ�ö� ��ũ�����̽��� ��Ű�� �����Ǵ� ��� �Դϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>
<? include "../_footer.php"; ?>
