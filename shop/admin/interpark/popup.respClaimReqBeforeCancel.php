<?

include "../_header.popup.php";

### Ŭ���ӿ�û����
$claim = $db->fetch("select * from ".INPK_CLAIM." where clmsno='{$_GET['clmsno']}'");
$resItem = $db->query("select * from ".INPK_CLAIM_ITEM." ci left join ".GD_ORDER_ITEM." oi on ci.item_sno=oi.sno where ci.itmsno='{$_GET['itmsno']}'");

?>

<script language="javascript">
var statId = '<?=$_GET['statId']?>';
function respClaimReq(fObj)
{
	var loadObj = null;

	if (fObj['isaccept'].checked === false){
		alert("���ο��θ� �����ϼž� �մϴ�.");
		fObj['isaccept'].focus();
		return false;
	}
	if (confirm("������ֹ���ҿ�û�� �����ϰڽ��ϱ�?") === false) return false;

	var urlStr = "../interpark/ajaxSock.php?mode=cnclReqAcceptForComm&clmsno=<?=$_GET['clmsno']?>&itmsno=<?=$_GET['itmsno']?>&dummy=" + new Date().getTime();
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
					if (statId != '') parent._ID(statId).innerHTML = '<font color=0074BA><b>����</b></font>';
					parent.closeLayer();
				}
				else {
					var failMsg = response.replace(/^fail:/, "");
					if (failMsg) failMsg = "\n\n-----------------------------------------------\n\n[���п���]\n" + failMsg;
					alert("������ ������ ���еǾ����ϴ�. �ٽ� �õ��ϼ���." + failMsg);
					if (failMsg.match(/öȸ/) && statId != ''){
						parent._ID(statId).innerHTML = '<font color=0074BA><b>��ûöȸ</b></font>';
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
</script>

<div class="title title_top" style="margin-top:10px;">������ֹ���ҿ�û �����ϱ�<span>������ֹ��� ���� ��ҿ�û�Դϴ�.</span></div>

<form onsubmit="return ( respClaimReq(this) ? false : false );" id="fm">

<div style="padding:5 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">��</font> ���� �Ʒ��� ��ҿ�û�� �ֹ���ǰ������ Ȯ���մϴ�.</b></font></div>
<table class=tb style="margin-bottom:10px;">
<col class=cellC><col class=cellL>
<?
while ($item = $db->fetch($resItem)){
	$goodsnm = $item['goodsnm'];
	if ($item['opt1']) $goodsnm .= "[{$item['opt1']}" . ($item['opt2'] ? "/{$item['opt2']}" : "") . "]";
	if ($item['addopt']) $goodsnm .= "<div>[" . str_replace("^","] [",$item[addopt]) . "]</div>";
?>
<tr>
	<td>��ǰ��</td>
	<td><?=$goodsnm?></td>
</tr>
<tr>
	<td>��û����</td>
	<td><?=$item['clm_qty']?> ��</td>
</tr>
<tr>
	<td>��û����</td>
	<td>[<?=$item['clm_rsn_tpnm']?>] <?=$item['clm_rsn_dtl']?></td>
</tr>
<tr>
	<td>��û��</td>
	<td><?=$item['clm_dt']?></td>
</tr>
<? } ?>
</table>

<!-- ���� : start -->
<div style="padding:20 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">��</font> ���ο��θ� ������ �� [Ȯ��]�� Ŭ���մϴ�.</b></font></div>
<div style="padding:0 0 5 5">
	<input type="checkbox" class="null" name="isaccept">������ֹ���ҿ�û�� �����մϴ�.
</div>

<div class="noline" style="text-align:center;"><input type=image src="../img/btn_confirm.gif" align=top></div>
<!-- ���� : end -->

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font color=EA0095>������ֹ��� ���� ��ҿ�û</font>�Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ҿ�û�� �����ϸ� <font color=0074BA>������ũ �����ڿ��� ȯ�ҹޱ� ��û SMS�� �߼�</font>�˴ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>
<script>table_design_load();</script>
</body>
</html>