<?php /* Template_ 2.2.7 2014/03/05 23:19:27 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/board/gallery/list.htm 000004434 */ 
if (is_array($TPL_VAR["list"])) $TPL_list_1=count($TPL_VAR["list"]); else if (is_object($TPL_VAR["list"]) && in_array("Countable", class_implements($TPL_VAR["list"]))) $TPL_list_1=$TPL_VAR["list"]->count();else $TPL_list_1=0;?>
<?php if(!$GLOBALS["pageView"]){?>
<?php $this->print_("header",$TPL_SCP,1);?> <?php echo $GLOBALS["bdHeader"]?>

<?php }?>

<table width=<?php echo $GLOBALS["bdWidth"]?> align=<?php echo $GLOBALS["bdAlign"]?> cellpadding=0 cellspacing=0><tr><td>

<form name=frmList action="<?php echo url("board/list.php")?>&" onsubmit="return chkFormList(this)">
<input type=hidden name=id value="<?php echo $TPL_VAR["id"]?>">

<table width=100%>
<tr>
<?php if($TPL_list_1){$TPL_I1=-1;foreach($TPL_VAR["list"] as $TPL_V1){$TPL_I1++;?>

<?php if($TPL_V1["notice"]){?><?  $TPL_VAR["cnt_notice"]++; ?>
	</tr><tr>
		<td height=25 colspan=10 bgcolor=#f7f7f7 style="border-bottom:1 solid #efefef">
		<img src="/shop/data/skin/campingyo/board/gallery/img/board_notice.gif" align=absmiddle> <?php if($GLOBALS["bdUseSubSpeech"]&&$TPL_V1["category"]){?>[<?php echo $TPL_V1["category"]?>]<?php }?> <?php echo $TPL_V1["link"]["view"]?><?php echo $TPL_V1["subject"]?><?php echo $TPL_VAR["link"]["end"]?>

		</td>
	</tr><tr>
<?php }else{?>

	<td valign=top>

	<table>
	<col align=center>
	<tr>
		<td>

		<table>
		<tr>
			<td width="<?php echo ($GLOBALS["bdListImgSizeW"]+ 13)?>" height="<?php echo ($GLOBALS["bdListImgSizeH"]+ 13)?>" style="border:3 solid #efefef;padding:1px" align="center">
<?php if($GLOBALS["bdListImg"]== 2){?><?php echo $TPL_V1["link"]["view"]?><?php }elseif($TPL_V1["imgnm"]){?><a href="javascript:popupImg('../data/board/<?php echo $TPL_VAR["id"]?>/<?php echo $TPL_V1["imgnm"]?>')"><?php }?><img src="../data/board/<?php echo $TPL_VAR["id"]?>/t/<?php echo $TPL_V1["imgnm"]?>" width="<?php echo $TPL_V1["imgSizeW"]?>" height="<?php echo $TPL_V1["imgSizeH"]?>" onerror=this.src="/shop/data/skin/campingyo/board/gallery/img/noimg.gif"></a>
			</td>
		</tr>
		</table>

		</td>
	</tr>
	<tr><td><?php if($GLOBALS["bdUseSubSpeech"]&&$TPL_V1["category"]){?>[<?php echo $TPL_V1["category"]?>]<?php }?><?php echo $TPL_V1["link"]["view"]?><?php echo $TPL_V1["subject"]?><?php echo $TPL_VAR["link"]["end"]?></td></tr>
	</table>

	</td>
<?php if(($TPL_I1+ 1-$TPL_VAR["cnt_notice"])%$GLOBALS["bdListImgCntW"]== 0){?></tr><tr><?php }?>
<?php }?>
<?php }}?>
</tr>
</table>

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
<?php if($GLOBALS["bdUseSubSpeech"]){?><td align=center><?php echo $TPL_VAR["speechBox"]?></td><?php }?>
			<td class=stxt>
<?php if(!($GLOBALS["bdField"]& 8)){?>
			<input type=checkbox name="search[name]" <?php echo $TPL_VAR["checked"]['search']['name']?>>이름
<?php }?>
			<input type=checkbox name="search[subject]" <?php echo $TPL_VAR["checked"]['search']['subject']?>>제목
			<input type=checkbox name="search[contents]" <?php echo $TPL_VAR["checked"]['search']['contents']?>>내용&nbsp;
			</td>
			<td><input name="search[word]" value="<?php echo $TPL_VAR["search"]["word"]?>" style="background-color:#FFFFFF;border:1px solid #DFDFDF;width:140" required></td>
			<td><a href="javascript:document.frmList.submit()"><img src="/shop/data/skin/campingyo/board/gallery/img/board_btn_search.gif"></a></td>
			<td><a href="<?php echo url("board/list.php?")?>&id=<?php echo $TPL_VAR["id"]?>"><img src="/shop/data/skin/campingyo/board/gallery/img/board_btn_cancel.gif"></a></td>
		</tr>
		</table>
	</td>
</tr>
</table>

<?php echo $TPL_VAR["link"]["list"]?><img src="/shop/data/skin/campingyo/board/gallery/img/board_btn_list.gif"><?php echo $TPL_VAR["link"]["end"]?>

<?php if($TPL_VAR["link"]["write"]){?><?php echo $TPL_VAR["link"]["write"]?><img src="/shop/data/skin/campingyo/board/gallery/img/board_btn_write.gif"><?php echo $TPL_VAR["link"]["end"]?><?php }?>

</form>

</td></tr></table>

<?php if(!$GLOBALS["pageView"]){?><?php echo $GLOBALS["bdFooter"]?> <?php $this->print_("footer",$TPL_SCP,1);?><?php }?>