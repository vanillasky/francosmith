<?
if(!$_GET['sno']) $_GET['mode'] = "dopt_register";
$button = str_replace("dopt_","",$_GET['mode']);
$title = ($_GET['mode']=="dopt_register")?"殿废":"荐沥";

if($_GET[sno]){
	$query = "select * from ".GD_DOPT." where sno='".$_GET[sno]."' limit 1";
	$data = $db->fetch($query);
	$checked[opttype][$data[opttype]] = "checked";
	$opt1 = explode("^",$data[opt1]);
	$opt2 = explode("^",$data[opt2]);
}
?>
<script language="javascript">
function getOpt(){
	var oOptnm =parent.document.getElementsByName('optnm[]');
	var oOpttype =parent.document.getElementsByName('opttype');
	var oOpt1 = parent.document.getElementsByName('opt1[]');
	var oOpt2 = parent.document.getElementsByName('opt2[]');

	var rOptnm = document.getElementsByName('optnm[]');
	var rOpttype = document.getElementsByName('opttype');
	var otr1 = document.getElementById('trOpt1');
	var otr2 = document.getElementById('trOpt2');

	for(var i=0;i<2;i++){
		rOptnm[i].value = oOptnm[i].value;
		rOpttype[i].checked = oOpttype[i].checked;
	}

	for(var i=0;i<oOpt1.length;i++){
		var otd = otr1.insertCell();
		otd.innerHTML = "<input type=text name='opt1[]' class='opt gray' style='width:50' value='"+oOpt1[i].value+"' required>";
	}

	for(var i=0;i<oOpt2.length;i++){
		otd = otr2.insertCell();
		otd.innerHTML = "<input type=text name='opt2[]' class='opt gray' style='width:50' value='"+oOpt2[i].value+"' required>";
	}
}

function addopt(k)
{
	var otr = document.getElementById('trOpt'+k);
	var otd = otr.insertCell();
	otd.innerHTML = "<input type=text name='opt"+k+"[]' class='opt gray' style='width:50' value='' required>";
}
function delopt(k)
{
	var otr = document.getElementById('trOpt'+k);
	if (otr.cells.length>2) otr.deleteCell();

}
function applyOpt()
{
	var sopt1 = document.getElementsByName('opt1[]');
	var sopt2 = document.getElementsByName('opt2[]');
	var soptnm =document.getElementsByName('optnm[]');
	var sopttype =document.getElementsByName('opttype');

	var opt1 = parent.document.getElementsByName('opt1[]');
	var opt2 =parent.document.getElementsByName('opt2[]');
	var optnm =parent.document.getElementsByName('optnm[]');
	var opttype =parent.document.getElementsByName('opttype');

	optnm[0].value = soptnm[0].value;
	optnm[1].value = soptnm[1].value;
	for(var i=0;i < sopttype.length;i++){
		if(opttype[i] == undefined) continue;
		if(sopttype[i].checked) opttype[i].checked = true;
	}

	var l = sopt1.length - opt1.length;

	if(l > 0){
		for(var i=0;i<l;i++) parent.addopt1();
	}
	else if(l < 0) {
		l = Math.abs(l);
		for(var i=0;i<l;i++) parent.delopt1();
	}
	for(var i=0;i<sopt1.length;i++) opt1[i].value = sopt1[i].value;

	var l = sopt2.length - opt2.length;
	if(l > 0){
		for(var i=0;i<l;i++) parent.addopt2();
	}
	else if(l < 0) {
		l = Math.abs(l);
		for(var i=0;i<l;i++) parent.delopt2();
	}
	for(var i=0;i<sopt2.length;i++) opt2[i].value = sopt2[i].value;
	alert('可记 利侩 肯丰!');
}
</script>
<form name=fm method=post action="indb.dopt.php" target="hiddenfrm"  onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$_GET['mode']?>">
<input type=hidden name=doptsno value="<?=$_GET['sno']?>">
<input type=hidden name=returnUrl value="<?=$returnUrl?>">

<div class=title>腹捞静绰 可记 <?=$title?>窍扁</div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td width=120 nowrap>扁夯 可记 力格</td>
	<td><div style="height:25;padding-top:5"><input type=text name="dopt_title" style="width:300" value="<?=$data[title]?>" required label="腹捞静绰 可记 力格" class="line"></div></td>
</tr>
<tr>
	<td width=120 nowrap>可记沥焊</td>
	<td>
	<div style="padding-bottom:10">
	<div>
	<font class=small color=black><b>可记疙1</b> : <input type=text name=optnm[] value="<?=$data[optnm1]?>">
	<a href="javascript:addopt(1)" onfocus=blur()><img src="../img/i_add.gif" align=absmiddle></a> <a href="javascript:delopt(1)" onfocus=blur()><img src="../img/i_del.gif" align=absmiddle></a><span style="width:20"></span>
	<b>可记疙2</b></font> : <input type=text name=optnm[] value="<?=$data[optnm2]?>">
	<a href="javascript:addopt(2)" onfocus=blur()><img src="../img/i_add.gif" align=absmiddle></a> <a href="javascript:delopt(2)" onfocus=blur()><img src="../img/i_del.gif" align=absmiddle></a><span style="width:20"></span>
	</div>
	<div>
	<span class=noline><b>可记免仿规侥</b> :
	<input type=radio name=opttype value="single" <?=$checked[opttype][single]?>> 老眉屈
	<input type=radio name=opttype value="double" <?=$checked[opttype][double]?>> 盒府屈
	</span>
	</div>
	</div>
	<table id="tblOpt1"  border=1 bordercolor=#cccccc style="border-collapse:collapse">
	<tr id="trOpt1">
		<td width="70" align="center">可记 1 </td>
		<?if($opt1)foreach($opt1 as $v){?>
		<td><input type=text name='opt1[]' class='opt gray' style='width:50' value='<?=$v?>' required></td>
		<?}?>
	</tr>
	</table>
	<div style="font:0;height:5"></div>
	<table id="tblOpt2"  border=1 bordercolor=#cccccc style="border-collapse:collapse">
	<tr id="trOpt2">
		<td width="70" align="center">可记 2 </td>
		<?if($opt2)foreach($opt2 as $v){?>
		<td><input type=text name='opt2[]' class='opt gray' style='width:50'  value='<?=$v?>' required></td>
		<?}?>
	</tr>
	</table>
	</td>
</tr>
</table>
<?
if(!$_GET[sno]){
?>
<script>getOpt();</script>
<?}?>
<?
if($_GET[mode]=='dopt_apply'){
?>
<script>applyOpt();</script>
<?
exit;
}
?>
<div class=button>
<?if($_GET['mode']  != "dopt_register"){?><a href="popup.dopt_list.php"><img src="../img/btn_list.gif"></a> <?}?><input type=image src="../img/btn_<?=$button ?>.gif">
</div>
</form>
<iframe  name="hiddenfrm" frameborder="0" width="100%" height="0"></iframe>