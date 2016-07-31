<?

$location = "기본관리 > 무통장결제 / 은행계좌 설정";
include "../_header.php";
include "../../conf/config.pay.php";
?>

<div class="title title_top">무통장결제<span>무통장결제의 사용여부를 선택하세요 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=5')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<form method=post action="indb.php">
<table class=tb>
<input type="hidden" name="mode" value="bank">
<col class=cellC><col class=cellL>
<tr height=30>
	<td>무통장결제 사용여부</td>
	<td class=noline>
	<input type=radio name="set[use][a]" value='on' <?if($set['use']['a'])echo"checked";?>> 사용
	<input type=radio name="set[use][a]" value='' <?if(!$set['use']['a'])echo"checked";?>> 사용안함
	</td>
</tr>
</table>
<div class=button align=center><input type=image src="../img/btn_save.gif"></div>
</form>

<div style="padding-top:20"></div>


<div class="title title_top">은행계좌 설정<span>무통장결제에 사용할 은행계좌를 등록하시기 바랍니다 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=5')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
<td class=rnd colspan=12></td>
</tr>
<tr class=rndbg>
<th>번호</th>
<th>은행명</th>
<th>계좌번호</th>
<th>예금주</th>
<th>수정</th>
<th>삭제</th>
</tr>
<tr>
<td class=rnd colspan=12></td>
</tr>
<?
$res = $db->query("select * from ".GD_LIST_BANK." where useyn='y'");
while ($data=$db->fetch($res)){ 
?>
<tr>
<td height=4 colspan=12></td>
</tr>
<tr class="center">
	<td width="50" class="ver8"><?=++$idx?></td>
	<td class="ver81"><?=$data[bank]?></td>
	<td class="ver8"><?=$data[account]?></td>
	<td class="ver81"><?=$data[name]?></td>
	<td width="50"><a href="javascript:popupLayer('popup.bank.php?mode=modBank&sno=<?=$data[sno]?>',500,300)"><img src="../img/i_edit.gif" border=0></a></td>
	<td width="50"><a href="indb.php?mode=delBank&sno=<?=$data[sno]?>" onclick="return confirm('정말로 삭제하시겠습니까?')"><img src="../img/i_del.gif" border=0></a></td>
</tr>
<tr>
<td height=4></td>
</tr>
<tr>
<td colspan=12 class=rndline></td>
</tr>
<? } ?>
</table>

<div class=pdv10 align=center style="padding-right:5"><a href="javascript:popupLayer('popup.bank.php?mode=addBank',500,300)"><img src="../img/btn_addbank.gif" border=0></a>
</div>

<div style="padding-top:20px"></div>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">결제은행은 고객이 무통장입금시 필요한 입금은행계좌정보입니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>