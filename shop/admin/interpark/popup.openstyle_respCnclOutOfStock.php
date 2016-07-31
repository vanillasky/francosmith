<?

include "../_header.popup.php";

### 주문상품정보
//$order = $db->fetch("select * from ".GD_ORDER." where ordno='{$_GET['ordno']}'");	// @확인 : 사용되지 않는 값
$item = $db->fetch("select * from ".GD_ORDER_ITEM." where sno='{$_GET['sno']}'");

?>

<script language="javascript">
var statId = '<?=$_GET['statId']?>';
function respClaimReq(fObj)
{
	var loadObj = null;

	if (fObj['isoutofstock'].checked === false){
		alert("품절주문취소여부를 선택하셔야 합니다.");
		fObj['isoutofstock'].focus();
		return false;
	}
	if (confirm("품절주문취소를 요청하겠습니까?") === false) return false;

	var urlStr = "../interpark/ajaxSock.php?mode=openstyle_cnclOutOfStockReqForComm&ordno=<?=$_GET['ordno']?>&sno=<?=$_GET['sno']?>&dummy=" + new Date().getTime();
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
					if (statId != '') parent._ID(statId).parentNode.removeChild(parent._ID(statId));
					alert("품절주문취소가 요청되었습니다.");
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

<div class="title title_top" style="margin-top:10px;">품절주문취소 요청하기<span>품절된 주문상품에 대해 품절주문취소를 인터파크에 요청합니다.</span></div>

<form onsubmit="return ( respClaimReq(this) ? false : false );" id="fm">

<div style="padding:5 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">①</font> 먼저 아래의 주문상품정보를 확인합니다.</b></font></div>
<table class=tb style="margin-bottom:10px;">
<col class=cellC><col class=cellL>
<?
$goodsnm = $item['goodsnm'];
if ($item['opt1']) $goodsnm .= "[{$item['opt1']}" . ($item['opt2'] ? "/{$item['opt2']}" : "") . "]";
if ($item['addopt']) $goodsnm .= "<div>[" . str_replace("^","] [",$item[addopt]) . "]</div>";
?>
<tr>
	<td>상품명</td>
	<td><?=$goodsnm?></td>
</tr>
</table>

<!-- 실행 : start -->
<div style="padding:20 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">②</font> 품절주문취소여부를 결정한 후 [확인]을 클릭합니다.</b></font></div>
<div style="padding:0 0 5 5">
	<input type="checkbox" class="null" name="isoutofstock">품절주문취소를 요청합니다.
</div>

<div class="noline" style="text-align:center;"><input type=image src="../img/btn_confirm.gif" align=top></div>
<!-- 실행 : end -->

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">품절된 주문상품에 대해 <font color=EA0095>품절주문취소를 인터파크에 요청</font>합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">품절주문취소를 요청하면 <font color=0074BA>인터파크 구매자에게 SMS와 메일이 발송</font>되며</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">구매자는 인터파크(e-고객센터>주문취소>주문취소하기)에서 <font color=0074BA>[주문즉시취소하기]를 실행</font>합니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>
<script>table_design_load();</script>
</body>
</html>
