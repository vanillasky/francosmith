<?

include "../_header.php";

if (!$sess && !$_COOKIE[guest_ordno]) go("../member/login.php?returnUrl=$_SERVER[PHP_SELF]");

@include "../conf/config.pay.php";
?>
<link rel='styleSheet' href='../admin/style.css'>
<?
if($set[delivery][basis])
{
	### 배송업체 정보
	$query = "select * from ".GD_LIST_DELIVERY." where useyn='y' order by deliverycomp";
	$res = $db->query($query);
	while ($data=$db->fetch($res)){
		$_delivery[] = $data;
		$r_delivery[$data[deliveryno]] = $data[deliverycomp];
	}

	if($_GET[item_sno]){
		// $data = $db->fetch("select * from  ".GD_ORDER_ITEM." where sno='$_GET[item_sno]' and dvcode and dvno");
		$data = $db->fetch("select * from  ".GD_ORDER_ITEM." where sno='$_GET[item_sno]' ");
		displayDelivery($data[dvno],$data[dvcode]);
	}else{
		$res = $db->query("select * from ".GD_ORDER_ITEM." where ordno = '$_GET[ordno]' and dvcode and dvno");
		while($data = $db->fetch($res)){
			$item[] = $data;
			if($data[dvno] && $data[dvcode])$tdv[] = $data[dvno] . "^" . $data[dvcode];
		}
		if($tdv)$rdv = array_unique($tdv);
		if(count($rdv) == '1'){
			$arr = explode('^',$rdv[0]);
			displayDelivery($arr[0],$arr[1]);
		}else if($item) {
?>
			<div class="title title_top">주문 상품별 배송추적 <span>주문상품별로 다르게 입력된 송장 번호를 추적하 실 수 있습니다.</span> </div>
	<div id=MSG01>
	<table cellpadding=1 cellspacing=0 border=0 class=small_tip>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font color=0074BA>각 해당상품의 택배사/송장번호를 클릭하면 배송상태를 추적</font>하실 수 있습니다.</td></tr>
	</table>
	</div>
	<script>cssRound('MSG01','#F7F7F7')</script>
	<table class=tb cellpadding=4 cellspacing=0>
		<tr height=25 bgcolor=#2E2B29 class=small4 style="padding-top:8px">
			<th><font color=white>번호</th>
			<th width=420><font color=white>상품명</th>
			<th width=50><font color=white>수량</th>
			<th nowrap><font color=white >택배사/송장번호</th>
		</tr>
		<col align=center span=3><col>
		<col align=center span=10>
		<?
		foreach($item as $v){
		?>
		<tr bgcolor="#ffffff" height=28>
			<td width=35 nowrap><font class=ver8 color=444444><?=++$idx?></td>

			<td align=left>
				<font class=small color=555555><?=$v[goodsnm]?>
				<? if ($v[opt1]){ ?>[<?=$v[opt1]?><? if ($v[opt2]){ ?>/<?=$v[opt2]?><? } ?>]<? } ?>
				<? if ($v[addopt]){ ?><div>[<?=str_replace("^","] [",$v[addopt])?>]</div><? } ?></a>

		<font class=small1 color=6d6d6d>제조사 : <?=$v[maker] ? $v[maker] : '없음'?>
			<font class=small1 color=6d6d6d>브랜드 : <?=$v[brandnm] ? $v[brandnm] : '없음'?>
			</td>
			<td nowrap><font class=small color=555555><b><?=$v[ea]?></b>개</td>
			<td nowrap><a href="javascript:popup('<?=$PHP_SELF?>?item_sno=<?=$v[sno]?>',650,500)"><font class=small1 color=0074BA><b><?=((!$v[dvcode]||!$v[dvno])&&$data[step])? "-":$r_delivery[$v[dvno]] . " " . $v[dvcode]?></b></a></font></td>
		</tr>
		<tr><td colspan=5 bgcolor=dedede></td></tr>
		<?}?>
	</table>
	<?
		}else{
			$data = $db -> 	fetch("select * from ".GD_ORDER." where ordno='$_GET[ordno]'");
			if($data[deliveryno] && $data[deliverycode]){
				displayDelivery($data[deliveryno],$data[deliverycode]);
			}
		}
	}
}else{
	$data = $db->fetch("select * from ".GD_ORDER." where ordno='$_GET[ordno]' limit 1");
	displayDelivery($data[deliveryno],$data[deliverycode]);
}
?>
<div style="padding:20px 0 10px 0" align=center>
<a href="javascript:window.close()">[확인]</a>
</div>