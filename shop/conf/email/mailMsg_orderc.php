<DIV 
style="BORDER-BOTTOM: #cfcfcf 2px solid; BORDER-LEFT: #cfcfcf 2px solid; PADDING-BOTTOM: 5px; PADDING-LEFT: 5px; WIDTH: 100px; PADDING-RIGHT: 5px; BORDER-TOP: #cfcfcf 2px solid; BORDER-RIGHT: #cfcfcf 2px solid; PADDING-TOP: 5px">
<TABLE style="FONT: 9pt 굴림">
<TBODY>
<TR>
<TD><IMG src="/shop/admin/img/mail/mail_bar_order.gif"></TD></TR>
<TR>
<TD 
style="PADDING-BOTTOM: 5px; PADDING-LEFT: 5px; PADDING-RIGHT: 5px; PADDING-TOP: 5px" 
height=400 vAlign=top>
<DIV 
style="PADDING-BOTTOM: 10px; LINE-HEIGHT: 150%; PADDING-LEFT: 10px; PADDING-RIGHT: 10px; PADDING-TOP: 10px">고객님, 
저희 쇼핑몰을 이용해 주셔서 감사합니다.<BR>{nameOrder}님께서 주문하신 상품이 주문 접수 되었습니다.<BR>주문내역 및 쿠폰 정보는 
마이페이지에서 주문/배송조회에서 확인하실 수 있습니다.<BR>구매 목표량이 있는 쿠폰의 경우,&nbsp;쿠폰번호는 구매 목표량에 도달하여 판매 
성공시에 일괄 발송됩니다.<BR></DIV>
<DIV 
style="BORDER-BOTTOM: #efefef 5px solid; BORDER-LEFT: #efefef 5px solid; PADDING-BOTTOM: 5px; PADDING-LEFT: 5px; PADDING-RIGHT: 5px; BORDER-TOP: #efefef 5px solid; BORDER-RIGHT: #efefef 5px solid; PADDING-TOP: 5px">
<DIV 
style="PADDING-BOTTOM: 0px; PADDING-LEFT: 10px; PADDING-RIGHT: 0px; BACKGROUND: #f7f7f7; HEIGHT: 25px; PADDING-TOP: 7px"><B>- 
구매상품 정보</B></DIV>
<TABLE style="FONT: 9pt 굴림" cellSpacing=0 cellPadding=0 width="100%">
<TBODY>
<TR>
<TD bgColor=#303030 height=2 colSpan=5></TD></TR>
<TR height=23 bgColor=#f0f0f0>
<TH class=input_txt colSpan=2>상품정보</TH>
<TH class=input_txt>판매가</TH>
<TH class=input_txt>수량</TH>
<TH class=input_txt>합계</TH></TR>
<TR>
<TD bgColor=#d6d6d6 height=1 colSpan=5></TD></TR>
<COLGROUP>
<COL width=60>
<COL>
<COL width=80>
<COL width=50>
<COL width=80></COLGROUP>
<TBODY>
<TR>
<TD height=60 align=middle>{=goodsimg(img,40,'',3)}</TD>
<TD>
<DIV>{goodsnm} {option}</DIV></TD>
<TD style="PADDING-RIGHT: 10px" align=right>{=number_format(price)}원</TD>
<TD align=middle>{ea}개</TD>
<TD style="PADDING-RIGHT: 10px" 
align=right>{=number_format((price)*ea)}원</TD></TR>
<TR>
<TD bgColor=#dedede height=1 colSpan=5></TD></TR>
<TR>
<TD bgColor=#f7f7f7 height=60 colSpan=5 align=right>총주문금액 &nbsp;<B 
id=cart_totalprice class=red>{=number_format(totalprice)}</B>원 &nbsp; </TD></TR>
<TR>
<TD bgColor=#efefef height=1 colSpan=5></TD></TR>
<TR>
<TD height=10 colSpan=5><STRONG></STRONG></TD></TR></TBODY></TABLE>
<DIV 
style="PADDING-BOTTOM: 0px; PADDING-LEFT: 10px; PADDING-RIGHT: 0px; BACKGROUND: #f7f7f7; HEIGHT: 25px; PADDING-TOP: 7px"><B>- 
주문자 정보</B></DIV>
<TABLE style="FONT: 9pt 굴림" cellPadding=2>
<COLGROUP>
<COL width=100></COLGROUP>
<TBODY>
<TR>
<TD height=5></TD></TR>
<TR>
<TD>주문번호</TD>
<TD><B>{ordno}</B></TD></TR>
<TR>
<TD>주문하시는 분</TD>
<TD>{nameOrder}</TD></TR>
<TR>
<TD>전화번호</TD>
<TD>{phoneOrder}</TD></TR>
<TR>
<TD>핸드폰</TD>
<TD>{mobileOrder}</TD></TR>
<TR>
<TD>결제방법</TD>
<TD>{str_settlekind}</TD></TR>
<TR>
<TD>결제금액</TD>
<TD><STRONG>{=number_format(settleprice)}원</STRONG></TD></TR>
<TR>
<TD height=10><STRONG></STRONG></TD></TR></TBODY></TABLE>
<DIV 
style="PADDING-BOTTOM: 0px; PADDING-LEFT: 10px; PADDING-RIGHT: 0px; BACKGROUND: #f7f7f7; HEIGHT: 25px; PADDING-TOP: 7px"><B>- 
받는 사람 정보</B></DIV>
<TABLE style="FONT: 9pt 굴림" cellPadding=2>
<COLGROUP>
<COL width=100>
<COLGROUP>
<TBODY>
<TR>
<TD height=5></TD></TR>
<TR>
<TD>받으시는 분</TD>
<TD>{nameReceiver}</TD></TR>
<TR>
<TD>핸드폰</TD>
<TD>{mobileReceiver}</TD></TR>
<TR>
<TD>전하는 메세지</TD>
<TD>{memo}</TD></TR>
<TR>
<TD height=10></TD></TR></TBODY></TABLE></DIV></TD></TR>
<TR>
<TD bgColor=#cfcfcf height=1></TD></TR>
<TR>
<TD 
style="PADDING-BOTTOM: 10px; PADDING-LEFT: 10px; PADDING-RIGHT: 10px; PADDING-TOP: 10px" 
align=middle>
<TABLE>
<TBODY>
<TR>
<TD rowSpan=2><!--{ @ dataBanner( 92 ) }-->{.tag}<!--{ / }--></TD>
<TD><IMG src="/shop/admin/img/mail/mail_bottom.gif"></TD></TR>
<TR>
<TD style="FONT: 8pt tahoma">Copyright(C) <STRONG><FONT 
color=#585858>{cfg.shopName} </FONT></STRONG>All right 
reserved.</TD></TR></TBODY></TABLE></TD></TR>
<TR>
<TD bgColor=#cfcfcf height=10></TD></TR></TBODY></TABLE></DIV>