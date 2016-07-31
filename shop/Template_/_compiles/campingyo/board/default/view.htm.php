<?php /* Template_ 2.2.7 2014/03/05 23:19:27 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/board/default/view.htm 000007982 */ 
if (is_array($TPL_VAR["loop"])) $TPL_loop_1=count($TPL_VAR["loop"]); else if (is_object($TPL_VAR["loop"]) && in_array("Countable", class_implements($TPL_VAR["loop"]))) $TPL_loop_1=$TPL_VAR["loop"]->count();else $TPL_loop_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?> <?php echo $GLOBALS["bdHeader"]?>


<table width=<?php echo $GLOBALS["bdWidth"]?> align=<?php echo $GLOBALS["bdAlign"]?> cellpadding=0 cellspacing=0><tr><td style="padding-top:20px">

<?php if($TPL_loop_1){foreach($TPL_VAR["loop"] as $TPL_V1){?>

<table width=100% cellpadding=0 cellspacing=0>
<tr><td height=2 bgcolor="#303030"></td></tr>
<tr>
	<td bgcolor=#ECECEC height=27 style="padding:0 10">
	<table width=100%>
	<tr height=21>
		<td><?php if($GLOBALS["bdUseSubSpeech"]&&$TPL_V1["category"]){?><b>[<?php echo $TPL_V1["category"]?>]</b> <?php }?><b><?php echo $TPL_V1["subject"]?></b></td>
<?php if(!($GLOBALS["bdField"]& 8)){?>
		<td align=right><?php echo $TPL_V1["name"]?></td>
<?php }?>
	</tr>
	</table>
	</td>
</tr>
<tr><td height=1 bgcolor="#CAC9C9"></td></tr>
<?php if($TPL_V1["urlLink"]){?>
<tr>
	<td class=eng style="padding:5">
	LINK : <a href="<?php echo $TPL_V1["urlLink"]?>" target=_blank><?php echo $TPL_V1["urlLink"]?></a>
	</td>
</tr>
<tr><td height=1 bgcolor=#efefef></td></tr>
<?php }?>
<?php if($TPL_V1["uploadedFile"]){?>
<tr>
	<td class=eng style="padding:5">
	FILE : <?php echo $TPL_V1["uploadedFile"]?>

	</td>
</tr>
<tr><td height=1 bgcolor=#efefef></td></tr>
<?php }?>
<tr>
	<td align=right class=eng style="padding:5">
	Posted at <?php echo $TPL_V1["regdt"]?>

<?php if(($TPL_V1["ip"]&&($GLOBALS["bdIp"]||$GLOBALS["ici_admin"]))){?>
	/ IP <?php echo $TPL_V1["ip"]?>

<?php }?>
<?php if($TPL_V1["email"]&&$GLOBALS["ici_admin"]){?>
	<div><?php echo $TPL_V1["email"]?></div>
<?php }?>
	</td>
</tr>
<tr>
	<td style="padding:10" height=200 valign=top id=contents>

	<table width=100% style="table-layout:fixed">
	<tr>
		<td style="word-wrap:break-word;word-break:break-all" id=contents_<?php echo $TPL_V1["no"]?> valign=top></td>
	</tr>
	</table>

	</td>
</tr>
<tr><td height=10></td></tr>
<tr><td height=1 bgcolor="#E0DFDF"></td></tr>
<tr><td height=3 bgcolor="#F7F7F7"></td></tr>
</table><br>

<table width=100% style="table-layout:fixed" cellpadding=0 cellspacing=0>
<?php if($GLOBALS["bdUseComment"]){?>
<tr>
	<td>

	<table width=100% cellpadding=0 cellspacing=0>
<?php if((is_array($TPL_R2=$TPL_V1["loopComment"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
	<tr><td colspan=3 height=1 bgcolor="#EFEFEF"></td></tr>
	<tr><td colspan=3 height=1 bgcolor="#FFFFFF"></td></tr>
	<tr height=22 bgcolor=#f7f7f7>
		<td width=100% style="padding-left:10">
<?php if($TPL_V2["m_no"]){?><b><?php }?>
		<?php echo $TPL_V2["name"]?>

		</td>
		<td nowrap class=eng><?php echo $TPL_V2["regdt"]?></td>
		<td style="padding:0 10" class=engb align=center>
<?php if($TPL_V2["link"]["delete"]){?><?php echo $TPL_V2["link"]["delete"]?>x<?php echo $TPL_VAR["link"]["end"]?>

<?php }else{?>-<?php }?>
		</td>
	</tr>
	<tr>
		<td colspan=3 style="padding:5; word-wrap:break-word; word-break:break-all;"><?php echo $TPL_V2["comment"]?></td>
	</tr>
<?php }}?>
	</table>

	</td>
</tr>
<tr>
	<td>

<?php if(!$GLOBALS["bdDenyComment"]){?>
	<form name=frmComment_<?php echo $TPL_VAR["no"]?> method=post action="<?php echo url("board/comment_ok.php")?>&" onsubmit="return chkForm(this)">
	<input type=hidden name=id value="<?php echo $TPL_VAR["id"]?>">
	<input type=hidden name=no value="<?php echo $TPL_V1["no"]?>">
	<input type=hidden name=mode value="write">
	<input type=hidden name=returnUrl value="<?php echo $_SERVER["REQUEST_URI"]?>">

	<table width=100% align=center>
	<tr>
		<td valign=top width=100 nowrap class=stxt>
		<B>이름</B><br>
		<input name=name class=linebg style="width:100%" required fld_esssential msgR="이름을 입력해주세요" value="<?php echo $GLOBALS["member"]["name"]?>" <?php echo $TPL_VAR["readonly"]["name"]?>>
<?php if(!$TPL_VAR["readonly"]["name"]){?>
		<B>비밀번호</B><br>
		<input type=password name=password class=linebg style="width:100%" required fld_esssential msgR="비밀번호를 입력해주세요">
<?php }?>
		</td>
		<td width=100% class=stxt>
		<B>메모</B><br>
		<textarea name=memo style="width:100%;height:56" class=linebg required fld_esssential msgR="코멘트를 입력해주세요"></textarea>
<?php if($GLOBALS["bdSpamComment"]& 2){?>
		<div style="margin-top:5px;"><B>자동등록방지</B><BR><?php echo $this->define('tpl_include_file_1',"proc/_captcha.htm")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?></div>
<?php }?>
		</td>
		<td valign=top style="padding-top:19">
		<input type=image src="/shop/data/skin/campingyo/board/default/img/board_btn_review.gif">
		</td>
	</tr>
	</table>

	</form>
<?php }?>

	</td>
</tr>
<?php }?>
<tr>
	<td align=center style="padding-top:10">

	<table width=100%>
	<tr>
		<td>
<?php if($TPL_V1["link"]["modify"]){?><?php echo $TPL_V1["link"]["modify"]?><img src="/shop/data/skin/campingyo/board/default/img/board_btn_modify.gif"><?php echo $TPL_VAR["link"]["end"]?> <?php }?>
<?php if($TPL_V1["link"]["delete"]){?><?php echo $TPL_V1["link"]["delete"]?><img src="/shop/data/skin/campingyo/board/default/img/board_btn_delete.gif"><?php echo $TPL_VAR["link"]["end"]?> <?php }?>
<?php if($TPL_VAR["link"]["write"]){?><?php echo $TPL_V1["link"]["reply"]?><img src="/shop/data/skin/campingyo/board/default/img/board_btn_reply.gif"><?php echo $TPL_VAR["link"]["end"]?><?php }?>
		</td>
		<td align=right>
		<?php echo $TPL_VAR["link"]["list"]?><img src="/shop/data/skin/campingyo/board/default/img/board_btn_list.gif"><?php echo $TPL_VAR["link"]["end"]?>

<?php if($TPL_VAR["link"]["write"]){?><?php echo $TPL_VAR["link"]["write"]?><img src="/shop/data/skin/campingyo/board/default/img/board_btn_write.gif"><?php echo $TPL_VAR["link"]["end"]?><?php }?>
		</td>
	</tr>
	</table>

	</td>
</tr>
</table>

<?php if($TPL_V1["relation"]){?>
<br><table width=100% cellpadding=5 cellspacing=0>
<col width=100 align=right bgcolor=#f7f7f7 style="padding-right:10px">
<col style="padding-left:10px">
<tr><td colspan=2 height=1 bgcolor=#cccccc></td></tr>
<?php if($TPL_V1["relation"]["next"]["subject"]){?>
<tr height=20>
	<td class=input_txt>다음글</td>
	<td><?php echo $TPL_V1["relation"]["next"]["link"]["view"]?><?php echo $TPL_V1["relation"]["next"]["subject"]?></a></td>
</tr><tr><td colspan=2 height=1 bgcolor=#cccccc></td></tr>
<?php }?>
<?php if($TPL_V1["relation"]["prev"]["subject"]){?>
<tr height=20>
	<td class=input_txt>이전글</td>
	<td><?php echo $TPL_V1["relation"]["prev"]["link"]["view"]?><?php echo $TPL_V1["relation"]["prev"]["subject"]?></a></td>
</tr><tr><td colspan=2 height=1 bgcolor=#cccccc></td></tr>
<?php }?>
<?php if($TPL_V1["relation"]["reply"]){?>
<tr>
	<td valign=top class=input_txt>답글</td>
	<td>

	<table cellpadding=0 cellspacing=0>
<?php if((is_array($TPL_R2=$TPL_V1["relation"]["reply"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
	<tr>
		<td height=20><?php echo $TPL_V2["gapReply"]?><?php if($TPL_V2["sub"]){?><img src="/shop/data/skin/campingyo/board/default/img/board_re.gif" align=absmiddle><?php }?><?php echo $TPL_V2["link"]["view"]?><?php echo $TPL_V2["subject"]?></a></td>
	</tr>
<?php }}?>
	</table>

	</td>
</tr><tr><td colspan=2 height=1 bgcolor=#cccccc></td></tr>
<?php }?>
</table><p>
<?php }?>

<br><textarea id=examC_<?php echo $TPL_V1["no"]?> style="display:none;width:100%;height:300px"><?php echo htmlspecialchars($TPL_V1["contents"])?></textarea>
<?php }}?>

<?php if($GLOBALS["bdTypeView"]== 2){?><?php $this->print_("list",$TPL_SCP,1);?><?php }?>
</td></tr></table>

<?php echo $GLOBALS["bdFooter"]?> <?php $this->print_("footer",$TPL_SCP,1);?>