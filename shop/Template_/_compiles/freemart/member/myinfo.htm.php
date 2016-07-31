<?php /* Template_ 2.2.7 2016/04/12 05:49:02 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/member/myinfo.htm 000000795 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- 상단이미지 || 현재위치 -->

<div class="page_title_div">
	<div class="page_title">Confirm Password</div>
	<div class="page_path"><a href="/shop/">HOME</a> &gt; <a href="/shop/mypage/mypage.php">마이페이지</a></div>
</div>
<div class="page_title_line"></div>

<div style="width:100%; padding-top:20px;"></div>



<div class="indiv"><!-- Start indiv -->

<?php if($TPL_VAR["memberSocialStatus"]){?>
<?php $this->print_("memberSocialStatus",$TPL_SCP,1);?>

<?php }?>
	
<?php $this->print_("frmMember",$TPL_SCP,1);?>


</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>