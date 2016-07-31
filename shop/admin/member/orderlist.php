<?

include "../_header.popup.php";
include "./_header.crm.php";
include "../../lib/page.class.php";

### 회원정보
$memInfo = $db->fetch("select * from ".GD_MEMBER." where m_no='$_GET[m_no]'");

### 주문정보
$db_table = "".GD_ORDER."";

$where[] = "m_no = '$_GET[m_no]'";

$pg = new Page($_GET[page]);
$pg->setQuery($db_table,$where,"ordno desc");
$pg->exec();

$res = $db->query($pg->query);

?>

<div class="title title_top">회원 주문내역보기</div>

<table>
<col style="font-weight:bold">
<tr>
	<td>- 회원이름</td>
	<td>: <?=$memInfo[name]?> (<?=$memInfo[m_id]?>)</td>
</tr>
<tr>
	<td>- 결제금액</td>
	<td>: <font class=ver8 color=0074BA><b><?=number_format($memInfo[sum_sale])?></b></font>원 <font class=small1 color=444444>(배송완료기준)</font></td>
</tr>
</table><p>


<div style="padding-left:8"><font class=small1 color=444444>아래 주문번호를 클릭하면 주문상세내역을 볼 수 있습니다.</div>
<div style="padding-top:3"></div>

<table width=100% border=1 bordercolor=#cccccc style="border-collapse:collapse">
<tr bgcolor=#302D2A height=25>
	<th><font class=small1 color=white><b>주문번호</th>
	<th><font class=small1 color=white><b>주문금액</th>
	<th><font class=small1 color=white><b>주문일</th>
	<th><font class=small1 color=white><b>처리상태</th>
</tr>
<col align=center span=4>
<? while ($data=$db->fetch($res)){ ?>
<tr height=23>
	<td><a href="javascript:popup('../order/popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><font class=ver7 color=0074BA><b><?=$data[ordno]?></b></a></td>
	<td><font class=ver81 color=555555><b><?=number_format($data[prn_settleprice])?></td>
	<td><font class=ver7 color=777777><?=$data[orddt]?></td>
	<td style="padding-top:2px"><font class=small1 color=666666><?=$r_stepi[$data[step]][$data[step2]]?></td>
</tr>
<? } ?>
</table>

<div class="pageNavi" align=center><font class=ver7 color=444444><?=$pg->page[navi]?></div>

<?include "./_footer.crm.php";?>