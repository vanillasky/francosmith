<?
include "../_header.popup.php";

if(!$_GET['sno']) $_GET['mode'] = "dopt_extend_register";
$button = str_replace("dopt_extend_","",$_GET['mode']);
$title = ($_GET['mode']=="dopt_extend_register")?"���":"����";

if($_GET[sno]){
	$query = "select * from ".GD_DOPT_EXTEND." where sno='".$_GET[sno]."' limit 1";
	$data = $db->fetch($query);

	$data[option] = unserialize($data[option]);
}

?>
<script language="javascript">

/*** �߰��ɼ� ***/
function add_addopt()
{
	var tbAdd = document.getElementById('tbAdd');
	oTr = tbAdd.insertRow(-1);
	oTd = oTr.insertCell(-1);
	oTd.innerHTML = "<input type=text name=addoptnm[]> <a href='javascript:void(0)' onClick='add_subadd(this)'><img src='../img/i_proadd.gif' align=absmiddle></a>";
	oTd = oTr.insertCell(-1);
	oTd.colSpan = 2;
	oTd.innerHTML = "\
	<table>\
	<tr>\
		<td><input type=text name=addopt[opt][" + (oTr.rowIndex-1) + "][] style='width:205px'> ���ý�</td>\
		<td>�Ǹűݾ׿� <input type=text name=addopt[addprice][" + (oTr.rowIndex-1) + "][] size=9> �� �߰�</td>\
	</tr>\
	</table>\
	";
	oTd = oTr.insertCell(-1);
	oTd.className = "noline";
	oTd.innerHTML = "<input type=checkbox name=addoptreq[" + (oTr.rowIndex-1) + "]>";
}
function del_addopt()
{
	var tbOption = document.getElementById('tbAdd');
	if (tbOption.rows.length>2) tbOption.deleteRow();
}
function add_subadd(obj)
{
	var idx = obj.parentNode.parentNode.rowIndex - 1;

	var tmp_tr = $(obj).up('tr');
	obj = $(tmp_tr).down('table');
	
	oTr = obj.insertRow(-1);
	oTd = oTr.insertCell(-1);
	oTd.innerHTML = "<input type=text name=addopt[opt][" + idx + "][] style='width:205px'> ���ý�";
	oTd = oTr.insertCell(-1);
	oTd.innerHTML = "�Ǹűݾ׿� <input type=text name=addopt[addprice][" + idx + "][] size=9> �� �߰�";
}
</script>


<form name=fm method=post action="indb.dopt.php" target="hiddenfrm"  onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$_GET['mode']?>">
<input type=hidden name=doptextendsno value="<?=$_GET['sno']?>">
<input type=hidden name=returnUrl value="<?=$returnUrl?>">

<div class=title>���̾��� �߰� �ɼ� <?=$title?>�ϱ�</div>

<table class=tb width="100%">
<col class=cellC><col class=cellL>
<tr>
	<td width=70 nowrap>�ɼ� ����</td>
	<td><div style="height:25;padding-top:5"><input type=text name="dopt_title" style="width:300" value="<?=$data['title']?>" required label="�ɼ� ����" class="line"></div></td>
</tr>
<tr>
	<td width=70 nowrap>�ɼ� ����</td>
	<td>
	<!-- -->
	<a href="javascript:add_addopt()"><img src="../img/i_addoption.gif" align=absmiddle></a>
	<a href="javascript:del_addopt()"><img src="../img/i_deloption.gif" align=absmiddle></a>
	<span class=small1 style="padding-left:5px">(�ɼǸ� �ƹ� ���뵵 �Է����� ������ �ش� �ɼ��� ����ó���˴ϴ�)</span>

	<div style="height:7px"></div>

	<table id=tbAdd width="100%" border=2 bordercolor=#cccccc style="border-collapse:collapse;">
	<tr bgcolor=#f7f7f7 align=center>
		<td>�ɼǸ� <font class=small>(��. �Ǽ��縮)</font></td>
		<td>�׸�� <font class=small>(��. �����)</font></td>
		<td>���� <font class=small color=444444>(�����϶��� 0���Է�)</font></td>
		<td>���Ž��ʼ�</td>
	</tr>
	<col valign=top style="padding-top:5px">
	<col span=2><col align=center valign=top style="padding-top:5px">
	<?
	if (is_array($data[option])) {

		foreach ($data[option] as $k=>$row){

	?>
	<tr>
		<td>
		<input type=text name=addoptnm[] value="<?=$row[name]?>"> <a href="javascript:void(0)" onClick="add_subadd(this)"><img src="../img/i_proadd.gif" align=absmiddle border=0></a>
		</td>
		<td colspan=2>

			<table>
			<col><col align=center>
			<? foreach ($row['options'] as $item){ ?>
			<tr>
				<td><input type=text name=addopt[opt][<?=$k?>][] value="<?=$item[name]?>" style="width:205px"> ���ý�</td>
				<td>�Ǹűݾ׿� <input type=text name=addopt[addprice][<?=$k?>][]  size=9 value="<?=$item[price]?>"> �� �߰�</td>
			</tr>
			<? } ?>
			</table>

		</td>
		<td class=noline align=center><input type=checkbox name=addoptreq[<?=$k?>] value="o" <?=($row['require']) ? 'checked' : ''?>></td>
	</tr>
	<? }
	}
	?>
	</table>


	<!-- -->
	</td>
</tr>
</table>




<div class=button>
<?if($_GET['mode']  != "dopt_extend_register"){?><a href="popup.dopt_extend_list.php"><img src="../img/btn_list.gif"></a> <?}?><input type=image src="../img/btn_<?=$button ?>.gif">
</div>
</form>
<iframe  name="hiddenfrm" frameborder="0" width="100%" height="0"></iframe>


<script>
<? if(!$_GET[sno]){ ?>
add_addopt();
<?}?>
table_design_load();
</script>
