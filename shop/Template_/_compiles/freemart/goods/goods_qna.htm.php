<?php /* Template_ 2.2.7 2014/07/30 21:42:58 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/goods/goods_qna.htm 000007158 */ 
if (is_array($GLOBALS["loop"])) $TPL__loop_1=count($GLOBALS["loop"]); else if (is_object($GLOBALS["loop"]) && in_array("Countable", class_implements($GLOBALS["loop"]))) $TPL__loop_1=$GLOBALS["loop"]->count();else $TPL__loop_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>

<script id="qna_chk"></script>
<script type="text/javascript">
function dynamicScript(url) {
	var script = document.createElement("script");
	script.type = "text/javascript";

	script.onload = script.onreadystatechange = function() {
		if(!script.readyState || script.readyState == "loaded" || script.readyState == "complete"){
			script.onload = script.onreadystatechange = null;
		}
	}

	script.src = url;
	document.getElementsByTagName("head")[0].appendChild(script);
}
function popup_register( mode, goodsno, sno )
{
<?php if(empty($GLOBALS["cfg"]['qnaWriteAuth'])&&!$GLOBALS["sess"]){?>
	alert( "회원전용입니다." );
<?php }else{?>
	if ( mode == 'del_qna' ) var win = window.open("goods_qna_del.php?mode=" + mode + "&sno=" + sno,"qna_register","width=400,height=200");
	else var win = window.open("goods_qna_register.php?mode=" + mode + "&goodsno=" + goodsno + "&sno=" + sno,"qna_register","width=650,height=752,scrollbars=yes");
	win.focus();
<?php }?>
}

function popup_pass(sno){
	var win = window.open("goods_qna_pass.php?sno=" + sno,"qna_register","width=400,height=200");
}

function view_content(sno)
{
	var obj = document.getElementById('content_id_'+sno);
	if(obj.style.display == "none"){
		dynamicScript("./goods_qna_chk.php?mode=view&sno="+sno);
	}else{
		obj.style.display = "none";
	}
}

</script>
<!-- 상단이미지 || 현재위치 -->
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td><img src="/shop/data/skin/freemart/img/common/title_qna.gif" border=0></td>
</tr>
<TR>
	<td class="path">HOME > <B>상품Q&A</B></td>
</TR>
</TABLE>


<div class="indiv"><!-- Start indiv -->

<form name=frmList>
<input type=hidden name=page_num value="<?php echo $_GET['page_num']?>">

<!-- 검색 : Start -->
<div style="border:1px solid #DEDEDE;" class="hundred">
<table width=100% cellpadding=10 cellspacing=0 border=0>
<tr>
	<td style="border:5px solid #F3F3F3;">
	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td><img src="/shop/data/skin/freemart/img/common/search.gif" border=0></td>
		<td style="padding-left:10">
		<table cellpadding="2" cellspacing="0" border="0">
		<tr>
			<td class=input_txt>상품분류</td>
			<td>
			<div id=dynamic></div>
			<script src="/shop/lib/js/categoryBox.js"></script>
			<script>new categoryBox('cate[]',<?php echo cateStep()?>,'<?php echo $GLOBALS["category"]?>','','frmList');</script>
			</td>
		</tr>
		<tr>
			<td class=input_txt>검색어</td>
			<td><select name="skey" class=select>
			<option value="all" <?php echo $GLOBALS["selected"]["skey"]['all']?>> 통합검색 </option>
			<option value="subject" <?php echo $GLOBALS["selected"]["skey"]['subject']?>> 제목 </option>
			<option value="contents" <?php echo $GLOBALS["selected"]["skey"]['contents']?>> 문의 </option>
			<option value="m_id" <?php echo $GLOBALS["selected"]["skey"]['m_id']?>> 작성자 </option>
			<option value="goodnm" <?php echo $GLOBALS["selected"]["skey"]['goodnm']?>> 상품명 </option>
			</select><input type="text" NAME="sword" value="<?php echo $_GET['sword']?>" size=47 class="linebg"><input type="image" src="/shop/data/skin/freemart/img/common/btn_search.gif" align=absmiddle>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
</div>
<!-- 검색 : End -->

<div style="float:right; padding-top:15">
<select onchange="this.form.page_num.value=this.value;this.form.submit()" style="font:8pt 돋움"><?php if((is_array($TPL_R1=$TPL_VAR["lstcfg"]["page_num"])&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><option value="<?php echo $TPL_V1?>" <?php echo $GLOBALS["selected"]["page_num"][$TPL_V1]?>><?php echo $TPL_V1?>개씩 정렬<?php }}?></select>
</div>

<table width="100%" border="0" cellspacing="0" cellpadding="5" style="clear:both;margin-top:5px;border-top-style:solid;border-top-color:#303030;border-top-width:2;border-bottom-style:solid;border-bottom-color:#D6D6D6;border-bottom-width:1;">
<tr height="23" bgcolor="#F0F0F0" class=input_txt>
	<th width=50>번호</th>
	<th width=60>이미지</th>
	<th>상품명/제목</th>
	<th width=80>작성자</th>
	<th width=80>작성일</th>
</tr>
</table>

<?php if($TPL__loop_1){foreach($GLOBALS["loop"] as $TPL_V1){?>
<div>
<table width="100%" cellpadding="0" cellspacing="0" style="border-bottom-style:solid;border-bottom-color:#E6E6E6;border-bottom-width:1;">
<tr height="55" onmouseover="this.style.background='#F7F7F7'" onmouseout="this.style.background=''">
	<td width="50" align="center"><?php echo $TPL_V1["idx"]?></td>
	<td width=60 align="center"><?php if($TPL_V1["goodsno"]&&$TPL_V1["type"]=='Q'){?><a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo goodsimg($TPL_V1["img_s"],$TPL_VAR["lstcfg"]["size"])?></a><?php }?></td>
<?php if($TPL_V1["type"]=='Q'){?>
	<td style="padding-left:0px;cursor:pointer;" onclick="view_content('<?php echo $TPL_V1["sno"]?>')"><div style="background-image: url(/shop/data/skin/freemart/img/common/ico_q.gif); background-repeat:no-repeat;background-position:left 3px;padding:3px 0px 0px 20px;"><?php echo $TPL_V1["subject"]?><?php if($TPL_V1["secretIcon"]){?>&nbsp;<img src="/shop/data/skin/freemart/img/common/icn_secret.gif" align=absmiddle><?php }?></div></td>
<?php }elseif($TPL_V1["type"]=='A'){?>
	<td style="padding-left:5px;cursor:pointer;" onclick="view_content('<?php echo $TPL_V1["sno"]?>')"><div style="background-image: url(/shop/data/skin/freemart/img/common/ico_a.gif); background-repeat:no-repeat;background-position:left 3px;padding:3px 0px 0px 27px;"> <?php echo $TPL_V1["subject"]?><?php if($TPL_V1["secretIcon"]){?>&nbsp;<img src="/shop/data/skin/freemart/img/common/icn_secret.gif" align=absmiddle><?php }?></div></td>
<?php }elseif($TPL_V1["type"]=='N'){?>
	<td style="padding-left:5px;cursor:pointer;" onclick="view_content('<?php echo $TPL_V1["sno"]?>')"><?php echo $TPL_V1["subject"]?><?php if($TPL_V1["secretIcon"]){?>&nbsp;<img src="/shop/data/skin/freemart/img/common/icn_secret.gif" align=absmiddle><?php }?></div></td>
<?php }?>
	<td width="80"><?php if($TPL_V1["name"]){?><?php echo $TPL_V1["name"]?><?php }elseif($TPL_V1["m_name"]){?><?php echo $TPL_V1["m_name"]?><?php }else{?><?php echo $TPL_V1["m_id"]?><?php }?></td>
	<td width="80"><?php echo substr($TPL_V1["regdt"], 0, 10)?></td>
</tr>
</table>
</div>
<div id="content_id_<?php echo $TPL_V1["sno"]?>" style="display:none"></div>
<?php }}?>

<div class="pagediv"><?php echo $TPL_VAR["pg"]->page['navi']?></div>

</form>

<div style="float:right;padding:10px 5px">
<a href="javascript:;" onclick="popup_register( 'add_qna', '' )"><img src="/shop/data/skin/freemart/img/common/btn_qna.gif"></a>
</div>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>