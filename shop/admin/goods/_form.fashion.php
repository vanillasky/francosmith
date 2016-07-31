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
		if( !tt ) tt='옵션명1';
		var j = i+1;
		if( tt != '옵션명1' ) tt = tt + j;
		tb.rows[i].cells[0].innerHTML = tt  + " 아이콘";
	}

	var tb = document.getElementById('tbopt2icon');
	for(var i=0;i<tb.rows.length;i++){
		var tt = document.getElementsByName('optnm[]')[1] .value;
		if( !tt ) tt='옵션명2';
		var j = i+1;
		if( tt != '옵션명2' ) tt = tt + j;
		tb.rows[i].cells[0].innerHTML = tt  + " 아이콘";
	}
}

function addopt1_fashion(){
	var tbOption = document.getElementById('tbopt1icon');
	var Rcnt = tbOption.rows.length;
	oTr = tbOption.insertRow(-1);
	oTr.height = "35";
	oTr.id = "tropt1icon_" + Rcnt;

	oTd = oTr.insertCell(-1);
	oTd.innerHTML = "옵션명1 아이콘";
	oTd = oTr.insertCell(-1);
	oTd.innerHTML = get_opticon('tbopt1icon',Rcnt);

	oTd = oTr.insertCell(-1);
	oTd.innerHTML = "상품이미지";
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
	oTd.innerHTML = "옵션명2 아이콘";
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
		if(im && kind == 'img') tag = "<input type=checkbox class=\"null\" name=\"del[opticon_a]["+idx+"]\"> <font class=small color=#585858>삭제 ("+im+") <img src='../../data/goods/"+im+"' width=20 style='border:1 solid #cccccc' onclick=popupImg('../data/goods/"+im+"','../') class=\"hand onerror=this.style.display='none'\" align=\"absmiddle\"></font>";
		var t =  "<input type=\"file\" name=\"" + fi + "[]\" class=\"opt gray\">" + tag;
	}else if(obj[1].checked){
		if(kind != 'color') var im = '';
		var t = "색상값 입력 : #<input type=\"text\" name=\"" + bu + "[]\" value=\""+im+"\" size=\"8\" maxlength=\"6\"><a href=\"javascript:poupcolortable("+idx+",'"+bu+"');\"><img src=\"../img/codi/btn_colortable_s.gif\" border=\"0\" alt=\"색상표 보기\" align=\"absmiddle\"></a>";
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
<div style="padding:10 0 10 0;font:돋움 14pt;font-weight:bold;">▼ 옵션별1 이미지/색상 설정&nbsp;&nbsp;<font class=extext><input type="radio" name="opt1kind" value="img" class="null" <?=$checked['opt1kind']['img']?> onclick="change_opticon('tbopt1icon')">이미지 <input type="radio" name="opt1kind" value="color" class="null" <?=$checked['opt1kind']['color']?> onclick="change_opticon('tbopt1icon')">색상타입 사용</font></div>
<table class=tb id="tbopt1icon">
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<?
$i=0;
foreach ($opt1 as $op1){
$i++;
if($data[opt1kind] == 'img'){
?>
<tr id="tropt1icon_<?=$i?>">
	<td>옵션명1 아이콘</td>
	<td><input type=file name="opticon_a[]" class="opt gray"><?if($opt1icon[$op1]){?><input type=checkbox class="null" name="del[opticon_a][<?=($i-1)?>]"> <font class=small color=#585858>삭제 (<?=$opt1icon[$op1]?>) <img src='../../data/goods/<?=$opt1icon[$op1]?>' width=20 style='border:1 solid #cccccc' onclick=popupImg('../data/goods/<?=$opt1icon[$op1]?>','../') class=hand onerror="this.style.display='none'" align="absmiddle"></font><?}?></td>
	<td>상품이미지</td>
	<td><input type=file name="opt1img[]" class="opt gray"><?if($opt1img[$op1]){?><input type=checkbox class="null" name="del[opt1img][<?=($i-1)?>]"> <font class=small color=#585858>삭제 (<?=$opt1img[$op1]?>) <img src='../../data/goods/<?=$opt1img[$op1]?>' width=20 style='border:1 solid #cccccc' onclick=popupImg('../data/goods/<?=$opt1img[$op1]?>','../') class=hand onerror="this.style.display='none'" align="absmiddle"></font><?}?></td>
</tr>
<?}else{?>
<tr  id="tropt1icon_<?=$i?>">
	<td>옵션명1 색상타입</td>
	<td>색상값 입력 : #<input type="text" name="opt1icon[]" value="<?=$opt1icon[$op1]?>" size="8" maxlength="6"><a href="javascript:poupcolortable(<?=($i-1)?>,'opt1icon');"><img src="../img/codi/btn_colortable_s.gif" border="0" alt="색상표 보기" align="absmiddle"></a></td>
	<td>상품이미지</td>
	<td><input type=file name="opt1img[]" class="opt gray"><?if($opt1img[$op1]){?><input type=checkbox class="null" name="del[opt1img][<?=($i-1)?>]"> <font class=small color=#585858>삭제 (<?=$opt1img[$op1]?>) <img src='../../data/goods/<?=$opt1img[$op1]?>' width=20 style='border:1 solid #cccccc' onclick=popupImg('../data/goods/<?=$opt1img[$op1]?>','../') class=hand onerror="this.style.display='none'" align="absmiddle"></font><?}?></td>
</tr>
<?}?>
<?}?>
</table>
<div style="padding:10 0 10 0;color:#5A5A5A;letter-spacing:-1" class="small">썸네일 아이콘은 가로 40 픽셀로 생성되며, 상품이미지는 가로 500 로 생성됩니다.</font></div>


<div style="padding:10 0 10 0;font:돋움 14pt;font-weight:bold;">▼ 옵션별2 이미지/색상 설정&nbsp;&nbsp;<font class=extext><input type="radio" name="opt2kind" value="img" class="null" <?=$checked['opt2kind']['img']?> onclick="change_opticon('tbopt2icon')">이미지 <input type="radio" name="opt2kind" value="color" class="null" <?=$checked['opt2kind']['color']?> onclick="change_opticon('tbopt2icon')">색상타입 사용</font></div>
<table class=tb id="tbopt2icon">
<col class=cellC><col class=cellL>
<?
$i=0;
foreach ($opt2 as $op2){
$i++;
if($data[opt2kind] == 'img'){
?>
<tr id="tropt2icon_<?=$i?>">
	<td>옵션명2 아이콘</td>
	<td><input type=file name="opticon_b[]" class="opt gray"><?if($opt2icon[$op2]){?><input type=checkbox class="null" name="del[opticon_b][<?=($i-1)?>]"> <font class=small color=#585858>삭제 (<?=$opt2icon[$op2]?>) <img src='../../data/goods/<?=$opt2icon[$op2]?>' width=20 style='border:1 solid #cccccc' onclick=popupImg('../data/goods/<?=$opt2icon[$op2]?>','../') class=hand onerror="this.style.display='none'" align="absmiddle"></font><?}?></td>
</tr>
<?}else{?>
<tr  id="tropt2icon_<?=$i?>">
	<td>옵션명2 색상타입</td>
	<td>색상값 입력 : #<input type="text" name="opt2icon[]" value="<?=$opt2icon[$op2]?>" size="8" maxlength="6"><a href="javascript:poupcolortable(<?=($i-1)?>,'opt2icon');"><img src="../img/codi/btn_colortable_s.gif" border="0" alt="색상표 보기" align="absmiddle"></a></td>
</tr>
<?}?>
<?}?>
</table>
<div style="padding:10 0 10 0;color:#5A5A5A;letter-spacing:-1" class="small">옵션출력방식이 분리형이 아닐경우 옵션2의 아이콘 이미지는 출력되지 않으며. 썸네일 이미지(가로 40픽셀)가 생성됩니다.</font></div>
<script>get_opt_title();</script>