<?
include "../_header.popup.php";
@include_once "../../conf/config.mobileShop.php";

$data = $db->fetch("select * from ".GD_TODAYSHOP_CATEGORY." where category='$_GET[category]'",1);
list($cntGoods) = $db->fetch("select count(distinct tgsno) from ".GD_TODAYSHOP_LINK." where category like '$data[category]%'");

$checked['tpl'][$lstcfg['tpl']] = "checked";
$checked['rtpl'][$lstcfg['rtpl']] = "checked";
$checked['hidden'][$data['hidden']] = "checked";
$checked['hidden_mobile'][$data['hidden_mobile']] = "checked";
$selected['level'][$data['level']] = "selected";

### 그룹정보 가져오기
$res = $db->query("select * from gd_member_grp order by level");
while($tmp = $db->fetch($res))$r_grp[] = $tmp;
unset($res);

### 기존 분류이미지
if($data[useimg])$imgName = getCategoryImgTS($_GET[category]);

$curPos = '전체분류';
if ($_GET['category']) $curPos = currPositionTS($_GET['category']);
?>

<style>
body {margin:0}
</style>
<script>
/*** 관련상품 ***/
function open_box(name,isopen)
{
	var mode;
	var isopen = (isopen || document.getElementById('obj_'+name).style.display!="block") ? true : false;
	mode = (isopen) ? "block" : "none";
	document.getElementById('obj_'+name).style.display = document.getElementById('obj2_'+name).style.display = mode;
	if (document.getElementById('obj_'+name).style.display!="block") iciRow = null;
}
function list_goods(name)
{
	var category = '';
	open_box(name,true);
	var els = document.forms[0][name+'[]'];
	for (i=0;i<els.length;i++) if (els[i].value) category = els[i].value;
	var ifrm = document.getElementById('ifrm_' + name);
	var goodsnm = eval("document.forms[0].search_" + name + ".value");
	ifrm.src = "_goodslist.php?name=" + name + "&category=" + category + "&goodsnm=" + goodsnm;
}
function go_list_goods(name){
	if (event.keyCode==13){
		list_goods(name);
		return false;
	}
}
function view_goods(name)
{
	open_box(name,false);
}
function moveEvent(obj, name)
{
	obj.onclick = function(){ spoit(name,this); }
	obj.ondblclick = function(){ remove(name,this); }
}
function remove(name,obj)
{
	var tb = document.getElementById('tb_'+name);
	tb.deleteRow(obj.rowIndex);
	react_goods(name);
}

function react_goods(name)
{
	var tmp = new Array();
	var obj = document.getElementById('tb_'+name);
	for (i=0;i<obj.rows.length;i++){
		tmp[tmp.length] = "<div style='float:left;border:1px solid #cccccc;margin:1px;' title='" + obj.rows[i].cells[1].getElementsByTagName('div')[0].innerHTML + "'>" + obj.rows[i].cells[0].innerHTML + "</div>";
	}
	document.getElementById(name+'X').innerHTML = tmp.join("") + "<div style='clear:both'>";
	parent.document.getElementById('ifrmCategory').style.height = document.body.scrollHeight;
}

var iciRow, preRow, nameObj;
function spoit(name,obj)
{
	nameObj = name;
	iciRow = obj;
	iciHighlight();
}
function iciHighlight()
{
	if (preRow) preRow.style.backgroundColor = "";
	iciRow.style.backgroundColor = "#FFF4E6";
	preRow = iciRow;
}
function moveTree(idx)
{
	if (document.getElementById("obj_"+nameObj).style.display!="block") return;
	var objTop = iciRow.parentNode.parentNode;
	var nextPos = iciRow.rowIndex+idx;
	if (nextPos==objTop.rows.length) nextPos = 0;

	var cln1 = iciRow.cells[0].cloneNode(true);
	var cln2 = iciRow.cells[1].cloneNode(true);
	objTop.deleteRow(iciRow.rowIndex);
	oTr = objTop.insertRow(nextPos);
	oTd = oTr.appendChild(cln1);
	oTd = oTr.appendChild(cln2);
	oTr.className = "hand";
	oTr.onclick = function(){ spoit(nameObj,this); }
	oTr.ondblclick = function(){ remove(nameObj,this); }

	iciRow = oTr;
	iciHighlight();
	react_goods(nameObj);
}
function keydnTree(e)
{
	if (iciRow==null) return;
	e = e ? e : event;
	switch (e.keyCode){
		case 38: moveTree(-1); break;
		case 40: moveTree(1); break;
	}
	return false;
}
document.onkeydown = keydnTree;
</script>

<form name=form method=post action="indb.category.php" onsubmit="return chkForm(this)" enctype="multipart/form-data">
<input type=hidden name=mode value="mod_category">
<input type=hidden name=category value="<?=$_GET[category]?>">

<div class="title_sub" style="margin:0">분류만들기/수정/삭제<span>분류명을 생성하고 수정, 삭제합니다. <font class=extext>(입력후 반드시 아래 수정버튼을 누르세요)</font></span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=10')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tbody style="height:26px">
<tr>
	<td>현재분류</td>
	<td>
	<?=$curPos;?>
	<a href="../../todayshop/today_list.php?category=<?=$_GET[category]?>" target=_blank><img src="../img/i_nowview.gif" border=0 align=absmiddle hspace=10></a>
	</td>
</tr>
<tr>
	<td>이 분류의 상품수</td>
	<td><b><?=number_format($cntGoods)?></b>개가 등록되어 있습니다. <font class=extext>(하위분류까지 포함)</font></td>
</tr>
<? if ($_GET[category]){ ?>
<tr>
	<td>현재분류명 수정</td>
	<td>
	<input type=text name=catnm class=lline required value="<?=$data[catnm]?>" label="현재분류명" maxlen="100">
	&nbsp; 분류코드 : <b><?=$data[category]?></b>
	</td>
</tr>
<? if ( !preg_match( "/^rental_mxfree/i", $godo[ecCode] ) ) { ?>
<tr>
	<td>분류감추기</td>
	<td class=noline>
<? if (getCateHideCntTS(substr($data[category],0,-3))){ ?>
	<input type=hidden name=hidden value='<?=$data[hidden]?>'> <font class=small1 color=E83700>상위분류가 감춤이므로 자동감춤 <font color=0074BA>(이 분류를 보이게 하려면 먼저, 상위분류를 보이는 상태로 바꾸고나서 변경하세요)</font>
<? } else { ?>
	<input type=radio name=hidden value=1 <?=$checked[hidden][1]?>> 감추기
	<input type=radio name=hidden value=0 <?=$checked[hidden][0]?>> 보이기
<? } ?>
	</td>
</tr>
<? } ?>
<!--모바일샵에서 감추기-->
<input type=hidden name=hidden_mobile value="<?php echo $data['hidden'];?>" />
<!--
<tr>
	<td>모바일샵에서 감추기</td>
	<td class=noline>
		<?php if($cfgMobileShop['vtype_category']=='1'){?>
			<? if (getCateHideCntTS(substr($data[category],0,-3),'mobile')){ ?>
				<input type=hidden name=hidden_mobile value='<?=$data[hidden_mobile]?>'> <font class=small1 color=E83700>상위분류가 감춤이므로 자동감춤 <font color=0074BA>(이 분류를 보이게 하려면 먼저, 상위분류를 보이는 상태로 바꾸고나서 변경하세요)</font>
			<? } else { ?>
				<input type=radio name=hidden_mobile value=1 <?=$checked[hidden_mobile][1]?>> 감추기
				<input type=radio name=hidden_mobile value=0 <?=$checked[hidden_mobile][0]?>> 보이기
			<? } ?>
		<?php }else{?>
			<input type=hidden name=hidden_mobile value="<?php echo $data['hidden'];?>" />
		<font class="red">위의 분류감추기와 동일하게 적용되도록 설정되어있습니다.</font>
		<?php }?>
	</td>
</tr>
-->
<? } ?>
<? if (strlen($_GET[category])<=9){ ?>
<tr>
	<td>하위분류 만들기</td>
	<td><input type=text name=sub  label="하위분류생성" maxlen="30" class="line"> <font class=extext>현재분류의 하위분류를 생성합니다</font></td>
</tr>
<? } ?>
<?if($_GET[category]){?>
<tr>
	<td>접근권한</td>
	<td>
	<select name="level">
		<option value="">제한없음</option>
		<?
		foreach($r_grp as $k => $v){
		?>
		<option value="<?=$v[level]?>" <?=$selected['level'][$v['level']]?>><?=$v[grpnm]?> - lv[<?=$v[level]?>]</option>
		<?
		}
		?>
	</select> 이상의 그룹에게만 접근을 허용합니다.
	</td>
</tr>
<tr>
	<td>분류삭제</td>
	<td><a href="javascript:if (document.form.category.value) parent.popupLayer('popup.delCategory.php?category='+document.form.category.value);else alert('전체분류는 삭제대상이 아닙니다');"><img src="../img/i_del.gif" border=0 align=absmiddle></a> <font class=extext>분류삭제시 하위분류도 함께 삭제됩니다. 신중히 삭제하세요.</font></td>
</tr>
<? } ?>
</table>

<div class="button"><input type=image src="../img/btn_modify.gif"></div>

</form>

<div id=MSG01>
<table class="small_ex">
<tr><td>
<img src="../img/icon_list.gif" align=absmiddle>상품분류탐색기에서 1차분류만들기 (최상위분류)를 누르면 1차분류를 생성할 수 있습니다.<br>
<img src="../img/icon_list.gif" align=absmiddle>분류페이지상단에서 이벤트나 배너를 배치하여 차별화될 수 있게 디자인해보세요.<br>
<img src="../img/icon_list.gif" align=absmiddle>분류순서변경은 해당분류를 선택후 키보드의 상하이동키↓↑로 조정하고 수정을 눌러 저장합니다.
</table>
</div>

<script>cssRound('MSG01')</script>

<script>
table_design_load();
window.onload = function(){
	parent.document.getElementById('ifrmCategory').style.height = document.body.scrollHeight;
}
<? if ($_GET[focus]=="sub"){ ?>
if (document.forms[0].sub) document.forms[0].sub.focus();
<? } ?>
</script>