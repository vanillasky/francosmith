<?php /* Template_ 2.2.7 2016/01/09 14:02:27 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/goods/goods_review.htm 000008924 */ 
if (is_array($GLOBALS["loop"])) $TPL__loop_1=count($GLOBALS["loop"]); else if (is_object($GLOBALS["loop"]) && in_array("Countable", class_implements($GLOBALS["loop"]))) $TPL__loop_1=$GLOBALS["loop"]->count();else $TPL__loop_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- 상단이미지 || 현재위치 -->
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td><img src="/shop/data/skin/freemart/img/common/title_review.gif" border=0></td>
</tr>
<TR>
	<td class="path">HOME > <B>이용후기</B></td>
</TR>
</TABLE>


<div class="indiv"><!-- Start indiv -->

<form name=frmList>
<input type=hidden name=sort value="<?php echo $_GET['sort']?>">
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
			<option value="contents" <?php echo $GLOBALS["selected"]["skey"]['contents']?>> 후기 </option>
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
<select onchange="this.form.sort.value=this.value;this.form.submit()" style="font:8pt 돋움"><?php if((is_array($TPL_R1=$TPL_VAR["lstcfg"]["sort"])&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_K1=>$TPL_V1){?><option value="<?php echo $TPL_K1?>" <?php echo $GLOBALS["selected"]["sort"][$TPL_K1]?>><?php echo $TPL_V1?><?php }}?></select>
<select onchange="this.form.page_num.value=this.value;this.form.submit()" style="font:8pt 돋움"><?php if((is_array($TPL_R1=$TPL_VAR["lstcfg"]["page_num"])&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><option value="<?php echo $TPL_V1?>" <?php echo $GLOBALS["selected"]["page_num"][$TPL_V1]?>><?php echo $TPL_V1?>개씩 정렬<?php }}?></select>
</div>

<table width="100%" border="0" cellspacing="0" cellpadding="5" style="clear:both;margin-top:5px;border-top-style:solid;border-top-color:#303030;border-top-width:2;border-bottom-style:solid;border-bottom-color:#D6D6D6;border-bottom-width:1;">
<tr height="23" bgcolor="#F0F0F0" class=input_txt>
	<th width=50>번호</th>
	<th width=60>이미지</th>
	<th>상품명/후기</th>
	<th width=80>작성자</th>
	<th width=80>작성일</th>
	<th width=80>평점</th>
</tr>
</table>

<?php if($TPL__loop_1){foreach($GLOBALS["loop"] as $TPL_V1){?>
<div>
<table width=100% cellpadding=3 cellspacing=0 style="border-bottom-style:solid;border-bottom-color:#E6E6E6;border-bottom-width:1;cursor:pointer;" onclick="view_content(this, event)">
<tr height=25 onmouseover=this.style.background="#F7F7F7" onmouseout=this.style.background="">
	<td width=50 align="center"><?php if($TPL_V1["notice"]== 1){?>공지<?php }else{?><?php echo $TPL_V1["idx"]?><?php }?></td>
	<td width=60 align="center"><?php if($TPL_V1["notice"]== 0&&$TPL_V1["goodsno"]){?><a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo goodsimg($TPL_V1["img_s"],$TPL_VAR["lstcfg"]["size"])?></a><?php }?></td>
	<td>
		<TABLE cellpadding=0 cellspacing=0 border=0>
			<TR>
<?php if($TPL_V1["type"]!='Q'&&$TPL_V1["notice"]== 0){?>
				<TD rowspan=2 width="27" valign="top" style="padding-top:3px;"><img src="/shop/data/skin/freemart/img/common/ico_a.gif"></TD>
<?php }?>
				<TD style="padding-top:5"><?php if($TPL_V1["notice"]== 0&&$TPL_V1["goodsno"]){?><span style="font-weight:bold;"><?php echo $TPL_V1["goodsnm"]?></span> <a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>"><img src="/shop/data/skin/freemart/img/common/btn_goodview2.gif" align=absmiddle></a><?php }?></TD>
			</TR>
			<tr><td style="padding-top:5; padding-bottom:5; height:45px" class=stxt><?php echo $TPL_V1["subject"]?></td></tr>
		</TABLE>
	</td>
	<td width=80 align="center"><?php if($TPL_V1["name"]){?><?php echo $TPL_V1["name"]?><?php }elseif($TPL_V1["m_name"]){?><?php echo $TPL_V1["m_name"]?><?php }else{?><?php echo $TPL_V1["m_id"]?><?php }?></td>
	<td width=80 align="center"><?php echo substr($TPL_V1["regdt"], 0, 10)?></td>
	<td width=80>
<?php if($TPL_V1["point"]> 0){?>
<?php if((is_array($TPL_R2=array_fill( 0,$TPL_V1["point"],''))&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>★<?php }}?>
<?php }?>
	</td>
</tr>
</table>
<div style="display:none;padding:10;border-bottom-style:solid;border-bottom-color:#E6E6E6;border-bottom-width:1;">
	<div width="100%" style="padding-left:55"><?php echo $TPL_V1["contents"]?></div>
	<div style="text-align:right;">
<?php if($TPL_V1["authreply"]=='Y'){?>
	<a href="javascript:;" onclick="popup_register( 'reply_review', '<?php echo $TPL_V1["goodsno"]?>', '<?php echo $TPL_V1["sno"]?>' );"><img src="/shop/data/skin/freemart/img/common/btn_reply.gif" border="0" align="absmiddle"></a>
<?php }?>
<?php if($TPL_V1["authmodify"]=='Y'){?>
	<a href="javascript:;" onclick="popup_register( 'mod_review', '<?php echo $TPL_V1["goodsno"]?>', '<?php echo $TPL_V1["sno"]?>' );"><img src="/shop/data/skin/freemart/img/common/btn_modify2.gif" border="0" align="absmiddle"></a>
<?php }?>
<?php if($TPL_V1["authdelete"]=='Y'){?>
	<a href="javascript:;" onclick="popup_register( 'del_review', '<?php echo $TPL_V1["goodsno"]?>', '<?php echo $TPL_V1["sno"]?>' );"><img src="/shop/data/skin/freemart/img/common/btn_delete.gif" border="0" align="absmiddle"></a>
<?php }?>
</div>
</div>
<?php }}?>

<div class="pagediv"><?php echo $TPL_VAR["pg"]->page['navi']?></div>

</form>

	<div style="text-align: right;">
		<img src="/shop/data/skin/freemart/img/common/btn_review.gif" style="cursor: pointer" onclick="popup_register('add_review', null, null);"/>
	</div>

</div><!-- End indiv -->


<script language="javascript">

function popup_register( mode, goodsno, sno )
{
<?php if(empty($GLOBALS["cfg"]['reviewWriteAuth'])&&!$GLOBALS["sess"]){?>
	alert( "회원전용입니다." );
<?php }else{?>
	if ( mode == 'del_review' ) var win = window.open("goods_review_del.php?mode=" + mode + "&sno=" + sno,"qna_register","width=400,height=200");
	else if (parseInt(goodsno) > 0) var win = window.open("goods_review_register.php?mode=" + mode + "&goodsno=" + goodsno + "&sno=" + sno,"qna_register","width=600,height=550");
	else var win = window.open("goods_review_register.php?mode=" + mode + "&sno=" + sno,"qna_register","width=600,height=450");
	win.focus();
<?php }?>
}

var preContent;

function view_content(obj, e)
{
	if ( document.getElementById && ( this.tagName == 'A' || this.tagName == 'IMG' ) ) return;
	else if ( !document.getElementById && ( e.target.tagName == 'A' || e.target.tagName == 'IMG' ) ) return;

	var div = obj.parentNode;

	for (var i=1, m=div.childNodes.length;i<m;i++) {
		if (div.childNodes[i].nodeType != 1) continue;	// text node.
		else if (obj == div.childNodes[ i ]) continue;

		obj = div.childNodes[ i ];
		break;
	}

	if (preContent && obj!=preContent){
		obj.style.display = "block";
		preContent.style.display = "none";
	}
	else if (preContent && obj==preContent) preContent.style.display = ( preContent.style.display == "none" ? "block" : "none" );
	else if (preContent == null ) obj.style.display = "block";

	preContent = obj;
}
</script>

<?php $this->print_("footer",$TPL_SCP,1);?>