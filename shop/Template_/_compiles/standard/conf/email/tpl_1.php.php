<?php /* Template_ 2.2.7 2014/01/20 20:32:54 /www/francotr3287_godo_co_kr/shop/conf/email/tpl_1.php 000002490 */  $this->include_("dataBanner");?>
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
src="/shop/admin/img/mail/mail_bar_payment.gif"></TD></TR></TBODY></TABLE><!--메일 상단 : End --><!--본문 부분 : Start -->
<TABLE cellSpacing=0 cellPadding=0 width=640 border=0>
<TBODY>
<TR>
<TD align=middle>
<TABLE cellSpacing=0 cellPadding=0 width=610 border=0>
<TBODY>
<TR>
<TD height=20></TD></TR>
<TR>
<TD style="FONT: 9pt/20px 돋움; COLOR: #585858"><STRONG><?php echo $TPL_VAR["nameOrder"]?> 고객님의 입금을 
확인하였습니다.</STRONG><BR><BR>빠른시일내에 주문하신 상품을 받아보실 수 있도록 노력하겠습니다.<BR></STRONG>저희 쇼핑몰을 
이용해주셔서 감사드리며, 언제나 만족스런 쇼핑을 하실 수 있도록 최선을 다하는 <?php echo $TPL_VAR["cfg"]["shopName"]?>쇼핑몰<A 
href="http://<?php echo $TPL_VAR["cfg"]["shopUrl"]?>/"><FONT 
color=#585858>(<STRONG>http://<?php echo $TPL_VAR["cfg"]["shopUrl"]?></STRONG>)</FONT></A> 가 
되겠습니다.<BR><BR>감사합니다. </TD></TR></TBODY></TABLE></TD></TR>
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