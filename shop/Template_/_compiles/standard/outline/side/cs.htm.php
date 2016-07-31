<?php /* Template_ 2.2.7 2014/07/23 17:03:45 /www/francotr3287_godo_co_kr/shop/data/skin/standard/outline/side/cs.htm 000002434 */  $this->include_("dataBanner");?>
<!-- 고객센터 메뉴 시작 -->
<div id="left_cs" style="width:<?php echo $GLOBALS["cfg"]['shopSideSize']?>px;">
	<div class="title_cs">고객센터</div>
	<div class="line_cs"></div>
	<div style="padding:5px 0 3px 8px;">
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
	<div class="line_cs"></div>
</div>
<!-- 고객센터 메뉴 끝 -->

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