<?

include "../_header.popup.php";
@include_once "../../lib/pAPI.class.php";
@include_once "../../lib/json.class.php";

$pAPI = new pAPI();
$json = new Services_JSON(16);
$category = $_GET['category'];
$json_data = $pAPI->getMainMenuItem($godo['sno'], $category);
$data = $json->decode($json_data);
$cntMenu = $data['count'];
?>

<script>
function chkForm2(obj){
	return chkForm(obj);
	parent.saveHistory(parent.form);
}
</script>

<form name=form method=post action="indb.php" onsubmit="return chkForm2(this)">
<input type=hidden name=mode value="del_category">
<input type=hidden name=category value="<?=$category?>">

<div class="title title_top">ī�װ� ����<span>����ī�װ��� �ڵ� �����˴ϴ�</span></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr height=26>
	<td>���� ī�װ�</td>
	<td><?=$data['name']?></td>
</tr>
<tr>
	<td>�����з� ��</td>
	<td><b><?=$cntMenu?></b>��</td>
</tr>
<tr>
	<td>���ǻ���</td>
	<td class=small1 style="color:#5B5B5B;padding:5px;">
		��ܲٹ̱⿡ ���� �̹����� �ٸ� �������� ����ϰ� ���� �� �����Ƿ� �ڵ� �������� �ʽ��ϴ�.<br>
		'�����ΰ��� > webFTP�̹������� > data > editor'���� �̹���üũ �� ���������ϼ���.
	</td>
</tr>
</table>

<div class="button_popup">
<input type=image src="../img/btn_confirm_s.gif">
<a href="javascript:parent.closeLayer()"><img src="../img/btn_cancel_s.gif"></a>
</div>

</form>

<script>table_design_load();</script>