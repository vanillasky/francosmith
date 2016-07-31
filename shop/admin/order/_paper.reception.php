<?
	$classids = array( "cssblue", "cssred" ); //-- 공급받는자용, 공급자용
	$headuser = array( "공급받는자용", "공급자용" ); //-- 공급받는자용, 공급자용

	### 직인
	include_once dirname(__FILE__) . "/../../conf/config.pay.php";
	$sealpath = '/shop/data/skin/' . $cfg['tplSkin'] . '/img/common/' . $set[tax][seal];

	$order = new order();
	$order->load($ordno);

	//사업장소재지
	if (!$cfg['road_address']) {
		$address = $cfg['address'];
	} else {
		$address = $cfg['road_address'];
	}

	$totalAmount = $order->getRealPrnSettleAmount();
?>
<style type = "text/css">
td, th { font-family: 돋움; font-size: 9pt; color: 333333;}

#cssblue { width: 306px; border: solid 2px #364f9e;  }
#cssblue table { border-collapse: collapse; }
#cssblue td { border-color:#364f9e; border-width:2px; border-style:solid; }

#cssblue #head { border-color:#364f9e; border-width:2px 2px 0px 2px; border-style:solid; }
#cssblue #head td { border-width:0px; border-style:solid; }

#cssred { width: 306px; border: solid 2px #ff4633;  }
#cssred table { border-collapse: collapse; }
#cssred td { border-color:#ff4633; border-width:2px; border-style:solid; }

#cssred #head { border-color:#ff4633; border-width:2px 2px 0px 2px; border-style:solid; }
#cssred #head td { border-width:0px; border-style:solid; }
</style>

<table cellspacing=10 cellpadding=0 border=0 align="center">
<tr valign="top">
<? for($loop=0; $loop < 2; $loop++){ ?>
	<td>
	<div id="<?=$classids[$loop]?>">
	<table id=head cellspacing=0 cellpadding=0 width="100%" border=0>
	<tr>
		<td width="23%" height=40>&nbsp;</td>
		<td align=middle width="44%">&nbsp;<font size=4>영 수 증</font></td>
		<td width="33%"><font style="font-weight: normal" size=1>( <?=$headuser[$loop]?> )</font></td>
	</tr>
	</table>

	<table width=100% border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td height=100% valign=top style="border-width: 3px 1px 0 0; background: url(<?=$sealpath?>) no-repeat; background-position: 235px 15px;">
			<table cellspacing=0 cellpadding=2 width="100%" border=0>
			<col width="8%"><col width="20%"><col width="30%"><col width="12%">
			<tr>
				<td valign=bottom colspan=2>no.</td>
				<td style="border-top-width: 0px;" align=right colspan=3>&nbsp;<font style="font-weight: normal; font-size: 12pt" color=black><?=$order[nameOrder]?>&nbsp;&nbsp;</font><font size=3>귀하</font>&nbsp;</td>
			</tr>
			<tr align=middle>
				<td rowspan=4 height="100%">공<br><br>급<br><br>자</td>
				<td>사 업 자<br>등록번호</td>
				<td colspan=3 align=left style="padding-left:10">&nbsp;<font size=3><?=$cfg[compSerial]?></font></td>
			</tr>
			<tr align=middle height=30>
				<td>상 호</td>
				<td>&nbsp;<?=$cfg[compName]?></td>
				<td>성명</td>
				<td>&nbsp;<?=$cfg[ceoName]?></td>
			</tr>
			<tr align=middle>
				<td>사 업 장<br>소 재 지</td>
				<td colspan=3>&nbsp;<?=$address?></td>
			</tr>
			<tr align=middle height=30>
				<td>업태</td>
				<td>&nbsp;<?=$cfg[service]?></td>
				<td>종목</td>
				<td>&nbsp;<?=$cfg[item]?></td>
			</tr>
			</table>
		</td>
	</tr>
	</table>

	<table cellspacing=0 cellpadding=2 width="100%" border=0>
	<tr align=middle>
		<td style="border-top-width: 0px;">작성년월일</td>
		<td style="border-left-width: 3px; border-right-width: 3px;">공급대가총액</td>
		<td style="border-top-width: 0px;">비 고</td>
	</tr>
	<tr align=middle>
		<td>&nbsp; <?=toDate(str_replace("-","",$order[orddt]),". ")?></td>
		<td style="border-left-width: 3px; border-right-width: 3px; border-bottom-width: 4px;">&nbsp;￦<?=number_format($totalAmount)?></td>
		<td align=right></td>
	</tr>
	</table>

	<table cellspacing=0 cellpadding=4 width="100%" border=0>
	<tr align=middle>
		<td style="border-top-width: 0px; border-bottom-width: 0px;">위 금액을 정히 영수( 청구 )함</td>
	</tr>
	</table>

	<table cellspacing=0 cellpadding=2 width="100%" border=0>
	<tr align=middle>
		<td>월</td>
		<td>일</td>
		<td>품 목</td>
		<td>수량</td>
		<td>단가</td>
		<td>금액</td>
	</tr>
	<?
	$total = array(
		'etc' => $totalAmount,	// 할인, 배송비 등의 합산 (=주문의 실 결제금액 - 상품가격 합)
	);

	$rowCount = 0;

	foreach ($order->getOrderItems() as $v){
		if ($v->hasCancelCompleted()) continue;

		$rowCount++;

		// 금액 총합
		$total['etc'] -= $v->getAmount();
	?>
	<tr>
		<td align=middle><?=substr($order[orddt],5,2)?></td>
		<td align=middle><?=substr($order[orddt],8,2)?></td>
		<td height=20>
		<?=$v[goodsnm]?>
		<? if ($v[opt1]){ ?>[<?=$v[opt1]?><? if ($v[opt2]){ ?>/<?=$v[opt2]?><? } ?>]<? } ?>
		<? if ($v[addopt]){ ?><div>[<?=str_replace("^","] [",$v[addopt])?>]</div><? } ?>
		</td>
		<td align=middle><?=$v[ea]?></td>
		<td align=right><?=number_format($v[price])?></td>
		<td align=right><?=number_format($v->getAmount())?></td>
	</tr>
	<? } ?>
	<?
	if ($total['etc']) {
		$rowCount++;
	?>
	<tr>
		<td align=middle><?=substr($order[orddt],5,2)?></td>
		<td align=middle><?=substr($order[orddt],8,2)?></td>
		<td height=20>기타 (배송비, 할인 등)</td>
		<td align=middle>1</td>
		<td align=right><?=number_format($total['etc'])?></td>
		<td align=right><?=number_format($total['etc'])?></td>
	</tr>
	<? } ?>
	<tr>
		<td align=middle colspan=6>*** 이 하 여 백 *** </td>
	</tr>
	<? for ($i=$rowCount;$i<9;$i++){ ?>
	<tr align=middle>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align=right>&nbsp;</td>
	</tr>
	<? } ?>
	</table>

	<table cellspacing=0 cellpadding=4 width="100%" border=0>
	<tr align=middle>
		<td style="border-top-width: 0px;" height=25><font style="font-weight: normal" size=1>부가가치세법시행규칙 제25조 규정에 의한 ( 영수증 )으로 개정.</font></td>
	</tr>
	</table>
	</div>
	</td>
<? } ?>
</tr>
</table>