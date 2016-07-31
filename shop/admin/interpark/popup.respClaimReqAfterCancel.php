<?

include "../_header.popup.php";

### 클레임요청정보
$claim = $db->fetch("select * from ".INPK_CLAIM." where clmsno='{$_GET['clmsno']}'");
$resItem = $db->query("select * from ".INPK_CLAIM_ITEM." ci left join ".GD_ORDER_ITEM." oi on ci.item_sno=oi.sno where ci.clmsno='{$_GET['clmsno']}'");

### 수거방법
$return_mthd = array(
	'1' => '반품전담택배_인터파크',
	'2' => '공급업체',
	'3' => '고객',
);
$claim['return_mthd_tpnm'] = trim($claim['return_mthd_tpnm']);
if ($claim['return_mthd_tpnm'] == '반품전담택배_인터파크'){
	unset($return_mthd['3']);
}
else if ($claim['return_mthd_tpnm'] == '공급업체'){
	unset($return_mthd['1']);
	unset($return_mthd['3']);
}
else if ($claim['return_mthd_tpnm'] == '고객'){
	unset($return_mthd['1']);
}

?>

<script language="javascript">
var statId = '<?=$_GET['statId']?>';
var clm_tpnm = '<?=$claim['clm_tpnm']?>';

function isChked2(El,msg)
{
	El = document.getElementsByName(El);
	for (i=0;i<El.length;i++) if (El[i].checked) var isChked = true;
	if (isChked){
		return true;
	} else {
		alert (msg);
		return false;
	}
}

function respClaimReq(fObj)
{
	var loadObj = null;
	var query = '';

	if (isChked2('isaccept',"승인여부를 선택하셔야 합니다.") === false) return false;
	if (fObj['isaccept'][0].checked === true){
		var respStr = "승인";
		if (isChked2('return_mthd_tp',"수거방법을 선택하셔야 합니다.") === false) return false;

		El = document.getElementsByName('return_mthd_tp');
		for (i=0;i<El.length;i++){
			if (El[i].checked) return_mthd_tp = El[i].value;
		}
		query += '&return_mthd_tp=' + return_mthd_tp;
	}
	else {
		var respStr = "거부";
		if (fObj.refuse_rsn.value == ''){
			alert("거부사유를 입력하세요.");
			fObj.refuse_rsn.focus();
			return false;
		}
		query += '&refuse_rsn=' + fObj.refuse_rsn.value;
	}
	if (confirm(clm_tpnm + "요청을 " + respStr + "하겠습니까?") === false) return false;

	if (clm_tpnm == '반품' && fObj['isaccept'][0].checked === true) mode = 'clmReqAcceptForComm';
	else if (clm_tpnm == '교환' && fObj['isaccept'][0].checked === true) mode = 'exchangeReqAcceptForComm';
	else if (clm_tpnm == '반품' && fObj['isaccept'][0].checked === false) mode = 'clmReqRefuseForComm';
	else if (clm_tpnm == '교환' && fObj['isaccept'][0].checked === false) mode = 'exchangeReqRefuseForComm';

	var urlStr = "../interpark/ajaxSock.php?mode=" + mode + "&clmsno=<?=$_GET['clmsno']?>" + query + "&dummy=" + new Date().getTime();
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
					if (statId != '') parent._ID(statId).innerHTML = '<font color=0074BA><b>' + respStr + '</b></font>';
					parent.closeLayer();
				}
				else {
					var failMsg = response.replace(/^fail:/, "");
					if (failMsg) failMsg = "\n\n-----------------------------------------------\n\n[실패원인]\n" + failMsg;
					alert("데이터 전송이 실패되었습니다. 다시 시도하세요." + failMsg);
					if (failMsg.match(/철회/) && statId != ''){
						parent._ID(statId).innerHTML = '<font color=0074BA><b>요청철회</b></font>';
						parent.closeLayer();
					}
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

function selAccept(fObj)
{
	_ID('reqAccept').style.display = (fObj['isaccept'][0].checked ? 'block' : 'none');
	_ID('reqRefuse').style.display = (fObj['isaccept'][1].checked ? 'block' : 'none');
}
</script>

<div class="title title_top" style="margin-top:10px;"><?=$claim['clm_tpnm']?>요청 승인/거절하기<span>아래 주문에 대한 <?=$claim['clm_tpnm']?>요청입니다.</span></div>

<form onsubmit="return ( respClaimReq(this) ? false : false );" id="fm">

<div style="padding:5 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">①</font> 먼저 아래의 주문상품정보를 확인합니다.</b></font></div>
<table class=tb style="margin-bottom:10px;">
<col class=cellC><col class=cellC><col class=cellL>
<?
while ($item = $db->fetch($resItem)){
	$goodsnm = $item['goodsnm'];
	if ($item['opt1']) $goodsnm .= "[{$item['opt1']}" . ($item['opt2'] ? "/{$item['opt2']}" : "") . "]";
	if ($item['addopt']) $goodsnm .= "<div>[" . str_replace("^","] [",$item[addopt]) . "]</div>";
?>
<tr>
	<td rowspan=4 width=30><?=++$idx?></td>
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
<div style="padding:20 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">②</font> 승인여부를 결정한 후 [확인]을 클릭합니다.</b></font></div>
<div style="padding:0 0 5 5">
	<div><input type="radio" class="null" name="isaccept" onclick="selAccept(this.form)"><?=$claim['clm_tpnm']?>요청을 <b>승인</b>합니다.</div>
	<div id="reqAccept" style="display:none; margin-left:20px; border:solid 1 #DDDDDD; padding:5px; background-color:#F6F6F6;">
		구매자는 <b>[<?=$claim['return_mthd_tpnm']?>]</b>으로 수거방법을 선택하였습니다.<br>
		구매자가 선택한 수거방법으로 수락하시겠습니까?<br>
		<?
		foreach($return_mthd as $k => $v){
			echo '<input type="radio" class="null" name="return_mthd_tp" value="' . $k . '" ' . ($claim['return_mthd_tpnm'] == $v ? 'checked' : '') . '>' . $v . '&nbsp;&nbsp;&nbsp;&nbsp;';
		}
		?>
	</div>
	<div><input type="radio" class="null" name="isaccept" onclick="selAccept(this.form)"><?=$claim['clm_tpnm']?>요청을 <b>거부</b>합니다.</div>
	<div id="reqRefuse" style="display:none; margin-left:20px; border:solid 1 #DDDDDD; padding:5px; background-color:#F6F6F6;">
		거부사유 : <input type="input" name="refuse_rsn" maxlength="100" size="80">
	</div>
</div>

<div class="noline" style="text-align:center;"><input type=image src="../img/btn_confirm.gif" align=top></div>
<!-- 실행 : end -->

</form>

<script>table_design_load();</script>
</body>
</html>