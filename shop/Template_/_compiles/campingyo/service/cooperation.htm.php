<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/service/cooperation.htm 000005337 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- 상단이미지 || 현재위치 -->
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td><img src="/shop/data/skin/campingyo/img/common/title_ad.gif" border=0></td>
</tr>
<TR>
	<td class="path">HOME > <B>광고/제휴문의</B></td>
</TR>
</TABLE>


<div class="indiv"><!-- Start indiv -->

<?php if(!$GLOBALS["sess"]&&is_file(sprintf("../skin/%s/service/_private_non.txt",$GLOBALS["cfg"]["tplSkin"]))){?>
<style>
.scroll	{
scrollbar-face-color: #FFFFFF;
scrollbar-shadow-color: #AFAFAF;
scrollbar-highlight-color: #AFAFAF;
scrollbar-3dlight-color: #FFFFFF;
scrollbar-darkshadow-color: #FFFFFF;
scrollbar-track-color: #F7F7F7;
scrollbar-arrow-color: #838383;
}
#boxScroll{width:96%; height:130px; overflow: auto; BACKGROUND: #ffffff; COLOR: #585858; font:9pt 돋움;border:1px #dddddd solid; overflow-x:hidden;text-align:left; }
</style>
<!-- 비회원 개인정보 취급방침 내용 -->
<div><img src="/shop/data/skin/campingyo/img/common/order_txt_non.gif" border=0></div>
<div style="padding-top:10px; background:#F1F1F1;  text-align:center;">
<div align="left" style="height:26;padding:3px 0 0 10px;">
<b>● 비회원 글작성에 대한 개인정보 수집에 대한 동의</b> (자세한 내용은 “<a href="<?php echo url("service/private.php")?>&">개인정보취급방침</a>”을 확인하시기 바랍니다)
</div>
<div id="boxScroll" class="scroll">
<?php echo $this->define('tpl_include_file_1',"/service/_private_non.txt")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>

</div>
<div align=center class=noline style="height:30px;margin-top:10px;" >
<input type="radio" name="private" value="y" onclick="javascript:document.fm.private.value='y';"> 동의합니다 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="private" value="n" onclick="javascript:document.fm.private.value='';"> 동의하지 않습니다
</div>
</div>
<div style="font-size:0;height:5px"></div>
<?php }?>

<form name=fm method=post action="<?php echo $TPL_VAR["cooActionUrl"]?>" onsubmit="return fm_save( this );" id="form">
<input type="hidden" name="mode" value="send">
<?php if(!$GLOBALS["sess"]&&is_file(sprintf("../skin/%s/service/_private_non.txt",$GLOBALS["cfg"]["tplSkin"]))){?>
<input type=hidden name=private value="">
<?php }?>

<div style="border:1px solid #DEDEDE;" class="hundred">
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td style="border:5px solid #F3F3F3; padding:10 0 10 0" align=center>

	<table border="0" cellspacing="0" cellpadding="5" width=95%>
	<tr>
		<col class=input_txt align=right width=100>
		<th>이름</th>
		<td><input type=text name='name' size=15></td>
	</tr>
	<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0px;"></td></tr>
	<tr>
		<th>이메일</th>
		<td><input type=text name=mail size=56></td>
	</tr>
	<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0px;"></td></tr>
	<tr>
		<th>분야</th>
		<td>
		<select name="itemcd" class=select>
		<option selected>------ 문의분야 ------</option>
<?php if((is_array($TPL_R1=codeitem('cooperation'))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_K1=>$TPL_V1){?>
		<option value="<?php echo $TPL_K1?>">↓ <?php echo $TPL_V1?></option>
<?php }}?>
		</select>
		</td>
	</tr>
	<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0px;"></td></tr>
	<tr>
		<th>제목</th>
		<td><input type=text name=title size=56></td>
	</tr>
	<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0px;"></td></tr>
	<tr>
		<th>내용</th>
		<td><textarea cols=75 rows=12 name=content></textarea></td>
	</tr>
	</table>

	</td>
</tr>
</table>
</div>

<div style="padding-top:15;text-align:center" class=noline>
<input type="image" src="/shop/data/skin/campingyo/img/common/btn_send.gif" border=0 align="absmiddle">
<a href="javascript:history.back();"><img src="/shop/data/skin/campingyo/img/common/btn_cancel.gif" align="absmiddle"></a>
</div>
</form>

</div><!-- End indiv -->



<script language=javascript>
<!--
/*-------------------------------------
 등록
-------------------------------------*/
function fm_save( fobj ){

<?php if(!$GLOBALS["sess"]&&is_file(sprintf("../skin/%s/service/_private_non.txt",$GLOBALS["cfg"]["tplSkin"]))){?>
	if ( fobj["private"].value != "y" ){

		alert( "비회원 개인정보 수집에 동의를 하셔야만 글작성이 가능합니다." );
		return false;
	}
<?php }?>

	if ( fobj["name"].value == "" ){

		alert( "이름을 입력하지 않았습니다.\n이름을 입력하세요!!" );
		fobj["name"].focus() ;
		return false;
	}

	if ( fobj["mail"].value == "" ){

		alert( "메일을 입력하지 않았습니다.\n메일을 입력하세요!!" );
		fobj["mail"].focus() ;
		return false;
	}

	if ( fobj["itemcd"].selectedIndex == 0 ){

		alert( "문의분야를 선택하지 않았습니다.\n문의분야를 선택하세요!!" );
		fobj["itemcd"].focus() ;
		return false;
	}

	if ( fobj["title"].value == "" ){

		alert( "제목을 입력하지 않았습니다.\n제목을 입력하세요!!" );
		fobj["title"].focus() ;
		return false;
	}

	if ( fobj["content"].value == "" ){

		alert( "내용을 입력하지 않았습니다.\n내용을 입력하세요!!" );
		fobj["content"].focus() ;
		return false;
	}

	return true;
}
//-->
</script>

<?php $this->print_("footer",$TPL_SCP,1);?>