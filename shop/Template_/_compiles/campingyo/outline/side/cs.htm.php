<?php /* Template_ 2.2.7 2014/03/30 13:43:31 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/outline/side/cs.htm 000003211 */  $this->include_("dataBanner");?>
<!-- 고객센터 메뉴 시작 -->
<div style="width:190px; border-bottom:solid 1px #ccc; padding:17px 0 0 0; margin:0;">
	<div style="padding:0px 0px 10px 17px; font-size:12px; font-weight:bold; color:#333; border-bottom:solid 1px #ccc;">고객센터</div>
	<div style="padding:10px 0 3px 8px;">
	<table cellpadding=0 cellspacing=7 border=0>
	<tr>
		<td><a href="<?php echo url("service/faq.php")?>&" class="lnbmenu">ㆍFAQ</a></td>
	</tr>
	<tr>
		<td><a href="<?php echo url("service/guide.php")?>&" class="lnbmenu">ㆍ이용안내</a></td>
	</tr>
	<tr>
		<td><a href="<?php echo url("mypage/mypage_qna.php")?>&" class="lnbmenu">ㆍ1:1문의게시판</a></td>
	</tr>
	<tr>
		<td><a href="<?php echo url("member/find_id.php")?>&" class="lnbmenu">ㆍID찾기</a></td>
	</tr>
	<tr>
		<td><a href="<?php echo url("member/find_pwd.php")?>&" class="lnbmenu">ㆍ비밀번호찾기</a></td>
	</tr>
	<tr>
		<td><a href="<?php echo url("member/myinfo.php")?>&" class="lnbmenu">ㆍ마이페이지</a></td>
	</tr>
	</table>
	</div>
</div>
<!-- 고객센터 메뉴 끝 -->
<!-- 메인왼쪽 고객센터 01 : Start -->
<div style="width:190px; height:95px; background:url(/shop/data/skin/campingyo/img/main/bn_cs.jpg) no-repeat;">
	<div style="padding:19px 0px 2px 66px;"><img src="/shop/data/skin/campingyo/img/main/txt_cs.gif"></div>
	<div style="padding-left:66px; font-size:14px; font-weight:bold; line-height:23px; color:#333; font-family:Tahoma, Geneva, sans-serif"><?php echo $GLOBALS["cfg"]['compPhone']?> </div>
	<dl style="margin:0px; padding-left:66px; color:#888; font-size:11px;">
		<dd style="margin:0px; line-height:12px;">MON - FRI</dd>
		<dd style="margin:0px; line-height:12px;">10:00 - 18:00</dd>
	</dl>
</div>
<!-- 메인왼쪽 고객센터 01 : End -->

<!-- 관리자에게 SMS보내기 기능 : 관련파일은 '디자인관리 > 기타페이지디자인 > 기타/추가페이지(proc) > 관리자에게 SMS상담문의하기 - ccsms.htm' 에 있습니다. -->
<!-- 아래 기능은 기본적으로 회원들만 보이도록 되어있는 소스입니다.
만약 비회원들도 이 기능을 사용하게 하려면 아래 소스중에,  \{ # ccsms \}  요부분만 남겨놓고 아래위 소스를 삭제하시면 됩니다.
또한 이기능을 사용하려면 '회원관리 > SMS포인트충전' 에서 포인트충전이 되어있어야만 가능합니다. -->

<?php if($GLOBALS["sess"]){?>
<?php $this->print_("ccsms",$TPL_SCP,1);?>

<?php }?>

<!-- 메인왼쪽배너 : Start -->
<table cellpadding="0" cellspacing="0" border="0"width=100%>
<tr><td align="left"><!-- (배너관리에서 수정가능) --><?php if((is_array($TPL_R1=dataBanner( 4))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
<tr><td align="left"><!-- (배너관리에서 수정가능) --><?php if((is_array($TPL_R1=dataBanner( 5))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
</table>
<!-- 메인왼쪽배너 : End -->

<div style="padding-top:80px"></div>