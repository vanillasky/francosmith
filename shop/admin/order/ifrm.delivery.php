<?
include "../_header.popup.php";
### 배송업체 정보
$query = "select * from ".GD_LIST_DELIVERY." where useyn='y' and deliveryno <> '100' order by deliverycomp";
$res = $db->query($query);
while ($data=$db->fetch($res))$_delivery[] = $data;

$query = "
	select a.*,b.step,GF.type, GF.status
	from ".GD_ORDER_ITEM." a
	left join ".GD_ORDER." b
	on a.ordno=b.ordno

	LEFT JOIN ".GD_GOODSFLOW_ORDER_MAP." AS OM
	ON a.ordno = OM.ordno AND OM.item_sno = a.sno

	LEFT JOIN ".GD_GOODSFLOW." AS GF
	ON GF.sno = OM.goodsflow_sno

	where a.sno in ($_GET[chk])
";
$res = $db->query($query);
$selCnt = $db->count_($res);
list($item_cnt)=$db->fetch("select count(*) from ".GD_ORDER_ITEM." where ordno='$_REQUEST[ordno]' and istep < 40");

if($selCnt == $item_cnt)$checked[chkDelivery] = "checked";

$datas = array();
$GF = false;
while ($data=$db->fetch($res)){
	$datas[] = $data;
	if (!empty($data['status'])) $GF = $data;
}

### 기본 배송사
if(!$data['deliveryno'] && $_delivery[0]['deliveryno']) $deliveryno = $_delivery[0]['deliveryno'];
$_selected['deliveryno'][$deliveryno] = "selected";
?>
<body style="margin:0" scroll=no>
<script>
	function ifmclose(){
		var obj = parent.document.getElementById('layer_cancel');
		obj.style.display='none';
	}
	function chkdelete(){
		var f = document.frmDelivery;
		f.chkDelDelivery.value='1';
		f.submit();
	}

document.observe("dom:loaded", function() {
	var selDeliveryNo=document.frmDelivery.deliveryno;
	var iptDeliveryCode=document.frmDelivery.deliverycode;
	Element.extend(iptDeliveryCode);
	Element.extend(selDeliveryNo);
	if(selDeliveryNo.value=="100") {
		iptDeliveryCode.disabled=true;
	}
	else {
		iptDeliveryCode.disabled=false;
	}

	selDeliveryNo.observe("change",function(evt){
		var element = evt.element();
		if(element.value=="100") {
			iptDeliveryCode.disabled=true;
		}
		else {
			iptDeliveryCode.disabled=false;
		}
	});


});
</script>
<form name=frmDelivery method=post action="indb.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="partDelivery">
<input type=hidden name=ordno value="<?=$_GET[ordno]?>">
<input type=hidden name=chkDelDelivery value="">


<div style="padding-bottom:5px">&nbsp;<img src="../img/icon_process.gif" align=absmiddle><b style="color:494949">주문상품 배송정보 입력하기</b></div>

<table border=2 bordercolor=#000000 style="border-collapse:collapse" width=100% cellpadding=0 cellspacing=0>
<tr><td>
<? if ($GF == false) { ?>
<table cellpadding=0 cellspacing=0 border=1 width=100% bordercolor=#e0e0e0 style="border-collapse:collapse">
<tr>
	<td bgcolor=#f7f7f7 style="padding:15"><font class=small1 color=434343 width=140><b>상품정보</td>

	<td>
	<table width=100% cellpadding=0 cellspacing=0>
	<tr bgcolor=#f7f7f7 height=22>
		<th><font class=small1 color=434343><b>상품명</th>
		<th width=150><font class=small1 color=434343><b>옵션</th>
		<th width=150><font class=small1 color=434343><b>수량</th>
	</tr>
	<?
	foreach ($datas as $data) {
		$step = $data[step];
		if($data[istep] >= 3)$cnt++;
	?>
	<input type=hidden name=sno[] value="<?=$data[sno]?>">
	<tr height=26>

		<td style="padding-left:10px"><font class=small color=666666><b><?=$data[goodsnm]?></b></font></td>
		<td></td>
		<td align=center><font color=555555 class=small><b><?=$data[ea]?></b><font class=small1>개</td>
	</tr>
	<? } ?>
    </table>
    </td>
</tr>

<tr>
	    <td bgcolor=#f7f7f7 style="padding:15"><font class=small1 color=434343><b>택배송장정보 입력</b></td>
		<!--
			<td style="padding-left:50">
				<div><input type=checkbox name=chkDelivery value='1' class=null <?=$checked[chkDelivery]?>> 주문리스트에서 배송추적 시 이 송장번호를 사용</div>
				<?if($cnt){?>
				<div><input type=checkbox name=chkDelDelivery value='1' class=null onclick='chkBdelivery(this)'> 입력한 송장번호를 삭제하고 입금확인으로 돌림</div>
				<?}?>
			</td>-->
		<td style="padding:12">
		<select name=deliveryno>
			<option value="">--- 택배사 선택 ---
			<? if ($_delivery){ foreach ($_delivery as $v){ ?>
				<option value="<?=$v[deliveryno]?>" <?=$_selected[deliveryno][$v[deliveryno]]?>><?=$v[deliverycomp]?>
				<? }} ?>
				</select>
				<input type=text name=deliverycode value="<?=$data[deliverycode]?>" style="width:220px">
				</div>
		</td>
		</tr>


<tr>
	<td colspan=4 class=noline align=left height=45>
	<div align=center><input type=image src="../img/btn_delinum_confirm.gif">&nbsp;&nbsp;<a href="javascript:chkdelete();"><img src="../img/btn_delinum_del.gif"></a>&nbsp;&nbsp;<a href="javascript:ifmclose()"><img src="../img/btn_delinum_close.gif"></a></div>
<!-- <div style="padding:8 0 0 67"><font color=black><b>- 배송정보 입력하기 -</b></font></div>
	<div style="padding:3 0 0 67"><font class=small1 color=444444>현 주문상태가 <font color=ED00A2>입금확인</font>인 경우 바로 <font color=ED00A2>배송정보가</font>입력 되면 <font color=ED00A2>배송중상태</font>로 처리하여야 합니다.</font></div> -->
	</td>
</tr>
</table>

<? } else { ?>
<table cellpadding=0 cellspacing=0 border=1 width=100% bordercolor=#e0e0e0 style="border-collapse:collapse">
<tr>

	<td style="padding:20px;">
	<? if ($GF['status'] == 'print_invoice') { ?>
	굿스플로 택배 연동서비스를 통해 발급 받은 송장번호는 직접 수정하실 수 없습니다. <br>
	주문>택배연동서비스>굿스플로 상품 수집 대기리스트에서 재출력 또는 취소하실 수 있습니다.<br>
	<div style="margin-top:3px;">
	<a href="javascript:void(0);" onClick="parent.location.href='../order/goodsflow.standby.php';"class="extext">[굿스플로 상품수집 대기리스트 바로가기]</a>
	</div>
	<? } else { ?>
	굿스플로 택배 연동서비스를 통해 발급 받은 송장번호는 수정하실 수 없습니다. <br>
	이미 상품을 택배사에서 집하한 경우 수정할 수 없습니다.
	<? } ?>
    </td>
</tr>




<tr>
	<td colspan=4 class=noline align=left height=45>
	<div align=center><a href="javascript:ifmclose()"><img src="../img/btn_delinum_close.gif"></a></div>

	</td>
</tr>
</table>
<? } ?>
    </td>
</tr>
</table>

</form>

<script>
table_design_load();
window.onload = function(){
	parent.document.getElementById('ifrmCancel').style.height = document.body.scrollHeight;
}
</script>