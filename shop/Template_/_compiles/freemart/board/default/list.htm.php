<?php /* Template_ 2.2.7 2016/04/22 14:06:51 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/board/default/list.htm 000007979 */ 
if (is_array($TPL_VAR["list"])) $TPL_list_1=count($TPL_VAR["list"]); else if (is_object($TPL_VAR["list"]) && in_array("Countable", class_implements($TPL_VAR["list"]))) $TPL_list_1=$TPL_VAR["list"]->count();else $TPL_list_1=0;?>
<?php if(!$GLOBALS["pageView"]){?>
<?php $this->print_("header",$TPL_SCP,1);?> <?php echo $GLOBALS["bdHeader"]?>

<?php }?>

<table width="<?php echo $GLOBALS["bdWidth"]?>" align="<?php echo $GLOBALS["bdAlign"]?>">
<tr>
	<td>
		<form name=frmList action="<?php echo url("board/list.php")?>&" onsubmit="return chkFormList(this)">
		<input type=hidden name=id value="<?php echo $TPL_VAR["id"]?>">

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
				<tr>
<?php if(!($GLOBALS["bdField"]& 1)){?><td width=20 align=center height=30 background="/shop/data/skin/freemart/board/default/img/board_bg.gif"><?php echo $TPL_VAR["link"]["chk"]?><img src="/shop/data/skin/freemart/board/default/img/board_field_01.gif"><?php echo $TPL_VAR["link"]["end"]?></td><?php }?>
<?php if(!($GLOBALS["bdField"]& 2)){?><td width=40 align=center background="/shop/data/skin/freemart/board/default/img/board_bg.gif"><img src="/shop/data/skin/freemart/board/default/img/board_field_02.gif"></td><?php }?>
<?php if(!($GLOBALS["bdField"]& 4)){?>
<?php if($GLOBALS["bdUseSubSpeech"]){?><td align=center background="/shop/data/skin/freemart/board/default/img/board_bg.gif"><?php echo $TPL_VAR["speechBox"]?></td><?php }?>
					<td align=center background="/shop/data/skin/freemart/board/default/img/board_bg.gif"><img src="/shop/data/skin/freemart/board/default/img/board_field_03.gif"></td>
<?php }?>
<?php if(!($GLOBALS["bdField"]& 8)){?><td width=100 align=center background="/shop/data/skin/freemart/board/default/img/board_bg.gif"><img src="/shop/data/skin/freemart/board/default/img/board_field_04.gif"></td><?php }?>
<?php if(!($GLOBALS["bdField"]& 16)){?><td width=100 align=center background="/shop/data/skin/freemart/board/default/img/board_bg.gif"><img src="/shop/data/skin/freemart/board/default/img/board_field_05.gif"></td><?php }?>
<?php if(!($GLOBALS["bdField"]& 32)){?><td width=40 align=center background="/shop/data/skin/freemart/board/default/img/board_bg.gif"><img src="/shop/data/skin/freemart/board/default/img/board_field_06.gif"></td><?php }?>
				</tr>
<?php if($TPL_list_1){foreach($TPL_VAR["list"] as $TPL_V1){?>
				<tr height=27 onmouseover=this.style.backgroundColor="#FAFAFA" onmouseout=this.style.backgroundColor=""
<?php if($GLOBALS["checked"]['chk'][$TPL_V1["no"]]){?>bgcolor=#F7F7F7<?php }elseif($TPL_V1["notice"]){?>bgcolor=#FAFAFA<?php }?>
				>
<?php if(!($GLOBALS["bdField"]& 1)){?>
					<td width=20 nowrap align=center><input type=checkbox name=sel[] value=<?php echo $TPL_V1["no"]?> <?php echo $GLOBALS["checked"]['chk'][$TPL_V1["no"]]?>></td>
<?php }?>
<?php if(!($GLOBALS["bdField"]& 2)){?>
					<td width=40 nowrap align=center class=eng>
<?php if($TPL_V1["notice"]){?><img src="/shop/data/skin/freemart/board/default/img/board_notice.gif" align=absmiddle><?php }else{?><?php echo $TPL_V1["num"]?><?php }?>
					</td>
<?php }?>
<?php if(!($GLOBALS["bdField"]& 4)){?>
<?php if($GLOBALS["bdUseSubSpeech"]){?><td width=50 nowrap><?php if($TPL_V1["category"]){?>[<?php echo $TPL_V1["category"]?>]<?php }?></td><?php }?>
					<td style="padding-left:10px" align="left">
					<?php echo $TPL_V1["gapReply"]?><?php if($TPL_V1["sub"]){?><img src="/shop/data/skin/freemart/board/default/img/board_re.gif" align=absmiddle><?php }?>
					<?php echo $TPL_V1["link"]["view"]?><?php if($TPL_V1["notice"]){?><b style="color:#FE8300"><?php }?><?php echo $TPL_V1["subject"]?><?php echo $TPL_VAR["link"]["end"]?>

<?php if($GLOBALS["bdUseComment"]&&$TPL_V1["comment"]){?>&nbsp;<span class=engs><FONT COLOR="#007FC8">[<?php echo $TPL_V1["comment"]?>]</FONT></span><?php }?>
<?php if($TPL_V1["secret"]){?><img src="/shop/data/skin/freemart/board/default/img/icn_secret.gif" align=absmiddle><?php }?>
<?php if($TPL_V1["new"]){?><img src="/shop/data/skin/freemart/board/default/img/board_new.gif" align=absmiddle><?php }?>
<?php if($TPL_V1["hot"]){?><img src="/shop/data/skin/freemart/board/default/img/board_hot.gif" align=absmiddle border=0><?php }?>
					<?php echo $TPL_V1["xx"]?>

					</td>
<?php }?>
<?php if(!($GLOBALS["bdField"]& 8)){?>
					<td width=100 nowrap align=center>
<?php if($TPL_V1["m_no"]){?><b><?php }?><?php echo $TPL_V1["name"]?>

					</td>
<?php }?>
<?php if(!($GLOBALS["bdField"]& 16)){?>
					<td width=100 nowrap align=center class=eng><?php echo substr($TPL_V1["regdt"], 0, 10)?></td>
<?php }?>
<?php if(!($GLOBALS["bdField"]& 32)){?>
					<td width=40 nowrap align=center class=eng><?php if($TPL_V1["hot"]){?><font color=FF6C68><?php }?><?php echo $TPL_V1["hit"]?></td>
<?php }?>
				</tr>
				<tr>
					<td colspan=10 height=1 bgcolor=#E0DFDF></td>
				</tr>
<?php }}?>
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
						<table cellpadding=0 cellspacing=0>
						<tr>
							<td>
<?php if(!($GLOBALS["bdField"]& 8)){?>
							<input type=checkbox name="search[name]" <?php echo $TPL_VAR["checked"]['search']['name']?>>이름
<?php }?>
							<input type=checkbox name="search[subject]" <?php echo $TPL_VAR["checked"]['search']['subject']?>>제목
							<input type=checkbox name="search[contents]" <?php echo $TPL_VAR["checked"]['search']['contents']?>>내용&nbsp;
							</td>
							<td><input name="search[word]" value="<?php echo $TPL_VAR["search"]["word"]?>" style="background-color:#FFFFFF;border:1px solid #DFDFDF;width:140" required></td>
							<td><a href="javascript:document.frmList.submit()"><img src="/shop/data/skin/freemart/board/default/img/board_btn_search.gif"></a></td>
							<td><a href="<?php echo url("board/list.php?")?>&id=<?php echo $TPL_VAR["id"]?>"><img src="/shop/data/skin/freemart/board/default/img/board_btn_cancel.gif"></a></td>
						</tr>
						</table>
					</td>
				</tr>
				</table>

			</td>
		</tr>
		<tr>
			<td height="10px"></td>
		</tr>
		<tr>
			<td align=center style="padding-top:15">
<?php if($TPL_VAR["link"]["delete"]){?><?php echo $TPL_VAR["link"]["delete"]?><img src="/shop/data/skin/freemart/board/default/img/btn_delete.jpg"><?php echo $TPL_VAR["link"]["end"]?> <?php }?>
<?php if(!($GLOBALS["bdField"]& 1)){?><?php echo $TPL_VAR["link"]["viewSel"]?><img src="/shop/data/skin/freemart/board/default/img/btn_view.jpg"><?php echo $TPL_VAR["link"]["end"]?> <?php }?>
			<?php echo $TPL_VAR["link"]["list"]?><img src="/shop/data/skin/freemart/board/default/img/btn_list.jpg"><?php echo $TPL_VAR["link"]["end"]?>

<?php if($TPL_VAR["link"]["write"]){?><?php echo $TPL_VAR["link"]["write"]?><img src="/shop/data/skin/freemart/board/default/img/btn_write.jpg"><?php echo $TPL_VAR["link"]["end"]?><?php }?>
			</td>
		</tr>
		</table><p>
</form>
	</td>
</tr>
</table>

<?php if(!$GLOBALS["pageView"]){?><?php echo $GLOBALS["bdFooter"]?> <?php $this->print_("footer",$TPL_SCP,1);?><?php }?>