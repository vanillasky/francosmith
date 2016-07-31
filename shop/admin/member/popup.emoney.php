<?

include "../_header.popup.php";
include "./_header.crm.php";
include "../../lib/page.class.php";

list ($name, $emoney) = $db->fetch("select name, emoney from ".GD_MEMBER." where m_no='$_GET[m_no]'"); # 현재 적립금

list ($total) = $db->fetch("select count(*) from ".GD_LOG_EMONEY." where m_no='$_GET[m_no]'"); # 총 레코드수

### 목록
$pg = new Page($_GET[page],10);
$db_table = "".GD_LOG_EMONEY."";
$pg->field = "*, date_format( regdt, '%Y.%m.%d' ) as regdts"; # 필드 쿼리
$where[] = "m_no='$_GET[m_no]'";
$pg->setQuery($db_table,$where,$orderby="regdt desc");
$pg->exec();

$res = $db->query($pg->query);
?>

<div style="margin-bottom:10px;">


<div class="title title_top">현재 적립금현황<span>현재의 적립금현황을 확인합니다</span></div>


<table cellpadding=0 cellspacing=1 border=0 bgcolor=EBEBEB>
<tr><td bgcolor=E8E8E8>
<table cellpadding=3 cellspacing=0 border=0 bgcolor=E8E8E8>
<tr><td bgcolor=F6F6F6 width=160 align=center>현재 <b><?=$name?></b>님의 적립금은</td>
<td bgcolor=white width=400>&nbsp;&nbsp;<b><?=number_format($emoney)?></b>원 입니다</td></tr></table>
</td></tr></table>

<div style="padding-top:20"></div>
<div class="title title_top">적립금지급/차감<span>적립금을 지급/차감합니다</span></div>

<form name=frmMember method=post action="../member/indb.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="emoney_add">
<input type=hidden name=m_no value="<?=$_GET[m_no]?>">

<table cellpadding=0 cellspacing=0 border=0 bgcolor=EBEBEB>
<tr><td bgcolor=E8E8E8>
<table cellpadding=2 cellspacing=1 border=0 bgcolor=E8E8E8>
<tr><td bgcolor=F6F6F6 width=160 align=center>지급액/차감액</td>
<td bgcolor=white width=400><input type=text name=emoney size=8 class="rline" required label="적립금"> 원 <font class=small color=444444>(차감시 마이너스금액으로 입력. 예) -200 )</font></td></tr>
<tr><td bgcolor=F6F6F6 align=center>이유</td>
<td bgcolor=white>
<select name="memo" required label="지급이유" onchange="openLayer('direct', (this.value=='direct' ? 'block' : 'none') )" style="float:left;">
		<option value="">- 선택하세요 -</option>
	<?
	foreach( codeitem('point') as $v ){
		echo '<option value="' . $v . '">' . $v . '</option>' . "\n";
	}
	?>
		<option value="direct">☞ 직접입력</option>
		</select>
		<div id="direct" style="display:none;"><input type=text name=direct_memo size=30 class="line"></div></td></tr>

</table>
</td></tr></table>

<div style="margin-bottom:10px;padding-top:10;" class=noline align=center>
<input type="image" src="../img/btn_confirm_s.gif">
</div>

</form>


<div class="title title_top">적립금내역<span>적립금 상세내역을 확인합니다</span></div>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan="6"></td></tr>
<tr class=rndbg>
	<th>번호</th>
	<th>날짜</th>
	<th>적립금액</th>
	<th>지급/차감이유</th>
	<th>주문번호</th>
	<th>삭제</th>
</tr>
<tr><td class=rnd colspan="6"></td></tr>
<col width=50 align=center>
<col width=60 align=center>
<col width=80 align=center>
<col align=left>
<col width=90 align=center>
<col width=40 align=center>
<?
while ($data=$db->fetch($res)){
?>
<tr><td height=4  colspan="6"></td></tr>
<tr height=25 align="center">
	<td><font class=ver81 color=616161><?=$pg->idx--?></td>
	<td><font class=ver81 color=616161><?=$data[regdts]?></td>
	<td><font class=ver81 color=0074BA><b><?=number_format($data[emoney])?></b></font>원</td>
	<td><font class=ver81 color=333333><?=$data[memo]?></td>
	<td><a href="javascript:popup('../order/popup.order.php?ordno=<?=$data['ordno']?>',800,600)"><font class="ver7" color="0074BA"><?=$data[ordno]?></font></a></td>
	<td align=center><a href="../member/indb.php?mode=emoney_delete&sno=<?=$data[sno]?>"><img src="../img/i_del.gif"></a></td>
</tr>
<tr><td height=4  colspan="6"></td></tr>
<tr><td colspan="6" class=rndline></td></tr>
<? } ?>
</table>

<div class="pageNavi center"><font class=ver8><?=$pg->page[navi]?></font></div>
</div>

<script>table_design_load();</script>
<?include "./_footer.crm.php";?>