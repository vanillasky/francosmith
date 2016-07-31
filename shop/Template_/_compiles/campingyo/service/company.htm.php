<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/service/company.htm 000002093 */  $this->include_("dataBanner");?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- 상단이미지 || 현재위치 -->
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
<td><img src="/shop/data/skin/campingyo/img/common/title_company.gif" border=0></td>
</tr>
<TR>
<td class="path">HOME > <B>회사소개</B></td>
</TR>
</TABLE>


<div class="indiv"><!-- Start indiv -->

<br>

<table width=100% cellSpacing=0 cellPadding=0 border=0>
<tr>
<td><!-- 회사소개 로고 (배너관리에서 수정가능) --><?php if((is_array($TPL_R1=dataBanner( 23))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td>
<td style="padding-left:10">인터넷 비지니스는 이제 우리 삶에서 가장 중요한
연결 매개체로 자리잡았습니다.<br>
고도는 다년간 오직 한길, 전자상거래 솔루션만은 전문적으로
개발/운영하여 왔습니다.<br><br>

고도는 웹비지니스의 구축보다 사후관리를
더욱 중요하게 생각합니다. 운영과정에서 더 큰 도움이 되고자
노력합니다.<br><br>

고도는 치열한 경쟁시대에서 앞서나갈 수 있도록 강력하게
도와드리는 가장 든든한 조력자가 될 것입니다.<br>
말뿐이 아닌 지속적인 솔루션 개발과 고퀄리티 웹 사이트 맞춤
컨설팅으로 고객 제일주의를 실현하겠습니다.<br><br>

고객을 위해 흘리는 땀을 두려워하지 않는 정직한 기업이
될 것을 약속드립니다.<br><br>

고도의 문은 고객여러분께 항상 열려있습니다.<br><br>
한분 한분 고객님들과 같이 커가는 고도가 되겠습니다.<br><br>
감사합니다
</td>
</tr>
<tr>
<td height=10></td>
</tr>
<tr>
<td align=middle colspan=2><!-- 회사약도 (배너관리에서 수정가능) --><?php if((is_array($TPL_R1=dataBanner( 22))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td>
</tr>
</table>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>