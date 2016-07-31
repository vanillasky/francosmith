<?php /* Template_ 2.2.7 2014/07/30 21:42:58 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/board/webzine/list.htm 000009734 */ 
if (is_array($TPL_VAR["list"])) $TPL_list_1=count($TPL_VAR["list"]); else if (is_object($TPL_VAR["list"]) && in_array("Countable", class_implements($TPL_VAR["list"]))) $TPL_list_1=$TPL_VAR["list"]->count();else $TPL_list_1=0;?>
<?php if(!$GLOBALS["pageView"]){?>
<?php $this->print_("header",$TPL_SCP,1);?> <?php echo $GLOBALS["bdHeader"]?>

<?php }?>
<table width=<?php echo $GLOBALS["bdWidth"]?> align=<?php echo $GLOBALS["bdAlign"]?> cellpadding=0 cellspacing=0><tr><td>
<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr>
	<td>

	<table width=100% cellpadding=0 cellspacing=0>
	<tr>
		<td colspan=10 align=right class=eng height=20>
<?php if(!$TPL_VAR["search"]["word"]){?> Total <?php echo $TPL_VAR["recode"]["total"]?> Articles, <?php echo $TPL_VAR["page"]["now"]?> of <?php echo $TPL_VAR["page"]["total"]?> Pages
<?php }else{?>	Search Mode <?php echo $TPL_VAR["page"]["now"]?> Page
<?php }?>
		</td>
	</tr>

<?php if($TPL_list_1){foreach($TPL_VAR["list"] as $TPL_V1){?>
	<tr <?php if($GLOBALS["checked"]['chk'][$TPL_V1["no"]]){?>bgcolor=#F7F7F7<?php }elseif($TPL_V1["notice"]){?>bgcolor=#FAFAFA<?php }?>>
		<td width=100% style="padding-left:10px">
			<table width=100% cellpadding=0 cellspacing=0>
			<tr><td style="padding-top:20px">
			<table width=100% cellpadding=0 cellspacing=0>
			<tr><td height=2 bgcolor="#303030"></td></tr>
			<tr>
				<td bgcolor=#ECECEC height=27 style="padding:0 10">
					<table width=100%>
					<tr height=21>
						<td>
							<table>
							<tr>
								<td><?php if(!($GLOBALS["bdField"]& 2)){?><?php if($TPL_V1["notice"]){?><img src="/shop/data/skin/freemart/board/webzine/img/board_notice.gif" align=absmiddle><?php }else{?><?php echo $TPL_V1["num"]?><?php }?><?php }?><td>
<?php if($GLOBALS["bdUseSubSpeech"]){?><td><?php if($TPL_V1["category"]){?>[<?php echo $TPL_V1["category"]?>]<?php }?></td><?php }?>
								<td>	<?php echo $TPL_V1["gapReply"]?><?php if($TPL_V1["sub"]){?><img src="/shop/data/skin/freemart/board/webzine/img/board_re.gif" align=absmiddle><?php }?><b><?php echo $TPL_V1["link"]["view"]?> <?php echo $TPL_V1["subject"]?><?php echo $TPL_VAR["link"]["end"]?></b><?php if($TPL_V1["secret"]){?><img src="/shop/data/skin/freemart/board/webzine/img/icn_secret.gif" align=absmiddle><?php }?><?php if($TPL_V1["new"]){?><img src="/shop/data/skin/freemart/board/webzine/img/board_new.gif" align=absmiddle><?php }?><?php if($TPL_V1["hot"]){?><img src="/shop/data/skin/freemart/board/webzine/img/board_hot.gif" align=absmiddle border=0><?php }?></td>
							</table>
						</td>
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
				<td style="padding:10" height=200 valign=top>

				<table width=100% style="table-layout:fixed">
				<tr>
					<td style="word-wrap:break-word;word-break:break-all"  valign=top  id=contents_<?php echo $TPL_V1["no"]?>><?php echo $TPL_V1["contents"]?></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr><td height=1 bgcolor="#E0DFDF"></td></tr>
			<tr><td height=3 bgcolor="#F7F7F7"></td></tr>
			</table>
			<table width=100%>
			<tr>
				<td align=right>
<?php if($TPL_V1["link"]["modify"]){?><?php echo $TPL_V1["link"]["modify"]?><img src="/shop/data/skin/freemart/board/webzine/img/board_btn_modify.gif"><?php echo $TPL_VAR["link"]["end"]?> <?php }?>
<?php if($TPL_V1["link"]["delete"]){?><?php echo $TPL_V1["link"]["delete"]?><img src="/shop/data/skin/freemart/board/webzine/img/board_btn_delete.gif"><?php echo $TPL_VAR["link"]["end"]?> <?php }?>
				</td>
			</tr>
			</table>
			<br>
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
				<form name=frmComment_<?php echo $TPL_V1["no"]?> method=post action="<?php echo url("board/comment_ok.php")?>&" onsubmit="return chkForm(this)">
				<input type=hidden name=id value="<?php echo $TPL_VAR["id"]?>">
				<input type=hidden name=no value="<?php echo $TPL_V1["no"]?>">
				<input type=hidden name=mode value="write">
				<input type=hidden name=returnUrl value="<?php echo $_SERVER["REQUEST_URI"]?>">
				<table width=100% align=center>
				<tr>
					<td valign=top width=100 nowrap class=stxt>
					<B>이름</B><br>
					<input name=name class=linebg style="width:100%" required msgR="이름을 입력해주세요" value="<?php echo $GLOBALS["member"]["name"]?>" <?php echo $TPL_VAR["readonly"]["name"]?>>
<?php if(!$TPL_VAR["readonly"]["name"]){?>
					<B>비밀번호</B><br>
					<input type=password name=password class=linebg style="width:100%" required msgR="비밀번호를 입력해주세요">
<?php }?>
					</td>
					<td width=100% class=stxt>
					<B>메모</B><br>
					<textarea name=memo style="width:100%;height:56" class=linebg required msgR="코멘트를 입력해주세요"></textarea>
<?php if($GLOBALS["bdSpamComment"]& 2){?>
					<div style="margin-top:5px;"><B>자동등록방지</B><BR><?php echo $this->define('tpl_include_file_1',"proc/_captcha.htm")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?></div>
<?php }?>
					</td>
					<td valign=top style="padding-top:19">
					<input type=image src="/shop/data/skin/freemart/board/webzine/img/board_btn_review.gif">
					</td>
				</tr>
				</table>
				</form>
<?php }?>
				</td>
			</tr>
<?php }?>
			</table>
			</td></tr></table>
		</td>
	</tr>
<?php }}?>
	<tr><td colspan=10 height=1 bgcolor=#E0DFDF></td></tr>
	</table>
	</td>
</tr>
<tr>
	<td height=40 bgcolor="#F7F7F7">
	<table width=100%>
	<tr>
		<td class=eng>
		<?php echo $TPL_VAR["page"]["navi"]?>

<?php if($TPL_VAR["link"]["prev"]){?><?php echo $TPL_VAR["link"]["prev"]?>이전<?php echo $TPL_VAR["link"]["end"]?> <?php }?>
<?php if($TPL_VAR["link"]["next"]){?><?php echo $TPL_VAR["link"]["next"]?>다음<?php echo $TPL_VAR["link"]["end"]?><?php }?>
		</td>
		<td align=right>
		<form name=frmList action="<?php echo url("board/list.php")?>&" onsubmit="return chkFormList(this)">
		<input type=hidden name=id value="<?php echo $TPL_VAR["id"]?>">
			<table cellpadding=0 cellspacing=0>
			<tr>
<?php if($GLOBALS["bdUseSubSpeech"]){?><td align=center><?php echo $TPL_VAR["speechBox"]?></td><?php }?>
				<td class=stxt>
<?php if(!($GLOBALS["bdField"]& 8)){?>
				<input type=checkbox name="search[name]" <?php echo $TPL_VAR["checked"]['search']['name']?>>이름
<?php }?>
				<input type=checkbox name="search[subject]" <?php echo $TPL_VAR["checked"]['search']['subject']?>>제목
				<input type=checkbox name="search[contents]" <?php echo $TPL_VAR["checked"]['search']['contents']?>>내용&nbsp;
				</td>
				<td><input name="search[word]" value="<?php echo $TPL_VAR["search"]["word"]?>" style="background-color:#FFFFFF;border:1px solid #DFDFDF;width:140" required></td>
				<td><a href="javascript:document.frmList.submit()"><img src="/shop/data/skin/freemart/board/webzine/img/board_btn_search.gif"></a></td>
				<td><a href="<?php echo url("board/list.php?")?>&id=<?php echo $TPL_VAR["id"]?>"><img src="/shop/data/skin/freemart/board/webzine/img/board_btn_cancel.gif"></a></td>
			</tr>
			</table>

		</form>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td align=center style="padding-top:15">
		<?php echo $TPL_VAR["link"]["list"]?><img src="/shop/data/skin/freemart/board/webzine/img/board_btn_list.gif"><?php echo $TPL_VAR["link"]["end"]?>

<?php if($TPL_VAR["link"]["write"]){?><?php echo $TPL_VAR["link"]["write"]?><img src="/shop/data/skin/freemart/board/webzine/img/board_btn_write.gif"><?php echo $TPL_VAR["link"]["end"]?><?php }?>
	</td>
</tr>
</table><p>
</td></tr></table>
<?php if(!$GLOBALS["pageView"]){?><?php echo $GLOBALS["bdFooter"]?> <?php $this->print_("footer",$TPL_SCP,1);?><?php }?>