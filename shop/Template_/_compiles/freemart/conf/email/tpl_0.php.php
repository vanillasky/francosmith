<?php /* Template_ 2.2.7 2016/05/02 08:09:28 /www/francotr3287_godo_co_kr/shop/conf/email/tpl_0.php 000005842 */  $this->include_("dataBanner");?>
<div style="padding: 5px; border: 2px solid rgb(207, 207, 207); width: 644px; height: 964px;">
<table style="font: 9pt/normal 굴림; font-size-adjust: none; font-stretch: normal;">
<tbody>
<tr>
<td><img src="/shop/admin/img/mail/mail_bar_order.gif"></td></tr>
<tr>
<td height="400" valign="top" style="padding: 5px;">
<div style="padding: 10px; line-height: 150%;">고객님, 
저희 쇼핑몰을 이용해 주셔서 감사합니다.<br><?php echo $TPL_VAR["nameOrder"]?>님께서 주문하신 제품이 주문 접수 되었습니다.<br>주문내역 및 배송정보는 
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
<td><b><?php echo $TPL_VAR["ordno"]?></b></td></tr>
<tr>
<td>주문하시는 분</td>
<td><?php echo $TPL_VAR["nameOrder"]?></td></tr>
<tr>
<td>전화번호</td>
<td><?php echo $TPL_VAR["phoneOrder"]?></td></tr>
<tr>
<td>핸드폰</td>
<td><?php echo $TPL_VAR["mobileOrder"]?></td></tr>
<tr>
<td>결제방법</td>
<td><?php echo $TPL_VAR["str_settlekind"]?></td></tr>
<tr>
<td>결제금액</td>
<td><strong><?php echo number_format($TPL_VAR["settleprice"])?>원</strong></td></tr>
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
<td><?php echo $TPL_VAR["nameReceiver"]?></td></tr>
<tr>
<td>주소</td>
<td>[<?php echo $TPL_VAR["zipcode"]?>] <?php echo $TPL_VAR["address"]?></td></tr>
<tr>
<td>전화번호</td>
<td><?php echo $TPL_VAR["phoneReceiver"]?></td></tr>
<tr>
<td>핸드폰</td>
<td><?php echo $TPL_VAR["mobileReceiver"]?></td></tr>
<tr>
<td>배송메세지</td>
<td><?php echo $TPL_VAR["memo"]?></td></tr>
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
<col width="80"><?php if((is_array($TPL_R1=$TPL_VAR["cart"]->item)&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
<tbody>
<tr>
<td height="60" align="middle"><?php echo goodsimg($TPL_V1["img"], 40,'', 3)?></td>
<td>
<div><?php echo $TPL_V1["goodsnm"]?> <?php if($TPL_V1["opt"]){?>[<?php echo implode("/",$TPL_V1["opt"])?>]<?php }?></div><?php if((is_array($TPL_R2=$TPL_V1["addopt"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>[<?php echo $TPL_V2["optnm"]?>:<?php echo $TPL_V2["opt"]?>]<?php }}?> 
<?php if($TPL_V1["delivery_type"]== 1){?>
<div>(무료배송)</div><?php }?></td>
<td align="middle"><?php echo number_format($TPL_V1["reserve"])?>원</td>
<td align="right" style="padding-right: 10px;"><?php echo number_format($TPL_V1["price"]+$TPL_V1["addprice"])?>원</td>
<td align="middle"><?php echo $TPL_V1["ea"]?>개</td>
<td align="right" style="padding-right: 10px;"><?php echo number_format(($TPL_V1["price"]+$TPL_V1["addprice"])*$TPL_V1["ea"])?>원</td></tr>
<tr>
<td height="1" bgcolor="#dedede" colspan="10"></td></tr><?php }}?>
<tr>
<td height="60" align="right" bgcolor="#f7f7f7" colspan="10">상품합계금액 &nbsp;<b id="cart_goodsprice"><?php echo number_format($TPL_VAR["cart"]->goodsprice)?></b>원 &nbsp; + &nbsp; 
배송비&nbsp;<?php if($TPL_VAR["deli_msg"]){?><?php echo $TPL_VAR["deli_msg"]?><?php }else{?><b id="cart_delivery"><?php echo number_format($TPL_VAR["cart"]->delivery)?></b>원<?php }?>&nbsp; = 
&nbsp; 총주문금액 &nbsp;<b class="red" id="cart_totalprice"><?php echo number_format($TPL_VAR["cart"]->totalprice)?></b>원 &nbsp; </td></tr>
<tr>
<td height="1" bgcolor="#efefef" colspan="10"></td></tr></tbody></table></div></td></tr>
<tr>
<td height="1" bgcolor="#cfcfcf"></td></tr>
<tr>
<td align="middle" style="padding: 10px;">
<table>
<tbody>
<tr>
<td rowspan="2"><?php if((is_array($TPL_R1=dataBanner( 92))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td>
<td><img src="/shop/admin/img/mail/mail_bottom.gif"></td></tr>
<tr>
<td style="font: 8pt/normal tahoma; font-size-adjust: none; font-stretch: normal;">Copyright(C) <strong><font color="#585858"><?php echo $TPL_VAR["cfg"]["shopName"]?>

</font></strong>All right reserved.</td></tr></tbody></table></td></tr>
<tr>
<td height="10" bgcolor="#cfcfcf"></td></tr></tbody></table></div>