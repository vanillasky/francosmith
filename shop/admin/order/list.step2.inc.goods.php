	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<col width="35"><col width="35"><col width="40"><col><col width="160"><col width="65"><col width="65"><col width="65">
	<tr><td class="rnd" colspan="20"></td></tr>
	<tr class="rndbg">
		<th>번호</th>
		<th></th>
		<th colspan="2">상품명</th>
		<th>옵션</th>
		<th>가격</th>
		<th>주문수량</th>
		<th>재고</th>
	</tr>
	<tr><td class="rnd" colspan="20"></td></tr>
	<?
	$idx = $pg->idx; $pr = 1;
	for ($i=0,$m=sizeof($arList);$i<$m;$i++) {
		$data = $arList[$i];
	?>
		<tr height=25 align=center>
			<td><font class=ver8 color=616161><?=$pr*$idx--?></font></td>
			<td><img src="../img/icon_int_order_<?=$data[channel]?>.gif" align="absmiddle"/></td>
			<td><?=goodsimg($data[img_s],40,'',1)?></td>
			<td align="left"><?=$data['goodsnm']?></td>
			<td><?=implode(' / ',array_notnull(explode(' / ',$data['option'])))?></td>
			<td class=ver81><b><?=number_format($data[price])?></b></td>
			<td><span class="small1" style="color:#444444;"><?=$data['ea']?></span></td>
			<td><span class="small1" style="color:#444444"><?=$data['stock']?></span></td>
		</tr>
		<tr><td colspan=20 bgcolor=E4E4E4></td></tr>
	<?
		}
	?>
	</table>