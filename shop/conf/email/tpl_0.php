<div style="padding: 5px; border: 2px solid rgb(207, 207, 207); width: 644px; height: 964px;">
<table style="font: 9pt/normal ����; font-size-adjust: none; font-stretch: normal;">
<tbody>
<tr>
<td><img src="/shop/admin/img/mail/mail_bar_order.gif"></td></tr>
<tr>
<td height="400" valign="top" style="padding: 5px;">
<div style="padding: 10px; line-height: 150%;">����, 
���� ���θ��� �̿��� �ּż� �����մϴ�.<br>{nameOrder}�Բ��� �ֹ��Ͻ� ��ǰ�� �ֹ� ���� �Ǿ����ϴ�.<br>�ֹ����� �� ��������� 
MY Shopping���� �ֹ�/�����ȸ���� Ȯ���Ͻ� �� �ֽ��ϴ�.<br>���Բ� ������ ��Ȯ�ϰ� ��ǰ�� ���޵� �� �ֵ��� �ּ��� 
���ϰڽ��ϴ�.</div>
<div style="padding: 5px; border: 5px solid rgb(239, 239, 239);">
<div style="background: rgb(247, 247, 247); padding: 7px 0px 0px 10px; height: 25px;"><b>- 
�ֹ��� ����</b></div>
<table style="font: 9pt/normal ����; font-size-adjust: none; font-stretch: normal;" cellpadding="2">
<colgroup>
<col width="100">
<tbody>
<tr>
<td height="5"></td></tr>
<tr>
<td>�ֹ���ȣ</td>
<td><b>{ordno}</b></td></tr>
<tr>
<td>�ֹ��Ͻô� ��</td>
<td>{nameOrder}</td></tr>
<tr>
<td>��ȭ��ȣ</td>
<td>{phoneOrder}</td></tr>
<tr>
<td>�ڵ���</td>
<td>{mobileOrder}</td></tr>
<tr>
<td>�������</td>
<td>{str_settlekind}</td></tr>
<tr>
<td>�����ݾ�</td>
<td><strong>{=number_format(settleprice)}��</strong></td></tr>
<tr>
<td height="10"><strong></strong></td></tr></tbody></table>
<div style="background: rgb(247, 247, 247); padding: 7px 0px 0px 10px; height: 25px;"><b>- 
��� ����</b></div>
<table style="font: 9pt/normal ����; font-size-adjust: none; font-stretch: normal;" cellpadding="2">
<colgroup>
<col width="100">
<tbody>
<tr>
<td height="5"></td></tr>
<tr>
<td>�����ô� ��</td>
<td>{nameReceiver}</td></tr>
<tr>
<td>�ּ�</td>
<td>[{zipcode}] {address}</td></tr>
<tr>
<td>��ȭ��ȣ</td>
<td>{phoneReceiver}</td></tr>
<tr>
<td>�ڵ���</td>
<td>{mobileReceiver}</td></tr>
<tr>
<td>��۸޼���</td>
<td>{memo}</td></tr>
<tr>
<td height="10"></td></tr></tbody></table>
<div style="background: rgb(247, 247, 247); padding: 7px 0px 0px 10px; height: 25px;"><b>- 
���Ż�ǰ ����</b></div>
<table width="100%" style="font: 9pt/normal ����; font-size-adjust: none; font-stretch: normal;" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td height="2" bgcolor="#303030" colspan="10"></td></tr>
<tr height="23" bgcolor="#f0f0f0">
<th class="input_txt" colspan="2">��ǰ����</th>
<th class="input_txt">������</th>
<th class="input_txt">�ǸŰ�</th>
<th class="input_txt">����</th>
<th class="input_txt">�հ�</th></tr>
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
<div>(������)</div><!--{ / }--></td>
<td align="middle">{=number_format(.reserve)}��</td>
<td align="right" style="padding-right: 10px;">{=number_format(.price + 
.addprice)}��</td>
<td align="middle">{.ea}��</td>
<td align="right" style="padding-right: 10px;">{=number_format((.price + 
.addprice)*.ea)}��</td></tr>
<tr>
<td height="1" bgcolor="#dedede" colspan="10"></td></tr><!--{ / }-->
<tr>
<td height="60" align="right" bgcolor="#f7f7f7" colspan="10">��ǰ�հ�ݾ� &nbsp;<b id="cart_goodsprice">{=number_format(cart->goodsprice)}</b>�� &nbsp; + &nbsp; 
��ۺ�&nbsp;<!--{ ? deli_msg }-->{deli_msg}<!--{ : }--><b id="cart_delivery">{=number_format(cart->delivery)}</b>��<!--{ / }-->&nbsp; = 
&nbsp; ���ֹ��ݾ� &nbsp;<b class="red" id="cart_totalprice">{=number_format(cart->totalprice)}</b>�� &nbsp; </td></tr>
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