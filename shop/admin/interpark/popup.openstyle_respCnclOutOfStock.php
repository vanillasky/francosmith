<?

include "../_header.popup.php";

### �ֹ���ǰ����
//$order = $db->fetch("select * from ".GD_ORDER." where ordno='{$_GET['ordno']}'");	// @Ȯ�� : ������ �ʴ� ��
$item = $db->fetch("select * from ".GD_ORDER_ITEM." where sno='{$_GET['sno']}'");

?>

<script language="javascript">
var statId = '<?=$_GET['statId']?>';
function respClaimReq(fObj)
{
	var loadObj = null;

	if (fObj['isoutofstock'].checked === false){
		alert("ǰ���ֹ���ҿ��θ� �����ϼž� �մϴ�.");
		fObj['isoutofstock'].focus();
		return false;
	}
	if (confirm("ǰ���ֹ���Ҹ� ��û�ϰڽ��ϱ�?") === false) return false;

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
					alert("ǰ���ֹ���Ұ� ��û�Ǿ����ϴ�.");
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

<div class="title title_top" style="margin-top:10px;">ǰ���ֹ���� ��û�ϱ�<span>ǰ���� �ֹ���ǰ�� ���� ǰ���ֹ���Ҹ� ������ũ�� ��û�մϴ�.</span></div>

<form onsubmit="return ( respClaimReq(this) ? false : false );" id="fm">

<div style="padding:5 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">��</font> ���� �Ʒ��� �ֹ���ǰ������ Ȯ���մϴ�.</b></font></div>
<table class=tb style="margin-bottom:10px;">
<col class=cellC><col class=cellL>
<?
$goodsnm = $item['goodsnm'];
if ($item['opt1']) $goodsnm .= "[{$item['opt1']}" . ($item['opt2'] ? "/{$item['opt2']}" : "") . "]";
if ($item['addopt']) $goodsnm .= "<div>[" . str_replace("^","] [",$item[addopt]) . "]</div>";
?>
<tr>
	<td>��ǰ��</td>
	<td><?=$goodsnm?></td>
</tr>
</table>

<!-- ���� : start -->
<div style="padding:20 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">��</font> ǰ���ֹ���ҿ��θ� ������ �� [Ȯ��]�� Ŭ���մϴ�.</b></font></div>
<div style="padding:0 0 5 5">
	<input type="checkbox" class="null" name="isoutofstock">ǰ���ֹ���Ҹ� ��û�մϴ�.
</div>

<div class="noline" style="text-align:center;"><input type=image src="../img/btn_confirm.gif" align=top></div>
<!-- ���� : end -->

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">ǰ���� �ֹ���ǰ�� ���� <font color=EA0095>ǰ���ֹ���Ҹ� ������ũ�� ��û</font>�մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">ǰ���ֹ���Ҹ� ��û�ϸ� <font color=0074BA>������ũ �����ڿ��� SMS�� ������ �߼�</font>�Ǹ�</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�����ڴ� ������ũ(e-������>�ֹ����>�ֹ�����ϱ�)���� <font color=0074BA>[�ֹ��������ϱ�]�� ����</font>�մϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>
<script>table_design_load();</script>
</body>
</html>
