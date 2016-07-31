<?php /* Template_ 2.2.7 2014/07/30 21:42:58 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/board/gallery/delete.htm 000002160 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?> <?php echo $GLOBALS["bdHeader"]?>


<table width=<?php echo $GLOBALS["bdWidth"]?> align=<?php echo $GLOBALS["bdAlign"]?> cellpadding=0 cellspacing=0><tr><td>

<form name=frmDelete action="<?php echo url("board/delete_ok.php")?>&" method=post onSubmit="return chkForm(this)">
<input type=hidden name=id value=<?php echo $_GET["id"]?>>
<input type=hidden name=sel[] value=<?php echo $GLOBALS["no"]?>>
<input type=hidden name=mode value="<?php echo $GLOBALS["mode"]?>">
<input type=hidden name=returnUrl value="<?php echo $GLOBALS["returnUrl"]?>">

<table width=100%>
<tr><td height=3 bgcolor=#efefef></td></tr>
<tr>
	<td height=150 align=center bgcolor=#f7f7f7>

<?php if(($GLOBALS["m_no"]&&$GLOBALS["m_no"]==$GLOBALS["sess"]["m_no"])||$GLOBALS["ici_admin"]){?>
	<b>정말로 삭제하시겠습니까?</b><p>
	글을 삭제합니다. 데이터 삭제시 복구가 불가능 합니다<br>
	<font color=#FF6600>또한 이 게시물의 작성자가 <b>업로드한 이미지도 같이 삭제</b>됩니다.<br>
	업로드 되어 있던 이미지는 다른 곳에서도 사용되고 있을 수 있으므로<br>
	<b>신중히 확인하시고 삭제하세요.</b></font>
<?php }elseif($GLOBALS["m_no"]){?>
	<b>삭제권한이 없습니다</b><p>
	글을 삭제할수 있는 권한을 가지고 있지 않습니다
<?php }else{?>
	<input type=password name=password required class=line><p>
	글을 삭제합니다. 비밀번호를 입력하여 주십시오<br>
	데이터 삭제시 복구가 불가능 합니다<br>
	<font color=#FF6600>또한 이 게시물의 작성자가 <b>업로드한 이미지도 같이 삭제</b>됩니다.<br>
	업로드 되어 있던 이미지는 다른 곳에서도 사용되고 있을 수 있으므로<br>
	<b>신중히 확인하시고 삭제하세요.</b></font>
<?php }?>

	</td>
</tr>
<tr><td height=2 bgcolor=#efefef></td></tr>
<tr>
	<td align=center><br>
	<input type=image src="/shop/data/skin/freemart/board/gallery/img/board_btn_ok.gif">
	<a href="javascript:history.back()"><img src="/shop/data/skin/freemart/board/gallery/img/board_btn_back.gif"></a>
	</td>
</tr>
</table>

</form>

</td></tr></table>

<?php echo $GLOBALS["bdFooter"]?> <?php $this->print_("footer",$TPL_SCP,1);?>