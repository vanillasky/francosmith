<?php /* Template_ 2.2.7 2016/04/12 04:57:55 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/member/join_type.htm 000004137 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '955993964516026',
      xfbml      : true,
      version    : 'v2.5'
    });
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td><img src="/shop/data/skin/campingyo/img/common/title_join.gif" border="0"></td>
</tr>
<tr>
	<td class="path">HOME > 회원가입 > <strong>가입방법선택</strong></td>
</tr>
</table>

<style type="text/css">
div.join-member-title {
	margin-left: 20px;
	margin-top: 5px;
	margin-bottom: 5px;
	background: url('/shop/data/skin/campingyo/img/join_title_01.gif') no-repeat;
	height: 23px;
	font-size: 0;
}
div.outer-border {
	margin-left: 20px;
	width: 650px;
	overflow: hidden;
	border: 1px solid #dedede;
}
div.inner-border {
	border: 5px solid #f3f3f3;
	padding: 30px;
	overflow: hidden;
}
div.join-shop {
	float: left;
	width: 300px;
}
div.join-shop div.join-shop-title {
	height: 12px;
	background: url('/shop/data/skin/campingyo/img/sns_join_01.gif') no-repeat;
	font-size: 0;
}
div.join-shop div.join-shop-select {
	margin-left: 20px;
	margin-top: 20px;
}
div.join-shop div.join-shop-select button.btn-join-shop {
	width: 219px;
	height: 54px;
	background: url('/shop/data/skin/campingyo/img/btn_shop_join.gif') no-repeat;
	border: none;
	font-size: 0;
}
div.join-shop div.join-shop-select div.join-shop-option {
	margin-top: 10px;
}
div.join-shop div.join-shop-select div.join-shop-option span {
	font: 12px gulim;
	color: #666666;
}
div.join-shop div.join-shop-select div.join-shop-option a.login-shop {
	font-weight: bold;
	color: #f7443f;
	text-decoration: underline;
}
div.join-sns {
	float: left;
	width: 280px;
}
div.join-sns div.join-sns-title {
	height: 12px;
	background: url('/shop/data/skin/campingyo/img/sns_join_02.gif') no-repeat;
	font-size: 0;
}
div.join-sns div.join-sns-select {
	margin-left: 20px;
	margin-top: 20px;
}
button.btn {
	cursor: pointer;
	border: none;
	font-size: 0;
}
div.join-sns div.join-sns-select div.sns-btn-group button.btn-facebook {
	width: 125px;
	height: 31px;
	background: url('/shop/data/skin/campingyo/img/login_sns_facebook.gif') no-repeat;
}
div.join-sns div.join-sns-select div.join-sns-description {
	margin-top: 10px;
	font: 11px dotum;
	color: #666666;
	line-height: 15px;
}
</style>

<div class="join-member-title">회원가입</div>

<div class="hundred outer-border">
	<div class="inner-border">
		<!-- 쇼핑몰 계정으로 회원가입 -->
		<div class="join-shop">
			<div class="join-shop-title">쇼핑몰 계정으로 회원가입</div>
			<div class="join-shop-select">
				<button class="btn btn-join-shop" onclick="location.href='<?php echo url("member/join.php?")?>&MODE=agreement';">쇼핑몰 회원가입</button>
				<div class="join-shop-option">
					<span>이미 쇼핑몰 회원이세요?</span>
					<a href="<?php echo url("member/login.php")?>&" class="login-shop">로그인</a>
				</div>
			</div>
		</div>
		
		<!-- SNS 계정으로 회원가입 -->
		<div class="join-sns">
			<div class="join-sns-title">SNS 계정으로 회원가입</div>
			<div class="join-sns-select">
				<div class="sns-btn-group">
<?php if($TPL_VAR["FacebookLoginURL"]){?>
					<button class="btn btn-facebook" onclick="popup('<?php echo $TPL_VAR["FacebookLoginURL"]?>', 400, 300);">페이스북</button>
<?php }?>
				</div>
				<div class="join-sns-description">
					SNS계정을 연동하여 빠르고 쉽고 안전하게 회원가입 할 수 있습니다. 이 과정에서 고객님의 데이터는 철저하게 보호됩니다.
				</div>
			</div>
		</div>
	</div>
</div>



<?php $this->print_("footer",$TPL_SCP,1);?>