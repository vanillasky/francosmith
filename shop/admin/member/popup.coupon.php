<?
include "../_header.popup.php";
include "./_header.crm.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";

$query = "select * from ".GD_MEMBER." where m_no='".$_GET[m_no]."' limit 1";
$data = $db->fetch($query);

list($coupon) = $db->fetch("select sum(coupon) from ".GD_ORDER." where coupon and m_no='$_GET[m_no]' and step='4' and step2='0' ");
?>
<div class="title title_top">ȸ�� ������볻��</div>
<table>
<col style="font-weight:bold">
<tr>
	<td>- ȸ���̸�</td>
	<td>: <?=$data[name]?> (<b><?=$data[m_id]?></b>)</td>
</tr>
<tr>
	<td>- �������ݾ�</td>
	<td>: <font class=ver8><b><?=number_format($coupon)?>��</font> <font class=small1 color=444444>(��ۿϷ����)</td>
</tr>
</table><p>
<div style="padding-left:8"><font class=small1 color=444444>�Ʒ� �ֹ���ȣ�� Ŭ���ϸ� �ֹ��󼼳����� �� �� �ֽ��ϴ�.</div>
<div style="padding-top:3"></div>
<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">

<tr>
<td valign="top" align="center">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<?
$db_table = "".GD_COUPON_ORDER." a left join ".GD_ORDER." b on a.ordno=b.ordno left join ".GD_COUPON_APPLY." c on a.applysno=c.sno";
$where[] = "a.m_no='$data[m_no]'";
$where[] = "b.step2=0";
$where[] = "b.step=4";

$pg = new Page($_GET[page]);
$pg->field = "*,a.coupon,a.emoney,a.dc";
$pg->setQuery($db_table,$where,"a.regdt desc");
$pg->exec();

$res = $db->query($pg->query);

		if($db -> count_($res) > 0){
		?>


		<tr>
		<td valign="top" colspan="10">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr>
			<td valign="top">
				<table width=100% border=1 bordercolor=#cccccc style="border-collapse:collapse">
				<tr bgcolor=#302D2A height=25>
					<th><font class=small1 color=white><b>������ȣ</th>
					<th><font class=small1 color=white><b>������</th>
					<th><font class=small1 color=white><b>����/����</th>
					<th><font class=small1 color=white><b>���� �ֹ� ����</th>
					<th><font class=small1 color=white><b>�ۼ���</th>
				</tr>
				<col align=center span=5>
				<?
				while($data = $db->fetch($res)){
				list($goodsnm) = $db->fetch("select goodsnm from ".GD_ORDER_ITEM." where ordno='$data[ordno]'");
				?>
				<tr height=23>
					<td><?=$data[couponcd]?></td>
					<td><div style="text-overflow:ellipsis;overflow:hidden;width:100px" nowrap><a href="../event/coupon_register.php?couponcd=<?=$data[couponcd]?>" target="_blank"><font color=0074BA><b><?=$data[coupon]?></font></a></div></td>
					<td><font color=444444><?
					if($data[dc]){
						if(substr($data[dc],-1,1) == '%')	echo "���� ".$data[dc];
						else echo "���� ".number_format($data[dc])."��";
					}
					if($data[emoney]){
						if(substr($data[emoney],-1,1) == '%')	echo "���� ".$data[emoney];
						else echo "���� ".number_format($data[emoney])."��";
					}
					?></td>
					<td><div style="text-overflow:ellipsis;overflow:hidden;width:100px" nowrap><font class=small1 color=444444><?=$goodsnm?></div><a href="javascript:popup('../order/popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><font class=ver71 color=0074BA><?=$data[ordno]?></a></td>
					<td><font class=small1 color=444444><?=substr($data[regdt],0,10)?></font></td>
				</tr>
				<?
				}
				?>
				</table>
			</td>
			</tr>
			</table>
		</td>
		</tr>
		<tr><td colspan="15" height="1" bgcolor="#CCCCCC"></td></tr>
		<tr><td colspan="10" height="10"></tr>
		<tr><td colspan="10" align=center><div class="pageNavi" align=center><?=$pg->page[navi]?></div></td></tr>
		<tr><td colspan="10" height="10"></tr>
		<?}?>

		</table>
	</td>
	</tr>


	</table>

</td>
</tr>
</table>

<?include "./_footer.crm.php";?>