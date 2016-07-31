	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<col width="25"><col width="100"><col width="160"><col width="*"><col width="60"><col width="90"><col width="60"><col width="60"><col width="55">
	<tr><td class="rnd" colspan="20"></td></tr>
	<tr class="rndbg">
		<th><a href="javascript:void(0)" onClick="chkBoxAll()" class=white>선택</a></th>
		<th>주문일시</th>
		<th>주문번호</th>
		<th>주문상품</th>
		<th>홍보채널</th>
		<th>주문자</th>
		<th>받는분</th>
		<th>송장번호</th>
		<th>배송비</th>
	</tr>
	<tr><td class="rnd" colspan="20"></td></tr>
<?
	$arList_keys = array_keys($arList);
	for ($i=0,$m=sizeof($arList_keys);$i<$m;$i++)  {
		$ordno = $arList_keys[$i];
		$order = $arList[$ordno];

		$orderinfo = $order[0];

		// 강조색, 선택 버튼 비활성화
		if ($orderinfo['ord_status'] >= 10 OR ($orderinfo['channel'] != 'enamoo' AND $orderinfo['ord_status'] > 2)) {
			$disabled = 'disabled';
			$bgcolor = '#F0F4FF';
		}
		else {
			$disabled = '';
			$bgcolor = '#ffffff';
		}
	?>
		<tr height=25 bgcolor="<?=$bgcolor?>" bg="<?=$bgcolor?>" align=center>
			<td class=noline><input type=checkbox class="chk_ordno" name="chk[<?=$orderinfo['channel']?>][]" value="<?=$orderinfo['ordno']?>" onclick="iciSelect(this)" required label=">선택사항이 없습니다" <?=$disabled?>></td>
			<td><font class=ver81 color=616161><?=substr($orderinfo[ord_date],0,-3)?></font></td>
			<td align=left>
				<a href="view.php?ordno=<?=$orderinfo[ordno]?>"><font class=ver81 color=<?=$orderinfo['flg_inflow'] == 'sugi' ? 'ED6C0A' : '0074BA'?>><b><?=$orderinfo[ordno]?><?=$orderinfo['flg_inflow'] == 'sugi' ? '<span class="small1">(수기)</span>' : ''?></b></font></a>
				<a href="javascript:popup('popup.order.php?ordno=<?=$orderinfo[ordno]?>',800,600)"><img src="../img/btn_newwindow.gif" border=0 align=absmiddle></a>
				<? if (!empty($orderinfo[old_ordno])){	?><a href="javascript:popup('popup.order.php?ordno=<?=$orderinfo[ordno]?>',800,600)"><img src="../img/icon_twice_order.gif"></a><? } ?>
				<? if ($orderinfo[flg_escrow]=="y"){	?><a href="javascript:popup('popup.order.php?ordno=<?=$orderinfo[ordno]?>',800,600)"><img src="../img/btn_escrow.gif"></a><? } ?>
				<? if ($orderinfo[flg_egg]=="y"){		?><a href="javascript:popup('popup.order.php?ordno=<?=$orderinfo[ordno]?>',800,600)"><img src="../img/icon_guar_order.gif"></a><? } ?>
				<? if (!empty($orderinfo[flg_cashreceipt])){	?><img src="../img/icon_cash_receipt.gif"><? } ?>
				<? if ($orderinfo[flg_cashbag]=="Y"){		?><a href="javascript:popup('popup.order.php?ordno=<?=$orderinfo[ordno]?>',800,600)"><img src="../img/icon_okcashbag.gif" align=absmiddle></a><? } ?>
				<?=($orderinfo['channel'] != 'enamoo') ? '<img src="../img/icon_int_order_'.$orderinfo['channel'].'.gif" align="absmiddle">' : ''?>
			</td>
			<td align=left>
				<table width="100%" border="0">
				<?
				foreach($order as $item) {
					$ea = '';
					if ($item['cs'] == 'f' || $item['cs'] == 'y'){
						$ea = '취소';
					} else {
						$ea = $item['ea'].'개';
					}
				?>
				<tr>
					<td width="40"><?=goodsimg($item[img_s],40,'',1)?></td>
					<td>
						<font class=small1 color=444444><?=$item['goodsnm']?></font>
						<div><font class=small1 color=444444>옵션 : <?=implode(' / ',array_notnull(explode(' / ',$item['option'])))?></font></div>
					</td>
					<td width="50" align="center"><font class=small1 color=444444><?=$ea?></font></td>
				</tr>
				<tr><td colspan=20 bgcolor=f5f5f5></td></tr>
				<? } ?>
				<tr>
					<td width="40"><font class=small1 color=444444>연락처</font></td>
					<td><font class=small1 color=808080><?=$orderinfo['rcv_mobile']?></font></td>
					<td></td>
				</tr>
				<tr>
					<td width="40"><font class=small1 color=444444>주소</font></td>
					<td><font class=small1 color=808080><?=$orderinfo['rcv_address']?></font></td>
					<td></td>
				</tr>
				</table>
			</td>
			<td><? if ($orderinfo['flg_inflow']!="" && $orderinfo['flg_inflow']!="sugi"){ ?><a href="javascript:popup('popup.order.php?ordno=<?=$orderinfo['ordno']?>',800,600)"><img src="../img/inflow_<?=$orderinfo['flg_inflow']?>.gif" align="absmiddle" alt="<?=$integrate_cfg['inflows'][$orderinfo['flg_inflow']]?>" /></a><? } ?></td>
			<td>
				<?php if($orderinfo[m_id]){ ?>
					<?php if($orderinfo['dormant_regDate'] == '0000-00-00 00:00:00'){ ?>
						<span id="navig" name="navig" m_id="<?=$orderinfo['m_id']?>" m_no="<?=$orderinfo['m_no']?>"><span class="small1" style="color:#0074BA;"><strong><?php echo $orderinfo['ord_name']; ?></strong> (<?php echo $orderinfo[m_id]; ?>)</span></span>
					<?php } else { ?>
						<span class="small1" style="color:#0074BA;"><strong><?php echo $orderinfo['ord_name']; ?></strong> (<?php echo $orderinfo[m_id]; ?> / 휴면회원)</span>
					<?php } ?>
				<?php } else { ?>
					<span class="small1" style="color:#0074BA;font-weight:bold;"><?=$orderinfo[ord_name]?></span>
				<?php } ?>
			</td>
			<td><span class="small1" style="color:#444444;"><?=$orderinfo['rcv_name']?></span></td>
			<td class=ver81>

				<select name="dlv_company[<?=$orderinfo['channel']?>][<?=$orderinfo['ordno']?>]">
					<? foreach ($integrate_cfg['dlv_company'][$orderinfo['channel']] as $code => $name) { ?>
					<? if ($code == 'SAGAWA') $name = 'SC 로지스(사가와)';?>
					<option value="<?=$code?>" <?=$_default_dlv_company[$orderinfo['channel']] == $code ? 'selected' : ''?>><?=$name?></option>
					<? } ?>
				</select>
				<input type="text" name="dlv_no[<?=$orderinfo['channel']?>][<?=$orderinfo['ordno']?>]" tabindex="1" onkeyPress="if (event.keyCode==13){return false;}">
			</td>
			<td class=small4 width=60><?=number_format($orderinfo['dlv_amount'])?></td>
		</tr>
		<tr><td colspan=20 bgcolor=E4E4E4></td></tr>

	<? } ?>
	</table>
