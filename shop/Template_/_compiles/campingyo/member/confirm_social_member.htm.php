<?php /* Template_ 2.2.7 2014/05/29 07:10:05 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/member/confirm_social_member.htm 000001793 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<style type="text/css">
.indiv {
	text-align: center;
}
button.btn {
	cursor: pointer;
	border: none;
	font-size: 0;
}
#sns-confirm {
	margin-top: 30px;
}
#sns-confirm div.outer-border {
	border: 1px solid #dedede;
}
#sns-confirm div.inner-border {
	border: 4px solid #f3f3f3;
	color: #404040;
	padding: 33px 0px;
	text-align: center;
}
#sns-confirm button.btn-facebook {
	width: 125px;
	height: 31px;
	background: url('/shop/data/skin/campingyo/img/login_sns_<?php echo strtolower($TPL_VAR["SocialCode"])?>.gif') no-repeat;
}
#sns-confirm div.sns-confirm-description {
	margin-top: 20px;
}
</style>

<!-- 상단이미지 || 현재위치 -->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td><img src="/shop/data/skin/campingyo/img/common/title_modifyinfo.gif" border="0"></td>
	</tr>
	<tr>
		<td class="path">HOME > 마이페이지 > <strong>회원정보수정</strong></td>
	</tr>
</table>


<div class="indiv"><!-- Start indiv -->

	<h2>SNS계정 재인증</h2>
	<div>
		<strong>회원님의 정보를 안전하게 보호</strong>하기 위하여 <strong>회원 계정에대한 재인증</strong>이 필요합니다.
	</div>

	<div id="sns-confirm">
		<div class="outer-border">
			<div class="inner-border">
				<button class="btn btn-facebook" onclick="popup('<?php echo $TPL_VAR["SocialConfirmMemberURL"]?>', 400, 300);">페이스북</button>
				<div class="sns-confirm-description">
					회원 계정의 재인증을 위하여 로그인 되어 있는 SNS를 클릭해 주세요.
				</div>
			</div>
		</div>
	</div>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>