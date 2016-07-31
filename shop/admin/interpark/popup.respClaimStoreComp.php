<?

include "../_header.popup.php";

### 클레임요청정보
$claim = $db->fetch("select * from ".INPK_CLAIM." where clmsno='{$_GET['clmsno']}'");
$resItem = $db->query("select * from ".INPK_CLAIM_ITEM." ci left join ".GD_ORDER_ITEM." oi on ci.item_sno=oi.sno where ci.itmsno='{$_GET['itmsno']}'");

### 배송업체 정보
$query = "select * from ".GD_LIST_DELIVERY." where useyn='y' order by deliverycomp";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	$_delivery[] = $data;
}

?>

<script language="javascript">
var statId = '<?=$_GET['statId']?>';
function respClaimReq(fObj)
{
	var loadObj = null;
	var query = '';

	if (fObj['iscomp'].checked === false){
		alert("입고확정여부를 선택하셔야 합니다.");
		fObj['iscomp'].focus();
		return false;
	}
	if (fObj['deliveryno'].value === false){
		alert("택배사를 선택하셔야 합니다.");
		fObj['deliveryno'].focus();
		return false;
	}
	else {
		query += '&deliveryno=' + fObj['deliveryno'].value;
	}
	if (fObj['deliverycode'].value === false){
		alert("송장번호를 입력하셔야 합니다.");
		fObj['deliverycode'].focus();
		return false;
	}
	else {
		query += '&deliverycode=' + fObj['deliverycode'].value;
	}

	if (confirm("입고(회수)를 확정하겠습니까?") === false) return false;

	var urlStr = "../interpark/ajaxSock.php?mode=clmStoreCompForComm&clmsno=<?=$_GET['clmsno']?>&itmsno=<?=$_GET['itmsno']?>" + query + "&dummy=" + new Date().getTime();
	var ajax = new Ajax.Request( urlStr,
	{
		method: "get",
		onLoading: function ()
		{
			if (loadObj == null){
				loadObj = _ID('fm').parentNode.insertBefore(document.createElement('DIV'), _ID('fm'));
				loadObj.style.position = 'relative';
				var cDiv = loadObj.appendChild(document.createElement('DIV'));
				var cImg = cDiv.appendChild(document.createElement('IMG'));
				cImg.src = '../img/loading.gif';
				with (cDiv.style) {
					position = 'absolute';
					backgroundColor = '#FFFFFF';
					border = 'solid 1px #dddddd';
					filter = "Alpha(Opacity=90)";
					opacity = "0.9";
					padding = 50;
					left = 100;
					top = 50;
				}
			}
			loadObj.style.display='block';
		},
		onComplete:  function ()
		{
			var req = ajax.transport;
			if ( req.status == 200 )
			{
				var response = req.responseText;
				if (response == 'succeed'){
					if (statId != '') parent._ID(statId).innerHTML = '<font color=0074BA><b>반품/교환입고확정전송</b></font>';
					parent.closeLayer();
				}
				else {
					var failMsg = response.replace(/^fail:/, "");
					if (failMsg) failMsg = "\n\n-----------------------------------------------\n\n[실패원인]\n" + failMsg;
					alert("데이터 전송이 실패되었습니다. 다시 시도하세요." + failMsg);
				}
			}
			else {
				var msg = req.getResponseHeader("Status");
				if ( msg == null || msg.length == null || msg.length <= 0 )
					alert( "Error! Request status is " + req.status );
				else
					alert( msg );
			}
			if (loadObj != null) loadObj.style.display='none';
		}
	} );
}
</script>

<div class="title title_top" style="margin-top:10px;"><?=$claim['clm_tpnm']?>입고(회수) 확정하기<span>수거지시된 주문을 입고(회수) 확정합니다.</span></div>

<form onsubmit="return ( respClaimReq(this) ? false : false );" id="fm">

<div style="padding:5 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">①</font> 먼저 아래의 주문상품정보를 확인합니다.</b></font></div>
<table class=tb style="margin-bottom:10px;">
<col class=cellC><col class=cellL>
<?
while ($item = $db->fetch($resItem)){
	$goodsnm = $item['goodsnm'];
	if ($item['opt1']) $goodsnm .= "[{$item['opt1']}" . ($item['opt2'] ? "/{$item['opt2']}" : "") . "]";
	if ($item['addopt']) $goodsnm .= "<div>[" . str_replace("^","] [",$item[addopt]) . "]</div>";
?>
<tr>
	<td>상품명</td>
	<td><?=$goodsnm?></td>
</tr>
<tr>
	<td>요청수량</td>
	<td><?=$item['clm_qty']?> 개</td>
</tr>
<tr>
	<td>요청사유</td>
	<td>[<?=$item['clm_rsn_tpnm']?>] <?=$item['clm_rsn_dtl']?></td>
</tr>
<tr>
	<td>요청일</td>
	<td><?=$item['clm_dt']?></td>
</tr>
<? } ?>
</table>

<!-- 실행 : start -->
<div style="padding:20 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">②</font> 입고(회수)여부를 확인하여 송장번호를 입력한 후 [확인]을 클릭합니다.</b></font></div>
<div style="padding:0 0 5 5">
	<div><input type="checkbox" class="null" name="iscomp">입고(회수)를 확정합니다.</div>
	<div style="margin-left:20px; border:solid 1 #DDDDDD; padding:5px; background-color:#F6F6F6;">
		회수 송장번호 :
		<select name=deliveryno>
		<option value="">==택배사==
		<? if ($_delivery){ foreach ($_delivery as $v){ ?>
		<option value="<?=$v[deliveryno]?>"><?=$v[deliverycomp]?>
		<? }} ?>
		</select>
		<input type=text name=deliverycode>
	</div>
</div>

<div class="noline" style="text-align:center;"><input type=image src="../img/btn_confirm.gif" align=top></div>
<!-- 실행 : end -->

</form>

<script>table_design_load();</script>
</body>
</html>