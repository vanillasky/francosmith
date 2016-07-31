<?

include "../_header.popup.php";
include "../../lib/page.class.php";

### 주문
$pg = new Page($_GET[page],5);
$pg->field = "a.ordno, a.orddt, a.settleprice";
$db_table = "".GD_ORDER." a left join ".GD_MEMBER." b on a.m_no=b.m_no";

$where[] = "a.m_no='$_GET[m_no]'";

$pg->setQuery($db_table,$where,$sort="ordno desc");
$pg->exec();

$res = $db->query($pg->query);
?>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width=100% height=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td height=260 style="border:10px solid #000000" valign=top>

	<table height=100% width=95% cellpadding=0 cellspacing=0 border=0 align=center>
	<tr>
		<td height=100% valign=top>

		<TABLE cellpadding=3 cellspacing=0 border=0>
		<tr><td class=stxt style="padding-top:10">문의하실 주문번호를 선택하세요.</td></tr>
		</TABLE>

		<table width=100% cellpadding=0 cellspacing=0 border=0 style="margin-top:10px;">
		<col width=20%>
		<col width=12%>
		<col width=36%>
		<col width=10%>
		<col width=15%>
		<col width=7%>
		<tr height=19 bgcolor="#3F3F3F">
			<th style="font:bold 8pt 돋움; color:#FFFFFF">주문번호</th>
			<th style="font:bold 8pt 돋움; color:#FFFFFF">주문일자</th>
			<th style="font:bold 8pt 돋움; color:#FFFFFF">상품명</th>
			<th style="font:bold 8pt 돋움; color:#FFFFFF">상품수</th>
			<th align=right style="font:bold 8pt 돋움; color:#FFFFFF">주문금액</th>
			<th style="font:bold 8pt 돋움; color:#FFFFFF">선택</th>
		</tr>
<?
while ($data=$db->fetch($res)){

	$data['idx'] = $pg->idx--;

	list( $data[cnt], $data[ea] ) = $db->fetch( "select count(ea), sum(ea) from ".GD_ORDER_ITEM." where ordno = '$data[ordno]' limit 1" );

	list( $data[goodsnm] ) = $db->fetch( "select goodsnm from ".GD_ORDER_ITEM." where ordno = '$data[ordno]' limit 1" );
	if ( $data[cnt] > 1 ) $data[goodsnm] = strcut( $data[goodsnm], 22 ) . ' 외 ' . ( $data[cnt] - 1 ) . '건';
	else $data[goodsnm] = strcut( $data[goodsnm], 28 );

	$data['orddt'] = substr($data['orddt'],2,8);
	?>
		<tr <?if($data['idx']%2){?>bgcolor="#f7f7f7"<?}?> height=25 align=center>
			<td><font class=ver81><?=$data['ordno']?></td>
			<td><font class=ver81><?=$data['orddt']?></td>
			<td><font class=ver81><?=$data['goodsnm']?></td>
			<td><font class=ver8><?=number_format($data['ea'])?></td>
			<td align=right><font class=ver8><?=number_format($data['settleprice'])?>원</td>
			<td><input type="radio" name="" onclick="parent.order_put('<?=$data['ordno']?>')"></td>
		</tr>
		<tr><td colspan=6 height=1 bgcolor="E5E5E5"></td></tr>
<?}?>
		</table>

		<div style="padding:10px" align=center><?=$pg->page[navi]?></div>

		</td>
	</tr>
	<TR>
		<TD height="19" align=right><A HREF="javascript:parent.order_close()" onFocus="blur()"><strong>close</strong></A></TD>
	</TR>
	</table>

	</td>
</tr>
</table>
</body>