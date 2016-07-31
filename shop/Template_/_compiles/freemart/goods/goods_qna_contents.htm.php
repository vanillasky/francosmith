<?php /* Template_ 2.2.7 2014/07/30 21:42:58 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/goods/goods_qna_contents.htm 000002005 */ ?>
<?php if($TPL_VAR["authview"]=='Y'){?>
<table id=contents width=100% cellpadding=0 cellspacing=0 style="border-top-style:solid;border-top-color:#303030;border-top-width:2;" onmouseover=this.style.background="#F7F7F7" onmouseout=this.style.background="">
<tr>
	<td style="border-top-style:solid;border-top-color:#E6E6E6;border-top-width:1;padding:5 5 5 0;">
	<div><?php echo $TPL_VAR["contents"]?></div>
	<div style="float:right;">
<?php if($TPL_VAR["parent"]==$TPL_VAR["sno"]&&!$TPL_VAR["notice"]){?>
	<a href="javascript:;" onclick="popup_register( 'reply_qna', '<?php echo $TPL_VAR["goodsno"]?>', '<?php echo $TPL_VAR["sno"]?>' );"><img src="/shop/data/skin/freemart/img/common/btn_reply.gif" border="0" align="absmiddle"></a>
<?php }?>
<?php if($TPL_VAR["authmodify"]=='Y'){?>
	<a href="javascript:;" onclick="popup_register( 'mod_qna', '<?php echo $TPL_VAR["goodsno"]?>', '<?php echo $TPL_VAR["sno"]?>' );"><img src="/shop/data/skin/freemart/img/common/btn_modify2.gif" border="0" align="absmiddle"></a>
<?php }?>
<?php if($TPL_VAR["authdelete"]=='Y'){?>
	<a href="javascript:;" onclick="popup_register( 'del_qna', '<?php echo $TPL_VAR["goodsno"]?>', '<?php echo $TPL_VAR["sno"]?>' );"><img src="/shop/data/skin/freemart/img/common/btn_delete.gif" border="0" align="absmiddle"></a>
<?php }?>
	</div>
	</td>
</tr>
<tr><td height=1 bgcolor="#E6E6E6" style="padding:0px;"></td></tr>
</table>
<?php }else{?>
<table width=100% cellpadding=0 cellspacing=0 style="border-top-style:solid;border-top-color:#303030;border-top-width:2;" onmouseover=this.style.background="#F7F7F7" onmouseout=this.style.background="">
<tr height=30>
	<td style="border-top-style:solid;border-top-color:#E6E6E6;border-top-width:1" align="center">비밀글 입니다.</td>
</tr>
<tr><td height="1" bgcolor="#E6E6E6" style="padding:0px;"></td></tr>
</table>
<?php }?>