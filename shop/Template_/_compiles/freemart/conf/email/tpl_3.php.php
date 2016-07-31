<?php /* Template_ 2.2.7 2013/09/17 17:56:37 /www/francotr3287_godo_co_kr/shop/conf/email/tpl_3.php 000002057 */  $this->include_("dataBanner");?>
&nbsp;
<TABLE
style="BORDER-RIGHT: #cccccc 1px solid; BORDER-TOP: #cccccc 1px solid; BORDER-LEFT: #cccccc 1px solid; BORDER-BOTTOM: #cccccc 1px solid"
cellSpacing=0 cellPadding=10 width=640 border=0>
<TBODY>
<TR>
<TD><!--메일 상단 : Start -->
<TABLE cellSpacing=0 cellPadding=0 width=640 border=0>
<TBODY>
<TR>
<TD width=640><IMG
src="/shop/admin/img/mail/mail_bar_delivery.gif"></TD></TR></TBODY></TABLE><!--메일 상단 : End --><!--본문 부분 : Start -->
<TABLE cellSpacing=0 cellPadding=0 width=640 border=0>
<TBODY>
<TR>
<TD align=middle>
<TABLE cellSpacing=0 cellPadding=0 width=610 border=0>
<TBODY>
<TR>
<TD height=20></TD></TR>
<TR>
<TD style="FONT: 9pt/20px 돋움; COLOR: #585858">
<P><STRONG><?php echo $TPL_VAR["nameOrder"]?> 고객님의 배송을 확인하였습니다.</STRONG><BR></P>
<P>감사합니다. </P></TD></TR></TBODY></TABLE></TD></TR>
<TR>
<TD height=16></TD></TR></TBODY></TABLE><!--본문 부분 : End --><!--메일 하단 : Start -->
<TABLE cellSpacing=0 cellPadding=0 width=640 border=0>
<TBODY>
<TR>
<TD bgColor=#dddddd height=1></TD></TR>
<TR>
<TD height=10></TD></TR>
<TR>
<TD align=middle height=20>
<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
<TBODY>
<TR>
<TD align=right width=200><?php if((is_array($TPL_R1=dataBanner( 92))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></TD>
<TD align=middle>
<TABLE cellSpacing=0 cellPadding=2 width="95%" border=0>
<TBODY>
<TR>
<TD><IMG src="/shop/admin/img/mail/mail_bottom.gif"></TD></TR>
<TR>
<TD style="FONT: 8pt verdana"><FONT color=#585858>Copyright(C)
<STRONG><?php echo $TPL_VAR["cfg"]["shopName"]?></STRONG> All right
reserved.</FONT></TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE></TD></TR>
<TR>
<TD align=middle
height=10></TD></TR></TBODY></TABLE><!--메일 하단 : End --></TD></TR></TBODY></TABLE>