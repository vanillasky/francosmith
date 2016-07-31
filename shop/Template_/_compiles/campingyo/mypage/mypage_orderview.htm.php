<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/mypage/mypage_orderview.htm 000032081 */  $this->include_("displayEggBanner");
if (is_array($TPL_VAR["item"])) $TPL_item_1=count($TPL_VAR["item"]); else if (is_object($TPL_VAR["item"]) && in_array("Countable", class_implements($TPL_VAR["item"]))) $TPL_item_1=$TPL_VAR["item"]->count();else $TPL_item_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<style>
#orderbox {border:5px solid #F3F3F3; padding:5px 10px;}
#orderbox table th {width:100;}
</style>
<script id="delivery"></script>

<!-- 상단이미지 || 현재위치 -->
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td><img src="/shop/data/skin/campingyo/img/common/title_orderdetail.gif" border=0></td></tr>
<tr><td class="path">home > 마이페이지 > <b>주문내역상세보기</b></td></tr>
</table><p>

<div class="indiv"><!-- Start indiv -->

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td height=2 bgcolor="#303030" colspan=10></td></tr>
<tr bgcolor=#F0F0F0 height=23>
	<th colspan=2 class="input_txt">상품정보</th>
	<th class="input_txt">판매가</th>
	<th class="input_txt">수량</th>
	<th class="input_txt">배송상태</th>
	<th class="input_txt">배송추적<br/>/이용후기</th>
</tr>
<tr><td height=1 bgcolor="#D6D6D6" colspan=10></td></tr>
<?php if($TPL_item_1){foreach($TPL_VAR["item"] as $TPL_V1){?>
<tr>
	<td align=center width=60 height=60><a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo goodsimg($TPL_V1["img_s"], 50)?></a></td>
	<td>
	<a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo $TPL_V1["goodsnm"]?>

<?php if($TPL_V1["opt1"]){?>[<?php echo $TPL_V1["opt1"]?><?php if($TPL_V1["opt2"]){?>/<?php echo $TPL_V1["opt2"]?><?php }?>]<?php }?>
<?php if($TPL_V1["addopt"]){?><div>[<?php echo str_replace("^","] [",$TPL_V1["addopt"])?>]</div><?php }?></a>
	</td>
	<td align=center><?php echo number_format($TPL_V1["price"])?>원</td>
	<td align=center><?php echo number_format($TPL_V1["ea"])?>개</td>
	<td align=center class=stxt><FONT COLOR="#007FC8"><?php echo $GLOBALS["r_istep"][$TPL_V1["istep"]]?></FONT></td>
	<td align=center>
<?php if($GLOBALS["set"]["delivery"]["basis"]&&$TPL_V1["dvcode"]){?>
	<a href="javascript:popup('mypage_delivery.php?item_sno=<?php echo $TPL_V1["sno"]?>',600,600)"><img src="/shop/data/skin/campingyo/img/common/btn_chase.gif"></a>
<?php }elseif(!$GLOBALS["set"]["delivery"]["basis"]&&$TPL_VAR["deliverycode"]){?>
	<a href="javascript:popup('mypage_delivery.php?ordno=<?php echo $TPL_VAR["ordno"]?>',600,600)"><img src="/shop/data/skin/campingyo/img/common/btn_chase.gif"></a>
<?php }?>
<?php if($TPL_V1["istep"]== 4){?><a href="javascript:;" onclick="popup_register( 'add_review', '<?php echo $TPL_V1["goodsno"]?>' )"><img src="/shop/data/skin/campingyo/img/common/btn_review.gif"></a><?php }?>
	</td>
</tr>
<tr><td colspan=10 height=1 bgcolor=#DEDEDE></td></tr>
<?php }}?>
</table><p>

<img src="/shop/data/skin/campingyo/img/common/order_txt_01.gif" border=0>
<!-- 01 주문자정보 -->
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"><img src="/shop/data/skin/campingyo/img/common/order_step_01.gif"></td>
	<td id="orderbox">

	<table>
	<col width=100>
	<tr>
		<td>주문자명</td>
		<td><?php echo $TPL_VAR["nameOrder"]?></td>
	</tr>
	<tr>
		<td>주문자 전화</td>
		<td><?php echo $TPL_VAR["phoneOrder"]?></td>
	</tr>
	<tr>
		<td>주문자 핸드폰</td>
		<td><?php echo $TPL_VAR["mobileOrder"]?></td>
	</tr>
	<tr>
		<td>이메일</td>
		<td><?php echo $TPL_VAR["email"]?></td>
	</tr>
	</table>

	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>

<!-- 02 배송정보 -->
<?php if($TPL_VAR["step"]<= 1){?>

	<form name="frmOrder" method="post" action="<?php echo url("mypage/indb.php")?>&" onsubmit="return chkForm(this)">
	<input type="hidden" name="mode" value="modReceiver">
	<input type="hidden" name="ordno" value="<?php echo $TPL_VAR["ordno"]?>">

<?php }?>
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"><img src="/shop/data/skin/campingyo/img/common/order_step_02.gif"></td>
	<td id="orderbox">

	<table>
	<col width=100>
	<tr>
		<td>받는자명</td>
		<td><?php echo $TPL_VAR["nameReceiver"]?></td>
	</tr>
	<tr>
		<td>받는자 전화</td>
		<td><?php echo $TPL_VAR["phoneReceiver"]?></td>
	</tr>
	<tr>
		<td>받는자 핸드폰</td>
		<td><?php echo $TPL_VAR["mobileReceiver"]?></td>
	</tr>
	<tr>
		<td>우편번호</td>
		<td><?php echo $TPL_VAR["zipcode"]?></td>
	</tr>
	<tr>
		<td>주소</td>
		<td><?php echo $TPL_VAR["address"]?> <?php echo $TPL_VAR["address_sub"]?><div style="padding-top:5px;font:12px dotum;color:#999;"><?php echo $TPL_VAR["road_address"]?> <?php echo $TPL_VAR["address_sub"]?></div></td>
	</tr>
<?php if($TPL_VAR["memo"]){?>
	<tr>
		<td>배송메세지</td>
		<td><?php echo $TPL_VAR["memo"]?></td>
	</tr>
<?php }?>
<?php if($TPL_VAR["deliverycode"]){?>
	<tr>
		<td>송장번호</td>
		<td><?php echo $TPL_VAR["deliverycomp"]?> <?php echo $TPL_VAR["deliverycode"]?></td>
	</tr>
<?php }?>
	</table>

	</td>
</tr>
</table>

<div style="font-size:0;height:5px"></div>

<!-- 03 결제금액 -->
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"><img src="/shop/data/skin/campingyo/img/common/order_step_03.gif"></td>
	<td id="orderbox">

	<table>
	<col width=100><col align="right">
	<tr>
		<td>총주문금액</td>
		<td><span id="paper_goodsprice"><?php echo number_format($TPL_VAR["goodsprice"])?></span>원</td>
	</tr>
	<tr>
		<td>배송비</td>
		<td><div id="paper_delivery_msg1" <?php if($TPL_VAR["deli_msg"]){?>style="display:none"<?php }?>><span id="paper_delivery"><?php echo number_format($TPL_VAR["delivery"])?></span>원</div>
		<div id="paper_delivery_msg2" style="float:left;margin:0;" <?php if(!$TPL_VAR["deli_msg"]){?>style="display:none"<?php }?>><?php echo $TPL_VAR["deli_msg"]?></div></td>
	</tr>
<?php if($TPL_VAR["item"][ 0]['todaygoods']!='y'){?>
	<tr>
		<td>회원할인</td>
		<td>- <span id="paper_memberdc"><?php echo number_format($TPL_VAR["memberdc"])?></span>원</td>
	</tr>
	<tr>
		<td>쿠폰할인</td>
		<td>- <span id="paper_coupon"><?php echo number_format($TPL_VAR["coupon"])?></span>원</td>
	</tr>
	<tr>
		<td>적립금 사용</td>
		<td>- <span id="paper_emoney"><?php echo number_format($GLOBALS["data"]["emoney"])?></span>원</td>
	</tr>
<?php if($GLOBALS["data"]["ncash_emoney"]){?>
	<tr>
		<td>네이버마일리지</td>
		<td>- <span id="paper_emoney"><?php echo number_format($GLOBALS["data"]["ncash_emoney"])?></span>원</td>
	</tr>
<?php }?>
<?php if($GLOBALS["data"]["ncash_cash"]){?>
	<tr>
		<td>네이버캐쉬</td>
		<td>- <span id="paper_emoney"><?php echo number_format($GLOBALS["data"]["ncash_cash"])?></span>원</td>
	</tr>
<?php }?>
<?php }?>
<?php if($TPL_VAR["eggFee"]){?>
	<tr>
		<td>보증보험 수수료</td>
		<td><span id="paper_eggfee"><?php echo number_format($TPL_VAR["eggFee"])?></span>원</td>
	</tr>
<?php }?>
	<tr>
		<td>결제금액</td>
		<td><b><span id="paper_settlement"><?php echo number_format($TPL_VAR["settleprice"])?></span>원</b></td>
	</tr>
<?php if($TPL_VAR["canceled_price"]){?>
	<tr>
		<td>취소금액</td>
		<td><b><?php echo number_format($TPL_VAR["canceled_price"])?>원</b></td>
	</tr>
<?php }?>
	</table>
	<div style="color: #007fc8; font-size: 11px; margin: 3px 0 0 3px">※ 네이버 마일리지가 사용된 결제시도건의 경우 마일리지 사용액이 저장되지 않아 재결제시에<br/>결제금액이 다르게 보일 수 있습니다.</div>
	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>
<?php if($TPL_VAR["step2"]== 50||$TPL_VAR["step2"]== 54){?>
<input type="hidden" name="settlekind" value="<?php echo $TPL_VAR["settlekind"]?>">
<input type="hidden" name="escrowyn" value="<?php echo $TPL_VAR["escrowyn"]?>">
<!-- 구매안전표시 start --><?php echo displayEggBanner( 1)?><!-- 구매안전표시 end -->
<?php }?>
<!-- 04 결제수단 -->
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"><img src="/shop/data/skin/campingyo/img/common/order_step_04.gif"></td>
	<td id="orderbox">

	<table>
	<col width=100>
<?php if($TPL_VAR["settlekind"]=="a"){?>
	<tr>
		<td>입금은행</td>
		<td><?php echo $TPL_VAR["bank"]?></td>
	</tr>
	<tr>
		<td>입금계좌</td>
		<td><?php echo $TPL_VAR["account"]?></td>
	</tr>
	<tr>
		<td>예금주명</td>
		<td><?php echo $TPL_VAR["name"]?></td>
	</tr>
	<tr>
		<td>입금자명</td>
		<td><?php echo $TPL_VAR["bankSender"]?></td>
	</tr>

<?php }elseif($TPL_VAR["settlekind"]=="c"){?>
	<tr>
		<td>결제방법</td>
		<td>신용카드</td>
	</tr>

<?php }elseif($TPL_VAR["settlekind"]=="o"){?>
	<tr>
		<td>결제방법</td>
		<td>계좌이체</td>
	</tr>

<?php }elseif($TPL_VAR["settlekind"]=="v"){?>
	<tr>
		<td>가상계좌</td>
		<td><?php echo $TPL_VAR["vAccount"]?></td>
	</tr>

<?php }elseif($TPL_VAR["settlekind"]=="h"){?>
	<tr>
		<td>결제방법</td>
		<td>핸드폰</td>
	</tr>

<?php }elseif($TPL_VAR["settlekind"]=="p"){?>
	<tr>
		<td>결제방법</td>
		<td>포인트결제</td>
	</tr>

<?php }elseif($TPL_VAR["settlekind"]=="d"){?>
	<tr>
		<td>결제방법</td>
		<td>전액할인 결제 (적립금 사용)</td>
	</tr>

<?php }elseif($TPL_VAR["settlekind"]=="u"){?>
	<tr>
		<td>결제방법</td>
		<td>CUP (중국 은행연합 카드)</td>
	</tr>

<?php if($TPL_VAR["memberdc"]){?>
	<tr>
		<td>회원할인</td>
		<td id="memberdc"><?php echo number_format($TPL_VAR["memberdc"])?>원</td>
	</tr>
<?php }?>
<?php if($TPL_VAR["coupon"]){?>
	<tr>
		<td>쿠폰할인</td>
		<td><?php echo number_format($TPL_VAR["coupon"])?>원</td>
	</tr>
<?php }?>
	<tr>
		<td>적립금결제</td>
		<td><b><?php echo number_format($TPL_VAR["emoney"])?>원</b></td>
	</tr>
<?php }?>
<?php if($TPL_VAR["step"]== 0&&$TPL_VAR["step2"]== 54&&in_array($TPL_VAR["settlekind"],array('c','o','v'))&&$TPL_VAR["pgfailreason"]){?><!-- 결제실패사유 -->
	<tr>
		<td>결제실패사유</td>
		<td><?php echo $TPL_VAR["pgfailreason"]?></td>
	</tr>
<?php }?>
<?php if($TPL_VAR["eggyn"]=='y'){?>
	<tr>
		<td>전자보증보험</td>
		<td><a href="javascript:popupEgg('<?php echo $GLOBALS["egg"]["usafeid"]?>', '<?php echo $TPL_VAR["ordno"]?>')"><font color=#0074BA><b><u><?php echo $TPL_VAR["eggno"]?> <font class=small>(보증서출력)</a></td>
	</tr>
<?php }elseif($GLOBALS["egg"]["use"]=='N'&&$TPL_VAR["eggyn"]=='f'){?>
	<tr>
		<td>전자보증보험</td>
		<td>보증서 발급 실패.</td>
	</tr>
<?php }elseif($GLOBALS["egg"]["use"]=='Y'&&$TPL_VAR["eggyn"]=='f'){?>
	<tr>
		<td>전자보증보험</td>
		<td>보증서 발급 실패. 재발급 받으세요.</td>
	</tr>
<?php }?>
	</table>

	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>
<?php if($TPL_VAR["step2"]== 50||$TPL_VAR["step2"]== 54){?>
<?php if($GLOBALS["egg"]["use"]=="Y"&&($GLOBALS["egg"]["scope"]=="A"||($GLOBALS["egg"]["scope"]=="P"&&$TPL_VAR["settleprice"]>=$GLOBALS["egg"]["min"]))){?>
<table id="egg" style="display:none; margin-top:10px;">
<col width=100>
<tr>
	<td valign=top style="padding-top:4px">전자보증보험</td>
	<td>
<?php if($GLOBALS["egg"]["scope"]=="P"){?>
	<div>구매 시 안전거래(매매보호) 사용유무를 직접 선택하실 수 있습니다.</div>
<?php }?>

	<div style="color:#FF6C68; font-weight:bold; margin-bottom:5px;">아래의 주의사항을 꼭 확인하세요!</div>

<?php if($GLOBALS["egg"]["scope"]=="P"){?>
	<div class=noline>&#149; 전자보증보험 발급여부 : <input type=radio name=eggIssue value="Y" onclick="egg_required()"> 발급 <input type=radio name=eggIssue value="N" onclick="egg_required()"> 미발급</div>
<?php }?>

	<div>&#149; <font color=444444>전자보증보험 안내 (100% 매매보호 안전결제)<br>
	물품대금결제시 구매자의 피해보호를 위해 '(주)서울보증보험'의 보증보험증권이 발급됩니다. 증권이 발급되는 것의 의미는,
	물품대금 결제시에 소비자에게 서울보증보험의 쇼핑몰보증보험 계약체결서를 인터넷상으로 자동 발급하여,
	피해발생시 쇼핑몰보증보험으로써 완벽하게 보호받을 수 있습니다.<br>
	또한, <span class='red'>입력하신 개인정보는 증권발급을 위한 정보로 사용되며 다른 용도로는 사용되지 않습니다.</span>
	</font></div>

<?php if($GLOBALS["egg"]["feepayer"]!="B"){?>
	<div>&#149; <font color=444444>보증보험 발행으로 구매시 별도의 수수료가 부과되지 않습니다.</font></div>
<?php }elseif($GLOBALS["egg"]["feepayer"]=="B"){?>
	<div>&#149; <font color=444444>보증보험 발행으로 구매시 <span style="color:#FF6C68; font-weight:bold;">보증보험증권 발급수수료는 구매자께서 부담</span>하시게 됩니다.<br>
	보증보험 발급수수료(총 결제금액의 0.5%) : <span id=infor_eggFee></span></font>
	</div>
	<input type=hidden name=eggFee>
<?php }?>

	<div style="padding-top:10px;">주민등록번호 :
	<input type=text name=resno[] maxlength=6 onkeyup="if (this.value.length==6) this.nextSibling.nextSibling.focus()" onkeydown="onlynumber()" style="width:80px"> -
	<input type=password name=resno[] maxlength=7 onkeydown="onlynumber()" style="width:90px">
	</div>
	<div style="text-align:center;" class=noline><input type=checkbox name=eggAgree value="Y"> 개인정보 이용에 동의합니다</div>
	</td>
</tr>
</table>
<?php }?>
</form>
<?php }else{?>
</form>
<?php if($TPL_VAR["step"]> 0&&$GLOBALS["egg"]["use"]=='Y'&&$TPL_VAR["eggyn"]=='f'){?>
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3" style="padding-top:13px"><b>전자보증보험 재발급</b></td>
	<td id="orderbox">
		<form id=form name=frmTax method=post action="<?php echo url("mypage/indb.php")?>&" onsubmit="return chkForm(this)">
		<input type=hidden name=mode value="eggcreate">
		<input type=hidden name=ordno value="<?php echo $TPL_VAR["ordno"]?>">
		<div>&#149; 전자보증보험 안내 (100% 매매보호 안전결제)<br>
		물품대금결제시 구매자의 피해보호를 위해 '(주)서울보증보험'의 보증보험증권이 발급됩니다.<br>
		증권이 발급되는 것의 의미는, 물품대금 결제시에 소비자에게 서울보증보험의 쇼핑몰보증보험 계약체결서를 인터넷상으로 자동 발급하여, 피해발생시 쇼핑몰보증보험으로써 완벽하게 보호받을 수 있습니다.<br>
		또한, <span class='red'>입력하신 개인정보는 증권발급을 위해 필요한 정보이며 다른 용도로 사용되지 않습니다.</span><br>
		(전자보증보험 발생시 별도의 수수료가 부과되지 않습니다.)
		</div>
		<div style="text-align:center; margin-top:10px;">주민등록번호 :
		<input type=text name=resno[] maxlength=6 onkeyup="if (this.value.length==6) this.nextSibling.nextSibling.focus()" onkeydown="onlynumber()" style="width:80px" required label="주민등록번호" msgR="전자보증보험을 발급받으시려면, 주민번호를 입력하셔야 결제가 가능합니다."> -
		<input type=password name=resno[] maxlength=7 onkeydown="onlynumber()" style="width:90px" required label="주민등록번호" msgR="전자보증보험을 발급받으시려면, 주민번호를 입력하셔야 결제가 가능합니다.">
		</div>
		<div style="text-align:center;" class=noline><input type=checkbox name=eggAgree value="Y" required label="개인정보 이용동의" msgR="전자보증보험을 발급받으시려면, 개인정보 이용동의가 필요합니다."> 개인정보 이용에 동의합니다</div>
		<a href="javascript:order_print('<?php echo $TPL_VAR["ordno"]?>');"><strong><FONT COLOR="EA0095">[세금계산서 인쇄]</font></strong></a></div>
		</form>
	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>
<?php }?>
<?php if($TPL_VAR["taxmode"]!=''&&!$TPL_VAR["cashreceipt"]){?>
<!-- 05 세금계산서 -->
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3" style="padding-top:13px"><b>세금계산서 발행신청</b></td>
	<td id="orderbox">
<?php if($TPL_VAR["taxmode"]=='taxview'){?>
		<!-- 세금계산서 발행정보 : start -->
		<div style="margin-top:5px; margin-bottom:3px; line-height:14pt;">
<?php if($TPL_VAR["taxed"]['step']== 0){?>
			<FONT COLOR="EA0095"><b>발행신청</b> (승인처리후 인쇄하실 수 있습니다)<br>
			<font color=444444>(아래 수정사항이 있는 경우 1:1문의 또는 전화로 요청)</font></font><br>신청일 : <?php echo $TPL_VAR["taxed"]['regdt']?><br>
<?php }elseif($TPL_VAR["taxed"]['step']== 1){?>
			<FONT COLOR="EA0095"><b>발행승인</b> (인쇄발행이 가능합니다)</font><br>발행액 : <b><?php echo number_format($TPL_VAR["taxed"]['price'])?></b>원, 승인일 : <b><?php echo $TPL_VAR["taxed"]['agreedt']?></b><br>
<?php }elseif($TPL_VAR["taxed"]['step']== 2){?>
			<FONT COLOR="EA0095"><b>발행완료</b> (인쇄발행이 완료되었습니다)</font><br>발행액 : <b><?php echo number_format($TPL_VAR["taxed"]['price'])?></b>원, 완료일 : <b><?php echo $TPL_VAR["taxed"]['printdt']?></b><br>
<?php }elseif($TPL_VAR["taxed"]['step']== 3){?>
			<div id="taxstep3"><FONT COLOR="EA0095"><b>전자발행</b></font></div>발행액 : <b><?php echo number_format($TPL_VAR["taxed"]['price'])?></b>원, 요청일 : <b><?php echo $TPL_VAR["taxed"]['agreedt']?></b><br>
<?php }?>

		사업자번호 : <?php echo $TPL_VAR["taxed"]['busino']?>&nbsp;&nbsp;
		회사명 : <?php echo $TPL_VAR["taxed"]['company']?><br>
		대표자명 : <?php echo $TPL_VAR["taxed"]['name']?>&nbsp;&nbsp;
		업태 : <?php echo $TPL_VAR["taxed"]['service']?>&nbsp;&nbsp;
		종목 : <?php echo $TPL_VAR["taxed"]['item']?><br>
		사업장주소 : <?php echo $TPL_VAR["taxed"]['address']?>

		</div>

<?php if($TPL_VAR["taxed"]['step']== 1||$TPL_VAR["taxed"]['step']== 2||$TPL_VAR["taxed"]['step']== 3){?>
		<div id="taxprint" style="margin-top:8px; text-align:center;">
		<a href="javascript:order_print('<?php echo $TPL_VAR["ordno"]?>');"><strong><FONT COLOR="EA0095">[세금계산서 인쇄]</font></strong></a></div>
		<div style="padding-top:5px"></div>
<?php }?>
		<!-- 세금계산서 발행정보 : end -->
<?php }?>

		<!-- 세금계산서 신청폼 : start -->
		<div id="taxapply" style="display:none;">
		<form id=form name=frmTax method=post action="<?php echo url("mypage/indb.php")?>&" onsubmit="return chkForm(this)">
		<input type=hidden name=mode value="taxapp">
		<input type=hidden name=ordno value="<?php echo $TPL_VAR["ordno"]?>">
		<div>
		사업자번호 : <input type=text name="busino" value="<?php echo $TPL_VAR["taxed"]['busino']?>" class=line required  option="regNum" label="사업자번호" size=10 maxlength=30> <font class=small1 color=444444>(숫자만기입)</font><br>
		<font color=white>사업</font>회사명 : <input type=text name="company" value="<?php echo $TPL_VAR["taxed"]['company']?>" class=line required label="회사명" size=10>&nbsp;&nbsp;&nbsp;
		대표자명 : <input type=text name="name" value="<?php echo $TPL_VAR["taxed"]['name']?>" class=line required label="대표자명" size=10><br>
		<font color=white>사업자</font>업태 : <input type=text name="service" value="<?php echo $TPL_VAR["taxed"]['service']?>" class=line required label="업태" size=10>&nbsp;&nbsp;&nbsp;
		<font color=white>사업</font>종목 : <input type=text name="item" value="<?php echo $TPL_VAR["taxed"]['item']?>" class=line required label="종목" size=10><br>
		사업장주소 : <input type=text name="address" value="<?php echo $TPL_VAR["taxed"]['address']?>" class=line required label="사업장주소" size=40>
		</div>
		<div style="text-align:center; margin-top:8px;"><input type="submit" value="[세금계산서 신청하기]" style="border:0;background-color:#ffffff;color:#EA0095;font-weight:bold;"></div>
		</form>
		</div>
		<script>
		_ID('taxapply').style.display = "<?php if($TPL_VAR["taxmode"]=='taxapp'){?>block<?php }else{?>none<?php }?>"; //
		</script>
		<!-- 세금계산서 신청폼 : end -->

<?php if($TPL_VAR["taxmode"]=='taxview'&&$TPL_VAR["taxed"]['step']== 3){?>
		<script src="/shop/lib/js/prototype.js"></script>
		<script>getTaxbill("<?php echo $TPL_VAR["taxed"]['doc_number']?>", "<?php echo $TPL_VAR["taxapp"]?>");</script>
<?php }?>
	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>
<!-- 05 세금계산서 End -->
<?php }?>

<?php if($GLOBALS["pg"]["receipt"]=="Y"&&$TPL_VAR["settlekind"]!="c"&&$TPL_VAR["settlekind"]!="h"&&$TPL_VAR["settleprice"]>= 1&&$TPL_VAR["taxmode"]!='taxview'){?><!-- 현금영수증 발급 -->
<?php $this->print_("cash_receipt",$TPL_SCP,1);?>

<?php }?>

<?php if($GLOBALS["ableCashbag"]){?><!-- OK캐쉬백 -->
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"></td>
	<td id="orderbox">
	<div><a href="javascript:okcashbag();"><img src="/shop/data/skin/campingyo/img/common/btn_okcash.gif"></a></div>
	<div class="small">'OK캐쉬백적립받기'버튼을 클릭하여 캐쉬백 포인트를 적립받으실수 있습니다. 캐쉬백 카드번호를 입력하지 않았거나 잘못 입력 하신 고객님의 경우 포인트 적립이 불가하오며, 아울러 등록하신 카드번호가 미등록 카드이거나 해지된 경우에도 적립이 불가하오니 이점 양지하시기 바랍니다. 일부상품,배송비 및 각종 수수료(보증보험)등의 결제 금액은 포인트가 추가 적립되지 않습니다. 적립시점 및 포인트는 배송완료(구매결정) 기준입니다.</div>
	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>
<?php }?>

<?php if($TPL_VAR["cbyn"]=='Y'&&$TPL_VAR["step"]=='4'&&$TPL_VAR["oktno"]){?><!-- OK캐쉬백 적립 정보-->
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"></td>
	<td id="orderbox">
		<div style="padding-left:5"><b>OK캐쉬백</b></div>
		<div style="font:0;height:5"></div>
		<table>
		<col width=100>
		<tr>
			<td>거래번호</td>
			<td><?php echo $TPL_VAR["oktno"]?></td>
		</tr>
		<tr>
			<td>적립포인트</td>
			<td><?php echo number_format($TPL_VAR["add_pnt"])?></td>
		</tr>
		</table>
	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>
<?php }?>

<?php if($TPL_VAR["step"]&&!$TPL_VAR["step2"]&&in_array($TPL_VAR["settlekind"],array('c','o','v'))){?><!-- 거래 영수증 발급 -->
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"></td>
	<td id="orderbox">

	<table>
	<col width=100>
	<tr>
		<td>거래영수증</td>
		<td>
<?php if($TPL_VAR["pg"]=="ipay"&&$TPL_VAR["settlekind"]=="c"){?>
<?php if($TPL_item_1){foreach($TPL_VAR["item"] as $TPL_V1){?>
			<div><a href="javascript:void(0)" onclick="window.open('https://accounting.auction.co.kr/card/receiptlist.aspx?order_no=<?php echo $TPL_V1["ipay_ordno"]?>&seller_id=<?php echo $TPL_VAR["ipay"]["sellerid"]?>','','width=410,height=650')"><?php echo $TPL_V1["goodsnm"]?> [<?php echo $TPL_V1["opt1"]?>/<?php echo $TPL_V1["opt2"]?>] 영수증출력</a></div>
<?php }}?>
<?php }elseif($GLOBALS["cfg"]["settlePg"]=="allat"||$GLOBALS["cfg"]["settlePg"]=="allatbasic"){?>
		<a href="javascript:void(0)" onClick="window.open('http://www.allatpay.com/servlet/AllatBizPop/member/pop_card_receipt.jsp?shop_id=<?php echo $GLOBALS["pg"]["id"]?>&order_no=<?php echo $TPL_VAR["ordno"]?>','','width=410,height=650')">영수증출력</a>
<?php }elseif($GLOBALS["cfg"]["settlePg"]=="kcp"&&$TPL_VAR["settlekind"]=="c"){?>
		<a href="javascript:popup('http://admin.kcp.co.kr/Modules/Sale/Card/ADSA_CARD_BILL_Receipt.jsp?c_trade_no=<?php echo $TPL_VAR["tno"]?>',428,741)">영수증출력</a>
<?php }elseif($GLOBALS["cfg"]["settlePg"]=="inicis"&&($TPL_VAR["cardtno"]||$TPL_VAR["escrowno"])){?>
		<a href="javascript:popup('https://iniweb.inicis.com/mall/cr/cm/mCmReceipt_head.jsp?noTid=<?php if($TPL_VAR["cardtno"]){?><?php echo $TPL_VAR["cardtno"]?><?php }else{?><?php echo $TPL_VAR["escrowno"]?><?php }?>&noMethod=1',428,741)">영수증출력</a>
<?php }elseif($GLOBALS["cfg"]["settlePg"]=="inipay"&&($TPL_VAR["cardtno"]||$TPL_VAR["escrowno"])){?>
		<a href="javascript:popup('https://iniweb.inicis.com/mall/cr/cm/mCmReceipt_head.jsp?noTid=<?php if($TPL_VAR["cardtno"]){?><?php echo $TPL_VAR["cardtno"]?><?php }else{?><?php echo $TPL_VAR["escrowno"]?><?php }?>&noMethod=1',428,741)">영수증출력</a>
<?php }elseif($GLOBALS["cfg"]["settlePg"]=="dacom"||$GLOBALS["cfg"]["settlePg"]=="lgdacom"){?>
		<script type="text/javascript" src="http://pgweb.dacom.net/WEB_SERVER/js/receipt_link.js"></script>
		<a href="javascript:showReceiptByTID('<?php echo $GLOBALS["pg"]["id"]?>','<?php echo $TPL_VAR["cardtno"]?>','<?php echo $TPL_VAR["authdata"]?>')">영수증 출력</a>
<?php }elseif($GLOBALS["cfg"]["settlePg"]=="agspay"&&$TPL_VAR["settlekind"]=="c"){?>
		<a href="javascript:popup('http://www.allthegate.com/customer/receiptLast3.jsp?sRetailer_id=<?php echo $GLOBALS["pg"]["id"]?>&approve=<?php echo $TPL_VAR["pgAppNo"]?>&send_no=<?php echo $TPL_VAR["cardtno"]?>&send_dt=<?php echo substr($TPL_VAR["pgAppDt"], 0, 8)?>',420,700)">영수증출력</a>
<?php }elseif($GLOBALS["cfg"]["settlePg"]=="easypay"&&$TPL_VAR["settlekind"]=="c"){?>
		<a href="javascript:receipt('<?php echo $TPL_VAR["cardtno"]?>', '신용카드');">영수증출력</a>
<?php }elseif($GLOBALS["cfg"]["settlePg"]=="settlebank"){?>
<?php if($TPL_VAR["settlekind"]=="c"){?>
				<a href="javascript:void(0)" onClick="window.open('https://pg.settlebank.co.kr/common/CommonMultiAction.do?_method=RcptView&mid=<?php echo $GLOBALS["pg"]["id"]?>&ordNo=<?php echo $TPL_VAR["ordno"]?>&svcCd=CD','','width=500,height=750')">영수증출력</a>
<?php }elseif($TPL_VAR["settlekind"]=="o"){?>
				<a href="javascript:void(0)" onClick="window.open('https://pg.settlebank.co.kr/common/CommonMultiAction.do?_method=RcptView&mid=<?php echo $GLOBALS["pg"]["id"]?>&ordNo=<?php echo $TPL_VAR["ordno"]?>&svcCd=BNK','','width=410,height=650')">영수증출력</a>
<?php }elseif($TPL_VAR["settlekind"]=="v"){?>
				<a href="javascript:void(0)" onClick="window.open('https://pg.settlebank.co.kr/common/CommonMultiAction.do?_method=RcptView&mid=<?php echo $GLOBALS["pg"]["id"]?>&ordNo=<?php echo $TPL_VAR["ordno"]?>&svcCd=VBANK','','width=410,height=650')">영수증출력</a>
<?php }?>
<?php }?>
		</td>
	</tr>
	</table>

	</td>
</tr>
</table>
<?php }?>
<?php }?>

<?php if($GLOBALS["sess"]){?>
<div align=right class=stxt style="padding:5px 0"><font size=2 COLOR="#007FC8">※</font> <FONT COLOR="#007FC8">주문취소/교환/반품을 원하시면 마이페이지의 <A HREF="<?php echo url("mypage/mypage_qna.php")?>&"><U>1:1문의게시판</U></A>을 이용하세요</font></div>
<?php }?>

<div style="padding:20px" align=center id="avoidDblPay">
<a href="javascript:history.back();"><img src="/shop/data/skin/campingyo/img/common/btn_back.gif" border=0></a>
<?php if($TPL_VAR["step"]== 0&&$TPL_VAR["step2"]== 0){?><a href="javascript:chkCancel();"><img src="/shop/data/skin/campingyo/img/common/btn_order_cancel.gif" border=0></a><?php }?>
<?php if($GLOBALS["resettleAble"]){?><a href="javascript:chkReSettle();"><img src="/shop/data/skin/campingyo/img/common/btn_again_order.gif" border=0></a></div><?php }?>
<div style="font-size:0;height:5px"></div>
</div><!-- End indiv -->
<script language="javascript">

function receipt(controlNo, payment)
{
    var receipt_url= "http://office.easypay.co.kr/receipt/ReceiptBranch.jsp?controlNo="+controlNo+"&payment="+payment; // 테스트
	window.open(receipt_url,"MEMB_POP_RECEIPT", 'toolbar=0,scroll=1,menubar=0,status=0,resizable=0,width=380,height=700');
}


function chkCancel(){
	var f = document.frmOrder;
	if(confirm('주문취소처리 하시겠습니까?')){
		f.mode.value='orderCancel';
		f.submit();
	}
}
function chkReSettle(){
	var f = document.frmOrder;
	if(chkForm(f)){
		f.mode.value='reSettle';
		f.action = 'settle.php';
		f.submit();
	}

}

function egg_required()
{
	egg_display();
	calcu_settle();
}
function calcu_eggFee(settlement)
{
	egg_display(settlement);
	var eggFee = 0;
	if (typeof(document.getElementsByName('eggFee')[0]) != "undefined"){
		if (document.getElementsByName('eggFee')[0].disabled == false) eggFee = parseInt(settlement * 0.005);
		document.getElementsByName('eggFee')[0].value = eggFee;
	}
	if (_ID('paper_eggFee') != null) _ID('paper_eggFee').innerHTML = comma(eggFee);
	if (_ID('infor_eggFee') != null){
		_ID('infor_eggFee').innerHTML = '<b>' + comma(eggFee) + '</b> 원';
		if (eggFee) _ID('infor_eggFee').innerHTML += ' (총 결제금액에 포함되었습니다.)';
	}
	return eggFee;
}
function egg_display(settlement)
{
	var min = parseInt('<?php echo $GLOBALS["egg"]["min"]?>');
	var display = 'block';
	if (_ID('egg') == null) return;
	if (typeof(settlement) != "undefined"){
		if (settlement < min && typeof(document.getElementsByName('eggIssue')[0]) != "undefined") display = 'none';
		else if (settlement <= 0) display = 'none';
		else if (_ID('egg').style.display != 'none') return;
	}
	if (display != 'none'){
		var obj = document.getElementsByName('settlekind');
		var settlekind =  obj.value;

		if (settlekind == null) display = 'none';
		else if (settlekind == 'h') display = 'none';
		else if (document.getElementsByName('escrow')[0].value == 'Y') display = 'none';
		else if (typeof(document.getElementsByName('eggIssue')[0]) != "undefined"){
			if (settlekind != 'a') display = 'none';
			else if(typeof(settlement) == "undefined"){
				settlement = uncomma(_ID('paper_settlement').innerHTML);
				if (typeof(document.getElementsByName('eggFee')) != "undefined") settlement -= document.getElementsByName('eggFee')[0].value;
				if (settlement < min) display = 'none';
			}
		}
	}
	if (_ID('egg').style.display == display && display =='none') return;
	_ID('egg').style.display = display;

	items = new Array();
	items.push( {name : "eggIssue", label : "전자보증보험 발급여부", msgR : ""} );
	items.push( {name : "resno[]", label : "주민등록번호", msgR : "전자보증보험을 발급받으시려면, 주민번호를 입력하셔야 결제가 가능합니다."} );
	items.push( {name : "eggAgree", label : "개인정보 이용동의", msgR : "전자보증보험을 발급받으시려면, 개인정보 이용동의가 필요합니다."} );
	items.push( {name : "eggFee", label : "발급수수료", msgR : ""} );
	for (var i = 0; i < items.length; i++){
		var obj = document.getElementsByName(items[i].name);
		if (display == 'block' && items[i].name != 'eggIssue' && typeof(document.getElementsByName('eggIssue')[0]) != "undefined")
			state = (document.getElementsByName('eggIssue')[0].checked ? 'block' : 'none');
		else state = display;
		for (var j = 0; j < obj.length; j++){
			if (state == 'block'){
				obj[j].setAttribute('required', '');
				obj[j].setAttribute('label', items[i].label);
				obj[j].setAttribute('msgR', items[i].msgR);
				obj[j].disabled = false;
			}
			else {
				obj[j].removeAttribute('required');
				obj[j].removeAttribute('label');
				obj[j].removeAttribute('msgR');
				obj[j].disabled = true;
			}
		}
	}
}
function calcu_settle()
{
	var coupon = settleprice = 0;
	var goodsprice	= uncomma(document.getElementById('paper_goodsprice').innerHTML);
	var delivery	= uncomma(document.getElementById('paper_delivery').innerHTML);
	var dc = uncomma(document.getElementById('paper_memberdc').innerHTML);
	var emoney = (document.frmOrder.emoney) ? uncomma(document.frmOrder.emoney.value) : 0;
	if (document.frmOrder.coupon){
		coupon = uncomma(document.frmOrder.coupon.value);
		if (goodsprice + delivery - dc - coupon - emoney < 0){
			emoney = goodsprice + delivery - dc - coupon;
			document.frmOrder.emoney.value = comma(cutting(emoney));
		}
		dc += coupon + emoney;
	}
	var settlement = (goodsprice + delivery - dc);

	settlement += calcu_eggFee(settlement); // 전자보증보험 발급수수료 계산
	document.getElementById('paper_settlement').innerHTML = comma(settlement);
}

function okcashbag(){
	var f = document.frmOrder;
	f.target = "ifrmHidden";
	f.action = "cashbag.php";
	f.submit();
}
function popup_register( mode, goodsno, sno )
{
<?php if(empty($GLOBALS["cfg"]['reviewWriteAuth'])&&!$GLOBALS["sess"]){?>
		alert( "회원전용입니다." );
<?php }else{?>
		var win = window.open("../goods/goods_review_register.php?mode=" + mode + "&goodsno=" + goodsno + "&sno=" + sno + "&referer=orderview","review_register","width=600,height=500");
		win.focus();
<?php }?>
}
</script>
<?php $this->print_("footer",$TPL_SCP,1);?>