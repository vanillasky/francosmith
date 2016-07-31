<script language="javascript">
function poupcolortable(idx,bu){
	var hrefStr = '../proc/help_colortable.php?iconidx='+idx+'&target='+bu;
	var win = popup_return( hrefStr, 'colortable', 400, 400, 600, 200, 0 );
	win.focus();
}

function get_opt_title(){
	var tb = document.getElementById('tbopt1icon');
	for(var i=0;i<tb.rows.length;i++){
		var tt = document.getElementsByName('optnm[]')[0] .value;
		if( !tt ) tt='�ɼǸ�1';
		var j = i+1;
		if( tt != '�ɼǸ�1' ) tt = tt + j;
		tb.rows[i].cells[0].innerHTML = tt  + " ������";
	}

	var tb = document.getElementById('tbopt2icon');
	for(var i=0;i<tb.rows.length;i++){
		var tt = document.getElementsByName('optnm[]')[1] .value;
		if( !tt ) tt='�ɼǸ�2';
		var j = i+1;
		if( tt != '�ɼǸ�2' ) tt = tt + j;
		tb.rows[i].cells[0].innerHTML = tt  + " ������";
	}
}

function addopt1_fashion(){
	var tbOption = document.getElementById('tbopt1icon');
	var Rcnt = tbOption.rows.length;
	oTr = tbOption.insertRow(-1);
	oTr.height = "35";
	oTr.id = "tropt1icon_" + Rcnt;

	oTd = oTr.insertCell(-1);
	oTd.innerHTML = "�ɼǸ�1 ������";
	oTd = oTr.insertCell(-1);
	oTd.innerHTML = get_opticon('tbopt1icon',Rcnt);

	oTd = oTr.insertCell(-1);
	oTd.innerHTML = "��ǰ�̹���";
	oTd = oTr.insertCell(-1);
	oTd.innerHTML = "<input type=\"file\" name=\"opt1img[]\" class=\"opt gray\">";
	get_opt_title();
}

function addopt2_fashion(){
	var tbOption = document.getElementById('tbopt2icon');
	var Rcnt = tbOption.rows.length;
	oTr = tbOption.insertRow(-1);
	oTr.id = "tropt2icon_" + Rcnt;
	oTd = oTr.insertCell(-1);
	oTd.innerHTML = "�ɼǸ�2 ������";
	oTd = oTr.insertCell(-1);
	oTd.innerHTML = get_opticon('tbopt2icon',Rcnt);
	 get_opt_title();
}

function delopt1_fashion(){
	var tbOption = document.getElementById('tbopt1icon');
	if (tbOption.rows.length>1) tbOption.deleteRow(-1);
}

function delopt2_fashion(){
	var tbOption = document.getElementById('tbopt2icon');
	if (tbOption.rows.length>1) tbOption.deleteRow(-1);
}

function delopt1part_fashion(idx)
{
	var tbOption = document.getElementById('tbopt1icon');
	if (tbOption.rows.length>1) tbOption.deleteRow(idx-1);
}

function delopt2part_fashion(idx)
{

	var tbOption = document.getElementById('tbopt2icon');
	if (tbOption.rows.length > 1) tbOption.deleteRow(idx - 5);
}

function get_opticon(tbn,idx){
	var r_icon1 = new Array();
	<?
	$i=0;
	foreach ($opt1 as $op1){
	?>
	r_icon1[<?=$i?>] = "<?=$opt1icon[$op1]?>";
	<?
	$i++;
	}
	?>
	var r_icon2 = new Array();
	<?
	$i=0;
	foreach ($opt2 as $op2){
	?>
	r_icon2[<?=$i?>] = "<?=$opt2icon[$op2]?>";
	<?
	$i++;
	}
	?>
	var im  = '';
	if(tbn == 'tbopt1icon'){
		var obj = document.getElementsByName('opt1kind');
		var fi = "opticon_a";
		var bu = "opt1icon";
		 if(r_icon1[idx-1])var im = r_icon1[idx-1];
		 var kind = "<?=$data[opt1kind]?>";
	}else if(tbn == 'tbopt2icon'){
		 var obj = document.getElementsByName('opt2kind');
		 var fi = "opticon_b";
		 var bu = "opt2icon";
		 if(r_icon2[idx-1])var im = r_icon2[idx-1];
		  var kind = "<?=$data[opt2kind]?>";
	}
	var tag = "";
	if(obj[0].checked){
		if(im && kind == 'img') tag = "<input type=checkbox class=\"null\" name=\"del[opticon_a]["+idx+"]\"> <font class=small color=#585858>���� ("+im+") <img src='../../data/goods/"+im+"' width=20 style='border:1 solid #cccccc' onclick=popupImg('../data/goods/"+im+"','../') class=\"hand onerror=this.style.display='none'\" align=\"absmiddle\"></font>";
		var t =  "<input type=\"file\" name=\"" + fi + "[]\" class=\"opt gray\">" + tag;
	}else if(obj[1].checked){
		if(kind != 'color') var im = '';
		var t = "���� �Է� : #<input type=\"text\" name=\"" + bu + "[]\" value=\""+im+"\" size=\"8\" maxlength=\"6\"><a href=\"javascript:poupcolortable("+idx+",'"+bu+"');\"><img src=\"../img/codi/btn_colortable_s.gif\" border=\"0\" alt=\"����ǥ ����\" align=\"absmiddle\"></a>";
	}
	return t;
}

function change_opticon(tbn){
	var tbOption = document.getElementById(tbn);
	var rl = tbOption.rows.length;

	for(var i=0;i < rl;i++){
		tbOption.rows[i].cells[1].innerHTML = get_opticon(tbn,i);
	}
}

</script>
<div style="border-bottom:3px #627dce solid;"></div>
<!-- -->
<div style="padding:10 0 10 0;font:���� 14pt;font-weight:bold;">�� �ɼǺ�1 �̹���/���� ����&nbsp;&nbsp;<font class=extext><input type="radio" name="opt1kind" value="img" class="null" <?=$checked['opt1kind']['img']?> onclick="change_opticon('tbopt1icon')">�̹��� <input type="radio" name="opt1kind" value="color" class="null" <?=$checked['opt1kind']['color']?> onclick="change_opticon('tbopt1icon')">����Ÿ�� ���</font></div>
<table class=tb id="tbopt1icon">
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<?
$i=0;
foreach ($opt1 as $op1){
$i++;
if($data[opt1kind] == 'img'){
?>
<tr id="tropt1icon_<?=$i?>">
	<td>�ɼǸ�1 ������</td>
	<td><input type=file name="opticon_a[]" class="opt gray"><?if($opt1icon[$op1]){?><input type=checkbox class="null" name="del[opticon_a][<?=($i-1)?>]"> <font class=small color=#585858>���� (<?=$opt1icon[$op1]?>) <img src='../../data/goods/<?=$opt1icon[$op1]?>' width=20 style='border:1 solid #cccccc' onclick=popupImg('../data/goods/<?=$opt1icon[$op1]?>','../') class=hand onerror="this.style.display='none'" align="absmiddle"></font><?}?></td>
	<td>��ǰ�̹���</td>
	<td><input type=file name="opt1img[]" class="opt gray"><?if($opt1img[$op1]){?><input type=checkbox class="null" name="del[opt1img][<?=($i-1)?>]"> <font class=small color=#585858>���� (<?=$opt1img[$op1]?>) <img src='../../data/goods/<?=$opt1img[$op1]?>' width=20 style='border:1 solid #cccccc' onclick=popupImg('../data/goods/<?=$opt1img[$op1]?>','../') class=hand onerror="this.style.display='none'" align="absmiddle"></font><?}?></td>
</tr>
<?}else{?>
<tr  id="tropt1icon_<?=$i?>">
	<td>�ɼǸ�1 ����Ÿ��</td>
	<td>���� �Է� : #<input type="text" name="opt1icon[]" value="<?=$opt1icon[$op1]?>" size="8" maxlength="6"><a href="javascript:poupcolortable(<?=($i-1)?>,'opt1icon');"><img src="../img/codi/btn_colortable_s.gif" border="0" alt="����ǥ ����" align="absmiddle"></a></td>
	<td>��ǰ�̹���</td>
	<td><input type=file name="opt1img[]" class="opt gray"><?if($opt1img[$op1]){?><input type=checkbox class="null" name="del[opt1img][<?=($i-1)?>]"> <font class=small color=#585858>���� (<?=$opt1img[$op1]?>) <img src='../../data/goods/<?=$opt1img[$op1]?>' width=20 style='border:1 solid #cccccc' onclick=popupImg('../data/goods/<?=$opt1img[$op1]?>','../') class=hand onerror="this.style.display='none'" align="absmiddle"></font><?}?></td>
</tr>
<?}?>
<?}?>
</table>
<div style="padding:10 0 10 0;color:#5A5A5A;letter-spacing:-1" class="small">����� �������� ���� 40 �ȼ��� �����Ǹ�, ��ǰ�̹����� ���� 500 �� �����˴ϴ�.</font></div>


<div style="padding:10 0 10 0;font:���� 14pt;font-weight:bold;">�� �ɼǺ�2 �̹���/���� ����&nbsp;&nbsp;<font class=extext><input type="radio" name="opt2kind" value="img" class="null" <?=$checked['opt2kind']['img']?> onclick="change_opticon('tbopt2icon')">�̹��� <input type="radio" name="opt2kind" value="color" class="null" <?=$checked['opt2kind']['color']?> onclick="change_opticon('tbopt2icon')">����Ÿ�� ���</font></div>
<table class=tb id="tbopt2icon">
<col class=cellC><col class=cellL>
<?
$i=0;
foreach ($opt2 as $op2){
$i++;
if($data[opt2kind] == 'img'){
?>
<tr id="tropt2icon_<?=$i?>">
	<td>�ɼǸ�2 ������</td>
	<td><input type=file name="opticon_b[]" class="opt gray"><?if($opt2icon[$op2]){?><input type=checkbox class="null" name="del[opticon_b][<?=($i-1)?>]"> <font class=small color=#585858>���� (<?=$opt2icon[$op2]?>) <img src='../../data/goods/<?=$opt2icon[$op2]?>' width=20 style='border:1 solid #cccccc' onclick=popupImg('../data/goods/<?=$opt2icon[$op2]?>','../') class=hand onerror="this.style.display='none'" align="absmiddle"></font><?}?></td>
</tr>
<?}else{?>
<tr  id="tropt2icon_<?=$i?>">
	<td>�ɼǸ�2 ����Ÿ��</td>
	<td>���� �Է� : #<input type="text" name="opt2icon[]" value="<?=$opt2icon[$op2]?>" size="8" maxlength="6"><a href="javascript:poupcolortable(<?=($i-1)?>,'opt2icon');"><img src="../img/codi/btn_colortable_s.gif" border="0" alt="����ǥ ����" align="absmiddle"></a></td>
</tr>
<?}?>
<?}?>
</table>
<div style="padding:10 0 10 0;color:#5A5A5A;letter-spacing:-1" class="small">�ɼ���¹���� �и����� �ƴҰ�� �ɼ�2�� ������ �̹����� ��µ��� ������. ����� �̹���(���� 40�ȼ�)�� �����˴ϴ�.</font></div>
<script>get_opt_title();</script>