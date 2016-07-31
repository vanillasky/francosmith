<?
$location = "링크프라이스 입점 > 머천트사이트설정";
@include "../_header.php";
@include "../../conf/merchant.php";
?>
<div class="title title_top">머천트사이트 설정</div>
<form name=form method=post action="indb.php">
<input type=hidden name=mode value="merchant">
<table class=tb>
	<tr class=rndbg>
		<td align=center><b>사용여부</b></td>
		<td align=center><b>머천트사이트</b></td>
		<td align=center><b>설정</b></td>
	</tr>

	<tr class=cellL>

		<td align=center><input type=checkbox class=null name=linkprice[chk] value='1' <?=($linkprice[chk])?"checked":""?>></td>
		<td style="padding-left:10" align=center><a href="http://www.linkprice.com/home/linkpricehome.htm" target="_blank"><font color=444444>링크프라이스</a></td>
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
					<td align=center>링크경로</td>
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
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font color=0074BA>링크프라이스에서 부여받은  상점아이디와 코드 페드 값을 설정하시고 사용여부를 체크해주세요.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font color=0074BA>위에 표시된 링크 경로는 링크프라이스에서 쇼핑몰로 넘어올때 링크프라이스의 쿠키가 생성되는 경로 입니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>
<? include "../_footer.php"; ?>
