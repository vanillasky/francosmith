<?

include "../_header.popup.php";

### Ŭ���ӿ�û����
$claim = $db->fetch("select * from ".INPK_CLAIM." where clmsno='{$_GET['clmsno']}'");
$resItem = $db->query("select * from ".INPK_CLAIM_ITEM." ci left join ".GD_ORDER_ITEM." oi on ci.item_sno=oi.sno where ci.itmsno='{$_GET['itmsno']}'");

### ��۾�ü ����
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
		alert("�԰�Ȯ�����θ� �����ϼž� �մϴ�.");
		fObj['iscomp'].focus();
		return false;
	}
	if (fObj['deliveryno'].value === false){
		alert("�ù�縦 �����ϼž� �մϴ�.");
		fObj['deliveryno'].focus();
		return false;
	}
	else {
		query += '&deliveryno=' + fObj['deliveryno'].value;
	}
	if (fObj['deliverycode'].value === false){
		alert("�����ȣ�� �Է��ϼž� �մϴ�.");
		fObj['deliverycode'].focus();
		return false;
	}
	else {
		query += '&deliverycode=' + fObj['deliverycode'].value;
	}

	if (confirm("�԰�(ȸ��)�� Ȯ���ϰڽ��ϱ�?") === false) return false;

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
					if (statId != '') parent._ID(statId).innerHTML = '<font color=0074BA><b>��ǰ/��ȯ�԰�Ȯ������</b></font>';
					parent.closeLayer();
				}
				else {
					var failMsg = response.replace(/^fail:/, "");
					if (failMsg) failMsg = "\n\n-----------------------------------------------\n\n[���п���]\n" + failMsg;
					alert("������ ������ ���еǾ����ϴ�. �ٽ� �õ��ϼ���." + failMsg);
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

<div class="title title_top" style="margin-top:10px;"><?=$claim['clm_tpnm']?>�԰�(ȸ��) Ȯ���ϱ�<span>�������õ� �ֹ��� �԰�(ȸ��) Ȯ���մϴ�.</span></div>

<form onsubmit="return ( respClaimReq(this) ? false : false );" id="fm">

<div style="padding:5 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">��</font> ���� �Ʒ��� �ֹ���ǰ������ Ȯ���մϴ�.</b></font></div>
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
<div style="padding:20 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">��</font> �԰�(ȸ��)���θ� Ȯ���Ͽ� �����ȣ�� �Է��� �� [Ȯ��]�� Ŭ���մϴ�.</b></font></div>
<div style="padding:0 0 5 5">
	<div><input type="checkbox" class="null" name="iscomp">�԰�(ȸ��)�� Ȯ���մϴ�.</div>
	<div style="margin-left:20px; border:solid 1 #DDDDDD; padding:5px; background-color:#F6F6F6;">
		ȸ�� �����ȣ :
		<select name=deliveryno>
		<option value="">==�ù��==
		<? if ($_delivery){ foreach ($_delivery as $v){ ?>
		<option value="<?=$v[deliveryno]?>"><?=$v[deliverycomp]?>
		<? }} ?>
		</select>
		<input type=text name=deliverycode>
	</div>
</div>

<div class="noline" style="text-align:center;"><input type=image src="../img/btn_confirm.gif" align=top></div>
<!-- ���� : end -->

</form>

<script>table_design_load();</script>
</body>
</html>