<TABLE style="BORDER-RIGHT: #cccccc 1px solid; BORDER-TOP: #cccccc 1px solid; BORDER-LEFT: #cccccc 1px solid; BORDER-BOTTOM: #cccccc 1px solid" cellSpacing=0 cellPadding=10 width=640 border=0>
<TBODY>
<TR>
<TD><!--메일 상단 : Start -->
<DIV><IMG src="/shop/admin/img/mail/mail_bar_qna.gif"></DIV><!--메일 상단 : End --><!--본문 부분 : Start -->
<TABLE style="MARGIN: 10px 0px" cellSpacing=0 cellPadding=6 width=610 align=center border=0>
<TBODY>
<TR>
<TD bgColor=#f2f2f2>
<TABLE cellSpacing=0 cellPadding=8 width="100%" border=0>
<TBODY>
<TR>
<TD style="FONT: 9pt/20px 돋움; COLOR: #585858" vAlign=top width=63><B>질문제목 :</B></TD>
<TD>{questiontitle}</TD></TR><!--{ ? question != '' }-->
<TR>
<TD style="BORDER-TOP: #e6e6e6 1px solid; FONT: 9pt/20px 돋움; COLOR: #585858" vAlign=top><B>질문내용 :</B></TD>
<TD style="BORDER-TOP: #e6e6e6 1px solid">{question}</TD></TR><!--{ / }--><!--{ ? answertitle != '' }-->
<TR>
<TD style="BORDER-TOP: #e6e6e6 1px solid; FONT: 9pt/20px 돋움; COLOR: #585858" vAlign=top><B>답변제목 :</B></TD>
<TD style="BORDER-TOP: #e6e6e6 1px solid">{answertitle}</TD></TR><!--{ / }--><!--{ ? answer != '' }-->
<TR>
<TD style="BORDER-TOP: #e6e6e6 1px solid; FONT: 9pt/20px 돋움; COLOR: #585858" vAlign=top><B>답변 :</B></TD>
<TD style="BORDER-TOP: #e6e6e6 1px solid">{answer}</TD></TR><!--{ / }--></TBODY></TABLE></TD></TR></TBODY></TABLE>
<TABLE style="MARGIN-BOTTOM: 16px; FONT: 9pt/20px 돋움; COLOR: #585858" cellSpacing=0 cellPadding=0 width=610 align=center border=0>
<TBODY>
<TR>
<TD style="PADDING-LEFT: 10px" height=60>기타 문의사항이 있으시면, <A href="mailto:{cfg.adminEmail}"><STRONG><FONT color=#585858>{cfg.adminEmail}</FONT></STRONG></A> 로 연락주시기 바랍니다.<BR>{cfg.shopName} 쇼핑몰을 이용해 주셔서 감사합니다. </TD></TR></TBODY></TABLE><!--본문 부분 : End --><!--메일 하단 : Start -->
<TABLE style="BORDER-RIGHT: #ffffff 0px solid; BORDER-TOP: #dddddd 1px solid; MARGIN-TOP: 10px; BORDER-LEFT: #ffffff 0px solid; BORDER-BOTTOM: #cfcfcf 1px solid" cellSpacing=0 cellPadding=0 width=640 border=0>
<TBODY>
<TR>
<TD style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; PADDING-BOTTOM: 10px; PADDING-TOP: 10px" align=middle height=20>
<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
<TBODY>
<TR>
<TD align=right width=200><!--{ @ dataBanner( 92 ) }-->{.tag}<!--{ / }--></TD>
<TD align=middle>
<TABLE cellSpacing=0 cellPadding=2 width="95%" border=0>
<TBODY>
<TR>
<TD><IMG src="/shop/admin/img/mail/mail_bottom.gif"></TD></TR>
<TR>
<TD style="FONT: 8pt verdana"><FONT color=#585858>Copyright(C) <STRONG>{cfg.shopName}</STRONG> All right reserved.</FONT></TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE><!--메일 하단 : End --></TD></TR></TBODY></TABLE>