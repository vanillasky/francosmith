<?php /* Template_ 2.2.7 2014/07/30 21:43:01 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/mypage/mypage_qna_register.htm 000005731 */ ?>
<html>
<head>
<title>1:1 문의작성</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<script src="/shop/data/skin/freemart/common.js"></script>
<link rel="styleSheet" href="/shop/data/skin/freemart/style.css">
</head>
<body>

<form name=fm method=post action="<?php echo $TPL_VAR["myqnaActionUrl"]?>" onSubmit="return chkForm(this)">
<input type=hidden name=mode value="<?php echo $GLOBALS["mode"]?>">
<input type=hidden name=itemcd value="<?php echo $GLOBALS["itemcd"]?>">
<input type=hidden name=sno value="<?php echo $GLOBALS["sno"]?>">


<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td height=500 style="border:10px solid #000000" valign=top>

	<div style="width:100%; background:#000000; border-bottom:2px solid #DDDDDD"><img src="/shop/data/skin/freemart/img/common/popup_title_mantoman.gif"></div>
	<div><img src="/shop/data/skin/freemart/img/common/space.gif" width=5 height=10></div>


	<CENTER>
	<div><img src="/shop/data/skin/freemart/img/common/space.gif" width=5 height=5></div>


	<div style="width:540; border:1px solid #DEDEDE;">
	<table width=100% cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td style="border:3px solid #F3F3F3; padding:5 5 5 5" align=center>

		<table width=500 id=form cellpadding=5 cellspacing=0 border=0>
		<col width=14% align=right>
		<tr>
			<td class="input_txt">아이디</td>
			<td><?php echo $GLOBALS["data"]["m_id"]?></td>
		</tr>
		<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0px;"></td></tr>

<?php if($GLOBALS["formtype"]!='reply'){?>
		<tr>
			<td class="input_txt">질문유형</td>
			<td><select name="itemcd" required fld_esssential label="질문유형" class=select>
			<option value="">↓상담내용을 선택하세요.</option>
<?php if((is_array($TPL_R1=codeitem('question'))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_K1=>$TPL_V1){?>
			<option value="<?php echo $TPL_K1?>" <?php if($GLOBALS["data"]["itemcd"]==$TPL_K1){?>selected<?php }?>><?php echo $TPL_V1?></option>
<?php }}?>
			</select></td>
		</tr>
		<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0px;"></td></tr>
		<tr>
			<td class="input_txt">주문번호</td>
			<td>
			<input type=text name=ordno style="width:25%" value="<?php echo $GLOBALS["data"]["ordno"]?>"> <a href="javascript:order_open();"><img src="/shop/data/skin/freemart/img/common/btn_inquiry_order.gif" align=absmiddle></a>

			</td>
		</tr>
		<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0px;"></td></tr>
		<tr>
			<td class="input_txt">이메일</td>
			<td><input type=text name=email value="<?php echo $GLOBALS["data"]["email"]?>" size=26>
			<span class=noline style="padding-left:10px"><input type=checkbox name=mailling <?php if($GLOBALS["data"]["mailling"]=='y'){?>checked<?php }?>><span style="font:8pt 돋움;color:#007FC8" >받습니다</span></span>
			</td>
		</tr>
		<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0px;"></td></tr>
		<tr>
			<td class="input_txt">문자메시지</td>
			<td>
			<input type=text name=mobile[] value="<?php echo $GLOBALS["data"]["mobile"][ 0]?>" size=4 maxlength=4> -
			<input type=text name=mobile[] value="<?php echo $GLOBALS["data"]["mobile"][ 1]?>" size=4 maxlength=4> -
			<input type=text name=mobile[] value="<?php echo $GLOBALS["data"]["mobile"][ 2]?>" size=4 maxlength=4>
			<span class=noline style="padding-left:10px"><input type=checkbox name=sms <?php if($GLOBALS["data"]["sms"]=='y'){?>checked<?php }?>><span style="font:8pt 돋움;color:#007FC8" >받습니다</span></span>
			</td>
		</tr>
		<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0px;"></td></tr>
<?php }?>

		<tr>
			<td class="input_txt">제목</td>
			<td><input type=text name=subject style="width:100%" required fld_esssential label="제목" value="<?php echo $GLOBALS["data"]["subject"]?>"></td>
		</tr>
		<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0px;"></td></tr>
		<tr>
			<td class="input_txt">내용</td>
			<td>

<?php if($GLOBALS["formtype"]!='reply'){?>
			<textarea name=contents style="width:100%;height:140" required fld_esssential label="내용"><?php echo $GLOBALS["data"]["contents"]?></textarea>
<?php }else{?>
			<textarea name=contents style="width:100%;height:260" required fld_esssential label="내용"><?php echo $GLOBALS["data"]["contents"]?></textarea>
<?php }?>

			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</div>

	<TABLE width=100%>
	<tr>
		<td align=center style="padding-top:5"><input type="image" src="/shop/data/skin/freemart/img/common/btn_upload.gif"></td>
	</tr>
	</TABLE>
	<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
	<TR>
		<TD align=right><A HREF="javascript:this.close()" onFocus="blur()"><img src="/shop/data/skin/freemart/img/common/popup_close.gif"></A></TD>
	</TR>
	</TABLE>


	</td>
</tr>
</table>

</form>


<iframe id=ifm_order frameborder=0 scrolling=no style="display:none; background-color:#ffffff; border-style:solid; border-width:1; border-color:#000000;"></iframe>
<script language="javascript">
function order_open(){
	var divEl = document.getElementById('ifm_order');
	divEl.style.display = "block";
	divEl.style.left = 20;
	divEl.style.top = 165;
	divEl.style.width = 560;
	divEl.style.height = 280;
	divEl.style.position = "absolute";
	if( divEl.src == '' ) divEl.src = "mypage_qna_order.php";
}

function order_close(){
	var divEl = document.getElementById('ifm_order');
	divEl.style.display = "none";
}

function order_put( ordno ){
	document.fm.ordno.value = ordno;
	order_close();
}
</script>


</body>
</html>