<?php /* Template_ 2.2.7 2014/03/05 23:19:27 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/goods/goods_review_register.htm 000010113 */ 
if (is_array($GLOBALS["file_arr"])) $TPL__file_arr_1=count($GLOBALS["file_arr"]); else if (is_object($GLOBALS["file_arr"]) && in_array("Countable", class_implements($GLOBALS["file_arr"]))) $TPL__file_arr_1=$GLOBALS["file_arr"]->count();else $TPL__file_arr_1=0;?>
<html>
<head>
<title>상품사용기작성</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<script src="/shop/data/skin/campingyo/common.js"></script>
<link rel="styleSheet" href="/shop/data/skin/campingyo/style.css">
<script language="javascript">
function setWindowResize() {
	var thisX = parseInt(document.getElementById("table_after").scrollWidth);
	var thisY = parseInt(document.getElementById("table_after").scrollHeight);
	var maxThisX = screen.width - 50;
	var maxThisY = screen.height - 50;
	var marginY = 0;

	if (navigator.userAgent.indexOf("MSIE 6") > 0) marginY = 45;        // IE 6.x
	else if(navigator.userAgent.indexOf("MSIE 7") > 0) marginY = 75;    // IE 7.x
	else if(navigator.userAgent.indexOf("MSIE 9") > 0) marginY = 80;    // IE 9.x
	else if(navigator.userAgent.indexOf("rv:11") > 0) marginY = 70;    // IE 11.x
	else if(navigator.userAgent.indexOf("Firefox") > 0) marginY = 80;   // FF
	else if(navigator.userAgent.indexOf("Opera") > 0) marginY = 30;     // Opera
	else if(navigator.userAgent.indexOf("Chrome") > 0) marginY = 70;     // Chrome
	else if(navigator.userAgent.indexOf("Netscape") > 0) marginY = -2;  // Netscape

	if (thisX > maxThisX) {
		window.document.body.scroll = "yes";
		thisX = maxThisX;
	}
	if (thisY > maxThisY - marginY) {
		window.document.body.scroll = "yes";
		thisX += 19;
		thisY = maxThisY - marginY;
	}
	window.resizeTo(thisX+20, thisY+marginY);
}
function add(){
	var table = document.getElementById('table');
	var reviewFileNum = "<?php echo $GLOBALS["reviewFileNum"]?>";
	if (table.rows.length>=parseInt(reviewFileNum)){
		alert("업로드는 최대 "+reviewFileNum+"개만 지원합니다");
		return;
	}
	var tr_num = table.rows.length;
	oTr		= table.insertRow( table.rows.length );
	oTr.id	= "tr_"+(tr_num);
	oTd1		= oTr.insertCell(0);
	oTd1.style.textAlign = "center";
	oTd2		= oTr.insertCell(1);
	tmpHTML = "<input type=file name='file[]' style='width:50%' class=line> <a href=\"javascript:del('"+"tr_"+(tr_num)+"')\"><img src='/shop/data/skin/campingyo/img/common/btn_upload_minus.gif' align=absmiddle></a>";
	oTd2.innerHTML = tmpHTML;
	calcul();
	setWindowResize();
}
function del(index,ncode)
{
<?php if($GLOBALS["mode"]=='mod_review'&&$GLOBALS["file_arr"]){?>
	index_sp = index.split("_");
	if(ncode == 1){
		if(!confirm("저장시 등록된 이미지가 삭제 됩니다.")){
			return;
		}
		del_file = document.createElement("input");
		del_file.name = "del_file["+index_sp[1]+"]";
		del_file.value = "on";
		document.getElementById("form_review").appendChild(del_file);
	}
<?php }?>
	var table = document.getElementById('table');
	for (i=0;i<table.rows.length;i++) if (index==table.rows[i].id) table.deleteRow(i);
	calcul();
	setWindowResize();
}

function calcul()
{
	var table = document.getElementById('table');
	for (i=0;i<table.rows.length;i++){
		table.rows[i].cells[0].innerHTML = i+1;
	}
}
</script>
</head>
<body onLoad="setWindowResize();">

<form name="form_review" id="form_review" method=post action="<?php echo url("goods/indb.php")?>&" enctype="multipart/form-data" onSubmit="return chkForm(this)">
<input type=hidden name=mode value="<?php echo $GLOBALS["mode"]?>">
<input type=hidden name=goodsno value="<?php echo $GLOBALS["goodsno"]?>">
<input type=hidden name=sno value="<?php echo $GLOBALS["sno"]?>">
<input type=hidden name=referer value="<?php echo $GLOBALS["referer"]?>">

<table id="table_after" width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td style="border-style:solid; border-width:10px; border-color:#000000" valign=top>

	<div style="width:100%; background:#000000; border-bottom:2px solid #DDDDDD"><img src="/shop/data/skin/campingyo/img/common/popup_title_review.gif"></div>

	<div style="margin-left:20px; margin-right:20px; margin-top: 10px;">
<?php if($GLOBALS["goods"]){?>
	<div style="border-width:5; border-style:solid; border-color:#EFEEEE;" class="hundred">
	<table width=500 cellpadding=0 cellspacing=0 border=0 align="center" style="margin-top:5; margin-bottom:5">
	<tr>
		<td width=50><?php echo goodsimg($GLOBALS["goods"]["img_s"], 50)?></td>
		<td style="line-height:20px;padding-left:10">
		<b><?php echo $GLOBALS["goods"]["goodsnm"]?></b><br>
		<?php echo number_format($GLOBALS["goods"]["price"])?>원
		</td>
	</tr>
	</table>
	</div>
<?php }?>

	<div style="border-width:1px; border-style:solid; border-color:#DEDEDE; margin-top:5px;" class="hundred">
	<table width=100% cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td style="border-width:3px; border-style:solid; border-color:#F3F3F3; padding:5 5 5 5">

		<table width=500 id=form cellpadding=5 cellspacing=0 border=0 align=center>
		<col width=50>
<?php if($GLOBALS["mode"]!='reply_review'){?>
		<tr>
			<td class="input_txt" align=right>평가</td>
			<td class="noline">
			<input type=radio name=point value=5 class=noline <?php echo $GLOBALS["data"]["point"]['5']?>>★★★★★
			<input type=radio name=point value=4 class=noline <?php echo $GLOBALS["data"]["point"]['4']?>>★★★★☆
			<input type=radio name=point value=3 class=noline <?php echo $GLOBALS["data"]["point"]['3']?>>★★★☆☆
			<input type=radio name=point value=2 class=noline <?php echo $GLOBALS["data"]["point"]['2']?>>★★☆☆☆
			<input type=radio name=point value=1 class=noline <?php echo $GLOBALS["data"]["point"]['1']?>>★☆☆☆☆
			</div>
			</td>
		</tr>
<?php }?>
		<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0px;"></td></tr>
		<tr>
			<td class="input_txt" align=right>작성자</td>
			<td>
			<div style="float:left; width:50%;"><input type=text name=name style="width:100" required fld_esssential label="작성자" value="<?php echo $GLOBALS["data"]["name"]?>"></div>
<?php if(!$GLOBALS["sess"]&&empty($GLOBALS["data"]['m_no'])){?>
			<div style="float:left; width:50%;"><span class="input_txt">비밀번호</span> <input type=password name=password style="width:100" required fld_esssential label="비밀번호"></div>
<?php }?>
			</td>
		</tr>
		<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0px;"></td></tr>
		<tr>
			<td class="input_txt" align=right>제목</td>
			<td><input type=text name=subject style="width:95%" required fld_esssential label="제목" value="<?php echo $GLOBALS["data"]["subject"]?>"></td>
		</tr>
		<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0px;"></td></tr>
		<tr>
			<td class="input_txt" align=right>내용</td>
			<td><textarea name=contents style="width:95%;height:120" required fld_esssential label="내용"><?php echo $GLOBALS["data"]["contents"]?></textarea></td>
		</tr>
<?php if($GLOBALS["cfg"]["reviewSpamBoard"]& 2){?>
		<tr>
			<td align=right class=input_txt>자동등록방지</td>
			<td class=cell_L><?php echo $this->define('tpl_include_file_1',"proc/_captcha.htm")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?></td>
		</tr>
<?php }?>
		<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0px;"></td></tr>
		<tr>
			<td align=right class=input_txt>이미지</td>
			<td class=cell_L>
			<table width=95% id=table cellpadding=0 cellspacing=0 border="0" style="border:solid 1px #CCC; border-collapse:collapse;">
<?php if($GLOBALS["mode"]=='mod_review'&&$GLOBALS["file_arr"]){?>
<?php if($TPL__file_arr_1){$TPL_I1=-1;foreach($GLOBALS["file_arr"] as $TPL_V1){$TPL_I1++;?>
			<tr id="tr_<?php echo $TPL_I1?>">
				<td width=20 nowrap align="center"><?php echo $TPL_I1+ 1?></td>
				<td width=100%>
				<input type=file name="file[]" style="width:50%" class=linebg>
<?php if($GLOBALS["reviewFileNum"]> 1){?>
<?php if($TPL_I1== 0){?>
					<a href="javascript:add()"><img src="/shop/data/skin/campingyo/img/common/btn_upload_plus.gif" align=absmiddle></a>
<?php }else{?>
<?php if($TPL_V1){?>
					<a href="javascript:del('tr_<?php echo $TPL_I1?>',1)"><img src="/shop/data/skin/campingyo/img/common/btn_upload_minus.gif" align=absmiddle></a>
<?php }else{?>
					<a href="javascript:del('tr_<?php echo $TPL_I1?>')"><img src="/shop/data/skin/campingyo/img/common/btn_upload_minus.gif" align=absmiddle></a>
<?php }?>
<?php }?>
<?php }?>
<?php if($TPL_V1){?>
				<?php echo $TPL_V1?>

<?php }?>
				</td>
			</tr>
<?php }}?>
<?php }else{?>
			<tr id="tr_0">
				<td width=20 nowrap align="center">1</td>
				<td width=100%>
				<input type=file name="file[]" style="width:50%" class=linebg>
<?php if($GLOBALS["reviewFileNum"]> 1){?>
				<a href="javascript:add()"><img src="/shop/data/skin/campingyo/img/common/btn_upload_plus.gif" align=absmiddle></a>
<?php }?>
				</td>
			</tr>
<?php }?>
			</table>
			<table><tr><td height=2></td></tr></table>
			<div width=100% style="padding:5;" class=stxt>
			- 파일은 최대 <?php echo $GLOBALS["reviewFileNum"]?>개까지 업로드가 지원됩니다.<br>
<?php if($GLOBALS["cfg"]["reviewLimitPixel"]){?>- 파일은 가로 사이즈가 <?php echo number_format($GLOBALS["cfg"]["reviewLimitPixel"])?>px보다 클 경우 자동 리사이즈 됩니다.<br><?php }?>
<?php if($GLOBALS["cfg"]["reviewFileSize"]){?>- 파일은 장당 최대 <?php echo $GLOBALS["cfg"]["reviewFileSize"]?>KB를 넘을 수 없습니다.<br><?php }?>
			</div>
			</td>
		</tr>
		</table>

		</td>
	</tr>
	</table>
	</div>
	</div>

	<TABLE width=100%>
	<tr>
		<td align=center style="padding-top:5"><input type="image" src="/shop/data/skin/campingyo/img/common/btn_upload.gif"></td>
	</tr>
	</TABLE>
	<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
	<TR>
		<TD align=right><A HREF="javascript:this.close()" onFocus="blur()"><img src="/shop/data/skin/campingyo/img/common/popup_close.gif"></A></TD>
	</TR>
	</TABLE>
	</td>
</tr>
</table>

</form>

</body>
</html>