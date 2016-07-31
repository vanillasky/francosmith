<?
include "../_header.popup.php";
$tplSkin = $cfg[tplSkin];
$tmp[] = "<img src='../../data/skin/$tplSkin/img/icon/good_icon_new.gif'>";
$tmp[] = "<img src='../../data/skin/$tplSkin/img/icon/good_icon_recomm.gif'>";
$tmp[] = "<img src='../../data/skin/$tplSkin/img/icon/good_icon_special.gif'>";
$tmp[] = "<img src='../../data/skin/$tplSkin/img/icon/good_icon_popular.gif'>";
$tmp[] = "<img src='../../data/skin/$tplSkin/img/icon/good_icon_event.gif'>";
$tmp[] = "<img src='../../data/skin/$tplSkin/img/icon/good_icon_reserve.gif'>";
$tmp[] = "<img src='../../data/skin/$tplSkin/img/icon/good_icon_best.gif'>";
$tmp[] = "<img src='../../data/skin/$tplSkin/img/icon/good_icon_sale.gif'>";

@include "../../conf/my_icon.php";

$r_myicon = isset($r_myicon) ? (array)$r_myicon : array();
for ($i=0;$i<=7;$i++) if (!isset($r_myicon[$i])) $r_myicon[$i] = '';
$cnt_myicon = sizeof($r_myicon);
?>
<script>
	function add_div(){

		var cntIcon = document.getElementsByClassName('c_icon').length;

		if( cntIcon >= 30 ){ alert("아이콘은 30개를 넘을 수 없습니다."); return false;}

		var oRow = document.getElementById('t_icon').insertRow();
		var oRow1 = document.getElementById('t_icon').insertRow();
		var oRow2 = document.getElementById('t_icon').insertRow();
		var oRow3 = document.getElementById('t_icon').insertRow();
		
		oRow.onmouseover=function(){t_icon.clickedRowIndex=this.rowIndex};

		oRow.setAttribute('className','c_icon');

		var oCell1 = oRow.insertCell();
		var oCell2 = oRow.insertCell();
		var oCell3 = oRow.insertCell();
		var oCell4 = oRow.insertCell();

		var oCell5 = oRow1.insertCell();
		var oCell6 = oRow2.insertCell();
		var oCell7 = oRow3.insertCell();

		oCell1.align = "center";
		oCell1.vAlign = "top";
		oCell1.style.cssText = "padding-top:10;";
		oCell1.innerHTML = "<font class=ver7>"+(cntIcon+1)+"</font>";

		oCell2.align = "center";
		oCell2.valign = "top";
		oCell2.style.cssText = "padding-top:10;";
		oCell2.innerHTML = "<font class=ver7>&nbsp;</font>";

		oCell3.align = "left";
		oCell3.innerHTML = "<div style='padding-top:5'><input type=file name='myicon[]'></div><div style='padding-top:3'><font class=small1 color=666666>* 상품등록일로부터 <input type='text' name='myicondt[]' size=4 maxlength=3 value='' onkeydown='onlynumber();'>일까지 아이콘을 노출합니다.</div>";

		oCell4.align = "center";
		oCell4.innerHTML = "<a href='javascript:del_tr();'><img src='../img/i_del.gif'></a>";

		oCell5.height = "4";
		oCell5.colSpan = "12";
		oCell6.colSpan = "12";
		oCell6.bgColor = "#ebebeb";		
		oCell7.height = "4";
		oCell7.colSpan = "12";

	}

	function del_tr(){
		t_icon.deleteRow(t_icon.clickedRowIndex);
		t_icon.deleteRow(t_icon.clickedRowIndex+1);
	}
</script>
<form name=form method=post enctype="multipart/form-data" action="indb.php">
<input type=hidden name=mode value="myicon">

<div class="title title_top">아이콘 설정 하기</div>
<div style='padding : 0 0 0 0' class="extext">* 아이콘 노출을 무제한으로 하고 싶다면  노출기간을 입력하지 마세요.</div>
<div style='padding : 3 0 3 0' class="extext">* 변경 후 쇼핑몰에 적용시키려면 새로고침을 하셔야합니다.</div>

<table width=100% cellpadding=0 cellspacing=0 border=0 id="t_icon">
<tr>
<td class=rnd colspan=12></td>
</tr>
<tr class=rndbg>
	<td align=center><b>번호</b></td>
	<td align=center width=100><b>아이콘</b></td>
	<td align=center><b>등록/수정</b></td>
	<td align=center><b>복구</b></td>
</tr>
<tr><td height=7 colspan=12></td></tr>
<?for($i=0;$i<count($r_myicon);$i++){?>
<tr height=26 class="c_icon">
	<td align=center valign=top style="padding-top:10"><font class=ver7><?=($i+1)?></font></td>
	<td align=center valign=top style="padding-top:10"><?if($r_myicon[$i]){?><img src='../../data/my_icon/<?=$r_myicon[$i]?>'><?}else{?><?=$tmp[$i]?><?}?></td>
	<td align=left>
	<div style="padding-top:5"><input type=file name="myicon[]"></div>
	<div style="padding-top:3"><font class=small1 color=666666>* 상품등록일로부터 <input type="text" name="myicondt[]" size=4 maxlength=3 value="<?=$r_myicondt[$i]?>" onkeydown="onlynumber()">일까지 아이콘을 노출합니다.</div>
	</td>
	<td align=center><?if($i < 8){?><a href='indb.php?mode=iconRecovery&idx=<?=$i?>'><img src="../img/btn_icon_return.gif"></a><?}else{?><a href='indb.php?mode=iconRemove&idx=<?=$i?>'><img src='../img/i_del.gif'></a><?}?></td>
</tr>
<tr><td height=4 colspan=12></td></tr>
<tr><td bgcolor=ebebeb colspan=12></td></tr>
<tr><td height=4 colspan=12></td></tr>
<?}?>
<tr><td height=10 colspan=12></td></tr>
</table>

<div class="noline" align="right"><img src="../img/i_add.gif" onclick="javascript:add_div();"></div>

<div class="button_popup">
<input type=image src="../img/btn_register.gif">
</div>

</form>

<script>table_design_load();</script>