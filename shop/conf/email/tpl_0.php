<div style="padding: 5px; border: 2px solid rgb(207, 207, 207); width: 644px; height: 964px;">
<table style="font: 9pt/normal 굴림; font-size-adjust: none; font-stretch: normal;">
<tbody>
<tr>
<td><img src="/shop/admin/img/mail/mail_bar_order.gif"></td></tr>
<tr>
<td height="400" valign="top" style="padding: 5px;">
<div style="padding: 10px; line-height: 150%;">고객님, 
저희 쇼핑몰을 이용해 주셔서 감사합니다.<br>{nameOrder}님께서 주문하신 제품이 주문 접수 되었습니다.<br>주문내역 및 배송정보는 
MY Shopping에서 주문/배송조회에서 확인하실 수 있습니다.<br>고객님께 빠르고 정확하게 제품이 전달될 수 있도록 최선을 
다하겠습니다.</div>
<div style="padding: 5px; border: 5px solid rgb(239, 239, 239);">
<div style="background: rgb(247, 247, 247); padding: 7px 0px 0px 10px; height: 25px;"><b>- 
주문자 정보</b></div>
<table style="font: 9pt/normal 굴림; font-size-adjust: none; font-stretch: normal;" cellpadding="2">
<colgroup>
<col width="100">
<tbody>
<tr>
<td height="5"></td></tr>
<tr>
<td>주문번호</td>
<td><b>{ordno}</b></td></tr>
<tr>
<td>주문하시는 분</td>
<td>{nameOrder}</td></tr>
<tr>
<td>전화번호</td>
<td>{phoneOrder}</td></tr>
<tr>
<td>핸드폰</td>
<td>{mobileOrder}</td></tr>
<tr>
<td>결제방법</td>
<td>{str_settlekind}</td></tr>
<tr>
<td>결제금액</td>
<td><strong>{=number_format(settleprice)}원</strong></td></tr>
<tr>
<td height="10"><strong></strong></td></tr></tbody></table>
<div style="background: rgb(247, 247, 247); padding: 7px 0px 0px 10px; height: 25px;"><b>- 
배송 정보</b></div>
<table style="font: 9pt/normal 굴림; font-size-adjust: none; font-stretch: normal;" cellpadding="2">
<colgroup>
<col width="100">
<tbody>
<tr>
<td height="5"></td></tr>
<tr>
<td>받으시는 분</td>
<td>{nameReceiver}</td></tr>
<tr>
<td>주소</td>
<td>[{zipcode}] {address}</td></tr>
<tr>
<td>전화번호</td>
<td>{phoneReceiver}</td></tr>
<tr>
<td>핸드폰</td>
<td>{mobileReceiver}</td></tr>
<tr>
<td>배송메세지</td>
<td>{memo}</td></tr>
<tr>
<td height="10"></td></tr></tbody></table>
<div style="background: rgb(247, 247, 247); padding: 7px 0px 0px 10px; height: 25px;"><b>- 
구매상품 정보</b></div>
<table width="100%" style="font: 9pt/normal 굴림; font-size-adjust: none; font-stretch: normal;" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td height="2" bgcolor="#303030" colspan="10"></td></tr>
<tr height="23" bgcolor="#f0f0f0">
<th class="input_txt" colspan="2">상품정보</th>
<th class="input_txt">적립금</th>
<th class="input_txt">판매가</th>
<th class="input_txt">수량</th>
<th class="input_txt">합계</th></tr>
<tr>
<td height="1" bgcolor="#d6d6d6" colspan="10"></td></tr>
<colgroup>
<col width="60">
<col>
<col width="60">
<col width="80">
<col width="50">
<col width="80"><!--{ @ cart->item }-->
<tbody>
<tr>
<td height="60" align="middle">{=goodsimg(.img,40,'',3)}</td>
<td>
<div>{.goodsnm} <!--{ ? .opt }-->[{=implode("/",.opt)}]<!--{ / }--></div><!--{ @ .addopt }-->[{..optnm}:{..opt}]<!--{ / }--> 
<!--{ ? .delivery_type == 1 }-->
<div>(무료배송)</div><!--{ / }--></td>
<td align="middle">{=number_format(.reserve)}원</td>
<td align="right" style="padding-right: 10px;">{=number_format(.price + 
.addprice)}원</td>
<td align="middle">{.ea}개</td>
<td align="right" style="padding-right: 10px;">{=number_format((.price + 
.addprice)*.ea)}원</td></tr>
<tr>
<td height="1" bgcolor="#dedede" colspan="10"></td></tr><!--{ / }-->
<tr>
<td height="60" align="right" bgcolor="#f7f7f7" colspan="10">상품합계금액 &nbsp;<b id="cart_goodsprice">{=number_format(cart->goodsprice)}</b>원 &nbsp; + &nbsp; 
배송비&nbsp;<!--{ ? deli_msg }-->{deli_msg}<!--{ : }--><b id="cart_delivery">{=number_format(cart->delivery)}</b>원<!--{ / }-->&nbsp; = 
&nbsp; 총주문금액 &nbsp;<b class="red" id="cart_totalprice">{=number_format(cart->totalprice)}</b>원 &nbsp; </td></tr>
<tr>
<td height="1" bgcolor="#efefef" colspan="10"></td></tr></tbody></table></div></td></tr>
<tr>
<td height="1" bgcolor="#cfcfcf"></td></tr>
<tr>
<td align="middle" style="padding: 10px;">
<table>
<tbody>
<tr>
<td rowspan="2"><!--{ @ dataBanner( 92 ) }-->{.tag}<!--{ / }--></td>
<td><img src="/shop/admin/img/mail/mail_bottom.gif"></td></tr>
<tr>
<td style="font: 8pt/normal tahoma; font-size-adjust: none; font-stretch: normal;">Copyright(C) <strong><font color="#585858">{cfg.shopName} 
</font></strong>All right reserved.</td></tr></tbody></table></td></tr>
<tr>
<td height="10" bgcolor="#cfcfcf"></td></tr></tbody></table></div>